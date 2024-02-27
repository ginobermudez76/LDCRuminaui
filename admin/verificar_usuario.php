<?php
session_start();
include '../includes/config.php'; // Incluyendo la conexión de la base de datos
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

    // Verificar si el usuario tiene el rol de Administrador
    if ($usuario['rol'] == 8) {
        // Mostrar el elemento del menú Administrar
// Verificar si se ha enviado el nombre de usuario
if (isset($_POST['username'])) {
    // Obtener el nombre de usuario enviado por la petición AJAX
    $username = $_POST['username'];

    // Consultar si el nombre de usuario ya existe en la base de datos
    $stmt = $conn->prepare("SELECT id FROM usuarios WHERE nombre_usuario = ?");
    $stmt->execute([$username]);
    $result = $stmt->fetch();

    if ($result) {
        // El nombre de usuario ya existe, enviar respuesta "existe" al cliente
        echo "existe";
    } else {
        // El nombre de usuario no existe, enviar respuesta "no existe" al cliente
        echo "no existe";
    }
}
} else {
    header("Location: ../public/index.php");
    exit();
}
} catch (PDOException $e) {
echo "Error: " . $e->getMessage();
}
?>
