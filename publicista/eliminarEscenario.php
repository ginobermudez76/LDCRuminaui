<?php
session_start();
if (!isset($_SESSION['usuario_admin'])) {
    echo "<script>window.location.href='../admin/login.php';</script>";
    exit();
}

include '../includes/config.php'; //incluyendo la conexión a la base de datos
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
        // Verificar si se recibió un ID válido y realizar la eliminación en la base de datos
        if (isset($_POST['id'])) {
            $idEscenario = $_POST['id'];

            // Obtener la ruta de la imagen almacenada en la base de datos
            $stmt = $conn->prepare("SELECT imagen FROM escenarios WHERE id = :id");
            $stmt->bindParam(':id', $idEscenario);
            $stmt->execute();
            $escenario = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($escenario && !empty($escenario['imagen'])) {
                // Ruta completa de la imagen
                $rutaImagen = "../uploads/escenarios/" . basename($escenario['imagen']);

                // Eliminar la imagen del sistema de archivos
                if (file_exists($rutaImagen)) {
                    unlink($rutaImagen);
                }
            }

            // Eliminar el escenario de la base de datos
            try {
                $stmtDeleteEscenario = $conn->prepare("DELETE FROM escenarios WHERE id = :id");
                $stmtDeleteEscenario->bindParam(':id', $idEscenario);
                $stmtDeleteEscenario->execute();

                echo "Escenario eliminado con éxito";
            } catch (PDOException $e) {
                echo "Error: " . $e->getMessage();
            }
        } else {
            echo "ID de escenario no proporcionado";
        }
    } else {
        echo "<script>window.location.href='../public/index.php';</script>";
        exit();
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
