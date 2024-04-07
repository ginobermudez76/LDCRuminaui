<?php
include '../includes/header.php';
include '../includes/config.php';

if (isset($_GET['tipo'])) {
    $tipo = $_GET['tipo'];
}
?>
<style>
    .nosotros-container {
        text-align: center;
        /* Alinea el texto en el centro */
    }

    .nosotros-container img {
        width: 90%;
        display: block;
        margin: 0 auto;
    }

    .historia-info,
    .mision-info,
    .vision-info {
        width: 95%;
        color: #FFF;
        margin: 0 auto;
        /* Centra horizontalmente */
        text-align: left;
        /* Restablece la alineación del texto a la izquierda */
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        padding: 20px;
    }

    .nosotros-container h5 {
        font-size: 30px;
    }

    .nosotros-container p {
        font-size: 15px;
    }

    /* Añade el estilo para el efecto de oscurecimiento */
    .imagen-oscura {
        position: relative;
    }

    .imagen-oscura::before {
        content: "";
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.4);
        /* Cambia el valor alpha (0.5) para ajustar la opacidad */
    }

    .imagen-oscura img {
        display: block;
        width: 100%;
        height: auto;
    }
</style>
<div class="nosotros-container">
    <?php if ($tipo === "historia") : ?>
        <div class="imagen-oscura"> <!-- Agrega la clase imagen-oscura al contenedor de la imagen -->
            <img src="../img/portada.png" alt="nuestra historia">
            <div class="historia-info">
                <h5>HISTORIA</h5>
                <p>El 6 de julio de 1940 con la presencia de los delegados de los clubes filiales fundadores: Welcome, Colombia, Círculo Deportivo Sangolquí, Pichincha, Flecha Roja, Chacarita, Juan de Salinas y Municipal, decidieron constituirse en asamblea general y después de deliberar sobre la importancia de crear una organización que agrupe a todas las actividades deportivas e impulse otras, procedieron a crear la Concentración Deportiva Cantonal de Rumiñahui.</p>
                <p>EL PRIMER DIRECTORIO ESTUVO CONFORMADO POR;</p>
                <p>PRESIDENTE: Sr. Ernesto Recalde (concejal, miembro de la Comisión de Deportes del cantón Rumiñahui).</p>
                <p>VICEPRESIDENTE: Sr. Humberto Tinta</p>
                <p>SECRETARIO: SR. Isaías Figueroa</p>
                <p>TESORERO: SR. Efraín Carrera</p>
            </div>

        </div>
    <?php elseif ($tipo === "mision") : ?>
        <div class="imagen-oscura"> <!-- Agrega la clase imagen-oscura al contenedor de la imagen -->
            <img src="../img/misiondeportiva.jpg" alt="Misión">
            <div class="mision-info">
                <h5>MISIÓN</h5>
                <p>Somos la institución rectora, que lidera, administra, fomenta y desarrolla el deporte formativo en el cantón Rumiñahui. Dentro de un ambiente que promueve los valores éticos, morales y el mejoramiento continuo.</p>
                <p>Mejorando la calidad de vida de la comunidad con inclusión social.</p>
            </div>

        </div>
    <?php elseif ($tipo === "vision") : ?>
        <div class="imagen-oscura"> <!-- Agrega la clase imagen-oscura al contenedor de la imagen -->
            <img src="../img/visiondeportiva.jpg" alt="Visión">
            <div class="vision-info">
                <h5>VISIÓN</h5>
                <p>
                    Ser la primera potencia deportiva de la provincia, cuya prioridad es promover deportistas de alta calidad a las selecciones provinciales y nacionales por deportes, a través de la preparación y formación integral de las/los atletas.</p>
            </div>

        </div>

    <?php elseif ($tipo === "directorio") : ?>
        <style>
            .linea {
                background-color: #0fc3c6;
                width: 100%;
                height: 5px;
            }

            .directorio-container {
                color: rgb(255, 255, 255);
                height: 1000px;
                width: 100%;
            }

            .directorio-container img {
                height: 30%;
                width: 30%;
                border: 5px;
                padding: 5px;
                background-color: #0fc3c6;
            }

            .dirigencia {
                height: 50%;
            }
            .presidente{
                height: 50%;
            }
            .presidente img{
                height: 60%;
            }
        </style>
        <div class="directorio-container">
            <div class="dirigencia">
                <h5>DIRIGENCIA</h5>
                <img src="../img/medalla.jpg" alt="dirigencia">
                <p>PRESIDENTE:</p>

                <p>VICEPRESIDENTE:</p>

                <p>SECRETARIO:</p>

                <p>TESORERO:</p>
                <div class="linea"></div>
            </div>
            <div class="presidente">
                <h5>PRESIDENTE</h5>
                <img src="../img/medalla.jpg" alt="Presidente">
                <p></p>
            </div>
        </div>

    <?php endif; ?>
</div>

<?php
include '../includes/footer.php';
?>