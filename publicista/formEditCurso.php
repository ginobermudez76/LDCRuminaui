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
        $idCurso = isset($_GET['id']) ? $_GET['id'] : null;
        try {
            $stmt = $conn->prepare("SELECT * FROM deportes");
            $stmt->execute();
            $deportes = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
        if ($idCurso) {
            try {
                $stmt = $conn->prepare("SELECT * FROM cursos WHERE id = :id");
                $stmt->bindParam(':id', $idCurso, PDO::PARAM_INT);
                $stmt->execute();
                $curso = $stmt->fetch(PDO::FETCH_ASSOC);
            } catch (PDOException $e) {
                echo "Error: " . $e->getMessage();
            }
        } else {
            echo "ID de solicitud no proporcionado";
            exit();
        }
?>
        <form id="formCursoEdit" action="editarCurso.php" method="post" enctype="multipart/form-data" onsubmit="return validarCamposEdit()">
            <div class="mb-3">
                <label for="nombre" class="form-label">Nombre del curso</label>
                <input type="text" class="form-control" id="nombreEdit" name="nombreEdit" required value="<?php echo htmlspecialchars($curso['nombre']); ?>" maxlength="100">
            </div>
            <div class="mb-3">
                <label for="imagen" class="form-label">imagen</label><br>
                <input type="checkbox" class="form-check-input" id="checkDImagen" name="checkDImagen" onchange="deshabilitarInputImagen()">

                <label class="form-check-label" for="checkEjemplo">Eliminar</label>
                <?php if (isset($curso['imagen']) && $curso['imagen']) : ?>
                    <a href="<?php echo htmlspecialchars($curso['imagen']); ?>" target="_blank">Imagen anterior</a>
                <?php else : ?>
                    <a>No hay imagen</a>
                <?php endif; ?>
                <input type="file" class="form-control" id="imagenEdit" name="imagenEdit" value="<?php echo htmlspecialchars($curso['imagen']); ?>" onchange="deshabilitarCheckbox()">
            </div>
            <div class="mb-3">
                <label for="descripcion" class="form-label">Descripción</label>
                <textarea class="form-control" id="descripcionEdit" name="descripcionEdit" maxlength="300" rows="3"><?php echo htmlspecialchars($curso['descripcion']); ?></textarea>
            </div>

            <div class="mb-3">
                <label for="deporte_id" class="form-label">Deporte</label>
                <select class="form-control" id="deporte_idEdit" name="deporte_idEdit" required>
                    <option value="">Seleccione un deporte</option>
                    <?php foreach ($deportes as $deporte) : ?>
                        <?php
                        // Compara el ID del deporte actual con el ID del deportista en el bucle
                        $selected = ($deporte['id'] == $curso['deporte_id']) ? 'selected' : '';
                        ?>
                        <option value="<?php echo $deporte['id']; ?>" <?php echo $selected; ?>>
                            <?php echo htmlspecialchars($deporte['nombre']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="mb-3">
                <label for="fecha_ini" class="form-label">Inicio</label>
                <input type="date" class="form-control" id="fecha_iniEdit" name="fecha_iniEdit" required value="<?php echo $curso['fecha_inicio']; ?>">
            </div>
            <div class="mb-3">
                <label for="fecha_f" class="form-label">Fin</label>
                <input type="date" class="form-control" id="fecha_fEdit" name="fecha_fEdit" required value="<?php echo $curso['fecha_fin']; ?>">
            </div>
            <input type="hidden" id="idCursoEdit" name="idCursoEdit" value="<?php echo $idCurso; ?>">
            <button type="submit" class="btn btn-primary" id="btnEnviarEdit">Editar curso</button>
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