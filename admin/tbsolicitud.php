<?php
include '../includes/config.php'; // incluyendo la conexión de la base de datos
include '../includes/header.php'; // incluyendo la cabecera común
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
// obtener lista de tipo de solicitud
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
    $valor = isset($_POST['valor']) ? $_POST['valor'] : null;

    // Verificar si el valor es numérico
    if (!is_numeric($valor)) {
        $valor = null;
    }
    $tipo = $_POST['tipo_id'];

    // Obtener el nombre de usuario
    try {
        $stmt = $conn->prepare("SELECT nombre_usuario FROM usuarios WHERE id = :usuario_id");
        $stmt->bindParam(':usuario_id', $usuario_id, PDO::PARAM_INT);
        $stmt->execute();
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
        $nombre_usuario = $usuario['nombre_usuario'];
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }

    // Directorio de destino para el documento
    $directorioDestino = "../uploads/documentos/solicitudes/";

    // Crear el directorio del usuario si no existe
    $directorioUsuario = $directorioDestino . $nombre_usuario . '/';
    if (!file_exists($directorioUsuario)) {
        if (!mkdir($directorioUsuario, 0777, true)) {
            echo "Error al crear el directorio del usuario";
        } else {
            echo "Directorio del usuario creado correctamente: " . $directorioUsuario;
        }
    }

    if (isset($_FILES['documento']) && $_FILES['documento']['error'] == 0) {
        $nombreArchivo = basename($_FILES['documento']['name']);
        $archivo = $directorioUsuario . $nombreArchivo;

        // Verificar si el archivo ya existe y renombrarlo si es necesario
        $contador = 1;
        while (file_exists($archivo)) {
            $nombreArchivo = pathinfo($_FILES['documento']['name'], PATHINFO_FILENAME) . '_' . $contador . '.' . pathinfo($_FILES['documento']['name'], PATHINFO_EXTENSION);
            $archivo = $directorioUsuario . $nombreArchivo;
            $contador++;
        }

        // Verificar la extensión del archivo
        $tipoArchivo = strtolower(pathinfo($nombreArchivo, PATHINFO_EXTENSION));
        $extensionesPermitidas = array('pdf', 'doc', 'docx', 'txt');

        if (!in_array($tipoArchivo, $extensionesPermitidas)) {
            $error = "El archivo no es un documento válido";
        } else {
            // Mover el archivo al directorio de destino
            if (!move_uploaded_file($_FILES["documento"]["tmp_name"], $archivo)) {
                $error = "Hubo un error al cargar el documento";
            } else {
                echo "Archivo subido correctamente: " . $archivo;
            }
        }
    } else {
        // Manejo en el caso de que no se haya seleccionado ningún archivo
        $archivo = "";
    }

    try {
        $conn->beginTransaction(); // Inicia una transacción

        // Insertar la solicitud en la base de datos
        $stmt = $conn->prepare("INSERT INTO solicitud (s_fecha, s_doc, s_valor, tipo, solicitante, descripcion) VALUES (NOW(), ?, ?, ?, ?, ?)");
        $stmt->execute([$archivo, $valor, $tipo, $usuario_id, $descripcion]);

        // Obtener el ID de la solicitud insertada
        $solicitudId = $conn->lastInsertId();

        // Llamar al procedimiento almacenado para actualizar el departamento encargado
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
                        Tipo: <?php echo htmlspecialchars($solicitud['tipo']); ?><br>
                        <?php if (!empty($solicitud['s_valor'])) : ?>
                            Monto: $<?php echo htmlspecialchars($solicitud['s_valor']); ?><br>
                        <?php endif; ?>
                        <?php if (!empty($solicitud['descripcion'])) : ?>
                            Descripción: <?php echo htmlspecialchars($solicitud['descripcion']); ?>
                        <?php endif; ?>
                    </td>

                    <td>
                        Departamento: <?php echo htmlspecialchars($solicitud['departamento_encargado']); ?><br>
                        Persona: <?php echo htmlspecialchars($solicitud['encargado']); ?>
                    </td>
                    <td><?php echo htmlspecialchars($solicitud['estado']); ?></td>
                    <td>
                        <?php

                        if ($solicitud['estado'] == 'En tramite') {
                        ?>
                            <button type="button" class="btn btn-secondary btn-sm">Editar</button>
                        <?php
                        }
                        ?>
                        <button class="btn btn-danger btn-sm" onclick="confirmarEliminacion(<?php echo $solicitud['s_id']; ?>)">Eliminar</button>

                    </td>
                </tr>
            <?php endforeach; ?>
            <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
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

<script>
    function validarTipo() {
        var seleccionTipo = document.getElementById("tipo_id").value;
        if (seleccionTipo === "") {
            alert("Por favor, seleccione un tipo de solicitud");
            return false;
        }

        // Validar el tipo de archivo seleccionado
        var archivoInput = document.getElementById("documento");


        var archivo = archivoInput.files[0];
        var extensionesPermitidas = ['pdf', 'doc', 'docx', 'txt'];
        var extension = archivo.name.split('.').pop().toLowerCase();

        if (!extensionesPermitidas.includes(extension)) {
            alert("El archivo seleccionado no es válido. Por favor, seleccione un archivo PDF, DOC, DOCX o TXT");
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
        console.log("ID de solicitud a eliminar:", idSolicitud);

        // Utiliza jQuery para enviar una solicitud AJAX a eliminar_solicitud.php
        $.ajax({
            type: "POST",
            url: "eliminar_solicitud.php",
            data: {
                id: idSolicitud
            },
            success: function(response) {
                // Manejar la respuesta, si es necesario
                console.log(response);
                alert("La solicitud se eliminó correctamente. Ruta del documento eliminado: " + response);
                //Recargar la página
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