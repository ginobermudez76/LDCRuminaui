<?php
include '../includes/config.php'; //incluyendo la conexión a la base de datos

// Verificar si se recibió un ID válido y realizar la eliminación en la base de datos
if (isset($_POST['id'])) {
    $idDeporte = $_POST['id'];

    // Obtener la ruta de la imagen almacenada en la base de datos
    $stmt = $conn->prepare("SELECT imagen FROM deportes WHERE id = :id");
    $stmt->bindParam(':id', $idDeporte);
    $stmt->execute();
    $deporte = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($deporte && !empty($deporte['imagen'])) {
        // Ruta completa de la imagen
        $rutaImagen = "../uploads/deportes/" . basename($deporte['imagen']);

        // Eliminar la imagen del sistema de archivos
        if (file_exists($rutaImagen)) {
            unlink($rutaImagen);
        }
    } 

    // Eliminar el deporte de la base de datos
    try {
        $stmt = $conn->prepare("DELETE FROM deportes WHERE id = :id");
        $stmt->bindParam(':id', $idDeporte);
        $stmt->execute();

        echo "Deporte eliminado con éxito";
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
} else {
    echo "ID de deporte no proporcionado";
}
?>
