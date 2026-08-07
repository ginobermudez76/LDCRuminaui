import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';

export interface Solicitud {
  s_id?: number;
  uuid: string;
  codigo: string;
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
    uuid?: string;
    name_tipo: string;
  };
  solicitante_relation?: {
    id: number;
    uuid?: string;
    primer_nombre: string;
    primer_apellido: string;
    nombre_usuario: string;
  };
  encargado_relation?: {
    id: number;
    uuid?: string;
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
    uuid?: string;
    rol_name: string;
  };
  historiales?: any[];
}

@Injectable({
  providedIn: 'root',
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

  getSolicitudByUuid(uuid: string): Observable<Solicitud> {
    return this.http.get<Solicitud>(`${this.apiUrl}/${uuid}`);
  }

  createSolicitud(data: any): Observable<any> {
    return this.http.post(this.apiUrl, data);
  }

  updateSolicitud(uuid: string, data: any): Observable<any> {
    return this.http.put(`${this.apiUrl}/${uuid}`, data);
  }

  deleteSolicitud(uuid: string): Observable<any> {
    return this.http.delete(`${this.apiUrl}/${uuid}`);
  }

  reassignSolicitud(
    uuid: string,
    data: { encargado?: string; departamento_encargado?: string; tipo?: string },
  ): Observable<any> {
    return this.http.patch(`${this.apiUrl}/${uuid}/reassign`, data);
  }

  procesarSolicitud(uuid: string, accion: 'Aprobar' | 'Denegar'): Observable<any> {
    return this.http.post(`${this.apiUrl}/${uuid}/procesar`, { accion });
  }
}
