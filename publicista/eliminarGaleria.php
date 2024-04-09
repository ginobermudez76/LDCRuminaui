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

        // Eliminación de imágenes seleccionadas
        if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['eliminar'])) {
            // Obtener las imágenes seleccionadas
            $imagenesAEliminar = $_POST['eliminar'];
            $tipo = $_POST['tipo'];
            $idEvento = $_POST['idEvento'];
            $nombreEvento = $_POST['nombreEvento'];

            // Ruta de la carpeta de imágenes
            switch ($tipo) {
                case "Evento":
                    $carpetaImagenes = "../uploads/eventos/" . $nombreEvento . "_" . $idEvento;
                    break;
                case "Deporte":
                    $carpetaImagenes = "../uploads/deportes/" . $nombreEvento . "_" . $idEvento;
                    break;
                case "Curso":
                    $carpetaImagenes = "../uploads/cursos/" . $nombreEvento . "_" . $idEvento;
                    break;
                default:
                    echo "Tipo de evento no válido";
                    exit();
            }

            foreach ($imagenesAEliminar as $imagen) {
                // Construir la ruta completa de la imagen
                $rutaImagen = $carpetaImagenes . "/" . $imagen;

                // Eliminar la imagen si existe
                if (file_exists($rutaImagen)) {
                    unlink($rutaImagen);
                }

                // Eliminar la entrada de la base de datos
                $stmtEliminarImagen = $conn->prepare("DELETE FROM galeria_imagenes WHERE tipo = ? AND id_tipo = ? AND nombre = ? AND ruta_imagenes = ?");
                $stmtEliminarImagen->execute([$tipo, $idEvento, $nombreEvento, $rutaImagen]);
            }
            if ($tipo == "Evento") {
                header("Location: eventos.php");
            } elseif ($tipo == "Deporte") {
                header("Location: deportes.php");
            } elseif ($tipo == "Curso") {
                header("Location: cursos.php");
            }
        }
    } else {
        echo "<script>window.location.href='../public/index.php';</script>";
        exit();
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
