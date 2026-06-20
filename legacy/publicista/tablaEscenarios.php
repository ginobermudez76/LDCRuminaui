<?php
session_start();
include '../includes/config.php'; //incluyendo la conexión a la base de datos

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
        // Mostrar el elemento del menú para publicista
        //logica para obtener la lista de escenarios de la base de datos
        try {
            $stmt = $conn->prepare("SELECT * FROM escenarios");
            $stmt->execute();

            $escenarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
?>
        <div class="container mt-4">
            <div class="table-responsive">
            <table class="table">
    <thead>
        <tr>
            <th>Id</th>
            <th>Imagen</th>
            <th>Nombre</th>
            <th>Ubicación</th>
            <th>Dirección</th>
            <th>Teléfono</th>
            <th>Supervisor</th>
            <th>Celular</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($escenarios as $escenario) : ?>
            <tr>
                <td><?php echo htmlspecialchars($escenario['id']); ?></td>
                <td>
                    <?php if (isset($escenario['imagen']) && $escenario['imagen']) : ?>
                        <img src="<?php echo htmlspecialchars($escenario['imagen']); ?>" alt="<?php echo htmlspecialchars($escenario['nombre']); ?>" style="width: 100px; height: auto;">
                    <?php else : ?>
                        <p>Sin Imagen</p>
                    <?php endif; ?>
                </td>
                <td><?php echo empty($escenario['nombre']) ? 'No se proporcionó nombre' : htmlspecialchars($escenario['nombre']); ?></td>
                <td><?php echo empty($escenario['ubicacion']) ? 'No se proporcionó ubicación' : htmlspecialchars($escenario['ubicacion']); ?></td>
                <td><?php echo empty($escenario['direccion']) ? 'No se proporcionó dirección' : htmlspecialchars($escenario['direccion']); ?></td>
                <td><?php echo empty($escenario['telefono']) ? 'No se proporcionó teléfono' : htmlspecialchars($escenario['telefono']); ?></td>
                <td><?php echo empty($escenario['supervisor']) ? 'No se proporcionó supervisor' : htmlspecialchars($escenario['supervisor']); ?></td>
                <td><?php echo empty($escenario['celular']) ? 'No se proporcionó celular' : htmlspecialchars($escenario['celular']); ?></td>
                <td>
                    <button type="button" class="btn btn-secondary btn-sm" onclick="loadForm(<?php echo $escenario['id']; ?>)">Editar</button>
                    <button class="btn btn-danger btn-sm" onclick="confirmarEliminacion(<?php echo $escenario['id']; ?>)">Eliminar</button>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

            </div>
        </div>

        <div id="modalEditEscenarios" class="modal edit">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editarEscenarioModalLabel">Editar escenario</h5>
                </div>
                <div id="formContent"></div>
<?php
    } else {
        echo "<script>window.location.href='../public/index.php';</script>";
        exit();
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>