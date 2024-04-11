<?php
session_start();
include '../includes/config.php'; //incluyendo la conexión de la base de datos

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
        <div class="container mt-4">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Id</th>
                            <th>Imagen</th>
                            <th>Nombre</th>
                            <th>Descripción</th>
                            <th>Galería de imágenes</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($deportes as $deporte) : ?>
                            <tr>
                                <td><?php echo htmlspecialchars($deporte['id']); ?></td>
                                <td>
                                    <?php if (isset($deporte['imagen']) && $deporte['imagen']) : ?>
                                        <img src="<?php echo htmlspecialchars($deporte['imagen']); ?>" alt="<?php echo htmlspecialchars($deporte['nombre']); ?>" style="width: 100px; height: auto;">
                                    <?php else : ?>
                                        <p>Sin Imagen</p>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo empty($deporte['nombre']) ? 'No se proporcionó nombre' : htmlspecialchars($deporte['nombre']); ?></td>
                                <td>
                                    <?php if (empty($deporte['descripcion'])) : ?>
                                        <p>Sin descripción</p>
                                    <?php else : ?>
                                        <?php echo htmlspecialchars($deporte['descripcion']); ?>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <a href="galeria_de_imagenes.php?id=<?php echo $deporte['id']; ?>&nombre=<?php echo urlencode($deporte['nombre']); ?>&tipo=Deporte" class="btn btn-secondary btn-sm">Agregar</a>
                                    <a href="eliminar_selecciones.php?id=<?php echo $deporte['id']; ?>&nombre=<?php echo urlencode($deporte['nombre']); ?>&tipo=Deporte" class="btn btn-danger btn-sm">Borrar</a>
                                </td>
                                <td>
                                    <button type="button" class="btn btn-secondary btn-sm" onclick="loadForm(<?php echo $deporte['id']; ?>)">Editar</button>
                                    <button class="btn btn-danger btn-sm" onclick="confirmarEliminacion(<?php echo $deporte['id']; ?>)">Eliminar</button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

            </div>
        </div>

        <div id="modalEditDeportes" class="modal edit">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editarDeporteModalLabel">Editar deporte</h5>
                </div>
                <div id="formContent"></div>
            </div>
        </div>
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
        <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
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
                    url: "eliminar_deporte.php",
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
?>