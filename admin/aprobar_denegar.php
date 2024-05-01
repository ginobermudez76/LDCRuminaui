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

    // Verificar si el usuario tiene el rol de necesario 
    if ($usuario['rol'] == 4 || $usuario['rol'] == 3 || $usuario['rol'] == 2 || $usuario['rol'] == 1 || $usuario['rol'] == 9) {
        // Logica para el form de aprobar o denegar
        if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['solicitud_id'])) {
            $solicitud_id = $_POST['solicitud_id'];
            $accion = $_POST['accion'];
            $tipo = $_POST['tipo_solicitud'];

            $stmt = $conn->prepare("SELECT id_tipo FROM solicitud_tipo WHERE name_tipo = :tipo_solicitud");
            $stmt->bindParam(':tipo_solicitud', $tipo, PDO::PARAM_STR);
            $stmt->execute();
            $tipoId = $stmt->fetch(PDO::FETCH_ASSOC);
            // Determinar el nuevo estado de la solicitud
            $nuevo_estado = null;
            if ($accion != 'Denegar') {
                if ($tipo != 'Otro tipo') {
                    if ($usuario['rol'] != 1) {
                        switch ($usuario['rol']) {
                                //sectretaría
                            case 9:
                                $nuevo_estado = ($accion == 'Aprobar') ? 2 : null;
                                break;
                                //metodologo y coordinador general    
                            case 2:
                            case 4:
                                $nuevo_estado = ($accion == 'Aprobar') ? 3 : null;
                                break;
                                //tesoreria
                            case 3:
                                $nuevo_estado = ($accion == 'Aprobar') ? 5 : null;
                                break;
                            default:
                                // No hacer nada para otros roles
                                break;
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
                                echo "<script>window.location.href='../admin/vsolicitudencargado.php';</script>";
                                exit();
                            } catch (PDOException $e) {
                                echo "Error: " . $e->getMessage();
                            }
                        }
                    } else {
                        $nuevo_estado =  5;
                        try {
                            $stmt = $conn->prepare("UPDATE solicitud SET estado = :estado, encargado = :usuario_id, departamento_encargado = :rol WHERE s_id = :solicitud_id");
                            $stmt->bindParam(':estado', $nuevo_estado, PDO::PARAM_INT);
                            $stmt->bindParam(':usuario_id', $usuario_id, PDO::PARAM_INT);
                            $stmt->bindParam(':rol', $usuario['rol'], PDO::PARAM_INT);
                            $stmt->bindParam(':solicitud_id', $solicitud_id, PDO::PARAM_INT);
                            $stmt->execute();
                            //ejecutar otra consulta
                            $stmt = $conn->prepare("INSERT INTO historial_solicitud (solicitud_id, fecha_asignacion, estado, responsable, departamento, tipo)
                        VALUES (?, CURRENT_TIMESTAMP(), ?, ?, ?, ?)");
                            $stmt->execute([$solicitud_id, $nuevo_estado, $usuario_id, $usuario['rol'], $tipoId['id_tipo'],]);
                            echo "<script>window.location.href='../admin/vsolicitudencargado.php';</script>";
                            exit();
                        } catch (PDOException $e) {
                            echo "Error: " . $e->getMessage();
                        }
                    }
                }else{
                    $nuevo_estado =  5;
                    try {
                        $stmt = $conn->prepare("UPDATE solicitud SET estado = :estado, encargado = :usuario_id, departamento_encargado = :rol WHERE s_id = :solicitud_id");
                        $stmt->bindParam(':estado', $nuevo_estado, PDO::PARAM_INT);
                        $stmt->bindParam(':usuario_id', $usuario_id, PDO::PARAM_INT);
                        $stmt->bindParam(':rol', $usuario['rol'], PDO::PARAM_INT);
                        $stmt->bindParam(':solicitud_id', $solicitud_id, PDO::PARAM_INT);
                        $stmt->execute();
                        //ejecutar otra consulta
                        $stmt = $conn->prepare("INSERT INTO historial_solicitud (solicitud_id, fecha_asignacion, estado, responsable, departamento, tipo)
                    VALUES (?, CURRENT_TIMESTAMP(), ?, ?, ?, ?)");
                        $stmt->execute([$solicitud_id, $nuevo_estado, $usuario_id, $usuario['rol'], $tipoId['id_tipo'],]);
                        echo "<script>window.location.href='../admin/vsolicitudencargado.php';</script>";
                        exit();
                    } catch (PDOException $e) {
                        echo "Error: " . $e->getMessage();
                    }
                }
            } else {
                $nuevo_estado =  4;
                try {
                    $stmt = $conn->prepare("UPDATE solicitud SET estado = :estado, encargado = :usuario_id, departamento_encargado = :rol WHERE s_id = :solicitud_id");
                    $stmt->bindParam(':estado', $nuevo_estado, PDO::PARAM_INT);
                    $stmt->bindParam(':usuario_id', $usuario_id, PDO::PARAM_INT);
                    $stmt->bindParam(':rol', $usuario['rol'], PDO::PARAM_INT);
                    $stmt->bindParam(':solicitud_id', $solicitud_id, PDO::PARAM_INT);
                    $stmt->execute();
                    //ejecutar otra consulta
                    $stmt = $conn->prepare("INSERT INTO historial_solicitud (solicitud_id, fecha_asignacion, estado, responsable, departamento, tipo)
                    VALUES (?, CURRENT_TIMESTAMP(), ?, ?, ?, ?)");
                    $stmt->execute([$solicitud_id, $nuevo_estado, $usuario_id, $usuario['rol'], $tipoId['id_tipo'],]);
                    echo "<script>window.location.href='../admin/vsolicitudencargado.php';</script>";
                    exit();
                } catch (PDOException $e) {
                    echo "Error: " . $e->getMessage();
                }
            }
        }
    } else {
        echo "<script>window.location.href='../public/index.php';</script>";
        exit();
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
