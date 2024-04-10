<?php

include '../includes/config.php'; // Incluyendo la conexión a la base de datos
include '../includes/header.php'; // Incluyendo la cabecera común

try {
    // Llamar al procedimiento almacenado
    $stmt = $conn->prepare("CALL ObtenerInfoEventos()");
    $stmt->execute();
    $eventos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Cerrar el cursor después de obtener los resultados
    $stmt->closeCursor();
} catch (PDOException $e) {
    echo "Error al obtener la información de eventos: " . $e->getMessage();
}
try {
    $stmt = $conn->prepare("SELECT imagen, mensaje FROM carta_condolencias");
    $stmt->execute();
    $condolencias = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $stmt->closeCursor();
} catch (PDOException $e) {
    echo "Error al obtener carta de condolencias: " . $e->getMessage();
}
try {
    // Obtener datos de la tabla "deporte"
    $stmtDeportes = $conn->prepare("SELECT * FROM deportes");
    $stmtDeportes->execute();
    $deportes = $stmtDeportes->fetchAll(PDO::FETCH_ASSOC);

    // Cerrar el cursor después de obtener los resultados
    $stmtDeportes->closeCursor();
} catch (PDOException $e) {
    echo "Error al obtener la información de deportes: " . $e->getMessage();
}

?>

<div class="container-fluid">
    <div class="container">
        <img class="inicio-img" src="../img/portada.png" alt="¿Quienes somos?">
        <div class="banner-inicio">
            <h5 class="">Mas que una liga deportiva</h5>
            <svg class="titulo-inicio" data-name="Capa 1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 644.08 250.78">
                <defs>
                    <style>
                        .cls-1 {
                            fill: #e0c37e;
                        }
                    </style>
                </defs>
                <path class="cls-1" d="M89.39,122.33l7.54-71.66.13-1.58a10.59,10.59,0,0,0-2.51-8.6h22.21a12.2,12.2,0,0,0-4.37,8.6l-.13,1.58-6.87,64.92-10.84,6.74h10.17l-.26,1.72q-.66,5.42,2.64,8.59H78.15L63.74,122.33l7.54-71.66.13-1.58q.53-5.56-2.51-8.6H91.24a12.52,12.52,0,0,0-4.36,8.6l-.13,1.58-7.54,71.66Z" transform="translate(-59.12 -40.49)" />
                <path class="cls-1" d="M134.08,85.05l-4.1,39q-.67,5.42,2.77,8.59H110.41a12.77,12.77,0,0,0,4.23-8.59l7.8-73.38.13-1.58q.53-5.56-2.51-8.6H142.4a12,12,0,0,0-4.49,8.6l-.13,1.58-2.25,20.5,8.72,15.33,3.84-35.83.13-1.58a10.59,10.59,0,0,0-2.51-8.6h22.21a12.19,12.19,0,0,0-4.36,8.6l-.14,1.58-7.8,73.38q-.66,5.42,2.65,8.59H136.06a13.17,13.17,0,0,0,4.23-8.59l2.51-23.67Z" transform="translate(-59.12 -40.49)" />
                <path class="cls-1" d="M210.09,132.64H187.88a12.76,12.76,0,0,0,4.23-8.59q.39-3.18.86-8.07T194,105.8c.39-3.52.77-7,1.12-10.44s.66-6.35.93-8.73L190.59,90c-1.81,1.11-3.64,2.23-5.49,3.38q-.91,7.92-1.78,16.19t-1.52,14.48c-.36,3.61.53,6.47,2.64,8.59H162.23a12.05,12.05,0,0,0,4.23-8.59q1.86-16.8,3.57-33.25t3.44-33.25l11.1-6.74H174.26a5.11,5.11,0,0,1,.07-.93,5.27,5.27,0,0,0,.07-.79c.43-3.62-.4-6.48-2.52-8.6H201q6.87,5.16,14.27,10.32-2,18.38-3.9,36.55t-3.9,36.69Q206.92,129.47,210.09,132.64ZM197,77.38l2.91-26.57H189.6L186,84.12Z" transform="translate(-59.12 -40.49)" />
                <path class="cls-1" d="M274.6,122.46,276.72,102l16.39-10.31L293,93.38l-2.38,22.21-11,6.87h10.31l-.27,1.59q-.66,5.42,2.65,8.59H244.46a13.17,13.17,0,0,0,4.23-8.59l7-66.5,11.1-6.88H256.49l.13-1.58a10.55,10.55,0,0,0-2.51-8.6H302a12.23,12.23,0,0,0-4.37,8.6l-.13,1.58L296.28,61,279.89,71.17l1.45-13.62,11-6.88H271.83L265,115.59l-10.84,6.87Z" transform="translate(-59.12 -40.49)" />
                <path class="cls-1" d="M339.25,122.46l-16.39,10.18H312.54l-14.27-10.18,7.53-71.65,16.39-10.18h10.32l14.27,10.18Zm-7.8-71.65H321.14l-6.88,64.78-.66,6.87h10.31Z" transform="translate(-59.12 -40.49)" />
                <path class="cls-1" d="M421.21,49.09l-.13,1.72-7.8,73.24q-.66,5.55,2.64,8.59H393.71a12.78,12.78,0,0,0,4.24-8.59l.26-1.59,7.54-71.65H395.57l-7.8,73.24c-.45,3.79.44,6.65,2.64,8.59H368.07q3.57-2.91,4.23-8.59l7.8-73.24H369.92l-7.54,71.65-.26,1.59q-.66,5.55,2.64,8.59H342.55a12.77,12.77,0,0,0,4.23-8.59l7-66.37,11-6.87H354.58l.14-1.72q.52-5.3-2.52-8.46h29l7.27,5,8.19-5h29A12,12,0,0,0,421.21,49.09Z" transform="translate(-59.12 -40.49)" />
                <path class="cls-1" d="M449.9,122.33l7.53-71.66.14-1.58a10.56,10.56,0,0,0-2.52-8.6h22.21a12.22,12.22,0,0,0-4.36,8.6l-.13,1.58-6.87,64.92-10.85,6.74h10.18l-.26,1.72q-.66,5.42,2.64,8.59H438.66l-14.41-10.31,7.54-71.66.13-1.58q.52-5.56-2.51-8.6h22.34a12.52,12.52,0,0,0-4.36,8.6l-.14,1.58-7.53,71.66Z" transform="translate(-59.12 -40.49)" />
                <path class="cls-1" d="M494.58,85.05l-4.09,39q-.67,5.42,2.77,8.59H470.92a12.77,12.77,0,0,0,4.23-8.59L483,50.67l.13-1.58q.53-5.56-2.51-8.6h22.34a12,12,0,0,0-4.49,8.6l-.14,1.58L496,71.17l8.72,15.33,3.84-35.83.13-1.58a10.59,10.59,0,0,0-2.51-8.6h22.21a12.2,12.2,0,0,0-4.37,8.6l-.13,1.58-7.8,73.38q-.66,5.42,2.65,8.59H496.57a13.22,13.22,0,0,0,4.23-8.59l2.51-23.67Z" transform="translate(-59.12 -40.49)" />
                <path class="cls-1" d="M553.81,40.49a12.19,12.19,0,0,0-4.36,8.6l-.14,1.58-7.8,73.38q-.66,5.42,2.65,8.59H522a13.17,13.17,0,0,0,4.23-8.59L534,50.67l.13-1.58a10.59,10.59,0,0,0-2.51-8.6Z" transform="translate(-59.12 -40.49)" />
                <path class="cls-1" d="M600.08,50.67l-7.54,71.66-19.83,10.31H547.06a12.73,12.73,0,0,0,4.23-8.59l7-66.5,11.11-6.88H559.09l.14-1.58q.53-5.56-2.51-8.6H585.8Zm-22.87,71.66,7.53-71.66H574.43l-6.87,64.78-10.84,6.88Z" transform="translate(-59.12 -40.49)" />
                <path class="cls-1" d="M646.48,132.64H624.27a12.76,12.76,0,0,0,4.23-8.59c.27-2.12.55-4.81.86-8.07s.66-6.65,1.06-10.18.77-7,1.12-10.44.66-6.35.93-8.73Q629.69,88.36,627,90l-5.49,3.38q-.91,7.92-1.78,16.19t-1.52,14.48q-.53,5.42,2.64,8.59H598.62a12.05,12.05,0,0,0,4.23-8.59q1.86-16.8,3.57-33.25t3.44-33.25L621,50.81H610.65a5.11,5.11,0,0,1,.07-.93,5.27,5.27,0,0,0,.07-.79q.66-5.43-2.51-8.6h29.08q6.87,5.16,14.28,10.32-2,18.38-3.9,36.55t-3.9,36.69C643.48,127.66,644.37,130.52,646.48,132.64ZM633.39,77.38l2.91-26.57H626l-3.57,33.31Z" transform="translate(-59.12 -40.49)" />
                <path class="cls-1" d="M703.19,50.67l-7.53,71.66-19.83,10.31H650.18a12.77,12.77,0,0,0,4.23-8.59l7-66.5,11.1-6.88H662.21l.13-1.58q.52-5.56-2.51-8.6h29.09Zm-22.87,71.66,7.54-71.66H677.55l-6.88,64.78-10.84,6.88Z" transform="translate(-59.12 -40.49)" />
                <path class="cls-1" d="M107,291.28H84.76A12.76,12.76,0,0,0,89,282.69c.27-2.12.55-4.81.86-8.07s.66-6.65,1.06-10.18.77-7,1.12-10.44.66-6.35.93-8.73q-2.77,1.72-5.49,3.37L82,252q-.93,7.92-1.79,16.19t-1.52,14.48q-.53,5.42,2.65,8.59H59.12a12.13,12.13,0,0,0,4.23-8.59q1.84-16.79,3.57-33.25t3.43-33.25l11.11-6.74H71.15a6.31,6.31,0,0,1,.06-.93,4.22,4.22,0,0,0,.07-.79q.66-5.43-2.51-8.59H97.85q6.87,5.15,14.28,10.31-2,18.38-3.9,36.55t-3.9,36.69Q103.81,288.11,107,291.28ZM93.88,236l2.91-26.57H86.48l-3.57,33.31Z" transform="translate(-59.12 -40.49)" />
                <path class="cls-1" d="M164,209.31,161.18,235,133.81,252l-3.31,30.67q-.66,5.42,2.65,8.59H110.94a13.17,13.17,0,0,0,4.23-8.59l7-66.5,11-6.88H123l.13-1.58a10.52,10.52,0,0,0-2.51-8.59h28.95ZM145.58,236l2.91-26.71H138.3l-3.56,33.45Z" transform="translate(-59.12 -40.49)" />
                <path class="cls-1" d="M210.09,291.28H187.88a12.76,12.76,0,0,0,4.23-8.59q.39-3.18.86-8.07T194,264.44c.39-3.52.77-7,1.12-10.44s.66-6.35.93-8.73l-5.49,3.37c-1.81,1.11-3.64,2.23-5.49,3.38q-.91,7.92-1.78,16.19t-1.52,14.48c-.36,3.61.53,6.47,2.64,8.59H162.23a12.05,12.05,0,0,0,4.23-8.59q1.86-16.79,3.57-33.25t3.44-33.25l11.1-6.74H174.26a5.11,5.11,0,0,1,.07-.93,5.27,5.27,0,0,0,.07-.79c.43-3.62-.4-6.48-2.52-8.59H201q6.87,5.15,14.27,10.31-2,18.38-3.9,36.55t-3.9,36.69Q206.92,288.11,210.09,291.28ZM197,236l2.91-26.57H189.6L186,242.76Z" transform="translate(-59.12 -40.49)" />
                <path class="cls-1" d="M243.8,281l3.44-33.45-27.37,17,5-48.38,11.1-6.88H225.69l.13-1.58q.52-5.55-2.51-8.59h47.86a12.19,12.19,0,0,0-4.37,8.59l-.13,1.58L265.35,222l-16.52,10.17,1.71-16,11-6.88H241.16l-3.84,35.83,27.37-17-.26,1.72L259.14,281l-16.4,10.31H213.66a12.77,12.77,0,0,0,4.23-8.59l1.32-11.9,16.53-10.31-.13,1-1.33,12.7-11,6.74Z" transform="translate(-59.12 -40.49)" />
                <path class="cls-1" d="M296.42,199.14a12.19,12.19,0,0,0-4.37,8.59l-.13,1.58-7.8,73.38q-.66,5.42,2.64,8.59H264.55a13.18,13.18,0,0,0,4.24-8.59l7.8-73.38.13-1.58a10.55,10.55,0,0,0-2.51-8.59Z" transform="translate(-59.12 -40.49)" />
                <path class="cls-1" d="M332.77,281.1l-16.39,10.18H306.06L291.79,281.1l7.53-71.65,16.4-10.18H326l14.28,10.18ZM325,209.45H314.66l-6.88,64.78-.66,6.87h10.32Z" transform="translate(-59.12 -40.49)" />
                <path class="cls-1" d="M359.48,243.69l-4.1,39q-.67,5.42,2.77,8.59H335.81a12.77,12.77,0,0,0,4.23-8.59l7.8-73.38.13-1.58q.52-5.55-2.51-8.59H367.8a12,12,0,0,0-4.49,8.59l-.13,1.58-2.25,20.5,8.72,15.33,3.84-35.83.13-1.58a10.55,10.55,0,0,0-2.51-8.59h22.21a12.14,12.14,0,0,0-4.36,8.59l-.14,1.58L381,282.69q-.66,5.42,2.65,8.59H361.46a13.22,13.22,0,0,0,4.23-8.59L368.2,259Z" transform="translate(-59.12 -40.49)" />
                <path class="cls-1" d="M435.49,291.28H413.28a12.76,12.76,0,0,0,4.23-8.59c.27-2.12.55-4.81.86-8.07s.66-6.65,1.06-10.18.77-7,1.12-10.44.66-6.35.93-8.73q-2.78,1.72-5.49,3.37L410.5,252q-.92,7.92-1.78,16.19t-1.52,14.48q-.52,5.42,2.64,8.59H387.63a12.05,12.05,0,0,0,4.23-8.59q1.86-16.79,3.57-33.25t3.44-33.25L410,209.45H399.66a5.11,5.11,0,0,1,.07-.93,5.27,5.27,0,0,0,.07-.79q.66-5.43-2.51-8.59h29.08q6.87,5.15,14.28,10.31-2,18.38-3.9,36.55t-3.9,36.69C432.49,286.3,433.38,289.16,435.49,291.28ZM422.4,236l2.91-26.57H415l-3.57,33.31Z" transform="translate(-59.12 -40.49)" />
                <path class="cls-1" d="M492.2,209.31,484.67,281l-19.83,10.31H439.19a12.77,12.77,0,0,0,4.23-8.59l7-66.5,11.1-6.88H451.22l.13-1.58q.52-5.55-2.51-8.59h29.09ZM469.33,281l7.54-71.66H466.56l-6.88,64.78L448.84,281Z" transform="translate(-59.12 -40.49)" />
                <path class="cls-1" d="M538.61,291.28H516.4a12.8,12.8,0,0,0,4.23-8.59q.39-3.18.86-8.07c.3-3.26.66-6.65,1.06-10.18s.77-7,1.12-10.44.66-6.35.92-8.73q-2.77,1.72-5.48,3.37c-1.81,1.11-3.64,2.23-5.49,3.38q-.93,7.92-1.78,16.19t-1.52,14.48c-.36,3.61.53,6.47,2.64,8.59H490.75a12.05,12.05,0,0,0,4.23-8.59q1.84-16.79,3.57-33.25T502,216.19l11.1-6.74H502.78a5.11,5.11,0,0,1,.07-.93,5.25,5.25,0,0,0,.06-.79q.66-5.43-2.51-8.59h29.09q6.87,5.15,14.27,10.31-2,18.38-3.9,36.55T536,282.69Q535.43,288.11,538.61,291.28ZM525.52,236l2.91-26.57H518.12l-3.57,33.31Z" transform="translate(-59.12 -40.49)" />
            </svg>
        </div>
    </div>



    <div class="titulo-containerl">
        <h1 class="title-logros">Logros</h1>
        <div class="horizontal2"></div>
        <div class="horizontal"></div>
        <div class="vertical"></div>

    </div>
    <div class="contenedor-logros">
        <?php include '../otros/logros.php'; ?>
    </div>





    <!-- DEPORTISTAS DESTACADOS -->
    <div class="titulo-containerdd">
        <h1 class="title-deportistas">Deportistas destacados</h1>
        <div class="horizontal"></div>
        <div class="vertical"></div>
        <div class="horizontal2"></div>
    </div>
    <div class="contenedor-deportistas">
        <!-- Carrusel izquierdo -->
        <?php include '../otros/deportistas1.php'; ?>
        <!-- Espacio vacío entre carruseles -->
        <img src="../img/logoX_LDCR.png" alt="Imagen del medio" class="medioDestacados">
        <!-- Carrusel derecho -->
        <?php include '../otros/deportistas2.php'; ?>
    </div>


    <!-- -->



    <?php
    /*
        <div class="imagenes-deportes">
            <?php
            $fuente = "../img/v1.jpg";
            for ($i = 0; $i < 4; $i++) {
                if ($i == 3) {
                    $fuente = "../img/v2.jpeg";
                } ?>
                <img src="<?php echo $fuente ?>" alt="">
            <?php } ?>

        </div>
        */
    ?>
    <div class="titulo-containere">
        <h1 class="title-escuela">Escuelas deportivas</h1>
        <div class="horizontal"></div>
    </div>
    <div class="escuelas">
        <?php
        if (empty($deportes)) {
            echo '<h1>No se han agregado las escuelas deportivas</h1>';
        } else {
        ?>
            <div id="carouselDeporte" class="carousel slide carrusel-deportes" data-bs-ride="carousel">
                <div class="carousel-inner">
                    <?php
                    // Dividir el array $deportes en grupos de tres
                    $grupos = array_chunk($deportes, 4);

                    // Iterar sobre los grupos de imágenes
                    foreach ($grupos as $grupo_index => $grupo) {
                    ?>
                        <div class="carousel-item <?php echo ($grupo_index === 0) ? 'active' : ''; ?>">
                            <div class="row justify-content-center"> <!-- Alinea las columnas al centro -->
                                <?php
                                // Calcular el tamaño de las columnas
                                $column_width = 12 / count($grupo);

                                // Iterar sobre las imágenes dentro de cada grupo
                                foreach ($grupo as $deporte) {
                                    $rutaImagen = $deporte['imagen'];
                                    $titulo = $deporte['nombre'];
                                ?>
                                    <div class="col-<?php echo $column_width; ?> overlayDeportes">
                                        <div class="imagenes-deportes">
                                            <img src="<?php echo htmlspecialchars($rutaImagen); ?>" alt="Imagen del deporte">
                                            <div class="overlay-content">
                                                <div class="overlay-text"><?php echo htmlspecialchars($titulo); ?></div>
                                            </div>
                                        </div>
                                    </div>
                                <?php
                                }
                                ?>
                            </div>
                        </div>
                    <?php
                    }
                    ?>
                </div>
                <button class="carousel-control-prev" type="button" data-bs-target="#carouselDeporte" data-bs-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Anterior</span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#carouselDeporte" data-bs-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Siguiente</span>
                </button>
            </div>
        <?php
        }
        ?>
    </div>

    <div class="modal mdCondolencia" id="modalCondolencias" >
        <div class="modal-dialog">
            <div class="modal-content contenido-condolencia">
                <div class="modal-body">
                    <div id="carruselCondolencias" class="carousel slide" data-bs-ride="carousel">
                        <div class="carousel-inner">
                            <?php foreach ($condolencias as $key => $condolencia) : ?>
                                <div class="carousel-item <?php echo ($key == 0) ? 'active' : ''; ?>">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <img src="<?php echo $condolencia['imagen']; ?>" class="d-block w-100" alt="Imagen">
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mensaje">
                                                <p class="txt-condolencias"><?php echo $condolencia['mensaje']; ?></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <button id="prevDer" class="carousel-control-prev" type="button" data-bs-target="#carruselCondolencias" data-bs-slide="prev">
                            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">Anterior</span>
                        </button>
                        <button id="nextDer" class="carousel-control-next" type="button" data-bs-target="#carruselCondolencias" data-bs-slide="next">
                            <span class="carousel-control-next-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">Siguiente</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        <?php if (!empty($condolencias)) : ?>
            // Si hay registros en $condolencias, abrir el modal
            var modalCondolencias = new bootstrap.Modal(document.getElementById('modalCondolencias'));
            modalCondolencias.show();
        <?php endif; ?>
    });
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-bYQVJwS/Jt2f7fSFb4hKQVaPvhIrm+PoH6n/TYYJ8WlxFgyC3m8M2MUpM3Il7eJb" crossorigin="anonymous"></script>

<?php include '../includes/footer.php'; ?>