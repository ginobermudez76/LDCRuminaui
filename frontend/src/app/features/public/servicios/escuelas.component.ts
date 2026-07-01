import { Component, OnInit, signal, inject } from '@angular/core';
import { CommonModule } from '@angular/common';
import { HttpClient } from '@angular/common/http';
import { NavbarComponent } from '../../../shared/components/navbar/navbar.component';
import { FooterComponent } from '../../../shared/components/footer/footer.component';

const API = 'http://localhost:8000/api';

@Component({
  selector: 'app-servicios-escuelas',
  standalone: true,
  imports: [CommonModule, NavbarComponent, FooterComponent],
  templateUrl: './escuelas.component.html',
  styleUrl: './escuelas.component.css'
})
export class ServiciosEscuelasComponent implements OnInit {
  private http = inject(HttpClient);
  items = signal<any[]>([]);
  ngOnInit() { this.http.get<any[]>(`${API}/deportes`).subscribe(d => this.items.set(d)); }
}
