import { Component } from '@angular/core';
import { CommonModule } from '@angular/common';

@Component({
  selector: 'app-footer',
  standalone: true,
  imports: [CommonModule],
  template: `
    <footer class="ldcr-footer">

      <!-- Main footer content -->
      <div class="footer-main">
        <div class="footer-grid">

          <!-- Logo + info -->
          <div class="footer-brand">
            <img src="/img/logoX_LDCR.png" alt="Logo LDCR" class="footer-logo"
                 onerror="this.style.display='none'" />
            <h3 class="footer-name">Liga Cantonal Rumiñahui</h3>
            <p class="footer-slogan">Liderando, administrando y fomentando el deporte formativo desde 1940.</p>
          </div>

          <!-- Dirección + Contacto -->
          <div class="footer-col">
            <h4>
              <span class="material-symbols-outlined">location_on</span>
              Dirección
            </h4>
            <p>Espejo 176 y Eloy Alfaro<br />Sangolquí 171103, Ecuador</p>

            <h4 class="mt">
              <span class="material-symbols-outlined">share</span>
              Contacto
            </h4>
            <div class="social-row">
              <a href="https://api.whatsapp.com/send?phone=593984529759" target="_blank" class="social-icon whatsapp" title="WhatsApp">
                <span class="material-symbols-outlined">chat</span>
              </a>
              <a href="https://www.facebook.com/liga.ruminahui" target="_blank" class="social-icon facebook" title="Facebook">
                <span class="material-symbols-outlined">thumb_up</span>
              </a>
              <a href="https://twitter.com/ldc_ruminahui?lang=es" target="_blank" class="social-icon twitter" title="Twitter / X">
                <span class="material-symbols-outlined">alternate_email</span>
              </a>
              <a href="https://www.instagram.com/ldc_ruminahui/?hl=es-la" target="_blank" class="social-icon instagram" title="Instagram">
                <span class="material-symbols-outlined">photo_camera</span>
              </a>
            </div>
          </div>

          <!-- Mapa -->
          <div class="footer-col map-col">
            <h4>
              <span class="material-symbols-outlined">map</span>
              Ubicación
            </h4>
            <div class="footer-map">
              <iframe
                src="https://www.google.com/maps/embed?pb=!1m17!1m12!1m3!1d3989.7525841707093!2d-78.44837492503538!3d-0.3288999996678666!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m2!1m1!2zMMKwMTknNDQuMCJTIDc4wrAyNic0NC45Ilc!5e0!3m2!1ses-419!2sec!4v1712551709959!5m2!1ses-419!2sec"
                allowfullscreen loading="lazy" referrerpolicy="no-referrer-when-downgrade">
              </iframe>
            </div>
          </div>

        </div>
      </div>

      <!-- Divider -->
      <div class="footer-divider"></div>

      <!-- Créditos -->
      <div class="footer-credits">
        <p class="credits-label">
          <span class="material-symbols-outlined">code</span>
          Página desarrollada por:
        </p>
        <div class="dev-row">
          <div class="dev-card">
            <span class="material-symbols-outlined dev-icon">person</span>
            <div class="dev-info">
              <strong>Gino Bermúdez</strong>
              <div class="dev-links">
                <a href="https://api.whatsapp.com/send?phone=593978678671" target="_blank" title="WhatsApp">
                  <span class="material-symbols-outlined">chat</span>
                </a>
                <a href="https://www.facebook.com/gino.bermudez.902" target="_blank" title="Facebook">
                  <span class="material-symbols-outlined">thumb_up</span>
                </a>
                <a href="https://www.linkedin.com/in/gino-berm%C3%BAdez-santos-985599227" target="_blank" title="LinkedIn">
                  <span class="material-symbols-outlined">work</span>
                </a>
                <a href="https://github.com/ginobermudez76" target="_blank" title="GitHub">
                  <span class="material-symbols-outlined">terminal</span>
                </a>
              </div>
            </div>
          </div>

          <div class="dev-card">
            <span class="material-symbols-outlined dev-icon">person</span>
            <div class="dev-info">
              <strong>Daniel Vizcaíno Chanataxi</strong>
              <div class="dev-links">
                <a href="https://api.whatsapp.com/send?phone=593978822207" target="_blank" title="WhatsApp">
                  <span class="material-symbols-outlined">chat</span>
                </a>
                <a href="https://www.facebook.com/DaniElFox55/" target="_blank" title="Facebook">
                  <span class="material-symbols-outlined">thumb_up</span>
                </a>
                <a href="https://www.linkedin.com/in/daniel-vizca%C3%ADno-195153175/" target="_blank" title="LinkedIn">
                  <span class="material-symbols-outlined">work</span>
                </a>
                <a href="https://github.com/DanielVizcainoISTER" target="_blank" title="GitHub">
                  <span class="material-symbols-outlined">terminal</span>
                </a>
              </div>
            </div>
          </div>
        </div>

        <!-- Institución -->
        <div class="instituto-row">
          <a href="https://ister.edu.ec/" target="_blank" class="instituto-link" title="ISTER">
            <img src="/img/RU.webp" alt="ISTER" onerror="this.style.display='none'" />
          </a>
          <a href="https://ister.edu.ec/desarrollo-de-software/" target="_blank" class="instituto-link" title="Desarrollo de Software">
            <img src="/img/s.webp" onerror="this.src='/img/s.png'" alt="Desarrollo de software" />
          </a>
        </div>
      </div>

      <!-- Bottom bar -->
      <div class="footer-bottom">
        <p>© 2024 Liga Cantonal Rumiñahui — Todos los derechos reservados.</p>
      </div>

    </footer>
  `,
  styles: [`
    :host { display: block; }

    .ldcr-footer {
      background: linear-gradient(to bottom, #07003a, #030022);
      border-top: 3px solid #0fc3c6;
      color: #fff;
      font-family: 'Poppins', sans-serif;
    }

    /* ─── Main ─── */
    .footer-main { padding: 48px 60px 32px; }
    .footer-grid {
      display: grid;
      grid-template-columns: 1.2fr 1fr 1.4fr;
      gap: 48px;
      max-width: 1200px;
      margin: 0 auto;
    }
    @media (max-width: 900px) { .footer-grid { grid-template-columns: 1fr; gap: 32px; } }

    /* Brand */
    .footer-brand { }
    .footer-logo { width: 80px; height: 80px; object-fit: contain; filter: drop-shadow(0 2px 6px rgba(0,0,0,0.4)); margin-bottom: 12px; display: block; }
    .footer-name { font-family: 'Pattaya', 'Lobster', cursive; font-size: 18px; color: #fff; margin: 0 0 8px; }
    .footer-slogan { font-size: 13px; color: rgba(255,255,255,0.55); line-height: 1.6; margin: 0; }

    /* Columns */
    .footer-col h4 {
      display: flex; align-items: center; gap: 8px;
      font-family: 'Bebas Neue', cursive; font-size: 16px; letter-spacing: 1.5px;
      color: #0fc3c6; margin: 0 0 12px;
    }
    .footer-col h4 .material-symbols-outlined { font-size: 18px; }
    .footer-col p { font-size: 14px; color: rgba(255,255,255,0.75); line-height: 1.7; margin: 0; }
    .footer-col .mt { margin-top: 24px; }

    /* Social */
    .social-row { display: flex; gap: 10px; flex-wrap: wrap; margin-top: 4px; }
    .social-icon {
      width: 40px; height: 40px; border-radius: 50%;
      display: flex; align-items: center; justify-content: center;
      text-decoration: none; transition: all 0.2s;
      border: 1px solid rgba(255,255,255,0.15);
    }
    .social-icon .material-symbols-outlined { font-size: 18px; }
    .social-icon:hover { transform: translateY(-3px); border-color: #0fc3c6; }
    .whatsapp { background: rgba(37,211,102,0.12); color: #25d366; }
    .whatsapp:hover { background: rgba(37,211,102,0.25); }
    .facebook { background: rgba(66,103,178,0.12); color: #4267B2; }
    .facebook:hover { background: rgba(66,103,178,0.25); }
    .twitter { background: rgba(255,255,255,0.06); color: rgba(255,255,255,0.8); }
    .twitter:hover { background: rgba(255,255,255,0.12); }
    .instagram { background: rgba(225,48,108,0.1); color: #e1306c; }
    .instagram:hover { background: rgba(225,48,108,0.22); }

    /* Map */
    .map-col { }
    .footer-map {
      position: relative; padding-bottom: 52%; height: 0;
      border-radius: 10px; overflow: hidden;
      border: 1px solid rgba(15,195,198,0.25);
    }
    .footer-map iframe { position: absolute; inset: 0; width: 100%; height: 100%; border: 0; }

    /* Divider */
    .footer-divider { height: 1px; background: rgba(15,195,198,0.25); margin: 0 60px; }

    /* Credits */
    .footer-credits { padding: 28px 60px; }
    .credits-label {
      display: flex; align-items: center; gap: 8px;
      font-size: 13px; color: rgba(255,255,255,0.5);
      margin: 0 0 16px; text-transform: uppercase; letter-spacing: 1px;
    }
    .credits-label .material-symbols-outlined { font-size: 16px; color: #0fc3c6; }

    .dev-row { display: flex; gap: 24px; flex-wrap: wrap; margin-bottom: 24px; }
    .dev-card {
      display: flex; align-items: center; gap: 12px;
      background: rgba(255,255,255,0.04); border: 1px solid rgba(15,195,198,0.15);
      border-radius: 10px; padding: 12px 16px; flex: 1; min-width: 220px;
    }
    .dev-icon { font-size: 36px; color: #0fc3c6; flex-shrink: 0; }
    .dev-info strong { display: block; font-size: 13px; color: #fff; margin-bottom: 6px; }
    .dev-links { display: flex; gap: 8px; }
    .dev-links a {
      color: rgba(255,255,255,0.5); text-decoration: none; transition: color 0.2s;
      display: flex; align-items: center;
    }
    .dev-links a .material-symbols-outlined { font-size: 16px; }
    .dev-links a:hover { color: #0fc3c6; }

    /* Institución */
    .instituto-row { display: flex; align-items: center; justify-content: center; gap: 20px; }
    .instituto-link img { width: 60px; height: 60px; object-fit: contain; filter: brightness(0.9); transition: filter 0.2s; }
    .instituto-link:hover img { filter: brightness(1.1); }

    /* Bottom */
    .footer-bottom {
      background: rgba(0,0,0,0.3);
      text-align: center; padding: 14px;
      border-top: 1px solid rgba(255,255,255,0.06);
    }
    .footer-bottom p { font-size: 12px; color: rgba(255,255,255,0.35); margin: 0; }
  `]
})
export class FooterComponent {}
