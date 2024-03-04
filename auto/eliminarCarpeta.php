<?php
$conectar = new mysqli("localhost", "root", "ghil3412", "liga");

// Llamar al procedimiento almacenado para actualizar los estados
$stmtLlamarSP = $conectar->prepare("CALL ActualizarEstadoEventos()");
$stmtLlamarSP->execute();
$stmtLlamarSP->close();

// Obtener la fecha y hora actual
$fechaActual = date('Y-m-d H:i:s');

    // Seleccionar eventos que deben ser eliminados
    $stmt = $conectar->prepare("SELECT id, nombre, imagen FROM eventos WHERE fecha_eliminar < ?");
    $stmt->bind_param('s', $fechaActual);
    $stmt->execute();
    $result = $stmt->get_result();
    $eventosParaEliminar = $result->fetch_all(MYSQLI_ASSOC);

    // Eliminar eventos, carpetas y imágenes asociadas
    foreach ($eventosParaEliminar as $evento) {
        // Borrar carpeta en ../uploads/eventos/
        $carpetaEvento = "../uploads/eventos/" . $evento['nombre'] . "_" . $evento['id'];

        if ($evento && !empty($evento['imagen'])) {
            // Ruta completa de la imagen
            $rutaImagen = "../uploads/eventos/" . basename($evento['imagen']);
    
            // Eliminar la imagen del sistema de archivos
            if (file_exists($rutaImagen)) {
                unlink($rutaImagen);
            }
        } 
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
        // Borrar imágenes asociadas al evento en la tabla galeria_imagenes
        $stmtDeleteImagenes = $conectar->prepare("DELETE FROM galeria_imagenes WHERE nombre = ? AND id_tipo = ? AND tipo = 'Evento'");
        $stmtDeleteImagenes->bind_param('si', $evento['nombre'], $evento['id']);
        $stmtDeleteImagenes->execute();       

        // Borrar evento por id en la tabla eventos
        $stmtDeleteEvento = $conectar->prepare("DELETE FROM eventos WHERE id = ?");
        $stmtDeleteEvento->bind_param('i', $evento['id']);
        $stmtDeleteEvento->execute();

    }

// Cierra la conexión
$conectar->close();
?>