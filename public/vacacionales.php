<?php
include '../includes/header.php';
include '../includes/config.php';
try {
    $stmt = $conn->prepare("CALL ObtenerInfoCursos()");
    $stmt->execute();
    $cursos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $stmt->closeCursor();
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
if (empty($cursos)) {
    echo '<h2>No hay cursos vacacionales por ahora</h2>';
} else {
?>
<div class="vacionales">
<div class="deportes container">
        <div class="deportes-descripcion">
            <h2 class="estatica">Cursos</h2><h2 class="movible">vacacionales</h2>
            <div class="linea"></div>
        </div>
    </div>
    <?php foreach ($cursos as $curso) : ?>
    <div class="container-vacacional">
        <div class="info-vacacional">
            <div clas="titulo">
            <h5><?php echo htmlspecialchars($curso['nombre']); ?></h5>
            <p><?php echo htmlspecialchars($curso['deporte']); ?></p>
            </div>
            <div class="fechas">
                <p>Inicio: <?php echo htmlspecialchars($curso['inicio']); ?></p>
                <p>Fin: <?php echo htmlspecialchars($curso['fin']); ?></p>
            </div>
            <div class="info-estado">
                <p class="<?php echo strtolower(str_replace(' ', '-', htmlspecialchars($curso['estado']))); ?>"><?php echo htmlspecialchars($curso['estado']); ?></p>
                <p class="<?php echo strtolower(str_replace(' ', '-', htmlspecialchars($curso['inscripciones']))); ?>"><?php echo htmlspecialchars($curso['inscripciones']); ?></p>
            </div>
            <div class="mas-info">
                <p><?php echo htmlspecialchars($curso['descripcion']); ?></p>
                <a type="button" class="btn btn-primary" href="../public/contacto.php">Contacto</a>
            </div>
        </div>
        <div class="imagen-boton">
            <img src="<?php echo htmlspecialchars($curso['imagen']); ?>" alt="<?php echo htmlspecialchars($curso['nombre']); ?>">
            <p class="ver-mas" id="<?php echo htmlspecialchars($curso['id']); ?>">Ver mas fotos</p>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<div class="modal mdDeporte" id="myModal">
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
<script>
$(document).ready(function() {
    // Cuando se hace clic en el nombre de deporte
    $(".ver-mas").click(function() {
        var curso_id = $(this).attr('id');
        // Hacer una petición AJAX para obtener las imágenes del carrusel
        $.ajax({
            url: "../otros/imagenesCurso.php",
            method: "POST",
            data: {
                id: curso_id
            },
            success: function(response) {
                // Agregar las imágenes al carrusel
                $("#carousel").html(response);
                // Mostrar el modal
                $("#myModal").css("display", "block");
            }
        });
    });

    // Cuando se hace clic en el botón de cerrar del modal
    $(".close").click(function() {
        $("#myModal").css("display", "none");
    });

    // Cuando se hace clic fuera del modal, ciérralo
    $(window).click(function(event) {
        var modal = $("#myModal");
        if (event.target == modal[0]) {
            modal.css("display", "none");
        }
    });
});

</script>
<?php
}
include '../includes/footer.php';
?>