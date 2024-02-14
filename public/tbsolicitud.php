<?php
session_start();
if (!isset($_SESSION['usuario_admin'])) {
    header("Location: /Ayudantias-1/admin/login.php");
    exit();
}

include '../includes/config.php'; //incluyendo la conexión de la base de datos
include '../includes/header.php'; //incluyendo la cabecera común

//logica para obtener la lista de deportes de la base de datos
try {
    $stmt = $conn->prepare("SELECT id, nombre, descripcion, imagen FROM deportes");
    $stmt->execute();

    $deporte = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>

<div class="container mt-4">
    <h2 class="gestionar">Solicitudes</h2>
    <a href="agregar_deporte.php" class="btn btn-primary mb-4">Adicionar nueva</a>
    
    <table class="table">
        <thead>
            <tr>
                <th>Id</th>
                <th>Fecha</th>
                <th>Nombre</th>
                <th>tipo</th>
                <th>Descripción</th>
                <th>Valor solicitado</th>
                <th>Archivo</th>
                <th>Estado</th>
            </tr>
        </thead>

    </table>
</div>


        </tbody>
    </table>
</div>

<?php include '../includes/footer.php'; ?>
