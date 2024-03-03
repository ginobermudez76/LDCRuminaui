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
    // Obtener datos de la tabla "deporte"
    $stmtDeportes = $conn->prepare("SELECT nombre, descripcion, imagen FROM deportes");
    $stmtDeportes->execute();
    $deportes = $stmtDeportes->fetchAll(PDO::FETCH_ASSOC);

    // Cerrar el cursor después de obtener los resultados
    $stmtDeportes->closeCursor();
} catch (PDOException $e) {
    echo "Error al obtener la información de deportes: " . $e->getMessage();
}

?>

    <section class="presentacion">
        <div class="presentacion-1">
            <div class="header-txt">
                <h1>¿Quienes somos?</h1>
                <span>LDCR</span>
                <p>La ‘Liga Deportiva Cantonal Rumiñahui’ es una institución de derecho privado con finalidad social, es máxima entidad del deporte en el Cantón Rumiñahui, Con Sede en la ciudad de Sangolquí, afiliada a la Concentración Deportiva de Pichincha</p>
            </div>
        </div>
    </section>

    <section class="mision">
        <div class="mision-1"></div>
        <div class="mision-2">
            <h2>Nuestra misión</h2>
            <p>Nuestra misión es fomentar el desarrollo integral de la comunidad a través del deporte, promoviendo valores, inclusión y bienestar. Trabajamos arduamente para ofrecer oportunidades deportivas que inspiren el espíritu competitivo y fortalezcan los lazos comunitarios.</p>
        </div>
    </section>
    <section class="vision">
        <div class="vision-2">
            <h2>Nuestra visión</h2>
            <p>Aspiramos a ser un referente regional en el ámbito deportivo, reconocidos por nuestra excelencia, compromiso social y contribución al desarrollo sostenible. Buscamos ser líderes en la promoción de un estilo de vida activo y saludable, impulsando el crecimiento personal y colectivo.

            </p>
        </div>
        <div class="vision-1">

        </div>
    </section>





        <div class="deportes container">
            <div class="deportes-descripcion">
                <h2>Nuestras escuelas deportivas</h2>
                <hr>
                <p>Nuestra liga deportiva cuenta con las siguientes escuelas deportivas</p>
            </div>
            <div class="row">
                <?php
                // Dividir el array de imágenes en grupos de cuatro
                $chunks = array_chunk($deportes, 4);

                // Iterar sobre cada grupo de imágenes
                foreach ($chunks as $grupo) {
                    echo '<div class="column">';
                    // Iterar sobre las imágenes en el grupo
                    foreach ($grupo as $depor) {
                        echo '<div class="card-img-top">';
                        echo '<img src="../uploads/deportes/' . htmlspecialchars(basename($depor['imagen'])) . '" class="card-img" alt="' . htmlspecialchars($depor['nombre']) . '">';
                        echo '<div class="overlay">';
                        echo '<div class="deporte-nombre">' . htmlspecialchars($depor['nombre']) . '</div>';
                        echo '</div>';
                        echo '</div>';
                    }
                    echo '</div>';
                }
                ?>
            </div>
        </div>


    <section class="eventos" name="Section_eventos" id="Section_eventos">
        <h2>Eventos</h2>

        <?php
        if (!empty($eventos)) {
            echo '<div class="container mt-4" id="detalleEvento">';

            foreach ($eventos as $evento) {
            }
            echo '</div>';
        } else {
            echo '<div class="container mt-4" id="detalleEvento">';
            echo '<p class="text-center">No hay eventos próximos. ¡No te desanimes! Pronto prepararemos algo especial para ti.</p>';
            echo '</div>';
        }
        ?>

        <div class="container mt-4" id="detalleEvento">
            <!-- Contenido del Evento -->
            <?php foreach ($eventos as $evento) : ?>

                <div class="row evento-container" style="background-image: url('<?php echo htmlspecialchars($evento['imagen']); ?>');">
                    <!-- Carrusel en la columna izquierda -->
                    <div class="col-md-6">
                        <!-- Carrusel de imágenes aquí -->
                        <div id="carouselEvento_<?php echo $evento['evento_id']; ?>" class="carousel slide" data-bs-ride="carousel">
                            <div class="carousel-inner">
                                <?php
                                // Obtener imágenes de la galería para el evento actual
                                $stmtGaleria = $conn->prepare("SELECT ruta_imagenes FROM galeria_imagenes WHERE id_tipo = :evento_id AND tipo = 'Evento'");
                                $stmtGaleria->bindParam(':evento_id', $evento['evento_id']);
                                $stmtGaleria->execute();
                                $imagenesGaleria = $stmtGaleria->fetchAll(PDO::FETCH_ASSOC);

                                // Iterar sobre las imágenes de la galería
                                foreach ($imagenesGaleria as $key => $imagen) :
                                    $rutaImagen = $imagen['ruta_imagenes'];
                                ?>
                                    <div class="carousel-item <?php echo $key === 0 ? 'active' : ''; ?>">
                                        <img src="<?php echo htmlspecialchars($rutaImagen); ?>" class="d-block w-100" alt="Imagen de la galería">
                                    </div>
                                <?php endforeach; ?>

                            </div>
                            <button class="carousel-control-prev" type="button" data-bs-target="#carouselEvento_<?php echo $evento['evento_id']; ?>" data-bs-slide="prev">
                                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                <span class="visually-hidden">Anterior</span>
                            </button>
                            <button class="carousel-control-next" type="button" data-bs-target="#carouselEvento_<?php echo $evento['evento_id']; ?>" data-bs-slide="next">
                                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                <span class="visually-hidden">Siguiente</span>
                            </button>
                        </div>
                    </div>
                    <!-- Detalles del Evento en la columna derecha -->
                    <div class="col-md-6 detalles-evento">
                        <h2><?php echo htmlspecialchars($evento['nombre_evento']); ?></h2>
                        <p>Inicio: <?php echo htmlspecialchars($evento['fecha_inicio']); ?></p>
                        <p>Fin: <?php echo htmlspecialchars($evento['fecha_fin']); ?></p>
                        <!--<p>Inscripciones: <?php echo htmlspecialchars($evento['inscripciones']); ?></p>-->
                        <p><?php echo htmlspecialchars($evento['estado']); ?></p>

                        <div class="descripcion-container">
                            <p>Descripción: <?php echo htmlspecialchars($evento['descripcion']); ?></p>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </section>



<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-bYQVJwS/Jt2f7fSFb4hKQVaPvhIrm+PoH6n/TYYJ8WlxFgyC3m8M2MUpM3Il7eJb" crossorigin="anonymous"></script>

<?php include '../includes/footer.php'; ?>