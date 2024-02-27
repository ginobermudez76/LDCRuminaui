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
        $tipo = $_POST['tipo_id'];

        // Verificar si el valor es numérico
        if (!is_numeric($valor)) {
            $valor = null;
        }

        // Directorio de destino para el documento
        $directorioDestino = "../uploads/documentos/solicitudes/";

        // Obtener el nombre de usuario y el nombre del archivo antiguo
        $stmt = $conn->prepare("SELECT s_doc, solicitante FROM solicitud WHERE s_id = :id");
        $stmt->bindParam(':id', $idSolicitud);
        $stmt->execute();
        $solicitud = $stmt->fetch(PDO::FETCH_ASSOC);
        $solicitanteId = $solicitud['solicitante'];
        $nombreArchivoAntiguo = basename($solicitud['s_doc']);

        //Buscar el nombre de usuario del solicitante en la tabla usuario
        $stmt = $conn->prepare("SELECT nombre_usuario FROM usuarios WHERE id = :solicitante");
        $stmt->bindParam(':solicitante', $solicitanteId, PDO::PARAM_INT);
        $stmt->execute();
        $nombreUsuario = $stmt->fetch(PDO::FETCH_ASSOC)['nombre_usuario'];

        // Verificar si se proporcionó un nuevo archivo y moverlo al directorio de destino
        if (isset($_FILES['documento']) && $_FILES['documento']['error'] == 0) {
            $nombreArchivoNuevo = basename($_FILES['documento']['name']);
            $archivoNuevo = $directorioDestino  . $nombreUsuario . "/" . $nombreArchivoNuevo;

            // Eliminar el archivo antiguo del sistema de archivos
            $rutaArchivoAntiguo = $directorioDestino  . $nombreUsuario . "/" . $nombreArchivoAntiguo;

            if (file_exists($rutaArchivoAntiguo)) {
                unlink($rutaArchivoAntiguo);
            }
            // Verificar si el archivo ya existe y renombrarlo si es necesario
            $contador = 1;
            while (file_exists($archivoNuevo)) {
                $nombreArchivo = pathinfo($_FILES['documento']['name'], PATHINFO_FILENAME) . '_' . $contador . '.' . pathinfo($_FILES['documento']['name'], PATHINFO_EXTENSION);
                $archivoNuevo = $directorioDestino . $nombreUsuario . "/" . $nombreArchivo;
                $contador++;
            }
            // Mover el archivo al directorio de destino
            if (!move_uploaded_file($_FILES["documento"]["tmp_name"], $archivoNuevo)) {
                throw new Exception("Hubo un error al cargar el nuevo documento");
            }

            // Actualizar la solicitud en la base de datos
            $stmt = $conn->prepare("UPDATE solicitud SET s_doc = ?, s_valor = ?, tipo = ?, descripcion = ? WHERE s_id = ?");
            $stmt->execute([$archivoNuevo, $valor, $tipo, $descripcion, $idSolicitud]);
        } else {

            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                // Directorio de destino para el documento
                $directorioArchivoEliminar = "../uploads/documentos/solicitudes/";
                try {

                    //Buscar el nombre de usuario del solicitante en la tabla usuario
                    $stmt = $conn->prepare("SELECT nombre_usuario FROM usuarios WHERE id = :solicitante");
                    $stmt->bindParam(':solicitante', $solicitanteId, PDO::PARAM_INT);
                    $stmt->execute();
                    $nombreUsuario = $stmt->fetch(PDO::FETCH_ASSOC)['nombre_usuario'];

                    // Obtener el nombre de usuario y el nombre del archivo antiguo
                    $stmt = $conn->prepare("SELECT s_doc FROM solicitud WHERE s_id = :id");
                    $stmt->bindParam(':id', $idSolicitud);
                    $stmt->execute();
                    $solicitud = $stmt->fetch(PDO::FETCH_ASSOC);
                    $nombreArchivoEliminar = basename($solicitud['s_doc']);

                    // Verificar si el checkbox está marcado
                    if (isset($_POST['checkDArchivo'])) {

                        // Actualizar la solicitud en la base de datos con s_doc como NULL
                        $stmt = $conn->prepare("UPDATE solicitud SET s_doc = NULL, s_valor = ?, tipo = ?, descripcion = ? WHERE s_id = ?");
                        $stmt->execute([$valor, $tipo, $descripcion, $idSolicitud]);

                        $rutaArchivoEliminar = $directorioArchivoEliminar  . $nombreUsuario . "/" . $nombreArchivoEliminar;
                        // Eliminar el archivo del sistema de archivos
                        if (file_exists($rutaArchivoEliminar)) {
                            unlink($rutaArchivoEliminar);
                        }
                    }
                } catch (PDOException $e) {
                    echo "Error: " . $e->getMessage();
                }
            } else {
                // Actualizar la solicitud en la base de datos sin cambiar el archivo
                $stmt = $conn->prepare("UPDATE solicitud SET s_valor = ?, tipo = ?, descripcion = ? WHERE s_id = ?");
                $stmt->execute([$valor, $tipo, $descripcion, $idSolicitud]);
            }
        }

        // Redirigir después de editar
        header("Location: tbsolicitud.php");
        exit();
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }
} else {
    // Si no se recibieron datos por POST, redirigir a la página de lista de solicitudes
    header("Location: tbsolicitud.php");
    exit();
}
