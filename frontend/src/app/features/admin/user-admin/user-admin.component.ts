import { Component, OnInit, signal, inject } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormBuilder, FormGroup, ReactiveFormsModule, Validators } from '@angular/forms';
import { TableModule } from 'primeng/table';
import { DialogModule } from 'primeng/dialog';
import { ButtonModule } from 'primeng/button';
import { InputTextModule } from 'primeng/inputtext';
import { SelectModule } from 'primeng/select';
import { MessageModule } from 'primeng/message';
import { TagModule } from 'primeng/tag';
import { MenuModule } from 'primeng/menu';
import { MenuItem } from 'primeng/api';
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
    MessageModule,
    TagModule,
    MenuModule
  ],
  templateUrl: './user-admin.component.html',
  styleUrl: './user-admin.component.css'
})
export class UserAdminComponent implements OnInit {
  private fb = inject(FormBuilder);
  private userService = inject(UsuarioService);
  private rolService = inject(SolicitudTipoService);

  usuarios = signal<Usuario[]>([]);
  roles = signal<Rol[]>([]);
  
  showDialog = false;
  isSaving = signal(false);
  errorMessage = signal<string | undefined>(undefined);
  selectedUsuarioId = signal<number | null>(null);

  // Invitation dialog state
  showInviteLinkDialog = signal(false);
  generatedInviteLink = signal<string | null>(null);

  // Password reset dialog state
  showResetPasswordDialog = signal(false);
  generatedPassword = signal<string | null>(null);

  menuItemsForSelectedUser: MenuItem[] = [];
  userForm!: FormGroup;

  ngOnInit() {
    this.initForm();
    this.loadUsers();
    this.loadRoles();

    // Auto-generation logic based on form changes (only when creating)
    this.userForm.valueChanges.subscribe(val => {
      if (this.selectedUsuarioId() === null) {
        this.generateCredentials(val);
      }
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
      username: ['']
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

    const last4Cedula = cedula.slice(-4);
    const generatedUsername = nombre + "." + apellido + last4Cedula + "@ldcruminahui.com";

    if (this.userForm.get('username')?.value !== generatedUsername) {
      this.userForm.patchValue({
        username: generatedUsername
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
    this.selectedUsuarioId.set(null);
    this.userForm.reset();
    this.userForm.get('username')?.enable();
    this.errorMessage.set(undefined);
    this.showDialog = true;
  }

  openEditDialog(user: Usuario) {
    this.selectedUsuarioId.set(user.id);
    this.errorMessage.set(undefined);
    
    // Autofill form using legacy model virtual attributes
    this.userForm.patchValue({
      nombre: (user as any).primer_nombre || '',
      snombre: (user as any).segundo_nombre || '',
      apellido: (user as any).primer_apellido || '',
      sapellido: (user as any).segundo_apellido || '',
      cedula: user.cedula || '',
      celular: user.celular || '',
      correo_electronico: user.correo_electronico || '',
      fecha_nac: user.fecha_nac ? user.fecha_nac.substring(0, 10) : '',
      rol_id: user.rol_relation && user.rol_relation.length > 0 ? user.rol_relation[0].id_rol : null,
      username: user.nombre_usuario || ''
    });

    // Username should not be editable when updating
    this.userForm.get('username')?.disable();
    this.showDialog = true;
  }

  closeDialog() {
    this.showDialog = false;
  }

  toggleActive(user: Usuario) {
    this.userService.toggleActive(user.id).subscribe({
      next: () => this.loadUsers(),
      error: (err) => console.error('Error switching user status', err)
    });
  }

  deleteUser(id: number) {
    if (confirm('¿Está seguro de eliminar este usuario?')) {
      this.userService.deleteUsuario(id).subscribe({
        next: () => this.loadUsers(),
        error: (err) => console.error('Error deleting user', err)
      });
    }
  }

  resetPassword(user: Usuario) {
    this.userService.resetPassword(user.id).subscribe({
      next: (res) => {
        this.generatedPassword.set(res.generated_password);
        this.showResetPasswordDialog.set(true);
      },
      error: (err) => console.error('Error resetting password', err)
    });
  }

  resendInvitation(user: Usuario) {
    this.userService.resendInvitation(user.id).subscribe({
      next: (res) => {
        this.generatedInviteLink.set(res.invitation_link);
        this.showInviteLinkDialog.set(true);
        this.loadUsers();
      },
      error: (err) => console.error('Error resending invitation', err)
    });
  }

  getInvitationSeverity(status?: string): 'warn' | 'success' | 'danger' | 'info' {
    switch (status) {
      case 'pendiente': return 'warn';
      case 'aceptada': return 'success';
      case 'expirada': return 'danger';
      default: return 'info';
    }
  }

  getInvitationLabel(status?: string): string {
    switch (status) {
      case 'pendiente': return 'Invitación Pendiente';
      case 'aceptada': return 'Aceptada / Activo';
      case 'expirada': return 'Invitación Expirada';
      default: return 'Desconocido';
    }
  }

  toggleMenu(event: Event, user: Usuario, menu: any) {
    const items: MenuItem[] = [
      {
        label: 'Editar Información',
        icon: 'pi pi-pencil',
        command: () => this.openEditDialog(user)
      },
      {
        label: user.activo ? 'Desactivar Cuenta' : 'Activar Cuenta',
        icon: user.activo ? 'pi pi-user-minus' : 'pi pi-user-plus',
        command: () => this.toggleActive(user)
      },
      {
        label: 'Reestablecer Contraseña',
        icon: 'pi pi-refresh',
        command: () => this.resetPassword(user)
      }
    ];

    if (user.invitation_status !== 'aceptada') {
      items.push({
        label: 'Reenviar Invitación',
        icon: 'pi pi-send',
        command: () => this.resendInvitation(user)
      });
    }

    items.push({
      label: 'Eliminar Usuario',
      icon: 'pi pi-trash',
      command: () => this.deleteUser(user.id)
    });

    this.menuItemsForSelectedUser = items;
    menu.toggle(event);
  }

  onSubmit() {
    if (this.userForm.invalid) return;

    this.isSaving.set(true);
    this.errorMessage.set(undefined);

    const formData = this.userForm.getRawValue();
    const id = this.selectedUsuarioId();

    if (id !== null) {
      this.userService.updateUsuario(id, formData).subscribe({
        next: () => {
          this.isSaving.set(false);
          this.showDialog = false;
          this.loadUsers();
        },
        error: (err) => {
          this.isSaving.set(false);
          this.errorMessage.set(err.error?.error || 'Error al actualizar el usuario');
        }
      });
    } else {
      this.userService.createUsuario(formData).subscribe({
        next: (res) => {
          this.isSaving.set(false);
          this.showDialog = false;
          this.loadUsers();
          if (res.invitation_link) {
            this.generatedInviteLink.set(res.invitation_link);
            this.showInviteLinkDialog.set(true);
          }
        },
        error: (err) => {
          this.isSaving.set(false);
          this.errorMessage.set(err.error?.error || 'Error al registrar el usuario');
        }
      });
    }
  }
}

