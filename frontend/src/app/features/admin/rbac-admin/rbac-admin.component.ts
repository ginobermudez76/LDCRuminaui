import { Component, OnInit, signal, inject } from '@angular/core';
import { CommonModule } from '@angular/common';
import {
  FormBuilder,
  FormGroup,
  FormsModule,
  ReactiveFormsModule,
  Validators,
} from '@angular/forms';
import { CardModule } from 'primeng/card';
import { InputTextModule } from 'primeng/inputtext';
import { ButtonModule } from 'primeng/button';
import { TableModule } from 'primeng/table';
import { DialogModule } from 'primeng/dialog';
import { CheckboxModule } from 'primeng/checkbox';
import { MessageModule } from 'primeng/message';
import { SelectModule } from 'primeng/select';
import { MenuModule } from 'primeng/menu';
import { MenuItem } from 'primeng/api';
import {
  RbacAdminService,
  RbacRole,
  RbacOption,
  RbacEndpoint,
} from '../../../core/services/rbac-admin.service';

@Component({
  selector: 'app-rbac-admin',
  standalone: true,
  imports: [
    CommonModule,
    FormsModule,
    ReactiveFormsModule,
    CardModule,
    InputTextModule,
    ButtonModule,
    TableModule,
    DialogModule,
    CheckboxModule,
    MessageModule,
    SelectModule,
    MenuModule,
  ],
  templateUrl: './rbac-admin.component.html',
  styleUrl: './rbac-admin.component.css',
})
export class RbacAdminComponent implements OnInit {
  private readonly fb = inject(FormBuilder);
  private readonly rbacService = inject(RbacAdminService);

  // Active section state
  activeTab = signal<'roles' | 'opciones' | 'endpoints'>('roles');

  // Database lists
  roles = signal<RbacRole[]>([]);
  options = signal<RbacOption[]>([]);
  endpoints = signal<RbacEndpoint[]>([]);

  // Loading and Error states
  isLoading = signal(false);
  errorMessage = signal<string | null>(null);
  successMessage = signal<string | null>(null);

  // Dialog visibilities
  roleDialogVisible = signal(false);
  optionDialogVisible = signal(false);
  endpointDialogVisible = signal(false);
  syncOptionsDialogVisible = signal(false);
  syncEndpointsDialogVisible = signal(false);

  // Forms
  roleForm!: FormGroup;
  optionForm!: FormGroup;
  endpointForm!: FormGroup;

  // Selected item states for editing/syncing (UUID-based)
  selectedRoleUuid = signal<string | null>(null);
  selectedOptionUuid = signal<string | null>(null);
  selectedEndpointUuid = signal<string | null>(null);

  // Sync checkboxes state lists (UUID-based)
  roleOptionsSelection = signal<string[]>([]);
  optionEndpointsSelection = signal<string[]>([]);

  rbacMenuItems: MenuItem[] = [];

  toggleRoleMenu(event: Event, role: RbacRole, menu: any) {
    this.selectedRoleUuid.set(role.uuid);
    this.rbacMenuItems = [
      {
        label: 'Asociar Menús',
        icon: 'pi pi-key',
        command: () => this.openSyncRoleOptions(role),
      },
      {
        label: 'Editar Rol',
        icon: 'pi pi-pencil',
        command: () => this.openEditRole(role),
      },
      {
        label: 'Eliminar Rol',
        icon: 'pi pi-trash',
        disabled: role.id !== undefined && role.id <= 9,
        command: () => this.deleteRole(role.uuid),
      },
    ];
    menu.toggle(event);
  }

  toggleOptionMenu(event: Event, opt: RbacOption, menu: any) {
    this.selectedOptionUuid.set(opt.uuid);
    const essential = [
      'G_SOLICITUDES_PROPIAS',
      'REGISTRAR_USUARIOS',
      'G_SOLICITUDES_ASIGNADAS',
      'APROBAR_SOLICITUDES',
      'PUBLICAR_CONTENIDO',
      'CONFIGURAR_RBAC',
    ];
    this.rbacMenuItems = [
      {
        label: 'Asociar Endpoints',
        icon: 'pi pi-cog',
        command: () => this.openSyncOptionEndpoints(opt),
      },
      {
        label: 'Editar Menú',
        icon: 'pi pi-pencil',
        command: () => this.openEditOption(opt),
      },
      {
        label: 'Eliminar Menú',
        icon: 'pi pi-trash',
        disabled: essential.includes(opt.nombre_opcion),
        command: () => this.deleteOption(opt.uuid),
      },
    ];
    menu.toggle(event);
  }

  toggleEndpointMenu(event: Event, end: RbacEndpoint, menu: any) {
    this.selectedEndpointUuid.set(end.uuid);
    this.rbacMenuItems = [
      {
        label: 'Editar Permiso',
        icon: 'pi pi-pencil',
        command: () => this.openEditEndpoint(end),
      },
      {
        label: 'Eliminar Permiso',
        icon: 'pi pi-trash',
        disabled: end.url.includes('api/rbac'),
        command: () => this.deleteEndpoint(end.uuid),
      },
    ];
    menu.toggle(event);
  }

  // Method list for endpoints drop down
  httpMethods = [
    { label: 'GET', value: 'GET' },
    { label: 'POST', value: 'POST' },
    { label: 'PUT', value: 'PUT' },
    { label: 'DELETE', value: 'DELETE' },
    { label: 'PATCH', value: 'PATCH' },
  ];

  ngOnInit() {
    this.initForms();
    this.loadAllData();
  }

  private initForms() {
    this.roleForm = this.fb.group({
      codigo: ['', [Validators.required, Validators.maxLength(100)]],
      nombre_rol: ['', [Validators.required, Validators.maxLength(100)]],
      descripcion: ['', [Validators.maxLength(255)]],
    });

    this.optionForm = this.fb.group({
      nombre_opcion: ['', [Validators.required, Validators.maxLength(150)]],
      descripcion: ['', [Validators.maxLength(255)]],
    });

    this.endpointForm = this.fb.group({
      nombre_endpoint: ['', [Validators.required, Validators.maxLength(150)]],
      metodo: ['GET', [Validators.required]],
      url: ['', [Validators.required, Validators.maxLength(255)]],
      rbac_enabled: [true, [Validators.required]],
    });
  }

  loadAllData() {
    this.isLoading.set(true);
    this.errorMessage.set(null);

    // Initial load sequence
    this.rbacService.getRoles().subscribe({
      next: (roles) => {
        this.roles.set(roles);
        this.rbacService.getOptions().subscribe({
          next: (opts) => {
            this.options.set(opts);
            this.rbacService.getEndpoints().subscribe({
              next: (ends) => {
                this.endpoints.set(ends);
                this.isLoading.set(false);
              },
              error: (err) => this.handleError('Error al cargar endpoints: ' + err.message),
            });
          },
          error: (err) => this.handleError('Error al cargar menús: ' + err.message),
        });
      },
      error: (err) => this.handleError('Error al cargar roles: ' + err.message),
    });
  }

  setTab(tab: 'roles' | 'opciones' | 'endpoints') {
    this.activeTab.set(tab);
    this.errorMessage.set(null);
    this.successMessage.set(null);
  }

  private handleError(msg: string) {
    this.errorMessage.set(msg);
    this.isLoading.set(false);
    setTimeout(() => this.errorMessage.set(null), 5000);
  }

  private showSuccess(msg: string) {
    this.successMessage.set(msg);
    setTimeout(() => this.successMessage.set(null), 4000);
  }

  // ==========================================
  // ROLES ACTIONS
  // ==========================================

  openNewRole() {
    this.selectedRoleUuid.set(null);
    this.roleForm.reset({ codigo: '', nombre_rol: '', descripcion: '' });
    this.roleDialogVisible.set(true);
  }

  openEditRole(role: RbacRole) {
    this.selectedRoleUuid.set(role.uuid);
    this.roleForm.patchValue({
      codigo: role.codigo,
      nombre_rol: role.nombre_rol,
      descripcion: role.descripcion,
    });
    this.roleDialogVisible.set(true);
  }

  saveRole() {
    if (this.roleForm.invalid) return;

    this.isLoading.set(true);
    const data = this.roleForm.value;
    const roleUuid = this.selectedRoleUuid();

    if (roleUuid === null) {
      this.rbacService.createRole(data).subscribe({
        next: () => {
          this.showSuccess('Rol creado con éxito.');
          this.roleDialogVisible.set(false);
          this.loadAllData();
        },
        error: (err) => this.handleError(err.error?.error || 'Error al crear rol.'),
      });
    } else {
      this.rbacService.updateRole(roleUuid, data).subscribe({
        next: () => {
          this.showSuccess('Rol actualizado con éxito.');
          this.roleDialogVisible.set(false);
          this.loadAllData();
        },
        error: (err) => this.handleError(err.error?.error || 'Error al actualizar rol.'),
      });
    }
  }

  deleteRole(uuid: string) {
    if (!confirm('¿Está seguro de eliminar este rol?')) return;

    this.isLoading.set(true);
    this.rbacService.deleteRole(uuid).subscribe({
      next: () => {
        this.showSuccess('Rol eliminado con éxito.');
        this.loadAllData();
      },
      error: (err) => this.handleError(err.error?.error || 'Error al eliminar rol.'),
    });
  }

  openSyncRoleOptions(role: RbacRole) {
    this.selectedRoleUuid.set(role.uuid);
    // Gather current option uuids
    const activeOptionUuids = (role.opciones || []).map((o) => o.uuid);
    this.roleOptionsSelection.set(activeOptionUuids);
    this.syncOptionsDialogVisible.set(true);
  }

  toggleRoleOption(optUuid: string) {
    const current = [...this.roleOptionsSelection()];
    const idx = current.indexOf(optUuid);
    if (idx === -1) {
      current.push(optUuid);
    } else {
      current.splice(idx, 1);
    }
    this.roleOptionsSelection.set(current);
  }

  saveRoleOptions() {
    const roleUuid = this.selectedRoleUuid();
    if (roleUuid === null) return;

    this.isLoading.set(true);
    this.rbacService.syncRoleOptions(roleUuid, this.roleOptionsSelection()).subscribe({
      next: () => {
        this.showSuccess('Permisos de menú sincronizados con éxito.');
        this.syncOptionsDialogVisible.set(false);
        this.loadAllData();
      },
      error: (err) => this.handleError(err.error?.error || 'Error al sincronizar opciones.'),
    });
  }

  // ==========================================
  // OPTIONS ACTIONS
  // ==========================================

  openNewOption() {
    this.selectedOptionUuid.set(null);
    this.optionForm.reset({ nombre_opcion: '', descripcion: '' });
    this.optionDialogVisible.set(true);
  }

  openEditOption(opt: RbacOption) {
    this.selectedOptionUuid.set(opt.uuid);
    this.optionForm.patchValue({
      nombre_opcion: opt.nombre_opcion,
      descripcion: opt.descripcion,
    });
    this.optionDialogVisible.set(true);
  }

  saveOption() {
    if (this.optionForm.invalid) return;

    this.isLoading.set(true);
    const data = this.optionForm.value;
    const optUuid = this.selectedOptionUuid();

    if (optUuid === null) {
      this.rbacService.createOption(data).subscribe({
        next: () => {
          this.showSuccess('Menú creado con éxito.');
          this.optionDialogVisible.set(false);
          this.loadAllData();
        },
        error: (err) => this.handleError(err.error?.error || 'Error al crear opción.'),
      });
    } else {
      this.rbacService.updateOption(optUuid, data).subscribe({
        next: () => {
          this.showSuccess('Menú actualizado con éxito.');
          this.optionDialogVisible.set(false);
          this.loadAllData();
        },
        error: (err) => this.handleError(err.error?.error || 'Error al actualizar opción.'),
      });
    }
  }

  deleteOption(uuid: string) {
    if (!confirm('¿Está seguro de eliminar esta opción de menú?')) return;

    this.isLoading.set(true);
    this.rbacService.deleteOption(uuid).subscribe({
      next: () => {
        this.showSuccess('Menú eliminado con éxito.');
        this.loadAllData();
      },
      error: (err) => this.handleError(err.error?.error || 'Error al eliminar opción.'),
    });
  }

  openSyncOptionEndpoints(opt: RbacOption) {
    this.selectedOptionUuid.set(opt.uuid);
    const activeEndpointUuids = (opt.endpoints || []).map((e) => e.uuid);
    this.optionEndpointsSelection.set(activeEndpointUuids);
    this.syncEndpointsDialogVisible.set(true);
  }

  toggleOptionEndpoint(endUuid: string) {
    const current = [...this.optionEndpointsSelection()];
    const idx = current.indexOf(endUuid);
    if (idx === -1) {
      current.push(endUuid);
    } else {
      current.splice(idx, 1);
    }
    this.optionEndpointsSelection.set(current);
  }

  saveOptionEndpoints() {
    const optUuid = this.selectedOptionUuid();
    if (optUuid === null) return;

    this.isLoading.set(true);
    this.rbacService.syncOptionEndpoints(optUuid, this.optionEndpointsSelection()).subscribe({
      next: () => {
        this.showSuccess('Permisos de endpoints sincronizados con éxito.');
        this.syncEndpointsDialogVisible.set(false);
        this.loadAllData();
      },
      error: (err) => this.handleError(err.error?.error || 'Error al sincronizar endpoints.'),
    });
  }

  // ==========================================
  // ENDPOINTS ACTIONS
  // ==========================================

  openNewEndpoint() {
    this.selectedEndpointUuid.set(null);
    this.endpointForm.reset({ nombre_endpoint: '', metodo: 'GET', url: '', rbac_enabled: true });
    this.endpointDialogVisible.set(true);
  }

  openEditEndpoint(end: RbacEndpoint) {
    this.selectedEndpointUuid.set(end.uuid);
    this.endpointForm.patchValue({
      nombre_endpoint: end.nombre_endpoint,
      metodo: end.metodo,
      url: end.url,
      rbac_enabled: end.rbac_enabled,
    });
    this.endpointDialogVisible.set(true);
  }

  saveEndpoint() {
    if (this.endpointForm.invalid) return;

    this.isLoading.set(true);
    const data = this.endpointForm.value;
    const endUuid = this.selectedEndpointUuid();

    if (endUuid === null) {
      this.rbacService.createEndpoint(data).subscribe({
        next: () => {
          this.showSuccess('Endpoint creado con éxito.');
          this.endpointDialogVisible.set(false);
          this.loadAllData();
        },
        error: (err) => this.handleError(err.error?.error || 'Error al crear endpoint.'),
      });
    } else {
      this.rbacService.updateEndpoint(endUuid, data).subscribe({
        next: () => {
          this.showSuccess('Endpoint actualizado con éxito.');
          this.endpointDialogVisible.set(false);
          this.loadAllData();
        },
        error: (err) => this.handleError(err.error?.error || 'Error al actualizar endpoint.'),
      });
    }
  }

  deleteEndpoint(uuid: string) {
    if (!confirm('¿Está seguro de eliminar este endpoint?')) return;

    this.isLoading.set(true);
    this.rbacService.deleteEndpoint(uuid).subscribe({
      next: () => {
        this.showSuccess('Endpoint eliminado con éxito.');
        this.loadAllData();
      },
      error: (err) => this.handleError(err.error?.error || 'Error al eliminar endpoint.'),
    });
  }
}
