import { Component, OnInit, signal, computed, inject } from '@angular/core';
import { CommonModule } from '@angular/common';
import { RouterLink } from '@angular/router';
import { TableModule } from 'primeng/table';
import { ButtonModule } from 'primeng/button';
import { DialogModule } from 'primeng/dialog';
import { TagModule } from 'primeng/tag';
import { SelectModule } from 'primeng/select';
import { InputTextModule } from 'primeng/inputtext';
import { MenuModule } from 'primeng/menu';
import { MenuItem } from 'primeng/api';
import { SolicitudService, Solicitud } from '../../../core/services/solicitud.service';
import { AuthService } from '../../../core/services/auth.service';

@Component({
  selector: 'app-solicitudes-list',
  standalone: true,
  imports: [
    CommonModule,
    RouterLink,
    TableModule,
    ButtonModule,
    DialogModule,
    TagModule,
    SelectModule,
    InputTextModule,
    MenuModule,
  ],
  templateUrl: './solicitudes-list.component.html',
  styleUrl: './solicitudes-list.component.css',
})
export class SolicitudesListComponent implements OnInit {
  private readonly solicitudService = inject(SolicitudService);
  private readonly authService = inject(AuthService);

  solicitudes = signal<Solicitud[]>([]);
  loading = signal(false);
  activeTab = signal<'created' | 'assigned'>('created');
  displayDetails = false;
  displayHistory = signal(false);
  selectedSolicitud = signal<Solicitud | null>(null);
  menuItemsForSelectedRow: MenuItem[] = [];

  currentUser = computed(() => this.authService.currentUserSignal());

  pendingCount = computed(() => this.solicitudes().filter((s) => s.estado === 1).length);
  approvedCount = computed(() => this.solicitudes().filter((s) => s.estado === 2).length);
  deniedCount = computed(() => this.solicitudes().filter((s) => s.estado === 3).length);

  filteredSolicitudes = computed(() => {
    // Already filtered by API request
    return this.solicitudes();
  });

  ngOnInit() {
    this.fetchData();
  }

  fetchData() {
    this.loading.set(true);
    if (this.activeTab() === 'created') {
      this.solicitudService.getSolicitudes().subscribe({
        next: (data) => {
          this.solicitudes.set(data);
          this.loading.set(false);
        },
        error: () => this.loading.set(false),
      });
    } else {
      this.solicitudService.getAsignadas().subscribe({
        next: (data) => {
          this.solicitudes.set(data);
          this.loading.set(false);
        },
        error: () => this.loading.set(false),
      });
    }
  }

  switchTab(tab: 'created' | 'assigned') {
    this.activeTab.set(tab);
    this.fetchData();
  }

  canViewAssigned(): boolean {
    return this.authService.hasOption('G_SOLICITUDES_ASIGNADAS');
  }

  canApproveDeny(solicitud: Solicitud): boolean {
    const user = this.currentUser();
    return user
      ? solicitud.encargado_relation?.uuid === user.uuid && [1, 2, 3].includes(solicitud.estado)
      : false;
  }

  canDelete(solicitud: Solicitud): boolean {
    const user = this.currentUser();
    return user
      ? (solicitud.solicitante_relation?.uuid === user.uuid && solicitud.estado === 1) ||
          this.authService.hasOption('REGISTRAR_USUARIOS')
      : false;
  }

  getStatusSeverity(estado: number): 'warn' | 'success' | 'danger' | 'info' {
    switch (estado) {
      case 1:
        return 'warn'; // Pendiente
      case 2:
        return 'success'; // Aprobada
      case 3:
        return 'danger'; // Denegada
      default:
        return 'info';
    }
  }

  showDetails(solicitud: Solicitud) {
    this.selectedSolicitud.set(solicitud);
    this.displayDetails = true;
  }

  showHistory(solicitud: Solicitud) {
    this.loading.set(true);
    this.solicitudService.getSolicitudByUuid(solicitud.uuid).subscribe({
      next: (data) => {
        this.selectedSolicitud.set(data);
        this.loading.set(false);
        this.displayHistory.set(true);
      },
      error: () => this.loading.set(false),
    });
  }

  toggleMenu(event: Event, solicitud: Solicitud, menu: any) {
    this.selectedSolicitud.set(solicitud);

    const items: MenuItem[] = [
      {
        label: 'Ver detalles',
        icon: 'pi pi-eye',
        command: () => this.showDetails(solicitud),
      },
      {
        label: 'Ver Historial',
        icon: 'pi pi-history',
        command: () => this.showHistory(solicitud),
      },
      ...(this.canApproveDeny(solicitud)
        ? [
            {
              label: 'Aprobar',
              icon: 'pi pi-check',
              command: () => this.updateStatus(solicitud, 2),
            },
            {
              label: 'Rechazar',
              icon: 'pi pi-times',
              command: () => this.updateStatus(solicitud, 3),
            },
          ]
        : []),
      ...(this.canDelete(solicitud)
        ? [
            {
              label: 'Eliminar',
              icon: 'pi pi-trash',
              command: () => this.deleteSolicitud(solicitud.uuid),
            },
          ]
        : []),
    ];

    this.menuItemsForSelectedRow = items;
    menu.toggle(event);
  }

  getStatusName(estado: number): string {
    switch (estado) {
      case 1:
        return 'Pendiente / En Trámite';
      case 2:
        return 'Aprobado Paso 1 / Segundo Paso';
      case 3:
        return 'Aprobado Paso 2 / Tercer Paso';
      case 4:
        return 'Rechazada';
      case 5:
        return 'Aprobada Completa';
      default:
        return 'Desconocido';
    }
  }

  updateStatus(solicitud: Solicitud, newStatus: number) {
    this.loading.set(true);
    const accion = newStatus === 2 ? 'Aprobar' : 'Denegar';
    this.solicitudService.procesarSolicitud(solicitud.uuid, accion).subscribe({
      next: () => {
        this.fetchData();
      },
      error: () => this.loading.set(false),
    });
  }

  deleteSolicitud(uuid: string) {
    if (confirm('¿Está seguro de eliminar esta solicitud?')) {
      this.loading.set(true);
      this.solicitudService.deleteSolicitud(uuid).subscribe({
        next: () => {
          this.fetchData();
        },
        error: () => this.loading.set(false),
      });
    }
  }
}
