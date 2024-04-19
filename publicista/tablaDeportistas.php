<?php
session_start();
include '../includes/config.php'; // incluyendo la conexión de la base de datos
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
        try {
            $stmt = $conn->prepare("CALL info_deportistas();");
            $stmt->execute();
            $deportistas = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
?>
        <div class="container mt-5">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Id</th>
                            <th>Imagen</th>
                            <th>Nombre</th>
                            <th>Deporte</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($deportistas as $deportista) : ?>
                            <tr>
                                <td><?php echo htmlspecialchars($deportista['id']); ?></td>
                                <td>
                                    <?php if (isset($deportista['imagen']) && $deportista['imagen']) : ?>
                                        <img src="<?php echo htmlspecialchars($deportista['imagen']); ?>" alt="<?php echo htmlspecialchars($deportista['nombre']); ?>" style="width: 100px; height: auto;">
                                    <?php else : ?>
                                        <p>Sin Imagen</p>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo empty($deportista['nombre']) ? 'No se proporcionó nombre' : htmlspecialchars($deportista['nombre']); ?></td>
                                <td><?php echo empty($deportista['deporte']) ? 'No se proporcionó deporte' : htmlspecialchars($deportista['deporte']); ?></td>
                                <td>
                                    <button type="button" class="btn btn-secondary btn-sm" onclick="loadForm(<?php echo $deportista['id']; ?>)">Editar</button>
                                    <button class="btn btn-danger btn-sm" onclick="confirmarEliminacion(<?php echo $deportista['id']; ?>)">Eliminar</button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

            </div>
        </div>
        <div id="modalEditDeportitas" class="modal edit">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editarSolicitudModalLabel">Editar informacion del deportista</h5>
                </div>
                <div id="formContent"></div>
            </div>
        </div>

<?php
    } else {
        echo "<script>window.location.href='../public/index.php';</script>";
        exit();
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>