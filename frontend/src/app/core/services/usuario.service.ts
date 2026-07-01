import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';
import { Rol } from './solicitud-tipo.service';

export interface Usuario {
  id: number;
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
  rol_relation?: {
    id: number;
    id_rol: number;
    rol?: Rol;
  }[];
}

@Injectable({
  providedIn: 'root'
})
export class UsuarioService {
  private apiUrl = 'http://localhost:8000/api/usuarios';

  constructor(private http: HttpClient) {}

  getUsuarios(): Observable<Usuario[]> {
    return this.http.get<Usuario[]>(this.apiUrl);
  }

  createUsuario(usuario: any): Observable<any> {
    return this.http.post<any>(this.apiUrl, usuario);
  }

  updateUsuario(id: number, usuario: any): Observable<Usuario> {
    return this.http.put<Usuario>(`${this.apiUrl}/${id}`, usuario);
  }

  deleteUsuario(id: number): Observable<any> {
    return this.http.delete<any>(`${this.apiUrl}/${id}`);
  }

  toggleActive(id: number): Observable<any> {
    return this.http.patch<any>(`${this.apiUrl}/${id}/toggle-active`, {});
  }

  resetPassword(id: number): Observable<any> {
    return this.http.post<any>(`${this.apiUrl}/${id}/reset-password`, {});
  }

  resendInvitation(id: number): Observable<any> {
    return this.http.post<any>(`${this.apiUrl}/${id}/resend-invitation`, {});
  }

  acceptInvitation(token: string, contrasena: string): Observable<any> {
    return this.http.post<any>(`http://localhost:8000/api/auth/accept-invitation`, { token, contrasena });
  }
}
