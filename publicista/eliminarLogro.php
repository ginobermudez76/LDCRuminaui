<?php
session_start();
if (!isset($_SESSION['usuario_admin'])) {
    echo "<script>window.location.href='../admin/login.php';</script>";
    exit();
}

include '../includes/config.php'; // Incluyendo la conexión a la base de datos
$usuario_id = $_SESSION['usuario_id'];
try {
    // Consultar el rol del usuario en la base de datos
    $stmt = $conn->prepare("SELECT rol FROM usuarios WHERE id = :usuario_id");
    $stmt->bindParam(':usuario_id', $usuario_id, PDO::PARAM_INT);
    $stmt->execute();

    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    // Verificar si el usuario tiene el rol de Publicista
    if ($usuario['rol'] == 7) {
        if (isset($_POST['id'])) {
            $logroId1 = $_POST['id'];
        // Obtener la ruta de la imagen almacenada en la base de datos
        $stmt = $conn->prepare("SELECT imagen FROM logros WHERE id = :id");
        $stmt->bindParam(':id', $logroId1);
        $stmt->execute();
        $logroId1 = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($logroId1 && !empty($logroId1['imagen'])) {
            // Ruta completa de la imagen
            $rutaImagen = "../uploads/deportes/logros/" . basename($logroId1['imagen']);

            // Eliminar la imagen del sistema de archivos
            if (file_exists($rutaImagen)) {
                unlink($rutaImagen);
            }
        }

        if (isset($_POST['id'])) {
            $logroId = $_POST['id'];
            // Eliminar el logro de la base de datos
            $stmtDeleteLogro = $conn->prepare("DELETE FROM logros WHERE id = :id");
            $stmtDeleteLogro->bindParam(':id', $logroId);
            $stmtDeleteLogro->execute();

            echo "Logro eliminado con éxito";
        }
    }
    } else {
        echo "<script>window.location.href='../public/index.php';</script>";
        exit();
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>