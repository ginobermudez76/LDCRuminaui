<?php

include '../includes/config.php';
include '../includes/header.php';

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
    if ($usuario['rol'] == 7) {
        // Mostrar el elemento del menú Administrar

// Verificar si se recibió un ID válido
if (isset($_GET['id'])) {
    $idEvento = $_GET['id'];

    // Obtener la información actual del evento
    $stmt = $conn->prepare("SELECT * FROM eventos WHERE id = :id");
    $stmt->bindParam(':id', $idEvento);
    $stmt->execute();
    $evento = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$evento) {
        echo "ID de evento no válido";
        exit();
    }

    // Procesar el formulario si se envió
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $nombre = $_POST['nombre'];
        $descripcion = $_POST['descripcion'];
        $fecha_ini = $_POST['fecha_ini'];
        $fecha_f = $_POST['fecha_f'];
        
        // Obtener la ruta de la imagen actual almacenada en la base de datos
        $rutaImagenAntigua = "../uploads/eventos/" . basename($evento['imagen']);

        if (!empty($evento['imagen']) && file_exists($rutaImagenAntigua)) {
            // Eliminar la imagen antigua del sistema de archivos solo si se envió un nuevo archivo
            if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] == 0) {
                unlink($rutaImagenAntigua);
            }
        }
        
        $rutaImagenNueva = ""; // Establecer la ruta de la nueva imagen como cadena vacía por defecto

        if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] == 0) {
            // Procesar la imagen solo si se proporciona una nueva y el checkbox no está marcado
            $directorioDestino = "../uploads/eventos/";
            $archivoImagen = $directorioDestino . basename($_FILES['imagen']['name']);
            $tipoArchivo = strtolower(pathinfo($archivoImagen, PATHINFO_EXTENSION));

            $check = getimagesize($_FILES["imagen"]["tmp_name"]);

            if ($check !== false) {
                if (move_uploaded_file($_FILES["imagen"]["tmp_name"], $archivoImagen)) {
                    // La imagen se cargó correctamente
                    // Actualizar la ruta de la nueva imagen en la base de datos
                    $rutaImagenNueva = "../uploads/eventos/" . basename($_FILES['imagen']['name']);
                } else {
                    $error = "Hubo un error al cargar la nueva imagen";
                }
            } else {
                $error = "El archivo no es una imagen válida";
            }
        }

        // Si el campo de imagen está deshabilitado, establecer la ruta de la nueva imagen como la imagen anterior
        if (isset($_POST['sinImagen'])) {
            $rutaImagenNueva = $evento['imagen'];
        }

        // Eliminar la imagen anterior solo si existe y se proporciona una nueva imagen
        if (!empty($evento['imagen']) && file_exists($rutaImagenAntigua) && !isset($_POST['sinImagen'])) {
            unlink($rutaImagenAntigua);
        }

        // Actualizar la base de datos con la nueva imagen o la imagen anterior
        $stmt = $conn->prepare("UPDATE eventos SET nombre=?, descripcion=?, fecha_inicio=?, fecha_fin=?, imagen=? WHERE id=?");
        $stmt->execute([$nombre, $descripcion, $fecha_ini, $fecha_f, $rutaImagenNueva, $idEvento]);

        echo "Evento editado con éxito";
        header("refresh:2;url=gestionar_eventos.php");
        exit();
    }

    // Obtener la lista de deportes para el formulario
    $stmtDeportes = $conn->prepare("SELECT * FROM deportes");
    $stmtDeportes->execute();
    $deportes = $stmtDeportes->fetchAll(PDO::FETCH_ASSOC);
} else {
    echo "ID de evento no proporcionado";
    exit();
}
?>

<div class="container mt-4">
    <h2>Editar Evento</h2>
    <?php if (!empty($error)) : ?>
        <div class="alert alert-danger">
            <?php echo $error; ?>
        </div>
    <?php endif; ?>
    <form action="editar_evento.php?id=<?php echo $idEvento; ?>" method="post" enctype="multipart/form-data">
        <div class="mb-3">
            <label for="nombre" class="form-label">Nombre del Evento</label>
            <input type="text" class="form-control" id="nombre" name="nombre" required value="<?php echo htmlspecialchars($evento['nombre']); ?>">
        </div>

        <div class="mb-3">
            <label for="descripcion" class="form-label">Descripción</label>
            <textarea class="form-control" id="descripcion" name="descripcion" required><?php echo htmlspecialchars($evento['descripcion']); ?></textarea>
        </div>

        <div class="mb-3">
            <label for="deporte_id" class="form-label">Deporte</label>
            <select class="form-control" id="deporte_id" name="deporte_id" required>
                <?php foreach ($deportes as $deporte) : ?>
                    <?php
                    // Compara el ID del deporte actual con el ID del deporte en el bucle
                    $selected = ($deporte['id'] == $evento['deporte_id']) ? 'selected' : '';
                    ?>
                    <option value="<?php echo $deporte['id']; ?>" <?php echo $selected; ?>>
                        <?php echo htmlspecialchars($deporte['nombre']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="mb-3">
            <label for="fecha_ini" class="form-label">Inicio</label>
            <input type="date" class="form-control" id="fecha_ini" name="fecha_ini" required value="<?php echo $evento['fecha_inicio']; ?>">
        </div>
        <div class="mb-3">
            <label for="fecha_f" class="form-label">Fin</label>
            <input type="date" class="form-control" id="fecha_f" name="fecha_f" required value="<?php echo $evento['fecha_fin']; ?>">
        </div>

        <div class="mb-3 form-check">
            <input type="checkbox" class="form-check-input" id="sinImagen" name="sinImagen" onchange="toggleImagenField()">
            <label class="form-check-label" for="sinImagen">Editar sin cambiar la imagen</label>
        </div>

        <div class="mb-3">
            <label for="imagen" class="form-label">Imagen</label>
            <input type="file" class="form-control" id="imagen" name="imagen" <?php echo isset($_POST['sinImagen']) ? 'disabled' : ''; ?>>
        </div>

        <button type="submit" class="btn btn-primary">Editar evento</button>
    </form>
</div>

<script>
    function toggleImagenField() {
        var imagenField = document.getElementById('imagen');
        var sinImagenCheckbox = document.getElementById('sinImagen');

        imagenField.disabled = sinImagenCheckbox.checked;
    }
</script>

<?php
}else{
    header("Location: /Ayudantias-1/public/index.php");
    exit();
}
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
include '../includes/footer.php';
?>
