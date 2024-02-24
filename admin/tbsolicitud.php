<?php
include '../includes/config.php'; //incluyendo la conexión de la base de datos
include '../includes/header.php'; //incluyendo la cabecera común
if (!isset($_SESSION['usuario_admin'])) {
    header("Location: /Ayudantias-1/admin/login.php");
    exit();
}
$usuario_id = $_SESSION['usuario_id'];

try {
    $stmt = $conn->prepare("CALL mostrar_solicitudes(:usuario_id)");
    $stmt->bindParam(':usuario_id', $usuario_id, PDO::PARAM_INT);
    $stmt->execute();
    $solicitudes = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
//obtener lista de tipo de solicitud
try {
    $stmt = $conn->prepare("SELECT id_tipo, name_tipo FROM solicitud_tipo");
    $stmt->execute();
    $tipos = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}

// Llamar al procedimiento almacenado para obtener las solicitudes

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $descripcion = $_POST['descripcion'];
    $valor = $_POST['valor'];
    $tipo = $_POST['tipo_id'];
    if (isset($_FILES['documento']) && $_FILES['documento']['error'] == 0) {
        $directorioDestino = "../uploads/documentos/solicitudes/";
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

        $stmt = $conn->prepare("INSERT INTO solicitud (s_fecha, s_doc, s_valor, tipo, solicitante, descripcion) VALUES (NOW(), ?, ?, ?, ?, ?)");
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
                <th>Fecha y hora</th>
                <th>Documento</th>
                <th>Descripción</th>
                <th>Encargado</th>
                <th>Estado</th>
                <th>Acciones</th>
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
                    <td>
                        Tipo:<?php echo htmlspecialchars($solicitud['tipo']); ?><br>
                        Valor solicitado: $ <?php echo htmlspecialchars($solicitud['s_valor']); ?><br>
                        Descripción: <?php echo htmlspecialchars($solicitud['descripcion']); ?>
                
                    </td>
                    <td>
                        Departamento: <?php echo htmlspecialchars($solicitud['departamento_encargado']); ?><br>
                        Persona: <?php echo htmlspecialchars($solicitud['encargado']); ?>
                    </td>
                    <td><?php echo htmlspecialchars($solicitud['estado']); ?></td>
                    <td>
                    <button type="button" class="btn btn-secondary btn-sm">Editar</button>
                        <button class="btn btn-danger btn-sm" onclick="confirmarEliminacion(<?php echo $solicitud['s_id']; ?>)">Eliminar</button>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<!-- Modal para agregar solicitud -->
<div class="modal fade" id="agregarSolicitudModal" tabindex="-1" aria-labelledby="agregarSolicitudModalLabel" aria-hidden="true" onsubmit="return validarTipo()">
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
                            <option value="">Tipo de solicitud</option>
                            <?php foreach ($tipos as $tipo) : ?>
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
<!-- Modal para editar solicitud -->
<div class="modal fade" id="editarSolicitudModal" tabindex="-1" aria-labelledby="editarSolicitudModalLabel" aria-hidden="true" onsubmit="return validarTipo()">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="agregarSolicitudModalLabel">Edición</h5>
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
                            <option value="">Tipo de solicitud</option>
                            <?php foreach ($tipos as $tipo) : ?>
                                <option value="<?php echo $tipo['id_tipo']; ?>"><?php echo htmlspecialchars($tipo['name_tipo']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="descripcion" class="form-label">Descripción</label>
                        <textarea class="form-control" id="descripcion" name="descripcion" rows="3" value="<?php echo htmlspecialchars($solicitud['descripcion']); ?>"></textarea>
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
<script>
    function validarTipo(){
        var seleccionTipo = document.getElementById("tipo_id").value;
        if (seleccionTipo === "") {
            alert("Por favor, seleccione un tipo de solicitud");
            return false;
        }
        return true;
    }
    function confirmarEliminacion(idSolicitud) {
        var confirmacion = confirm("¿Está seguro que desea eliminar esta solicitud?");

        if (confirmacion) {
            // Usuario hizo clic en "Aceptar", enviar solicitud a eliminar_solicitud.php
            eliminarSolicitud(idSolicitud);
        } else {
            // Usuario hizo clic en "Cancelar", no hacer nada
        }
    }

    function eliminarSolicitud(idSolicitud) {
        // Utiliza jQuery para enviar una solicitud AJAX a eliminar_evento.php
        $.ajax({
            type: "POST",
            url: "eliminar_solicitud.php",
            data: {
                id: idSolicitud
            },
            success: function(response) {
                // Manejar la respuesta, si es necesario
                console.log(response);

                //Recargar la página o
                location.reload();
            },
            error: function(error) {
                // Manejar errores si es necesario
                console.error(error);
            }
        });
    }
</script>

<?php include '../includes/footer.php'; ?>

quiero mandar el id de usuario y el id de la solicitud al modal editarSolicitudModal al precionar el boton editar, para que me obtenga los datos de la solicitud y los ponga en los campos del modal para editar, y luego quiero mandar los datos actualizados al modal