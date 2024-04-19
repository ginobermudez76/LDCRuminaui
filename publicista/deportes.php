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
        // Mostrar el elemento del menú para publicista
        //logica para obtener la lista de deportes de la base de datos
        try {
            $stmt = $conn->prepare("SELECT * FROM deportes");
            $stmt->execute();

            $deportes = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
?>
        <div class="container mt-5 mr-5">
            <h2 class="gestionar">Deportes ofertados</h2>
            <button type="button" class="btn btn-primary mb-4" data-bs-toggle="modal" data-bs-target="#agregarDeporteModal">Agregar deporte +</button>
        </div>
        <!-- Modal para agregar dporte -->
        <div class="modal fade" id="agregarDeporteModal" tabindex="-1" aria-labelledby="agregarDeporteModalLabel" aria-hidden="true">
            <div class=" modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="agregarDeporteModalLabel">Agregar deporte</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="formDeporte" autocomplete="off" method="post" enctype="multipart/form-data" onsubmit="return validarCamposInsert()">
                            <div class="mb-3">
                                <label for="nombre" class="form-label">Nombre del Deporte</label>
                                <input type="text" class="form-control" id="nombre" name="nombre" requerid maxlength="100">
                            </div>
                            <div class="mb-3">
                                <label for="descripcion" class="form-label">Descripción</label>
                                <textarea class="form-control" id="descripcion" name="descripcion" rows="3" maxlength="300"></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="imagen" class="form-label">Imagen</label>
                                <input type="file" class="form-control" id="imagen" name="imagen" requerid></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary">Agregar nuevo deporte</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div class="container">
            <div class="row" id="tablaDeportes">
            </div>
        </div>
        <script>
            $("#tablaDeportes").load("tablaDeportes.php");
            $(document).ready(function() {

            });
        </script>
        <?php
        include 'validar.php';
        include 'limitar.php';
        ?>
        <script>
            // Función trim similar a la de PHP en JavaScript
            function trim(str) {
                return str.replace(/^\s+|\s+$/g, '');
            }

            function validarCamposEdit() {
                var nombreDeporte = document.getElementById("nombreEdit").value;

                // Utilizamos la función trim para eliminar espacios en blanco al principio y al final
                nombreDeporte1 = trim(nombreDeporte);

                if (nombreDeporte1 === "") {
                    alert("El deporte debe tener un nombre");
                    return false;
                }
                var archivoInput = document.getElementById("imagenEdit");


                var archivo = archivoInput.files[0];
                var extensionesPermitidas = ['gif', 'png', 'jpg', 'webp', 'jpeg'];
                var extension = archivo.name.split('.').pop().toLowerCase();

                if (!extensionesPermitidas.includes(extension)) {
                    alert("Formato no soportado");
                    return false;
                }

                return true;
            }
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
            // Función para manejar la respuesta del servidor
            function handleResponse(response) {
                if (response.success) {
                    alertify.success(response.message);
                    // Recargar la tabla
                    $('#formDeporte')[0].reset();
                    $("#tablaDeportes").load("tablaDeportes.php");
                } else {
                    alertify.error(response.message);
                }
            }

            $(document).ready(function() {
                // Manejar el envío del formulario
                $('#formDeporte').submit(function(event) {
                    event.preventDefault(); // Evitar el envío del formulario por defecto
                    var formData = new FormData($(this)[0]); // Obtener los datos del formulario
                    $.ajax({
                        url: 'insertarDeporte.php',
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
                var modal = document.getElementById("modalEditDeportes");
                modal.style.display = "block";
            }

            // Función para cerrar el modal
            function closeModal() {
                var modal = document.getElementById("modalEditDeportes");
                modal.style.display = "none";
            }

            // Cierra el modal si se hace clic fuera de él
            window.onclick = function(event) {
                var modal = document.getElementById("modalEditDeportes");
                if (event.target == modal) {
                    modal.style.display = "none";
                }
            }

            // Carga el formulario desde el otro script PHP cuando se abre el modal
            function loadForm(idDeporte) {
                var xhttp = new XMLHttpRequest();
                xhttp.onreadystatechange = function() {
                    if (this.readyState == 4 && this.status == 200) {
                        document.getElementById("formContent").innerHTML = this.responseText;
                        document.getElementById("idDeporteEdit").value = idDeporte; // Establecer el ID del deportita en el formulario
                        openModal(); // Abre el modal después de cargar el contenido
                        // Función para limitar la cantidad de dígitos en el campo de celular
                        document.getElementById('nombreEdit').addEventListener('input', function() {
                            // Obtener el valor actual del campo de celular
                            var deporteNombre = this.value;
                            // Limitar el valor a 100 caracteres
                            if (deporteNombre.length > 100) {
                                this.value = deporteNombre.slice(0, 100);
                            }
                        });
                        // Función para limitar la cantidad de dígitos en el campo de descripcion
                        document.getElementById('descripcionedit').addEventListener('input', function() {
                            // Obtener el valor actual del campo de celular
                            var deporteDescripcion = this.value;
                            // Limitar el valor a 10 caracteres
                            if (deporteDescripcion.length > 300) {
                                this.value = deporteDescripcion.slice(0, 300);
                            }
                        });
                    }
                };
                xhttp.open("GET", "formEditDeporte.php?id=" + idDeporte, true); // Pasar el ID del deporte en la URL
                xhttp.send();
            }
        </script>

        <script>
            function confirmarEliminacion(idDeporte) {
                var confirmacion = confirm("¿Está seguro que desea eliminar este deporte?");

                if (confirmacion) {
                    // Usuario hizo clic en "Aceptar", enviar solicitud a eliminar_deporte.php
                    eliminarDeporte(idDeporte);
                } else {
                    // Usuario hizo clic en "Cancelar", no hacer nada
                }
            }

            function eliminarDeporte(idDeporte) {
                // Utiliza jQuery para enviar una solicitud AJAX a eliminar_evento.php
                $.ajax({
                    type: "POST",
                    url: "eliminarDeporte.php",
                    data: {
                        id: idDeporte
                    },
                    success: function(response) {
                        // Manejar la respuesta, si es necesario
                        console.log(response);
                        alert(response);
                        // Puedes recargar la tabla o actualizar la lista de deportes de alguna manera
                        $("#tablaDeportes").load("tablaDeportes.php");
                    },
                    error: function(error) {
                        // Manejar errores si es necesario
                        alert(response);
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
include '../includes/footer.php'; ?>