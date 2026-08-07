import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';

export interface WorkflowStep {
  id?: number;
  solicitud_tipo_id?: number;
  orden: number;
  rol_id: string; // Change to string to hold UUID
  nombre_paso?: string;
  rol?: {
    id: number;
    uuid: string;
    nombre_rol: string;
  };
}

export interface SolicitudTipo {
  id_tipo?: number;
  uuid: string;
  codigo: string;
  name_tipo: string;
  requiere_documento: boolean;
  requiere_valor: boolean;
  requiere_descripcion: boolean;
  activo: boolean;
  steps?: WorkflowStep[];
}

export interface Rol {
  id?: number;
  uuid: string;
  codigo: string;
  nombre_rol: string;
  descripcion?: string;
}

@Injectable({
  providedIn: 'root',
})
export class SolicitudTipoService {
  private apiTipoUrl = 'http://localhost:8000/api/solicitud-tipos';
  private apiRolesUrl = 'http://localhost:8000/api/roles';

  constructor(private http: HttpClient) {}

  getSolicitudTipos(): Observable<SolicitudTipo[]> {
    return this.http.get<SolicitudTipo[]>(this.apiTipoUrl);
  }

  getSolicitudTipoByUuid(uuid: string): Observable<SolicitudTipo> {
    return this.http.get<SolicitudTipo>(`${this.apiTipoUrl}/${uuid}`);
  }

  createSolicitudTipo(data: any): Observable<any> {
    return this.http.post(this.apiTipoUrl, data);
  }

  updateSolicitudTipo(uuid: string, data: any): Observable<any> {
    return this.http.put(`${this.apiTipoUrl}/${uuid}`, data);
  }

  deleteSolicitudTipo(uuid: string): Observable<any> {
    return this.http.delete(`${this.apiTipoUrl}/${uuid}`);
  }

  getRoles(all?: boolean): Observable<Rol[]> {
    const url = all ? `${this.apiRolesUrl}?all=true` : this.apiRolesUrl;
    return this.http.get<Rol[]>(url);
  }
}
