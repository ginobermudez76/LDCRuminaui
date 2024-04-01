<?php
session_start();
include '../includes/config.php'; //incluyebdo la conexion de la base de datos


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
        // Mostrar el elemento del menú Administrarsdasdasdasdasdas

        if ($_SERVER["REQUEST_METHOD"] == "POST") {

            $nombre = $_POST['nombre'];
            $descripcion = $_POST['descripcion'];
            if (trim($nombre) == '') {
                $error = "No puede insertar espacios vacios";
            } else {

            if (isset($_FILES['documento']) && $_FILES['documento']['error'] == 0) {

                $directorioDestino = "../uploads/documentos/";
                $archivoDocumento = $directorioDestino . basename($_FILES['documento']['name']);

                // Verificar si el archivo ya existe y renombrarlo si es necesario
                $contador = 1;
                $nombreArchivo = pathinfo($_FILES['documento']['name'], PATHINFO_FILENAME);
                $extensionArchivo = pathinfo($_FILES['documento']['name'], PATHINFO_EXTENSION);
                $archivo = $directorioDestino . $nombreArchivo . '.' . $extensionArchivo;

                while (file_exists($archivo)) {
                    $nombreArchivo = pathinfo($_FILES['documento']['name'], PATHINFO_FILENAME) . '_' . $contador;
                    $archivo = $directorioDestino . $nombreArchivo . '.' . $extensionArchivo;
                    $contador++;
                }

                $archivoDocumento = $archivo;

                if (move_uploaded_file($_FILES["documento"]["tmp_name"], $archivoDocumento)) {
                    // el documento se cargó correctamente
                } else {
                    $error = "Hubo un error al cargar el documento";
                }
            } else {
                // manejo en el caso de que el documento no se cargue
                $archivoDocumento = "";
            }

            //insertar en la base de datos (con o sin documento)

            try {
                $stmt = $conn->prepare("INSERT INTO documentos (nombre, descripcion, documento) VALUES (?, ?, ?)");
                $stmt->execute([$nombre, $descripcion, $archivoDocumento]);

                //redirigir despues de agregar

                header("Location: documentos.php");
                exit();
            } catch (PDOException $e) {
                echo "Error: " . $e->getMessage();
            }
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
