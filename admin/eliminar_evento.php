<?php
include '../includes/config.php'; // Incluyendo la conexión a la base de datos

// Verificar si se recibió un ID válido y realizar la eliminación en la base de datos
if (isset($_POST['id'])) {
    $idEvento = $_POST['id'];

    // Obtener la información del evento
    $stmtEvento = $conn->prepare("SELECT nombre, imagen FROM eventos WHERE id = :id");
    $stmtEvento->bindParam(':id', $idEvento);
    $stmtEvento->execute();
    $evento = $stmtEvento->fetch(PDO::FETCH_ASSOC);

    // Verificar si la carpeta existe
    $carpetaEvento = "../uploads/eventos/" . $evento['nombre'] . "_" . $idEvento;
    if (file_exists($carpetaEvento) && is_dir($carpetaEvento)) {
        // Eliminar carpeta y su contenido
        $files = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($carpetaEvento, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::CHILD_FIRST
        );

        foreach ($files as $fileinfo) {
            $action = ($fileinfo->isDir() ? 'rmdir' : 'unlink');
            $action($fileinfo->getRealPath());
        }

        rmdir($carpetaEvento);
    }

    // Eliminar imágenes asociadas al evento en la tabla galeria_imagenes
    $stmtDeleteImagenes = $conn->prepare("DELETE FROM galeria_imagenes WHERE nombre = :nombreEvento AND id_tipo = :idEvento AND tipo = 'Evento'");
    $stmtDeleteImagenes->bindParam(':nombreEvento', $evento['nombre']);
    $stmtDeleteImagenes->bindParam(':idEvento', $idEvento);
    $stmtDeleteImagenes->execute();

    // Eliminar el evento de la base de datos
    try {
        $stmtDeleteEvento = $conn->prepare("DELETE FROM eventos WHERE id = :id");
        $stmtDeleteEvento->bindParam(':id', $idEvento);
        $stmtDeleteEvento->execute();

        echo "Evento eliminado con éxito";
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
} else {
    echo "ID de evento no proporcionado";
}
?>
