import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';

const API = 'http://localhost:8000/api';

export interface Deporte {
  id?: number;
  uuid: string;
  codigo: string;
  nombre: string;
  descripcion?: string;
  imagen?: string;
}

export interface Evento {
  id?: number;
  uuid: string;
  codigo: string;
  nombre: string;
  fecha_inicio: string;
  fecha_fin: string;
  descripcion?: string;
  imagen?: string;
  deporte_id: number;
  estado?: string;
  inscripciones?: string;
  deporte?: Deporte;
}

export interface Logro {
  id?: number;
  uuid: string;
  codigo: string;
  titulo: string;
  tipologro?: string;
  deporte_id?: number;
  imagen?: string;
  deporte?: Deporte;
}

export interface Curso {
  id?: number;
  uuid: string;
  codigo: string;
  nombre: string;
  descripcion?: string;
  imagen?: string;
  fecha_inicio?: string;
  fecha_fin?: string;
  deporte_id?: number;
  estado?: string;
  inscripciones?: string;
}

export interface Documento {
  id?: number;
  uuid: string;
  codigo: string;
  nombre: string;
  descripcion?: string;
  documento?: string;
}

export interface DeportistaDestacado {
  id?: number;
  uuid: string;
  codigo: string;
  nombre_deportista: string;
  deporte_id?: number;
  imagen?: string;
  deporte?: Deporte;
}

export interface CartaCondolencia {
  id?: number;
  uuid: string;
  codigo: string;
  mensaje: string;
  imagen?: string;
  fecha_eliminar: string;
}

@Injectable({ providedIn: 'root' })
export class PublicistaService {
  constructor(private http: HttpClient) {}

  // Deportes
  getDeportes(): Observable<Deporte[]> {
    return this.http.get<Deporte[]>(`${API}/deportes`);
  }
  createDeporte(fd: FormData): Observable<Deporte> {
    return this.http.post<Deporte>(`${API}/deportes`, fd);
  }
  deleteDeporte(uuid: string): Observable<any> {
    return this.http.delete(`${API}/deportes/${uuid}`);
  }

  // Eventos
  getEventos(): Observable<Evento[]> {
    return this.http.get<Evento[]>(`${API}/eventos`);
  }
  createEvento(fd: FormData): Observable<Evento> {
    return this.http.post<Evento>(`${API}/eventos`, fd);
  }
  deleteEvento(uuid: string): Observable<any> {
    return this.http.delete(`${API}/eventos/${uuid}`);
  }

  // Logros
  getLogros(): Observable<Logro[]> {
    return this.http.get<Logro[]>(`${API}/logros`);
  }
  createLogro(fd: FormData): Observable<Logro> {
    return this.http.post<Logro>(`${API}/logros`, fd);
  }
  deleteLogro(uuid: string): Observable<any> {
    return this.http.delete(`${API}/logros/${uuid}`);
  }

  // Cursos
  getCursos(): Observable<Curso[]> {
    return this.http.get<Curso[]>(`${API}/cursos`);
  }
  createCurso(fd: FormData): Observable<Curso> {
    return this.http.post<Curso>(`${API}/cursos`, fd);
  }
  deleteCurso(uuid: string): Observable<any> {
    return this.http.delete(`${API}/cursos/${uuid}`);
  }

  // Documentos
  getDocumentos(): Observable<Documento[]> {
    return this.http.get<Documento[]>(`${API}/documentos`);
  }
  createDocumento(fd: FormData): Observable<Documento> {
    return this.http.post<Documento>(`${API}/documentos`, fd);
  }
  deleteDocumento(uuid: string): Observable<any> {
    return this.http.delete(`${API}/documentos/${uuid}`);
  }

  // Deportistas Destacados
  getDeportistas(): Observable<DeportistaDestacado[]> {
    return this.http.get<DeportistaDestacado[]>(`${API}/deportistas`);
  }
  createDeportista(fd: FormData): Observable<DeportistaDestacado> {
    return this.http.post<DeportistaDestacado>(`${API}/deportistas`, fd);
  }
  deleteDeportista(uuid: string): Observable<any> {
    return this.http.delete(`${API}/deportistas/${uuid}`);
  }

  // Cartas de Condolencia
  getCartas(): Observable<CartaCondolencia[]> {
    return this.http.get<CartaCondolencia[]>(`${API}/cartas`);
  }
  createCarta(fd: FormData): Observable<CartaCondolencia> {
    return this.http.post<CartaCondolencia>(`${API}/cartas`, fd);
  }
  deleteCarta(uuid: string): Observable<any> {
    return this.http.delete(`${API}/cartas/${uuid}`);
  }
}
