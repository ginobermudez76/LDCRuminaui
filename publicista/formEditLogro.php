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
        $idLogro = isset($_GET['id']) ? $_GET['id'] : null;
        try {
            $stmt = $conn->prepare("SELECT * FROM deportes");
            $stmt->execute();
            $deportes = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
        if ($idLogro) {
            try {
                $stmt = $conn->prepare("SELECT * FROM logros WHERE id = :id");
                $stmt->bindParam(':id', $idLogro, PDO::PARAM_INT);
                $stmt->execute();
                $logro = $stmt->fetch(PDO::FETCH_ASSOC);
            } catch (PDOException $e) {
                echo "Error: " . $e->getMessage();
            }
        } else {
            echo "ID de solicitud no proporcionado";
            exit();
        }
?>
        <form id="formLogroEdit" action="editarLogro.php" enctype="multipart/form-data" method="post" onsubmit="return validarCamposEdit()">
            <div class="mb-3">
                <label for="Titulo" class="form-label">Nombre del logro</label>
                <input type="text" class="form-control" id="tituloEdit" name="tituloEdit" value="<?php echo htmlspecialchars($logro['titulo']); ?>"></input>
            </div>
            <div class="mb-3">
                <label for="Tipo" class="form-label">Tipo</label>
                <select class="form-control" id="tipoLogro" name="tipoLogro">
                    <option value="">Seleccione un tipo</option>
                    <option value="Medalla" <?php echo ($logro['tipologro'] == 'Medalla') ? 'selected' : ''; ?>>Medalla</option>
                    <option value="Copa" <?php echo ($logro['tipologro'] == 'Copa') ? 'selected' : ''; ?>>Copa</option>
                    <option value="Reconocimiento" <?php echo ($logro['tipologro'] == 'Reconocimiento') ? 'selected' : ''; ?>>Reconocimiento</option>
                </select>
            </div>

            <div class="mb-3">
                <label for="imagenEdit" class="form-label">imagen</label><br>
                <input type="checkbox" class="form-check-input" id="checkDImagen" name="checkDImagen" onchange="deshabilitarInputImagen()">

                <label class="form-check-label" for="checkEjemplo">Eliminar</label>
                <?php if (isset($logro['imagen']) && $logro['imagen']) : ?>
                    <a href="<?php echo htmlspecialchars($logro['imagen']); ?>" target="_blank">Imagen anterior</a>
                <?php else : ?>
                    <a>No hay imagen</a>
                <?php endif; ?>
                <input type="file" class="form-control" id="imagenEdit" name="imagen" value="<?php echo htmlspecialchars($logro['imagen']); ?>" onchange="deshabilitarCheckbox()">
            </div>
            <select class="form-control" id="deporte_idEdit" name="deporte_idEdit" required>
                <option value="">Seleccione un logro</option>
                <?php foreach ($deportes as $deporte) : ?>
                    <?php
                    // Compara el ID del logro actual con el ID del logro en el bucle
                    $selected = ($deporte['id'] == $logro['deporte_id']) ? 'selected' : '';
                    ?>
                    <option value="<?php echo $deporte['id']; ?>" <?php echo $selected; ?>>
                        <?php echo htmlspecialchars($deporte['nombre']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <input type="hidden" id="idLogroEdit" name="idLogro" value="<?php echo $idLogro; ?>">
            <button type="submit" class="btn btn-primary" id="btnEnviar">Publicar</button>
        </form>


<?php
    } else {
        header("Location: ../public/index.php");
        exit();
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>