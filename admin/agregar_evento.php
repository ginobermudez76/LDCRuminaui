<?php

include '../includes/config.php'; //incluyendo la conexión de la base de datos
include '../includes/header.php'; //incluyendo la cabecera común

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
// Obtener la lista de tipo de 
try {
    $stmt = $conn->prepare("SELECT id, nombre FROM deportes");
    $stmt->execute();
    $deportes = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = $_POST['nombre'];
    $descripcion = $_POST['descripcion'];
    $fecha_ini = $_POST['fecha_ini'];
    $fecha_f = $_POST['fecha_f'];
    $deporte_id = $_POST['deporte_id'];

    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] == 0) {
        $directorioDestino = "../uploads/eventos/";

        $archivoImagen = $directorioDestino . basename($_FILES['imagen']['name']);
        
        $tipoArchivo = strtolower(pathinfo($archivoImagen, PATHINFO_EXTENSION));

        $check = getimagesize($_FILES["imagen"]["tmp_name"]);

        if($check != false){
            if(move_uploaded_file($_FILES["imagen"]["tmp_name"], $archivoImagen)){
                //la imagen se cargo correctamente

            }else{
                $error = "Hubo un error al cargar la imagen";
            }
        }else{
            $error ="El archivo no es una imagen";
        }
    } else {
        // Manejo en el caso de que la imagen no se cargue
        $archivoImagen = "";
    }

    try {
        $stmt = $conn->prepare("INSERT INTO eventos (nombre, descripcion, fecha_inicio, fecha_fin, deporte_id, imagen) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$nombre, $descripcion, $fecha_ini, $fecha_f, $deporte_id, $archivoImagen]);

        // Redirigir después de agregar
        header("Location: gestionar_eventos.php");
        exit();
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>

<div class="container mt-4">
    <h2>Agregar Evento</h2>
    <?php if (!empty($error)): ?>
        <div class="alert alert-danger">
            <?php echo $error; ?>
        </div>
    <?php endif; ?>
    <form action="agregar_evento.php" method="post" enctype="multipart/form-data">
    <div class="mb-3">
                <label for="nombre" class="form-label">Nombre</label>
                <input type="text" class="form-control" id="nombre" name="nombre" requerid>
            </div>
            <div class="mb-3">
                <label for="descripcion" class="form-label">Descripción</label>
                <textarea type="text" class="form-control" id="descripcion" name="descripcion" requerid></textarea>
            </div>
        <div class="mb-3">
            <label for="deporte_id" class="form-label">Deporte</label>
            <select class="form-control" id="deporte_id" name="deporte_id" required>
                <?php foreach ($deportes as $deporte): ?>
                    <option value="<?php echo $deporte['id']; ?>"><?php echo htmlspecialchars($deporte['nombre']); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="mb-3">
            <label for="nombre" class="form-label">Inicio</label>
            <input type="date" class="form-control" id="fecha_ini" name="fecha_ini" requerid>
        </div>
        <div class="mb-3">
            <label for="nombre" class="form-label">Fin</label>
            <input type="date" class="form-control" id="fecha_f" name="fecha_f" requerid>
        </div>
        <div class="mb-3">
            <label for="imagen" class="form-label">Imagen principal</label>
            <input type="file" class="form-control" id="imagen" name="imagen" requerid></textarea>
        </div>

        <button type="submit" class="btn btn-primary">Publicar evento</button>
    </form>
</div>
<?php 
}else{
    header("Location: /Ayudantias-1/public/index.php");
    exit();
}
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
include '../includes/footer.php'; ?>
