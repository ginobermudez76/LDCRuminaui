import { Component, OnInit, signal, inject } from '@angular/core';
import { CommonModule } from '@angular/common';
import { ActivatedRoute } from '@angular/router';
import { NavbarComponent } from '../../../shared/components/navbar/navbar.component';
import { FooterComponent } from '../../../shared/components/footer/footer.component';

@Component({
  selector: 'app-nosotros',
  standalone: true,
  imports: [CommonModule, NavbarComponent, FooterComponent],
  template: `
    <app-navbar></app-navbar>
    <div class="nosotros-page">
      <!-- Hero sub-header -->
      <div class="page-hero">
        <div class="hero-title-row">
          <span class="material-symbols-outlined hero-icon">
            {{ icons[tipo()] || 'info' }}
          </span>
          <h1>{{ labels[tipo()] || 'Nosotros' }}</h1>
        </div>
        <div class="hero-line"></div>
      </div>

      <!-- Content -->
      <div class="content-wrap">

        <div *ngIf="tipo() === 'historia'" class="section-content">
          <div class="text-card">
            <h2>Historia</h2>
            <p>El 6 de julio de <strong>1940</strong>, con la presencia de los delegados de los clubes filiales fundadores: Welcome, Colombia, Círculo Deportivo Sangolquí, Pichincha, Flecha Roja, Chacarita, Juan de Salinas y Municipal, decidieron constituirse en asamblea general y procedieron a crear la <strong>Concentración Deportiva Cantonal de Rumiñahui</strong>.</p>
            <div class="directivo-list">
              <div class="directivo-card">
                <span class="material-symbols-outlined">person</span>
                <div>
                  <strong>Presidente</strong>
                  <p>Sr. Ernesto Recalde</p>
                </div>
              </div>
              <div class="directivo-card">
                <span class="material-symbols-outlined">person</span>
                <div>
                  <strong>Vicepresidente</strong>
                  <p>Sr. Humberto Tinta</p>
                </div>
              </div>
              <div class="directivo-card">
                <span class="material-symbols-outlined">person</span>
                <div>
                  <strong>Secretario</strong>
                  <p>Sr. Isaías Figueroa</p>
                </div>
              </div>
              <div class="directivo-card">
                <span class="material-symbols-outlined">person</span>
                <div>
                  <strong>Tesorero</strong>
                  <p>Sr. Efraín Carrera</p>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div *ngIf="tipo() === 'mision'" class="section-content">
          <div class="text-card mission-vision">
            <span class="material-symbols-outlined big-icon">flag</span>
            <h2>Misión</h2>
            <p>Somos la institución rectora, que <strong>lidera, administra, fomenta y desarrolla</strong> el deporte formativo en el cantón Rumiñahui. Dentro de un ambiente que promueve los valores éticos, morales y el mejoramiento continuo.</p>
            <p>Mejorando la calidad de vida de la comunidad con <strong>inclusión social</strong>.</p>
          </div>
        </div>

        <div *ngIf="tipo() === 'vision'" class="section-content">
          <div class="text-card mission-vision">
            <span class="material-symbols-outlined big-icon">visibility</span>
            <h2>Visión</h2>
            <p>Ser la <strong>primera potencia deportiva</strong> de la provincia, cuya prioridad es promover deportistas de alta calidad a las selecciones provinciales y nacionales por deportes, a través de la preparación y formación integral de las/los atletas.</p>
          </div>
        </div>

        <div *ngIf="tipo() === 'directorio'" class="section-content">
          <div class="text-card">
            <span class="material-symbols-outlined big-icon">groups</span>
            <h2>Directorio</h2>
            <div class="directivo-list">
              <div class="directivo-card">
                <span class="material-symbols-outlined">person</span>
                <div><strong>Presidente</strong><p>—</p></div>
              </div>
              <div class="directivo-card">
                <span class="material-symbols-outlined">person</span>
                <div><strong>Vicepresidente</strong><p>—</p></div>
              </div>
              <div class="directivo-card">
                <span class="material-symbols-outlined">person</span>
                <div><strong>Secretario</strong><p>—</p></div>
              </div>
              <div class="directivo-card">
                <span class="material-symbols-outlined">person</span>
                <div><strong>Tesorero</strong><p>—</p></div>
              </div>
            </div>
          </div>
        </div>

        <!-- Default -->
        <div *ngIf="!tipo()" class="section-content">
          <div class="tabs-row">
            <a class="tab-card" href="/nosotros?tipo=historia">
              <span class="material-symbols-outlined">history_edu</span>
              <span>Historia</span>
            </a>
            <a class="tab-card" href="/nosotros?tipo=mision">
              <span class="material-symbols-outlined">flag</span>
              <span>Misión</span>
            </a>
            <a class="tab-card" href="/nosotros?tipo=vision">
              <span class="material-symbols-outlined">visibility</span>
              <span>Visión</span>
            </a>
            <a class="tab-card" href="/nosotros?tipo=directorio">
              <span class="material-symbols-outlined">groups</span>
              <span>Directorio</span>
            </a>
          </div>
        </div>

      </div>
    </div>
    <app-footer></app-footer>
  `,
  styles: [`
    .nosotros-page {
      min-height: 100vh;
      background: linear-gradient(to bottom, #030022, #11637c);
      color: #fff;
    }
    .page-hero {
      padding: 48px 60px 32px;
      border-bottom: 2px solid rgba(15,195,198,0.3);
    }
    .hero-title-row { display: flex; align-items: center; gap: 16px; }
    .hero-icon { font-size: 48px; color: #0fc3c6; }
    h1 { font-family: 'Lobster', 'Pattaya', cursive; font-size: 48px; margin: 0; text-shadow: 2px 2px 6px rgba(0,0,0,0.4); }
    .hero-line { width: 80px; height: 5px; background: #0fc3c6; margin-top: 12px; border-radius: 2px; }
    .content-wrap { padding: 48px 60px; max-width: 900px; margin: 0 auto; }
    .section-content { animation: fadeIn 0.4s ease; }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
    .text-card {
      background: rgba(255,255,255,0.05); border: 1px solid rgba(15,195,198,0.2);
      border-left: 4px solid #0fc3c6; border-radius: 12px; padding: 36px 40px;
    }
    .text-card h2 { font-family: 'Bebas Neue', cursive; font-size: 32px; color: #0fc3c6; margin: 0 0 20px; }
    .text-card p { font-size: 17px; line-height: 1.8; color: rgba(255,255,255,0.85); margin-bottom: 14px; }
    .mission-vision { text-align: center; }
    .big-icon { font-size: 72px; color: #0fc3c6; display: block; margin-bottom: 16px; }
    .directivo-list { display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 16px; margin-top: 24px; }
    .directivo-card {
      display: flex; align-items: center; gap: 12px;
      background: rgba(15,195,198,0.1); border: 1px solid rgba(15,195,198,0.2);
      border-radius: 8px; padding: 14px;
    }
    .directivo-card .material-symbols-outlined { font-size: 32px; color: #0fc3c6; flex-shrink: 0; }
    .directivo-card strong { font-size: 13px; text-transform: uppercase; letter-spacing: 0.5px; color: #0fc3c6; display: block; }
    .directivo-card p { font-size: 15px; margin: 4px 0 0; color: #fff; }
    .tabs-row { display: grid; grid-template-columns: repeat(auto-fill, minmax(180px, 1fr)); gap: 20px; }
    .tab-card {
      display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 12px;
      background: rgba(255,255,255,0.06); border: 1px solid rgba(15,195,198,0.3);
      border-radius: 12px; padding: 32px 20px; text-decoration: none; color: #fff;
      transition: all 0.2s;
    }
    .tab-card .material-symbols-outlined { font-size: 48px; color: #0fc3c6; }
    .tab-card:hover { background: rgba(15,195,198,0.15); transform: translateY(-4px); }
  `]
})
export class NosotrosComponent implements OnInit {
  private route = inject(ActivatedRoute);
  tipo = signal('');
  labels: Record<string, string> = { historia: 'Historia', mision: 'Misión', vision: 'Visión', directorio: 'Directorio' };
  icons: Record<string, string> = { historia: 'history_edu', mision: 'flag', vision: 'visibility', directorio: 'groups' };

  ngOnInit() {
    this.route.queryParams.subscribe(p => this.tipo.set(p['tipo'] ?? ''));
  }
}
