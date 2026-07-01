import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';

export interface RbacEndpoint {
  id: number;
  uuid: string;
  nombre_endpoint: string;
  metodo: string;
  url: string;
  rbac_enabled: boolean;
}

export interface RbacOption {
  id: number;
  uuid: string;
  nombre_opcion: string;
  descripcion?: string;
  endpoints?: RbacEndpoint[];
}

export interface RbacRole {
  id: number;
  uuid: string;
  codigo: string;
  nombre_rol: string;
  descripcion?: string;
  opciones?: RbacOption[];
}

@Injectable({
  providedIn: 'root'
})
export class RbacAdminService {
  private baseApiUrl = 'http://localhost:8000/api/rbac';

  constructor(private http: HttpClient) {}

  // ==========================================
  // ROLES MANAGEMENT
  // ==========================================

  getRoles(): Observable<RbacRole[]> {
    return this.http.get<RbacRole[]>(`${this.baseApiUrl}/roles`);
  }

  createRole(data: any): Observable<RbacRole> {
    return this.http.post<RbacRole>(`${this.baseApiUrl}/roles`, data);
  }

  updateRole(id: number, data: any): Observable<RbacRole> {
    return this.http.put<RbacRole>(`${this.baseApiUrl}/roles/${id}`, data);
  }

  deleteRole(id: number): Observable<any> {
    return this.http.delete<any>(`${this.baseApiUrl}/roles/${id}`);
  }

  syncRoleOptions(roleId: number, optionIds: number[]): Observable<any> {
    return this.http.post<any>(`${this.baseApiUrl}/roles/${roleId}/opciones`, { option_ids: optionIds });
  }

  // ==========================================
  // OPTIONS MANAGEMENT
  // ==========================================

  getOptions(): Observable<RbacOption[]> {
    return this.http.get<RbacOption[]>(`${this.baseApiUrl}/opciones`);
  }

  createOption(data: any): Observable<RbacOption> {
    return this.http.post<RbacOption>(`${this.baseApiUrl}/opciones`, data);
  }

  updateOption(id: number, data: any): Observable<RbacOption> {
    return this.http.put<RbacOption>(`${this.baseApiUrl}/opciones/${id}`, data);
  }

  deleteOption(id: number): Observable<any> {
    return this.http.delete<any>(`${this.baseApiUrl}/opciones/${id}`);
  }

  syncOptionEndpoints(optionId: number, endpointIds: number[]): Observable<any> {
    return this.http.post<any>(`${this.baseApiUrl}/opciones/${optionId}/endpoints`, { endpoint_ids: endpointIds });
  }

  // ==========================================
  // ENDPOINTS MANAGEMENT
  // ==========================================

  getEndpoints(): Observable<RbacEndpoint[]> {
    return this.http.get<RbacEndpoint[]>(`${this.baseApiUrl}/endpoints`);
  }

  createEndpoint(data: any): Observable<RbacEndpoint> {
    return this.http.post<RbacEndpoint>(`${this.baseApiUrl}/endpoints`, data);
  }

  updateEndpoint(id: number, data: any): Observable<RbacEndpoint> {
    return this.http.put<RbacEndpoint>(`${this.baseApiUrl}/endpoints/${id}`, data);
  }

  deleteEndpoint(id: number): Observable<any> {
    return this.http.delete<any>(`${this.baseApiUrl}/endpoints/${id}`);
  }
}
