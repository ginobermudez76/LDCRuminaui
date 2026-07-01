import { Component, OnInit, signal, inject } from '@angular/core';
import { CommonModule } from '@angular/common';
import { HttpClient } from '@angular/common/http';
import { NavbarComponent } from '../../../shared/components/navbar/navbar.component';
import { FooterComponent } from '../../../shared/components/footer/footer.component';

const API = 'http://localhost:8000/api';

@Component({
  selector: 'app-documentos-publicos',
  standalone: true,
  imports: [CommonModule, NavbarComponent, FooterComponent],
  templateUrl: './documentos.component.html',
  styleUrl: './documentos.component.css'
})
export class DocumentosPublicosComponent implements OnInit {
  private http = inject(HttpClient);
  items = signal<any[]>([]);
  ngOnInit() { this.http.get<any[]>(`${API}/documentos`).subscribe(d => this.items.set(d)); }
}
