<?php
session_start();
include '../includes/config.php'; //incluyendo la conexión de la base de datos
if (!isset($_SESSION['usuario_admin'])) {
    echo "<script>window.location.href='../admin/login.php';</script>";
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
                $idCarta = $_POST['idCarta']; // Corrección aquí
                $mensaje = $_POST['mensajeEdit'];
        
                // Directorio de destino para el documento
                $directorioDestino = "../uploads/cartaCondolencia/";
        
                // Verificar si se proporcionó un nuevo archivo y moverlo al directorio de destino
                if (isset($_FILES['imagenEdit']) && $_FILES['imagenEdit']['error'] == 0) { // Corrección aquí
        
                    $archivoNuevo = obtenerNombreArchivoNuevo($_FILES['imagenEdit']['name'], $directorioDestino); // Corrección aquí
        
                    // Eliminar el archivo antiguo del sistema de archivos
                    eliminarArchivoAntiguo($idCarta, $directorioDestino);
        
                    // Mover el archivo al directorio de destino
                    if (!move_uploaded_file($_FILES["imagenEdit"]["tmp_name"], $archivoNuevo)) {
                        throw new Exception("Hubo un error al cargar el nuevo documento");
                    }
                    $stmt = $conn->prepare("UPDATE carta_condolencias SET imagen = ? WHERE id = ?"); // Corrección aquí
                    $stmt->execute([$archivoNuevo, $idCarta]); // Corrección aquí
                }
        
                // Verificar si el checkbox está marcado
                if (isset($_POST['checkDImagen'])) {
                    eliminarArchivoYActualizarBD($idCarta, $mensaje);
                } else {
                    // Actualizar la solicitud en la base de datos sin cambiar el archivo
                    actualizarBD($idCarta, $mensaje);
                }
        
                // Redirigir después de editar
                echo "<script>window.location.href='../publicista/carta_de_condolencias.php';</script>";
                exit();
            } catch (Exception $e) {
                echo "Error: " . $e->getMessage();
            }
        } else {
            // Si no se recibieron datos por POST, redirigir a la página de lista de solicitudes
            echo "<script>window.location.href='../publicista/carta_de_condolencias.php';</script>";
            exit();
        }
    } else {
        echo "<script>window.location.href='../public/index.php';</script>";
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

function eliminarArchivoAntiguo($idCarta, $directorioDestino) {
    global $conn;

    $stmt = $conn->prepare("SELECT imagen FROM carta_condolencias WHERE id = :id");
    $stmt->bindParam(':id', $idCarta);
    $stmt->execute();
    $nombreArchivoEliminar = basename($stmt->fetch(PDO::FETCH_ASSOC)['imagen']); // Corrección aquí

    $rutaArchivoEliminar = $directorioDestino . $nombreArchivoEliminar;

    if (file_exists($rutaArchivoEliminar) && is_file($rutaArchivoEliminar)) {
        unlink($rutaArchivoEliminar);
    }
}

function eliminarArchivoYActualizarBD($idCarta, $mensaje) {
    global $conn;

    $stmt = $conn->prepare("SELECT imagen FROM carta_condolencias WHERE id = :id");
    $stmt->bindParam(':id', $idCarta);
    $stmt->execute();
    $nombreArchivoEliminar = basename($stmt->fetch(PDO::FETCH_ASSOC)['imagen']); // Corrección aquí

    $rutaArchivoEliminar = "../uploads/cartaCondolencia/" . $nombreArchivoEliminar;

    if (file_exists($rutaArchivoEliminar) && is_file($rutaArchivoEliminar)) {
        unlink($rutaArchivoEliminar);
    }

    $stmt = $conn->prepare("UPDATE carta_condolencias SET imagen = NULL, mensaje = ? WHERE id = ?");
    $stmt->execute([$mensaje, $idCarta]);
}

function actualizarBD($idCarta, $mensaje) {
    global $conn;

    $stmt = $conn->prepare("UPDATE carta_condolencias SET mensaje = ? WHERE id = ?");
    $stmt->execute([$mensaje, $idCarta]);
}
?>