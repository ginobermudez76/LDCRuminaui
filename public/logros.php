<?php
include '../includes/config.php';
try {
    $stmt = $conn->prepare("CALL info_logros()");
    $stmt->execute();
    $logros = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $stmt->closeCursor();
} catch (PDOException $e) {
    echo "Error al obtener la información de deportistas: " . $e->getMessage();
}
?>
<div id="carouselDeporte" class="carousel slide" data-bs-ride="carousel">
    <div class="carousel-inner">
        <?php
        // Iterar sobre las imágenes obtenidas de la base de datos
        foreach ($logros as $key => $logro) {
            $rutaImagen = $logro['imagen']; // Ajusta según la estructura de tu tabla
            $titulo = $logro['titulo']; // Ajusta según la estructura de tu tabla
            ?>
            <div class="carousel-item <?php echo ($key === 0) ? 'active' : ''; ?>">
                <img src="<?php echo htmlspecialchars($rutaImagen); ?>" class="d-block w-100" alt="Imagen del logro">
                <div class="overlay2">
                    <h5><?php echo htmlspecialchars($titulo); ?></h5>
                    <p>Deporte: <?php echo $logro['deporte']; ?></p> <!-- Mostrar el deporte_id -->
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
