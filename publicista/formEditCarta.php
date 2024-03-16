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
    $idCarta = isset($_GET['id']) ? $_GET['id'] : null;

    if ($idCarta) {
        try {
            $stmt = $conn->prepare("SELECT * FROM carta_condolencias WHERE id = :id");
            $stmt->bindParam(':id', $idCarta, PDO::PARAM_INT);
            $stmt->execute();
            $carta = $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    } else {
        echo "ID de carta no proporcionado";
        exit();
    }
?>
<form id="formCartaEdit" action="editarCarta.php" enctype="multipart/form-data" method="post" onsubmit="return validarCamposEdit()">

    <div class="mb-3">
        <label for="imagenEdit" class="form-label">imagen</label><br>
        <input type="checkbox" class="form-check-input" id="checkDImagen" name="checkDImagen" onchange="deshabilitarInputImagen()">

        <label class="form-check-label" for="checkEjemplo">Eliminar</label>
        <?php if (isset($carta['imagen']) && $carta['imagen']) : ?>
            <a href="<?php echo htmlspecialchars($carta['imagen']); ?>" target="_blank">Imagen anterior</a>
        <?php else : ?>
            <a>No hay imagen</a>
        <?php endif; ?>
        <input type="file" class="form-control" id="imagenEdit" name="imagen" value="<?php echo htmlspecialchars($carta['imagen']); ?>" onchange="deshabilitarCheckbox()">
    </div>
    <div class="mb-3">
        <label for="Mensaje" class="form-label">Mensaje</label>
        <textarea type="text" class="form-control" id="mensajeEdit" name="mensajeEdit"><?php echo htmlspecialchars($carta['mensaje']); ?></textarea>
    </div>
    <input type="hidden" id="idCartaEdit" name="idCarta" value="<?php echo $idCarta; ?>">
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
