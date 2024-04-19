<?php
include '../includes/config.php'; //incluyendo la conexión de la base de datos

include '../includes/header.php'; //incluyendo la cabecera común

if (!isset($_SESSION['usuario_admin'])) {
    echo "<script>window.location.href='../admin/login.php';</script>";
    exit();
}
$main =  'Documento';
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
        <div class="container mt-5 mr-5">
            <h2 class="gestionar">Formatos de documentos</h2>
            <button type="button" class="btn btn-primary mb-4" data-bs-toggle="modal" data-bs-target="#agregarDocumentoModal">Agregar documento +</button>
        </div>
        <!-- Modal para agregar documento -->
        <div class="modal fade" id="agregar<?php echo htmlspecialchars($main) ?>Modal" tabindex="-1" aria-labelledby="agregarDocumentoModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="agregar<?php echo htmlspecialchars($main) ?>ModalLabel">Agregar documento</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="form<?php echo htmlspecialchars($main) ?>" autocomplete="off" method="post" enctype="multipart/form-data" onsubmit="return validarCamposInsert()">
                            <div class="mb-3">
                                <label for="nombre" class="form-label">Nombre del Documento</label>
                                <input type="text" class="form-control" id="nombre" name="nombre" requerid maxlength="200">
                            </div>
                            <div class="mb-3">
                                <label for="descripcion" class="form-label">Descripción (la descripción no se muestra al publico)</label>
                                <textarea class="form-control" id="descripcion" name="descripcion" rows="3" maxlength="2000"></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="documento" class="form-label">Documento</label>
                                <input type="file" class="form-control" id="documento" name="documento" requerid></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary">Agregar nuevo documento</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div class="container">
            <div class="row" id="tablaDocumentos">
            </div>
        </div>

        <?php
        include 'validar.php';
        include 'limitar.php';
        ?>
        <script>
            var main = "<?php echo $main ?>";
            $("#tabla" + main + "s").load("tabla" + main + "s.php");
            $(document).ready(function() {
                var main = "<?php echo $main ?>";
                <?php $main ?>
                $("#tabla" + main + "s").load("tabla" + main + "s.php");

            });
        </script>
        <script>
            function trim(str) {
                return str.replace(/^\s+|\s+$/g, '');
            }

            function validarCamposEdit() {
                var nombreDocumento = document.getElementById("nombreEdit").value;
                nombreDocumento1 = trim(nombreDocumento);
                if (nombreDocumento1 === "") {
                    alert("El documento debe tener un nombre");
                    return false;
                }
                var archivoInput = document.getElementById("documentoEdit");


                var archivo = archivoInput.files[0];
                var extensionesPermitidas = ['pdf', 'doc', 'docx', 'txt', 'ppt', 'pptx'];
                var extension = archivo.name.split('.').pop().toLowerCase();

                if (!extensionesPermitidas.includes(extension)) {
                    alert("Formato no soportado");
                    return false;
                }


                return true;
            }
            // Función para limitar la cantidad de dígitos en el campo de celular
        </script>
        <script>
            function deshabilitarInputDocumento() {
                var checkbox = document.getElementById("checkDDocumento");
                var inputDocumento = document.getElementById("documentoEdit");

                if (checkbox.checked) {
                    inputDocumento.disabled = true;
                } else {
                    inputDocumento.disabled = false;
                }
            }

            function deshabilitarCheckbox() {
                var checkbox = document.getElementById("checkDDocumento");
                var inputDocumento = document.getElementById("documentoEdit");

                if (inputDocumento.value) {
                    checkbox.disabled = true;
                } else {
                    checkbox.disabled = false;
                }
            }
        </script>
        <script>
            // Función para manejar la respuesta del servidor
            function handleResponse(response) {
                if (response.success) {
                    alertify.success(response.message);
                    // Recargar la página después de 1.5 segundos
                    $('#formDocumento')[0].reset();
                    $("#tablaDocumentos").load("tablaDocumentos.php");
                } else {
                    alertify.error(response.message);
                }
            }

            $(document).ready(function() {
                // Manejar el envío del formulario
                $('#formDocumento').submit(function(event) {
                    event.preventDefault(); // Evitar el envío del formulario por defecto
                    var formData = new FormData($(this)[0]); // Obtener los datos del formulario
                    $.ajax({
                        url: 'insertarDocumento.php',
                        type: 'POST',
                        data: formData,
                        async: false,
                        success: function(response) {
                            handleResponse(JSON.parse(response));
                        },
                        cache: false,
                        contentType: false,
                        processData: false
                    });
                    return false;
                });
            });
        </script>
<?php
    } else {
        echo "<script>window.location.href='../public/index.php';</script>";
        exit();
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
include '../includes/footer.php'; ?>