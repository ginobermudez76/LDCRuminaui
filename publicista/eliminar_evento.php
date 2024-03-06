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
        // Mostrar el elemento del menú Administrarsdasdasdasdasdas
        // Verificar si se recibió un ID válido y realizar la eliminación en la base de datos
        if (isset($_POST['id'])) {
            $idEvento1 = $_POST['id'];

            // Obtener la ruta de la imagen almacenada en la base de datos
            $stmt = $conn->prepare("SELECT imagen FROM eventos WHERE id = :id");
            $stmt->bindParam(':id', $idEvento1);
            $stmt->execute();
            $evento1 = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($evento1 && !empty($evento1['imagen'])) {
                // Ruta completa de la imagen
                $rutaImagen = "../uploads/eventos/" . basename($evento1['imagen']);

                // Eliminar la imagen del sistema de archivos
                if (file_exists($rutaImagen)) {
                    unlink($rutaImagen);
                }
            }
        }
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
    } else {
        header("Location: ../public/index.php");
        exit();
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
