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
                $idDeportista = $_POST['idDeportistaEdit']; // Corrección aquí
                $nombre = $_POST['nombreEdit'];
                $deporte = $_POST['deporte_idEdit'];
        
                // Directorio de destino para el documento
                $directorioDestino = "../uploads/deportes/deportistas/";
        
                // Verificar si se proporcionó un nuevo archivo y moverlo al directorio de destino
                if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] == 0) { // Corrección aquí
        
                    $archivoNuevo = obtenerNombreArchivoNuevo($_FILES['imagen']['name'], $directorioDestino); // Corrección aquí
        
                    // Eliminar el archivo antiguo del sistema de archivos
                    eliminarArchivoAntiguo($idDeportista, $directorioDestino);
        
                    // Mover el archivo al directorio de destino
                    if (!move_uploaded_file($_FILES["imagen"]["tmp_name"], $archivoNuevo)) {
                        throw new Exception("Hubo un error al cargar el nuevo documento");
                    }
                    $stmt = $conn->prepare("UPDATE deportistas_destacados SET imagen = ? WHERE id = ?"); // Corrección aquí
                    $stmt->execute([$archivoNuevo, $idDeportista]); // Corrección aquí
                }
        
                // Verificar si el checkbox está marcado
                if (isset($_POST['checkDImagen'])) {
                    eliminarArchivoYActualizarBD($idDeportista, $nombre, $deporte);
                } else {
                    // Actualizar la solicitud en la base de datos sin cambiar el archivo
                    actualizarBD($idDeportista, $deporte, $nombre);
                }
        
                // Redirigir después de editar
                header("Location: deportistas_destacados.php");
                exit();
            } catch (Exception $e) {
                echo "Error: " . $e->getMessage();
            }
        } else {
            // Si no se recibieron datos por POST, redirigir a la página de lista de solicitudes
            header("Location: deportistas_destacados.php");
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

function eliminarArchivoAntiguo($idDeportista, $directorioDestino) {
    global $conn;

    $stmt = $conn->prepare("SELECT imagen FROM deportistas_destacados WHERE id = :id");
    $stmt->bindParam(':id', $idDeportista);
    $stmt->execute();
    $nombreArchivoEliminar = basename($stmt->fetch(PDO::FETCH_ASSOC)['imagen']); // Corrección aquí

    $rutaArchivoEliminar = $directorioDestino . $nombreArchivoEliminar;

    if (file_exists($rutaArchivoEliminar)) {
        unlink($rutaArchivoEliminar);
    }
}

function eliminarArchivoYActualizarBD($idDeportista, $nombre, $deporte) {
    global $conn;

    $stmt = $conn->prepare("SELECT imagen FROM deportistas_destacados WHERE id = :id");
    $stmt->bindParam(':id', $idDeportista);
    $stmt->execute();
    $nombreArchivoEliminar = basename($stmt->fetch(PDO::FETCH_ASSOC)['imagen']); // Corrección aquí

    $rutaArchivoEliminar = "../uploads/deportes/deportistas/" . $nombreArchivoEliminar;

    if (file_exists($rutaArchivoEliminar)) {
        unlink($rutaArchivoEliminar);
    }

    $stmt = $conn->prepare("UPDATE deportistas_destacados SET imagen = NULL, deporte_id = ?, nombre_deportista = ? WHERE id = ?");
    $stmt->execute([$deporte, $nombre, $idDeportista]);
}

function actualizarBD($idDeportista, $deporte, $nombre) {
    global $conn;

    $stmt = $conn->prepare("UPDATE deportistas_destacados SET deporte_id = ?, nombre_deportista = ? WHERE id = ?");
    $stmt->execute([$deporte, $nombre, $idDeportista]);
}
?>