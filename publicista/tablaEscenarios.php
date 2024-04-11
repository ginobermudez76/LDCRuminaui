<?php
session_start();
include '../includes/config.php'; //incluyendo la conexión a la base de datos

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
        <div class="container mt-4">
            <div class="table-responsive">
            <table class="table">
    <thead>
        <tr>
            <th>Id</th>
            <th>Imagen</th>
            <th>Nombre</th>
            <th>Ubicación</th>
            <th>Dirección</th>
            <th>Teléfono</th>
            <th>Supervisor</th>
            <th>Celular</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($escenarios as $escenario) : ?>
            <tr>
                <td><?php echo htmlspecialchars($escenario['id']); ?></td>
                <td>
                    <?php if (isset($escenario['imagen']) && $escenario['imagen']) : ?>
                        <img src="<?php echo htmlspecialchars($escenario['imagen']); ?>" alt="<?php echo htmlspecialchars($escenario['nombre']); ?>" style="width: 100px; height: auto;">
                    <?php else : ?>
                        <p>Sin Imagen</p>
                    <?php endif; ?>
                </td>
                <td><?php echo empty($escenario['nombre']) ? 'No se proporcionó nombre' : htmlspecialchars($escenario['nombre']); ?></td>
                <td><?php echo empty($escenario['ubicacion']) ? 'No se proporcionó ubicación' : htmlspecialchars($escenario['ubicacion']); ?></td>
                <td><?php echo empty($escenario['direccion']) ? 'No se proporcionó dirección' : htmlspecialchars($escenario['direccion']); ?></td>
                <td><?php echo empty($escenario['telefono']) ? 'No se proporcionó teléfono' : htmlspecialchars($escenario['telefono']); ?></td>
                <td><?php echo empty($escenario['supervisor']) ? 'No se proporcionó supervisor' : htmlspecialchars($escenario['supervisor']); ?></td>
                <td><?php echo empty($escenario['celular']) ? 'No se proporcionó celular' : htmlspecialchars($escenario['celular']); ?></td>
                <td>
                    <button type="button" class="btn btn-secondary btn-sm" onclick="loadForm(<?php echo $escenario['id']; ?>)">Editar</button>
                    <button class="btn btn-danger btn-sm" onclick="confirmarEliminacion(<?php echo $escenario['id']; ?>)">Eliminar</button>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

            </div>
        </div>

        <div id="modalEditEscenarios" class="modal edit">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editarEscenarioModalLabel">Editar escenario</h5>
                </div>
                <div id="formContent"></div>
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
        <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
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
?>