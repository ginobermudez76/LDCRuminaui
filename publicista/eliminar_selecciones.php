<?php

include '../includes/config.php';
include '../includes/header.php';

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
        // Mostrar el elemento del menú Administrar


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
?>

<div class="container mt-4">
    <h2>Galería de Imágenes</h2>

    <form action="eliminarGaleria.php?id=<?php echo $idEvento; ?>&nombre=<?php echo urlencode($nombreEvento); ?>&tipo=<?php echo $tipo; ?>" method="post">
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
        <input type="hidden" id="tipo" name="tipo" value="<?php echo $tipo; ?>">
        <input type="hidden" id="idEvento" name="idEvento" value="<?php echo $idEvento; ?>">
        <input type="hidden" id="nombreEvento" name="nombreEvento" value="<?php echo $nombreEvento; ?>">
        <button type="submit" class="btn btn-danger">Eliminar selección</button>

    </form>
</div>


<?php 
}else{
    echo "<script>window.location.href='../public/index.php';</script>";
    exit();
}
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
include '../includes/footer.php'; ?>
