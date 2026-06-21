import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';

export interface WorkflowStep {
  id?: number;
  solicitud_tipo_id?: number;
  orden: number;
  rol_id: number;
  nombre_paso?: string;
  rol?: {
    id: number;
    nombre_rol: string;
  };
}

export interface SolicitudTipo {
  id_tipo: number;
  name_tipo: string;
  requiere_documento: boolean;
  requiere_valor: boolean;
  requiere_descripcion: boolean;
  activo: boolean;
  steps?: WorkflowStep[];
}

export interface Rol {
  id: number;
  nombre_rol: string;
  descripcion?: string;
}

@Injectable({
  providedIn: 'root'
})
export class SolicitudTipoService {
  private apiTipoUrl = 'http://localhost:8000/api/solicitud-tipos';
  private apiRolesUrl = 'http://localhost:8000/api/roles';

  constructor(private http: HttpClient) {}

  getSolicitudTipos(): Observable<SolicitudTipo[]> {
    return this.http.get<SolicitudTipo[]>(this.apiTipoUrl);
  }

  getSolicitudTipoById(id: number): Observable<SolicitudTipo> {
    return this.http.get<SolicitudTipo>(`${this.apiTipoUrl}/${id}`);
  }

  createSolicitudTipo(data: any): Observable<any> {
    return this.http.post(this.apiTipoUrl, data);
  }

  updateSolicitudTipo(id: number, data: any): Observable<any> {
    return this.http.put(`${this.apiTipoUrl}/${id}`, data);
  }

  deleteSolicitudTipo(id: number): Observable<any> {
    return this.http.delete(`${this.apiTipoUrl}/${id}`);
  }

  getRoles(): Observable<Rol[]> {
    return this.http.get<Rol[]>(this.apiRolesUrl);
  }
}
