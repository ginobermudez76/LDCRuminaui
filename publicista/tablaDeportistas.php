<?php
session_start();
include '../includes/config.php'; // incluyendo la conexión de la base de datos
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
        try {
            $stmt = $conn->prepare("CALL info_deportistas();");
            $stmt->execute();
            $deportistas = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
?>
        <div class="container mt-5">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Id</th>
                            <th>Imagen</th>
                            <th>Nombre</th>
                            <th>Deporte</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($deportistas as $deportista) : ?>
                            <tr>
                                <td><?php echo htmlspecialchars($deportista['id']); ?></td>
                                <td>
                                    <?php if (isset($deportista['imagen']) && $deportista['imagen']) : ?>
                                        <img src="<?php echo htmlspecialchars($deportista['imagen']); ?>" alt="<?php echo htmlspecialchars($deportista['nombre']); ?>" style="width: 100px; height: auto;">
                                    <?php else : ?>
                                        <p>Sin Imagen</p>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo htmlspecialchars($deportista['nombre']); ?></td>
                                <td><?php echo htmlspecialchars($deportista['deporte']); ?></td>
                                <td>
                                    <button type="button" class="btn btn-secondary btn-sm" onclick="loadForm(<?php echo $deportista['id']; ?>)">Editar</button>
                                    <button class="btn btn-danger btn-sm" onclick="confirmarEliminacion(<?php echo $deportista['id']; ?>)">Eliminar</button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div id="modalEditDeportitas" class="modal edit">
            <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editarSolicitudModalLabel">Editar informacion del deportista</h5>
            </div>
                <div id="formContent"></div>
            </div>
        </div>
        <script>
    // Función para abrir el modal
    function openModal() {
        var modal = document.getElementById("modalEditDeportitas");
        modal.style.display = "block";
    }

    // Función para cerrar el modal
    function closeModal() {
        var modal = document.getElementById("modalEditDeportitas");
        modal.style.display = "none";
    }

    // Cierra el modal si se hace clic fuera de él
    window.onclick = function(event) {
        var modal = document.getElementById("modalEditDeportitas");
        if (event.target == modal) {
            modal.style.display = "none";
        }
    }

    // Carga el formulario desde el otro script PHP cuando se abre el modal
    function loadForm(idDeportista) {
    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            document.getElementById("formContent").innerHTML = this.responseText;
            document.getElementById("idDeportistaEdit").value = idDeportista; // Establecer el ID del deportita en el formulario
            openModal(); // Abre el modal después de cargar el contenido
        }
    };
    xhttp.open("GET", "formEditDeportista.php?id=" + idDeportista, true); // Pasar el ID del deportista en la URL
    xhttp.send();
}

</script>
        <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
        <script>
            function confirmarEliminacion(idDeportista) {
                var confirmacion = confirm("¿Está seguro que desea eliminar. Esta acción no se puede deshacer.?");

                if (confirmacion) {
                    // Usuario hizo clic en "Aceptar", enviar solicitud a eliminarDeportista.php
                    eliminarDeportista(idDeportista);
                } else {
                    // Usuario hizo clic en "Cancelar", no hacer nada
                }
            }

            function eliminarDeportista(idDeportista) {
                // Utiliza jQuery para enviar una solicitud AJAX a eliminarDeportista.php
                $.ajax({
                    type: "POST",
                    url: "eliminarDeportista.php",
                    data: {
                        id: idDeportista
                    },
                    success: function(response) {
                        // Manejar la respuesta, si es necesario
                        console.log(response);

                        // Puedes recargar la página o actualizar la lista de deportes de alguna manera
                        location.reload();
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
        header("Location: ../public/index.php");
        exit();
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
