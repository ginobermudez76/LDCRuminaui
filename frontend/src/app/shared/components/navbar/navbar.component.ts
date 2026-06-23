import { Component, HostListener, signal } from '@angular/core';
import { CommonModule } from '@angular/common';
import { RouterLink } from '@angular/router';

@Component({
  selector: 'app-navbar',
  standalone: true,
  imports: [CommonModule, RouterLink],
  template: `
    <nav class="ldcr-navbar" [class.scrolled]="isScrolled()">
      <div class="nav-inner">

        <!-- Brand -->
        <a [routerLink]="['/']" class="nav-brand">
          <img src="/img/logoX_LDCR.png" alt="Logo LDCR"
               class="nav-logo" onerror="this.style.display='none'" />
          <span class="brand-name">Liga Cantonal Rumiñahui</span>
        </a>

        <!-- Hamburger (mobile) -->
        <button class="nav-toggler" (click)="toggleMenu()">
          <span class="material-symbols-outlined">menu</span>
        </button>

        <!-- Links -->
        <div class="nav-links" [class.open]="menuOpen()">

          <!-- Inicio -->
          <a [routerLink]="['/']" class="nav-link" (click)="closeMenu()">
            <span class="material-symbols-outlined nav-link-icon">home</span>
            Inicio
          </a>

          <!-- Nosotros dropdown -->
          <div class="nav-dropdown" (mouseenter)="openDd('nosotros')" (mouseleave)="closeDd()">
            <button class="nav-link dropdown-trigger" [class.active]="activeDd() === 'nosotros'">
              <span class="material-symbols-outlined nav-link-icon">info</span>
              Nosotros
              <span class="material-symbols-outlined chevron">expand_more</span>
            </button>
            <div class="dropdown-menu" [class.visible]="activeDd() === 'nosotros'">
              <a [routerLink]="['/nosotros']" [queryParams]="{tipo:'historia'}" class="dd-item" (click)="closeMenu()">
                <span class="material-symbols-outlined">history_edu</span> Historia
              </a>
              <a [routerLink]="['/nosotros']" [queryParams]="{tipo:'mision'}" class="dd-item" (click)="closeMenu()">
                <span class="material-symbols-outlined">flag</span> Misión
              </a>
              <a [routerLink]="['/nosotros']" [queryParams]="{tipo:'vision'}" class="dd-item" (click)="closeMenu()">
                <span class="material-symbols-outlined">visibility</span> Visión
              </a>
              <a [routerLink]="['/nosotros']" [queryParams]="{tipo:'directorio'}" class="dd-item" (click)="closeMenu()">
                <span class="material-symbols-outlined">groups</span> Directorio
              </a>
            </div>
          </div>

          <!-- Servicios dropdown -->
          <div class="nav-dropdown" (mouseenter)="openDd('servicios')" (mouseleave)="closeDd()">
            <button class="nav-link dropdown-trigger" [class.active]="activeDd() === 'servicios'">
              <span class="material-symbols-outlined nav-link-icon">sports</span>
              Servicios
              <span class="material-symbols-outlined chevron">expand_more</span>
            </button>
            <div class="dropdown-menu" [class.visible]="activeDd() === 'servicios'">
              <a [routerLink]="['/servicios/escuelas']" class="dd-item" (click)="closeMenu()">
                <span class="material-symbols-outlined">school</span> Escuelas permanentes
              </a>
              <a [routerLink]="['/servicios/escenarios']" class="dd-item" (click)="closeMenu()">
                <span class="material-symbols-outlined">stadium</span> Escenarios deportivos
              </a>
              <a [routerLink]="['/servicios/documentos']" class="dd-item" (click)="closeMenu()">
                <span class="material-symbols-outlined">folder_open</span> Descarga de documentos
              </a>
              <a [routerLink]="['/servicios/vacacionales']" class="dd-item" (click)="closeMenu()">
                <span class="material-symbols-outlined">calendar_month</span> Cursos vacacionales
              </a>
            </div>
          </div>

          <!-- Noticias -->
          <a [routerLink]="['/noticias']" class="nav-link" (click)="closeMenu()">
            <span class="material-symbols-outlined nav-link-icon">newspaper</span>
            Noticias
          </a>

          <!-- Contacto -->
          <a [routerLink]="['/contacto']" class="nav-link" (click)="closeMenu()">
            <span class="material-symbols-outlined nav-link-icon">mail</span>
            Contacto
          </a>

          <!-- Login button (mobile only, shown in menu) -->
          <a [routerLink]="['/login']" class="nav-btn-login mobile-only" (click)="closeMenu()">
            <span class="material-symbols-outlined">login</span>
            Ingresar
          </a>
        </div>

        <!-- Login button (desktop) -->
        <a [routerLink]="['/login']" class="nav-btn-login desktop-only">
          <span class="material-symbols-outlined">account_circle</span>
          Ingresar
        </a>

      </div>
    </nav>
  `,
  styles: [`
    :host { display: block; }

    /* ─── PALETA LDCR ─── */
    /* Primario: #030022 → #11637c  (legacy body gradient) */
    /* Acento: #0fc3c6  (legacy cyan) */
    /* Hover text: #abb51a (legacy yellow-green) */
    /* Login btn: #4BBBEC */

    .ldcr-navbar {
      position: sticky; top: 0; z-index: 1000;
      background: linear-gradient(90deg, #030022 0%, #0a2a4a 60%, #11637c 100%);
      padding: 0 32px;
      box-shadow: 0 0 10px rgba(0,0,0,0.4);
      transition: background 0.3s;
    }
    .ldcr-navbar.scrolled {
      background: rgba(3, 0, 34, 0.97);
      box-shadow: 0 2px 20px rgba(0,0,0,0.5);
    }

    .nav-inner {
      max-width: 1400px; margin: 0 auto;
      display: flex; align-items: center; gap: 8px; height: 70px;
    }

    /* Brand */
    .nav-brand { display: flex; align-items: center; gap: 10px; text-decoration: none; flex-shrink: 0; }
    .nav-logo { width: 52px; height: 52px; object-fit: contain; filter: drop-shadow(0 2px 4px rgba(0,0,0,0.4)); }
    .brand-name { font-family: 'Pattaya', 'Lobster', cursive; font-size: 15px; color: #fff; white-space: nowrap; text-shadow: 1px 1px 4px rgba(0,0,0,0.5); }

    /* Toggler */
    .nav-toggler { display: none; background: none; border: none; color: #fff; cursor: pointer; margin-left: auto; }
    .nav-toggler .material-symbols-outlined { font-size: 28px; }

    /* Links container */
    .nav-links { display: flex; align-items: center; gap: 4px; flex: 1; margin-left: 24px; }

    /* Nav link */
    .nav-link {
      display: flex; align-items: center; gap: 4px;
      color: #fff; font-family: 'Pattaya', sans-serif; font-size: 14px;
      padding: 6px 12px; border-radius: 6px; border: none; background: none; cursor: pointer;
      text-decoration: none; transition: color 0.2s, background 0.2s; white-space: nowrap;
    }
    .nav-link:hover, .nav-link.active { color: #abb51a; background: rgba(255,255,255,0.08); }
    .nav-link-icon { font-size: 18px; }

    /* Dropdown */
    .nav-dropdown { position: relative; }
    .dropdown-trigger { }
    .chevron { font-size: 18px; transition: transform 0.2s; }
    .nav-dropdown:hover .chevron { transform: rotate(180deg); }

    .dropdown-menu {
      position: absolute; top: calc(100% + 4px); left: 0;
      background: rgba(3, 0, 50, 0.97); border: 1px solid rgba(15,195,198,0.3);
      border-radius: 8px; min-width: 220px; padding: 6px 0;
      box-shadow: 0 8px 24px rgba(0,0,0,0.4);
      opacity: 0; visibility: hidden; transform: translateY(-6px);
      transition: all 0.2s; pointer-events: none;
    }
    .dropdown-menu.visible {
      opacity: 1; visibility: visible; transform: translateY(0); pointer-events: all;
    }
    .dd-item {
      display: flex; align-items: center; gap: 10px;
      color: rgba(255,255,255,0.85); font-size: 13px; padding: 10px 16px;
      text-decoration: none; transition: all 0.2s; border-left: 2px solid transparent;
    }
    .dd-item .material-symbols-outlined { font-size: 16px; color: #0fc3c6; }
    .dd-item:hover { color: #0fc3c6; background: rgba(15,195,198,0.1); border-left-color: #0fc3c6; }

    /* Login button */
    .nav-btn-login {
      display: flex; align-items: center; gap: 6px;
      background: #4BBBEC; color: #fff; font-size: 13px; font-weight: 700;
      padding: 8px 18px; border-radius: 25px; text-decoration: none; white-space: nowrap;
      border: 1px solid #4BBBEC; transition: all 0.2s; flex-shrink: 0; margin-left: auto;
      text-shadow: 1px 1px 3px rgba(0,0,0,0.3);
    }
    .nav-btn-login .material-symbols-outlined { font-size: 18px; }
    .nav-btn-login:hover { background: #2a9fd6; border-color: #2a9fd6; transform: translateY(-1px); box-shadow: 0 4px 12px rgba(75,187,236,0.4); }
    .mobile-only { display: none; }
    .desktop-only { display: flex; }

    /* Mobile */
    @media (max-width: 900px) {
      .nav-toggler { display: flex; }
      .nav-links {
        display: none; position: absolute; top: 70px; left: 0; right: 0;
        background: rgba(3, 0, 34, 0.98); flex-direction: column; align-items: flex-start;
        padding: 16px; gap: 4px; border-top: 1px solid rgba(15,195,198,0.3);
      }
      .nav-links.open { display: flex; }
      .nav-dropdown, .nav-link { width: 100%; }
      .dropdown-menu { position: static; opacity: 1; visibility: visible; transform: none; box-shadow: none; border: none; background: rgba(255,255,255,0.05); border-radius: 4px; }
      .desktop-only { display: none; }
      .mobile-only { display: flex; margin-top: 8px; }
    }
  `]
})
export class NavbarComponent {
  isScrolled = signal(false);
  menuOpen = signal(false);
  activeDd = signal<string | null>(null);

  @HostListener('window:scroll')
  onScroll() {
    this.isScrolled.set(window.scrollY > 20);
  }

  toggleMenu() { this.menuOpen.update(v => !v); }
  closeMenu() { this.menuOpen.set(false); this.activeDd.set(null); }
  openDd(name: string) { this.activeDd.set(name); }
  closeDd() { this.activeDd.set(null); }
}
