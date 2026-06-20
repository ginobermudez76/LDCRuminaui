<?php
session_start();
include '../includes/config.php'; //incluyendo la conexión de la base de datos

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

    // Verificar si el usuario tiene el rol de Publicista
    if ($usuario['rol'] == 4 || $usuario['rol'] == 3 || $usuario['rol'] == 2 || $usuario['rol'] == 1 || $usuario['rol'] == 9) {
        //Logica para el form de reasignar
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $tipo = $_POST['tipoReasignar'];
            $solicitudId = $_POST['solicitudId'];

            try {
                // Iniciar transacción
                $conn->beginTransaction();

                $stmt = $conn->prepare("UPDATE solicitud SET tipo = ? WHERE s_id = ?");
                $stmt->execute([$tipo, $solicitudId]);

                // Llamar al procedimiento almacenado
                $stmt = $conn->prepare("CALL actualizarDepartamentoEnUpdate(?, ?)");
                $stmt->execute([$tipo, $solicitudId]);

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
        echo "<script>window.location.href='../public/index.php';</script>";
        exit();
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
