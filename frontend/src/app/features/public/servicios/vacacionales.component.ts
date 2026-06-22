import { Component, OnInit, signal, inject } from '@angular/core';
import { CommonModule } from '@angular/common';
import { HttpClient } from '@angular/common/http';
import { NavbarComponent } from '../../../shared/components/navbar/navbar.component';
import { FooterComponent } from '../../../shared/components/footer/footer.component';

const API = 'http://localhost:8000/api';

@Component({
  selector: 'app-vacacionales',
  standalone: true,
  imports: [CommonModule, NavbarComponent, FooterComponent],
  template: `
    <app-navbar></app-navbar>
    <div class="servicios-page">
      <div class="page-hero">
        <div class="hero-title-row">
          <span class="material-symbols-outlined hero-icon">calendar_month</span>
          <h1>Cursos Vacacionales</h1>
        </div>
        <div class="hero-line"></div>
      </div>
      <div class="content-wrap">
        <div class="grid-cards" *ngIf="items().length > 0; else empty">
          <div class="curso-card" *ngFor="let c of items()" [class]="getEstadoClass(c.estado)">
            <div class="card-img-wrap">
              <img *ngIf="c.imagen" [src]="'http://localhost:8000' + c.imagen" [alt]="c.nombre" />
              <div *ngIf="!c.imagen" class="card-placeholder">
                <span class="material-symbols-outlined">sports_tennis</span>
              </div>
              <span class="estado-badge">{{ c.estado }}</span>
            </div>
            <div class="card-body">
              <h3>{{ c.nombre }}</h3>
              <p *ngIf="c.descripcion">{{ c.descripcion }}</p>
              <div class="card-meta">
                <span class="meta-item" *ngIf="c.fecha_inicio">
                  <span class="material-symbols-outlined">event</span>
                  {{ c.fecha_inicio }}
                </span>
                <span class="meta-item" *ngIf="c.fecha_fin">
                  <span class="material-symbols-outlined">event</span>
                  {{ c.fecha_fin }}
                </span>
                <span class="inscripcion-badge" *ngIf="c.inscripciones" [class]="getInscripcionClass(c.inscripciones)">
                  <span class="material-symbols-outlined">how_to_reg</span>
                  {{ c.inscripciones }}
                </span>
              </div>
            </div>
          </div>
        </div>
        <ng-template #empty><p class="empty-msg">No hay cursos vacacionales disponibles.</p></ng-template>
      </div>
    </div>
    <app-footer></app-footer>
  `,
  styles: [`
    .servicios-page { min-height: 100vh; background: linear-gradient(to bottom, #030022, #11637c); color: #fff; }
    .page-hero { padding: 48px 60px 32px; border-bottom: 2px solid rgba(15,195,198,0.3); }
    .hero-title-row { display: flex; align-items: center; gap: 16px; }
    .hero-icon { font-size: 48px; color: #0fc3c6; }
    h1 { font-family: 'Lobster', 'Pattaya', cursive; font-size: 48px; margin: 0; }
    .hero-line { width: 80px; height: 5px; background: #0fc3c6; margin-top: 12px; border-radius: 2px; }
    .content-wrap { padding: 48px 60px; max-width: 1200px; margin: 0 auto; }
    .grid-cards { display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 24px; }
    .curso-card { background: rgba(255,255,255,0.06); border: 1px solid rgba(15,195,198,0.2); border-radius: 12px; overflow: hidden; transition: all 0.3s; }
    .curso-card:hover { transform: translateY(-4px); }
    .card-img-wrap { position: relative; height: 180px; }
    .card-img-wrap img { width: 100%; height: 100%; object-fit: cover; }
    .card-placeholder { width: 100%; height: 100%; background: rgba(15,195,198,0.1); display: flex; align-items: center; justify-content: center; }
    .card-placeholder .material-symbols-outlined { font-size: 64px; color: #0fc3c6; }
    .estado-badge { position: absolute; top: 10px; right: 10px; font-size: 11px; font-weight: 700; padding: 3px 10px; border-radius: 99px; background: rgba(0,0,0,0.6); color: #fff; }
    .card-body { padding: 16px; }
    .card-body h3 { font-size: 16px; font-weight: 700; color: #0fc3c6; margin: 0 0 8px; }
    .card-body p { font-size: 13px; color: rgba(255,255,255,0.7); margin: 0 0 12px; }
    .card-meta { display: flex; flex-wrap: wrap; gap: 8px; align-items: center; }
    .meta-item { display: flex; align-items: center; gap: 4px; font-size: 12px; color: rgba(255,255,255,0.6); }
    .meta-item .material-symbols-outlined { font-size: 14px; }
    .inscripcion-badge { display: flex; align-items: center; gap: 4px; font-size: 11px; font-weight: 700; padding: 3px 10px; border-radius: 99px; }
    .inscripcion-badge .material-symbols-outlined { font-size: 14px; }
    .abiertas { background: rgba(39,174,96,0.2); color: #2ecc71; }
    .cerradas { background: rgba(231,76,60,0.2); color: #e74c3c; }
    .en-curso { background: rgba(243,156,18,0.2); border-color: rgba(243,156,18,0.3); }
    .finalizado { background: rgba(127,140,141,0.15); border-color: rgba(127,140,141,0.2); }
    .proximamente { background: rgba(15,195,198,0.1); border-color: rgba(15,195,198,0.3); }
    .empty-msg { text-align: center; color: rgba(255,255,255,0.6); font-size: 16px; padding: 60px; }
  `]
})
export class VacacionalesComponent implements OnInit {
  private http = inject(HttpClient);
  items = signal<any[]>([]);
  ngOnInit() { this.http.get<any[]>(`${API}/cursos`).subscribe(d => this.items.set(d)); }
  getEstadoClass(e: string) { return e?.toLowerCase().replace(' ', '-') ?? ''; }
  getInscripcionClass(i: string) { return i === 'Abiertas' ? 'inscripcion-badge abiertas' : 'inscripcion-badge cerradas'; }
}
