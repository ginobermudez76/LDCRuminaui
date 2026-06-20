import { Component, OnInit, signal, computed, inject } from '@angular/core';
import { CommonModule } from '@angular/common';
import { RouterLink } from '@angular/router';
import { TableModule } from 'primeng/table';
import { ButtonModule } from 'primeng/button';
import { DialogModule } from 'primeng/dialog';
import { TagModule } from 'primeng/tag';
import { SelectModule } from 'primeng/select';
import { InputTextModule } from 'primeng/inputtext';
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
    InputTextModule
  ],
  template: `
    <div class="solicitudes-container">
      <div class="header-section">
        <h1 class="page-title">Listado de Solicitudes</h1>
        <button 
          pButton 
          icon="pi pi-plus" 
          label="Nueva Solicitud" 
          routerLink="/dashboard/solicitudes/nueva" 
          class="p-button-success"
        ></button>
      </div>

      <!-- Quick Summary Cards -->
      <div class="stats-grid">
        <div class="stat-card pending">
          <div class="stat-icon"><i class="pi pi-clock"></i></div>
          <div class="stat-info">
            <span class="stat-value">{{ pendingCount() }}</span>
            <span class="stat-label">Pendientes</span>
          </div>
        </div>
        <div class="stat-card approved">
          <div class="stat-icon"><i class="pi pi-check-circle"></i></div>
          <div class="stat-info">
            <span class="stat-value">{{ approvedCount() }}</span>
            <span class="stat-label">Aprobadas</span>
          </div>
        </div>
        <div class="stat-card denied">
          <div class="stat-icon"><i class="pi pi-times-circle"></i></div>
          <div class="stat-info">
            <span class="stat-value">{{ deniedCount() }}</span>
            <span class="stat-label">Denegadas</span>
          </div>
        </div>
      </div>

      <!-- Filter Buttons for Agents/Admins -->
      <div class="filter-bar" *ngIf="canViewAssigned()">
        <span class="p-buttonset">
          <button 
            pButton 
            label="Creadas por mí" 
            icon="pi pi-user" 
            [class.p-button-outlined]="activeTab() !== 'created'"
            (click)="switchTab('created')"
          ></button>
          <button 
            pButton 
            label="Asignadas a mí" 
            icon="pi pi-users" 
            [class.p-button-outlined]="activeTab() !== 'assigned'"
            (click)="switchTab('assigned')"
          ></button>
        </span>
      </div>

      <!-- Data Table -->
      <div class="card table-card">
        <p-table 
          [value]="filteredSolicitudes()" 
          [paginator]="true" 
          [rows]="10" 
          [loading]="loading()"
          [rowsPerPageOptions]="[5, 10, 20]"
          responsiveLayout="stack"
          styleClass="p-datatable-striped p-datatable-gridlines"
        >
          <ng-template pTemplate="header">
            <tr>
              <th>ID</th>
              <th>Fecha</th>
              <th>Tipo</th>
              <th>Descripción</th>
              <th>Solicitante</th>
              <th>Encargado</th>
              <th>Valor</th>
              <th>Estado</th>
              <th style="width: 150px">Acciones</th>
            </tr>
          </ng-template>
          <ng-template pTemplate="body" let-solicitud>
            <tr>
              <td>{{ solicitud.s_id }}</td>
              <td>{{ solicitud.s_fecha | date: 'dd/MM/yyyy HH:mm' }}</td>
              <td>{{ solicitud.tipo_relation?.name_tipo || 'Desconocido' }}</td>
              <td class="text-truncate">{{ solicitud.descripcion || 'Sin descripción' }}</td>
              <td>{{ solicitud.solicitante_relation?.nombre_usuario || 'N/A' }}</td>
              <td>{{ solicitud.encargado_relation?.nombre_usuario || 'Sin asignar' }}</td>
              <td>\${{ solicitud.s_valor | number: '1.2-2' }}</td>
              <td>
                <p-tag 
                  [value]="solicitud.estado_relation?.estado_nombre || 'Pendiente'" 
                  [severity]="getStatusSeverity(solicitud.estado)"
                ></p-tag>
              </td>
              <td>
                <div class="actions-wrapper">
                  <button 
                    pButton 
                    icon="pi pi-eye" 
                    class="p-button-rounded p-button-info p-button-text" 
                    title="Ver detalles"
                    (click)="showDetails(solicitud)"
                  ></button>
                  <button 
                    *ngIf="canApproveDeny(solicitud)"
                    pButton 
                    icon="pi pi-check" 
                    class="p-button-rounded p-button-success p-button-text" 
                    title="Aprobar"
                    (click)="updateStatus(solicitud, 2)"
                  ></button>
                  <button 
                    *ngIf="canApproveDeny(solicitud)"
                    pButton 
                    icon="pi pi-times" 
                    class="p-button-rounded p-button-danger p-button-text" 
                    title="Rechazar"
                    (click)="updateStatus(solicitud, 3)"
                  ></button>
                  <button 
                    *ngIf="canDelete(solicitud)"
                    pButton 
                    icon="pi pi-trash" 
                    class="p-button-rounded p-button-danger p-button-text" 
                    title="Eliminar"
                    (click)="deleteSolicitud(solicitud.s_id)"
                  ></button>
                </div>
              </td>
            </tr>
          </ng-template>
          <ng-template pTemplate="emptymessage">
            <tr>
              <td colspan="9" class="text-center p-4">No se encontraron solicitudes.</td>
            </tr>
          </ng-template>
        </p-table>
      </div>
    </div>

    <!-- Request Details Modal -->
    <p-dialog 
      header="Detalles de la Solicitud #{{ selectedSolicitud()?.s_id }}" 
      [(visible)]="displayDetails" 
      [modal]="true" 
      [style]="{width: '50vw'}" 
      [breakpoints]="{'960px': '75vw', '640px': '100vw'}"
      [draggable]="false" 
      [resizable]="false"
    >
      <div class="details-grid" *ngIf="selectedSolicitud()">
        <div class="detail-row">
          <strong>Fecha de Registro:</strong>
          <span>{{ selectedSolicitud()?.s_fecha | date: 'dd/MM/yyyy HH:mm:ss' }}</span>
        </div>
        <div class="detail-row">
          <strong>Tipo de Solicitud:</strong>
          <span>{{ selectedSolicitud()?.tipo_relation?.name_tipo }}</span>
        </div>
        <div class="detail-row">
          <strong>Valor:</strong>
          <span>\${{ selectedSolicitud()?.s_valor | number: '1.2-2' }}</span>
        </div>
        <div class="detail-row">
          <strong>Estado Actual:</strong>
          <p-tag 
            [value]="selectedSolicitud()?.estado_relation?.estado_nombre || 'Pendiente'" 
            [severity]="getStatusSeverity(selectedSolicitud()?.estado || 1)"
          ></p-tag>
        </div>
        <div class="detail-row">
          <strong>Solicitante:</strong>
          <span>{{ selectedSolicitud()?.solicitante_relation?.primer_nombre }} {{ selectedSolicitud()?.solicitante_relation?.primer_apellido }} (&#64;{{ selectedSolicitud()?.solicitante_relation?.nombre_usuario }})</span>
        </div>
        <div class="detail-row">
          <strong>Encargado Asignado:</strong>
          <span>{{ selectedSolicitud()?.encargado_relation?.primer_nombre || 'No asignado' }} {{ selectedSolicitud()?.encargado_relation?.primer_apellido || '' }}</span>
        </div>
        <div class="detail-row">
          <strong>Departamento Encargado:</strong>
          <span>{{ selectedSolicitud()?.departamento_encargado_relation?.rol_name || 'Sin asignar' }}</span>
        </div>
        <div class="detail-row full-width">
          <strong>Descripción:</strong>
          <p class="description-text">{{ selectedSolicitud()?.descripcion || 'Sin descripción' }}</p>
        </div>
        <div class="detail-row full-width" *ngIf="selectedSolicitud()?.s_doc">
          <strong>Documento Adjunto:</strong>
          <span><a [href]="selectedSolicitud()?.s_doc" target="_blank" class="doc-link"><i class="pi pi-file"></i> Ver Documentación</a></span>
        </div>
      </div>
      <ng-template pTemplate="footer">
        <button pButton icon="pi pi-times" label="Cerrar" class="p-button-text" (click)="displayDetails = false"></button>
      </ng-template>
    </p-dialog>
  `,
  styles: [`
    .solicitudes-container {
      display: flex;
      flex-direction: column;
      gap: 20px;
    }
    .header-section {
      display: flex;
      justify-content: space-between;
      align-items: center;
    }
    .page-title {
      font-size: 24px;
      font-weight: 700;
      color: #1e3c72;
      margin: 0;
    }
    .stats-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
      gap: 20px;
    }
    .stat-card {
      display: flex;
      align-items: center;
      gap: 15px;
      background: #fff;
      padding: 20px;
      border-radius: 8px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.05);
      border-left: 5px solid;
    }
    .stat-card.pending { border-left-color: #fbc02d; }
    .stat-card.approved { border-left-color: #4caf50; }
    .stat-card.denied { border-left-color: #f44336; }
    .stat-icon {
      font-size: 30px;
    }
    .stat-card.pending .stat-icon { color: #fbc02d; }
    .stat-card.approved .stat-icon { color: #4caf50; }
    .stat-card.denied .stat-icon { color: #f44336; }
    .stat-info {
      display: flex;
      flex-direction: column;
    }
    .stat-value {
      font-size: 24px;
      font-weight: 700;
    }
    .stat-label {
      font-size: 13px;
      color: #666;
      font-weight: 600;
    }
    .filter-bar {
      margin-top: 10px;
    }
    .table-card {
      background: #fff;
      padding: 15px;
      border-radius: 8px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    }
    .text-truncate {
      max-width: 200px;
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
    }
    .actions-wrapper {
      display: flex;
      gap: 5px;
    }
    .details-grid {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 15px;
      padding: 10px 0;
    }
    .detail-row {
      display: flex;
      flex-direction: column;
      gap: 5px;
      padding-bottom: 10px;
      border-bottom: 1px solid #eee;
    }
    .detail-row.full-width {
      grid-column: span 2;
    }
    .description-text {
      background: #f8f9fa;
      padding: 10px;
      border-radius: 6px;
      margin: 0;
      border: 1px solid #eee;
    }
    .doc-link {
      color: #1e3c72;
      text-decoration: none;
      font-weight: 600;
    }
    .doc-link:hover {
      text-decoration: underline;
    }
    .text-center {
      text-align: center;
    }
    .p-4 {
      padding: 1.5rem;
    }
  `]
})
export class SolicitudesListComponent implements OnInit {
  private solicitudService = inject(SolicitudService);
  private authService = inject(AuthService);

  solicitudes = signal<Solicitud[]>([]);
  loading = signal(false);
  activeTab = signal<'created' | 'assigned'>('created');
  displayDetails = false;
  selectedSolicitud = signal<Solicitud | null>(null);

  currentUser = computed(() => this.authService.currentUserSignal());

  pendingCount = computed(() => this.solicitudes().filter(s => s.estado === 1).length);
  approvedCount = computed(() => this.solicitudes().filter(s => s.estado === 2).length);
  deniedCount = computed(() => this.solicitudes().filter(s => s.estado === 3).length);

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
        error: () => this.loading.set(false)
      });
    } else {
      this.solicitudService.getAsignadas().subscribe({
        next: (data) => {
          this.solicitudes.set(data);
          this.loading.set(false);
        },
        error: () => this.loading.set(false)
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
    return user ? solicitud.encargado === user.id && solicitud.estado === 1 : false;
  }

  canDelete(solicitud: Solicitud): boolean {
    const user = this.currentUser();
    return user ? (solicitud.solicitante === user.id && solicitud.estado === 1) || this.authService.hasOption('REGISTRAR_USUARIOS') : false;
  }

  getStatusSeverity(estado: number): "warn" | "success" | "danger" | "info" {
    switch (estado) {
      case 1: return 'warn';     // Pendiente
      case 2: return 'success';  // Aprobada
      case 3: return 'danger';   // Denegada
      default: return 'info';
    }
  }

  showDetails(solicitud: Solicitud) {
    this.selectedSolicitud.set(solicitud);
    this.displayDetails = true;
  }

  updateStatus(solicitud: Solicitud, newStatus: number) {
    this.loading.set(true);
    this.solicitudService.updateSolicitud(solicitud.s_id, { estado: newStatus }).subscribe({
      next: () => {
        this.fetchData();
      },
      error: () => this.loading.set(false)
    });
  }

  deleteSolicitud(id: number) {
    if (confirm('¿Está seguro de eliminar esta solicitud?')) {
      this.loading.set(true);
      this.solicitudService.deleteSolicitud(id).subscribe({
        next: () => {
          this.fetchData();
        },
        error: () => this.loading.set(false)
      });
    }
  }
}
