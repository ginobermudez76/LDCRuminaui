<?php
include '../includes/config.php';
try {
    $stmt = $conn->prepare("CALL info_logros()");
    $stmt->execute();
    $logros = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Variables para almacenar los logros por tipo
    $medallas = [];
    $copas = [];
    $reconocimientos = [];

    // Iterar sobre los logros y clasificarlos por tipo
    foreach ($logros as $logro) {
        switch ($logro['tipologro']) {
            case 'Medalla':
                $medallas[] = $logro;
                break;
            case 'Copa':
                $copas[] = $logro;
                break;
            case 'Reconocimiento':
                $reconocimientos[] = $logro;
                break;
            default:
                // Manejar caso inesperado
                break;
        }
    }

    $stmt->closeCursor();
} catch (PDOException $e) {
    echo "Error al obtener la información de logros: " . $e->getMessage();
}
?>

<div id="carouselLogro" class="carousel slide carrusel-logros" data-bs-ride="carousel">
    <div class="carousel-inner">
        <?php
        // Iterar sobre los logros y agruparlos en grupos de tres según su tipo
        $numGrupos = max(count($copas), count($medallas), count($reconocimientos));
        for ($i = 0; $i < $numGrupos; $i++) {
        ?>
            <div class="carousel-item <?php echo ($i === 0) ? 'active' : ''; ?>">
                <div class="row">
                    <?php
                    // Mostrar logro de tipo "Copa" en la posición actual del grupo
                    if (isset($copas[$i])) {
                        $logro = $copas[$i];
                        mostrarLogro($logro, "copa");
                    }

                    // Mostrar logro de tipo "Medalla" en la posición actual del grupo
                    if (isset($medallas[$i])) {
                        $logro = $medallas[$i];
                        mostrarLogro($logro, "medalla");
                    }

                    // Mostrar logro de tipo "Reconocimiento" en la posición actual del grupo
                    if (isset($reconocimientos[$i])) {
                        $logro = $reconocimientos[$i];
                        mostrarLogro($logro, "reconocimiento");
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

<?php
// Función para mostrar un logro en HTML
function mostrarLogro($logro, $tipo)
{
    $rutaImagen = $logro['imagen'];
    $titulo = $logro['titulo'];
?>
    <div class="col contenedor-infoLogros">
        <div class="image-container">
            <img src="../img/<?php echo $tipo; ?>.jpg" class="default-image d-block w-100 main-image" alt="Imagen del logro">
            <img src="<?php echo htmlspecialchars($rutaImagen); ?>" class="hover-image d-none w-100 main-image" alt="Imagen del logro">
            <h5><?php echo htmlspecialchars($titulo); ?></h5>
            <p><?php echo $logro['deporte']; ?></p>
        </div>
    </div>
<?php
}
?>
<script>
    // Cambiar la imagen al pasar el mouse sobre ella
    document.querySelectorAll('.image-container').forEach(item => {
        item.addEventListener('mouseover', event => {
            item.querySelector('.default-image').classList.add('d-none');
            item.querySelector('.hover-image').classList.remove('d-none');
        });

        item.addEventListener('mouseout', event => {
            item.querySelector('.default-image').classList.remove('d-none');
            item.querySelector('.hover-image').classList.add('d-none');
        });
    });
</script>
