<?php
include '../includes/config.php'; // Incluyendo la conexión de la base de datos

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
?>
