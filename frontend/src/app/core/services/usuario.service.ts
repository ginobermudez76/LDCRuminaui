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

  createUsuario(usuario: any): Observable<Usuario> {
    return this.http.post<Usuario>(this.apiUrl, usuario);
  }
}
