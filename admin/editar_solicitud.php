<?php
session_start();
include '../includes/config.php';

// Verificar si el usuario está autenticado como administrador
if (!isset($_SESSION['usuario_admin'])) {
    header("Location: /Ayudantias-1/admin/login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obtener los datos del formulario de edición
    $idSolicitud = $_POST['idSolicitud'];
    $descripcion = $_POST['descripcion'];
    $valor = isset($_POST['valor']) ? $_POST['valor'] : null;
    $tipo = $_POST['tipo_id'];

    // Verificar si el valor es numérico
    if (!is_numeric($valor)) {
        $valor = null;
    }

    // Directorio de destino para el documento
    $directorioDestino = "../uploads/documentos/solicitudes/";

    // Obtener el nombre de usuario y el nombre del archivo antiguo
    try {
        $stmt = $conn->prepare("SELECT s_doc, solicitante FROM solicitud WHERE s_id = :id");
        $stmt->bindParam(':id', $idSolicitud);
        $stmt->execute();
        $solicitud = $stmt->fetch(PDO::FETCH_ASSOC);
        $solicitanteId = $solicitud['solicitante'];
        $nombreArchivoAntiguo = basename($solicitud['s_doc']);
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
    //Buscar el nombre de usuario del solicitante en la tabla usuario
    try {
        $stmt = $conn->prepare("SELECT nombre_usuario FROM usuarios WHERE id = :solicitante");
        $stmt->bindParam(':solicitante', $solicitanteId, PDO::PARAM_INT);
        $stmt->execute();
        $nombreUsuario = $stmt->fetch(PDO::FETCH_ASSOC)['nombre_usuario'];
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
    // Verificar si se proporcionó un nuevo archivo y moverlo al directorio de destino
    if (isset($_FILES['documento']) && $_FILES['documento']['error'] == 0) {
        $nombreArchivoNuevo = basename($_FILES['documento']['name']);
        $archivoNuevo = $directorioDestino  . $nombreUsuario . "/" . $nombreArchivoNuevo;

        // Eliminar el archivo antiguo del sistema de archivos
        $rutaArchivoAntiguo = $directorioDestino  . $nombreUsuario . "/" . $nombreArchivoAntiguo;
        if (file_exists($rutaArchivoAntiguo)) {
            unlink($rutaArchivoAntiguo);
        }
        if (file_exists($rutaArchivoAntiguo)) {
            unlink($rutaArchivoAntiguo);
        }
        // Mover el archivo al directorio de destino
        if (!move_uploaded_file($_FILES["documento"]["tmp_name"], $archivoNuevo)) {
            echo "Hubo un error al cargar el nuevo documento";
            exit();
        }
    try {
        
        // Verificar si el valor es numérico
        if (!is_numeric($valor)) {
            $valor = null;
            
        }
        // Actualizar la solicitud en la base de datos
        $stmt = $conn->prepare("UPDATE solicitud SET s_doc = ?, s_valor = ?, tipo = ?, descripcion = ? WHERE s_id = ?");
        $stmt->execute([$archivoNuevo, $valor, $tipo, $descripcion, $idSolicitud]);

        // Redirigir después de editar
        header("Location: tbsolicitud.php");
        exit();
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
    } else {
        // Si no se proporcionó un nuevo archivo, conservar el archivo antiguo

            // No se proporcionó un nuevo archivo, conservar el archivo anterior
    try {
        if (!is_numeric($valor)) {
            $valor = null;
            
        }
        // Actualizar la solicitud en la base de datos sin cambiar el archivo
        $stmt = $conn->prepare("UPDATE solicitud SET s_valor = ?, tipo = ?, descripcion = ? WHERE s_id = ?");
        $stmt->execute([$valor, $tipo, $descripcion, $idSolicitud]);

        // Redirigir después de editar
        header("Location: tbsolicitud.php");
        exit();
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
    }


} else {
    // Si no se recibieron datos por POST, redirigir a la página de lista de solicitudes
    header("Location: tbsolicitud.php");
    exit();
}
?>
