import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';

export interface Solicitud {
  s_id: number;
  s_fecha: string;
  s_doc?: string;
  s_valor: number;
  tipo: number;
  solicitante: number;
  encargado?: number;
  solicitantext?: number;
  descripcion?: string;
  estado: number;
  departamento_encargado?: number;
  tipo_relation?: {
    id_tipo: number;
    name_tipo: string;
  };
  solicitante_relation?: {
    id: number;
    primer_nombre: string;
    primer_apellido: string;
    nombre_usuario: string;
  };
  encargado_relation?: {
    id: number;
    primer_nombre: string;
    primer_apellido: string;
    nombre_usuario: string;
  };
  solicitantext_relation?: {
    id_ext: number;
    ext_nombre: string;
    ext_apellido: string;
    cedula: string;
  };
  estado_relation?: {
    id_estado: number;
    estado_nombre: string;
  };
  departamento_encargado_relation?: {
    id_rol: number;
    rol_name: string;
  };
}

@Injectable({
  providedIn: 'root'
})
export class SolicitudService {
  private apiUrl = 'http://localhost:8000/api/solicitudes';

  constructor(private http: HttpClient) {}

  getSolicitudes(): Observable<Solicitud[]> {
    return this.http.get<Solicitud[]>(this.apiUrl);
  }

  getAsignadas(): Observable<Solicitud[]> {
    return this.http.get<Solicitud[]>(`${this.apiUrl}/asignadas`);
  }

  getSolicitudById(id: number): Observable<Solicitud> {
    return this.http.get<Solicitud>(`${this.apiUrl}/${id}`);
  }

  createSolicitud(data: any): Observable<any> {
    return this.http.post(this.apiUrl, data);
  }

  updateSolicitud(id: number, data: any): Observable<any> {
    return this.http.put(`${this.apiUrl}/${id}`, data);
  }

  deleteSolicitud(id: number): Observable<any> {
    return this.http.delete(`${this.apiUrl}/${id}`);
  }

  reassignSolicitud(id: number, data: { encargado?: number; departamento_encargado?: number; tipo?: number }): Observable<any> {
    return this.http.patch(`${this.apiUrl}/${id}/reassign`, data);
  }

  procesarSolicitud(id: number, accion: 'Aprobar' | 'Denegar'): Observable<any> {
    return this.http.post(`${this.apiUrl}/${id}/procesar`, { accion });
  }
}
