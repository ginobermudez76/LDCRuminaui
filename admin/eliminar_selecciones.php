<?php
include '../includes/config.php';
include '../includes/header.php';

if (isset($_GET['id']) && isset($_GET['nombre']) && isset($_GET['tipo'])) {
    $idEvento = $_GET['id'];
    $nombreEvento = urldecode($_GET['nombre']);
    $tipo = $_GET['tipo'];
}

// Obtener las imágenes existentes
$imagenes = [];

$carpetaImagenes = ($tipo == "Evento")
    ? "../uploads/eventos/" . $nombreEvento . "_" . $idEvento
    : "../uploads/deportes/" . $nombreEvento . "_" . $idEvento;

if (file_exists($carpetaImagenes) && is_dir($carpetaImagenes)) {
    $archivos = scandir($carpetaImagenes);
    $imagenes = array_diff($archivos, array('..', '.'));
}

// Eliminación de imágenes seleccionadas
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['eliminar'])) {
    // Obtener las imágenes seleccionadas
    $imagenesAEliminar = $_POST['eliminar'];

    // Ruta de la carpeta de imágenes
    $carpetaImagenes = ($tipo == "Evento")
        ? "../uploads/eventos/" . $nombreEvento . "_" . $idEvento
        : "../uploads/deportes/" . $nombreEvento . "_" . $idEvento;

    foreach ($imagenesAEliminar as $imagen) {
        // Construir la ruta completa de la imagen
        $rutaImagen = $carpetaImagenes . "/" . $imagen;

        // Eliminar la imagen si existe
        if (file_exists($rutaImagen)) {
            unlink($rutaImagen);
        }

        // Eliminar la entrada de la base de datos
        $stmtEliminarImagen = $conn->prepare("DELETE FROM galeria_imagenes WHERE tipo = ? AND id_tipo = ? AND nombre = ? AND ruta_imagenes = ?");
        $stmtEliminarImagen->execute([$tipo, $idEvento, $nombreEvento, $rutaImagen]);
    }
    if ($tipo == "Evento") {
        header("Location: gestionar_eventos.php");
    } elseif ($tipo == "Deporte") {
        header("Location: gestionar_deportes.php"); // Corregir el nombre del archivo
    }
}

?>

<div class="container mt-4">
    <h2>Galería de Imágenes</h2>

    <form action="eliminar_selecciones.php?id=<?php echo $idEvento; ?>&nombre=<?php echo urlencode($nombreEvento); ?>&tipo=<?php echo $tipo; ?>" method="post">
        <table class="table">
            <thead>
                <tr>
                    <th>Imagen</th>
                    <th>Eliminar</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($imagenes as $imagen) : ?>
                    <tr>
                        <td>
                            <img src="<?php echo htmlspecialchars($carpetaImagenes . '/' . $imagen); ?>" alt="<?php echo htmlspecialchars($imagen); ?>" style="width: 100px; height: auto;">
                        </td>
                        <td>
                            <input type="checkbox" name="eliminar[]" value="<?php echo htmlspecialchars($imagen); ?>">
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <button type="submit" class="btn btn-danger">Eliminar selección</button>
    </form>
</div>


<?php include '../includes/footer.php'; ?>
