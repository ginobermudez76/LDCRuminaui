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
            $stmt = $conn->prepare("SELECT * FROM carta_condolencias");
            $stmt->execute();
            $cartas = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
                            <th>Mensaje</th>
                            <th>Visible hasta:</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($cartas as $carta) : ?>
                            <tr>
                                <td><?php echo htmlspecialchars($carta['id']); ?></td>
                                <td>
                                    <?php if (isset($carta['imagen']) && $carta['imagen']) : ?>
                                        <img src="<?php echo htmlspecialchars($carta['imagen']); ?>" alt="<?php echo htmlspecialchars($carta['mensaje']); ?>" style="width: 100px; height: auto;">
                                    <?php else : ?>
                                        <p>Sin Imagen</p>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo htmlspecialchars($carta['mensaje']); ?></td>
                                <td><?php echo htmlspecialchars($carta['fecha_eliminar']); ?></td>
                                <td>
                                    <button type="button" class="btn btn-secondary btn-sm" onclick="loadForm(<?php echo $carta['id']; ?>)">Editar</button>
                                    <button class="btn btn-danger btn-sm" onclick="confirmarEliminacion(<?php echo $carta['id']; ?>)">Eliminar</button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div id="modalEditCartas" class="modal edit">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Editar Carta de condolencias</h5>
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