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
<div class="container-fluid">
<<<<<<< HEAD
    <div class= "container">

    <img class="inicio-img" src="../img/portada.png" alt="¿Quienes somos?">
            <div class="carousel-caption d-none d-md-block text-inicio">
                <h5 class="">Sobre nosotros</h5>
                <p>La ‘Liga Deportiva Cantonal Rumiñahui’ es una institución de derecho privado con finalidad social, es máxima entidad del deporte en el Cantón Rumiñahui, Con Sede en la ciudad de Sangolquí, afiliada a la Concentración Deportiva de Pichincha</p>
            </div>
        </div>       
=======
    <div class="container mt-4">
        <div id="carouselpresentacion" class="carousel" data-bs-ride="carousel-fade" data-bs-interval="5000">
            <div class="carousel-inner">
                <div class="carousel-item active">
                    <img class="d-block " src="../img/portada.png" alt="¿Quienes somos?">
                    <div class="carousel-caption d-none d-md-block">
                        <h5>Sobre nosotros</h5>
                        <p>La ‘Liga Deportiva Cantonal Rumiñahui’ es una institución de derecho privado con finalidad social, es máxima entidad del deporte en el Cantón Rumiñahui, Con Sede en la ciudad de Sangolquí, afiliada a la Concentración Deportiva de Pichincha</p>
                    </div>
                </div>
                <div class="carousel-item">
                    <img class="d-block w-100" src="../img/misiondeportiva.jpg" alt="Misión">
                    <div class="carousel-caption d-none d-md-block">
                        <h5>Misión</h5>
                        <p>Nuestra misión es fomentar el desarrollo integral de la comunidad a través del deporte, promoviendo valores, inclusión y bienestar. Trabajamos arduamente para ofrecer oportunidades deportivas que inspiren el espíritu competitivo y fortalezcan los lazos comunitarios.</p>
                    </div>
                </div>
                <div class="carousel-item">
                    <img class="d-block w-100" src="../img/visiondeportiva.jpg" alt="Visión">
                    <div class="carousel-caption d-none d-md-block">
                        <h5>Visión</h5>
                        <p>Aspiramos a ser un referente regional en el ámbito deportivo, reconocidos por nuestra excelencia, compromiso social y contribución al desarrollo sostenible. Buscamos ser líderes en la promoción de un estilo de vida activo y saludable, impulsando el crecimiento personal y colectivo.</p>
                    </div>
                </div>
            </div>

            <button class="carousel-control-prev" type="button" data-bs-target="#carouselpresentacion" data-bs-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Anterior</span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#carouselpresentacion" data-bs-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Siguiente</span>
            </button>
        </div>
>>>>>>> df2d2ebe8492819d679bd0c25c7b245b891bc51d
    </div>
    <div class="row">
    <?php include '../public/logros.php'; ?>  
    </div>

    <div class="row">
        
        <!-- Carrusel izquierdo -->
        <div class="col-md-6">
        <?php include '../public/deportistas1.php'; ?>
        </div>

        <!-- Espacio vacío entre carruseles -->
        <div class="col-md-2"></div>

        <!-- Carrusel derecho -->
        <div class="col-md-6">
        <?php include '../public/deportistas2.php'; ?>
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
        $chunks = array_chunk($deportes, 2);?>
        <?php foreach ($chunks as $grupo): ?>
        <div class="column">
            <?php foreach ($grupo as $depor): ?>
                <div class="column">
                    <div class="card-img-top">
                        <img src="../uploads/deportes/<?php echo htmlspecialchars(basename($depor['imagen'])); ?>" class="card-img" alt="<?php echo htmlspecialchars($depor['nombre']); ?>">
                        <div class="overlay">
                            <div class="deporte-nombre" data-deporte-id="<?php echo htmlspecialchars($depor['id']); ?>"><?php echo htmlspecialchars($depor['nombre']); ?></div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endforeach; ?>
        
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