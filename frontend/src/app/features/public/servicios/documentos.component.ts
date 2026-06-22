import { Component, OnInit, signal, inject } from '@angular/core';
import { CommonModule } from '@angular/common';
import { HttpClient } from '@angular/common/http';
import { NavbarComponent } from '../../../shared/components/navbar/navbar.component';

const API = 'http://localhost:8000/api';

@Component({
  selector: 'app-documentos-publicos',
  standalone: true,
  imports: [CommonModule, NavbarComponent],
  template: `
    <app-navbar></app-navbar>
    <div class="servicios-page">
      <div class="page-hero">
        <div class="hero-title-row">
          <span class="material-symbols-outlined hero-icon">folder_open</span>
          <h1>Descarga de Documentos</h1>
        </div>
        <div class="hero-line"></div>
      </div>
      <div class="content-wrap">
        <div class="grid-docs" *ngIf="items().length > 0; else empty">
          <div class="doc-card" *ngFor="let d of items()">
            <div class="doc-icon-wrap">
              <span class="material-symbols-outlined">description</span>
            </div>
            <div class="doc-info">
              <h3>{{ d.nombre }}</h3>
              <p *ngIf="d.descripcion">{{ d.descripcion }}</p>
              <a *ngIf="d.documento" [href]="'http://localhost:8000' + d.documento" target="_blank" class="download-btn">
                <span class="material-symbols-outlined">download</span>
                Descargar
              </a>
            </div>
          </div>
        </div>
        <ng-template #empty><p class="empty-msg">No hay documentos disponibles.</p></ng-template>
      </div>
    </div>
  `,
  styles: [`
    .servicios-page { min-height: 100vh; background: linear-gradient(to bottom, #030022, #11637c); color: #fff; }
    .page-hero { padding: 48px 60px 32px; border-bottom: 2px solid rgba(15,195,198,0.3); }
    .hero-title-row { display: flex; align-items: center; gap: 16px; }
    .hero-icon { font-size: 48px; color: #0fc3c6; }
    h1 { font-family: 'Lobster', 'Pattaya', cursive; font-size: 48px; margin: 0; }
    .hero-line { width: 80px; height: 5px; background: #0fc3c6; margin-top: 12px; border-radius: 2px; }
    .content-wrap { padding: 48px 60px; max-width: 900px; margin: 0 auto; }
    .grid-docs { display: flex; flex-direction: column; gap: 16px; }
    .doc-card { display: flex; align-items: center; gap: 20px; background: rgba(255,255,255,0.06); border: 1px solid rgba(15,195,198,0.2); border-radius: 12px; padding: 20px 24px; transition: all 0.2s; }
    .doc-card:hover { border-color: #0fc3c6; background: rgba(15,195,198,0.08); }
    .doc-icon-wrap { width: 56px; height: 56px; background: rgba(15,195,198,0.15); border-radius: 12px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
    .doc-icon-wrap .material-symbols-outlined { font-size: 28px; color: #0fc3c6; }
    .doc-info { flex: 1; }
    .doc-info h3 { font-size: 16px; font-weight: 700; color: #fff; margin: 0 0 6px; }
    .doc-info p { font-size: 13px; color: rgba(255,255,255,0.6); margin: 0 0 12px; }
    .download-btn { display: inline-flex; align-items: center; gap: 6px; background: #0fc3c6; color: #030022; font-size: 13px; font-weight: 700; padding: 8px 16px; border-radius: 8px; text-decoration: none; transition: all 0.2s; }
    .download-btn .material-symbols-outlined { font-size: 16px; }
    .download-btn:hover { background: #0aa8ab; }
    .empty-msg { text-align: center; color: rgba(255,255,255,0.6); font-size: 16px; padding: 60px; }
  `]
})
export class DocumentosPublicosComponent implements OnInit {
  private http = inject(HttpClient);
  items = signal<any[]>([]);
  ngOnInit() { this.http.get<any[]>(`${API}/documentos`).subscribe(d => this.items.set(d)); }
}
