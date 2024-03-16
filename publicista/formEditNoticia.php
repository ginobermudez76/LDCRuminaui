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
    $idNoticia = isset($_GET['id']) ? $_GET['id'] : null;

    if ($idNoticia) {
        try {
            $stmt = $conn->prepare("SELECT * FROM noticias WHERE id = :id");
            $stmt->bindParam(':id', $idNoticia, PDO::PARAM_INT);
            $stmt->execute();
            $noticia = $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    } else {
        echo "ID de noticia no proporcionado";
        exit();
    }
?>
<form id="formNoticiaEdit" action="editarNoticia.php" enctype="multipart/form-data" method="post" onsubmit="return validarCamposEdit()">
    <div class="mb-3">
        <label for="Titulo" class="form-label">Titulo</label>
        <input type="text" class="form-control" id="tituloEdit" name="tituloEdit" value="<?php echo htmlspecialchars($noticia['titulo']); ?>"></input>
    </div>
    <div class="mb-3">
        <label for="imagenEdit" class="form-label">imagen</label><br>
        <input type="checkbox" class="form-check-input" id="checkDImagen" name="checkDImagen" onchange="deshabilitarInputImagen()">

        <label class="form-check-label" for="checkEjemplo">Eliminar</label>
        <?php if (isset($noticia['imagen']) && $noticia['imagen']) : ?>
            <a href="<?php echo htmlspecialchars($noticia['imagen']); ?>" target="_blank">Imagen anterior</a>
        <?php else : ?>
            <a>No hay imagen</a>
        <?php endif; ?>
        <input type="file" class="form-control" id="imagenEdit" name="imagen" value="<?php echo htmlspecialchars($noticia['imagen']); ?>" onchange="deshabilitarCheckbox()">
    </div>
    <div class="mb-3">
                    <label for="cuerpo" class="form-label">Cuerpo de la noticia</label>
                    <textarea class="form-control" id="cuerpo" name="cuerpo" rows="3" required><?php echo htmlspecialchars($noticia['cuerpo']); ?></textarea>
                </div>
    <input type="hidden" id="idNoticiaEdit" name="idNoticia" value="<?php echo $idNoticia; ?>">
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
