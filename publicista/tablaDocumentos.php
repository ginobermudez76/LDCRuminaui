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
    if ($usuario['rol'] == 7) {
        // Mostrar el elemento del menú para publicista
        //logica para obtener la lista de documentos de la base de datos
        try {
            $stmt = $conn->prepare("SELECT * FROM documentos");
            $stmt->execute();

            $documentos = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
                            <th>Documento</th>
                            <th>Nombre</th>
                            <th>Descripción</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($documentos as $documento) : ?>
                            <tr>
                                <td><?php echo htmlspecialchars($documento['id']); ?></td>
                                <td>
                                    <?php if (isset($documento['documento']) && $documento['documento']) : ?>
                                        <a href="<?php echo htmlspecialchars($documento['documento']); ?>" target="_blank">Ver documento</a>
                                    <?php else : ?>
                                        <p>No hay documento</p>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo empty($documento['nombre']) ? 'No se proporcionó nombre' : htmlspecialchars($documento['nombre']); ?></td>
                                <td>
                                    <?php if (empty($documento['descripcion'])) : ?>
                                        <p>No hay descripción</p>
                                    <?php else : ?>
                                        <?php echo htmlspecialchars($documento['descripcion']); ?>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <button type="button" class="btn btn-secondary btn-sm" onclick="loadForm(<?php echo $documento['id']; ?>)">Editar</button>
                                    <button class="btn btn-danger btn-sm" onclick="confirmarEliminacion(<?php echo $documento['id']; ?>)">Eliminar</button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

            </div>
        </div>
        <div id="modalEditDocumentos" class="modal edit">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editarDocumentoModalLabel">Editar documento</h5>
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