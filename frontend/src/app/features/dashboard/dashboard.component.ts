import { Component, computed } from '@angular/core';
import { CommonModule } from '@angular/common';
import { Router, RouterOutlet, RouterLink, RouterLinkActive } from '@angular/router';
import { ButtonModule } from 'primeng/button';
import { AuthService } from '../../core/services/auth.service';

@Component({
  selector: 'app-dashboard',
  standalone: true,
  imports: [
    CommonModule,
    RouterOutlet,
    RouterLink,
    RouterLinkActive,
    ButtonModule
  ],
  template: `
    <div class="dashboard-wrapper">
      <!-- Top Navbar -->
      <header class="navbar">
        <div class="brand">
          <i class="pi pi-shield brand-icon"></i>
          <span>Liga Cantonal Rumiñahui</span>
        </div>
        <div class="user-info" *ngIf="user()">
          <span class="user-name">Hola, {{ user()?.primer_nombre }} ({{ roleName() }})</span>
          <button 
            pButton 
            icon="pi pi-power-off" 
            label="Salir" 
            class="p-button-danger p-button-text p-button-sm logout-btn"
            (click)="logout()"
          ></button>
        </div>
      </header>

      <!-- Main Layout -->
      <div class="main-layout">
        <!-- Sidebar Navigation -->
        <aside class="sidebar">
          <nav class="nav-menu">
            <a 
              *ngFor="let item of menuItems()"
              [routerLink]="item.link" 
              routerLinkActive="active" 
              class="nav-item"
            >
              <i [class]="item.icon + ' nav-icon'"></i>
              <span>{{ item.label }}</span>
            </a>
          </nav>
        </aside>

        <!-- Page Content Area -->
        <main class="content-area">
          <router-outlet></router-outlet>
        </main>
      </div>
    </div>
  `,
  styles: [`
    .dashboard-wrapper {
      display: flex;
      flex-direction: column;
      height: 100vh;
      overflow: hidden;
    }
    .navbar {
      display: flex;
      justify-content: space-between;
      align-items: center;
      background-color: #1e3c72;
      color: #fff;
      padding: 0 20px;
      height: 60px;
      box-shadow: 0 2px 4px rgba(0,0,0,0.1);
      z-index: 100;
    }
    .brand {
      display: flex;
      align-items: center;
      gap: 10px;
      font-size: 18px;
      font-weight: 700;
    }
    .brand-icon {
      font-size: 22px;
      color: #ffd700;
    }
    .user-info {
      display: flex;
      align-items: center;
      gap: 15px;
    }
    .user-name {
      font-size: 14px;
      font-weight: 500;
    }
    .logout-btn {
      color: #ff8a80 !important;
    }
    .main-layout {
      display: flex;
      flex: 1;
      overflow: hidden;
    }
    .sidebar {
      width: 250px;
      background-color: #ffffff;
      border-right: 1px solid #e0e0e0;
      padding: 20px 10px;
      box-sizing: border-box;
    }
    .nav-menu {
      display: flex;
      flex-direction: column;
      gap: 10px;
    }
    .nav-item {
      display: flex;
      align-items: center;
      gap: 12px;
      padding: 12px 15px;
      text-decoration: none;
      color: #555;
      font-weight: 600;
      font-size: 14px;
      border-radius: 6px;
      transition: background-color 0.2s, color 0.2s;
    }
    .nav-item:hover {
      background-color: #f0f4f8;
      color: #1e3c72;
    }
    .nav-item.active {
      background-color: #e1ecf8;
      color: #1e3c72;
    }
    .nav-icon {
      font-size: 16px;
    }
    .content-area {
      flex: 1;
      padding: 25px;
      background-color: #f8f9fa;
      overflow-y: auto;
      box-sizing: border-box;
    }
    @media (max-width: 768px) {
      .sidebar {
        width: 70px;
      }
      .nav-item span {
        display: none;
      }
    }
  `]
})
export class DashboardComponent {
  user = computed(() => this.authService.currentUserSignal());
  roleName = computed(() => this.user()?.rol_relation?.rol_name || 'Usuario');

  menuItems = computed(() => {
    const user = this.user();
    if (!user) return [];
    
    const items = [];
    if (this.authService.hasOption('G_SOLICITUDES_PROPIAS')) {
      items.push({ label: 'Mis Solicitudes', link: '/dashboard/solicitudes', icon: 'pi pi-file-edit' });
    }
    if (this.authService.hasOption('G_SOLICITUDES_ASIGNADAS')) {
      items.push({ label: 'Solicitudes Asignadas', link: '/dashboard/asignadas', icon: 'pi pi-list' });
    }
    if (this.authService.hasOption('REGISTRAR_USUARIOS')) {
      items.push({ label: 'Registrar Usuarios', link: '/dashboard/register', icon: 'pi pi-user-plus' });
      items.push({ label: 'Configurar Flujos', link: '/dashboard/workflow', icon: 'pi pi-cog' });
    }
    if (this.authService.hasOption('PUBLICAR_CONTENIDO')) {
      items.push({ label: 'Eventos y Deportes', link: '/dashboard/eventos', icon: 'pi pi-calendar' });
    }
    return items;
  });

  constructor(private authService: AuthService, private router: Router) {}

  logout() {
    this.authService.logout().subscribe({
      next: () => {
        this.router.navigate(['/login']);
      },
      error: () => {
        this.router.navigate(['/login']);
      }
    });
  }
}
