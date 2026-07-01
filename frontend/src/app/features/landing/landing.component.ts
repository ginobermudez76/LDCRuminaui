import { Component, OnInit, signal, inject, OnDestroy, computed } from '@angular/core';
import { CommonModule } from '@angular/common';
import { PublicistaService, Logro, DeportistaDestacado, Deporte, Evento, CartaCondolencia } from '../../core/services/publicista.service';
import { NavbarComponent } from '../../shared/components/navbar/navbar.component';
import { FooterComponent } from '../../shared/components/footer/footer.component';

interface Noticia {
  id: number;
  titulo: string;
  imagen?: string;
  cuerpo?: string;
}

@Component({
  selector: 'app-landing',
  standalone: true,
  imports: [CommonModule, NavbarComponent, FooterComponent],
  templateUrl: './landing.component.html',
  styleUrl: './landing.component.css'
})
export class LandingComponent implements OnInit, OnDestroy {
  private svc = inject(PublicistaService);

  deportesGrupos = computed(() => {
    const arr = this.deportes();
    const chunked = [];
    for (let i = 0; i < arr.length; i += 4) {
      chunked.push(arr.slice(i, i + 4));
    }
    if (chunked.length > 1) {
      chunked.push(chunked[0]);
    }
    return chunked;
  });

  isTransitioning = signal(true);

  logros = signal<Logro[]>([]);
  deportistas = signal<DeportistaDestacado[]>([]);
  deportes = signal<Deporte[]>([]);
  eventos = signal<Evento[]>([]);
  cartas = signal<CartaCondolencia[]>([]);

  showCondolencias = signal(false);
  cartaIdx = signal(0);
  deportistaIdx = signal(0);

  deportistasIzq = signal<DeportistaDestacado[]>([]);
  deportistasDer = signal<DeportistaDestacado[]>([]);

  idxIzq = signal(0);
  idxDer = signal(0);
  idxEscuela = signal(0);
  private izqInterval: any;
  private derInterval: any;
  private escuelaInterval: any;
  private cartaInterval: any;

  ngOnInit() {
    this.svc.getLogros().subscribe(d => this.logros.set(d));
    this.svc.getDeportistas().subscribe(d => {
      this.deportistas.set(d);
      // Split items alternating (odds and evens)
      this.deportistasIzq.set(d.filter((_, i) => i % 2 !== 0));
      this.deportistasDer.set(d.filter((_, i) => i % 2 === 0));
    });
    this.svc.getDeportes().subscribe(d => this.deportes.set(d));
    this.svc.getEventos().subscribe(d => this.eventos.set(d.slice(0, 6)));
    this.svc.getCartas().subscribe(d => {
      this.cartas.set(d);
      if (d.length > 0) this.showCondolencias.set(true);
    });

    if (typeof window !== 'undefined') {
      this.startAutoplay();
    }
  }

  ngOnDestroy() {
    this.stopAutoplay();
  }

  private startAutoplay() {
    this.startIzqAutoplay();
    this.startDerAutoplay();
    this.startEscuelaAutoplay();
    this.startCartaAutoplay();
  }

  private stopAutoplay() {
    if (this.izqInterval) clearInterval(this.izqInterval);
    if (this.derInterval) clearInterval(this.derInterval);
    if (this.escuelaInterval) clearInterval(this.escuelaInterval);
    if (this.cartaInterval) clearInterval(this.cartaInterval);
  }

  private startIzqAutoplay() {
    if (this.izqInterval) clearInterval(this.izqInterval);
    this.izqInterval = setInterval(() => this.nextIzq(), 4000);
  }

  private startDerAutoplay() {
    if (this.derInterval) clearInterval(this.derInterval);
    this.derInterval = setInterval(() => this.nextDer(), 4500);
  }

  private startEscuelaAutoplay() {
    if (this.escuelaInterval) clearInterval(this.escuelaInterval);
    this.escuelaInterval = setInterval(() => this.nextEscuela(), 5000);
  }

  private startCartaAutoplay() {
    if (this.cartaInterval) clearInterval(this.cartaInterval);
    this.cartaInterval = setInterval(() => this.nextCarta(), 6000);
  }

  closeCondolencias() { this.showCondolencias.set(false); }

  prevCarta() {
    const len = this.cartas().length;
    if (len > 0) {
      this.cartaIdx.update(i => (i - 1 + len) % len);
      this.startCartaAutoplay();
    }
  }
  nextCarta() {
    const len = this.cartas().length;
    if (len > 0) {
      this.cartaIdx.update(i => (i + 1) % len);
      this.startCartaAutoplay();
    }
  }

  prevIzq() {
    const len = this.deportistasIzq().length;
    if (len > 0) {
      this.idxIzq.update(i => (i - 1 + len) % len);
      this.startIzqAutoplay();
    }
  }
  nextIzq() {
    const len = this.deportistasIzq().length;
    if (len > 0) {
      this.idxIzq.update(i => (i + 1) % len);
      this.startIzqAutoplay();
    }
  }

  prevDer() {
    const len = this.deportistasDer().length;
    if (len > 0) {
      this.idxDer.update(i => (i - 1 + len) % len);
      this.startDerAutoplay();
    }
  }
  nextDer() {
    const len = this.deportistasDer().length;
    if (len > 0) {
      this.idxDer.update(i => (i + 1) % len);
      this.startDerAutoplay();
    }
  }

  prevEscuela() {
    const allLen = this.deportesGrupos().length;
    if (allLen <= 1) return;

    if (this.idxEscuela() === 0) {
      this.isTransitioning.set(false);
      this.idxEscuela.set(allLen - 1);
      setTimeout(() => {
        this.isTransitioning.set(true);
        this.idxEscuela.set(allLen - 2);
      }, 50);
    } else {
      if (!this.isTransitioning()) {
        this.isTransitioning.set(true);
        setTimeout(() => {
          this.idxEscuela.update(i => i - 1);
        }, 50);
      } else {
        this.idxEscuela.update(i => i - 1);
      }
    }
    this.startEscuelaAutoplay();
  }

  nextEscuela() {
    const allLen = this.deportesGrupos().length;
    if (allLen <= 1) return;

    if (!this.isTransitioning()) {
      this.isTransitioning.set(true);
      setTimeout(() => {
        this.idxEscuela.update(i => i + 1);
      }, 50);
    } else {
      this.idxEscuela.update(i => i + 1);
    }
    this.startEscuelaAutoplay();
  }

  onTransitionEnd() {
    const allLen = this.deportesGrupos().length;
    if (this.idxEscuela() >= allLen - 1) {
      this.isTransitioning.set(false);
      setTimeout(() => {
        this.idxEscuela.set(0);
      }, 50);
    }
  }
}
