<footer>
  <div class="footer-container">
    <div class="row">
      <div class="col-md-6">
        <div class="contacto">
          <h3>Dirección</h3>
          <p>Dirección: Espejo 176 y Eloy Alfaro, Sangolquí 171103, Ecuador</p>
          <div class="col-md-6">
            <h3>Contacto</h3>
            <a href="https://api.whatsapp.com/send?phone=593984529759">
              <img src="../img/whatsapp.png" alt="Facebook"></a>

            <a href="https://www.facebook.com/liga.ruminahui">
              <img src="../img/facebook.png" alt="Facebook">
            </a>

            <a href="https://twitter.com/ldc_ruminahui?lang=es">
              <img src="../img/X.png" alt="Twitter">
            </a>

            <a href="https://www.instagram.com/ldc_ruminahui/?hl=es-la">
              <img src="../img/instagram.png" alt="Instagram">
            </a>
          </div>
        </div>

      </div>
      <div class="col-md-6">
        <div class="ubicacion">
          <h3>Ubicación</h3>
          <div class="map-container">
          <iframe src="https://www.google.com/maps/embed?pb=!1m17!1m12!1m3!1d3989.7525841707093!2d-78.44837492503538!3d-0.3288999996678666!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m2!1m1!2zMMKwMTknNDQuMCJTIDc4wrAyNic0NC45Ilc!5e0!3m2!1ses-419!2sec!4v1712551709959!5m2!1ses-419!2sec" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
          </div>
        </div>
      </div>
    </div>
  </div>
  <h3 class="text-center mb-0">
    
    <?php

    if(!isset($_SESSION['usuario_admin'])){ ?>
    Pagina desarrollada por:
    <?php
    }else{ ?>
      Sistema de solicitudes desarrollado por:
    <?php
    }
    ?>
  </h3>
  <div class="desarrollador-container">
    <?php
    if (!isset($_SESSION['usuario_admin'])) { ?>
      <p class="text-center mb-0">
       Gino Bermúdez Santos
      </p>
      <div class="info-desarrollador">
        <a href="https://api.whatsapp.com/send?phone=593978678671">
          <img src="../img/whatsapp.png" alt="Facebook">
        </a>
        <a href="https://www.facebook.com/gino.bermudez.902">
          <img src="../img/facebook.png" alt="Facebook">
        </a>
        <a href="https://www.linkedin.com/in/gino-berm%C3%BAdez-santos-985599227">
          <img src="../img/linkedin.png" alt="Linkedin">
        </a>
        <a href="https://github.com/ginobermudez76">
          <img src="../img/github.png" alt="GitHub">
        </a>
      </div>
    <?php
    } else { ?>
      <div class="gino">
        <p class="text-center mb-0">
          Gino Bermúdez Santos
        </p>
        <div class="info-desarrollador">
          <a href="https://api.whatsapp.com/send?phone=593978678671">
            <img src="../img/whatsapp.png" alt="Facebook">
          </a>
          <a href="https://www.facebook.com/gino.bermudez.902">
            <img src="../img/facebook.png" alt="Facebook">
          </a>
          <a href="https://www.linkedin.com/in/gino-berm%C3%BAdez-santos-985599227">
            <img src="../img/linkedin.png" alt="Linkedin">
          </a>
          <a href="https://github.com/ginobermudez76">
            <img src="../img/github.png" alt="GitHub">
          </a>
        </div>
      </div>
      <div class="daniel">
        <p class="text-center mb-0">
          Daniel Vizcaíno Chanataxi
        </p>
        <div class="info-desarrollador"">
          <a href="https://api.whatsapp.com/send?phone=593978822207">
            <img src="../img/whatsapp.png" alt="Facebook">
          </a>
          <a href="https://www.facebook.com/DaniElFox55/">
            <img src="../img/facebook.png" alt="Facebook">
          </a>
          <a href="https://www.linkedin.com/in/daniel-vizca%C3%ADno-195153175/">
            <img src="../img/linkedin.png" alt="Linkedin">
          </a>
          <a href="https://github.com/DanielVizcainoISTER">
            <img src="../img/github.png" alt="GitHub">
          </a>
        </div>
      </div>

    <?php
    }
    ?>
  </div>

</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>

</body>

</html>
