<?php

include '../includes/config.php'; //incluyendo la conexión de la base de datos
include '../includes/header.php'; //incluyendo la cabecera común

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
            <h2 class="gestionar">Noticias</h2>
            <button type="button" class="btn btn-primary mb-4" data-bs-toggle="modal" data-bs-target="#agregarNoticiaModal">Publicar una noticia</button>
        </div>
        <!-- Modal para agregar deportista destacado -->
        <div class="modal fade" id="agregarNoticiaModal" tabindex="-1" aria-labelledby="agregarNoticiaModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="agregarNoticiaModalLabel">Publicar noticia</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="formNoticias" autocomplete="off" method="post" enctype="multipart/form-data" onsubmit="return validarCamposNoticias()">
                            <div class="mb-3">
                                <label for="titulo" class="form-label">Titulo</label>
                                <input type="text" class="form-control" id="titulo" name="titulo" required maxlength="100"></input>
                            </div>
                            <div class="mb-3">
                                <label for="imagen" class="form-label">Imagen</label>
                                <input type="file" class="form-control" id="imagen" name="imagen" required></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="cuerpo" class="form-label">Cuerpo de la noticia</label>
                                <textarea class="form-control" id="cuerpo" name="cuerpo" rows="3" required maxlength="5000"></textarea>
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
            <div class="row" id="tablaNoticias">
            </div>
        </div>
        <script>
            function validarCamposNoticias() {

                var tituloNoticia = document.getElementById("titulo").value;
                if (tituloNoticia === "") {
                    alert("El titulo no puede quedar vacio.");
                    return false;
                }
                var cuerpoNoticia = document.getElementById("cuerpo").value;
                if (cuerpoNoticia === "") {
                    alert("La noticia debe tener un cuerpo.");
                    return false;
                }
                return true;
            }
                // Función para limitar la cantidad de dígitos en el campo de celular
    document.getElementById('titulo').addEventListener('input', function() {
        // Obtener el valor actual del campo de celular
        var titulo = this.value;
        // Limitar el valor a 100 caracteres
        if (titulo.length > 100) {
            this.value = titulo.slice(0, 100);
        }
    });
    document.getElementById('cuerpo').addEventListener('input', function() {
        // Obtener el valor actual del campo de celular
        var cuerpo = this.value;
        // Limitar el valor a 100 caracteres
        if (cuerpo.length > 5000) {
            this.value = cuerpo.slice(0, 5000);
        }
    });
        </script>
        <script>
            $("#tablaNoticias").load("tablaNoticias.php"); //load es una funcion de Jquery
            $(document).ready(function() {

            });
        </script>
        <script>
            // Función para manejar la respuesta del servidor
            function handleResponse(response) {
                if (response.success) {
                    alertify.success(response.message);
                    // Recargar la página después de 1.5 segundos
                    $('#formNoticias')[0].reset();
                    $("#tablaNoticias").load("tablaNoticias.php");
                } else {
                    alertify.error(response.message);
                }
            }

            $(document).ready(function() {
                // Manejar el envío del formulario
                $('#formNoticias').submit(function(event) {
                    event.preventDefault(); // Evitar el envío del formulario por defecto
                    var formData = new FormData($(this)[0]); // Obtener los datos del formulario
                    $.ajax({
                        url: 'insertarNoticia.php',
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
include '../includes/footer.php';
?>