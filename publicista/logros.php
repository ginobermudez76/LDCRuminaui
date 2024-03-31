<?php

include '../includes/config.php'; //incluyendo la conexión de la base de datos
include '../includes/header.php'; //incluyendo la cabecera común

if (!isset($_SESSION['usuario_admin'])) {
    header("Location: ../admin/login.php");
    exit();
}
$usuario_id = $_SESSION['usuario_id'];

try {
    // Consultar el rol del usuario en la base de datos
    $stmt = $conn->prepare("SELECT rol FROM usuarios WHERE id = :usuario_id");
    $stmt->bindParam(':usuario_id', $usuario_id, PDO::PARAM_INT);
    $stmt->execute();

    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    // Verificar si el usuario tiene el rol de Publicista
    if ($usuario['rol'] == 7) {
        // Mostrar el elemento del menú Administrar
        // Obtener la lista de tipo de deportes
        try {
            $stmt = $conn->prepare("SELECT id, nombre FROM deportes");
            $stmt->execute();
            $deportes = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
?>
        <div class="container mt-5 mr-5">
            <h2 class="gestionar">Logros</h2>
            <button type="button" class="btn btn-primary mb-4" data-bs-toggle="modal" data-bs-target="#agregarLogroModal">Agregar logro</button>
        </div>
        <!-- Modal para agregar deportista destacado -->
        <div class="modal fade" id="agregarLogroModal" tabindex="-1" aria-labelledby="agregarLogroModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="agregarLogroModalLabel">Agregar logro +</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="formLogros" method="post" enctype="multipart/form-data" onsubmit="return validarCampos()">
                            <div class="mb-3">
                                <label for="Nombre" class="form-label">Nombre del logro</label>
                                <input type="text" class="form-control" id="nombre" name="nombre"></input>
                            </div>
                            <div class="mb-3">
                                <label for="Tipo" class="form-label">Tipo</label>
                                <select class="form-control" id="tipoLogro" name="tipoLogro">
                                    <option value="">Seleccione un tipo</option>
                                    <option value="Medalla">Medalla</option>
                                    <option value="Copa">Copa</option>
                                    <option value="Reconocimiento">Reconocimiento</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="imagen" class="form-label">Imagen</label>
                                <input type="file" class="form-control" id="imagen" name="imagen" required></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="deporte" class="form-label">Deporte</label>
                                <select class="form-control" id="deporte_id" name="deporte_id">
                                    <option value="">Seleccione un deporte</option>
                                    <?php foreach ($deportes as $deporte) : ?>
                                        <option value="<?php echo $deporte['id']; ?>"><?php echo htmlspecialchars($deporte['nombre']); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="mb-3">
                                <button type="submit" class="btn btn-primary">Publicar</button>
                            </div>

                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div class="container">
            <div class="row" id="tablaLogros">
            </div>
        </div>
        <script>
            $("#tablaLogros").load("tablaLogros.php");
        </script>
        <script>
            function validarCampos() {

                var nombreEvento = document.getElementById("nombre").value;
                if (nombreEvento === "") {
                    alert("El nombre no puede quedar vacio");
                    return false;
                }
                var nombreEvento = document.getElementById("tipoLogro").value;
                if (nombreEvento === "") {
                    alert("Seleccione un tipo de logro");
                    return false;
                }
                var nombredeporte = document.getElementById("deporte_id").value;
                if (nombredeporte === "") {
                    alert("El deporte no puede quedar vacio");
                    return false;
                }
                return true;
            }
        </script>

        <script>
            // Función para manejar la respuesta del servidor
            function handleResponse(response) {
                if (response.success) {
                    alertify.success(response.message);
                    // Recargar la página después de 1.5 segundos
                    setTimeout(function() {
                        location.reload();
                    }, 1500);
                } else {
                    alertify.error(response.message);
                }
            }

            $(document).ready(function() {
                // Manejar el envío del formulario
                $('#formLogros').submit(function(event) {
                    event.preventDefault(); // Evitar el envío del formulario por defecto
                    var formData = new FormData($(this)[0]); // Obtener los datos del formulario
                    $.ajax({
                        url: 'insertarLogro.php',
                        type: 'POST',
                        data: formData,
                        async: false,
                        success: function(response) {
                            handleResponse(JSON.parse(response));
                        },
                        cache: false,
                        contentType: false,
                        processData: false
                    });
                    return false;
                });
            });
        </script>

<?php
    } else {
        header("Location: ../public/index.php");
        exit();
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
include '../includes/footer.php';
?>