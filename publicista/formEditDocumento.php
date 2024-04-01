<?php
session_start();
include '../includes/config.php'; // incluyendo la conexión de la base de datos
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
        $idDocumento = isset($_GET['id']) ? $_GET['id'] : null;
        try {
            $stmt = $conn->prepare("SELECT * FROM documentos");
            $stmt->execute();
            $documentos = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
        if ($idDocumento) {
            try {
                $stmt = $conn->prepare("SELECT * FROM documentos WHERE id = :id");
                $stmt->bindParam(':id', $idDocumento, PDO::PARAM_INT);
                $stmt->execute();
                $documento = $stmt->fetch(PDO::FETCH_ASSOC);
            } catch (PDOException $e) {
                echo "Error: " . $e->getMessage();
            }
        } else {
            echo "ID de solicitud no proporcionado";
            exit();
        }
?>
        <form id="formDocumentoEdit" action="editarDocumento.php" method="post" enctype="multipart/form-data" onsubmit="return validarCamposEdit()">
            <div class="mb-3">
                <label for="nombre" class="form-label">Nombre del Documento</label>
                <input type="text" class="form-control" id="nombreEdit" name="nombreEdit" required value="<?php echo htmlspecialchars($documento['nombre']); ?>">
            </div>
            <div class="mb-3">
                <label for="imagen" class="form-label">Documento</label><br>
                <input type="checkbox" class="form-check-input" id="checkDImagen" name="checkDImagen" onchange="deshabilitarInputImagen()">

                <label class="form-check-label" for="checkEjemplo">Eliminar</label>
                <?php if (isset($documento['documento']) && $documento['documento']) : ?>
                    <a href="<?php echo htmlspecialchars($documento['documento']); ?>" target="_blank">Documento anterior</a>
                <?php else : ?>
                    <a>No hay documento</a>
                <?php endif; ?>
                <input type="file" class="form-control" id="imagenEdit" name="imagenEdit" value="<?php echo htmlspecialchars($documento['documento']); ?>" onchange="deshabilitarCheckbox()">
            </div>
            <div class="mb-3">
                <label for="descripcionEdit" class="form-label">Descripción</label>
                <textarea type="text" class="form-control" id="descripcionEdit" name="descripcionEdit">
                        <?php if (!empty($documento['descripcion'])) : ?>
                        <?php echo htmlspecialchars($documento['descripcion']); ?>
                        <?php endif; ?>
                    </textarea>
            </div>

            <input type="hidden" id="idDocumentoEdit" name="idDocumentoEdit" value="<?php echo $idDocumento; ?>">
            <button type="submit" class="btn btn-primary" id="btnEnviarEdit">Editar documento</button>
        </form>


<?php
    } else {
        echo "<script>window.location.href='../public/index.php';</script>";
        exit();
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
