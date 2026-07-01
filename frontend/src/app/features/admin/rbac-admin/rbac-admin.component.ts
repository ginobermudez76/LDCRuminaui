import { Component, OnInit, signal, inject } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormBuilder, FormGroup, FormsModule, ReactiveFormsModule, Validators } from '@angular/forms';
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
import { RbacAdminService, RbacRole, RbacOption, RbacEndpoint } from '../../../core/services/rbac-admin.service';

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
    MenuModule
  ],
  templateUrl: './rbac-admin.component.html',
  styleUrl: './rbac-admin.component.css'
})
export class RbacAdminComponent implements OnInit {
  private fb = inject(FormBuilder);
  private rbacService = inject(RbacAdminService);

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

  // Selected item states for editing/syncing
  selectedRoleId = signal<number | null>(null);
  selectedOptionId = signal<number | null>(null);
  selectedEndpointId = signal<number | null>(null);

  // Sync checkboxes state lists
  roleOptionsSelection = signal<number[]>([]);
  optionEndpointsSelection = signal<number[]>([]);

  rbacMenuItems: MenuItem[] = [];

  toggleRoleMenu(event: Event, role: RbacRole, menu: any) {
    this.selectedRoleId.set(role.id);
    this.rbacMenuItems = [
      {
        label: 'Asociar Menús',
        icon: 'pi pi-key',
        command: () => this.openSyncRoleOptions(role)
      },
      {
        label: 'Editar Rol',
        icon: 'pi pi-pencil',
        command: () => this.openEditRole(role)
      },
      {
        label: 'Eliminar Rol',
        icon: 'pi pi-trash',
        disabled: role.id <= 9,
        command: () => this.deleteRole(role.id)
      }
    ];
    menu.toggle(event);
  }

  toggleOptionMenu(event: Event, opt: RbacOption, menu: any) {
    this.selectedOptionId.set(opt.id);
    const essential = ['G_SOLICITUDES_PROPIAS', 'REGISTRAR_USUARIOS', 'G_SOLICITUDES_ASIGNADAS', 'APROBAR_SOLICITUDES', 'PUBLICAR_CONTENIDO', 'CONFIGURAR_RBAC'];
    this.rbacMenuItems = [
      {
        label: 'Asociar Endpoints',
        icon: 'pi pi-cog',
        command: () => this.openSyncOptionEndpoints(opt)
      },
      {
        label: 'Editar Menú',
        icon: 'pi pi-pencil',
        command: () => this.openEditOption(opt)
      },
      {
        label: 'Eliminar Menú',
        icon: 'pi pi-trash',
        disabled: essential.includes(opt.nombre_opcion),
        command: () => this.deleteOption(opt.id)
      }
    ];
    menu.toggle(event);
  }

  toggleEndpointMenu(event: Event, end: RbacEndpoint, menu: any) {
    this.selectedEndpointId.set(end.id);
    this.rbacMenuItems = [
      {
        label: 'Editar Permiso',
        icon: 'pi pi-pencil',
        command: () => this.openEditEndpoint(end)
      },
      {
        label: 'Eliminar Permiso',
        icon: 'pi pi-trash',
        disabled: end.url.includes('api/rbac'),
        command: () => this.deleteEndpoint(end.id)
      }
    ];
    menu.toggle(event);
  }

  // Method list for endpoints drop down
  httpMethods = [
    { label: 'GET', value: 'GET' },
    { label: 'POST', value: 'POST' },
    { label: 'PUT', value: 'PUT' },
    { label: 'DELETE', value: 'DELETE' },
    { label: 'PATCH', value: 'PATCH' }
  ];

  ngOnInit() {
    this.initForms();
    this.loadAllData();
  }

  private initForms() {
    this.roleForm = this.fb.group({
      codigo: ['', [Validators.required, Validators.maxLength(100)]],
      nombre_rol: ['', [Validators.required, Validators.maxLength(100)]],
      descripcion: ['', [Validators.maxLength(255)]]
    });

    this.optionForm = this.fb.group({
      nombre_opcion: ['', [Validators.required, Validators.maxLength(150)]],
      descripcion: ['', [Validators.maxLength(255)]]
    });

    this.endpointForm = this.fb.group({
      nombre_endpoint: ['', [Validators.required, Validators.maxLength(150)]],
      metodo: ['GET', [Validators.required]],
      url: ['', [Validators.required, Validators.maxLength(255)]],
      rbac_enabled: [true, [Validators.required]]
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
              error: (err) => this.handleError('Error al cargar endpoints: ' + err.message)
            });
          },
          error: (err) => this.handleError('Error al cargar menús: ' + err.message)
        });
      },
      error: (err) => this.handleError('Error al cargar roles: ' + err.message)
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
    this.selectedRoleId.set(null);
    this.roleForm.reset({ codigo: '', nombre_rol: '', descripcion: '' });
    this.roleDialogVisible.set(true);
  }

  openEditRole(role: RbacRole) {
    this.selectedRoleId.set(role.id);
    this.roleForm.patchValue({
      codigo: role.codigo,
      nombre_rol: role.nombre_rol,
      descripcion: role.descripcion
    });
    this.roleDialogVisible.set(true);
  }

  saveRole() {
    if (this.roleForm.invalid) return;

    this.isLoading.set(true);
    const data = this.roleForm.value;
    const roleId = this.selectedRoleId();

    if (roleId === null) {
      this.rbacService.createRole(data).subscribe({
        next: () => {
          this.showSuccess('Rol creado con éxito.');
          this.roleDialogVisible.set(false);
          this.loadAllData();
        },
        error: (err) => this.handleError(err.error?.error || 'Error al crear rol.')
      });
    } else {
      this.rbacService.updateRole(roleId, data).subscribe({
        next: () => {
          this.showSuccess('Rol actualizado con éxito.');
          this.roleDialogVisible.set(false);
          this.loadAllData();
        },
        error: (err) => this.handleError(err.error?.error || 'Error al actualizar rol.')
      });
    }
  }

  deleteRole(id: number) {
    if (!confirm('¿Está seguro de eliminar este rol?')) return;

    this.isLoading.set(true);
    this.rbacService.deleteRole(id).subscribe({
      next: () => {
        this.showSuccess('Rol eliminado con éxito.');
        this.loadAllData();
      },
      error: (err) => this.handleError(err.error?.error || 'Error al eliminar rol.')
    });
  }

  openSyncRoleOptions(role: RbacRole) {
    this.selectedRoleId.set(role.id);
    // Gather current option ids
    const activeOptionIds = (role.opciones || []).map(o => o.id);
    this.roleOptionsSelection.set(activeOptionIds);
    this.syncOptionsDialogVisible.set(true);
  }

  toggleRoleOption(optId: number) {
    const current = [...this.roleOptionsSelection()];
    const idx = current.indexOf(optId);
    if (idx === -1) {
      current.push(optId);
    } else {
      current.splice(idx, 1);
    }
    this.roleOptionsSelection.set(current);
  }

  saveRoleOptions() {
    const roleId = this.selectedRoleId();
    if (roleId === null) return;

    this.isLoading.set(true);
    this.rbacService.syncRoleOptions(roleId, this.roleOptionsSelection()).subscribe({
      next: () => {
        this.showSuccess('Permisos de menú sincronizados con éxito.');
        this.syncOptionsDialogVisible.set(false);
        this.loadAllData();
      },
      error: (err) => this.handleError(err.error?.error || 'Error al sincronizar opciones.')
    });
  }

  // ==========================================
  // OPTIONS ACTIONS
  // ==========================================

  openNewOption() {
    this.selectedOptionId.set(null);
    this.optionForm.reset({ nombre_opcion: '', descripcion: '' });
    this.optionDialogVisible.set(true);
  }

  openEditOption(opt: RbacOption) {
    this.selectedOptionId.set(opt.id);
    this.optionForm.patchValue({
      nombre_opcion: opt.nombre_opcion,
      descripcion: opt.descripcion
    });
    this.optionDialogVisible.set(true);
  }

  saveOption() {
    if (this.optionForm.invalid) return;

    this.isLoading.set(true);
    const data = this.optionForm.value;
    const optId = this.selectedOptionId();

    if (optId === null) {
      this.rbacService.createOption(data).subscribe({
        next: () => {
          this.showSuccess('Menú creado con éxito.');
          this.optionDialogVisible.set(false);
          this.loadAllData();
        },
        error: (err) => this.handleError(err.error?.error || 'Error al crear opción.')
      });
    } else {
      this.rbacService.updateOption(optId, data).subscribe({
        next: () => {
          this.showSuccess('Menú actualizado con éxito.');
          this.optionDialogVisible.set(false);
          this.loadAllData();
        },
        error: (err) => this.handleError(err.error?.error || 'Error al actualizar opción.')
      });
    }
  }

  deleteOption(id: number) {
    if (!confirm('¿Está seguro de eliminar esta opción de menú?')) return;

    this.isLoading.set(true);
    this.rbacService.deleteOption(id).subscribe({
      next: () => {
        this.showSuccess('Menú eliminado con éxito.');
        this.loadAllData();
      },
      error: (err) => this.handleError(err.error?.error || 'Error al eliminar opción.')
    });
  }

  openSyncOptionEndpoints(opt: RbacOption) {
    this.selectedOptionId.set(opt.id);
    const activeEndpointIds = (opt.endpoints || []).map(e => e.id);
    this.optionEndpointsSelection.set(activeEndpointIds);
    this.syncEndpointsDialogVisible.set(true);
  }

  toggleOptionEndpoint(endId: number) {
    const current = [...this.optionEndpointsSelection()];
    const idx = current.indexOf(endId);
    if (idx === -1) {
      current.push(endId);
    } else {
      current.splice(idx, 1);
    }
    this.optionEndpointsSelection.set(current);
  }

  saveOptionEndpoints() {
    const optId = this.selectedOptionId();
    if (optId === null) return;

    this.isLoading.set(true);
    this.rbacService.syncOptionEndpoints(optId, this.optionEndpointsSelection()).subscribe({
      next: () => {
        this.showSuccess('Permisos de endpoints sincronizados con éxito.');
        this.syncEndpointsDialogVisible.set(false);
        this.loadAllData();
      },
      error: (err) => this.handleError(err.error?.error || 'Error al sincronizar endpoints.')
    });
  }

  // ==========================================
  // ENDPOINTS ACTIONS
  // ==========================================

  openNewEndpoint() {
    this.selectedEndpointId.set(null);
    this.endpointForm.reset({ nombre_endpoint: '', metodo: 'GET', url: '', rbac_enabled: true });
    this.endpointDialogVisible.set(true);
  }

  openEditEndpoint(end: RbacEndpoint) {
    this.selectedEndpointId.set(end.id);
    this.endpointForm.patchValue({
      nombre_endpoint: end.nombre_endpoint,
      metodo: end.metodo,
      url: end.url,
      rbac_enabled: end.rbac_enabled
    });
    this.endpointDialogVisible.set(true);
  }

  saveEndpoint() {
    if (this.endpointForm.invalid) return;

    this.isLoading.set(true);
    const data = this.endpointForm.value;
    const endId = this.selectedEndpointId();

    if (endId === null) {
      this.rbacService.createEndpoint(data).subscribe({
        next: () => {
          this.showSuccess('Endpoint creado con éxito.');
          this.endpointDialogVisible.set(false);
          this.loadAllData();
        },
        error: (err) => this.handleError(err.error?.error || 'Error al crear endpoint.')
      });
    } else {
      this.rbacService.updateEndpoint(endId, data).subscribe({
        next: () => {
          this.showSuccess('Endpoint actualizado con éxito.');
          this.endpointDialogVisible.set(false);
          this.loadAllData();
        },
        error: (err) => this.handleError(err.error?.error || 'Error al actualizar endpoint.')
      });
    }
  }

  deleteEndpoint(id: number) {
    if (!confirm('¿Está seguro de eliminar este endpoint?')) return;

    this.isLoading.set(true);
    this.rbacService.deleteEndpoint(id).subscribe({
      next: () => {
        this.showSuccess('Endpoint eliminado con éxito.');
        this.loadAllData();
      },
      error: (err) => this.handleError(err.error?.error || 'Error al eliminar endpoint.')
    });
  }
}
