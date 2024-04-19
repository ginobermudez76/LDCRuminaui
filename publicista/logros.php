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
                        <form id="formLogros" autocomplete="off" method="post" enctype="multipart/form-data" onsubmit="return validarCamposInsert()">
                            <div class="mb-3">
                                <label for="Nombre" class="form-label">Nombre del logro</label>
                                <input type="text" class="form-control" id="nombre" name="nombre" required maxlength="100"></input>
                            </div>
                            <div class="mb-3">
                                <label for="Tipo" class="form-label">Tipo</label>
                                <select class="form-control" id="tipoLogro" name="tipoLogro" required>
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
        <?php
        include 'validar.php';
        include 'limitar.php';
        ?>        
        <script>
            $("#tablaLogros").load("tablaLogros.php");
            $(document).ready(function() {

});
        </script>

        <script>
            // Función para manejar la respuesta del servidor
            function handleResponse(response) {
                if (response.success) {
                    alertify.success(response.message);
                    // Recargar la página después de 1.5 segundos
                    $('#formLogros')[0].reset();
                    $("#tablaLogros").load("tablaLogros.php");
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
        <script>
    // Función para abrir el modal
    function openModal() {
        var modal = document.getElementById("modalEditLogros");
        modal.style.display = "block";
    }

    // Función para cerrar el modal
    function closeModal() {
        var modal = document.getElementById("modalEditLogros");
        modal.style.display = "none";
    }

    // Cierra el modal si se hace clic fuera de él
    window.onclick = function(event) {
        var modal = document.getElementById("modalEditLogros");
        if (event.target == modal) {
            modal.style.display = "none";
        }
    }

    // Carga el formulario desde el otro script PHP cuando se abre el modal
    function loadForm(idLogro) {
    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            document.getElementById("formContent").innerHTML = this.responseText;
            document.getElementById("idLogroEdit").value = idLogro; // Establecer el ID del logro en el formulario
            openModal(); // Abre el modal después de cargar el contenido
            document.getElementById('nombreEdit').addEventListener('input', function() {
                // Obtener el valor actual del campo de celular
                var nombre = this.value;
                // Limitar el valor a 100 caracteres
                if (nombre.length > 100) {
                    this.value = nombre.slice(0, 100);
                }
            });
        }
    };
    xhttp.open("GET", "formEditLogro.php?id=" + idLogro, true); // Pasar el ID del logro en la URL
    xhttp.send();
}

</script>
<script>
      function trim(str) {
        return str.replace(/^\s+|\s+$/g, '');
    }  
            function validarCamposEdit() {

                var nombreLogro = document.getElementById("tituloEdit").value;
                nombreLogro1 = trim(nombreLogro);
                if (nombreLogro1 === "") {
                    alert("El titulo no puede quedar vacio");
                    return false;
                }
                var tipoLogro = document.getElementById("tipoLogroEdit").value;
                if (tipoLogro === "") {
                    alert("Seleccione un tipo de logro");
                    return false;
                }
                var nombredeporte = document.getElementById("deporte_idEdit").value;
                if (nombredeporte === "") {
                    alert("El deporte no puede quedar vacio");
                    return false;
                }
                return true;
            }
        </script>
        <script>
            function confirmarEliminacion(idLogro) {
                var confirmacion = confirm("¿Está seguro que desea eliminar. Esta acción no se puede deshacer.?");

                if (confirmacion) {
                    // Usuario hizo clic en "Aceptar", enviar solicitud a eliminarLogro.php
                    eliminarLogro(idLogro);
                } else {
                    // Usuario hizo clic en "Cancelar", no hacer nada
                }
            }

            function eliminarLogro(idLogro) {
                // Utiliza jQuery para enviar una solicitud AJAX a eliminarLogro.php
                $.ajax({
                    type: "POST",
                    url: "eliminarLogro.php",
                    data: {
                        id: idLogro
                    },
                    success: function(response) {
                        // Manejar la respuesta, si es necesario
                        console.log(response);

                        // Puedes recargar la página o actualizar la lista de logros de alguna manera
                        $("#tablaLogros").load("tablaLogros.php");
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