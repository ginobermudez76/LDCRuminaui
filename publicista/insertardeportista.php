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

            $nombre = $_POST['nombre'];
            $depor = $_POST['deporte_id'];
            $response = array();

            if (trim($nombre) == '') {
                $response['success'] = false;
                $response['message'] = "El nombre no puede estar vacío.";
            } elseif (!isset($_FILES['imagen']) || $_FILES['imagen']['error'] != 0) {
                $response['success'] = false;
                $response['message'] = "La imagen es obligatoria.";
                
            } elseif (empty($depor)) {
                $response['success'] = false;
                $response['message'] = "El deporte es obligatorio.";
            } else {

                $directorioDestino = "../uploads/deportes/deportistas/";
                $archivoImagen = $directorioDestino . basename($_FILES['imagen']['name']);
                $tipoArchivo = strtolower(pathinfo($archivoImagen, PATHINFO_EXTENSION));
                $check = getimagesize($_FILES["imagen"]["tmp_name"]);

                if ($check != false) {
                    // Es una imagen, continuar con el proceso

                    // Verificar si el archivo ya existe y renombrarlo si es necesario
                    $contador = 1;
                    $nombreArchivo = pathinfo($_FILES['imagen']['name'], PATHINFO_FILENAME);
                    $extensionArchivo = pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION);
                    $archivo = $directorioDestino . $nombreArchivo . '.' . $extensionArchivo;

                    while (file_exists($archivo)) {
                        $nombreArchivo = pathinfo($_FILES['imagen']['name'], PATHINFO_FILENAME) . '_' . $contador;
                        $archivo = $directorioDestino . $nombreArchivo . '.' . $extensionArchivo;
                        $contador++;
                    }

                    $archivoImagen = $archivo;

                    if (move_uploaded_file($_FILES["imagen"]["tmp_name"], $archivoImagen)) {
                        // la imagen se cargó correctamente
                    } else {
                        $response['success'] = false;
                        $response['message'] = "Hubo un error al cargar la imagen.";
                        $archivoImagen="";
                    }
                } else {
                    $response['success'] = false;
                    $response['message'] = "El archivo no es una imagen.";
                    $archivoImagen="";                
                }

                //insertar en la base de datos (con o sin imagen)

                try {
                    $stmt = $conn->prepare("INSERT INTO deportistas_destacados (nombre_deportista, deporte_id, imagen) VALUES (?, ?, ?)");
                    $stmt->execute([$nombre, $depor, $archivoImagen]);

                    //redirigir despues de agregar
                    $response['success'] = true;
                    $response['message'] = "Deportista insertado correctamente.";
                } catch (PDOException $e) {
                    $response['success'] = false;
                    $response['message'] = "Hubo un error al insertar el deportista: " . $e->getMessage();
                }
            }
            // Enviar la respuesta en formato JSON
            echo json_encode($response);
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
?>
