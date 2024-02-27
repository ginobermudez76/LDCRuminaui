<?php
session_start();
if (!isset($_SESSION['usuario_admin'])) {
    header("Location: ../admin/login.php");
    exit();
}

include '../includes/config.php'; // Incluyendo la conexión a la base de datos


// Verificar si se recibió un ID válido y realizar la eliminación en la base de datos
if (isset($_POST['id'])) {
    $idSolicitud = filter_var($_POST['id'], FILTER_SANITIZE_NUMBER_INT);


    try { 

        // Obtener la ruta del documento y el id de solicitante almacenados en la base de datos
        $stmt = $conn->prepare("SELECT s_doc, solicitante FROM solicitud WHERE s_id = :id");
        $stmt->bindParam(':id', $idSolicitud);
        $stmt->execute();
        $solicitud = $stmt->fetch(PDO::FETCH_ASSOC);
        $solicitanteId = $solicitud['solicitante'];
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }

    if ($solicitud && !empty($solicitud['s_doc'])) {
        // Obtener el nombre de usuario
        try {
            $stmt = $conn->prepare("SELECT nombre_usuario FROM usuarios WHERE id = :solicitante");
            $stmt->bindParam(':solicitante', $solicitanteId, PDO::PARAM_INT);
            $stmt->execute();
            $nombreUsuario = $stmt->fetch(PDO::FETCH_ASSOC)['nombre_usuario'];
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    
        // Ruta completa del documento
        $rutaDocumento = "../uploads/documentos/solicitudes/" . $nombreUsuario . "/" . basename($solicitud['s_doc']);
       
        // Eliminar el documento del sistema de archivos
        if (file_exists($rutaDocumento)) {
        
            unlink($rutaDocumento);

        }
    }
    

    // Eliminar el solicitud de la base de datos
    try {
        $stmtDeleteSolicitud = $conn->prepare("DELETE FROM solicitud WHERE s_id = :id");
        $stmtDeleteSolicitud->bindParam(':id', $idSolicitud);
        $stmtDeleteSolicitud->execute();
        echo "Solicitud eliminada con exito.";
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
} else {
    echo "ID de solicitud no proporcionado";
}
