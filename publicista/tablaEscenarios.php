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
                            <th>Galería de imágenes</th>
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
                                <td><?php echo htmlspecialchars($escenario['nombre']); ?></td>
                                <td><?php echo htmlspecialchars($escenario['ubicacion']); ?></td>
                                <td><?php echo htmlspecialchars($escenario['direccion']); ?></td>
                                <td><?php echo htmlspecialchars($escenario['telefono']); ?></td>
                                <td><?php echo htmlspecialchars($escenario['supervisor']); ?></td>
                                <td><?php echo htmlspecialchars($escenario['celular']); ?></td>
                                <td>
                                    <a href="galeria_de_imagenes.php?id=<?php echo $escenario['id']; ?>&nombre=<?php echo urlencode($escenario['nombre']); ?>&tipo=Escenario" class="btn btn-secondary btn-sm">Agregar</a>
                                    <a href="eliminar_selecciones.php?id=<?php echo $escenario['id']; ?>&nombre=<?php echo urlencode($escenario['nombre']); ?>&tipo=Escenario" class="btn btn-danger btn-sm">Borrar</a>
                                </td>
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
                        location.reload();
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
