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

    // Verificar si el usuario tiene el rol de necesario 
    if ($usuario['rol'] == 4 || $usuario['rol'] == 3 || $usuario['rol'] == 2 || $usuario['rol'] == 1 || $usuario['rol'] == 9) {
        // Logica para el form de aprobar o denegar
        if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['solicitud_id'])) {
            $solicitud_id = $_POST['solicitud_id'];
            $accion = $_POST['accion'];
            $tipo = $_POST['tipo_solicitud'];

            $stmt = $conn->prepare("SELECT id_tipo FROM solicitud_tipo WHERE name_tipo = :tipo_solicitud");
            $stmt->bindParam(':tipo_solicitud', $tipo, PDO::PARAM_INT);
            $stmt->execute();
            $tipoId = $stmt->fetch(PDO::FETCH_ASSOC);
            // Determinar el nuevo estado de la solicitud
            $nuevo_estado = null;
            if ($accion != 'Denegar') {
                switch ($usuario['rol']) {
                    case 9:
                        $nuevo_estado = ($accion == 'Aprobar') ? 2 : null;
                        break;
                    case 2:
                    case 4:
                        $nuevo_estado = ($accion == 'Aprobar') ? 3 : null;
                        break;
                    case 1:
                    case 3:
                        $nuevo_estado = ($accion == 'Aprobar') ? 5 : null;
                        break;
                    default:
                        // No hacer nada para otros roles
                        break;
                }
            } else {
                $nuevo_estado =  4;
            }

            // Actualizar el estado de la solicitud si se determinó un nuevo estado
            if ($nuevo_estado !== null) {
                try {
                    $stmt = $conn->prepare("UPDATE solicitud SET estado = :estado WHERE s_id = :solicitud_id");
                    $stmt->bindParam(':estado', $nuevo_estado, PDO::PARAM_INT);
                    $stmt->bindParam(':solicitud_id', $solicitud_id, PDO::PARAM_INT);
                    $stmt->execute();
                    // Llamar al procedimiento almacenado para actualizar el departamento y otros campos relacionados
                    $stmt = $conn->prepare("CALL procesarAccionSP(?, ?, ?)");
                    $stmt->execute([$nuevo_estado, $tipoId['id_tipo'], $solicitud_id]);
                    header("Location: ../admin/vsolicitudencargado.php");
                    exit();
                } catch (PDOException $e) {
                    echo "Error: " . $e->getMessage();
                }
            }
        }
    } else {
        header("Location: ../public/index.php");
        exit();
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
