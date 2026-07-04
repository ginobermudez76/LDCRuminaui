import { Injectable, signal, computed } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable, tap } from 'rxjs';

export interface Usuario {
  id?: number;
  uuid: string;
  codigo: string;
  primer_nombre: string;
  segundo_nombre?: string;
  primer_apellido: string;
  segundo_apellido?: string;
  cedula: string;
  celular?: string;
  correo: string;
  nombre_usuario: string;
  rol?: number;
  fecha_nac?: string;
  rol_relation?: {
    id_rol: number;
    rol_name: string;
  };
  opciones?: string[];
  foto_perfil?: string;
}

interface LoginResponse {
  token: string;
  token_type: string;
  expires_in: number;
  user: Usuario;
}

@Injectable({
  providedIn: 'root'
})
export class AuthService {
  private apiUrl = 'http://localhost:8000/api'; // Adjust port if running on a different port

  // Angular Signals for state management
  currentUserSignal = signal<Usuario | null>(null);
  isAuthenticated = computed(() => this.currentUserSignal() !== null);

  constructor(private http: HttpClient) {
    this.loadStoredUser();
  }

  hasOption(optionCode: string): boolean {
    const user = this.currentUserSignal();
    return user?.opciones ? user.opciones.includes(optionCode) : false;
  }

  private loadStoredUser() {
    const storedUser = localStorage.getItem('auth_user');
    const token = localStorage.getItem('auth_token');
    
    if (storedUser && token) {
      try {
        this.currentUserSignal.set(JSON.parse(storedUser));
      } catch (e) {
        this.clearAuth();
      }
    }
  }

  login(nombre_usuario: string, contrasena: string): Observable<LoginResponse> {
    return this.http.post<LoginResponse>(`${this.apiUrl}/auth/login`, {
      nombre_usuario,
      contrasena
    }).pipe(
      tap(response => {
        this.setAuth(response.token, response.user);
      })
    );
  }

  register(userData: any): Observable<any> {
    return this.http.post<any>(`${this.apiUrl}/auth/register`, userData).pipe(
      tap(response => {
        if (response.token && response.user) {
          this.setAuth(response.token, response.user);
        }
      })
    );
  }

  logout(): Observable<any> {
    return this.http.post(`${this.apiUrl}/auth/logout`, {}).pipe(
      tap({
        finalize: () => {
          this.clearAuth();
        }
      })
    );
  }

  getProfile(): Observable<Usuario> {
    return this.http.get<Usuario>(`${this.apiUrl}/auth/profile`).pipe(
      tap(user => {
        this.currentUserSignal.set(user);
        localStorage.setItem('auth_user', JSON.stringify(user));
      })
    );
  }

  private setAuth(token: string, user: Usuario) {
    localStorage.setItem('auth_token', token);
    localStorage.setItem('auth_user', JSON.stringify(user));
    this.currentUserSignal.set(user);
  }

  private clearAuth() {
    localStorage.removeItem('auth_token');
    localStorage.removeItem('auth_user');
    this.currentUserSignal.set(null);
  }
}
