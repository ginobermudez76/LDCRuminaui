<?php
include '../includes/config.php'; // incluyendo la conexión de la base de datos
include '../includes/header.php'; // incluyendo la cabecera común
if (!isset($_SESSION['usuario_admin'])) {
    header("Location: ../admin/login.php");
    exit();
}
$usuario_id = $_SESSION['usuario_id'];
// obtener lista de tipo de solicitud
try {
    $stmt = $conn->prepare("SELECT * FROM solicitud_tipo");
    $stmt->execute();
    $tipos = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}

?>
<div class="container mt-5 mr-5">
<h2 class="gestionar">Solicitudes</h2>
    <button type="button" class="btn btn-primary mb-4" data-bs-toggle="modal" data-bs-target="#agregarSolicitudModal">Agregar +</button>
</div>
<div class="container">
    <div class="row" id="tablaSoli">
    </div>
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
                <form action="insertarSolicitud.php" method="POST" enctype="multipart/form-data">
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
    $("#tablaSoli").load("tablaSolicitudes.php"); //load es una funcion de Jquery

    function deshabilitarInputArchivo() {
        var checkbox = document.getElementById("checkDArchivo");
        var inputArchivo = document.getElementById("documentoEdit");

        if (checkbox.checked) {
            inputArchivo.disabled = true;
        } else {
            inputArchivo.disabled = false;
        }
    }

    function deshabilitarCheckbox() {
        var checkbox = document.getElementById("checkDArchivo");
        var inputArchivo = document.getElementById("documentoEdit");

        if (inputArchivo.files.length > 0) {
            checkbox.disabled = true;
        } else {
            checkbox.disabled = false;
        }
    }

    function validarTipo() {
        var seleccionTipo = document.getElementById("tipo_id").value;
        if (seleccionTipo === "") {
            alert("Por favor, seleccione un tipo de solicitud");
            return false;
        }

        // Validar el tipo de archivo seleccionado
        var archivoInput = document.getElementById("documento");


        var archivo = archivoInput.files[0];
        var extensionesPermitidas = ['pdf'];
        var extension = archivo.name.split('.').pop().toLowerCase();

        if (!extensionesPermitidas.includes(extension)) {
            alert("El archivo seleccionado no es válido. Por favor, seleccione un archivo PDF");
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
</script>
<script>
    function validarTipoEdit() {
        var seleccionTipoEdit = document.getElementById("tipoEdit").value;
        if (seleccionTipoEdit === "") {
            alert("Por favor, seleccione un tipo de solicitud");
            return false;
        }

        // Validar el tipo de archivo seleccionado
        var archivoInputEdit = document.getElementById("documentoEdit");


        var archivoEdit = archivoInputEdit.files[0];
        var extensionesPermitidasEdit = ['pdf', 'doc', 'docx', 'txt'];
        var extensionEdit = archivoEdit.name.split('.').pop().toLowerCase();

        if (!extensionesPermitidasEdit.includes(extensionEdit)) {
            alert("El archivo seleccionado no es válido. Por favor, seleccione un archivo PDF, DOC, DOCX o TXT");
            return false;
        }
        return true;
    }
</script>


<?php include '../includes/footer.php'; ?>