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
        $hashnewpass = password_hash($newpass, PASSWORD_DEFAULT);
        $hashpass = password_hash($pass, PASSWORD_DEFAULT);
        $response = array();
        // Obtener la contraseña almacenada en la base de datos
        $stmt = $conn->prepare("SELECT contrasena FROM usuarios WHERE id = :usuario_id");
        $stmt->bindParam(':usuario_id', $usuarioId, PDO::PARAM_INT);
        $stmt->execute();
        $contrasena = $stmt->fetch(PDO::FETCH_ASSOC);

        // Verificar si la contraseña actual coincide con la almacenada en la base de datos
        if ($contrasena['contrasena'] != $hashpass) {
            $response['success'] = false;
            $response['message'] = "Contraseña anterior incorrecta";
        } else {
            // Procesar el cambio de contraseña
            $stmt = $conn->prepare("UPDATE usuarios SET contrasena = ? WHERE id = ?");
            $stmt->execute([$hashnewpass, $usuarioId]);
            $response['success'] = true;
            $response['message'] = "Contraseña cambiada con exito.";

        }
    } catch (Exception $e) {
        $response['success'] = false;
        $response['message'] = "Error: " . $e->getMessage();
        echo json_encode($response);
    }
    echo json_encode($response);
    exit();
}
?>