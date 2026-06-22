import { Component } from '@angular/core';
import { CommonModule } from '@angular/common';
import { NavbarComponent } from '../../../shared/components/navbar/navbar.component';

@Component({
  selector: 'app-contacto',
  standalone: true,
  imports: [CommonModule, NavbarComponent],
  template: `
    <app-navbar></app-navbar>
    <div class="contacto-page">
      <div class="page-hero">
        <div class="hero-title-row">
          <span class="material-symbols-outlined hero-icon">mail</span>
          <h1>Contáctanos</h1>
        </div>
        <div class="hero-line"></div>
      </div>

      <div class="content-wrap">
        <div class="contact-grid">
          <!-- Info -->
          <div class="info-col">
            <div class="info-card">
              <span class="material-symbols-outlined info-icon">location_on</span>
              <h3>Dirección</h3>
              <p>Espejo 176 y Eloy Alfaro, Sangolquí 171103, Ecuador</p>
            </div>

            <div class="info-card">
              <span class="material-symbols-outlined info-icon">share</span>
              <h3>Redes Sociales</h3>
              <div class="social-links">
                <a href="https://api.whatsapp.com/send?phone=593984529759" target="_blank" class="social-btn whatsapp">
                  <span class="material-symbols-outlined">chat</span>
                  WhatsApp
                </a>
                <a href="https://www.facebook.com/liga.ruminahui" target="_blank" class="social-btn facebook">
                  <span class="material-symbols-outlined">thumb_up</span>
                  Facebook
                </a>
                <a href="https://twitter.com/ldc_ruminahui?lang=es" target="_blank" class="social-btn twitter">
                  <span class="material-symbols-outlined">alternate_email</span>
                  Twitter / X
                </a>
                <a href="https://www.instagram.com/ldc_ruminahui/?hl=es-la" target="_blank" class="social-btn instagram">
                  <span class="material-symbols-outlined">photo_camera</span>
                  Instagram
                </a>
              </div>
            </div>
          </div>

          <!-- Map -->
          <div class="map-col">
            <h3>Ubicación</h3>
            <div class="map-wrap">
              <iframe
                src="https://www.google.com/maps/embed?pb=!1m17!1m12!1m3!1d3989.7525841707093!2d-78.44837492503538!3d-0.3288999996678666!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m2!1m1!2zMMKwMTknNDQuMCJTIDc4wrAyNic0NC45Ilc!5e0!3m2!1ses-419!2sec!4v1712551709959!5m2!1ses-419!2sec"
                allowfullscreen loading="lazy" referrerpolicy="no-referrer-when-downgrade">
              </iframe>
            </div>
          </div>
        </div>
      </div>
    </div>
  `,
  styles: [`
    .contacto-page { min-height: 100vh; background: linear-gradient(to bottom, #030022, #11637c); color: #fff; }
    .page-hero { padding: 48px 60px 32px; border-bottom: 2px solid rgba(15,195,198,0.3); }
    .hero-title-row { display: flex; align-items: center; gap: 16px; }
    .hero-icon { font-size: 48px; color: #0fc3c6; }
    h1 { font-family: 'Lobster', 'Pattaya', cursive; font-size: 48px; margin: 0; }
    .hero-line { width: 80px; height: 5px; background: #0fc3c6; margin-top: 12px; border-radius: 2px; }
    .content-wrap { padding: 48px 60px; max-width: 1200px; margin: 0 auto; }
    .contact-grid { display: grid; grid-template-columns: 1fr 1.5fr; gap: 40px; }
    @media (max-width: 768px) { .contact-grid { grid-template-columns: 1fr; } }
    .info-col { display: flex; flex-direction: column; gap: 24px; }
    .info-card { background: rgba(255,255,255,0.06); border: 1px solid rgba(15,195,198,0.25); border-radius: 12px; padding: 28px; }
    .info-icon { font-size: 36px; color: #0fc3c6; display: block; margin-bottom: 12px; }
    .info-card h3 { font-family: 'Bebas Neue', cursive; font-size: 22px; color: #0fc3c6; margin: 0 0 10px; letter-spacing: 1px; }
    .info-card p { font-size: 15px; color: rgba(255,255,255,0.8); margin: 0; }
    .social-links { display: flex; flex-direction: column; gap: 10px; margin-top: 8px; }
    .social-btn { display: flex; align-items: center; gap: 10px; padding: 10px 16px; border-radius: 8px; font-size: 14px; font-weight: 600; text-decoration: none; transition: all 0.2s; color: #fff; }
    .social-btn .material-symbols-outlined { font-size: 20px; }
    .whatsapp { background: rgba(37,211,102,0.15); border: 1px solid rgba(37,211,102,0.3); }
    .whatsapp:hover { background: rgba(37,211,102,0.3); }
    .facebook { background: rgba(66,103,178,0.15); border: 1px solid rgba(66,103,178,0.3); }
    .facebook:hover { background: rgba(66,103,178,0.3); }
    .twitter { background: rgba(255,255,255,0.07); border: 1px solid rgba(255,255,255,0.15); }
    .twitter:hover { background: rgba(255,255,255,0.15); }
    .instagram { background: rgba(225,48,108,0.12); border: 1px solid rgba(225,48,108,0.25); }
    .instagram:hover { background: rgba(225,48,108,0.25); }
    .map-col h3 { font-family: 'Bebas Neue', cursive; font-size: 22px; color: #0fc3c6; margin: 0 0 16px; letter-spacing: 1px; }
    .map-wrap { position: relative; padding-bottom: 56.25%; height: 0; border-radius: 12px; overflow: hidden; border: 1px solid rgba(15,195,198,0.3); }
    .map-wrap iframe { position: absolute; inset: 0; width: 100%; height: 100%; border: 0; }
  `]
})
export class ContactoComponent {}
