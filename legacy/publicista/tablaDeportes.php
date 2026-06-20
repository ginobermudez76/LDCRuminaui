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
        //logica para obtener la lista de deportes de la base de datos
        try {
            $stmt = $conn->prepare("SELECT * FROM deportes");
            $stmt->execute();

            $deportes = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
                            <th>Descripción</th>
                            <th>Galería de imágenes</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($deportes as $deporte) : ?>
                            <tr>
                                <td><?php echo htmlspecialchars($deporte['id']); ?></td>
                                <td>
                                    <?php if (isset($deporte['imagen']) && $deporte['imagen']) : ?>
                                        <img src="<?php echo htmlspecialchars($deporte['imagen']); ?>" alt="<?php echo htmlspecialchars($deporte['nombre']); ?>" style="width: 100px; height: auto;">
                                    <?php else : ?>
                                        <p>Sin Imagen</p>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo empty($deporte['nombre']) ? 'No se proporcionó nombre' : htmlspecialchars($deporte['nombre']); ?></td>
                                <td>
                                    <?php if (empty($deporte['descripcion'])) : ?>
                                        <p>Sin descripción</p>
                                    <?php else : ?>
                                        <?php echo htmlspecialchars($deporte['descripcion']); ?>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <a href="galeria_de_imagenes.php?id=<?php echo $deporte['id']; ?>&nombre=<?php echo urlencode($deporte['nombre']); ?>&tipo=Deporte" class="btn btn-secondary btn-sm">Agregar</a>
                                    <a href="eliminar_selecciones.php?id=<?php echo $deporte['id']; ?>&nombre=<?php echo urlencode($deporte['nombre']); ?>&tipo=Deporte" class="btn btn-danger btn-sm">Borrar</a>
                                </td>
                                <td>
                                    <button type="button" class="btn btn-secondary btn-sm" onclick="loadForm(<?php echo $deporte['id']; ?>)">Editar</button>
                                    <button class="btn btn-danger btn-sm" onclick="confirmarEliminacion(<?php echo $deporte['id']; ?>)">Eliminar</button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

            </div>
        </div>

        <div id="modalEditDeportes" class="modal edit">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editarDeporteModalLabel">Editar deporte</h5>
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