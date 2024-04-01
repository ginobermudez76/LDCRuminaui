<?php
session_start();
include '../includes/config.php'; // incluyendo la conexión de la base de datos
if (!isset($_SESSION['usuario_admin'])) {
    echo "<script>window.location.href='../admin/login.php';</script>";
    exit();
}

$idSolicitud = isset($_GET['id']) ? $_GET['id'] : null;

// obtener lista de tipo de solicitud
try {
    $stmt = $conn->prepare("SELECT * FROM solicitud_tipo");
    $stmt->execute();
    $tipos = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}

// Obtener detalles de la solicitud con el ID proporcionado
if ($idSolicitud) {
    try {
        $stmt = $conn->prepare("SELECT * FROM solicitud WHERE s_id = :id");
        $stmt->bindParam(':id', $idSolicitud, PDO::PARAM_INT);
        $stmt->execute();
        $solicitud = $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
} else {
    echo "ID de solicitud no proporcionado";
    exit();
}
?>

<form id="formEditarSolicitud" action="editar_solicitud.php" method="POST" enctype="multipart/form-data">
    <div class="mb-3">
        <label for="documentoEdit" class="form-label">Documento</label><br>
        <input type="checkbox" class="form-check-input" id="checkDArchivo" name="checkDArchivo" onchange="deshabilitarInputArchivo()">

        <label class="form-check-label" for="checkEjemplo">Eliminar</label>
        <?php if (isset($solicitud['s_doc']) && $solicitud['s_doc']) : ?>
            <a href="<?php echo htmlspecialchars($solicitud['s_doc']); ?>" target="_blank">Documento Anterior</a>
        <?php else : ?>
            <a>No hay documento</a>
        <?php endif; ?>
        <input type="file" class="form-control" id="documentoEdit" name="documento" value="<?php echo htmlspecialchars($solicitud['s_doc']); ?>" onchange="deshabilitarCheckbox()">
    </div>
    <div class="mb-3">
        <label for="tipoEdit" class="form-label">Tipo</label>
        <select class="form-control" id="tipoEdit" name="tipoEdit">
            <option value="">Tipo de solicitud</option>
            <?php foreach ($tipos as $tipo) : ?>
                <?php
                $selected = ($tipo['id_tipo'] == $solicitud['tipo']) ? 'selected' : ''; ?>
                <option value="<?php echo $tipo['id_tipo']; ?>" <?php echo $selected; ?>>
                    <?php echo htmlspecialchars($tipo['name_tipo']); ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="mb-3">
        <label for="descripcionEdit" class="form-label">Descripción</label>
        <textarea class="form-control" id="descripcionEdit" name="descripcion" rows="3"><?php echo htmlspecialchars($solicitud['descripcion']); ?></textarea>
    </div>
    <div class="mb-3">
        <label for="valorEdit" class="form-label">Valor solicitado</label>
        <input type="number" class="form-control" id="valorEdit" name="valor" step="0.01" value="<?php echo htmlspecialchars($solicitud['s_valor']); ?>">
    </div>
    <input type="hidden" id="idSolicitudEdit" name="idSolicitud" value="<?php echo $idSolicitud; ?>">
    <button type="submit" class="btn btn-primary">Enviar cambios</button>
</form>