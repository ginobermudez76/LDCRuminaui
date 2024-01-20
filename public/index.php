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

?>

</header>
<section>
        <div class="container mt-4">
            <h2>¿Quiénes somos?</h2>
            <p>Somos BERPART, una empresa especializada en la venta de repuestos de auto a nivel mecánico y eléctrico. Nos enorgullece ofrecer a nuestros clientes una amplia gama de productos de alta calidad para satisfacer sus necesidades automotrices. Con años de experiencia en la industria, nos destacamos por nuestro compromiso con la excelencia y la satisfacción del cliente.</p>
            <p>Nuestra misión es proporcionar soluciones confiables y duraderas para mantener los vehículos en óptimo estado. En BERPART, no solo vendemos repuestos, sino que también compartimos la pasión por los automóviles y nos esforzamos por brindar un servicio excepcional a cada cliente.</p>
          </div>
    </section>
    <section>
        <div class="container mt-4">
            <h2>¿Qué ofrecemos?</h2>
            <p>En BERPART, ofrecemos más que repuestos de auto; brindamos servicios integrales para mantener tu vehículo en las mejores condiciones. Nuestro equipo de profesionales altamente capacitados se especializa en servicios de mantenimiento, reparación y asesoría técnica.</p>
            <p>Desde el diagnóstico preciso hasta la instalación de repuestos, nos aseguramos de que tu automóvil reciba la atención que merece. Ya sea una reparación específica, un mantenimiento programado o simplemente necesitas asesoramiento, en BERPART estamos comprometidos a ser tu aliado confiable en el mundo automotriz.</p>
        </div>
    </section>
    <section>
        <div class="container mt-4">
            <h2>Nuestra misión</h2>
            <p>En BERPART, ofrecemos más que repuestos de auto; brindamos servicios integrales para mantener tu vehículo en las mejores condiciones. Nuestro equipo de profesionales altamente capacitados se especializa en servicios de mantenimiento, reparación y asesoría técnica.</p>
            <p>Desde el diagnóstico preciso hasta la instalación de repuestos, nos aseguramos de que tu automóvil reciba la atención que merece. Ya sea una reparación específica, un mantenimiento programado o simplemente necesitas asesoramiento, en BERPART estamos comprometidos a ser tu aliado confiable en el mundo automotriz.</p>
        </div>
    </section>
    <section>
        <div class="container mt-4">
            <h2>Nuestra visión</h2>
            <p>En BERPART, ofrecemos más que repuestos de auto; brindamos servicios integrales para mantener tu vehículo en las mejores condiciones. Nuestro equipo de profesionales altamente capacitados se especializa en servicios de mantenimiento, reparación y asesoría técnica.</p>
            <p>Desde el diagnóstico preciso hasta la instalación de repuestos, nos aseguramos de que tu automóvil reciba la atención que merece. Ya sea una reparación específica, un mantenimiento programado o simplemente necesitas asesoramiento, en BERPART estamos comprometidos a ser tu aliado confiable en el mundo automotriz.</p>
        </div>
    </section>
<section name="Section_deportes" id ="Section_deportes">

</section>

<section name="Section_eventos" id="Section_eventos">
<div class="container mt-4" id="detalleEvento">
    <!-- Contenido del Evento -->
    <?php foreach ($eventos as $evento) : ?>
        
            <div class="row">
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
                <p>Fecha de Inicio: <?php echo htmlspecialchars($evento['fecha_inicio']); ?></p>
                <p>Fecha de Fin: <?php echo htmlspecialchars($evento['fecha_fin']); ?></p>
                <p>Inscripciones: <?php echo htmlspecialchars($evento['inscripciones']); ?></p>
                <p>Estado: <?php echo htmlspecialchars($evento['estado']); ?></p>
                <p>Descripción: <?php echo htmlspecialchars($evento['descripcion']); ?></p>
                <p>Deporte: <?php echo htmlspecialchars($evento['nombre_deporte']); ?></p>
            </div>
        </div>    


    <?php endforeach; ?>
</div>
</section>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-bYQVJwS/Jt2f7fSFb4hKQVaPvhIrm+PoH6n/TYYJ8WlxFgyC3m8M2MUpM3Il7eJb" crossorigin="anonymous"></script>

<?php include '../includes/footer.php'; ?>