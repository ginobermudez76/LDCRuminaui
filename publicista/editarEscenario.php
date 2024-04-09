<?php
session_start();
include '../includes/config.php';

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

        // Procesar la nueva imagen si se proporciona
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            try {
                $idEscenario = $_POST['idEscenarioEdit'];
                $nombre = $_POST['nombreEdit'];
                $ubicacion = $_POST['ubicacionEdit'];
                $direccion = $_POST['direccionEdit'];
                $telefono = $_POST['telefonoEdit'];
                $supervisor = $_POST['supervisorEdit'];
                $celular = $_POST['celularEdit'];

                // Directorio de destino para la imagen
                $directorioDestino = "../uploads/escenarios/";

                // Verificar si se proporcionó una nueva imagen y moverla al directorio de destino
                if (isset($_FILES['imagenEdit']) && $_FILES['imagenEdit']['error'] == 0) {

                    $archivoNuevo = obtenerNombreArchivoNuevo($_FILES['imagenEdit']['name'], $directorioDestino);

                    // Eliminar el archivo antiguo del sistema de archivos
                    eliminarArchivoAntiguo($idEscenario, $directorioDestino);

                    // Mover el archivo al directorio de destino
                    if (!move_uploaded_file($_FILES["imagenEdit"]["tmp_name"], $archivoNuevo)) {
                        throw new Exception("Hubo un error al cargar la nueva imagen");
                    }

                    // Actualizar la ruta de la imagen en la base de datos
                    $stmt = $conn->prepare("UPDATE escenarios SET imagen = ? WHERE id = ?");
                    $stmt->execute([$archivoNuevo, $idEscenario]);
                }

                // Verificar si el checkbox está marcado
                if (isset($_POST['checkDImagen'])) {
                    eliminarImagenYActualizarBD($idEscenario, $nombre, $ubicacion, $direccion, $telefono, $supervisor, $celular);
                } else {
                    // Actualizar la base de datos sin cambiar la imagen
                    actualizarBD($idEscenario, $nombre, $ubicacion, $direccion, $telefono, $supervisor, $celular);
                }

                // Redirigir después de editar
                echo "<script>window.location.href='../publicista/escenarios.php';</script>";
                exit();
            } catch (Exception $e) {
                echo "Error: " . $e->getMessage();
            }
        } else {
            // Si no se recibieron datos por POST, redirigir a la página de lista de escenarios
            echo "<script>window.location.href='../publicista/escenarios.php';</script>";
            exit();
        }
    } else {
        echo "<script>window.location.href='../public/index.php';</script>";
        exit();
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}

// Función para obtener un nombre de archivo único en un directorio dado
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

// Función para eliminar el archivo antiguo del sistema de archivos
function eliminarArchivoAntiguo($idEscenario, $directorioDestino)
{
    global $conn;

    $stmt = $conn->prepare("SELECT imagen FROM escenarios WHERE id = :id");
    $stmt->bindParam(':id', $idEscenario);
    $stmt->execute();
    $nombreArchivoEliminar = basename($stmt->fetch(PDO::FETCH_ASSOC)['imagen']);

    $rutaArchivoEliminar = $directorioDestino . $nombreArchivoEliminar;

    if (file_exists($rutaArchivoEliminar) && is_file($rutaArchivoEliminar)) {
        unlink($rutaArchivoEliminar);
    }
}

// Función para eliminar la imagen y actualizar la base de datos
function eliminarImagenYActualizarBD($idEscenario, $nombre, $ubicacion, $direccion, $telefono, $supervisor, $celular)
{
    global $conn;

    // Eliminar la imagen del sistema de archivos
    $stmt = $conn->prepare("SELECT imagen FROM escenarios WHERE id = :id");
    $stmt->bindParam(':id', $idEscenario);
    $stmt->execute();
    $nombreArchivoEliminar = basename($stmt->fetch(PDO::FETCH_ASSOC)['imagen']);

    $rutaArchivoEliminar = "../uploads/escenarios/" . $nombreArchivoEliminar;

    if (file_exists($rutaArchivoEliminar) && is_file($rutaArchivoEliminar)) {
        unlink($rutaArchivoEliminar);
    }

    // Actualizar la base de datos sin la imagen
    $stmt = $conn->prepare("UPDATE escenarios SET imagen = NULL, nombre = ?, ubicacion = ?, direccion = ?, telefono = ?, supervisor = ?, celular = ? WHERE id = ?");
    $stmt->execute([$nombre, $ubicacion, $direccion, $telefono, $supervisor, $celular, $idEscenario]);
}

// Función para actualizar la base de datos
function actualizarBD($idEscenario, $nombre, $ubicacion, $direccion, $telefono, $supervisor, $celular)
{
    global $conn;

    $stmt = $conn->prepare("UPDATE escenarios SET nombre = ?, ubicacion = ?, direccion = ?, telefono = ?, supervisor = ?, celular = ? WHERE id = ?");
    $stmt->execute([$nombre, $ubicacion, $direccion, $telefono, $supervisor, $celular, $idEscenario]);
}
?>
