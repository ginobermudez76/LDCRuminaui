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
    $stmtDeportes = $conn->prepare("SELECT * FROM deportes");
    $stmtDeportes->execute();
    $deportes = $stmtDeportes->fetchAll(PDO::FETCH_ASSOC);

    // Cerrar el cursor después de obtener los resultados
    $stmtDeportes->closeCursor();
} catch (PDOException $e) {
    echo "Error al obtener la información de deportes: " . $e->getMessage();
}

?>

<div class="presentacion">
    <div class="presentacion-1">
        <div class="header-txt">
            <h1>¿Quienes somos?</h1>
            <span>LDCR</span>
            <p>La ‘Liga Deportiva Cantonal Rumiñahui’ es una institución de derecho privado con finalidad social, es máxima entidad del deporte en el Cantón Rumiñahui, Con Sede en la ciudad de Sangolquí, afiliada a la Concentración Deportiva de Pichincha</p>
        </div>
    </div>
</div>

<div class="mision">
    <div class="mision-1"></div>
    <div class="mision-2">
        <h2>Nuestra misión</h2>
        <p>Nuestra misión es fomentar el desarrollo integral de la comunidad a través del deporte, promoviendo valores, inclusión y bienestar. Trabajamos arduamente para ofrecer oportunidades deportivas que inspiren el espíritu competitivo y fortalezcan los lazos comunitarios.</p>
    </div>
</div>
<div class="vision">
    <div class="vision-2">
        <h2>Nuestra visión</h2>
        <p>Aspiramos a ser un referente regional en el ámbito deportivo, reconocidos por nuestra excelencia, compromiso social y contribución al desarrollo sostenible. Buscamos ser líderes en la promoción de un estilo de vida activo y saludable, impulsando el crecimiento personal y colectivo.

        </p>
    </div>
    <div class="vision-1">

    </div>
</div>

<div class="row">
    <div class="deportes container">
        <div class="deportes-descripcion">
            <h2>Nuestras escuelas deportivas</h2>
            <p>Nuestra liga deportiva cuenta con las siguientes escuelas deportivas</p>
        </div>

    </div>
    <?php
    // Dividir el array de imágenes en grupos de cuatro
    $chunks = array_chunk($deportes, 2);

    // Iterar sobre cada grupo de imágenes
    foreach ($chunks as $grupo) {
        echo '<div class="column">';
        // Iterar sobre las imágenes en el grupo
        foreach ($grupo as $depor) {
            echo '<div class="column">';
            echo '<div class="card-img-top">';
            echo '<img src="../uploads/deportes/' . htmlspecialchars(basename($depor['imagen'])) . '" class="card-img" alt="' . htmlspecialchars($depor['nombre']) . '">';
            echo '<div class="overlay">';
            echo '<div class="deporte-nombre" data-deporte-id="' . htmlspecialchars($depor['id']) . '">' . htmlspecialchars($depor['nombre']) . '</div>';
            echo '</div>';
            echo '</div>';
            echo '</div>';
        }
        echo '</div>';
    }
    ?>
</div>

<div class="modal" id="myModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <span class="close">&times;</span>
            <div class="modal-body">
                <div id="carousel" class="carousel"></div>
                <!-- Corregir la clase del contenedor de la superposición -->
                <div class="overlay2">
                    <div class="col-md-6" id="descripcionDeporte"></div>
                </div>
            </div>
        </div>
    </div>
</div>




<div class="deportes container">
        <div class="deportes-descripcion">
            <h2>Eventos deportivos</h2>
        </div>

    </div>
<div class="contenedor-imagenes">
    <?php foreach ($eventos as $index => $evento) : ?>
        <div class="container1">
            <img onclick="toggleTexto(<?php echo $index; ?>)" class="imagen1" src="<?php echo $evento['imagen']; ?>" alt="<?php echo $evento['descripcion']; ?>">
            <div class="texto-desplegable" id="texto-desplegable-<?php echo $index; ?>">
                <p><?php echo htmlspecialchars($evento['nombre_evento']); ?></p>
                <p>Inicio: <?php echo htmlspecialchars($evento['fecha_inicio']); ?></p>
                <p>Fin: <?php echo htmlspecialchars($evento['fecha_fin']); ?></p>
                <?php echo htmlspecialchars($evento['inscripciones']); ?></p>-->
                <p><?php echo htmlspecialchars($evento['estado']); ?></p>
                <p><?php echo htmlspecialchars($evento['descripcion']); ?></p>
            </div>
        </div>
    <?php endforeach; ?>
</div>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        // Cuando se hace clic en el nombre de deporte
        $(".deporte-nombre").click(function() {
            var deporte_id = $(this).data("deporte-id");
            // Hacer una petición AJAX para obtener las imágenes del carrusel
            $.ajax({
                url: "obtener_imagenes.php",
                method: "GET",
                data: {
                    deporte_id: deporte_id
                },
                success: function(response) {
                    // Agregar las imágenes al carrusel
                    $("#carousel").html(response);
                    // Mostrar el modal
                    $("#myModal").css("display", "block");
                }
            });

            // Hacer una petición AJAX para obtener la descripción del deporte
            $.ajax({
                url: "obtener_descripcion_deporte.php",
                method: "GET",
                data: {
                    deporte_id: deporte_id
                },
                success: function(response) {
                    // Agregar la descripción del deporte
                    $("#descripcionDeporte").html(response);
                }
            });
        });

        // Cuando se hace clic en el botón de cerrar del modal
        $(".close").click(function() {
            $("#myModal").css("display", "none");
        });
    });


</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-bYQVJwS/Jt2f7fSFb4hKQVaPvhIrm+PoH6n/TYYJ8WlxFgyC3m8M2MUpM3Il7eJb" crossorigin="anonymous"></script>

<?php include '../includes/footer.php'; ?>