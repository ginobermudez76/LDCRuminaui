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
        //logica para obtener la lista de escenarios de la base de datos
        try {
            $stmt = $conn->prepare("SELECT * FROM escenarios");
            $stmt->execute();

            $escenarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
?>
        <div class="container mt-5 mr-5">
            <h2 class="gestionar">Escenarios ofertados</h2>
            <button type="button" class="btn btn-primary mb-4" data-bs-toggle="modal" data-bs-target="#agregarEscenarioModal">Agregar escenario +</button>
        </div>
        <!-- Modal para agregar escenario -->
        <div class="modal fade" id="agregarEscenarioModal" tabindex="-1" aria-labelledby="agregarEscenarioModalLabel" aria-hidden="true"">
    <div class=" modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="agregarEscenarioModalLabel">Agregar escenario</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="formEscenario" method="post" enctype="multipart/form-data" onsubmit="return validarCamposInsert()">
                        <div class="mb-3">
                            <label for="nombre" class="form-label">Nombre del Escenario</label>
                            <input type="text" class="form-control" id="nombre" name="nombre" required maxlength="100">
                        </div>
                        <div class="mb-3">
                            <label for="ubicacion" class="form-label">Ubicación (enlace de mapa de la ubicación en google maps)</label>
                            <textarea class="form-control" id="ubicacion" name="ubicacion" rows="3" required maxlength="5000"></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="direccion" class="form-label">Dirección</label>
                            <input type="text" class="form-control" id="direccion" name="direccion" required maxlength="500">
                        </div>
                        <div class="mb-3">
                            <label for="telefono" class="form-label">Teléfono</label>
                            <input type="text" class="form-control" id="telefono" name="telefono" required maxlength="10">
                        </div>
                        <div class="mb-3">
                            <label for="supervisor" class="form-label">Supervisor</label>
                            <input type="text" class="form-control" id="supervisor" name="supervisor" required maxlength="250">
                        </div>
                        <div class="mb-3">
                            <label for="celular" class="form-label">Celular del supervisor</label>
                            <input type="text" class="form-control" id="celular" name="celular" required maxlength="10">
                        </div>
                        <div class="mb-3">
                            <label for="imagen" class="form-label">Imagen</label>
                            <input type="file" class="form-control" id="imagen" name="imagen" required></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">Agregar nuevo escenario</button>
                    </form>
                </div>
            </div>
        </div>
        </div>
        <div class="container">
            <div class="row" id="tablaEscenarios">
            </div>
        </div>

        <script>
            $("#tablaEscenarios").load("tablaEscenarios.php");
            $(document).ready(function() {

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

<?php include 'validar.php';
include 'limitar.php';
?>
        <script>
            // Función para manejar la respuesta del servidor
            function handleResponse(response) {
                if (response.success) {
                    alertify.success(response.message);
                    // Recargar la página después de 1.5 segundos
                    $('#formEscenario')[0].reset();
                    $("#tablaEscenarios").load("tablaEscenarios.php");
                } else {
                    alertify.error(response.message);
                }
            }

            $(document).ready(function() {
                // Manejar el envío del formulario
                $('#formEscenario').submit(function(event) {
                    event.preventDefault(); // Evitar el envío del formulario por defecto
                    var formData = new FormData($(this)[0]); // Obtener los datos del formulario
                    $.ajax({
                        url: 'insertarEscenario.php',
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
                    function trim(str) {
                        return str.replace(/^\s+|\s+$/g, '');
                    }

                    function validarCamposEdit() {
                        var nombreEscenario = trim(document.getElementById("nombreEdit").value);
                        var ubicacionEscenario = trim(document.getElementById("ubicacionEdit").value);
                        var direccionEscenario = trim(document.getElementById("direccionEdit").value);
                        var telefonoEscenario = trim(document.getElementById("telefonoEdit").value);
                        var supervisorEscenario = trim(document.getElementById("supervisorEdit").value);
                        var celularEscenario = trim(document.getElementById("celularEdit").value);

                        if (nombreEscenario === "" || ubicacionEscenario === "" || direccionEscenario === "" || telefonoEscenario === "" || supervisorEscenario === "" || celularEscenario === "") {
                            alert("Todos los campos son obligatorios");
                            return false;
                        }

                        var archivoInput = document.getElementById("imagenEdit");
                        var archivo = archivoInput.files[0];
                        if (archivo && !(/\.(jpg|jpeg|png|gif)$/i).test(archivo.name)) {
                            alert("Formato de imagen no válido");
                            return false;
                        }
                        return true;
                    }
                </script>
            </div>
        </div>
        <script>
            // Función para abrir el modal
            function openModal() {
                var modal = document.getElementById("modalEditEscenarios");
                modal.style.display = "block";
            }

            // Función para cerrar el modal
            function closeModal() {
                var modal = document.getElementById("modalEditEscenarios");
                modal.style.display = "none";
            }

            // Cierra el modal si se hace clic fuera de él
            window.onclick = function(event) {
                var modal = document.getElementById("modalEditEscenarios");
                if (event.target == modal) {
                    modal.style.display = "none";
                }
            }

            // Carga el formulario desde el otro script PHP cuando se abre el modal
            function loadForm(idEscenario) {
                var xhttp = new XMLHttpRequest();
                xhttp.onreadystatechange = function() {
                    if (this.readyState == 4 && this.status == 200) {
                        document.getElementById("formContent").innerHTML = this.responseText;
                        document.getElementById("idEscenarioEdit").value = idEscenario; // Establecer el ID del escenario en el formulario
                        openModal(); // Abre el modal después de cargar el contenido
                                    // Función para limitar la cantidad de dígitos en el campo de celular
            document.getElementById('nombreEdit').addEventListener('input', function() {
                // Obtener el valor actual del campo de celular
                var escenarioNombre = this.value;
                // Limitar el valor a 100 caracteres
                if (escenarioNombre.length > 100) {
                    this.value = escenarioNombre.slice(0, 100);
                }
            });
            document.getElementById('ubicacionEdit').addEventListener('input', function() {
                // Obtener el valor actual del campo de celular
                var ubicacion = this.value;
                // Limitar el valor a 100 caracteres
                if (ubicacion.length > 5000) {
                    this.value = ubicacion.slice(0, 5000);
                }
            });
            document.getElementById('direccionEdit').addEventListener('input', function() {
                // Obtener el valor actual del campo de celular
                var direccion = this.value;
                // Limitar el valor a 100 caracteres
                if (direccion.length > 500) {
                    this.value = direccion.slice(0, 500);
                }
            });
            document.getElementById('telefonoEdit').addEventListener('input', function() {
                // Obtener el valor actual del campo de celular
                var telefono = this.value;
                // Limitar el valor a 100 caracteres
                if (telefono.length > 10) {
                    this.value = telefono.slice(0, 10);
                }
            });
            document.getElementById('celularEdit').addEventListener('input', function() {
                // Obtener el valor actual del campo de celular
                var celular = this.value;
                // Limitar el valor a 100 caracteres
                if (celular.length > 10) {
                    this.value = celular.slice(0, 10);
                }
            });
            document.getElementById('supervisorEdit').addEventListener('input', function() {
                // Obtener el valor actual del campo de celular
                var supervisor = this.value;
                // Limitar el valor a 100 caracteres
                if (supervisor.length > 250) {
                    this.value = supervisor.slice(0, 250);
                }
            });
            document.getElementById('telefonoEdit').addEventListener('input', function() {
                // Obtener el valor actual del campo de teléfono
                var telefono = this.value;
                // Quitar todos los caracteres que no sean números
                var numerosTelefono = telefono.replace(/\D/g, '');
                // Actualizar el valor del campo con solo los números
                this.value = numerosTelefono;
            });

            document.getElementById('celularEdit').addEventListener('input', function() {
                // Obtener el valor actual del campo de celular
                var celular = this.value;
                // Quitar todos los caracteres que no sean números
                var numerosCelular = celular.replace(/\D/g, '');
                // Actualizar el valor del campo con solo los números
                this.value = numerosCelular;
            });
                    }
                };
                xhttp.open("GET", "formEditEscenario.php?id=" + idEscenario, true); // Pasar el ID del escenario en la URL
                xhttp.send();
            }
        </script>
        <script>
            function confirmarEliminacion(idEscenario) {
                var confirmacion = confirm("¿Está seguro que desea eliminar este escenario?");

                if (confirmacion) {
                    // Usuario hizo clic en "Aceptar", enviar solicitud a eliminar_escenario.php
                    eliminarEscenario(idEscenario);
                } else {
                    // Usuario hizo clic en "Cancelar", no hacer nada
                }
            }

            function eliminarEscenario(idEscenario) {
                // Utiliza jQuery para enviar una solicitud AJAX a eliminar_escenario.php
                $.ajax({
                    type: "POST",
                    url: "eliminarEscenario.php",
                    data: {
                        id: idEscenario
                    },
                    success: function(response) {
                        // Manejar la respuesta, si es necesario
                        console.log(response);
                        alert(response);
                        // Puedes recargar la página o actualizar la lista de escenarios de alguna manera
                        $("#tablaEscenarios").load("tablaEscenarios.php");
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