<?php
session_start();
include '../includes/config.php'; // Incluyendo la conexión a la base de datos

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
            // Obtener los datos del formulario
            $mensaje = $_POST['mensaje'];

            $response = array();

            if (trim($mensaje) == '') {
                $response['success'] = false;
                $response['message'] = "El mensaje no puede estar vacio";
            } elseif (!isset($_FILES['imagen']) || $_FILES['imagen']['error'] != 0) {
                $response['success'] = false;
                $response['message'] = "La imagen es obligatoria.";
            } else {

                $directorioDestino = "../uploads/cartaCondolencia/";

                $archivoImagen = $directorioDestino . basename($_FILES['imagen']['name']);

                $tipoArchivo = strtolower(pathinfo($archivoImagen, PATHINFO_EXTENSION));

                $check = getimagesize($_FILES["imagen"]["tmp_name"]);

                if ($check != false) {
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
                        //la imagen se cargo correctamente

                    } else {
                        $response['success'] = false;
                        $response['message'] = "Hubo un error al cargar la imagen.";
                        $archivoImagen = "";
                    }
                } else {
                    $response['success'] = false;
                    $response['message'] = "Hubo un error al cargar la imagen.";
                    $archivoImagen = "";
                }

                try {
                    $fecha_eliminar = date('Y-m-d H:i:s', strtotime('+1 week'));
                    $stmt = $conn->prepare("INSERT INTO carta_condolencias(imagen, mensaje, fecha_eliminar) VALUES (?, ?, ?)");
                    $stmt->execute([$archivoImagen, $mensaje, $fecha_eliminar]);

                    // Redirigir después de agregar
                    $response['success'] = true;
                    $response['message'] = "Su mensaje se publicó correctamente";
                } catch (PDOException $e) {
                    $response['success'] = false;
                    $response['message'] = "Hubo un error al mostrar la carta de condolencias " . $e->getMessage();
                }
            }
            // Enviar la respuesta en formato JSON
            echo json_encode($response);
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
?>