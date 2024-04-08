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
        // Mostrar el elemento del menú Administrar
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $nombre = $_POST['nombre'];
            $descripcion = $_POST['descripcion'];
            $fecha_ini = $_POST['fecha_ini'];
            $fecha_f = $_POST['fecha_f'];
            $deporte_id = $_POST['deporte_id'];
            // Validar las fechas
            $fecha_ini_obj = DateTime::createFromFormat('Y-m-d', $fecha_ini);
            $fecha_f_obj = DateTime::createFromFormat('Y-m-d', $fecha_f);
            $response = array();
            if (trim($nombre) == '') {
                $response['success'] = false;
                $response['message'] = "El nombre no puede estar vacío.";
            } elseif (empty($deporte_id)) {
                $response['success'] = false;
                $response['message'] = "El deporte es obligatorio.";
            } elseif (!$fecha_ini_obj) {
                $response['success'] = false;
                $response['message'] = "La fecha de inicio tienen un formato inválido.";
            } elseif (!$fecha_f_obj) {
                $response['success'] = false;
                $response['message'] = "La fecha de finalización tienen un formato inválido.";
            }elseif ($fecha_f_obj < $fecha_ini_obj) {
                $response['success'] = false;
                $response['message'] = "La fecha de fin no puede ser anterior a la fecha de inicio.";
            } else {

                if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] == 0) {
                    $directorioDestino = "../uploads/eventos/";

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
                    $response['message'] = "El archivo no es una imagen.";
                    $archivoImagen = "";
                }
            } else {
                // Manejo en el caso de que la imagen no se cargue
                $archivoImagen = "";
            }

            try {
                $stmt = $conn->prepare("INSERT INTO eventos (nombre, descripcion, fecha_inicio, fecha_fin, deporte_id, imagen) VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->execute([$nombre, $descripcion, $fecha_ini, $fecha_f, $deporte_id, $archivoImagen]);

                // Redirigir después de agregar
                $response['success'] = true;
                $response['message'] = "El curso se inserto correctamente";
            } catch (PDOException $e) {
                $response['success'] = false;
                $response['message'] = "Ocurrio un error: " . $e->getMessage();
            }
        }
        echo json_encode($response);
        exit();
    }

    } else {
        echo "<script>window.location.href='../public/index.php';</script>";
        exit();
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>