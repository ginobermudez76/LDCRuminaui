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
  template: `
    <div class="user-admin-container">
      <header class="page-header">
        <h1 class="title">Administración de Usuarios</h1>
        <p class="subtitle">Gestiona los usuarios registrados y sus roles en el sistema.</p>
      </header>

      <div class="table-card">
        <p-table [value]="usuarios()" [paginator]="true" [rows]="10" responsiveLayout="scroll" styleClass="p-datatable-sm p-datatable-striped">
          <ng-template pTemplate="caption">
            <div class="flex justify-between items-center">
              <span class="table-title">Lista de Usuarios</span>
              <button pButton type="button" icon="pi pi-user-plus" label="Nuevo Usuario" class="p-button-primary p-button-sm" (click)="openNewDialog()"></button>
            </div>
          </ng-template>
          <ng-template pTemplate="header">
            <tr>
              <th>ID</th>
              <th>Nombres Completos</th>
              <th>Usuario</th>
              <th>Correo</th>
              <th>Cédula</th>
              <th>Rol Principal</th>
              <th>Estado</th>
            </tr>
          </ng-template>
          <ng-template pTemplate="body" let-user>
            <tr>
              <td>{{ user.id }}</td>
              <td>{{ user.nombres }} {{ user.apellidos }}</td>
              <td><span class="user-badge">{{ user.nombre_usuario }}</span></td>
              <td>{{ user.correo_electronico }}</td>
              <td>{{ user.cedula }}</td>
              <td>
                <span class="role-badge" *ngIf="user.rol_relation && user.rol_relation.length > 0">
                  {{ user.rol_relation[0].rol?.nombre_rol }}
                </span>
              </td>
              <td>
                <span class="status-badge" [class.inactive]="!user.activo">
                  {{ user.activo ? 'Activo' : 'Inactivo' }}
                </span>
              </td>
            </tr>
          </ng-template>
          <ng-template pTemplate="emptymessage">
            <tr>
              <td colspan="7" class="text-center p-4 text-gray-500">No hay usuarios registrados.</td>
            </tr>
          </ng-template>
        </p-table>
      </div>

      <p-dialog [(visible)]="showDialog" [header]="'Registrar Nuevo Usuario'" [modal]="true" [style]="{width: '600px'}" styleClass="custom-dialog">
        <form [formGroup]="userForm" (ngSubmit)="onSubmit()" class="user-form">
          
          <div class="form-grid">
            <div class="form-field">
              <label for="nombre">Primer Nombre *</label>
              <input pInputText id="nombre" formControlName="nombre" />
            </div>
            <div class="form-field">
              <label for="snombre">Segundo Nombre</label>
              <input pInputText id="snombre" formControlName="snombre" />
            </div>
            
            <div class="form-field">
              <label for="apellido">Primer Apellido *</label>
              <input pInputText id="apellido" formControlName="apellido" />
            </div>
            <div class="form-field">
              <label for="sapellido">Segundo Apellido</label>
              <input pInputText id="sapellido" formControlName="sapellido" />
            </div>

            <div class="form-field">
              <label for="cedula">Cédula *</label>
              <input pInputText id="cedula" formControlName="cedula" maxlength="10" />
            </div>
            <div class="form-field">
              <label for="celular">Celular</label>
              <input pInputText id="celular" formControlName="celular" maxlength="10" />
            </div>

            <div class="form-field">
              <label for="fecha_nac">Fecha de Nacimiento *</label>
              <input type="date" pInputText id="fecha_nac" formControlName="fecha_nac" />
            </div>
            
            <div class="form-field">
              <label for="rol_id">Rol Asignado *</label>
              <p-select [options]="roles()" optionLabel="nombre_rol" optionValue="id" formControlName="rol_id" [style]="{width:'100%'}"></p-select>
            </div>
            
            <div class="form-field full-width">
              <label for="correo_electronico">Correo Electrónico *</label>
              <input type="email" pInputText id="correo_electronico" formControlName="correo_electronico" />
            </div>
          </div>

          <div class="auto-generated-section">
            <h4 class="auto-title"><i class="pi pi-cog"></i> Credenciales Autogeneradas</h4>
            <p class="auto-desc">Estas credenciales se generan automáticamente y serán guardadas de forma segura.</p>
            
            <div class="form-grid">
              <div class="form-field">
                <label for="username">Usuario Autogenerado</label>
                <input pInputText id="username" formControlName="username" readonly class="readonly-input" />
              </div>
              <div class="form-field">
                <label for="password">Contraseña Autogenerada</label>
                <input pInputText id="password" formControlName="password" readonly class="readonly-input" />
              </div>
            </div>
          </div>

          <div class="error-wrapper" *ngIf="errorMessage()">
            <p-message severity="error" [text]="errorMessage() || ''"></p-message>
          </div>

          <div class="dialog-footer">
            <button pButton type="button" label="Cancelar" class="p-button-text p-button-secondary" (click)="closeDialog()"></button>
            <button pButton type="submit" label="Registrar Usuario" icon="pi pi-check" [disabled]="userForm.invalid" [loading]="isSaving()"></button>
          </div>
        </form>
      </p-dialog>
    </div>
  `,
  styles: [`
    .user-admin-container {
      display: flex;
      flex-direction: column;
      gap: 24px;
      padding: 10px;
    }
    .page-header {
      background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
      padding: 24px;
      border-radius: 12px;
      color: white;
      box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }
    .title {
      margin: 0 0 8px 0;
      font-size: 24px;
      font-weight: 700;
    }
    .subtitle {
      margin: 0;
      opacity: 0.9;
      font-size: 14px;
    }
    .table-card {
      background: white;
      border-radius: 12px;
      padding: 20px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.05);
      border: 1px solid #e2e8f0;
    }
    .table-title {
      font-size: 18px;
      font-weight: 700;
      color: #1e293b;
    }
    .flex { display: flex; }
    .justify-between { justify-content: space-between; }
    .items-center { align-items: center; }
    
    .user-badge {
      font-weight: 600;
      color: #2563eb;
      background: #eff6ff;
      padding: 4px 8px;
      border-radius: 4px;
      font-family: monospace;
    }
    .role-badge {
      font-size: 12px;
      background: #f1f5f9;
      color: #475569;
      padding: 4px 10px;
      border-radius: 20px;
      font-weight: 600;
      border: 1px solid #e2e8f0;
    }
    .status-badge {
      font-size: 11px;
      padding: 3px 8px;
      border-radius: 9999px;
      background: #dcfce7;
      color: #166534;
      font-weight: 600;
    }
    .status-badge.inactive {
      background: #fee2e2;
      color: #991b1b;
    }

    .user-form {
      display: flex;
      flex-direction: column;
      gap: 20px;
      margin-top: 10px;
    }
    .form-grid {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 16px;
    }
    .form-field {
      display: flex;
      flex-direction: column;
      gap: 6px;
    }
    .form-field.full-width {
      grid-column: 1 / -1;
    }
    .form-field label {
      font-size: 13px;
      font-weight: 600;
      color: #475569;
    }
    
    .auto-generated-section {
      background: #f8fafc;
      border: 1px dashed #cbd5e1;
      padding: 16px;
      border-radius: 8px;
      margin-top: 8px;
    }
    .auto-title {
      margin: 0 0 4px 0;
      font-size: 14px;
      color: #1e3c72;
      display: flex;
      align-items: center;
      gap: 6px;
    }
    .auto-desc {
      margin: 0 0 12px 0;
      font-size: 12px;
      color: #64748b;
    }
    .readonly-input {
      background-color: #e2e8f0 !important;
      color: #334155 !important;
      font-family: monospace;
      font-weight: 600;
    }
    
    .dialog-footer {
      display: flex;
      justify-content: flex-end;
      gap: 12px;
      margin-top: 24px;
      padding-top: 16px;
      border-top: 1px solid #e2e8f0;
    }
  `]
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
