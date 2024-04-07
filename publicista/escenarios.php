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
                    <form id="formEscenario" method="post" enctype="multipart/form-data" onsubmit="return validarCamposEscenario()">
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

        <script>
            function trim(str) {
                return str.replace(/^\s+|\s+$/g, '');
            }

            function validarCamposEscenario() {
                var nombreEscenario = document.getElementById("nombre").value;
                nombreEscenario1 = trim(nombreEscenario);
                if (nombreEscenario1 === "") {
                    alert("El escenario debe tener un nombre");
                    return false;
                }
                var ubicacion = document.getElementById("ubicacion").value;
                ubicacion1 = trim(ubicacion);
                if (ubicacion1 === "") {
                    alert("El escenario debe tener un enlace de ubicación");
                    return false;
                }
                var direccion = document.getElementById("direccion").value;
                direccion1 = trim(direccion);
                if (direccion1 === "") {
                    alert("El escenario debe tener la dirección");
                    return false;
                }
                var telefono = document.getElementById("telefono").value;
                telefono1 = trim(telefono);
                if (telefono1 === "") {
                    alert("Debe proporcionar un numero de contacto");
                    return false;
                }
                var supervisor = document.getElementById("supervisor").value;
                supervisor1 = trim(supervisor);
                if (supervisor1 === "") {
                    alert("Debe asignar un encargado para este escenario");
                    return false;
                }
                var celular = document.getElementById("celular").value;
                celular1 = trim(celular);
                if (celular1 === "") {
                    alert("Debe proporcionar el numero de contacto del servidor");
                    return false;
                }
                var archivoInput = document.getElementById("imagen");


                var archivo = archivoInput.files[0];
                var extensionesPermitidas = ['gif', 'png', 'jpg', 'webp', 'jpeg', 'svg'];
                var extension = archivo.name.split('.').pop().toLowerCase();

                if (!extensionesPermitidas.includes(extension)) {
                    alert("Formato no soportado");
                    return false;
                }

                return true;
            }
            // Función para limitar la cantidad de dígitos en el campo de celular
            document.getElementById('nombre').addEventListener('input', function() {
                // Obtener el valor actual del campo de celular
                var escenarioNombre = this.value;
                // Limitar el valor a 100 caracteres
                if (escenarioNombre.length > 100) {
                    this.value = escenarioNombre.slice(0, 100);
                }
            });
            document.getElementById('ubicacion').addEventListener('input', function() {
                // Obtener el valor actual del campo de celular
                var ubicacion = this.value;
                // Limitar el valor a 100 caracteres
                if (ubicacion.length > 5000) {
                    this.value = ubicacion.slice(0, 5000);
                }
            });
            document.getElementById('direccion').addEventListener('input', function() {
                // Obtener el valor actual del campo de celular
                var direccion = this.value;
                // Limitar el valor a 100 caracteres
                if (direccion.length > 500) {
                    this.value = direccion.slice(0, 500);
                }
            });
            document.getElementById('telefono').addEventListener('input', function() {
                // Obtener el valor actual del campo de celular
                var telefono = this.value;
                // Limitar el valor a 100 caracteres
                if (telefono.length > 10) {
                    this.value = telefono.slice(0, 10);
                }
            });
            document.getElementById('celular').addEventListener('input', function() {
                // Obtener el valor actual del campo de celular
                var celular = this.value;
                // Limitar el valor a 100 caracteres
                if (celular.length > 10) {
                    this.value = celular.slice(0, 10);
                }
            });
            document.getElementById('supervisor').addEventListener('input', function() {
                // Obtener el valor actual del campo de celular
                var supervisor = this.value;
                // Limitar el valor a 100 caracteres
                if (supervisor.length > 250) {
                    this.value = supervisor.slice(0, 250);
                }
            });
            document.getElementById('telefono').addEventListener('input', function() {
                // Obtener el valor actual del campo de teléfono
                var telefono = this.value;
                // Quitar todos los caracteres que no sean números
                var numerosTelefono = telefono.replace(/\D/g, '');
                // Actualizar el valor del campo con solo los números
                this.value = numerosTelefono;
            });

            document.getElementById('celular').addEventListener('input', function() {
                // Obtener el valor actual del campo de celular
                var celular = this.value;
                // Quitar todos los caracteres que no sean números
                var numerosCelular = celular.replace(/\D/g, '');
                // Actualizar el valor del campo con solo los números
                this.value = numerosCelular;
            });
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
<?php
    } else {
        echo "<script>window.location.href='../public/index.php';</script>";
        exit();
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
include '../includes/footer.php'; ?>