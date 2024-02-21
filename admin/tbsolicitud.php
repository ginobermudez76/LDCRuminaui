<?php
include '../includes/config.php'; //incluyendo la conexión de la base de datos
include '../includes/header.php'; //incluyendo la cabecera común

if (!isset($_SESSION['usuario_admin'])) {
    header("Location: /Ayudantias-1/admin/login.php");
    exit();
}
$usuario_id = $_SESSION['usuario_id'];
//obtener lista de tipo de solicitud
try {
    $stmt = $conn->prepare("SELECT id_tipo, name_tipo FROM solicitud_tipo");
    $stmt->execute();
    $tipo = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}

//logica para obtener la lista de solicitudes de la base de datos
try {
    $stmt = $conn->prepare("SELECT s_id, s_fecha, s_doc, tipo, descripcion, s_valor, solicitante, encargado, solicitantext, estado FROM solicitud WHERE solicitante = :usuario_id");
    $stmt->bindParam(':usuario_id', $usuario_id, PDO::PARAM_INT);
    $stmt->execute();

    $solicitudes = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $descripcion = $_POST['descripcion'];
    $valor = $_POST['valor'];
    $tipo = $_POST['tipo_id'];


    if (isset($_FILES['documento']) && $_FILES['documento']['error'] == 0) {

        $directorioDestino = "../uploads/documentos/";

        $archivo = $directorioDestino . basename($_FILES['documento']['name']);

        $tipoArchivo = strtolower(pathinfo($archivo, PATHINFO_EXTENSION));
    
        // Lista de extensiones permitidas
        $extensionesPermitidas = array('pdf', 'doc', 'docx', 'txt');
    
        // Verificar si la extensión del archivo está en la lista de extensiones permitidas
        if (in_array($tipoArchivo, $extensionesPermitidas)) {
            if (move_uploaded_file($_FILES["documento"]["tmp_name"], $archivo)) {
                // El archivo se cargó correctamente
            } else {
                $error = "Hubo un error al cargar el documento";
            }
        } else {
            $error = "El archivo no es un documento válido";
        }
    } else {
        // Manejo en el caso de que no se haya seleccionado ningún archivo
        $archivo = "";
    }
    

    try {
        $conn->beginTransaction(); // Inicia una transacción
    
        $stmt = $conn->prepare("INSERT INTO solicitud (s_fecha, s_doc, s_valor, tipo, solicitante, descripcion, estado) VALUES (NOW(), ?, ?, ?, ?, ?, '1')");
        $stmt->execute([$archivo, $valor, $tipo, $usuario_id, $descripcion]);
        
        // Obtén el ID de la solicitud insertada
        $solicitudId = $conn->lastInsertId();
    
        // Llama al procedimiento almacenado
        $stmt = $conn->prepare("CALL actualizar_departamento_encargado_proc(?, ?)");
        $stmt->execute([$tipo, $solicitudId]);
        
        $conn->commit(); // Commit la transacción si todo es correcto
    
        // Redirigir después de agregar
        header("Location: tbsolicitud.php");
        exit();
    } catch (PDOException $e) {
        $conn->rollBack(); // Hace rollback en caso de error
        echo "Error: " . $e->getMessage();
    }
    
}
?>

<div class="container mt-4">
    <h2 class="gestionar">Solicitudes</h2>
    <button type="button" class="btn btn-primary mb-4" data-bs-toggle="modal" data-bs-target="#agregarSolicitudModal">Agregar +</button>

    <table class="table">
        <thead>
            <tr>
                <th>Id</th>
                <th>Fecha</th>
                <th>Documento</th>
                <th>Tipo</th>
                <th>Descripción</th>
                <th>Valor solicitado</th>
                <th>Solicitante</th>
                <th>Encargado</th>
                <th>Estado</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($solicitudes as $solicitud) : ?>
                <tr>
                    <td><?php echo htmlspecialchars($solicitud['s_id']); ?></td>
                    <td><?php echo htmlspecialchars($solicitud['s_fecha']); ?></td>
                    <td>
    <?php if (isset($solicitud['s_doc']) && $solicitud['s_doc']) : ?>
        <a href="<?php echo htmlspecialchars($solicitud['s_doc']); ?>" target="_blank">Ver documento</a>
    <?php else : ?>
        <p1>No hay documento</p1>
    <?php endif; ?>
</td>

                    <td><?php echo htmlspecialchars($solicitud['tipo']); ?></td>
                    <td><?php echo htmlspecialchars($solicitud['descripcion']); ?></td>
                    <td>$ <?php echo htmlspecialchars($solicitud['s_valor']); ?></td>
                    <td><?php echo htmlspecialchars($solicitud['solicitante']); ?></td>
                    <td><?php echo htmlspecialchars($solicitud['encargado']); ?></td>
                    <td><?php echo htmlspecialchars($solicitud['estado']); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- Modal para agregar solicitud -->
<div class="modal fade" id="agregarSolicitudModal" tabindex="-1" aria-labelledby="agregarSolicitudModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="agregarSolicitudModalLabel">Agregar Solicitud</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="tbsolicitud.php" method="POST" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label for="documento" class="form-label">Documento</label>
                        <input type="file" class="form-control" id="documento" name="documento">
                    </div>
                    <div class="mb-3">
                        <label for="tipo" class="form-label">Tipo</label>
                        <select class="form-select" id="tipo_id" name="tipo_id">
                            <?php foreach ($tipo as $tipo) : ?>
                                <option value="<?php echo $tipo['id_tipo']; ?>"><?php echo htmlspecialchars($tipo['name_tipo']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="descripcion" class="form-label">Descripción</label>
                        <textarea class="form-control" id="descripcion" name="descripcion" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="valor" class="form-label">Valor solicitado</label>
                        <input type="number" class="form-control" id="valor" name="valor" step="0.01">
                    </div>
                    <button type="submit" class="btn btn-primary">Agregar Solicitud</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>