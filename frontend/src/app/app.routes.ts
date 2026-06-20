import { Routes } from '@angular/router';
import { LoginComponent } from './features/auth/login/login.component';
import { DashboardComponent } from './features/dashboard/dashboard.component';
import { SolicitudesListComponent } from './features/solicitudes/solicitudes-list/solicitudes-list.component';
import { SolicitudFormComponent } from './features/solicitudes/solicitud-form/solicitud-form.component';
import { authGuard } from './core/guards/auth.guard';

export const routes: Routes = [
  { path: '', redirectTo: 'login', pathMatch: 'full' },
  { path: 'login', component: LoginComponent },
  { 
    path: 'dashboard', 
    component: DashboardComponent,
    canActivate: [authGuard],
    children: [
      { path: '', redirectTo: 'solicitudes', pathMatch: 'full' },
      { path: 'solicitudes', component: SolicitudesListComponent },
      { path: 'solicitudes/nueva', component: SolicitudFormComponent }
    ]
  },
  { path: '**', redirectTo: 'login' }
];
