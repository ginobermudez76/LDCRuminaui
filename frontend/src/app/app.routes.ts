import { Routes } from '@angular/router';
import { LoginComponent } from './features/auth/login/login.component';
import { DashboardComponent } from './features/dashboard/dashboard.component';
import { SolicitudesListComponent } from './features/solicitudes/solicitudes-list/solicitudes-list.component';
import { SolicitudFormComponent } from './features/solicitudes/solicitud-form/solicitud-form.component';
import { UserAdminComponent } from './features/admin/user-admin/user-admin.component';
import { AsignadasPlaceholderComponent } from './features/solicitudes/asignadas-placeholder.component';
import { WorkflowAdminComponent } from './features/admin/workflow-admin/workflow-admin.component';
import { DeportesComponent } from './features/publicista/deportes/deportes.component';
import { EventosComponent } from './features/publicista/eventos/eventos.component';
import { LogrosComponent } from './features/publicista/logros/logros.component';
import { CursosComponent } from './features/publicista/cursos/cursos.component';
import { DocumentosComponent } from './features/publicista/documentos/documentos.component';
import { DeportistasComponent } from './features/publicista/deportistas/deportistas.component';
import { CartasCondolenciaComponent } from './features/publicista/cartas-condolencia/cartas-condolencia.component';
import { LandingComponent } from './features/landing/landing.component';
import { authGuard } from './core/guards/auth.guard';

export const routes: Routes = [
  // Public landing page
  { path: '', component: LandingComponent },
  { path: 'login', component: LoginComponent },
  {
    path: 'dashboard',
    component: DashboardComponent,
    canActivate: [authGuard],
    children: [
      { path: '', redirectTo: 'solicitudes', pathMatch: 'full' },
      { path: 'solicitudes', component: SolicitudesListComponent },
      { path: 'solicitudes/nueva', component: SolicitudFormComponent },
      { path: 'register', component: UserAdminComponent },
      { path: 'asignadas', component: AsignadasPlaceholderComponent },
      { path: 'workflow', component: WorkflowAdminComponent },
      // Publicista routes
      { path: 'deportes', component: DeportesComponent },
      { path: 'eventos', component: EventosComponent },
      { path: 'logros', component: LogrosComponent },
      { path: 'cursos', component: CursosComponent },
      { path: 'documentos', component: DocumentosComponent },
      { path: 'deportistas', component: DeportistasComponent },
      { path: 'cartas', component: CartasCondolenciaComponent },
    ]
  },
  { path: '**', redirectTo: '' }
];
