<?php
session_start();
include '../includes/config.php';

if (!isset($_SESSION['usuario_admin'])) {
    echo "<script>window.location.href='../admin/login.php';</script>";
    exit();
}
$usuario_id = $_SESSION['usuario_id'];
// Llamar al procedimiento almacenado para obtener del usuario
try {
    $stmt = $conn->prepare("CALL info_usuario(:usuario_id)");
    $stmt->bindParam(':usuario_id', $usuario_id, PDO::PARAM_INT);
    $stmt->execute();
    // Recuperar un solo registro
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>

<div class='info'>
    <h2>Información personal</h2>
    <label>Nombre: <?php echo htmlspecialchars($usuario['persona']); ?></label><br>
    <label>Rol: <?php echo htmlspecialchars($usuario['rol']); ?></label><br>
    <label>No. cédula: <?php echo htmlspecialchars($usuario['cedula']); ?></label><br>
    <label>Celular: <?php echo htmlspecialchars($usuario['celular']); ?></label><br>
    <label>E-mail: <?php echo htmlspecialchars($usuario['correo']); ?></label><br>
    <label>Nombre de usuario: <?php echo htmlspecialchars($usuario['usuario']); ?></label><br>
    <label>Cumpleaños: <?php echo htmlspecialchars($usuario['cumpleanos']); ?></label><br>
</div>
