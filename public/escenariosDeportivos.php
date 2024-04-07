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

<div class="container-escenario">
    <?php foreach ($escenarios as $escenario) : ?>
        <div class="linea"></div>
        <div class="escenario">

            <div class="imagen-ubicacion">
                <img src="<?php echo htmlspecialchars($escenario['imagen']); ?>" alt="<?php echo htmlspecialchars($escenario['nombre']); ?>">
                <div class="map-container">
                    <?php
                    // Validar y sanitizar la URL antes de usarla en el iframe
                    $ubicacion = $escenario['ubicacion'];
                    if (filter_var($ubicacion, FILTER_VALIDATE_URL)) {
                        // Si la URL es válida, mostrar el iframe
                        echo '<iframe src="' . htmlspecialchars($ubicacion) . '"></iframe>';
                    } else {
                        // Si la URL no es válida, mostrar un mensaje de error o una URL predeterminada
                        echo 'URL no válida';
                    }
                    ?>
                </div>
            </div>
            <div class="informacion-escenario">
                <?php if (empty($escenario['nombre'])) { ?>
                    <h2>No se proporcionó nombre</h2>
                <?php } else { ?>
                    <h2><?php echo htmlspecialchars($escenario['nombre']); ?></h2>
                <?php } ?>

                <?php if (empty($escenario['direccion'])) { ?>
                    <p>No se proporcionó dirección</p>
                <?php } else { ?>
                    <p>Dirección: <?php echo htmlspecialchars($escenario['direccion']); ?></p>
                <?php } ?>

                <?php if (empty($escenario['telefono'])) { ?>
                    <p>No se proporcionó teléfono</p>
                <?php } else { ?>
                    <p>Teléfono: <?php echo htmlspecialchars($escenario['telefono']); ?></p>
                <?php } ?>

                <?php if (empty($escenario['supervisor'])) { ?>
                    <p>No se proporcionó supervisor</p>
                <?php } else { ?>
                    <p>Supervisor: <?php echo htmlspecialchars($escenario['supervisor']); ?></p>
                <?php } ?>

                <?php if (empty($escenario['celular'])) { ?>
                    <p>No se proporcionó celular</p>
                <?php } else { ?>
                    <p>Celular: <?php echo htmlspecialchars($escenario['celular']); ?></p>
                <?php } ?>
            </div>

        </div>

    <?php endforeach; ?>
</div>
<style>
    .linea {
        background-color: #0fc3c6;
        width: 100%;
        height: 5px;
    }

    .container-escenario {
        flex-direction: column;
        justify-content: center;
    }

    .escenario {
        justify-content: center;
        display: flex;
        margin-bottom: 20px;
        margin-top: 100px;
    }

    .imagen-ubicacion {
        width: 70%;
        display: flex;
        flex-direction: column;
    }

    .imagen-ubicacion img {
        width: 100%;
        height: 300px;
    }

    .imagen-ubicacion .map-container {
        width: 100%;
        height: 300px;
        padding: 0 0 0 0;
    }

    .imagen-ubicacion iframe {
        height: 300px;
    }

    .escenario .informacion {
        width: 30%;
        padding-left: 20px;
    }

    .escenario h2 {
        font-size: 30px;
        color: white;
        padding-left: 30px;
    }

    .escenario p {
        font-size: 15px;
        color: white;
        padding-left: 40px;
    }
</style>





<?php
include '../includes/footer.php';
?>