<?php
session_start();
include '../includes/config.php'; //incluyendo la conexión de la base de datos

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

    // Verificar si el usuario tiene el rol de Publicista
    if ($usuario['rol'] == 4 || $usuario['rol'] == 3 || $usuario['rol'] == 2 || $usuario['rol'] == 1 || $usuario['rol'] == 9) {
        //Logica para el form de reasignar
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $encargado = $_POST['usuarioReasignarPorUsuario'];
            $solicitudId = $_POST['solicitudIdUsuario'];
            $estado = 0;

            $stmt = $conn->prepare("SELECT rol FROM usuarios WHERE id = :usuarioReasignarPorUsuario");
            $stmt->bindParam(':usuarioReasignarPorUsuario', $encargado, PDO::PARAM_INT);
            $stmt->execute();

            $usuarioRol = $stmt->fetch(PDO::FETCH_ASSOC);
            $dptEncargado = $usuarioRol['rol'];

            if ($usuario['rol'] == 9) {
                $estado = 2;
            } else {
                $estado = 3;
            }
            try {
                // Iniciar transacción
                $conn->beginTransaction();
                $stmt = $conn->prepare("UPDATE solicitud SET encargado = ?, departamento_encargado = ?, tipo = 4, estado = ? WHERE s_id = ?");
                $stmt->execute([$encargado, $dptEncargado, $estado, $solicitudId]);


                $stmt = $conn->prepare("INSERT INTO historial_solicitud (solicitud_id, fecha_asignacion, estado, responsable, departamento, tipo)
                VALUES (?, CURRENT_TIMESTAMP(), ?, ?, ?, ?)");
                $stmt->execute([$solicitudId, $estado, $encargado, $dptEncargado, '4']);
                // Confirmar transacción
                $conn->commit();
                // Redirigir después de actualizar
                header("Location: vsolicitudencargado.php");
                exit();
            } catch (PDOException $e) {
                // Revertir transacción en caso de error
                $conn->rollBack();
                echo "Error: " . $e->getMessage();
            }
        }
    } else {
        header("Location: ../public/index.php");
        exit();
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
