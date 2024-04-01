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
            $documentoId1 = $_POST['id'];
        // Obtener la ruta del documento almacenado en la base de datos
        $stmt = $conn->prepare("SELECT documento FROM documentos WHERE id = :id");
        $stmt->bindParam(':id', $documentoId1);
        $stmt->execute();
        $documentoId1 = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($documentoId1 && !empty($documentoId1['documento'])) {
            // Ruta completa del documento
            $rutaDocumento = "../uploads/documentos/" . basename($documentoId1['documento']);

            // Eliminar el documento del sistema de archivos
            if (file_exists($rutaDocumento)) {
                unlink($rutaDocumento);
            }
        }

        if (isset($_POST['id'])) {
            $documentoId = $_POST['id'];
            // Eliminar el documento de la base de datos
            $stmtDeleteDocumento = $conn->prepare("DELETE FROM documentos WHERE id = :id");
            $stmtDeleteDocumento->bindParam(':id', $documentoId);
            $stmtDeleteDocumento->execute();

            echo "Documento eliminado con éxito";
        }
    }
    } else {
        header("Location: ../public/index.php");
        exit();
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
