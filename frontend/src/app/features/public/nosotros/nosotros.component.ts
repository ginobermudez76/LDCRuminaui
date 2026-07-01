import { Component, OnInit, signal, inject } from '@angular/core';
import { CommonModule } from '@angular/common';
import { ActivatedRoute } from '@angular/router';
import { NavbarComponent } from '../../../shared/components/navbar/navbar.component';
import { FooterComponent } from '../../../shared/components/footer/footer.component';

@Component({
  selector: 'app-nosotros',
  standalone: true,
  imports: [CommonModule, NavbarComponent, FooterComponent],
  templateUrl: './nosotros.component.html',
  styleUrl: './nosotros.component.css'
})
export class NosotrosComponent implements OnInit {
  private route = inject(ActivatedRoute);
  tipo = signal('');
  labels: Record<string, string> = { historia: 'Historia', mision: 'Misión', vision: 'Visión', directorio: 'Directorio' };
  icons: Record<string, string> = { historia: 'history_edu', mision: 'flag', vision: 'visibility', directorio: 'groups' };

  ngOnInit() {
    this.route.queryParams.subscribe(p => this.tipo.set(p['tipo'] ?? ''));
  }
}
