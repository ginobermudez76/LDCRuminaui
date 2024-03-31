<?php
include '../includes/config.php'; // Incluyendo la conexión a la base de datos
include '../includes/header.php';
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

?>
        <div class="container mt-4">
            <h2 class="gestionar">Carta de condolencias</h2>
            <form method="post" id="formCartas" enctype="multipart/form-data" onsubmit="return validarCamposEvento()">
                <div class="mb-3">
                    <label for="descripcion" class="form-label">Mensaje</label>
                    <textarea type="text" class="form-control" id="mensaje" name="mensaje"></textarea>
                </div>
                <div class="mb-3">
                    <label for="imagen" class="form-label">Imagen</label>
                    <input type="file" class="form-control" id="imagen" name="imagen" required></textarea>
                </div>
                <button type="submit" class="btn btn-primary" id="btnEnviar">Mostrar</button>
            </form>
        </div>
        <div class="container">
            <div class="row" id="tablaCartas">
            </div>
        </div>


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
            $("#tablaCartas").load("tablaCartas.php"); //load es una funcion de Jquery
            $(document).ready(function() {


                $('#btnEnviar').click(function() {
                    var formData = new FormData($('#formCartas')[0]);
                    $.ajax({
                        url: 'insertarCarta.php',
                        type: 'POST',
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function(response) {
                            var jsonData = JSON.parse(response);
                            if (jsonData.success) {
                                // Mostrar mensaje de éxito
                                alert(jsonData.message);
                                $("#tablaCartas").load("tablaCartas.php");
                                $("#formCartas")[0].reset();
                            } else {
                                // Mostrar mensaje de error
                                alert(jsonData.message);
                            }
                        }
                    });


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
include '../includes/footer.php'
?>