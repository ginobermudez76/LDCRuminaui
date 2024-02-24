<?php
include '../includes/config.php'; //incluyendo la conexión de la base de datos
include '../includes/header.php'; //incluyendo la cabecera común

if (!isset($_SESSION['usuario_admin'])) {
    header("Location: /Ayudantias-1/admin/login.php");
    exit();
}
$usuario_id = $_SESSION['usuario_id'];
//obtener lista de tipo de solicitud
try {
    $stmt = $conn->prepare("SELECT id_tipo, name_tipo FROM solicitud_tipo");
    $stmt->execute();
    $tipo = $stmt->fetchAll(PDO::FETCH_ASSOC);
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

<div class="container mt-4">
    <h2 class="gestionar">Solicitudes</h2>
    <button type="button" class="btn btn-primary mb-4" data-bs-toggle="modal" data-bs-target="#agregarSolicitudModal">Agregar +</button>

    <table class="table">
        <thead>
            <tr>
                <th>Id</th>
                <th>Fecha</th>
                <th>Documento</th>
                <th>Tipo</th>
                <th>Descripción</th>
                <th>Valor solicitado</th>
                <th>Departamento</th>
                <th>Encargado</th>
                <th>Estado</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($solicitudes as $solicitud) : ?>
                <tr>
                    <td><?php echo htmlspecialchars($solicitud['s_id']); ?></td>
                    <td><?php echo htmlspecialchars($solicitud['s_fecha']); ?></td>
                    <td>
                        <?php if (isset($solicitud['s_doc']) && $solicitud['s_doc']) : ?>
                            <a href="<?php echo htmlspecialchars($solicitud['s_doc']); ?>" target="_blank">Ver documento</a>
                        <?php else : ?>
                            <p1>No hay documento</p1>
                        <?php endif; ?>
                    </td>

                    <td><?php echo htmlspecialchars($solicitud['tipo']); ?></td>
                    <td><?php echo htmlspecialchars($solicitud['descripcion']); ?></td>
                    <td>$ <?php echo htmlspecialchars($solicitud['s_valor']); ?></td>
                    <td><?php echo htmlspecialchars($solicitud['departamento_encargado']); ?></td>
                    <td><?php echo htmlspecialchars($solicitud['encargado']); ?></td>
                    <td><?php echo htmlspecialchars($solicitud['estado']); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- Modal para agregar solicitud -->
<div class="modal fade" id="agregarSolicitudModal" tabindex="-1" aria-labelledby="agregarSolicitudModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="agregarSolicitudModalLabel">Agregar Solicitud</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="tbsolicitud.php" method="POST" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label for="documento" class="form-label">Documento</label>
                        <input type="file" class="form-control" id="documento" name="documento">
                    </div>
                    <div class="mb-3">
                        <label for="tipo" class="form-label">Tipo</label>
                        <select class="form-select" id="tipo_id" name="tipo_id">
                            <?php foreach ($tipo as $tipo) : ?>
                                <option value="<?php echo $tipo['id_tipo']; ?>"><?php echo htmlspecialchars($tipo['name_tipo']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="descripcion" class="form-label">Descripción</label>
                        <textarea class="form-control" id="descripcion" name="descripcion" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="valor" class="form-label">Valor solicitado</label>
                        <input type="number" class="form-control" id="valor" name="valor" step="0.01">
                    </div>
                    <button type="submit" class="btn btn-primary">Agregar Solicitud</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
