import { Component, OnInit, signal, inject } from '@angular/core';
import { CommonModule } from '@angular/common';
import { HttpClient } from '@angular/common/http';
import { NavbarComponent } from '../../../shared/components/navbar/navbar.component';
import { FooterComponent } from '../../../shared/components/footer/footer.component';

const API = 'http://localhost:8000/api';

@Component({
  selector: 'app-vacacionales',
  standalone: true,
  imports: [CommonModule, NavbarComponent, FooterComponent],
  templateUrl: './vacacionales.component.html',
  styleUrl: './vacacionales.component.css'
})
export class VacacionalesComponent implements OnInit {
  private http = inject(HttpClient);
  items = signal<any[]>([]);
  ngOnInit() { this.http.get<any[]>(`${API}/cursos`).subscribe(d => this.items.set(d)); }
  getEstadoClass(e: string) { return e?.toLowerCase().replace(' ', '-') ?? ''; }
  getInscripcionClass(i: string) { return i === 'Abiertas' ? 'inscripcion-badge abiertas' : 'inscripcion-badge cerradas'; }
}
