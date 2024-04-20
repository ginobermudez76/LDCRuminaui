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
        // Mostrar el elemento del menú Administrar
        // Procesar el formulario si se envió
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            try {
                $idCurso = $_POST['idCursoEdit'];
                $nombre = $_POST['nombreEdit'];
                $descripcion = $_POST['descripcionEdit'];
                $fecha_ini = $_POST['fecha_iniEdit'];
                $fecha_f = $_POST['fecha_fEdit'];
                $deporte = $_POST['deporte_idEdit'];

                // Directorio de destino para el documento
                $directorioDestino = "../uploads/cursos/";

                // Verificar si se proporcionó un nuevo archivo y moverlo al directorio de destino
                if (isset($_FILES['imagenEdit']) && $_FILES['imagenEdit']['error'] == 0) {

                    $archivoNuevo = obtenerNombreArchivoNuevo($_FILES['imagenEdit']['name'], $directorioDestino);

                    // Eliminar el archivo antiguo del sistema de archivos
                    eliminarArchivoAntiguo($idCurso, $directorioDestino);

                    // Mover el archivo al directorio de destino
                    if (!move_uploaded_file($_FILES["imagenEdit"]["tmp_name"], $archivoNuevo)) {
                        throw new Exception("Hubo un error al cargar el nuevo documento");
                    }
                    $stmt = $conn->prepare("UPDATE cursos SET imagen =? WHERE id = ?");
                    $stmt->execute([$archivoNuevo, $idCurso]);
                }

                // Verificar si el checkbox está marcado
                if (isset($_POST['checkDImagen'])) {
                    eliminarArchivoYActualizarBD($idCurso, $nombre, $descripcion, $fecha_ini, $fecha_f, $deporte);
                } else {
                    // Actualizar la solicitud en la base de datos sin cambiar el archivo
                    actualizarBD($idCurso, $nombre, $descripcion, $fecha_ini, $fecha_f, $deporte);
                }


                // Redirigir después de editar
                echo "<script>window.location.href='../publicista/cursos.php';</script>";
                exit();
            } catch (Exception $e) {
                echo "Error: " . $e->getMessage();
            }
        } else {
            // Si no se recibieron datos por POST, redirigir a la página de lista de solicitudes
            echo "<script>window.location.href='../publicista/cursos.php';</script>";
            exit();
        }
    } else {
        echo "<script>window.location.href='../public/index.php';</script>";
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

function eliminarArchivoAntiguo($idCurso, $directorioDestino)
{
    global $conn;

    $stmt = $conn->prepare("SELECT imagen FROM cursos WHERE id = :id");
    $stmt->bindParam(':id', $idCurso);
    $stmt->execute();
    $nombreArchivoEliminar = basename($stmt->fetch(PDO::FETCH_ASSOC)['imagen']);

    $rutaArchivoEliminar = $directorioDestino . $nombreArchivoEliminar;

    if (file_exists($rutaArchivoEliminar) && is_file($rutaArchivoEliminar)) {
        unlink($rutaArchivoEliminar);
    }
}


function eliminarArchivoYActualizarBD($idCurso, $nombre, $descripcion, $fecha_ini, $fecha_f, $deporte)
{
    global $conn;

    $stmt = $conn->prepare("SELECT imagen FROM cursos WHERE id = :id");
    $stmt->bindParam(':id', $idCurso);
    $stmt->execute();
    $nombreArchivoEliminar = basename($stmt->fetch(PDO::FETCH_ASSOC)['imagen']);

    $rutaArchivoEliminar = "../uploads/cursos/" . $nombreArchivoEliminar;

    if (file_exists($rutaArchivoEliminar) && is_file($rutaArchivoEliminar)) {
        unlink($rutaArchivoEliminar);
    }

    $stmt = $conn->prepare("UPDATE cursos SET imagen = NULL, nombre = ?,  descripcion = ?, fecha_inicio =?, fecha_fin = ?, deporte_id = ? WHERE id = ?");
    $stmt->execute([$nombre, $descripcion, $fecha_ini, $fecha_f, $deporte, $idCurso]);
}

function actualizarBD($idCurso, $nombre, $descripcion, $fecha_ini, $fecha_f, $deporte)
{
    global $conn;

    $stmt = $conn->prepare("UPDATE cursos SET  nombre = ?, descripcion = ?, fecha_inicio = ?, fecha_fin = ?, deporte_id = ? WHERE id = ?");
    $stmt->execute([$nombre, $descripcion, $fecha_ini, $fecha_f, $deporte, $idCurso]);
}