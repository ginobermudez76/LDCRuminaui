<?php
// Primero, incluye tu archivo de configuración de base de datos (si no lo has hecho ya)
include '../includes/config.php'; // Reemplaza 'conexion.php' con la ruta correcta a tu archivo de conexión

// Verifica si se ha enviado el parámetro deporte_id
if (isset($_GET['deporte_id'])) {
    // Sanitiza el valor del parámetro para evitar inyección SQL
    $deporte_id = htmlspecialchars($_GET['deporte_id']);

    $stmtDescripcion = $conn->prepare("SELECT * FROM deportes WHERE id = :deporte_id");
    $stmtDescripcion->bindParam(':deporte_id', $deporte_id);
    $stmtDescripcion->execute();
    $descripcionDeporte = $stmtDescripcion->fetch(PDO::FETCH_ASSOC);

    // Obtener imágenes de la galería para el deporte actual
    $stmtGaleria = $conn->prepare("SELECT ruta_imagenes FROM galeria_imagenes WHERE id_tipo = :deporte_id AND tipo = 'Deporte'");
    $stmtGaleria->bindParam(':deporte_id', $deporte_id);
    $stmtGaleria->execute();
    $imagenesGaleria = $stmtGaleria->fetchAll(PDO::FETCH_ASSOC);


    if ($imagenesGaleria) {
?>
        <div class="row">
            <div id="carouselgaleriaDeportes" class="carousel slide galeria" data-bs-ride="carousel">
                <div class="carousel-inner">
                    <?php
                    // Iterar sobre las imágenes de la galería
                    foreach ($imagenesGaleria as $key => $imagen) {
                        $rutaImagen = $imagen['ruta_imagenes'];
                        $active_class = ($key === 0) ? 'active' : '';
                    ?>
                        <div class="carousel-item <?php echo $active_class; ?>">
                            <img src="<?php echo htmlspecialchars($rutaImagen); ?>" class="d-block w-100" alt="Imagen de la galería">
                            <div class="overlay2">
                                <p><?php echo htmlspecialchars($descripcionDeporte['descripcion']); ?></p>
                            </div>
                        </div>
                    <?php
                    }
                    ?>
                </div>
                <button class="carousel-control-prev" type="button" data-bs-target="#carouselgaleriaDeportes" data-bs-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Anterior</span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#carouselgaleriaDeportes" data-bs-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Siguiente</span>
                </button>
            </div>
        </div>
<?php
    } else {
        // Si no hay imágenes, muestra un mensaje de error o maneja la situación de otra manera
        echo 'No se encontraron imágenes para este deporte.';
    }
} else {
    // Si no se proporciona el parámetro deporte_id, muestra un mensaje de error o maneja la situación de otra manera
    echo 'Se requiere el parámetro deporte_id.';
}
?>
