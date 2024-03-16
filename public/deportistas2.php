<?php
include '../includes/config.php';

try {
    $stmt = $conn->prepare("SELECT * FROM deportistas_destacados WHERE MOD(id, 2) = 0");
    $stmt->execute();
    $deportistasPares = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $stmt->closeCursor();
} catch (PDOException $e) {
    echo "Error al obtener la información de deportistas: " . $e->getMessage();
}
?>

<?php if (!empty($deportistasPares)) : ?>
    <div id="carouselDerecho" class="carousel slide carrusel-destacados" data-bs-ride="carousel">
        <div class="carousel-inner">
            <?php foreach ($deportistasPares as $index => $deportistaPar) : ?>
                <div class="carousel-item <?= ($index === 0 ? 'active' : '') ?>">
                    <img class="d-block w-100" src="<?= $deportistaPar['imagen'] ?>" alt="<?= $deportistaPar['nombre_deportista'] ?>">
                    <div class="overlayDeportistas">
                        <div class="carousel-caption d-none d-md-block">
                            <h5><?= $deportistaPar['nombre_deportista'] ?></h5>
                        </div>
                    </div>

                </div>
            <?php endforeach; ?>
        </div>
        <button id="prevDer" class="carousel-control-prev" type="button" data-bs-target="#carouselDerecho" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Anterior</span>
        </button>
        <button id="nextDer" class="carousel-control-next" type="button" data-bs-target="#carouselDerecho" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Siguiente</span>
        </button>
    </div>
<?php else : ?>
    <p>No hay deportistas destacados disponibles.</p>
<?php endif; ?>