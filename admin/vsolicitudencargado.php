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
    if ($usuario['rol'] == 4 || $usuario['rol'] == 3 || $usuario['rol'] == 2 || $usuario['rol'] == 1 || $usuario['rol'] == 9) {

        // Mostrar el elemento del menú Administrar

        // Llamar al procedimiento almacenado para obtener las solicitudes
        try {
            $stmt = $conn->prepare("CALL solicitudes_asignadas(:usuario_id)");
            $stmt->bindParam(':usuario_id', $usuario_id, PDO::PARAM_INT);
            $stmt->execute();
            $solicitudes = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
?>
        <style>
            .fa-solid {
                padding-right: 5px;
            }
        </style>
        <div class="container mt-4">
            <h2 class="gestionar">Solicitudes asignadas</h2>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Id</th>
                            <th>Fecha</th>
                            <th>Documento</th>
                            <th>Tipo</th>
                            <th>Descripción</th>
                            <th>Solicitante</th>
                            <th>Valor solicitado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($solicitudes as $solicitud) : ?>
                            <tr>
                                <td>
                                    <?php echo htmlspecialchars($solicitud['s_id']); ?>
                                </td>
                                <td>
                                    <?php echo htmlspecialchars($solicitud['s_fecha']); ?>
                                </td>
                                <td>
                                    <?php if (isset($solicitud['s_doc']) && $solicitud['s_doc']) : ?>
                                        <a href="<?php echo htmlspecialchars($solicitud['s_doc']); ?>" target="_blank">Ver documento</a>
                                    <?php else : ?>
                                        <p1>No hay documento</p1>
                                    <?php endif; ?>
                                </td>

                                <td>
                                    <?php echo htmlspecialchars($solicitud['tipo']); ?>
                                </td>
                                <td><?php if (!empty($solicitud['descripcion'])) { ?>
                                        <?php echo htmlspecialchars($solicitud['descripcion']); ?>
                                    <?php } else { ?>
                                        Sin descripción
                                    <?php } ?>
                                <td>
                                    <?php echo htmlspecialchars($solicitud['solicitante']); ?>
                                </td>
                                <td>
                                    <?php if (!empty($solicitud['s_valor'])) { ?>
                                        $<?php echo htmlspecialchars($solicitud['s_valor']); ?>
                                    <?php } else { ?>
                                        No aplica
                                    <?php } ?>
                                </td>
                                <td>
                                    <?php if ($solicitud['estado'] == 'Rechazada' || $solicitud['estado'] == 'Aprobada') { ?>
                                        <button type="button" class="btn btn-secondary mb-4 btncerrar">Cerrar</button>
                                    <?php } else { ?>
                                        <button type="button" class="btn btn-primary mb-4 acciones" data-solicitud-id="<?php echo $solicitud['s_id']; ?>" data-bs-toggle="modal" data-bs-target="#AccionesModal<?php echo $solicitud['s_id']; ?>">Acciones</button>
                                    <?php } ?>
                                </td>
                            </tr>

                            <!-- Modal para mostrar botones de acciones -->
                            <div class="modal fade" id="AccionesModal<?php echo $solicitud['s_id']; ?>" tabindex="-1" aria-labelledby="AccionesModalLabel" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="AccionesModalLabel">Acciones de la Solicitud</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body" id="solicitudDetails">
                                            <?php echo htmlspecialchars($solicitud['s_id']); ?>
                                        </div>
                                        <div class="modal-footer">
                                            <!-- Dentro de tu bucle foreach -->
                                            <form action="aprobar_denegar.php" method="POST" enctype="multipart/form-data">
                                                <input type="hidden" name="solicitud_id" value="<?php echo $solicitud['s_id']; ?>">
                                                <input type="hidden" name="tipo_solicitud" value="<?php echo htmlspecialchars($solicitud['tipo']); ?>">
                                                <button type="submit" name="accion" value="Aprobar" class="btn btn-success"><i class="fa-solid fa-check"></i>Aprobar</button>
                                                <button type="submit" name="accion" value="Denegar" class="btn btn-danger"><i class="fa-solid fa-xmark"></i>Denegar</button>
                                                <button type="button" id="Reasignar" class="btn btn-info text-light" data-bs-toggle="modal" data-bs-target="#ReasignarModal<?php echo $solicitud['s_id']; ?>" data-solicitud-id="<?php echo $solicitud['s_id']; ?>">
                                                    <i class=" fa-solid fa-rotate-right"></i>Reasignar
                                                </button>
                                            </form>

                                        </div>
                                    </div>
                                </div>
                            </div>


                            <!-- Modal para Reasignar -->
                            <div class="modal fade" id="ReasignarModal<?php echo $solicitud['s_id']; ?>" tabindex="-1" aria-labelledby="ReasignarModalLabel" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="ReasignarModalLabel">Reasignar solicitud: <?php echo htmlspecialchars($solicitud['s_id']); ?></h5>
                                        </div>
                                        <div class="modal-body">
                                            <!-- Formulario reasignacion -->
                                        </div>
                                    </div>
                                </div>
                            </div>


                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>


        </div>


        <!-- Script para la impresion de la id en la URL y no cambiar de ventana al dar click en el boton Acciones-->
        <script>
            $(document).ready(function() {
                $('.acciones').click(function() {
                    var solicitudId = $(this).data('solicitud-id');

                    // Actualizar la URL con el ID
                    history.pushState(null, null, '?id=' + solicitudId);

                    // Mostrar el modal correspondiente
                    $('#AccionesModal' + solicitudId).modal('show');
                });

                // Manejar el evento de cierre del modal AccionesModal
                $('.modal').on('hidden.bs.modal', function() {
                    // Restaurar la URL sin el parámetro de solicitud_id
                    history.pushState(null, null, window.location.pathname);
                });
            });
        </script>
        <script>
            // Obtener referencia al checkbox
            var checkbox = document.getElementById("checkMostrarUsuarios");

            // Obtener referencia al div que contiene el select de usuarios
            var divUsuarioReasignar = document.getElementById("divUsuarioReasignar");

            // Agregar un listener al checkbox para escuchar el cambio de estado
            checkbox.addEventListener('change', function() {
                // Si el checkbox está marcado, mostrar el div; de lo contrario, ocultarlo
                if (this.checked) {
                    divUsuarioReasignar.style.display = "block";
                } else {
                    divUsuarioReasignar.style.display = "none";
                }
            });
        </script>
        <script>
            // Agregar un controlador de eventos al botón "Reasignar" dentro del modal
            document.getElementById("btnReasignar").addEventListener("click", function() {
                // Obtener los valores seleccionados de los campos del formulario
                var tipoReasignar = document.getElementById("tipoReasignar").value;
                var checkMostrarUsuarios = document.getElementById("checkMostrarUsuarios").checked;
                var usuarioReasignar = document.getElementById("usuarioReasignar").value;

                // Crear un objeto con los datos a enviar al servidor
                var datos = {
                    tipo_id: tipoReasignar,
                    solicitud_id: <?php echo $solicitud['s_id']; ?>,
                    checkMostrarUsuarios: checkMostrarUsuarios,
                    usuario_id: usuarioReasignar
                };

                // Crear una instancia de XMLHttpRequest
                var xhr = new XMLHttpRequest();

                // Configurar la solicitud
                xhr.open("POST", "reasignarSolicitud.php");

                // Establecer el tipo de contenido
                xhr.setRequestHeader("Content-Type", "application/json");

                // Enviar los datos al servidor
                xhr.send(JSON.stringify(datos));

                // Manejar la respuesta del servidor
                xhr.onload = function() {
                    if (xhr.status === 200) {
                        // La solicitud se ha completado correctamente
                        // Puedes redirigir o mostrar un mensaje de éxito aquí
                        console.log(xhr.responseText);
                        // Recarga la página después de reasignar
                        location.reload();
                    } else {
                        // La solicitud no se ha completado correctamente
                        // Puedes mostrar un mensaje de error aquí
                        console.error('Error al reasignar solicitud');
                    }
                };
            });
        </script>
<script>
    // Agregar un controlador de eventos al botón "Reasignar" dentro del modal
    document.getElementById("btnReasignar").addEventListener("click", function() {
        // Obtener el valor del checkbox
        var checkMostrarUsuarios = document.getElementById("checkMostrarUsuarios").checked;

        // Establecer el valor del campo oculto con el estado del checkbox
        document.getElementById("checkMostrarUsuariosHidden").value = checkMostrarUsuarios ? 1 : 0;

        // Resto del código para enviar los datos al servidor...
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