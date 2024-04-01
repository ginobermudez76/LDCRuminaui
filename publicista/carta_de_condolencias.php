<?php
include '../includes/config.php'; // Incluyendo la conexión a la base de datos
include '../includes/header.php';
if (!isset($_SESSION['usuario_admin'])) {
    echo "<script>window.location.href='../admin/login.php';</script>";
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

?>
        <div class="container mt-5 mr-5">
            <h2 class="gestionar">Carta de condolencias</h2>
            <button type="button" class="btn btn-primary mb-4" data-bs-toggle="modal" data-bs-target="#agregarCartaModal">Publicar carta de condolencias</button>
        </div>
        <!-- Modal para agregar deportista destacado -->
        <div class="modal fade" id="agregarCartaModal" tabindex="-1" aria-labelledby="agregarDeportistaModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="agregarCartaModalLabel">Mostrar carta de condolencias</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="formCartas" method="post" enctype="multipart/form-data" onsubmit="return validarCamposEvento()">
                            <div class="mb-3">
                                <label for="descripcion" class="form-label">Mensaje</label>
                                <textarea type="text" class="form-control" id="mensaje" name="mensaje"></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="imagen" class="form-label">Imagen</label>
                                <input type="file" class="form-control" id="imagen" name="imagen" required></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary">Mostrar</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="container">
            <div class="row" id="tablaCartas">
            </div>
        </div>
        <script>
            $("#tablaCartas").load("tablaCartas.php"); //load es una funcion de Jquery
        </script>

        <script>
            function validarCamposEvento() {
                var mensajeCon = document.getElementById("mensaje").value;
                if (mensajeCon.trim() === "") {
                    alert("El mensaje no debe estar vacío");
                    return false;
                }

                var archivoInput = document.getElementById("imagen");

                var archivo = archivoInput.files[0];
                var extensionesPermitidas = ['gif', 'png', 'jpg', 'webp', 'jpeg'];
                var extension = archivo.name.split('.').pop().toLowerCase();

                if (!extensionesPermitidas.includes(extension)) {
                    alert("Formato no soportado");
                    return false;
                }

                return true;
            }

            // Función para limitar la cantidad de dígitos en el campo de mensaje
            document.getElementById('mensaje').addEventListener('input', function() {
                // Obtener el valor actual del campo de mensaje
                var mensajeCondolencia = this.value;
                // Limitar el valor a 700 caracteres
                if (mensajeCondolencia.length > 700) {
                    this.value = mensajeCondolencia.slice(0, 700);
                }
            });
        </script>
        <script>
            function deshabilitarInputImagen() {
                var checkbox = document.getElementById("checkDImagen");
                var inputImagen = document.getElementById("imagenEdit");

                if (checkbox.checked) {
                    inputImagen.disabled = true;
                } else {
                    inputImagen.disabled = false;
                }
            }

            function deshabilitarCheckbox() {
                var checkbox = document.getElementById("checkDImagen");
                var inputImagen = document.getElementById("imagenEdit");

                if (inputImagen.value) {
                    checkbox.disabled = true;
                } else {
                    checkbox.disabled = false;
                }
            }
        </script>
        <script>
            function validarCamposEdit() {

                var nombredeporteEdit = document.getElementById("mensajeEdit").value;
                if (nombredeporteEdit === "") {
                    alert("El mensaje no puede quedar vacio");
                    return false;
                }

                var imagenInputEdit = document.getElementById("imagenEdit");
                var imagen = imagenInputEdit.files[0];
                var extensionesPermitidas = ['gif', 'png', 'jpg', 'webp', 'jpeg', 'svg'];
                var extension = imagen.name.split('.').pop().toLowerCase();

                if (!extensionesPermitidas.includes(extension)) {
                    alert("Formato no soportado");
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
                $('#formCartas').submit(function(event) {
                    event.preventDefault(); // Evitar el envío del formulario por defecto
                    var formData = new FormData($(this)[0]); // Obtener los datos del formulario
                    $.ajax({
                        url: 'insertarCarta.php',
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
        echo "<script>window.location.href='../public/index.php';</script>";
        exit();
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
include '../includes/footer.php'
?>