import { Component, OnInit, signal, inject } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormBuilder, FormGroup, ReactiveFormsModule, Validators } from '@angular/forms';
import { TableModule } from 'primeng/table';
import { DialogModule } from 'primeng/dialog';
import { ButtonModule } from 'primeng/button';
import { InputTextModule } from 'primeng/inputtext';
import { SelectModule } from 'primeng/select';
import { MessageModule } from 'primeng/message';
import { UsuarioService, Usuario } from '../../../core/services/usuario.service';
import { SolicitudTipoService, Rol } from '../../../core/services/solicitud-tipo.service';

@Component({
  selector: 'app-user-admin',
  standalone: true,
  imports: [
    CommonModule,
    ReactiveFormsModule,
    TableModule,
    DialogModule,
    ButtonModule,
    InputTextModule,
    SelectModule,
    MessageModule
  ],
  templateUrl: './user-admin.component.html',
  styleUrl: './user-admin.component.css'
})
export class UserAdminComponent implements OnInit {
  private fb = inject(FormBuilder);
  private userService = inject(UsuarioService);
  private rolService = inject(SolicitudTipoService); // Reusing Role endpoint from here

  usuarios = signal<Usuario[]>([]);
  roles = signal<Rol[]>([]);
  
  showDialog = false;
  isSaving = signal(false);
  errorMessage = signal<string | undefined>(undefined);
  
  userForm!: FormGroup;

  ngOnInit() {
    this.initForm();
    this.loadUsers();
    this.loadRoles();

    // Auto-generation logic based on form changes
    this.userForm.valueChanges.subscribe(val => {
      this.generateCredentials(val);
    });
  }

  private initForm() {
    this.userForm = this.fb.group({
      nombre: ['', [Validators.required, Validators.maxLength(45)]],
      snombre: ['', Validators.maxLength(45)],
      apellido: ['', [Validators.required, Validators.maxLength(45)]],
      sapellido: ['', Validators.maxLength(45)],
      cedula: ['', [Validators.required, Validators.pattern('^[0-9]{10}$')]],
      celular: ['', [Validators.pattern('^[0-9]{10}$')]],
      correo_electronico: ['', [Validators.required, Validators.email, Validators.maxLength(100)]],
      fecha_nac: ['', Validators.required],
      rol_id: [null, Validators.required],
      username: [''],
      password: ['']
    });
  }

  private generateCredentials(val: any) {
    if (!val.nombre || !val.apellido || !val.cedula || val.cedula.length < 4) {
      return;
    }

    const nombre = val.nombre.trim().toLowerCase().normalize("NFD").replace(/[\u0300-\u036f]/g, '').replace(/[^a-z]/g, '');
    const apellido = val.apellido.trim().toLowerCase().normalize("NFD").replace(/[\u0300-\u036f]/g, '').replace(/[^a-z]/g, '');
    const cedula = val.cedula.trim();

    if (nombre === '' || apellido === '') return;

    const currentYear = new Date().getFullYear();
    const last4Cedula = cedula.slice(-4);
    
    // Contraseña: Apellido(Cap) + ultimos 4 cedula + @ + año
    const capitalizedApellido = apellido.charAt(0).toUpperCase() + apellido.slice(1);
    const generatedPassword = capitalizedApellido + last4Cedula + "@" + currentYear;
    
    // Username: nombre.apellido + ultimos 4 cedula + @ldcruminahui.com
    const generatedUsername = nombre + "." + apellido + last4Cedula + "@ldcruminahui.com";

    // Only patch if different to avoid infinite loop
    if (this.userForm.get('username')?.value !== generatedUsername || 
        this.userForm.get('password')?.value !== generatedPassword) {
      this.userForm.patchValue({
        username: generatedUsername,
        password: generatedPassword
      }, { emitEvent: false });
    }
  }

  loadUsers() {
    this.userService.getUsuarios().subscribe({
      next: (data) => this.usuarios.set(data),
      error: () => console.error('Error loading users')
    });
  }

  loadRoles() {
    this.rolService.getRoles().subscribe({
      next: (data) => this.roles.set(data),
      error: () => console.error('Error loading roles')
    });
  }

  openNewDialog() {
    this.userForm.reset();
    this.errorMessage.set(undefined);
    this.showDialog = true;
  }

  closeDialog() {
    this.showDialog = false;
  }

  onSubmit() {
    if (this.userForm.invalid) return;

    this.isSaving.set(true);
    this.errorMessage.set(undefined);

    this.userService.createUsuario(this.userForm.getRawValue()).subscribe({
      next: () => {
        this.isSaving.set(false);
        this.showDialog = false;
        this.loadUsers();
      },
      error: (err) => {
        this.isSaving.set(false);
        this.errorMessage.set(err.error?.error || 'Error al registrar el usuario');
      }
    });
  }
}
