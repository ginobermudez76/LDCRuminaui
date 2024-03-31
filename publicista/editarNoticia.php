<?php
session_start();
include '../includes/config.php'; //incluyendo la conexión de la base de datos
if (!isset($_SESSION['usuario_admin'])) {
    header("Location: ../admin/login.php");
    exit();
}
$usuario_id = $_SESSION['usuario_id'];

try {
    // Consultar el rol del usuario en la base de datos
    $stmt = $conn->prepare("SELECT rol FROM usuarios WHERE id = :usuario_id");
    $stmt->bindParam(':usuario_id', $usuario_id, PDO::PARAM_INT);
    $stmt->execute();
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    // Verificar si el usuario tiene el rol de Publicista
    if ($usuario['rol'] == 7) {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            try {
                // Obtener los datos del formulario de edición
                $idNoticia = $_POST['idNoticia'];
                $titulo = $_POST['tituloEdit'];
                $cuerpo = $_POST['cuerpoEdit'];
        
                // Directorio de destino para laimagen
                $directorioDestino = "../uploads/noticias/";
        
                // Verificar si se proporcionó un nuevo archivo y moverlo al directorio de destino
                if (isset($_FILES['imagenEdit']) && $_FILES['imagenEdit']['error'] == 0) { 
        
                    $archivoNuevo = obtenerNombreArchivoNuevo($_FILES['imagenEdit']['name'], $directorioDestino);
        
                    // Eliminar el archivo antiguo del sistema de archivos
                    eliminarArchivoAntiguo($idNoticia, $directorioDestino);
        
                    // Mover el archivo al directorio de destino
                    if (!move_uploaded_file($_FILES["imagenEdit"]["tmp_name"], $archivoNuevo)) {
                        throw new Exception("Hubo un error al cargar la nueva imagen");
                    }
                    $stmt = $conn->prepare("UPDATE noticias SET imagen = ? WHERE id = ?"); // Corrección aquí
                    $stmt->execute([$archivoNuevo, $idNoticia]); // Corrección aquí
                }
        
                // Verificar si el checkbox está marcado
                if (isset($_POST['checkDImagen'])) {
                    eliminarArchivoYActualizarBD($idNoticia, $cuerpo, $titulo);
                } else {
                    // Actualizar la solicitud en la base de datos sin cambiar el archivo
                    actualizarBD($idNoticia, $cuerpo, $titulo);
                }
        
                // Redirigir después de editar
                header("Location: noticias.php");
                exit();
            } catch (Exception $e) {
                echo "Error: " . $e->getMessage();
            }
        } else {
            // Si no se recibieron datos por POST, redirigir a la página de lista de solicitudes
            header("Location: noticias.php");
            exit();
        }
    } else {
        header("Location: ../public/index.php");
        exit();
    }
} catch (PDOException $e) {
    $response['success'] = false;
    $response['message'] = "Error: " . $e->getMessage();
    echo json_encode($response);
}

// Definir las funciones fuera del bloque if para que estén disponibles en todo el script

function obtenerNombreArchivoNuevo($nombreArchivo, $directorioDestino) {
    $archivoNuevo = $directorioDestino . $nombreArchivo;

    $contador = 1;
    while (file_exists($archivoNuevo)) {
        $nombreArchivo = pathinfo($nombreArchivo, PATHINFO_FILENAME) . '_' . $contador . '.' . pathinfo($nombreArchivo, PATHINFO_EXTENSION);
        $archivoNuevo = $directorioDestino . $nombreArchivo;
        $contador++;
    }
    return $archivoNuevo;
}

function eliminarArchivoAntiguo($idNoticia, $directorioDestino) {
    global $conn;

    $stmt = $conn->prepare("SELECT imagen FROM noticias WHERE id = :id");
    $stmt->bindParam(':id', $idNoticia);
    $stmt->execute();
    $nombreArchivoEliminar = basename($stmt->fetch(PDO::FETCH_ASSOC)['imagen']);

    $rutaArchivoEliminar = $directorioDestino . $nombreArchivoEliminar;

    if (file_exists($rutaArchivoEliminar)) {
        unlink($rutaArchivoEliminar);
    }
}

function eliminarArchivoYActualizarBD($idNoticia, $titulo, $cuerpo) {
    global $conn;

    $stmt = $conn->prepare("SELECT imagen FROM noticias WHERE id = :id");
    $stmt->bindParam(':id', $idNoticia);
    $stmt->execute();
    $nombreArchivoEliminar = basename($stmt->fetch(PDO::FETCH_ASSOC)['imagen']); // Corrección aquí

    $rutaArchivoEliminar = "../uploads/noticias/" . $nombreArchivoEliminar;

    if (file_exists($rutaArchivoEliminar)) {
        unlink($rutaArchivoEliminar);
    }

    $stmt = $conn->prepare("UPDATE noticias SET imagen = NULL, cuerpo = ?, titulo = ? WHERE id = ?");
    $stmt->execute([$cuerpo, $titulo, $idNoticia]);
}

function actualizarBD($idNoticia, $cuerpo, $titulo) {
    global $conn;

    $stmt = $conn->prepare("UPDATE noticias SET cuerpo = ?, titulo = ? WHERE id = ?");
    $stmt->execute([$cuerpo, $titulo, $idNoticia]);
}
?>