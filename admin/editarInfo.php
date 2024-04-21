<?php
session_start();
include '../includes/config.php';

if (!isset($_SESSION['usuario_admin'])) {
    echo "<script>window.location.href='../admin/login.php';</script>";
    exit();
}
$usuario_id = $_SESSION['usuario_id'];
// Procesar la nueva imagen si se proporciona
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = $_POST['nombre'];
    $snombre = $_POST['snombre'];
    $apellido = $_POST['apellido'];
    $sapellido = $_POST['sapellido'];
    $cedula = $_POST['cedula'];
    $celular = $_POST['celular'];
    $email = $_POST['mail'];
    $cumple = $_POST['fecha_n'];

    // Actualizar la base de datos con la nueva imagen o la imagen anterior
    $stmt = $conn->prepare("UPDATE usuarios SET primer_nombre=?, segundo_nombre=?, primer_apellido=?, segundo_apellido=?, cedula=?, celular=?, correo=?, fecha_nac=? WHERE id=$usuario_id");
    $stmt->execute([$nombre, $snombre, $apellido, $sapellido, $cedula, $celular, $email, $cumple]);

    echo "<script>window.location.href='../admin/cuenta.php';</script>";
    exit();
}
?>