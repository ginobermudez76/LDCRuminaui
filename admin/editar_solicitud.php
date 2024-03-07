<?php
session_start();
include '../includes/config.php';

// Verificar si el usuario está autenticado como administrador
if (!isset($_SESSION['usuario_admin'])) {
    header("Location: ../admin/login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        // Obtener los datos del formulario de edición
        $idSolicitud = $_POST['idSolicitud'];
        $descripcion = $_POST['descripcion'];
        $valor = isset($_POST['valor']) ? $_POST['valor'] : null;
        $tipo = $_POST['tipoEdit'];

        // Verificar si el valor es numérico
        if (!is_numeric($valor)) {
            $valor = null;
        }

        // Directorio de destino para el documento
        $directorioDestino = "../uploads/documentos/solicitudes/";

        // Verificar si se proporcionó un nuevo archivo y moverlo al directorio de destino
        if (isset($_FILES['documento']) && $_FILES['documento']['error'] == 0) {
            $nombreUsuario = obtenerNombreUsuario($idSolicitud);

            $archivoNuevo = obtenerNombreArchivoNuevo($_FILES['documento']['name'], $nombreUsuario, $directorioDestino);

            // Eliminar el archivo antiguo del sistema de archivos
            eliminarArchivoAntiguo($idSolicitud, $nombreUsuario, $directorioDestino);

            // Mover el archivo al directorio de destino
            if (!move_uploaded_file($_FILES["documento"]["tmp_name"], $archivoNuevo)) {
                throw new Exception("Hubo un error al cargar el nuevo documento");
            }
            $stmt = $conn->prepare("UPDATE solicitud SET s_doc = ? WHERE s_id = ?");
            $stmt->execute([$archivoNuevo, $idSolicitud]);
        }

        // Verificar si el checkbox está marcado
        if (isset($_POST['checkDArchivo'])) {
            eliminarArchivoYActualizarBD($idSolicitud, $valor, $tipo, $descripcion);
        } else {
            // Actualizar la solicitud en la base de datos sin cambiar el archivo
            actualizarBD($idSolicitud, $valor, $tipo, $descripcion);
        }

        // Redirigir después de editar
        header("Location: solicitudes.php");
        exit();
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }
} else {
    // Si no se recibieron datos por POST, redirigir a la página de lista de solicitudes
    header("Location: solicitudes.php");
    exit();
}

function obtenerNombreUsuario($idSolicitud) {
    global $conn;

    $stmt = $conn->prepare("SELECT solicitante FROM solicitud WHERE s_id = :id");
    $stmt->bindParam(':id', $idSolicitud);
    $stmt->execute();
    $solicitanteId = $stmt->fetch(PDO::FETCH_ASSOC)['solicitante'];

    $stmt = $conn->prepare("SELECT nombre_usuario FROM usuarios WHERE id = :solicitante");
    $stmt->bindParam(':solicitante', $solicitanteId, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC)['nombre_usuario'];
}

function obtenerNombreArchivoNuevo($nombreArchivo, $nombreUsuario, $directorioDestino) {
    $archivoNuevo = $directorioDestino . $nombreUsuario . "/" . $nombreArchivo;

    $contador = 1;
    while (file_exists($archivoNuevo)) {
        $nombreArchivo = pathinfo($nombreArchivo, PATHINFO_FILENAME) . '_' . $contador . '.' . pathinfo($nombreArchivo, PATHINFO_EXTENSION);
        $archivoNuevo = $directorioDestino . $nombreUsuario . "/" . $nombreArchivo;
        $contador++;
    }
    return $archivoNuevo;
}

function eliminarArchivoAntiguo($idSolicitud, $nombreUsuario, $directorioDestino) {
    global $conn;

    $stmt = $conn->prepare("SELECT s_doc FROM solicitud WHERE s_id = :id");
    $stmt->bindParam(':id', $idSolicitud);
    $stmt->execute();
    $nombreArchivoEliminar = basename($stmt->fetch(PDO::FETCH_ASSOC)['s_doc']);

    $rutaArchivoEliminar = $directorioDestino . $nombreUsuario . "/" . $nombreArchivoEliminar;

    if (file_exists($rutaArchivoEliminar)) {
        unlink($rutaArchivoEliminar);
    }
}

function eliminarArchivoYActualizarBD($idSolicitud, $valor, $tipo, $descripcion) {
    global $conn;

    $nombreUsuario = obtenerNombreUsuario($idSolicitud);

    $stmt = $conn->prepare("SELECT s_doc FROM solicitud WHERE s_id = :id");
    $stmt->bindParam(':id', $idSolicitud);
    $stmt->execute();
    $nombreArchivoEliminar = basename($stmt->fetch(PDO::FETCH_ASSOC)['s_doc']);

    $rutaArchivoEliminar = "../uploads/documentos/solicitudes/" . $nombreUsuario . "/" . $nombreArchivoEliminar;

    if (file_exists($rutaArchivoEliminar)) {
        unlink($rutaArchivoEliminar);
    }

    $stmt = $conn->prepare("UPDATE solicitud SET s_doc = NULL, s_valor = ?, tipo = ?, descripcion = ? WHERE s_id = ?");
    $stmt->execute([$valor, $tipo, $descripcion, $idSolicitud]);
}

function actualizarBD($idSolicitud, $valor, $tipo, $descripcion) {
    global $conn;

    $stmt = $conn->prepare("UPDATE solicitud SET s_valor = ?, tipo = ?, descripcion = ? WHERE s_id = ?");
    $stmt->execute([$valor, $tipo, $descripcion, $idSolicitud]);
}
?>
