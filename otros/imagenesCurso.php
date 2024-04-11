<?php

include '../includes/config.php'; // Reemplaza 'conexion.php' con la ruta correcta a tu archivo de conexión


if (isset($_POST['id'])) {

    $id = $_POST['id'];

    // Obtener imágenes de la galería para el curso actual
    $stmtGaleria = $conn->prepare("SELECT ruta_imagenes FROM galeria_imagenes WHERE id_tipo = :id AND tipo = 'Curso'");
    $stmtGaleria->bindParam(':id', $id);
    $stmtGaleria->execute();
    $imagenesGaleria = $stmtGaleria->fetchAll(PDO::FETCH_ASSOC);


    if ($imagenesGaleria) {
?>
        <div class="row">
            <div id="carouselgaleriaCurso" class="carousel slide galeriaCurso" data-bs-ride="carousel">
                <div class="carousel-inner">
                    <?php
                    // Iterar sobre las imágenes de la galería
                    foreach ($imagenesGaleria as $key => $imagen) {
                        $rutaImagen = $imagen['ruta_imagenes'];
                        $active_class = ($key === 0) ? 'active' : '';
                    ?>
                        <div class="carousel-item <?php echo $active_class; ?>">
                            <img src="<?php echo htmlspecialchars($rutaImagen); ?>" class="d-block w-100" alt="Imagen de la galería">
                        </div>
                    <?php
                    }
                    ?>
                </div>
                <button class="carousel-control-prev" type="button" data-bs-target="#carouselgaleriaCurso" data-bs-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Anterior</span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#carouselgaleriaCurso" data-bs-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Siguiente</span>
                </button>
            </div>
        </div>
<?php
    } else {
        // Si no hay imágenes, muestra un mensaje de error o maneja la situación de otra manera
        echo 'No se encontraron imágenes para este curso.';
    }
} else {
    // Si no se proporciona el parámetro curso_id, muestra un mensaje de error o maneja la situación de otra manera
    echo 'Se requiere el parámetro curso_id.';
}
?>