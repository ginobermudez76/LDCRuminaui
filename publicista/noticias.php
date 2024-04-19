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
                        <form id="formNoticias" autocomplete="off" method="post" enctype="multipart/form-data" onsubmit="return validarCamposInsert()">
                            <div class="mb-3">
                                <label for="titulo" class="form-label">Titulo</label>
                                <input type="text" class="form-control" id="titulo" name="titulo"></input>
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
        <?php include 'validar.php';
        include 'limitar.php';
        ?>
        <script>
            $("#tablaNoticias").load("tablaNoticias.php"); //load es una funcion de Jquery
            $(document).ready(function() {});
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
                <script>
    // Función para abrir el modal
    function openModal() {
        var modal = document.getElementById("modalEditNoticias");
        modal.style.display = "block";
    }

    // Función para cerrar el modal
    function closeModal() {
        var modal = document.getElementById("modalEditNoticias");
        modal.style.display = "none";
    }

    // Cierra el modal si se hace clic fuera de él
    window.onclick = function(event) {
        var modal = document.getElementById("modalEditNoticias");
        if (event.target == modal) {
            modal.style.display = "none";
        }
    }

    // Carga el formulario desde el otro script PHP cuando se abre el modal
    function loadForm(idNoticia) {
    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            document.getElementById("formContent").innerHTML = this.responseText;
            document.getElementById("idNoticiaEdit").value = idNoticia; // Establecer el ID de la noticia en el formulario
            openModal(); // Abre el modal después de cargar el contenido
                            // Función para limitar la cantidad de dígitos en el campo de celular
    document.getElementById('tituloEdit').addEventListener('input', function() {
        // Obtener el valor actual del campo de celular
        var titulo = this.value;
        // Limitar el valor a 100 caracteres
        if (titulo.length > 100) {
            this.value = titulo.slice(0, 100);
        }
    });
    document.getElementById('cuerpoEdit').addEventListener('input', function() {
        // Obtener el valor actual del campo de celular
        var cuerpo = this.value;
        // Limitar el valor a 100 caracteres
        if (cuerpo.length > 5000) {
            this.value = cuerpo.slice(0, 5000);
        }
    });
        }
    };
    xhttp.open("GET", "formEditNoticia.php?id=" + idNoticia, true); // Pasar el ID de la noticia en la URL
    xhttp.send();
}

</script>
<script>
        function trim(str) {
        return str.replace(/^\s+|\s+$/g, '');
    }
            function validarCamposEdit() {

                var tituloNoticia = document.getElementById("tituloEdit").value;
                tituloNoticia1 = trim(tituloNoticia);
                if (tituloNoticia1 === "") {
                    alert("El titulo no puede quedar vacio.");
                    return false;
                }
                var cuerpoNoticia = document.getElementById("cuerpoEdit").value;
                cuerpoNoticia1 = trim(cuerpoNoticia);
                if (cuerpoNoticia1 === "") {
                    alert("La noticia debe tener un cuerpo.");
                    return false;
                }
                return true;
            }
        </script>
        <script>
            function confirmarEliminacion(idNoticia) {
                var confirmacion = confirm("¿Está seguro que desea eliminar. Esta acción no se puede deshacer.?");

                if (confirmacion) {
                    // Usuario hizo clic en "Aceptar", enviar solicitud a eliminarNoticia.php
                    eliminarNoticia(idNoticia);
                } else {
                    // Usuario hizo clic en "Cancelar", no hacer nada
                }
            }

            function eliminarNoticia(idNoticia) {
                // Utiliza jQuery para enviar una solicitud AJAX a eliminarNoticia.php
                $.ajax({
                    type: "POST",
                    url: "eliminarNoticia.php",
                    data: {
                        id: idNoticia
                    },
                    success: function(response) {
                        // Manejar la respuesta, si es necesario
                        console.log(response);

                        // Puedes recargar la página o actualizar la lista de noticias de alguna manera
                        $("#tablaNoticias").load("tablaNoticias.php");
                    },
                    error: function(error) {
                        // Manejar errores si es necesario
                        console.error(error);
                    }
                });
            }
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