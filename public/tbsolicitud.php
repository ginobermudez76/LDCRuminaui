<?php

include '../includes/config.php'; //incluyendo la conexión de la base de datos
include '../includes/header.php'; //incluyendo la cabecera común

if (!isset($_SESSION['usuario_admin'])) {
    header("Location: /Ayudantias-1/admin/login.php");
    exit();
}

//logica para obtener la lista de deportes de la base de datos
try {
    $stmt = $conn->prepare("SELECT s_id, s_fecha, s_doc, tipo, descripcion, s_valor, solicitante, encargado, solicitantext, estado FROM solicitud");
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
                <th>Documento</th>
                <th>tipo</th>
                <th>Descripción</th>
                <th>Valor solicitado</th>
                <th>solicitante</th>
                <th>Encargado</th>
                <th>Estado</th>
            </tr>
        </thead>

    </table>
</div>


        </tbody>
    </table>
</div>

<?php include '../includes/footer.php'; ?>
