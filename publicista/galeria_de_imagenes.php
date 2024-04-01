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

        // Tomar los valores enviados por los botones de gestionar_eventos.php o gestionar_deportes.php
        if (isset($_GET['id']) && isset($_GET['nombre']) && isset($_GET['tipo'])) {
            $idTipo = $_GET['id'];
            $nombreTipo = urldecode($_GET['nombre']);
            $tipoGaleria = $_GET['tipo'];
        }
?>

        <div class="container mt-4">
            <h2>Agregar imagenes</h2>
            <?php if (!empty($error)) : ?>
                <div class="alert alert-danger">
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>
            <form action="insertarGaleria.php" method="post" enctype="multipart/form-data">
                <div class="mb-3">
                    <label for="input1" class="form-label">Tipo</label>
                    <input type="text" class="form-control" id="tipo_galeria" name="tipo_galeria" value="<?php echo htmlspecialchars($tipoGaleria); ?>" readonly>
                </div>

                <div class="mb-3">
                    <label for="input2" class="form-label">Nombre</label>
                    <input type="text" class="form-control" id="nombre_tipo" name="nombre_tipo" value="<?php echo htmlspecialchars($nombreTipo); ?>" readonly>
                </div>

                <div class="mb-3">
                    <label for="input3" class="form-label">ID</label>
                    <input type="text" class="form-control" id="id_tp" name="id_tp" value="<?php echo htmlspecialchars($idTipo); ?>" readonly>
                </div>
                <div class="mb-3">
                    <label for="imagenes" class="form-label">Galería de imágenes</label>
                    <input type="file" class="form-control" id="imagenes" name="imagenes[]" multiple required>
                </div>

                <button type="submit" class="btn btn-primary">Guardar galería</button>
            </form>
        </div>
<?php
    } else {
        echo "<script>window.location.href='../public/index.php';</script>";
        exit();
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
include '../includes/footer.php'; ?>