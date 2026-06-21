import { Component, OnInit, signal, inject, OnDestroy } from '@angular/core';
import { CommonModule } from '@angular/common';
import { Router, RouterLink } from '@angular/router';
import { PublicistaService, Logro, DeportistaDestacado, Deporte, Evento, CartaCondolencia } from '../../core/services/publicista.service';

interface Noticia {
  id: number;
  titulo: string;
  imagen?: string;
  cuerpo?: string;
}

@Component({
  selector: 'app-landing',
  standalone: true,
  imports: [CommonModule, RouterLink],
  template: `
    <!-- CONDOLENCIAS MODAL -->
    <div class="modal-overlay" *ngIf="showCondolencias()" (click)="closeCondolencias()">
      <div class="modal-box" (click)="$event.stopPropagation()">
        <button class="modal-close" (click)="closeCondolencias()">✕</button>
        <div class="condolencia-slide" *ngIf="cartas().length > 0">
          <div class="condolencia-img-wrap">
            <p class="condolencia-title">EN MEMORIA DE NUESTRO CAMARADA</p>
            <img *ngIf="cartas()[cartaIdx()].imagen" [src]="'http://localhost:8000' + cartas()[cartaIdx()].imagen" alt="Condolencia" />
          </div>
          <div class="condolencia-msg">
            <p>{{ cartas()[cartaIdx()].mensaje }}</p>
          </div>
        </div>
        <div class="carousel-controls" *ngIf="cartas().length > 1">
          <button (click)="prevCarta()">‹</button>
          <span>{{ cartaIdx() + 1 }} / {{ cartas().length }}</span>
          <button (click)="nextCarta()">›</button>
        </div>
      </div>
    </div>

    <!-- NAVBAR -->
    <nav class="landing-nav">
      <div class="nav-brand">
        <span class="brand-shield">⚔</span>
        <span class="brand-text">Liga Cantonal Rumiñahui</span>
      </div>
      <div class="nav-links">
        <a href="#inicio" class="nav-link">Inicio</a>
        <a href="#logros" class="nav-link">Logros</a>
        <a href="#deportistas" class="nav-link">Deportistas</a>
        <a href="#escuelas" class="nav-link">Escuelas</a>
        <a href="#eventos" class="nav-link">Eventos</a>
        <a [routerLink]="['/login']" class="nav-btn">Ingresar</a>
      </div>
    </nav>

    <!-- HERO SECTION -->
    <section id="inicio" class="hero-section">
      <div class="hero-content">
        <span class="hero-tagline">Más que una liga deportiva</span>
        <h1 class="hero-title">
          Liga Deportiva<br/>
          <span class="hero-title-highlight">Cantonal Rumiñahui</span>
        </h1>
        <p class="hero-subtitle">Liderando, administrando y fomentando el deporte formativo en el cantón Rumiñahui desde 1940.</p>
        <div class="hero-btns">
          <a href="#escuelas" class="btn-hero-primary">Ver Escuelas</a>
          <a href="#eventos" class="btn-hero-secondary">Próximos Eventos</a>
        </div>
      </div>
      <div class="hero-badges">
        <div class="badge-card">
          <span class="badge-num">{{ deportes().length }}</span>
          <span class="badge-label">Deportes</span>
        </div>
        <div class="badge-card">
          <span class="badge-num">{{ logros().length }}</span>
          <span class="badge-label">Logros</span>
        </div>
        <div class="badge-card">
          <span class="badge-num">{{ deportistas().length }}</span>
          <span class="badge-label">Destacados</span>
        </div>
      </div>
    </section>

    <!-- LOGROS SECTION -->
    <section id="logros" class="section-container">
      <div class="section-header">
        <h2 class="section-title gold">Logros</h2>
        <div class="section-line"></div>
      </div>
      <div class="logros-grid" *ngIf="logros().length > 0; else noLogros">
        <div class="logro-card-legacy" *ngFor="let logro of logros()">
          <div class="logro-bg" *ngIf="logro.imagen" [style.backgroundImage]="'url(http://localhost:8000' + logro.imagen + ')'"></div>
          <div class="logro-content">
            <div class="logro-text">
              <h3>{{ logro.titulo }}</h3>
              <span class="logro-deporte" *ngIf="logro.deporte">{{ logro.deporte.nombre }}</span>
            </div>
            <div class="logro-icon">
              <span *ngIf="logro.tipologro === 'Copa'" class="material-symbols-outlined" style="font-size: 48px; color: rgba(255,255,255,0.7);">trophy</span>
              <span *ngIf="logro.tipologro === 'Medalla'" class="material-symbols-outlined" style="font-size: 48px; color: rgba(255,255,255,0.7);">social_leaderboard</span>
              <span *ngIf="logro.tipologro === 'Reconocimiento'" class="material-symbols-outlined" style="font-size: 48px; color: rgba(255,255,255,0.7);">history_edu</span>
              <i *ngIf="!logro.tipologro" class="pi pi-star"></i>
            </div>
          </div>
        </div>
      </div>
      <ng-template #noLogros><p class="empty-section">No hay logros para mostrar.</p></ng-template>
    </section>

    <!-- DEPORTISTAS DESTACADOS -->
    <section id="deportistas" class="section-dark">
      <div class="section-header">
        <h2 class="section-title white">Deportistas Destacados</h2>
        <div class="section-line white"></div>
      </div>
      <div class="deportistas-split" *ngIf="deportistas().length > 0; else noDeportistas">
        
        <!-- Left Carousel (Odds) -->
        <div class="dep-carousel">
          <div class="dep-slide" *ngIf="deportistasIzq().length > 0">
            <button class="dep-nav left" (click)="prevIzq()">‹</button>
            <div class="deportista-img-wrap">
              <img *ngIf="deportistasIzq()[idxIzq()].imagen" [src]="'http://localhost:8000' + deportistasIzq()[idxIzq()].imagen" [alt]="deportistasIzq()[idxIzq()].nombre_deportista" />
              <div *ngIf="!deportistasIzq()[idxIzq()].imagen" class="deportista-placeholder">🌟</div>
              <div class="deportista-overlay">
                <div class="deportista-name">{{ deportistasIzq()[idxIzq()].nombre_deportista }}</div>
                <div class="deportista-sport" *ngIf="deportistasIzq()[idxIzq()].deporte">{{ deportistasIzq()[idxIzq()].deporte?.nombre }}</div>
              </div>
            </div>
            <button class="dep-nav right" (click)="nextIzq()">›</button>
          </div>
        </div>

        <!-- Center Logo -->
        <div class="dep-logo">
          <img src="assets/images/logoX_LDCR.png" alt="LDCR Logo" onerror="this.src='http://localhost:8080/img/logoX_LDCR.png'" />
        </div>

        <!-- Right Carousel (Evens) -->
        <div class="dep-carousel">
          <div class="dep-slide" *ngIf="deportistasDer().length > 0">
            <button class="dep-nav left" (click)="prevDer()">‹</button>
            <div class="deportista-img-wrap">
              <img *ngIf="deportistasDer()[idxDer()].imagen" [src]="'http://localhost:8000' + deportistasDer()[idxDer()].imagen" [alt]="deportistasDer()[idxDer()].nombre_deportista" />
              <div *ngIf="!deportistasDer()[idxDer()].imagen" class="deportista-placeholder">🌟</div>
              <div class="deportista-overlay">
                <div class="deportista-name">{{ deportistasDer()[idxDer()].nombre_deportista }}</div>
                <div class="deportista-sport" *ngIf="deportistasDer()[idxDer()].deporte">{{ deportistasDer()[idxDer()].deporte?.nombre }}</div>
              </div>
            </div>
            <button class="dep-nav right" (click)="nextDer()">›</button>
          </div>
        </div>

      </div>
      <ng-template #noDeportistas><p class="empty-section white">No hay deportistas para mostrar.</p></ng-template>
    </section>

    <!-- ESCUELAS DEPORTIVAS -->
    <section id="escuelas" class="section-gradient">
      <div class="section-header">
        <h2 class="section-title white">Escuelas Deportivas</h2>
        <div class="section-line white"></div>
      </div>
      <div class="escuelas-carousel" *ngIf="deportes().length > 0; else noEscuelas">
        <button class="car-btn-out left" (click)="prevEscuela()">‹</button>
        <div class="escuelas-track">
          <div class="escuela-card" *ngFor="let d of visibleEscuelas()">
            <div class="escuela-img-wrap">
              <img *ngIf="d.imagen" [src]="'http://localhost:8000' + d.imagen" [alt]="d.nombre" />
              <div *ngIf="!d.imagen" class="escuela-placeholder">⚽</div>
              <div class="escuela-overlay">
                <span>{{ d.nombre }}</span>
              </div>
            </div>
          </div>
        </div>
        <button class="car-btn-out right" (click)="nextEscuela()">›</button>
      </div>
      <ng-template #noEscuelas><p class="empty-section white">No hay escuelas deportivas para mostrar.</p></ng-template>
    </section>

    <!-- EVENTOS -->
    <section id="eventos" class="section-gradient">
      <div class="section-header">
        <h2 class="section-title white">Próximos Eventos</h2>
        <div class="section-line white"></div>
      </div>
      <div class="eventos-grid" *ngIf="eventos().length > 0; else noEventos">
        <div class="evento-card" *ngFor="let e of eventos()">
          <div class="evento-img-wrap">
            <img *ngIf="e.imagen" [src]="'http://localhost:8000' + e.imagen" [alt]="e.nombre" />
            <div *ngIf="!e.imagen" class="evento-placeholder">📅</div>
          </div>
          <div class="evento-info">
            <h3>{{ e.nombre }}</h3>
            <p class="evento-deporte" *ngIf="e.deporte">{{ e.deporte.nombre }}</p>
            <div class="evento-fechas">
              <span>{{ e.fecha_inicio }} → {{ e.fecha_fin }}</span>
            </div>
            <span class="evento-estado" [class.activo]="e.estado === 'Activo'">{{ e.estado }}</span>
          </div>
        </div>
      </div>
      <ng-template #noEventos><p class="empty-section white">No hay eventos para mostrar.</p></ng-template>
    </section>

    <!-- FOOTER -->
    <footer class="landing-footer">
      <div class="footer-content">
        <div class="footer-brand">
          <span class="brand-shield">⚔</span>
          <span>Liga Cantonal Rumiñahui</span>
        </div>
        <div class="footer-links">
          <div class="footer-col">
            <h4>Nosotros</h4>
            <a href="#">Historia</a>
            <a href="#">Misión y Visión</a>
            <a href="#">Directorio</a>
          </div>
          <div class="footer-col">
            <h4>Sistema</h4>
            <a [routerLink]="['/login']">Iniciar Sesión</a>
          </div>
        </div>
      </div>
      <div class="footer-bottom">
        <p>© 2024 Liga Cantonal Rumiñahui · Sangolquí, Ecuador</p>
      </div>
    </footer>
  `,
  styles: [`
    /* ─── Reset ─── */
    :host { display: block; font-family: 'Inter', 'Segoe UI', sans-serif; }
    * { box-sizing: border-box; }
    a { text-decoration: none; }

    /* ─── CONDOLENCIAS MODAL ─── */
    .modal-overlay { position: fixed; inset: 0; background: rgba(0,0,0,0.75); z-index: 9999; display: flex; align-items: center; justify-content: center; }
    .modal-box { background: #fff; border-radius: 16px; width: 90%; max-width: 680px; padding: 32px; position: relative; }
    .modal-close { position: absolute; top: 16px; right: 16px; background: none; border: none; font-size: 22px; cursor: pointer; color: #475569; }
    .condolencia-slide { display: flex; gap: 24px; }
    .condolencia-img-wrap { flex: 1; text-align: center; }
    .condolencia-title { font-size: 12px; font-weight: 700; letter-spacing: 1px; color: #475569; margin-bottom: 12px; }
    .condolencia-img-wrap img { width: 100%; max-height: 280px; object-fit: cover; border-radius: 8px; }
    .condolencia-msg { flex: 1; display: flex; align-items: center; }
    .condolencia-msg p { font-size: 15px; line-height: 1.7; color: #334155; font-style: italic; }
    .carousel-controls { display: flex; align-items: center; justify-content: center; gap: 20px; margin-top: 20px; }
    .carousel-controls button { background: #1e3c72; color: white; border: none; border-radius: 50%; width: 36px; height: 36px; font-size: 20px; cursor: pointer; }

    /* ─── NAVBAR ─── */
    .landing-nav { position: sticky; top: 0; z-index: 100; background: rgba(30,60,114,0.97); backdrop-filter: blur(8px); display: flex; justify-content: space-between; align-items: center; padding: 0 40px; height: 68px; }
    .nav-brand { display: flex; align-items: center; gap: 10px; color: #fff; font-size: 16px; font-weight: 700; }
    .brand-shield { font-size: 22px; color: #ffd700; }
    .nav-links { display: flex; align-items: center; gap: 24px; }
    .nav-link { color: rgba(255,255,255,0.85); font-size: 14px; font-weight: 500; transition: color 0.2s; }
    .nav-link:hover { color: #ffd700; }
    .nav-btn { background: #ffd700; color: #1e3c72; padding: 8px 20px; border-radius: 8px; font-weight: 700; font-size: 14px; transition: all 0.2s; }
    .nav-btn:hover { background: #fff; transform: translateY(-1px); }

    /* ─── HERO ─── */
    .hero-section { min-height: calc(100vh - 68px); background: linear-gradient(135deg, #0f1f5e 0%, #1e3c72 50%, #2a5298 100%); display: flex; flex-direction: column; align-items: center; justify-content: center; text-align: center; padding: 60px 40px; position: relative; overflow: hidden; }
    .hero-section::before { content: ''; position: absolute; inset: 0; background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.03'%3E%3Ccircle cx='30' cy='30' r='4'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E"); }
    .hero-content { position: relative; z-index: 1; }
    .hero-tagline { display: inline-block; background: rgba(255,215,0,0.15); color: #ffd700; border: 1px solid rgba(255,215,0,0.3); padding: 6px 16px; border-radius: 20px; font-size: 13px; font-weight: 600; letter-spacing: 1px; margin-bottom: 20px; }
    .hero-title { font-size: clamp(36px, 6vw, 72px); font-weight: 800; color: #fff; margin: 0 0 16px 0; line-height: 1.1; }
    .hero-title-highlight { color: #ffd700; }
    .hero-subtitle { font-size: 16px; color: rgba(255,255,255,0.75); max-width: 540px; margin: 0 auto 36px; line-height: 1.7; }
    .hero-btns { display: flex; gap: 16px; justify-content: center; flex-wrap: wrap; }
    .btn-hero-primary { background: #ffd700; color: #1e3c72; padding: 14px 32px; border-radius: 10px; font-weight: 700; font-size: 15px; transition: all 0.2s; }
    .btn-hero-primary:hover { transform: translateY(-2px); box-shadow: 0 8px 25px rgba(255,215,0,0.4); }
    .btn-hero-secondary { background: rgba(255,255,255,0.1); color: #fff; border: 1px solid rgba(255,255,255,0.3); padding: 14px 32px; border-radius: 10px; font-weight: 600; font-size: 15px; transition: all 0.2s; }
    .btn-hero-secondary:hover { background: rgba(255,255,255,0.2); }
    .hero-badges { display: flex; gap: 24px; margin-top: 60px; flex-wrap: wrap; justify-content: center; position: relative; z-index: 1; }
    .badge-card { background: rgba(255,255,255,0.08); border: 1px solid rgba(255,255,255,0.15); backdrop-filter: blur(8px); padding: 20px 32px; border-radius: 12px; text-align: center; }
    .badge-num { display: block; font-size: 36px; font-weight: 800; color: #ffd700; }
    .badge-label { font-size: 13px; color: rgba(255,255,255,0.7); font-weight: 600; text-transform: uppercase; letter-spacing: 1px; }

    /* ─── SECTION COMMONS ─── */
    .section-container { padding: 80px 60px; background: #f8fafc; }
    .section-dark { padding: 80px 60px; background: #1e293b; }
    .section-gradient { padding: 80px 60px; background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%); }
    .section-header { text-align: center; margin-bottom: 48px; }
    .section-title { font-size: 36px; font-weight: 800; margin: 0 0 12px 0; }
    .section-title.gold { color: #b45309; }
    .section-title.blue { color: #1e3c72; }
    .section-title.white { color: #fff; }
    .section-line { height: 4px; width: 60px; background: #ffd700; margin: 0 auto; border-radius: 2px; }
    .section-line.white { background: rgba(255,255,255,0.4); }
    .section-line.blue { background: #1e3c72; }
    .empty-section { text-align: center; color: #94a3b8; font-size: 16px; }
    .empty-section.white { color: rgba(255,255,255,0.6); }

    /* ─── LOGROS ─── */
    .logros-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 24px; }
    .logro-card-legacy { position: relative; background: #3b4252; border-radius: 4px; overflow: hidden; box-shadow: 0 4px 15px rgba(0,0,0,0.2); transition: all 0.3s; height: 100px; display: flex; cursor: pointer; border-left: 4px solid #38bdf8; }
    .logro-card-legacy:hover { transform: translateY(-4px); box-shadow: 0 12px 30px rgba(0,0,0,0.3); }
    .logro-bg { position: absolute; inset: 0; background-size: cover; background-position: center; opacity: 0; transition: opacity 0.3s; z-index: 1; }
    .logro-card-legacy:hover .logro-bg { opacity: 0.4; }
    .logro-content { position: relative; z-index: 2; display: flex; justify-content: space-between; align-items: center; width: 100%; padding: 0 24px; }
    .logro-text { display: flex; flex-direction: column; gap: 4px; }
    .logro-text h3 { margin: 0; font-size: 18px; color: #fff; font-weight: 700; }
    .logro-deporte { font-size: 13px; color: #e2e8f0; }
    .logro-icon { font-size: 48px; color: rgba(255,255,255,0.7); transition: opacity 0.3s; }
    .logro-card-legacy:hover .logro-icon { opacity: 0; }

    /* ─── DEPORTISTAS ─── */
    .deportistas-split { display: flex; align-items: center; justify-content: center; gap: 60px; flex-wrap: wrap; }
    .dep-carousel { position: relative; width: 300px; height: 350px; }
    .dep-slide { position: relative; width: 100%; height: 100%; }
    .deportista-img-wrap { width: 100%; height: 100%; overflow: hidden; border: 4px solid #38bdf8; position: relative; cursor: pointer; }
    .deportista-img-wrap img { width: 100%; height: 100%; object-fit: cover; transition: transform 0.4s; }
    .deportista-placeholder { width: 100%; height: 100%; background: #334155; display: flex; align-items: center; justify-content: center; font-size: 64px; }
    .deportista-overlay { position: absolute; inset: 0; background: rgba(15, 23, 42, 0.8); display: flex; flex-direction: column; align-items: center; justify-content: center; opacity: 0; transition: opacity 0.3s; padding: 20px; text-align: center; }
    .deportista-img-wrap:hover .deportista-overlay { opacity: 1; }
    .deportista-img-wrap:hover img { transform: scale(1.1); }
    .deportista-name { font-size: 20px; font-weight: 700; color: #fff; margin-bottom: 8px; }
    .deportista-sport { font-size: 14px; color: #ffd700; text-transform: uppercase; letter-spacing: 1px; font-weight: 600; }
    .dep-nav { position: absolute; top: 50%; transform: translateY(-50%); background: transparent; border: none; color: rgba(255,255,255,0.7); font-size: 40px; cursor: pointer; z-index: 10; transition: color 0.2s; text-shadow: 0 2px 4px rgba(0,0,0,0.5); }
    .dep-nav:hover { color: #fff; }
    .dep-nav.left { left: 10px; }
    .dep-nav.right { right: 10px; }
    .dep-logo { width: 220px; flex-shrink: 0; display: flex; justify-content: center; }
    .dep-logo img { max-width: 100%; filter: drop-shadow(0 4px 6px rgba(0,0,0,0.4)); }

    /* ─── ESCUELAS ─── */
    .escuelas-carousel { display: flex; align-items: center; justify-content: center; position: relative; padding: 0 60px; max-width: 1100px; margin: 0 auto; }
    .escuelas-track { display: flex; gap: 10px; overflow: hidden; justify-content: center; flex: 1; }
    .escuela-card { flex-shrink: 0; width: 220px; }
    .escuela-img-wrap { position: relative; height: 350px; cursor: pointer; transform: skewX(-15deg); overflow: hidden; }
    .escuela-img-wrap img { width: 140%; height: 100%; object-fit: cover; transform: skewX(15deg) translateX(-15%); transition: transform 0.4s; }
    .escuela-placeholder { width: 100%; height: 100%; background: linear-gradient(135deg, #dbeafe, #bfdbfe); display: flex; align-items: center; justify-content: center; font-size: 56px; transform: skewX(15deg); }
    .escuela-overlay { position: absolute; inset: 0; background: rgba(15, 23, 42, 0.85); display: flex; align-items: center; justify-content: center; opacity: 0; transition: opacity 0.3s; transform: skewX(15deg); text-align: center; padding: 10px; }
    .escuela-img-wrap:hover .escuela-overlay { opacity: 1; }
    .escuela-img-wrap:hover img { transform: skewX(15deg) translateX(-15%) scale(1.1); }
    .escuela-overlay span { color: #fff; font-size: 16px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; }
    .car-btn-out { position: absolute; top: 50%; transform: translateY(-50%); background: transparent; border: none; color: rgba(255,255,255,0.6); font-size: 48px; cursor: pointer; transition: color 0.2s; }
    .car-btn-out:hover { color: #fff; }
    .car-btn-out.left { left: 0; }
    .car-btn-out.right { right: 0; }

    /* ─── EVENTOS ─── */
    .eventos-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 24px; }
    .evento-card { background: rgba(255,255,255,0.08); border: 1px solid rgba(255,255,255,0.1); border-radius: 16px; overflow: hidden; transition: all 0.3s; }
    .evento-card:hover { background: rgba(255,255,255,0.12); transform: translateY(-4px); }
    .evento-img-wrap { height: 160px; overflow: hidden; }
    .evento-img-wrap img { width: 100%; height: 100%; object-fit: cover; }
    .evento-placeholder { width: 100%; height: 100%; background: rgba(255,255,255,0.05); display: flex; align-items: center; justify-content: center; font-size: 48px; }
    .evento-info { padding: 20px; }
    .evento-info h3 { margin: 0 0 6px 0; font-size: 16px; font-weight: 700; color: #fff; }
    .evento-deporte { font-size: 12px; color: #ffd700; margin: 0 0 10px 0; }
    .evento-fechas { font-size: 12px; color: rgba(255,255,255,0.6); margin-bottom: 10px; }
    .evento-estado { font-size: 11px; padding: 3px 10px; border-radius: 99px; background: rgba(255,255,255,0.1); color: rgba(255,255,255,0.7); font-weight: 600; }
    .evento-estado.activo { background: rgba(22,163,74,0.2); color: #4ade80; }

    /* ─── FOOTER ─── */
    .landing-footer { background: #0f172a; color: rgba(255,255,255,0.7); padding: 60px 60px 0; }
    .footer-content { display: flex; gap: 80px; flex-wrap: wrap; margin-bottom: 48px; }
    .footer-brand { display: flex; align-items: center; gap: 12px; font-size: 18px; font-weight: 700; color: #fff; }
    .footer-links { display: flex; gap: 60px; flex: 1; flex-wrap: wrap; }
    .footer-col { display: flex; flex-direction: column; gap: 10px; }
    .footer-col h4 { color: #fff; font-size: 14px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; margin: 0 0 6px 0; }
    .footer-col a { font-size: 14px; color: rgba(255,255,255,0.6); transition: color 0.2s; }
    .footer-col a:hover { color: #ffd700; }
    .footer-bottom { border-top: 1px solid rgba(255,255,255,0.08); padding: 20px 0; text-align: center; font-size: 13px; }
  `]
})
export class LandingComponent implements OnInit, OnDestroy {
  private svc = inject(PublicistaService);

  logros = signal<Logro[]>([]);
  deportistas = signal<DeportistaDestacado[]>([]);
  deportes = signal<Deporte[]>([]);
  eventos = signal<Evento[]>([]);
  cartas = signal<CartaCondolencia[]>([]);

  showCondolencias = signal(false);
  cartaIdx = signal(0);
  deportistaIdx = signal(0);

  deportistasIzq = signal<DeportistaDestacado[]>([]);
  deportistasDer = signal<DeportistaDestacado[]>([]);

  idxIzq = signal(0);
  idxDer = signal(0);
  idxEscuela = signal(0);
  private intervals: any[] = [];

  ngOnInit() {
    this.svc.getLogros().subscribe(d => this.logros.set(d));
    this.svc.getDeportistas().subscribe(d => {
      this.deportistas.set(d);
      // Split items alternating (odds and evens)
      this.deportistasIzq.set(d.filter((_, i) => i % 2 !== 0));
      this.deportistasDer.set(d.filter((_, i) => i % 2 === 0));
    });
    this.svc.getDeportes().subscribe(d => this.deportes.set(d));
    this.svc.getEventos().subscribe(d => this.eventos.set(d.slice(0, 6)));
    this.svc.getCartas().subscribe(d => {
      this.cartas.set(d);
      if (d.length > 0) this.showCondolencias.set(true);
    });

    if (typeof window !== 'undefined') {
      this.intervals.push(setInterval(() => this.nextIzq(), 4000));
      this.intervals.push(setInterval(() => this.nextDer(), 4500));
      this.intervals.push(setInterval(() => this.nextEscuela(), 5000));
    }
  }

  ngOnDestroy() {
    this.intervals.forEach(i => clearInterval(i));
  }

  closeCondolencias() { this.showCondolencias.set(false); }
  prevCarta() { this.cartaIdx.update(i => (i - 1 + this.cartas().length) % this.cartas().length); }
  nextCarta() { this.cartaIdx.update(i => (i + 1) % this.cartas().length); }

  prevIzq() { this.idxIzq.update(i => (i - 1 + this.deportistasIzq().length) % this.deportistasIzq().length); }
  nextIzq() { this.idxIzq.update(i => (i + 1) % this.deportistasIzq().length); }
  
  prevDer() { this.idxDer.update(i => (i - 1 + this.deportistasDer().length) % this.deportistasDer().length); }
  nextDer() { this.idxDer.update(i => (i + 1) % this.deportistasDer().length); }

  visibleEscuelas(): Deporte[] {
    const all = this.deportes();
    if (all.length === 0) return [];
    const start = this.idxEscuela();
    return all.slice(start, start + 4);
  }

  prevEscuela() {
    this.idxEscuela.update(i => {
      const allLen = this.deportes().length;
      if (allLen === 0) return 0;
      let newIdx = i - 4;
      if (newIdx < 0) {
        const rem = allLen % 4;
        newIdx = allLen - (rem === 0 ? 4 : rem);
      }
      return newIdx;
    });
  }

  nextEscuela() {
    this.idxEscuela.update(i => {
      const allLen = this.deportes().length;
      if (allLen === 0) return 0;
      let newIdx = i + 4;
      if (newIdx >= allLen) newIdx = 0;
      return newIdx;
    });
  }
}
