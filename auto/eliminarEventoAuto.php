<?php
$conectar = new mysqli("localhost", "root", "ghil3412", "liga");

if ($conectar->connect_error) {
    die("Error de conexión: " . $conectar->connect_error);
}

try {
    // Llamar al procedimiento almacenado para actualizar los estados
    $stmtLlamarSP = $conectar->prepare("CALL ActualizarEstadoEventos()");
    $stmtLlamarSP->execute();
    $stmtLlamarSP->close();

    // Obtener la fecha y hora actual
    $fechaActual = date('Y-m-d H:i:s');

    $stmt = $conectar->prepare("SELECT id, nombre, imagen FROM eventos WHERE fecha_eliminar < ?");
    $stmt->bind_param('s', $fechaActual);
    $stmt->execute();
    $result = $stmt->get_result();
    $eventosParaEliminar = $result->fetch_all(MYSQLI_ASSOC);

    foreach ($eventosParaEliminar as $evento) {
        // Eliminar imagen del sistema de archivos
        if ($evento && !empty($evento['imagen'])) {
            $rutaImagen = "../uploads/eventos/" . basename($evento['imagen']);
            if (file_exists($rutaImagen)) {
                unlink($rutaImagen);
            }
        }

        // Eliminar carpeta y su contenido
        $carpetaEvento = "../uploads/eventos/" . $evento['nombre'] . "_" . $evento['id'];
        if (file_exists($carpetaEvento) && is_dir($carpetaEvento)) {
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

        // Borrar imágenes asociadas al evento en la tabla galeria_imagenes
        $stmtDeleteImagenes = $conectar->prepare("DELETE FROM galeria_imagenes WHERE nombre = ? AND id_tipo = ? AND tipo = 'Evento'");
        $stmtDeleteImagenes->bind_param('si', $evento['nombre'], $evento['id']);
        $stmtDeleteImagenes->execute();

        // Borrar evento por id en la tabla eventos
        $stmtDeleteEvento = $conectar->prepare("DELETE FROM eventos WHERE id = ?");
        $stmtDeleteEvento->bind_param('i', $evento['id']);
        $stmtDeleteEvento->execute();
    }

    echo "Eventos eliminados correctamente";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}

$conectar->close();
?>