<?php
session_start();
include '../includes/config.php';

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

        // Procesar la nueva imagen si se proporciona
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            try {
                $idDeporte = $_POST['idDeporteEdit'];
                $nombre = $_POST['nombreEdit'];
                $descripcion = $_POST['descripcionEdit'];

                // Directorio de destino para el documento
                $directorioDestino = "../uploads/deportes/";

                // Verificar si se proporcionó un nuevo archivo y moverlo al directorio de destino
                if (isset($_FILES['imagenEdit']) && $_FILES['imagenEdit']['error'] == 0) {

                    $archivoNuevo = obtenerNombreArchivoNuevo($_FILES['imagenEdit']['name'], $directorioDestino);

                    // Eliminar el archivo antiguo del sistema de archivos
                    eliminarArchivoAntiguo($idDeporte, $directorioDestino);

                    // Mover el archivo al directorio de destino
                    if (!move_uploaded_file($_FILES["imagenEdit"]["tmp_name"], $archivoNuevo)) {
                        throw new Exception("Hubo un error al cargar el nuevo documento");
                    }
                    $stmt = $conn->prepare("UPDATE deportes SET imagen =? WHERE id = ?");
                    $stmt->execute([$archivoNuevo, $idDeporte]);
                }

                // Verificar si el checkbox está marcado
                if (isset($_POST['checkDImagen'])) {
                    eliminarArchivoYActualizarBD($idDeporte, $nombre, $descripcion);
                } else {
                    // Actualizar la solicitud en la base de datos sin cambiar el archivo
                    actualizarBD($idDeporte, $nombre, $descripcion);
                }


                // Redirigir después de editar
                header("Location: deportes.php");
                exit();
            } catch (Exception $e) {
                echo "Error: " . $e->getMessage();
            }
        } else {
            // Si no se recibieron datos por POST, redirigir a la página de lista de solicitudes
            header("Location: deportes.php");
            exit();
        }
    } else {
        header("Location: ../public/index.php");
        exit();
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}


function obtenerNombreArchivoNuevo($nombreArchivo, $directorioDestino)
{
    $archivoNuevo = $directorioDestino . $nombreArchivo;

    $contador = 1;
    while (file_exists($archivoNuevo)) {
        $nombreArchivo = pathinfo($nombreArchivo, PATHINFO_FILENAME) . '_' . $contador . '.' . pathinfo($nombreArchivo, PATHINFO_EXTENSION);
        $archivoNuevo = $directorioDestino . $nombreArchivo;
        $contador++;
    }
    return $archivoNuevo;
}

function eliminarArchivoAntiguo($idDeporte, $directorioDestino)
{
    global $conn;

    $stmt = $conn->prepare("SELECT imagen FROM deportes WHERE id = :id");
    $stmt->bindParam(':id', $idDeporte);
    $stmt->execute();
    $nombreArchivoEliminar = basename($stmt->fetch(PDO::FETCH_ASSOC)['imagen']);

    $rutaArchivoEliminar = $directorioDestino . $nombreArchivoEliminar;

    if (file_exists($rutaArchivoEliminar)) {
        unlink($rutaArchivoEliminar);
    }
}

function eliminarArchivoYActualizarBD($idDeporte, $nombre, $descripcion)
{
    global $conn;

    $stmt = $conn->prepare("SELECT imagen FROM deportes WHERE id = :id");
    $stmt->bindParam(':id', $idDeporte);
    $stmt->execute();
    $nombreArchivoEliminar = basename($stmt->fetch(PDO::FETCH_ASSOC)['imagen']);

    $rutaArchivoEliminar = "../uploads/deportes/" . $nombreArchivoEliminar;

    if (file_exists($rutaArchivoEliminar)) {
        unlink($rutaArchivoEliminar);
    }

    $stmt = $conn->prepare("UPDATE deportes SET imagen = NULL, nombre = ?,  descripcion = ? WHERE id = ?");
    $stmt->execute([$nombre, $descripcion, $idDeporte]);
}

function actualizarBD($idDeporte, $nombre, $descripcion)
{
    global $conn;

    $stmt = $conn->prepare("UPDATE deportes SET  nombre = ?, descripcion = ? WHERE id = ?");
    $stmt->execute([$nombre, $descripcion, $idDeporte]);
}
?>