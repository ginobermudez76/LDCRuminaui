import { Component, OnInit, signal, inject, OnDestroy } from '@angular/core';
import { CommonModule } from '@angular/common';
import { PublicistaService, Logro, DeportistaDestacado, Deporte, Evento, CartaCondolencia } from '../../core/services/publicista.service';
import { NavbarComponent } from '../../shared/components/navbar/navbar.component';
import { FooterComponent } from '../../shared/components/footer/footer.component';

interface Noticia {
  id: number;
  titulo: string;
  imagen?: string;
  cuerpo?: string;
}

@Component({
  selector: 'app-landing',
  standalone: true,
  imports: [CommonModule, NavbarComponent, FooterComponent],
  template: `
    <!-- CONDOLENCIAS MODAL -->
    <div class="modal-overlay" *ngIf="showCondolencias()" (click)="closeCondolencias()">
      <div class="modal-box" (click)="$event.stopPropagation()">
        <button class="modal-close" (click)="closeCondolencias()">
          <span class="material-symbols-outlined">close</span>
        </button>
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
    <app-navbar></app-navbar>


    <!-- HERO SECTION -->
    <section id="inicio" class="hero-section">
      <div class="hero-content">
        <h5 class="hero-tagline">Más que una liga deportiva</h5>
        
        <!-- Legacy SVG Title Mark -->
        <div class="legacy-title-wrap">
          <svg class="titulo-inicio-legacy" data-name="Capa 1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 644.08 250.78">

            <path class="cls-legacy-1" d="M89.39,122.33l7.54-71.66.13-1.58a10.59,10.59,0,0,0-2.51-8.6h22.21a12.2,12.2,0,0,0-4.37,8.6l-.13,1.58-6.87,64.92-10.84,6.74h10.17l-.26,1.72q-.66,5.42,2.64,8.59H78.15L63.74,122.33l7.54-71.66.13-1.58q.53-5.56-2.51-8.6H91.24a12.52,12.52,0,0,0-4.36,8.6l-.13,1.58-7.54,71.66Z" transform="translate(-59.12 -40.49)" />
            <path class="cls-legacy-1" d="M134.08,85.05l-4.1,39q-.67,5.42,2.77,8.59H110.41a12.77,12.77,0,0,0,4.23-8.59l7.8-73.38.13-1.58q.53-5.56-2.51-8.6H142.4a12,12,0,0,0-4.49,8.6l-.13,1.58-2.25,20.5,8.72,15.33,3.84-35.83.13-1.58a10.59,10.59,0,0,0-2.51-8.6h22.21a12.19,12.19,0,0,0-4.36,8.6l-.14,1.58-7.8,73.38q-.66,5.42,2.65,8.59H136.06a13.17,13.17,0,0,0,4.23-8.59l2.51-23.67Z" transform="translate(-59.12 -40.49)" />
            <path class="cls-legacy-1" d="M210.09,132.64H187.88a12.76,12.76,0,0,0,4.23-8.59q.39-3.18.86-8.07T194,105.8c.39-3.52.77-7,1.12-10.44s.66-6.35.93-8.73L190.59,90c-1.81,1.11-3.64,2.23-5.49,3.38q-.91,7.92-1.78,16.19t-1.52,14.48c-.36,3.61.53,6.47,2.64,8.59H162.23a12.05,12.05,0,0,0,4.23-8.59q1.86-16.8,3.57-33.25t3.44-33.25l11.1-6.74H174.26a5.11,5.11,0,0,1,.07-.93,5.27,5.27,0,0,0,.07-.79c.43-3.62-.4-6.48-2.52-8.6H201q6.87,5.16,14.27,10.32-2,18.38-3.9,36.55t-3.9,36.69Q206.92,129.47,210.09,132.64ZM197,77.38l2.91-26.57H189.6L186,84.12Z" transform="translate(-59.12 -40.49)" />
            <path class="cls-legacy-1" d="M274.6,122.46,276.72,102l16.39-10.31L293,93.38l-2.38,22.21-11,6.87h10.31l-.27,1.59q-.66,5.42,2.65,8.59H244.46a13.17,13.17,0,0,0,4.23-8.59l7-66.5,11.1-6.88H256.49l.13-1.58a10.55,10.55,0,0,0-2.51-8.6H302a12.23,12.23,0,0,0-4.37,8.6l-.13,1.58L296.28,61,279.89,71.17l1.45-13.62,11-6.88H271.83L265,115.59l-10.84,6.87Z" transform="translate(-59.12 -40.49)" />
            <path class="cls-legacy-1" d="M339.25,122.46l-16.39,10.18H312.54l-14.27-10.18,7.53-71.65,16.39-10.18h10.32l14.27,10.18Zm-7.8-71.65H321.14l-6.88,64.78-.66,6.87h10.31Z" transform="translate(-59.12 -40.49)" />
            <path class="cls-legacy-1" d="M421.21,49.09l-.13,1.72-7.8,73.24q-.66,5.55,2.64,8.59H393.71a12.78,12.78,0,0,0,4.24-8.59l.26-1.59,7.54-71.65H395.57l-7.8,73.24c-.45,3.79.44,6.65,2.64,8.59H368.07q3.57-2.91,4.23-8.59l7.8-73.24H369.92l-7.54,71.65-.26,1.59q-.66,5.55,2.64,8.59H342.55a12.77,12.77,0,0,0,4.23-8.59l7-66.37,11-6.87H354.58l.14-1.72q.52-5.3-2.52-8.46h29l7.27,5,8.19-5h29A12,12,0,0,0,421.21,49.09Z" transform="translate(-59.12 -40.49)" />
            <path class="cls-legacy-1" d="M449.9,122.33l7.53-71.66.14-1.58a10.56,10.56,0,0,0-2.52-8.6h22.21a12.22,12.22,0,0,0-4.36,8.6l-.13,1.58-6.87,64.92-10.85,6.74h10.18l-.26,1.72q-.66,5.42,2.64,8.59H438.66l-14.41-10.31,7.54-71.66.13-1.58q.52-5.56-2.51-8.6h22.34a12.52,12.52,0,0,0-4.36,8.6l-.14,1.58-7.53,71.66Z" transform="translate(-59.12 -40.49)" />
            <path class="cls-legacy-1" d="M494.58,85.05l-4.09,39q-.67,5.42,2.77,8.59H470.92a12.77,12.77,0,0,0,4.23-8.59L483,50.67l.13-1.58q.53-5.56-2.51-8.6h22.34a12,12,0,0,0-4.49,8.6l-.14,1.58L496,71.17l8.72,15.33,3.84-35.83.13-1.58a10.59,10.59,0,0,0-2.51-8.6h22.21a12.2,12.2,0,0,0-4.37,8.6l-.13,1.58-7.8,73.38q-.66,5.42,2.65,8.59H496.57a13.22,13.22,0,0,0,4.23-8.59l2.51-23.67Z" transform="translate(-59.12 -40.49)" />
            <path class="cls-legacy-1" d="M553.81,40.49a12.19,12.19,0,0,0-4.36,8.6l-.14,1.58-7.8,73.38q-.66,5.42,2.65,8.59H522a13.17,13.17,0,0,0,4.23-8.59L534,50.67l.13-1.58a10.59,10.59,0,0,0-2.51-8.6Z" transform="translate(-59.12 -40.49)" />
            <path class="cls-legacy-1" d="M600.08,50.67l-7.54,71.66-19.83,10.31H547.06a12.73,12.73,0,0,0,4.23-8.59l7-66.5,11.11-6.88H559.09l.14-1.58q.53-5.56-2.51-8.6H585.8Zm-22.87,71.66,7.53-71.66H574.43l-6.87,64.78-10.84,6.88Z" transform="translate(-59.12 -40.49)" />
            <path class="cls-legacy-1" d="M646.48,132.64H624.27a12.76,12.76,0,0,0,4.23-8.59c.27-2.12.55-4.81.86-8.07s.66-6.65,1.06-10.18.77-7,1.12-10.44.66-6.35.93-8.73Q629.69,88.36,627,90l-5.49,3.38q-.91,7.92-1.78,16.19t-1.52,14.48q-.53,5.42,2.64,8.59H598.62a12.05,12.05,0,0,0,4.23-8.59q1.86-16.8,3.57-33.25t3.44-33.25L621,50.81H610.65a5.11,5.11,0,0,1,.07-.93,5.27,5.27,0,0,0,.07-.79q.66-5.43-2.51-8.6h29.08q6.87,5.16,14.28,10.32-2,18.38-3.9,36.55t-3.9,36.69C643.48,127.66,644.37,130.52,646.48,132.64ZM633.39,77.38l2.91-26.57H626l-3.57,33.31Z" transform="translate(-59.12 -40.49)" />
            <path class="cls-legacy-1" d="M703.19,50.67l-7.53,71.66-19.83,10.31H650.18a12.77,12.77,0,0,0,4.23-8.59l7-66.5,11.1-6.88H662.21l.13-1.58q.52-5.56-2.51-8.6h29.09Zm-22.87,71.66,7.54-71.66H677.55l-6.88,64.78-10.84,6.88Z" transform="translate(-59.12 -40.49)" />
            <path class="cls-legacy-1" d="M107,291.28H84.76A12.76,12.76,0,0,0,89,282.69c.27-2.12.55-4.81.86-8.07s.66-6.65,1.06-10.18.77-7,1.12-10.44.66-6.35.93-8.73q-2.77,1.72-5.49,3.37L82,252q-.93,7.92-1.79,16.19t-1.52,14.48q-.53,5.42,2.65,8.59H59.12a12.13,12.13,0,0,0,4.23-8.59q1.84-16.79,3.57-33.25t3.43-33.25l11.11-6.74H71.15a6.31,6.31,0,0,1,.06-.93,4.22,4.22,0,0,0,.07-.79q.66-5.43-2.51-8.59H97.85q6.87,5.15,14.28,10.31-2,18.38-3.9,36.55t-3.9,36.69Q103.81,288.11,107,291.28ZM93.88,236l2.91-26.57H86.48l-3.57,33.31Z" transform="translate(-59.12 -40.49)" />
            <path class="cls-legacy-1" d="M164,209.31,161.18,235,133.81,252l-3.31,30.67q-.66,5.42,2.65,8.59H110.94a13.17,13.17,0,0,0,4.23-8.59l7-66.5,11-6.88H123l.13-1.58a10.52,10.52,0,0,0-2.51-8.59h28.95ZM145.58,236l2.91-26.71H138.3l-3.56,33.45Z" transform="translate(-59.12 -40.49)" />
            <path class="cls-legacy-1" d="M210.09,291.28H187.88a12.76,12.76,0,0,0,4.23-8.59q.39-3.18.86-8.07T194,264.44c.39-3.52.77-7,1.12-10.44s.66-6.35.93-8.73l-5.49,3.37c-1.81,1.11-3.64,2.23-5.49,3.38q-.91,7.92-1.78,16.19t-1.52,14.48c-.36,3.61.53,6.47,2.64,8.59H162.23a12.05,12.05,0,0,0,4.23-8.59q1.86-16.79,3.57-33.25t3.44-33.25l11.1-6.74H174.26a5.11,5.11,0,0,1,.07-.93,5.27,5.27,0,0,0,.07-.79c.43-3.62-.4-6.48-2.52-8.59H201q6.87,5.15,14.27,10.31-2,18.38-3.9,36.55t-3.9,36.69Q206.92,288.11,210.09,291.28ZM197,236l2.91-26.57H189.6L186,242.76Z" transform="translate(-59.12 -40.49)" />
            <path class="cls-legacy-1" d="M243.8,281l3.44-33.45-27.37,17,5-48.38,11.1-6.88H225.69l.13-1.58q.52-5.55-2.51-8.59h47.86a12.19,12.19,0,0,0-4.37,8.59l-.13,1.58L265.35,222l-16.52,10.17,1.71-16,11-6.88H241.16l-3.84,35.83,27.37-17-.26,1.72L259.14,281l-16.4,10.31H213.66a12.77,12.77,0,0,0,4.23-8.59l1.32-11.9,16.53-10.31-.13,1-1.33,12.7-11,6.74Z" transform="translate(-59.12 -40.49)" />
            <path class="cls-legacy-1" d="M296.42,199.14a12.19,12.19,0,0,0-4.37,8.59l-.13,1.58-7.8,73.38q-.66,5.42,2.64,8.59H264.55a13.18,13.18,0,0,0,4.24-8.59l7.8-73.38.13-1.58a10.55,10.55,0,0,0-2.51-8.59Z" transform="translate(-59.12 -40.49)" />
            <path class="cls-legacy-1" d="M332.77,281.1l-16.39,10.18H306.06L291.79,281.1l7.53-71.65,16.4-10.18H326l14.28,10.18ZM325,209.45H314.66l-6.88,64.78-.66,6.87h10.32Z" transform="translate(-59.12 -40.49)" />
            <path class="cls-legacy-1" d="M359.48,243.69l-4.1,39q-.67,5.42,2.77,8.59H335.81a12.77,12.77,0,0,0,4.23-8.59l7.8-73.38.13-1.58q.52-5.55-2.51-8.59H367.8a12,12,0,0,0-4.49,8.59l-.13,1.58-2.25,20.5,8.72,15.33,3.84-35.83.13-1.58a10.55,10.55,0,0,0-2.51-8.59h22.21a12.14,12.14,0,0,0-4.36,8.59l-.14,1.58L381,282.69q-.66,5.42,2.65,8.59H361.46a13.22,13.22,0,0,0,4.23-8.59L368.2,259Z" transform="translate(-59.12 -40.49)" />
            <path class="cls-legacy-1" d="M435.49,291.28H413.28a12.76,12.76,0,0,0,4.23-8.59c.27-2.12.55-4.81.86-8.07s.66-6.65,1.06-10.18.77-7,1.12-10.44.66-6.35.93-8.73q-2.78,1.72-5.49,3.37L410.5,252q-.92,7.92-1.78,16.19t-1.52,14.48q-.52,5.42,2.64,8.59H387.63a12.05,12.05,0,0,0,4.23-8.59q1.86-16.79,3.57-33.25t3.44-33.25L410,209.45H399.66a5.11,5.11,0,0,1,.07-.93,5.27,5.27,0,0,0,.07-.79q.66-5.43-2.51-8.59h29.08q6.87,5.15,14.28,10.31-2,18.38-3.9,36.55t-3.9,36.69C432.49,286.3,433.38,289.16,435.49,291.28ZM422.4,236l2.91-26.57H415l-3.57,33.31Z" transform="translate(-59.12 -40.49)" />
            <path class="cls-legacy-1" d="M492.2,209.31,484.67,281l-19.83,10.31H439.19a12.77,12.77,0,0,0,4.23-8.59l7-66.5,11.1-6.88H451.22l.13-1.58q.52-5.55-2.51-8.59h29.09ZM469.33,281l7.54-71.66H466.56l-6.88,64.78L448.84,281Z" transform="translate(-59.12 -40.49)" />
            <path class="cls-legacy-1" d="M538.61,291.28H516.4a12.8,12.8,0,0,0,4.23-8.59q.39-3.18.86-8.07c.3-3.26.66-6.65,1.06-10.18s.77-7,1.12-10.44.66-6.35.92-8.73q-2.77,1.72-5.48,3.37c-1.81,1.11-3.64,2.23-5.49,3.38q-.93,7.92-1.78,16.19t-1.52,14.48c-.36,3.61.53,6.47,2.64,8.59H490.75a12.05,12.05,0,0,0,4.23-8.59q1.84-16.79,3.57-33.25T502,216.19l11.1-6.74H502.78a5.11,5.11,0,0,1,.07-.93,5.25,5.25,0,0,0,.06-.79q.66-5.43-2.51-8.59h29.09q6.87,5.15,14.27,10.31-2,18.38-3.9,36.55T536,282.69Q535.43,288.11,538.61,291.28ZM525.52,236l2.91-26.57H518.12l-3.57,33.31Z" transform="translate(-59.12 -40.49)" />
          </svg>
        </div>
        
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
    <section id="logros" class="logros-section-legacy">
      <div class="titulo-containerl">
        <svg class="logros-title-svg" viewBox="0 0 1200 160" xmlns="http://www.w3.org/2000/svg">
          <path d="M 0,130 L 108,130 L 108,80 L 480,80" fill="none" stroke="#0fc3c6" stroke-width="5" stroke-linecap="square" />
          <text x="600" y="80" class="svg-title-text" dominant-baseline="central" text-anchor="middle">Logros</text>
        </svg>
      </div>
      
      <div class="contenedor-logros">
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
                <span *ngIf="!logro.tipologro" class="material-symbols-outlined" style="font-size: 48px; color: rgba(255,255,255,0.7);">star</span>
              </div>
            </div>
          </div>
        </div>
        <ng-template #noLogros><p class="empty-section white">No hay logros para mostrar.</p></ng-template>
      </div>
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
    <app-footer></app-footer>
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
    .hero-section {
      min-height: calc(100vh - 68px);
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      text-align: center;
      padding: 60px 40px;
      position: relative;
      overflow: hidden;
    }
    .hero-section::before {
      content: '';
      position: absolute;
      inset: 0;
      background: linear-gradient(135deg, rgba(3, 0, 34, 0.6) 0%, rgba(17, 99, 124, 0.4) 100%), url('/img/portada.png');
      background-size: cover;
      background-position: center;
      transform: scaleX(-1);
      z-index: 0;
    }
    .hero-section::after {
      content: '';
      position: absolute;
      inset: 0;
      background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.02'%3E%3Ccircle cx='30' cy='30' r='4'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
      z-index: 1;
      pointer-events: none;
    }
    .hero-content { position: relative; z-index: 2; width: 100%; display: flex; flex-direction: column; align-items: center; }
    .hero-tagline {
      display: block;
      width: 100%;
      max-width: 600px;
      margin: 0 auto;
      text-align: left;
      padding-left: 4px;
      color: #ffffff;
      font-family: 'Tw Cen MT Condensed', 'Tw Cen MT', 'Arial Narrow', sans-serif;
      font-size: 26px;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 3px;
      background: none;
      border: none;
      padding-top: 0;
      padding-bottom: 0;
      padding-right: 0;
      text-shadow: 1px 1px 4px rgba(0,0,0,0.8);
    }
    
    .legacy-title-wrap {
      width: 100%;
      max-width: 600px;
      margin: 10px auto 30px;
      filter: drop-shadow(0 4px 12px rgba(0, 0, 0, 0.7));
    }
    .titulo-inicio-legacy {
      width: 100%;
      height: auto;
      display: block;
    }
    .titulo-inicio-legacy .cls-legacy-1 {
      fill: #ffffff;
      transition: fill 0.25s ease;
      cursor: pointer;
    }
    .titulo-inicio-legacy .cls-legacy-1:hover {
      fill: #0fc3c6;
    }
    
    .hero-subtitle { font-size: 16px; color: #fff; text-shadow: 0 2px 8px rgba(0,0,0,0.9), 0 1px 2px rgba(0,0,0,0.9); max-width: 540px; margin: 0 auto 36px; text-align: center; line-height: 1.7; font-weight: 500; }
    .hero-btns { display: flex; gap: 16px; justify-content: center; flex-wrap: wrap; }
    .btn-hero-primary { background: #4BBBEC; color: #ffffff; border: 1px solid #4BBBEC; padding: 14px 32px; border-radius: 10px; font-weight: 700; font-size: 15px; transition: all 0.2s; }
    .btn-hero-primary:hover { background: #2a9fd6; border-color: #2a9fd6; transform: translateY(-2px); box-shadow: 0 8px 25px rgba(75,187,236,0.4); }
    .btn-hero-secondary { background: rgba(255,255,255,0.1); color: #fff; border: 1px solid rgba(255,255,255,0.3); padding: 14px 32px; border-radius: 10px; font-weight: 600; font-size: 15px; transition: all 0.2s; }
    .btn-hero-secondary:hover { background: rgba(255,255,255,0.2); }
    .hero-badges { display: flex; gap: 24px; margin-top: 60px; flex-wrap: wrap; justify-content: center; position: relative; z-index: 2; }
    .badge-card { background: rgba(255,255,255,0.08); border: 1px solid rgba(255,255,255,0.15); backdrop-filter: blur(8px); padding: 20px 32px; border-radius: 12px; text-align: center; }
    .badge-num { display: block; font-size: 36px; font-weight: 800; color: #ffffff; }
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
    .logros-section-legacy {
      min-height: 100vh;
      background: linear-gradient(to bottom, #030022, #11637c);
      color: #fff;
      padding: 40px 0 80px;
      position: relative;
      overflow: hidden;
      display: flex;
      flex-direction: column;
    }

    .titulo-containerl {
      position: relative;
      width: 100%;
      display: flex;
      justify-content: center;
      align-items: center;
      padding: 40px 0;
    }

    .logros-title-svg {
      width: 100%;
      height: auto;
      max-width: 1200px;
      display: block;
    }

    .svg-title-text {
      font-family: "Lobster", 'Pattaya', cursive;
      font-size: 64px;
      fill: #ffffff;
      font-weight: 400;
    }

    .contenedor-logros {
      flex: 1;
      max-width: 1200px;
      width: 100%;
      margin: 0 auto;
      padding: 0 40px;
      display: flex;
      align-items: center;
    }

    .logros-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(340px, 1fr));
      gap: 28px;
      width: 100%;
    }

    .logro-card-legacy {
      position: relative;
      background: rgba(255,255,255,0.04);
      border: 1px solid rgba(15,195,198,0.2);
      border-left: 5px solid #0fc3c6;
      border-radius: 8px;
      overflow: hidden;
      box-shadow: 0 4px 15px rgba(0,0,0,0.3);
      transition: all 0.3s ease;
      height: 480px;
      display: flex;
      cursor: pointer;
    }

    .logro-card-legacy:hover {
      transform: translateY(-5px);
      box-shadow: 0 12px 30px rgba(0,0,0,0.5);
      border-color: rgba(15,195,198,0.5);
      border-left-color: #abb51a;
    }

    .logro-bg {
      position: absolute;
      inset: 0;
      background-size: cover;
      background-position: center;
      opacity: 0.15;
      transition: opacity 0.4s ease;
      z-index: 1;
    }

    .logro-card-legacy:hover .logro-bg {
      opacity: 0.6;
    }

    .logro-content {
      position: relative;
      z-index: 2;
      display: flex;
      justify-content: space-between;
      align-items: flex-end;
      width: 100%;
      height: 100%;
      padding: 32px 28px;
      background: linear-gradient(to top, rgba(3,0,34,0.9) 0%, rgba(3,0,34,0.2) 65%, rgba(3,0,34,0) 100%);
    }

    .logro-text {
      display: flex;
      flex-direction: column;
      gap: 6px;
    }

    .logro-text h3 {
      margin: 0;
      font-size: 24px;
      color: #fff;
      font-weight: 700;
      text-shadow: 1px 1px 3px rgba(0,0,0,0.8);
      font-family: 'Poppins', sans-serif;
    }

    .logro-deporte {
      font-size: 16px;
      color: #0fc3c6;
      font-weight: 600;
      text-shadow: 1px 1px 2px rgba(0,0,0,0.8);
    }

    .logro-icon {
      font-size: 64px;
      color: rgba(255,255,255,0.7);
      transition: all 0.3s ease;
    }

    .logro-card-legacy:hover .logro-icon {
      color: #ffd700;
      transform: scale(1.1);
    }

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
      this.intervals.push(setInterval(() => this.nextCarta(), 6000));
    }
  }

  ngOnDestroy() {
    this.intervals.forEach(i => clearInterval(i));
  }

  closeCondolencias() { this.showCondolencias.set(false); }

  prevCarta() {
    const len = this.cartas().length;
    if (len > 0) this.cartaIdx.update(i => (i - 1 + len) % len);
  }
  nextCarta() {
    const len = this.cartas().length;
    if (len > 0) this.cartaIdx.update(i => (i + 1) % len);
  }

  prevIzq() {
    const len = this.deportistasIzq().length;
    if (len > 0) this.idxIzq.update(i => (i - 1 + len) % len);
  }
  nextIzq() {
    const len = this.deportistasIzq().length;
    if (len > 0) this.idxIzq.update(i => (i + 1) % len);
  }

  prevDer() {
    const len = this.deportistasDer().length;
    if (len > 0) this.idxDer.update(i => (i - 1 + len) % len);
  }
  nextDer() {
    const len = this.deportistasDer().length;
    if (len > 0) this.idxDer.update(i => (i + 1) % len);
  }

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
