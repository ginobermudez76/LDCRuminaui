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
        //obtener lista de tipo de solicitud
        try {
            $stmt = $conn->prepare("SELECT id_tipo, name_tipo FROM solicitud_tipo");
            $stmt->execute();
            $tipo = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }

        try {
            // Consultar usuarios con el nombre de su rol
            $stmt = $conn->prepare("SELECT u.id as id, u.nombre_usuario as nombre_usuario, r.rol_name AS rol 
                                                    FROM usuarios u
                                                    LEFT JOIN roles r ON u.rol = r.id_rol
                                                    WHERE u.rol <> 5 AND u.rol <> 6 AND u.rol <> 7 AND u.rol <> 8 AND u.id <> :usuario_id");
            $stmt->bindParam(':usuario_id', $usuario_id, PDO::PARAM_INT);
            $stmt->execute();
            $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
                                        <button type="button" class="btn btn-secondary mb-4 btncerrar" data-solicitud-id1="<?php echo $solicitud['s_id']; ?>">Cerrar</button>
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
                                            <h5 class="modal-title" id="AccionesModalLabel">Acciones de la solicitud: <?php echo htmlspecialchars($solicitud['s_id']); ?></h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body" id="solicitudDetails">
                                            <?php if ($solicitud['tipo'] == 'Otro tipo') { ?>
                                                <p style="color:red; font-size:20px;">Esta solicitud es "otro tipo", hacer click en aceptar o denegar aprobara o rechazara la solicitud en su totalidad, si usted no es quien debe procesar estás acciones, por favor reasignela.</p>
                                            <?php } ?>

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
                                            <form action="reasignarSolicitudTipo.php" method="POST" enctype="multipart/form-data" class="form-reasignar-tipo">
                                                <div class="mb-3">
                                                    <label for="tipoReasignarLabel" class="form-label"><strong>Reasignar automaticamente por tipo</strong></label>
                                                    <!-- Agrega el select de tipos -->
                                                    <select id="tipoReasignar" class="form-select tipo" name="tipoReasignar">
                                                        <?php foreach ($tipo as $tiporea) : ?>
                                                            <?php if ($tiporea['name_tipo'] !== $solicitud['tipo']) : ?>
                                                                <option value="<?php echo $tiporea['id_tipo']; ?>">
                                                                    <?php echo htmlspecialchars($tiporea['name_tipo']); ?>
                                                                </option>
                                                            <?php endif; ?>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                                <input type="hidden" id="solicitudId" name="solicitudId" value="<?php echo $solicitud['s_id']; ?>">
                                                <button type="submit" id="btnReasignarPorTipo" class="btn btn-success"><i class="fa-solid fa-check"></i>Reasignar</button>

                                            </form>

                                            <?php if ($solicitud['tipo'] == 'Otro tipo') { ?>
                                                <div class="mb-3">
                                                    <input type="checkbox" class="form-check-input checkMostrarUsuarios" id="checkMostrarUsuarios_<?php echo $solicitud['s_id']; ?>" data-target="#divUsuarioReasignar_<?php echo $solicitud['s_id']; ?>">
                                                    <label for="checkMostrarUsuarios_<?php echo $solicitud['s_id']; ?>" class="form-label">Reasignar manualmente por usuario</label>
                                                </div>
                                                <div id="divUsuarioReasignar_<?php echo $solicitud['s_id']; ?>" class="divUsuarioReasignar" style="display: none;">
                                                    <form action="reasignarSolicitudUsuario.php" method="POST" enctype="multipart/form-data" class="form-reasignar-usuario" >

                                                        <div class="mb-3">
                                                            <label for="reasignarUser" class="form-label">El departamento encargado se asigna de forma automatica</label>
                                                            <label for="usuarioReasignar" class="form-label"><strong>Usuario</strong></label>
                                                            <!-- Agrega el select de usuarios -->
                                                            <select id="usuarioReasignarPorUsuario" class="form-select usuario" name="usuarioReasignarPorUsuario">
                                                                <?php foreach ($usuarios as $usuariosrea) : ?>
                                                                    <option value="<?php echo $usuariosrea['id']; ?>">
                                                                        <?php echo htmlspecialchars($usuariosrea['rol']); ?>: <?php echo htmlspecialchars($usuariosrea['nombre_usuario']); ?>
                                                                    </option>
                                                                <?php endforeach; ?>
                                                            </select>

                                                        </div>
                                                        <input type="hidden" id="solicitudIdUsuario" name="solicitudIdUsuario" value="<?php echo $solicitud['s_id']; ?>">
                                                        <button type="submit" id="btnReasignarPorUsuario" class="btn btn-success"><i class="fa-solid fa-check"></i>Reasignar</button>

                                                    </form>

                                                </div>
                                            <?php } ?>
                                        </div>
                                        <div class="modal-footer">
                                            <button id="Cancelar" class="btn btn-danger" data-bs-dismiss="modal"><i class="fa-solid fa-xmark dism"></i>Cancelar</button>
                                        </div>
                                    </div>
                                </div>
                            </div>


                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <script>
            $(document).ready(function() {
                $(".btncerrar").click(function() {
                    var solicitud_id1 = $(this).data("solicitud-id1");
                    // Aquí puedes enviar los datos al script de procesamiento utilizando AJAX
                    $.ajax({
                        url: 'cerrarSolicitud.php', // Ruta al script de procesamiento
                        method: 'POST',
                        data: {
                            solicitud_id: solicitud_id1
                            // Aquí puedes incluir más datos si los necesitas
                        },
                        success: function(response) {
                            // Manejar la respuesta del script de procesamiento
                            console.log(response);
                            // Recargar la página después de cerrar la solicitud exitosamente
                            location.reload();
                        },
                        error: function(xhr, status, error) {
                            // Manejar errores
                            console.error(xhr);
                        }
                    });
                });
            });
        </script>


        <script>
            document.querySelectorAll('.checkMostrarUsuarios').forEach(function(checkbox) {
                checkbox.addEventListener('change', function() {
                    var targetId = this.getAttribute('data-target');
                    var targetDiv = document.querySelector(targetId);
                    if (targetDiv) {
                        targetDiv.style.display = this.checked ? 'block' : 'none';
                    }
                });
            });
        </script>


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
<?php

    } else {
        header("Location: ../public/index.php");
        exit();
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}



include '../includes/footer.php'; ?>