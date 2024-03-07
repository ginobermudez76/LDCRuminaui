<?php
session_start();
if (!isset($_SESSION['usuario_admin'])) {
    header("Location: /Ayudantias-1/admin/login.php");
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
            $deportistaId1 = $_POST['id'];
        // Obtener la ruta de la imagen almacenada en la base de datos
        $stmt = $conn->prepare("SELECT imagen FROM deportistas_destacados WHERE id = :id");
        $stmt->bindParam(':id', $deportistaId1);
        $stmt->execute();
        $deportistaId1 = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($deportistaId1 && !empty($deportistaId1['imagen'])) {
            // Ruta completa de la imagen
            $rutaImagen = "../uploads/deportes/deportistas/" . basename($deportistaId1['imagen']);

            // Eliminar la imagen del sistema de archivos
            if (file_exists($rutaImagen)) {
                unlink($rutaImagen);
            }
        }

        if (isset($_POST['id'])) {
            $deportistaId = $_POST['id'];
            // Eliminar el deportista de la base de datos
            $stmtDeleteDeportista = $conn->prepare("DELETE FROM deportistas_destacados WHERE id = :id");
            $stmtDeleteDeportista->bindParam(':id', $deportistaId);
            $stmtDeleteDeportista->execute();

            echo "Deportista eliminado con éxito";
        }
    }
    } else {
        header("Location: ../public/index.php");
        exit();
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
