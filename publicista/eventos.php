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
        $stmt = $conn->prepare("SELECT rol FROM usuarios WHERE id = :usuario_id");
        $stmt->bindParam(':usuario_id', $usuario_id, PDO::PARAM_INT);
        $stmt->execute();
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
            <h2 class="gestionar">Gestionar eventos</h2>
            <button type="button" class="btn btn-primary mb-4" data-bs-toggle="modal" data-bs-target="#agregarEventoModal">Agregar evento +</button>
        </div>
        <!-- Modal para agregar dporte -->
        <div class="modal fade" id="agregarEventoModal" tabindex="-1" aria-labelledby="agregarEventoModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="agregarEventoModalLabel">Agregar deporte</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form action="insertarEvento.php" method="post" enctype="multipart/form-data" onsubmit="return validarCamposEvento()">
                            <div class="mb-3">
                                <label for="nombre" class="form-label">Nombre</label>
                                <input type="text" class="form-control" id="nombre" name="nombre" required>
                            </div>
                            <div class="mb-3">
                                <label for="descripcion" class="form-label">Descripción</label>
                                <textarea type="text" class="form-control" id="descripcion" name="descripcion"></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="deporte_id" class="form-label">Deporte</label>

                                <select class="form-control" id="deporte_id" name="deporte_id" required>
                                    <option value="">Seleccione un deporte</option>
                                    <?php foreach ($deportes as $deporte) : ?>
                                        <option value="<?php echo $deporte['id']; ?>"><?php echo htmlspecialchars($deporte['nombre']); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="nombre" class="form-label">Inicio</label>
                                <input type="date" class="form-control" id="fecha_ini" name="fecha_ini" requerid>
                            </div>
                            <div class="mb-3">
                                <label for="nombre" class="form-label">Fin</label>
                                <input type="date" class="form-control" id="fecha_f" name="fecha_f" requerid>
                            </div>
                            <div class="mb-3">
                                <label for="imagen" class="form-label">Imagen principal</label>
                                <input type="file" class="form-control" id="imagen" name="imagen" requerid></textarea>
                            </div>

                            <button type="submit" class="btn btn-primary">Publicar evento</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div class="container">
            <div class="row" id="tablaEventos">
            </div>
        </div>
        <script>
            $("#tablaEventos").load("tablaEventos.php");
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
            function confirmarEliminacion(idEvento) {
                var confirmacion = confirm("¿Está seguro que desea eliminar este evento?");

                if (confirmacion) {
                    // Usuario hizo clic en "Aceptar", enviar solicitud a eliminar_evento.php
                    eliminarEvento(idEvento);
                } else {
                    // Usuario hizo clic en "Cancelar", no hacer nada
                }
            }

            function eliminarEvento(idEvento) {
                // Utiliza jQuery para enviar una solicitud AJAX a eliminar_evento.php
                $.ajax({
                    type: "POST",
                    url: "eliminar_evento.php",
                    data: {
                        id: idEvento
                    },
                    success: function(response) {
                        // Manejar la respuesta, si es necesario
                        console.log(response);
                        alert(response);
                        // Puedes recargar la página o actualizar la lista de productos de alguna manera
                        location.reload();
                    },
                    error: function(error) {
                        alert(response);
                        // Manejar errores si es necesario
                        console.error(error);
                    }
                });
            }
        </script>

        <script>
            function validarCamposEvento() {
                // Validación de selección de tipo
                var deporteSeleccionado = document.getElementById("deporte_id").value;
                if (deporteSeleccionado === "") {
                    alert("Por favor, seleccione un deporte");
                    return false;
                }
                var nombreEvento = document.getElementById("nombre").value;
                if (nombreEvento === "") {
                    alert("El evento debe tener un nombre");
                    return false;
                }

                // Validación de la fecha de inicio
                var fechaInicio = document.getElementById("fecha_ini").value;
                var fechaInicioArray = fechaInicio.split("-");
                if (fechaInicioArray.length !== 3) {
                    alert("Por favor, introduzca una fecha de inicio válida.");
                    return false;
                }
                var yearInicio = fechaInicioArray[0];
                var monthInicio = fechaInicioArray[1];
                var dayInicio = fechaInicioArray[2];

                // Verificar si el año tiene 4 dígitos
                if (yearInicio.length !== 4 || isNaN(yearInicio)) {
                    alert("Por favor, introduzca un año entre 0001 y 9999 en la fecha de inicio.");
                    return false;
                }

                // Crear objeto de fecha de inicio y verificar si es válida
                var fechaInicioObjeto = new Date(yearInicio, monthInicio - 1, dayInicio);
                if (isNaN(fechaInicioObjeto.getTime())) {
                    alert("Por favor, introduzca una fecha de inicio válida.");
                    return false;
                }

                // Validación de la fecha de finalización
                var fechaFin = document.getElementById("fecha_f").value;
                var fechaFinArray = fechaFin.split("-");
                if (fechaFinArray.length !== 3) {
                    alert("Por favor, introduzca una fecha de finalización válida.");
                    return false;
                }
                var yearFin = fechaFinArray[0];
                var monthFin = fechaFinArray[1];
                var dayFin = fechaFinArray[2];

                // Verificar si el año tiene 4 dígitos
                if (yearFin.length !== 4 || isNaN(yearFin)) {
                    alert("Por favor, introduzca un año entre 0001 y 9999 en la fecha de finalización.");
                    return false;
                }

                // Crear objeto de fecha de finalización y verificar si es válida
                var fechaFinObjeto = new Date(yearFin, monthFin - 1, dayFin);
                if (isNaN(fechaFinObjeto.getTime())) {
                    alert("Por favor, introduzca una fecha de finalización válida.");
                    return false;
                }

                // Validación de la fecha de inicio
                var fechaInicio = document.getElementById("fecha_ini").value;
                var fechaInicioObjeto = new Date(fechaInicio);
                if (isNaN(fechaInicioObjeto.getTime())) {
                    alert("Por favor, introduzca una fecha de inicio válida.");
                    return false;
                }

                // Validación de la fecha de finalización
                var fechaFin = document.getElementById("fecha_f").value;
                var fechaFinObjeto = new Date(fechaFin);
                if (isNaN(fechaFinObjeto.getTime())) {
                    alert("Por favor, introduzca una fecha de finalización válida.");
                    return false;
                }

                // Verificar que la fecha de finalización no sea menor que la fecha de inicio
                if (fechaFinObjeto < fechaInicioObjeto) {
                    alert("La fecha de finalización no puede ser menor que la fecha de inicio.");
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
            // Función para limitar la cantidad de dígitos en el campo de celular
            document.getElementById('nombre').addEventListener('input', function() {
                // Obtener el valor actual del campo de celular
                var deporteNombre = this.value;
                // Limitar el valor a 100 caracteres
                if (deporteNombre.length > 100) {
                    this.value = deporteNombre.slice(0, 100);
                }
            });
            // Función para limitar la cantidad de dígitos en el campo de descripcion
            document.getElementById('descripcion').addEventListener('input', function() {
                // Obtener el valor actual del campo de celular
                var deporteDescripcion = this.value;
                // Limitar el valor a 10 caracteres
                if (deporteDescripcion.length > 300) {
                    this.value = deporteDescripcion.slice(0, 300);
                }
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
include '../includes/footer.php'; ?>