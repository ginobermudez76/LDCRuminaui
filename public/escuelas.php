<?php

include '../includes/config.php'; // Incluyendo la conexión a la base de datos
include '../includes/header.php'; // Incluyendo la cabecera común

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

<div class="contenedor-galeria">
    <div class="deportes container">
        <div class="deportes-descripcion">
            <h2>Escuelas disponibles de forma permanente</h2>
            <div class="linea"></div>
            <p>Nuestra liga deportiva cuenta con las siguientes escuelas deportivas</p>
        </div>
    </div>

    <?php
    if (empty($deportes)) {
        echo '<p>Aun no se ha agregado nada</p>';
    } else {
        // Dividir el array de imágenes en grupos de dos
        $chunks = array_chunk($deportes, 2);

        // Iterar sobre cada grupo de imágenes
        foreach ($chunks as $grupo) {
            echo '<div class="column-grupo">';
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

<script>
    $(document).ready(function() {
        // Cuando se hace clic en el nombre de deporte
        $(".deporte-nombre").click(function() {
            var deporte_id = $(this).data("deporte-id");
            // Hacer una petición AJAX para obtener las imágenes del carrusel
            $.ajax({
                url: "../otros/obtener_imagenes.php",
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

<?php include '../includes/footer.php' ?>
