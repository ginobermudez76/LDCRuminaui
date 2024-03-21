<?php
session_start();
include '../includes/config.php'; // incluyendo la conexión de la base de datos
if (!isset($_SESSION['usuario_admin'])) {
    header("Location: ../admin/login.php");
    exit();
}
$usuario_id = $_SESSION['usuario_id'];
// obtener lista de tipo de solicitud
try {
    $stmt = $conn->prepare("SELECT * FROM solicitud_tipo");
    $stmt->execute();
    $tipos = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}

// Llamar al procedimiento almacenado para obtener las solicitudes
try {
    $stmt = $conn->prepare("CALL mostrar_solicitudes(:usuario_id)");
    $stmt->bindParam(':usuario_id', $usuario_id, PDO::PARAM_INT);
    $stmt->execute();
    $solicitudes = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
<div class="container mt-2">
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Id</th>
                    <th>Fecha y hora</th>
                    <th>Documento</th>
                    <th>Descripción</th>
                    <th>Encargado</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($solicitudes as $solicitud) : ?>
                    <tr>
                        <td><?php echo htmlspecialchars($solicitud['id']); ?></td>
                        <td><?php echo htmlspecialchars($solicitud['fecha']); ?></td>
                        <td>
                            <?php if (isset($solicitud['doc']) && $solicitud['doc']) : ?>
                                <a href="<?php echo htmlspecialchars($solicitud['doc']); ?>" target="_blank">Ver documento</a>
                            <?php else : ?>
                                <p1>No hay documento</p1>
                            <?php endif; ?>
                        </td>
                        <td>
                            Tipo: <?php echo htmlspecialchars($solicitud['tipo']); ?><br>
                            <?php if (!empty($solicitud['valor'])) : ?>
                                Monto: $<?php echo htmlspecialchars($solicitud['valor']); ?><br>
                            <?php endif; ?>
                            <?php if (!empty($solicitud['descripcion'])) : ?>
                                Descripción: <?php echo htmlspecialchars($solicitud['descripcion']); ?>
                            <?php endif; ?>
                        </td>

                        <td>
                            <?php if (!empty($solicitud['departamento_encargado']) && !empty($solicitud['encargado'])) { ?>
                                <?php echo htmlspecialchars($solicitud['departamento_encargado']); ?><br>
                                <?php echo htmlspecialchars($solicitud['encargado']); ?>
                            <?php } else { ?>
                                La el proceso ha finalizado.
                            <?php } ?>


                        </td>
                        <td><?php echo htmlspecialchars($solicitud['estado']); ?></td>
                        <td>
                            <?php

                            if ($solicitud['estado'] == 'En tramite') {
                            ?>
                                <button type="button" class="btn btn-secondary btn-sm" onclick="loadForm(<?php echo $solicitud['id']; ?>)">Editar</button>
                                <button class="btn btn-danger btn-sm" onclick="confirmarEliminacion(<?php echo $solicitud['id']; ?>)">Eliminar</button>
                            <?php
                            } else { ?>
                                <form action="historialSolicitud.php" method="post">
                                    <input type="hidden" name="id_solicitud" value="<?php echo $solicitud['id']; ?>">
                                    <button type="submit" class="btn btn-link">Ver historial</button>
                                </form>

                            <?php
                            }
                            ?>


                        </td>
                    </tr>
                <?php endforeach; ?>
                <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
            </tbody>
        </table>
    </div>
</div>

<div id="myModal" class="modal edit" onsubmit="return validarTipoEdit()">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="editarSolicitudModalLabel">Editar solicitud</h5>
        </div>
        <div id="formContent"></div>


    </div>
</div>

<script>
    // Función para abrir el modal
    function openModal() {
        var modal = document.getElementById("myModal");
        modal.style.display = "block";
    }

    // Función para cerrar el modal
    function closeModal() {
        var modal = document.getElementById("myModal");
        modal.style.display = "none";
    }

    // Cierra el modal si se hace clic fuera de él
    window.onclick = function(event) {
        var modal = document.getElementById("myModal");
        if (event.target == modal) {
            modal.style.display = "none";
        }
    }

    // Carga el formulario desde el otro script PHP cuando se abre el modal
    function loadForm(idSolicitud) {
        var xhttp = new XMLHttpRequest();
        xhttp.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
                document.getElementById("formContent").innerHTML = this.responseText;
                document.getElementById("idSolicitudEdit").value = idSolicitud; // Establecer el ID de la solicitud en el formulario
                openModal(); // Abre el modal después de cargar el contenido
            }
        };
        xhttp.open("GET", "formEditSoli.php?id=" + idSolicitud, true); // Pasar el ID de la solicitud en la URL
        xhttp.send();
    }
</script>
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>

<script>
    function confirmarEliminacion(idSolicitud) {
        var confirmacion = confirm("¿Está seguro que desea eliminar esta solicitud?");

        if (confirmacion) {
            // Usuario hizo clic en "Aceptar", enviar solicitud a eliminar_solicitud.php
            eliminarSolicitud(idSolicitud);
        } else {
            // Usuario hizo clic en "Cancelar", no hacer nada
        }
    }

    function eliminarSolicitud(idSolicitud) {


        // Utiliza jQuery para enviar una solicitud AJAX a eliminar_solicitud.php
        $.ajax({
            type: "POST",
            url: "eliminar_solicitud.php",
            data: {
                id: idSolicitud
            },
            success: function(response) {
                // Manejar la respuesta, si es necesario
                console.log(response);
                alert(response);
                //Recargar la página
                location.reload();
            },
            error: function(error) {
                // Manejar errores si es necesario
                console.error(error);
            }
        });
    }
</script>

<script>
    function validarTipoEdit() {
        var seleccionTipoEdit = document.getElementById("tipoEdit").value;
        if (seleccionTipoEdit === "") {
            alert("Por favor, seleccione un tipo de solicitud");
            return false;
        }

        // Validar el tipo de archivo seleccionado
        var archivoInputEdit = document.getElementById("documentoEdit");


        var archivoEdit = archivoInputEdit.files[0];
        var extensionesPermitidasEdit = ['pdf'];
        var extensionEdit = archivoEdit.name.split('.').pop().toLowerCase();

        if (!extensionesPermitidasEdit.includes(extensionEdit)) {
            alert("El archivo seleccionado no es válido. Por favor, seleccione un archivo PDF");
            return false;
        }
        return true;
    }
</script>
<script>
    $(document).ready(function() {
        $('#formEditarSolicitud').submit(function(event) {
            event.preventDefault(); // Evita que el formulario se envíe normalmente

            // Captura los datos del formulario
            var formData = new FormData($(this)[0]);

            // Envia la solicitud AJAX a editar_solicitud.php
            $.ajax({
                url: 'editar_solicitud.php',
                type: 'POST',
                data: formData,
                async: false,
                cache: false,
                contentType: false,
                processData: false,
                success: function(response) {
                    // Manejar la respuesta si es necesario
                    console.log(response);
                    alert(response);
                    //Recargar la página
                    location.reload();
                },
                error: function(error) {
                    // Manejar errores si es necesario
                    console.error(error);
                }
            });

            return false;
        });
    });
</script>