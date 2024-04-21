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
            $noticiaId = $_POST['id'];
            // Obtener la ruta de la imagen almacenada en la base de datos
            $stmtImagen = $conn->prepare("SELECT imagen FROM noticias WHERE id = :id");
            $stmtImagen->bindParam(':id', $noticiaId);
            $stmtImagen->execute();
            $noticiaImagen = $stmtImagen->fetch(PDO::FETCH_ASSOC);

            if ($noticiaImagen && !empty($noticiaImagen['imagen'])) {
                // Ruta completa de la imagen
                $rutaImagen = "../uploads/noticias/" . basename($noticiaImagen['imagen']);

                // Eliminar la imagen del sistema de archivos
                if (file_exists($rutaImagen)) {
                    unlink($rutaImagen);
                }
            }

            // Eliminar la noticia de la base de datos
            $stmtDeleteNoticia = $conn->prepare("DELETE FROM noticias WHERE id = :id");
            $stmtDeleteNoticia->bindParam(':id', $noticiaId);
            $stmtDeleteNoticia->execute();

            echo "Noticia eliminada con éxito";
        } else {
            echo "No se proporcionó un ID de noticia";
        }
    } else {
        echo "<script>window.location.href='../public/index.php';</script>";
        exit();
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
