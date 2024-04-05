<?php
include '../includes/config.php'; //incluyendo la conexión de la base de datos

include '../includes/header.php'; //incluyendo la cabecera común

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
<div class="container mt-5 mr-5">
<h2 class="gestionar">Formatos de documentos</h2>
    <button type="button" class="btn btn-primary mb-4" data-bs-toggle="modal" data-bs-target="#agregarDocumentoModal">Agregar documento +</button>
</div>
<!-- Modal para agregar documento -->
<div class="modal fade" id="agregarDocumentoModal" tabindex="-1" aria-labelledby="agregarDocumentoModalLabel" aria-hidden="true" onsubmit="return validarTipo()">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="agregarDocumentoModalLabel">Agregar documento</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
            <form id="formDocumento" method="post" enctype="multipart/form-data" onsubmit="return validarCamposEvento()">
                <div class="mb-3">
                    <label for="nombre" class="form-label">Nombre del Documento</label>
                    <input type="text" class="form-control" id="nombre" name="nombre" requerid>
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
        <script>
            $("#tablaDocumentos").load("tablaDocumento.php");
            $(document).ready(function() {

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
            document.getElementById('nombreEdit').addEventListener('input', function() {
                // Obtener el valor actual del campo de celular
                var documentoNombre = this.value;
                // Limitar el valor a 100 caracteres
                if (documentoNombre.length > 100) {
                    this.value = documentoNombre.slice(0, 100);
                }
            });
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
            function validarCamposEvento() {
                var nombreDocumento = document.getElementById("nombre").value;
                if (nombreDocumento === "") {
                    alert("El documento debe tener un nombre");
                    return false;
                }
                var archivoInput = document.getElementById("documento");


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
            document.getElementById('nombre').addEventListener('input', function() {
                // Obtener el valor actual del campo de celular
                var documentoNombre = this.value;
                // Limitar el valor a 100 caracteres
                if (documentoNombre.length > 100) {
                    this.value = documentoNombre.slice(0, 100);
                }
            });
            // Función para limitar la cantidad de dígitos en el campo de descripcion
            document.getElementById('descripcion').addEventListener('input', function() {
                // Obtener el valor actual del campo de celular
                var documentoDescripcion = this.value;
                // Limitar el valor a 10 caracteres
                if (documentoDescripcion.length > 300) {
                    this.value = documentoDescripcion.slice(0, 300);
                }
            });
        </script>
                <script>
            // Función para manejar la respuesta del servidor
            function handleResponse(response) {
                if (response.success) {
                    alertify.success(response.message);
                    // Recargar la página después de 1.5 segundos
                    setTimeout(function() {
                        location.reload();
                    }, 1500);
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
