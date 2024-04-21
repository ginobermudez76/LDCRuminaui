<?php
session_start();
if (!isset($_SESSION['usuario_admin'])) {
    echo "<script>window.location.href='../admin/login.php';</script>";
    exit();
}

include '../includes/config.php'; //incluyendo la conexión a la base de datos
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
            $idDeporte1 = $_POST['id'];

            // Obtener la ruta de la imagen almacenada en la base de datos
            $stmt = $conn->prepare("SELECT imagen FROM deportes WHERE id = :id");
            $stmt->bindParam(':id', $idDeporte1);
            $stmt->execute();
            $deporte1 = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($deporte1 && !empty($deporte1['imagen'])) {
                // Ruta completa de la imagen
                $rutaImagen = "../uploads/deportes/" . basename($deporte1['imagen']);

                // Eliminar la imagen del sistema de archivos
                if (file_exists($rutaImagen)) {
                    unlink($rutaImagen);
                }
            }
            // Verificar si se recibió un ID válido y realizar la eliminación en la base de datos
            if (isset($_POST['id'])) {
                $idDeporte = $_POST['id'];

                // Obtener la información del deporte
                $stmtDeporte = $conn->prepare("SELECT nombre, imagen FROM deportes WHERE id = :id");
                $stmtDeporte->bindParam(':id', $idDeporte);
                $stmtDeporte->execute();
                $deporte = $stmtDeporte->fetch(PDO::FETCH_ASSOC);

                // Verificar si la carpeta existe
                $carpetaDeporte = "../uploads/deportes/" . $deporte['nombre'] . "_" . $idDeporte;
                if (file_exists($carpetaDeporte) && is_dir($carpetaDeporte)) {
                    // Eliminar carpeta y su contenido
                    $files = new RecursiveIteratorIterator(
                        new RecursiveDirectoryIterator($carpetaDeporte, RecursiveDirectoryIterator::SKIP_DOTS),
                        RecursiveIteratorIterator::CHILD_FIRST
                    );

                    foreach ($files as $fileinfo) {
                        $action = ($fileinfo->isDir() ? 'rmdir' : 'unlink');
                        $action($fileinfo->getRealPath());
                    }

                    rmdir($carpetaDeporte);
                }

                // Eliminar imágenes asociadas al deporte en la tabla galeria_imagenes
                $stmtDeleteImagenes = $conn->prepare("DELETE FROM galeria_imagenes WHERE nombre = :nombreDeporte AND id_tipo = :idDeporte AND tipo = 'Deporte'");
                $stmtDeleteImagenes->bindParam(':nombreDeporte', $deporte['nombre']);
                $stmtDeleteImagenes->bindParam(':idDeporte', $idDeporte);
                $stmtDeleteImagenes->execute();

                // Eliminar el deporte de la base de datos
                try {
                    $stmtDeleteDeporte = $conn->prepare("DELETE FROM deportes WHERE id = :id");
                    $stmtDeleteDeporte->bindParam(':id', $idDeporte);
                    $stmtDeleteDeporte->execute();

                    echo "Deporte eliminado con éxito";
                } catch (PDOException $e) {
                    echo "Error: " . $e->getMessage();
                }
            } else {
                echo "ID de deporte no proporcionado";
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