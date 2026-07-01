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
  templateUrl: './dashboard.component.html',
  styleUrl: './dashboard.component.css'
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
      items.push({ label: 'Deportes', link: '/dashboard/deportes', icon: 'pi pi-star' });
      items.push({ label: 'Eventos', link: '/dashboard/eventos', icon: 'pi pi-calendar' });
      items.push({ label: 'Logros', link: '/dashboard/logros', icon: 'pi pi-trophy' });
      items.push({ label: 'Cursos', link: '/dashboard/cursos', icon: 'pi pi-book' });
      items.push({ label: 'Documentos', link: '/dashboard/documentos', icon: 'pi pi-file' });
      items.push({ label: 'Deportistas', link: '/dashboard/deportistas', icon: 'pi pi-users' });
      items.push({ label: 'Cartas Condolencia', link: '/dashboard/cartas', icon: 'pi pi-heart' });
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
