<?php
session_start();
include '../includes/config.php';

// Verificar si el usuario está autenticado como administrador
if (!isset($_SESSION['usuario_admin'])) {
    header("Location: ../admin/login.php");
    exit();
}
$usuarioId = $_SESSION['usuario_id'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        // Obtener los datos del formulario de edición
        $pass = $_POST['pass'];
        $newpass = $_POST['newpass'];
        $response = array();

        // Obtener la contraseña almacenada en la base de datos
        $stmt = $conn->prepare("SELECT contrasena FROM usuarios WHERE id = :usuario_id");
        $stmt->bindParam(':usuario_id', $usuarioId, PDO::PARAM_INT);
        $stmt->execute();
        $contrasena = $stmt->fetch(PDO::FETCH_ASSOC);

        // Verificar si la contraseña actual coincide con la almacenada en la base de datos
        if (password_verify($pass, $contrasena['contrasena'])) {
            // Procesar el cambio de contraseña
            $hashnewpass = password_hash($newpass, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("UPDATE usuarios SET contrasena = ? WHERE id = ?");
            $stmt->execute([$hashnewpass, $usuarioId]);
            $response['success'] = true;
            $response['message'] = "Contraseña cambiada con éxito.";
            $response['redirect'] = true;
        } else {
            $response['success'] = false;
            $response['message'] = "Contraseña anterior incorrecta";
        }
    } catch (Exception $e) {
        $response['success'] = false;
        $response['message'] = "Error: " . $e->getMessage();
    }
    echo json_encode($response);
    exit();
}
?>