import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';
import { Rol } from './solicitud-tipo.service';

export interface Usuario {
  id?: number;
  uuid: string;
  codigo: string;
  nombre_usuario: string;
  correo_electronico: string;
  nombres: string;
  apellidos: string;
  cedula: string;
  celular: string;
  fecha_nac: string;
  activo: boolean;
  invitation_token?: string;
  invitation_expires_at?: string;
  invitation_status?: string;
  rol?: number;
  roles?: any[];
  foto_perfil?: string;
  rol_relation?: {
    id: number;
    id_rol: number;
    rol?: Rol;
  }[];
}

@Injectable({
  providedIn: 'root',
})
export class UsuarioService {
  private readonly apiUrl = 'http://localhost:8000/api/usuarios';

  constructor(private readonly http: HttpClient) {}

  getUsuarios(): Observable<Usuario[]> {
    return this.http.get<Usuario[]>(this.apiUrl);
  }

  createUsuario(usuario: any): Observable<any> {
    return this.http.post<any>(this.apiUrl, usuario);
  }

  updateUsuario(uuid: string, usuario: any): Observable<Usuario> {
    // We send FormData with _method=PUT via POST to support files
    return this.http.post<Usuario>(`${this.apiUrl}/${uuid}`, usuario);
  }

  deleteUsuario(uuid: string): Observable<any> {
    return this.http.delete<any>(`${this.apiUrl}/${uuid}`);
  }

  toggleActive(uuid: string): Observable<any> {
    return this.http.patch<any>(`${this.apiUrl}/${uuid}/toggle-active`, {});
  }

  resetPassword(uuid: string): Observable<any> {
    return this.http.post<any>(`${this.apiUrl}/${uuid}/reset-password`, {});
  }

  resendInvitation(uuid: string): Observable<any> {
    return this.http.post<any>(`${this.apiUrl}/${uuid}/resend-invitation`, {});
  }

  acceptInvitation(token: string, contrasena: string): Observable<any> {
    return this.http.post<any>(`http://localhost:8000/api/auth/accept-invitation`, {
      token,
      contrasena,
    });
  }
}
