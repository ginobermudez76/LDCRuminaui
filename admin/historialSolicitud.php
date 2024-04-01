<?php
include '../includes/config.php'; // incluyendo la conexión de la base de datos
include '../includes/header.php';
if (!isset($_SESSION['usuario_admin'])) {
    echo "<script>window.location.href='../admin/login.php';</script>";
    exit();
}

// Obtener el ID de la solicitud del formulario POST
$idSolicitud = isset($_POST['id_solicitud']) ? $_POST['id_solicitud'] : null;

if ($idSolicitud !== null) {
    try {
        // Llamar al procedimiento almacenado para obtener el historial de la solicitud
        $stmt = $conn->prepare("CALL ObtenerHistorialSolicitud(:solicitud_id)");
        $stmt->bindParam(':solicitud_id', $idSolicitud, PDO::PARAM_INT);
        $stmt->execute();
        $historial = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>

<div class="container mt-2">
    <div class="table-responsive">
    <h2 class="gestionar">Historial</h2>
        <table class="table">
            <thead>
                <tr>
                    <th>Id</th>
                    <th>Solicitud</th>
                    <th>Tipo</th>
                    <th>Estado</th>
                    <th>Responsable</th>
                    <th>Fecha de asignación</th>
                    <th>Fecha de respuesta</th>
                </tr>
            </thead>
            <tbody>
                <?php if(isset($historial) && !empty($historial)) : ?>
                    <?php foreach ($historial as $solicitud) : ?>
                        <tr>
                            <td><?php echo htmlspecialchars($solicitud['id']); ?></td>
                            <td><?php echo htmlspecialchars($solicitud['solicitud']); ?></td>
                            <td><?php echo htmlspecialchars($solicitud['tipo']); ?></td>
                            <td><?php echo htmlspecialchars($solicitud['estado']); ?></td>
                            <td><?php echo htmlspecialchars($solicitud['departamento']); ?>
                            <br><?php echo htmlspecialchars($solicitud['responsable']); ?></td>
                            <td><?php echo htmlspecialchars($solicitud['asignacion']); ?></td>
                            <td><?php echo htmlspecialchars($solicitud['respuesta']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else : ?>
                    <tr>
                        <td colspan="7">No se encontró historial para esta solicitud.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?php
include '../includes/footer.php';
?>