<?php
include '../includes/config.php';
include '../includes/navbar.php';

if (!isset($_SESSION['usuario_admin'])) {
   header("Location: /ayudantias/admin/login.php");
   exit();
}

$error = "";

if (isset($_GET['id'])) {
    $idDeporte = $_GET['id'];

    // Obtener la información actual del producto
    $stmt = $conn->prepare("SELECT * FROM deportes WHERE id = :id");
    $stmt->bindParam(':id', $idDeporte);
    $stmt->execute();
    $deporte = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$deporte) {
        echo "ID de deporte no válido";
        exit();
    }

    // Procesar la nueva imagen si se proporciona
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $nombre = $_POST['nombre'];
        $descripcion = $_POST['descripcion'];

        // Obtener la ruta de la imagen actual almacenada en la base de datos
        $rutaImagenAntigua = "../uploads/deportes/" . basename($deporte['imagen']);

        if (!empty($deporte['imagen']) && file_exists($rutaImagenAntigua)) {
            // Eliminar la imagen antigua del sistema de archivos solo si se envió un nuevo archivo
            if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] == 0) {
                unlink($rutaImagenAntigua);
            }
        }
        $rutaImagenNueva = ""; // Establecer la ruta de la nueva imagen como cadena vacía por defecto

        if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] == 0) {
            // Procesar la imagen solo si se proporciona una nueva y el checkbox no está marcado
            $directorioDestino = "../uploads/deportes/";

            $archivoImagen = $directorioDestino . basename($_FILES['imagen']['name']);

            $tipoArchivo = strtolower(pathinfo($archivoImagen, PATHINFO_EXTENSION));

            $check = getimagesize($_FILES["imagen"]["tmp_name"]);

            if ($check !== false) {
                if (move_uploaded_file($_FILES["imagen"]["tmp_name"], $archivoImagen)) {
                    // La imagen se cargó correctamente
                    // Actualizar la ruta de la nueva imagen en la base de datos
                    $rutaImagenNueva = "../uploads/deportes/" . basename($_FILES['imagen']['name']);
                } else {
                    $error = "Hubo un error al cargar la nueva imagen";
                }
            } else {
                $error = "El archivo no es una imagen válida";
            }
        }

        // Si el campo de imagen está deshabilitado, establecer la ruta de la nueva imagen como la imagen anterior
        if (isset($_POST['sinImagen'])) {
            $rutaImagenNueva = $deporte['imagen'];
        }

        // Eliminar la imagen anterior solo si existe y se proporciona una nueva imagen
        if (!empty($deporte['imagen']) && file_exists($rutaImagenAntigua) && !isset($_POST['sinImagen'])) {
            unlink($rutaImagenAntigua);
        }

        // Actualizar la base de datos con la nueva imagen o la imagen anterior
        $stmt = $conn->prepare("UPDATE deportes SET nombre=?, descripcion=?, imagen=? WHERE id=?");
        $stmt->execute([$nombre, $descripcion, $rutaImagenNueva, $idDeporte]);


        header("refresh:2;url=gestionar_deportes.php");
        exit();
    }
}
?>

<div class="container mt-4">
    <h2>Editar Producto</h2>
    <?php if (!empty($error)) : ?>
        <div class="alert alert-danger">
            <?php echo $error; ?>
        </div>
    <?php endif; ?>
    <form action="editar_deporte.php?id=<?php echo $idDeporte; ?>" method="post" enctype="multipart/form-data">
    <div class="mb-3">
                <label for="nombre" class="form-label">Nombre del Deporte</label>
                <input type="text" class="form-control" id="nombre" name="nombre" required value="<?php echo htmlspecialchars($deporte['nombre']); ?>">
            </div>
            <div class="mb-3">
                <label for="descripcion" class="form-label">Descripción</label>
                <textarea type="text" class="form-control" id="descripcion" name="descripcion" required><?php echo htmlspecialchars($deporte['descripcion']); ?>
                </textarea>
            </div>
            <div class="mb-3 form-check">
                 <input type="checkbox" class="form-check-input" id="sinImagen" name="sinImagen" onchange="toggleImagenField()">
                 <label class="form-check-label" for="sinImagen">Editar sin cambiar la imagen</label>
            </div>
            <div class="mb-3">
                <label for="imagen" class="form-label">Imagen</label>
                <input type="file" class="form-control" id="imagen" name="imagen" <?php echo isset($_POST['sinImagen']) ? 'disabled' : ''; ?>>
            </div>
        <button type="submit" class="btn btn-primary">Editar deporte</button>
    </form>
</div>

<script>
    function toggleImagenField() {
        var imagenField = document.getElementById('imagen');
        var sinImagenCheckbox = document.getElementById('sinImagen');

        imagenField.disabled = sinImagenCheckbox.checked;
    }
</script>

<script>
    function validarCamposEvento() {
        var nombreDeporte = document.getElementById("nombre").value;
        if (nombreDeporte === "") {
            alert("El deporte debe tener un nombre");
            return false;
        }

 
        return true;
    }
</script>

<?php
include '../includes/footer.php';
?>