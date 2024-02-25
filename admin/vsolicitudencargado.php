<?php
include '../includes/config.php'; //incluyendo la conexión de la base de datos
include '../includes/header.php'; //incluyendo la cabecera común

if (!isset($_SESSION['usuario_admin'])) {
    header("Location: /Ayudantias-1/admin/login.php");
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
    if ($usuario['rol'] == 4 || $usuario['rol'] == 3 || $usuario['rol'] == 2 || $usuario['rol'] == 1) {
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
                    <?php foreach ($solicitudes as $solicitud): ?>
                        <tr>
                            <td>
                                <?php echo htmlspecialchars($solicitud['s_id']); ?>
                            </td>
                            <td>
                                <?php echo htmlspecialchars($solicitud['s_fecha']); ?>
                            </td>
                            <td>
                                <?php if (isset($solicitud['s_doc']) && $solicitud['s_doc']): ?>
                                    <a href="<?php echo htmlspecialchars($solicitud['s_doc']); ?>" target="_blank">Ver documento</a>
                                <?php else: ?>
                                    <p1>No hay documento</p1>
                                <?php endif; ?>
                            </td>

                            <td>
                                <?php echo htmlspecialchars($solicitud['tipo']); ?>
                            </td>
                            <td>
                                <?php echo htmlspecialchars($solicitud['descripcion']); ?>
                            </td>
                            <td>
                                <?php echo htmlspecialchars($solicitud['solicitante']); ?>
                            </td>
                            <td>$
                                <?php echo htmlspecialchars($solicitud['s_valor']); ?>
                            </td>
                            <td>
                                <button type="button" class="btn btn-primary mb-4 acciones" data-bs-toggle="modal"
                                    data-bs-target="#AccionesModal"
                                    >Acciones</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="modal fade" id="AccionesModal" tabindex="-1" aria-labelledby="AccionesModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="AccionesModalLabel">Acciones</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <button type="button" id="Aprobar" class="btn btn-success"><i
                                class="fa-solid fa-check"></i>Aprobar</button>
                        <button type="button" id="Denegar" class="btn btn-danger"><i
                                class="fa-solid fa-xmark"></i>Denegar</button>
                        <button type="button" id="Reasignar" class="btn btn-info text-light"><i
                                class="fa-solid fa-rotate-right"></i>Reasignar</button>

                    </div>
                </div>
            </div>
        </div>
 <?php
    } else {
        header("Location: /Ayudantias-1/public/index.php");
        exit();
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}



include '../includes/footer.php'; ?>
