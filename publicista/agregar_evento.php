<?php

include '../includes/config.php'; //incluyendo la conexión de la base de datos
include '../includes/header.php'; //incluyendo la cabecera común

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
        // Obtener la lista de tipo de deportes
        try {
            $stmt = $conn->prepare("SELECT id, nombre FROM deportes");
            $stmt->execute();
            $deportes = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $nombre = $_POST['nombre'];
            $descripcion = $_POST['descripcion'];
            $fecha_ini = $_POST['fecha_ini'];
            $fecha_f = $_POST['fecha_f'];
            $deporte_id = $_POST['deporte_id'];
            if (trim($nombre) == '') {
                $error = "No puede insertar espacios vacios";
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
                        $error = "Hubo un error al cargar la imagen";
                    }
                } else {
                    $error = "El archivo no es una imagen";
                }
            } else {
                // Manejo en el caso de que la imagen no se cargue
                $archivoImagen = "";
            }

            try {
                $stmt = $conn->prepare("INSERT INTO eventos (nombre, descripcion, fecha_inicio, fecha_fin, deporte_id, imagen) VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->execute([$nombre, $descripcion, $fecha_ini, $fecha_f, $deporte_id, $archivoImagen]);

                // Redirigir después de agregar
                header("Location: gestionar_eventos.php");
                exit();
            } catch (PDOException $e) {
                echo "Error: " . $e->getMessage();
            }
        }
    }
?>

        <div class="container mt-4">
            <h2 class="gestionar">Agregar Evento</h2>
            <?php if (!empty($error)) : ?>
                <div class="alert alert-danger">
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>

        </div>



<?php
    } else {
        echo "<script>window.location.href='../public/index.php';</script>";
        exit();
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
include '../includes/footer.php'; ?>