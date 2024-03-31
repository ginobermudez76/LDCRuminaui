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

            $titulo = $_POST['titulo'];
            $cuerpo = $_POST['cuerpo'];

            $response = array();

            if (trim($titulo) == '') {
                $response['success'] = false;
                $response['message'] = "El titulo no puede estar vacío.";
            } elseif (trim($cuerpo) == '') {
                $response['success'] = false;
                $response['message'] = "La imagen es obligatoria.";
            } elseif (!isset($_FILES['imagen']) || $_FILES['imagen']['error'] != 0) {
                $response['success'] = false;
                $response['message'] = "La imagen es obligatoria.";
            } else {

                $directorioDestino = "../uploads/noticias/";
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
                        // la imagen se cargó correctamente
                    } else {
                        $response['success'] = false;
                        $response['message'] = "Hubo un error al cargar la imagen.";
                        $archivoImagen = "";
                    }
                } else {
                    $response['success'] = false;
                    $response['message'] = "El archivo no es una imagen.";
                    $archivoImagen = "";
                }
                // manejo en el caso de que la imagen no se cargue una imagen



                //insertar en la base de datos (con o sin imagen)

                try {
                    $stmt = $conn->prepare("INSERT INTO noticias (titulo, cuerpo, imagen, fecha) VALUES (?, ?, ?, NOW())");
                    $stmt->execute([$titulo, $cuerpo, $archivoImagen]);

                    $response['success'] = true;
                    $response['message'] = "La noticia fue publicada";
                } catch (PDOException $e) {
                    $response['success'] = false;
                    $response['message'] = "Hubo un error al publicar la noticia: " . $e->getMessage();
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
