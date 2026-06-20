<?php
$conectar = new mysqli("localhost", "root", "ghil3412", "liga");

// Llamar al procedimiento almacenado para actualizar los estados de eventos
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
    $rutaImagen = "";

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

$stmtLlamarSPCursos = $conectar->prepare("CALL ActualizarEstadoCursos()");
$stmtLlamarSPCursos->execute();
$stmtLlamarSPCursos->close();

// Obtener la fecha y hora actual
$fechaActual2 = date('Y-m-d H:i:s');

// Seleccionar cursos que deben ser eliminados
$stmt1 = $conectar->prepare("SELECT id, nombre, imagen FROM cursos WHERE fecha_eliminar < ?");
$stmt1->bind_param('s', $fechaActual2);
$stmt1->execute();
$result1 = $stmt1->get_result();
$cursosParaEliminar = $result1->fetch_all(MYSQLI_ASSOC);

// Eliminar cursos, carpetas y imágenes asociadas
foreach ($cursosParaEliminar as $curso) {
    // Borrar carpeta en ../uploads/curso/
    $carpetaCurso = "../uploads/cursos/" . $curso['nombre'] . "_" . $curso['id'];
    $rutaImagen1 = "";

    if ($curso && !empty($curso['imagen'])) {
        // Ruta completa de la imagen
        $rutaImagen1 = "../uploads/cursos/" . basename($curso['imagen']);

        // Eliminar la imagen del sistema de archivos
        if (file_exists($rutaImagen1)) {
            unlink($rutaImagen1);
        }
    }

    if (file_exists($carpetaCurso) && is_dir($carpetaCurso)) {
        // Eliminar carpeta y su contenido
        $files = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($carpetaCurso, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::CHILD_FIRST
        );

        foreach ($files as $fileinfo) {
            $action = ($fileinfo->isDir() ? 'rmdir' : 'unlink');
            $action($fileinfo->getRealPath());
        }

        rmdir($carpetaCurso);
    }

    // Borrar imágenes asociadas al curso en la tabla galeria_imagenes
    $stmtDeleteImagenes1 = $conectar->prepare("DELETE FROM galeria_imagenes WHERE nombre = ? AND id_tipo = ? AND tipo = 'Curso'");
    $stmtDeleteImagenes1->bind_param('si', $curso['nombre'], $curso['id']);
    $stmtDeleteImagenes1->execute();

    // Borrar curso por id en la tabla cursos
    $stmtDeleteCurso = $conectar->prepare("DELETE FROM cursos WHERE id = ?");
    $stmtDeleteCurso->bind_param('i', $curso['id']);
    $stmtDeleteCurso->execute();
}

// Seleccionar cartas de condolencia que deben ser eliminadas
$stmt2 = $conectar->prepare("SELECT id, imagen FROM carta_condolencias WHERE fecha_eliminar < ?");
$stmt2->bind_param('s', $fechaActual2);
$stmt2->execute();
$result2 = $stmt2->get_result();
$eliminarCondolencias = $result2->fetch_all(MYSQLI_ASSOC);

// Eliminar cartas de condolencia e imágenes asociadas
foreach ($eliminarCondolencias as $condolencia) {
    if ($condolencia && !empty($condolencia['imagen'])) {
        // Ruta completa de la imagen
        $rutaImagen2 = "../uploads/cartaCondolencia/" . basename($condolencia['imagen']);

        // Eliminar la imagen del sistema de archivos
        if (file_exists($rutaImagen2)) {
            unlink($rutaImagen2);
        }
    }

    // Borrar carta de condolencia por id en la tabla carta_condolencias
    $stmtDeleteCondolencia = $conectar->prepare("DELETE FROM carta_condolencias WHERE id = ?");
    $stmtDeleteCondolencia->bind_param('i', $condolencia['id']);
    $stmtDeleteCondolencia->execute();
}

// Cierra la conexión
$conectar->close();
?>