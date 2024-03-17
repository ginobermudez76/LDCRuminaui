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
<div id="carouselLogro" class="carousel slide carrusel-logros" data-bs-ride="carousel">
    <div class="carousel-inner">
        <?php
        // Dividir el array $logros en grupos de tres
        $grupos = array_chunk($logros, 3);
        
        // Iterar sobre los grupos de imágenes
        foreach ($grupos as $grupo_index => $grupo) {
            ?>
            <div class="carousel-item <?php echo ($grupo_index === 0) ? 'active' : ''; ?>">
                <div class="row">
                    <?php
                    // Iterar sobre las imágenes dentro de cada grupo
                    foreach ($grupo as $logro) {
                        $rutaImagen = $logro['imagen'];
                        $titulo = $logro['titulo'];
                        ?>
                        <div class="col">
                            <div class="image-container">
                                <img src="<?php echo htmlspecialchars($rutaImagen); ?>" class="d-block w-100" alt="Imagen del logro">
                                <div class="overlayLogros">
                                    <h5><?php echo htmlspecialchars($titulo); ?></h5>
                                    <p>Deporte: <?php echo $logro['deporte']; ?></p>
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
    <button class="carousel-control-prev" type="button" data-bs-target="#carouselLogro" data-bs-slide="prev">
        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Anterior</span>
    </button>
    <button class="carousel-control-next" type="button" data-bs-target="#carouselLogro" data-bs-slide="next">
        <span class="carousel-control-next-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Siguiente</span>
    </button>
</div>
