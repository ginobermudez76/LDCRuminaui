import { Component, OnInit, signal, inject } from '@angular/core';
import { CommonModule } from '@angular/common';
import { HttpClient } from '@angular/common/http';
import { NavbarComponent } from '../../../shared/components/navbar/navbar.component';
import { FooterComponent } from '../../../shared/components/footer/footer.component';

const API = 'http://localhost:8000/api';

@Component({
  selector: 'app-noticias',
  standalone: true,
  imports: [CommonModule, NavbarComponent, FooterComponent],
  templateUrl: './noticias.component.html',
  styleUrl: './noticias.component.css',
})
export class NoticiasComponent implements OnInit {
  private readonly http = inject(HttpClient);
  items = signal<any[]>([]);
  ngOnInit() {
    // Intentar cargar desde API si existen, si no mostrar vacío
    try {
      this.http
        .get<any[]>(`${API}/noticias`)
        .subscribe({ next: (d) => this.items.set(d), error: () => {} });
    } catch {}
  }
}
