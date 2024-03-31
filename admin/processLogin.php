<?php
session_start(); // Iniciar sesión si no está iniciada

include '../includes/config.php';

$error_login = '';

if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_POST["nombre_usuario"]) && !empty($_POST["contrasena"])) {
    $nombre_usuario = $_POST["nombre_usuario"];
    $contrasena = $_POST["contrasena"];

    try {
        $sql = "SELECT id, nombre_usuario, contrasena FROM usuarios WHERE nombre_usuario = :nombre_usuario";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':nombre_usuario', $nombre_usuario, PDO::PARAM_STR);
        $stmt->execute();
        
        if ($stmt->rowCount() == 1) {
            $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
            if (password_verify($contrasena, $usuario['contrasena'])) {
                $_SESSION['usuario_id'] = $usuario['id'];
                $_SESSION['usuario_admin'] = $usuario['nombre_usuario'];

                header("Location: ../public/index.php");
                exit();
            } else {
                $error_login = "Nombre de Usuario o contraseña incorrectos";
            }
        } else {
            $error_login = "El usuario no existe";
        }
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
} else {
    $error_login = "Por favor, complete todos los campos";
}

$_SESSION['error_login'] = $error_login;
echo "<script>window.location.href='login.php';</script>";
exit();
?>
