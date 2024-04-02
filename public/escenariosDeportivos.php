<?php
include '../includes/header.php';
include '../includes/config.php';

$stmt = $conn->prepare("SELECT * FROM escenarios");
$stmt->execute();
$escenarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<div class="escenarios">
<div id="carouselEscenario" class="carousel slide carousel-escenarios" data-bs-ride="carousel">
    <div class="carousel-inner">
        <?php
        $active = true; // Variable para activar la clase 'active' en el primer elemento

        foreach ($escenarios as $escenario) {
            $rutaImagen = $escenario['imagen'];
            $nombreEscenario = $escenario['nombre'];
        ?>
            <div class="carousel-item <?php echo $active ? 'active' : ''; ?>">
                <img src="<?php echo htmlspecialchars($rutaImagen); ?>" class="d-block w-100" alt="<?php echo htmlspecialchars($nombreEscenario); ?>">
                <div class="carousel-caption d-none d-md-block">
                    <h5><?php echo htmlspecialchars($nombreEscenario); ?></h5>
                </div>
            </div>
        <?php
            $active = false; // Desactivar la clase 'active' después del primer elemento
        }
        ?>
    </div>
    <button class="carousel-control-prev" type="button" data-bs-target="#carouselEscenario" data-bs-slide="prev">
        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Anterior</span>
    </button>
    <button class="carousel-control-next" type="button" data-bs-target="#carouselEscenario" data-bs-slide="next">
        <span class="carousel-control-next-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Siguiente</span>
    </button>
</div>    
</div>

<?php
include '../includes/footer.php';
?>
