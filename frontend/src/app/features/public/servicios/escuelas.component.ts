import { Component, OnInit, signal, inject } from '@angular/core';
import { CommonModule } from '@angular/common';
import { HttpClient } from '@angular/common/http';
import { NavbarComponent } from '../../../shared/components/navbar/navbar.component';

const API = 'http://localhost:8000/api';

@Component({
  selector: 'app-servicios-escuelas',
  standalone: true,
  imports: [CommonModule, NavbarComponent],
  template: `
    <app-navbar></app-navbar>
    <div class="servicios-page">
      <div class="page-hero">
        <div class="hero-title-row">
          <span class="material-symbols-outlined hero-icon">school</span>
          <h1>Escuelas Permanentes</h1>
        </div>
        <div class="hero-line"></div>
      </div>
      <div class="content-wrap">
        <div class="grid-cards" *ngIf="items().length > 0; else empty">
          <div class="escuela-card" *ngFor="let d of items()">
            <div class="card-img-wrap">
              <img *ngIf="d.imagen" [src]="'http://localhost:8000' + d.imagen" [alt]="d.nombre" />
              <div *ngIf="!d.imagen" class="card-placeholder">
                <span class="material-symbols-outlined">sports</span>
              </div>
              <div class="card-overlay"><span>{{ d.nombre }}</span></div>
            </div>
            <div class="card-body">
              <h3>{{ d.nombre }}</h3>
              <p *ngIf="d.descripcion">{{ d.descripcion }}</p>
            </div>
          </div>
        </div>
        <ng-template #empty>
          <p class="empty-msg">No hay escuelas deportivas disponibles.</p>
        </ng-template>
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
    .content-wrap { padding: 48px 60px; max-width: 1200px; margin: 0 auto; }
    .grid-cards { display: grid; grid-template-columns: repeat(auto-fill, minmax(260px, 1fr)); gap: 24px; }
    .escuela-card { background: rgba(255,255,255,0.06); border: 1px solid rgba(15,195,198,0.2); border-radius: 12px; overflow: hidden; transition: all 0.3s; }
    .escuela-card:hover { transform: translateY(-6px); border-color: #0fc3c6; }
    .card-img-wrap { position: relative; height: 200px; }
    .card-img-wrap img { width: 100%; height: 100%; object-fit: cover; }
    .card-placeholder { width: 100%; height: 100%; background: rgba(15,195,198,0.1); display: flex; align-items: center; justify-content: center; }
    .card-placeholder .material-symbols-outlined { font-size: 64px; color: #0fc3c6; }
    .card-overlay { position: absolute; inset: 0; background: rgba(3,0,34,0.8); display: flex; align-items: center; justify-content: center; opacity: 0; transition: 0.3s; }
    .card-img-wrap:hover .card-overlay { opacity: 1; }
    .card-overlay span { color: #fff; font-size: 18px; font-weight: 700; text-transform: uppercase; }
    .card-body { padding: 16px; }
    .card-body h3 { font-size: 16px; font-weight: 700; color: #0fc3c6; margin: 0 0 8px; }
    .card-body p { font-size: 13px; color: rgba(255,255,255,0.7); margin: 0; }
    .empty-msg { text-align: center; color: rgba(255,255,255,0.6); font-size: 16px; padding: 60px; }
  `]
})
export class ServiciosEscuelasComponent implements OnInit {
  private http = inject(HttpClient);
  items = signal<any[]>([]);
  ngOnInit() { this.http.get<any[]>(`${API}/deportes`).subscribe(d => this.items.set(d)); }
}
