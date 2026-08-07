import { Component, computed, signal, HostListener, inject } from '@angular/core';
import { CommonModule } from '@angular/common';
import { Router, RouterOutlet, RouterLink, RouterLinkActive } from '@angular/router';
import { ButtonModule } from 'primeng/button';
import { AuthService } from '../../core/services/auth.service';

@Component({
  selector: 'app-dashboard',
  standalone: true,
  imports: [CommonModule, RouterOutlet, RouterLink, RouterLinkActive, ButtonModule],
  templateUrl: './dashboard.component.html',
  styleUrl: './dashboard.component.css',
})
export class DashboardComponent {
  private readonly authService = inject(AuthService);
  private readonly router = inject(Router);

  user = computed(() => this.authService.currentUserSignal());
  roleName = computed(() => this.user()?.rol_relation?.rol_name || 'Usuario');

  showProfileDropdown = signal(false);
  logoError = signal(false);

  menuItems = computed(() => {
    const user = this.user();
    if (!user) return [];

    return [
      ...(this.authService.hasOption('G_SOLICITUDES_PROPIAS')
        ? [
            {
              label: 'Mis Solicitudes',
              link: '/dashboard/solicitudes',
              icon: 'pi pi-file-edit',
            },
          ]
        : []),
      ...(this.authService.hasOption('G_SOLICITUDES_ASIGNADAS')
        ? [
            {
              label: 'Solicitudes Asignadas',
              link: '/dashboard/asignadas',
              icon: 'pi pi-list',
            },
          ]
        : []),
      ...(this.authService.hasOption('REGISTRAR_USUARIOS')
        ? [
            {
              label: 'Registrar Usuarios',
              link: '/dashboard/register',
              icon: 'pi pi-user-plus',
            },
            { label: 'Configurar Flujos', link: '/dashboard/workflow', icon: 'pi pi-cog' },
          ]
        : []),
      ...(this.authService.hasOption('CONFIGURAR_RBAC')
        ? [{ label: 'Roles y Permisos', link: '/dashboard/rbac', icon: 'pi pi-shield' }]
        : []),
      ...(this.authService.hasOption('PUBLICAR_CONTENIDO')
        ? [
            { label: 'Deportes', link: '/dashboard/deportes', icon: 'pi pi-star' },
            { label: 'Eventos', link: '/dashboard/eventos', icon: 'pi pi-calendar' },
            { label: 'Logros', link: '/dashboard/logros', icon: 'pi pi-trophy' },
            { label: 'Cursos', link: '/dashboard/cursos', icon: 'pi pi-book' },
            { label: 'Documentos', link: '/dashboard/documentos', icon: 'pi pi-file' },
            { label: 'Deportistas', link: '/dashboard/deportistas', icon: 'pi pi-users' },
            { label: 'Cartas Condolencia', link: '/dashboard/cartas', icon: 'pi pi-heart' },
          ]
        : []),
    ];
  });

  toggleProfileDropdown(event: Event) {
    event.stopPropagation();
    this.showProfileDropdown.update((val) => !val);
  }

  getInitials(): string {
    const u = this.user();
    if (!u) return 'U';

    const first = u.primer_nombre ? u.primer_nombre.trim().charAt(0) : '';
    const last = u.primer_apellido ? u.primer_apellido.trim().charAt(0) : '';
    const initials = (first + last).toUpperCase();
    return initials || u.nombre_usuario.charAt(0).toUpperCase() || 'U';
  }

  goToSettings() {
    alert('Configuración del perfil: Funcionalidad en desarrollo.');
    this.showProfileDropdown.set(false);
  }

  goToDocs() {
    alert('Documentación del sistema: Funcionalidad en desarrollo.');
    this.showProfileDropdown.set(false);
  }

  goToSupport() {
    alert('Soporte técnico: Funcionalidad en desarrollo.');
    this.showProfileDropdown.set(false);
  }

  @HostListener('document:click', ['$event'])
  onDocumentClick(event: MouseEvent) {
    const target = event.target as HTMLElement;
    if (!target.closest('.user-profile-menu-container')) {
      this.showProfileDropdown.set(false);
    }
  }

  logout() {
    this.authService.logout().subscribe({
      next: () => {
        this.router.navigate(['/login']);
      },
      error: () => {
        this.router.navigate(['/login']);
      },
    });
  }
}
