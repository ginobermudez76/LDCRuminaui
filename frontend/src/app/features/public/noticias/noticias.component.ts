import { Component, OnInit, signal, inject } from '@angular/core';
import { CommonModule } from '@angular/common';
import { HttpClient } from '@angular/common/http';
import { NavbarComponent } from '../../../shared/components/navbar/navbar.component';
import { FooterComponent } from '../../../shared/components/footer/footer.component';

const API = 'http://localhost:8000/api';

@Component({
  selector: 'app-noticias',
  standalone: true,
  imports: [CommonModule, NavbarComponent, FooterComponent],
  template: `
    <app-navbar></app-navbar>
    <div class="noticias-page">
      <div class="page-hero">
        <div class="hero-title-row">
          <span class="material-symbols-outlined hero-icon">newspaper</span>
          <h1>Noticias</h1>
        </div>
        <div class="hero-line"></div>
      </div>
      <div class="content-wrap">
        <div class="grid-noticias" *ngIf="items().length > 0; else empty">
          <div class="noticia-card" *ngFor="let n of items()">
            <div class="card-img-wrap">
              <img *ngIf="n.imagen" [src]="'http://localhost:8000' + n.imagen" [alt]="n.titulo" />
              <div *ngIf="!n.imagen" class="card-placeholder">
                <span class="material-symbols-outlined">article</span>
              </div>
              <div class="card-overlay">
                <p class="overlay-title">{{ n.titulo }}</p>
                <a class="ver-mas-btn">
                  <span class="material-symbols-outlined">open_in_new</span>
                  Ver más
                </a>
              </div>
            </div>
            <div class="card-body">
              <h3>{{ n.titulo }}</h3>
            </div>
          </div>
        </div>
        <ng-template #empty><p class="empty-msg">No hay noticias para mostrar.</p></ng-template>
      </div>
    </div>
    <app-footer></app-footer>
  `,
  styles: [`
    .noticias-page { min-height: 100vh; background: linear-gradient(to bottom, #030022, #11637c); color: #fff; }
    .page-hero { padding: 48px 60px 32px; border-bottom: 2px solid rgba(15,195,198,0.3); }
    .hero-title-row { display: flex; align-items: center; gap: 16px; }
    .hero-icon { font-size: 48px; color: #0fc3c6; }
    h1 { font-family: 'Lobster', 'Pattaya', cursive; font-size: 48px; margin: 0; }
    .hero-line { width: 80px; height: 5px; background: #0fc3c6; margin-top: 12px; border-radius: 2px; }
    .content-wrap { padding: 48px 60px; max-width: 1200px; margin: 0 auto; }
    .grid-noticias { display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 24px; }
    .noticia-card { background: rgba(255,255,255,0.04); border: 1px solid rgba(15,195,198,0.2); border-radius: 12px; overflow: hidden; transition: all 0.3s; }
    .noticia-card:hover { transform: translateY(-4px); border-color: #0fc3c6; }
    .card-img-wrap { position: relative; height: 240px; overflow: hidden; }
    .card-img-wrap img { width: 100%; height: 100%; object-fit: cover; transition: transform 0.4s; }
    .noticia-card:hover .card-img-wrap img { transform: scale(1.05); }
    .card-placeholder { width: 100%; height: 100%; background: rgba(15,195,198,0.1); display: flex; align-items: center; justify-content: center; }
    .card-placeholder .material-symbols-outlined { font-size: 72px; color: #0fc3c6; }
    .card-overlay { position: absolute; inset: 0; background: rgba(3,0,34,0.8); display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 16px; opacity: 0; transition: 0.3s; padding: 20px; text-align: center; }
    .card-img-wrap:hover .card-overlay { opacity: 1; }
    .overlay-title { font-size: 15px; color: #fff; font-weight: 600; margin: 0; }
    .ver-mas-btn { display: inline-flex; align-items: center; gap: 6px; background: #0fc3c6; color: #030022; font-size: 13px; font-weight: 700; padding: 8px 16px; border-radius: 8px; text-decoration: none; cursor: pointer; }
    .ver-mas-btn .material-symbols-outlined { font-size: 16px; }
    .card-body { padding: 16px; }
    .card-body h3 { font-size: 15px; font-weight: 700; color: rgba(255,255,255,0.9); margin: 0; }
    .empty-msg { text-align: center; color: rgba(255,255,255,0.6); font-size: 16px; padding: 60px; }
  `]
})
export class NoticiasComponent implements OnInit {
  private http = inject(HttpClient);
  items = signal<any[]>([]);
  ngOnInit() {
    // Intentar cargar desde API si existen, si no mostrar vacío
    try {
      this.http.get<any[]>(`${API}/noticias`).subscribe({ next: d => this.items.set(d), error: () => {} });
    } catch {}
  }
}
