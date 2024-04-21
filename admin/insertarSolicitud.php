<?php
session_start();
include '../includes/config.php'; // incluyendo la conexión de la base de datos
if (!isset($_SESSION['usuario_admin'])) {
    echo "<script>window.location.href='../admin/login.php';</script>";
    exit();
}
$usuario_id = $_SESSION['usuario_id'];
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $descripcion = $_POST['descripcion'];
    $valor = isset($_POST['valor']) ? $_POST['valor'] : null;

    // Verificar si el valor es numérico
    if (!is_numeric($valor)) {
        $valor = null;
    }
    $tipo = $_POST['tipo_id'];

    // Obtener el nombre de usuario
    try {
        $stmt = $conn->prepare("SELECT nombre_usuario FROM usuarios WHERE id = :usuario_id");
        $stmt->bindParam(':usuario_id', $usuario_id, PDO::PARAM_INT);
        $stmt->execute();
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
        $nombre_usuario = $usuario['nombre_usuario'];
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }

    // Directorio de destino para el documento
    $directorioDestino = "../uploads/documentos/solicitudes/";
    // Crear el directorio del usuario si no existe
    $directorioUsuario = $directorioDestino . $nombre_usuario . '/';
    if (!file_exists($directorioUsuario)) {
        if (!mkdir($directorioUsuario, 0777, true)) {
            echo "Error al crear el directorio del usuario";
        } else {
            echo "Directorio del usuario creado correctamente: " . $directorioUsuario;
        }
    }

    if (isset($_FILES['documento']) && $_FILES['documento']['error'] == 0) {
        $nombreArchivo = basename($_FILES['documento']['name']);
        $archivo = $directorioUsuario . $nombreArchivo;

        // Verificar si el archivo ya existe y renombrarlo si es necesario
        $contador = 1;
        while (file_exists($archivo)) {
            $nombreArchivo = pathinfo($_FILES['documento']['name'], PATHINFO_FILENAME) . '_' . $contador . '.' . pathinfo($_FILES['documento']['name'], PATHINFO_EXTENSION);
            $archivo = $directorioUsuario . $nombreArchivo;
            $contador++;
        }

        // Verificar la extensión del archivo
        $tipoArchivo = strtolower(pathinfo($nombreArchivo, PATHINFO_EXTENSION));
        $extensionesPermitidas = array('pdf', 'doc', 'docx', 'txt');

        if (!in_array($tipoArchivo, $extensionesPermitidas)) {
            $error = "El archivo no es un documento válido";
        } else {
            // Mover el archivo al directorio de destino
            if (!move_uploaded_file($_FILES["documento"]["tmp_name"], $archivo)) {
                $error = "Hubo un error al cargar el documento";
            } else {
                echo "Archivo subido correctamente: " . $archivo;
            }
        }
    } else {
        // Manejo en el caso de que no se haya seleccionado ningún archivo
        $archivo = "";
    }

    try {
        $conn->beginTransaction(); // Inicia una transacción

        // Insertar la solicitud en la base de datos
        $stmt = $conn->prepare("INSERT INTO solicitud (s_fecha, s_doc, s_valor, tipo, solicitante, descripcion) VALUES (NOW(), ?, ?, ?, ?, ?)");
        $stmt->execute([$archivo, $valor, $tipo, $usuario_id, $descripcion]);

        // Obtener el ID de la solicitud insertada
        $solicitudId = $conn->lastInsertId();

        // Llamar al procedimiento almacenado para actualizar el departamento encargado
        $stmt = $conn->prepare("CALL actualizar_departamento_encargado_proc(?, ?)");
        $stmt->execute([$tipo, $solicitudId]);

        $conn->commit(); // Commit la transacción si todo es correcto

        // Redirigir después de agregar
        echo "<script>window.location.href='../admin/solicitudes.php';</script>";
        exit();
    } catch (PDOException $e) {
        $conn->rollBack(); // Hace rollback en caso de error
        echo "Error: " . $e->getMessage();
    }
}
?>