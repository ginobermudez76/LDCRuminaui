<?php
session_start();
include '../includes/config.php';

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
    if ($usuario['rol'] == 7) {
        $inscripcion = $_POST['tipo'];
        $eventoId = $_POST['id'];
        if ($inscripcion == 'Abiertas') {
            $stmt = $conn->prepare("UPDATE eventos SET inscripciones = 'Abiertas' WHERE id = :id");
            $stmt->bindParam(':id', $eventoId, PDO::PARAM_INT);
            $stmt->execute();
            echo "Las inscripciones ahora están abiertas";
        } elseif($inscripcion == 'Cerradas'){
            $stmt = $conn->prepare("UPDATE eventos SET inscripciones = 'Cerradas' WHERE id = :id");
            $stmt->bindParam(':id', $eventoId, PDO::PARAM_INT);
            $stmt->execute();
            echo "Las inscripciones ahora están cerradas";
        }
    } else {
        echo "<script>window.location.href='../public/index.php';</script>";
        exit();
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>