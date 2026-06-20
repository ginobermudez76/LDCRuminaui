<?php
session_start();
include '../includes/config.php'; // Incluyendo la conexión de la base de datos

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

    // Verificar si el usuario tiene el rol de Administrador
    if ($usuario['rol'] == 8) {
        // Mostrar el elemento del menú Administrar
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            // Obtener los datos del formulario
            $nombre = $_POST['nombre'];
            $snombre = $_POST['snombre'];
            $apellido = $_POST['apellido'];
            $sapellido = $_POST['sapellido'];
            $cedula = $_POST['cedula'];
            $celular = $_POST['celular'];
            $correo = $_POST['mail'];
            $user = $_POST['username'];
            $pass = $_POST['contrasena'];
            $rol = $_POST['rolid'];
            $fechanac = $_POST['fecha_n'];
            if (trim($nombre) == '' || trim($apellido) == '' || trim($pass) == '' || trim($cedula) == '' || trim($user) == '') {
                $error = "No puede insertar espacios vacios";
            } else {
                try {
                    $hash = password_hash($pass, PASSWORD_DEFAULT);
                    $stmt = $conn->prepare("INSERT INTO usuarios (primer_nombre, segundo_nombre, primer_apellido, segundo_apellido, cedula, celular, correo, nombre_usuario, contrasena, rol, fecha_nac ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                    $stmt->execute([$nombre, $snombre, $apellido, $sapellido, $cedula, $celular, $correo, $user, $hash, $rol, $fechanac]);

                    // Redirigir después de agregar
                    header("Location:  register.php");
                    exit();
                } catch (PDOException $e) {
                    $mensaje = "Error: " . $e->getMessage();
                }
            }
        }
    } else {
        echo "<script>window.location.href='../public/index.php';</script>";
        exit();
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
} ?>