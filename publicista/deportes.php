<?php
include '../includes/config.php'; //incluyendo la conexión de la base de datos

include '../includes/header.php'; //incluyendo la cabecera común

if (!isset($_SESSION['usuario_admin'])) {
    header("Location: ../admin/login.php");
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
<div class="container mt-5 mr-5">
<h2 class="gestionar">Deportes ofertados</h2>
    <button type="button" class="btn btn-primary mb-4" data-bs-toggle="modal" data-bs-target="#agregarDeporteModal">Agregar deporte +</button>
</div>
<!-- Modal para agregar dporte -->
<div class="modal fade" id="agregarDeporteModal" tabindex="-1" aria-labelledby="agregarDeporteModalLabel" aria-hidden="true" onsubmit="return validarTipo()">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="agregarDeporteModalLabel">Agregar deporte</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
            <form id="formDeporte" action="insertarDeporte.php" method="post" enctype="multipart/form-data" onsubmit="return validarCamposEvento()">
                <div class="mb-3">
                    <label for="nombre" class="form-label">Nombre del Deporte</label>
                    <input type="text" class="form-control" id="nombre" name="nombre" requerid>
                </div>
                <div class="mb-3">
                    <label for="descripcion" class="form-label">Descripción</label>
                    <textarea type="text" class="form-control" id="descripcion" name="descripcion"></textarea>
                </div>
                <div class="mb-3">
                    <label for="imagen" class="form-label">Imagen</label>
                    <input type="file" class="form-control" id="imagen" name="imagen" requerid></textarea>
                </div>
                <button type="submit" class="btn btn-primary">Agregar nuevo deporte</button>
            </form>
            </div>
        </div>
    </div>
</div>
        <div class="container">
            <div class="row" id="tablaDeportes">
            </div>
        </div>
        <script>
            $("#tablaDeportes").load("tablaDeportes.php");
            $(document).ready(function() {

            });
        </script>
        <script>
            function validarCamposEdit() {
                var nombreDeporte = document.getElementById("nombreEdit").value;
                if (nombreDeporte === "") {
                    alert("El deporte debe tener un nombre");
                    return false;
                }
                var archivoInput = document.getElementById("imagenEdit");


                var archivo = archivoInput.files[0];
                var extensionesPermitidas = ['gif', 'png', 'jpg', 'webp', 'jpeg'];
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
                var deporteNombre = this.value;
                // Limitar el valor a 100 caracteres
                if (deporteNombre.length > 100) {
                    this.value = deporteNombre.slice(0, 100);
                }
            });
            // Función para limitar la cantidad de dígitos en el campo de descripcion
            document.getElementById('descripcionedit').addEventListener('input', function() {
                // Obtener el valor actual del campo de celular
                var deporteDescripcion = this.value;
                // Limitar el valor a 10 caracteres
                if (deporteDescripcion.length > 300) {
                    this.value = deporteDescripcion.slice(0, 300);
                }
            });
        </script>
        <script>
            function deshabilitarInputImagen() {
                var checkbox = document.getElementById("checkDImagen");
                var inputImagen = document.getElementById("imagenEdit");

                if (checkbox.checked) {
                    inputImagen.disabled = true;
                } else {
                    inputImagen.disabled = false;
                }
            }

            function deshabilitarCheckbox() {
                var checkbox = document.getElementById("checkDImagen");
                var inputImagen = document.getElementById("imagenEdit");

                if (inputImagen.value) {
                    checkbox.disabled = true;
                } else {
                    checkbox.disabled = false;
                }
            }
        </script>
            <script>
            function validarCamposEvento() {
                var nombreDeporte = document.getElementById("nombre").value;
                if (nombreDeporte === "") {
                    alert("El deporte debe tener un nombre");
                    return false;
                }
                var archivoInput = document.getElementById("imagen");


                var archivo = archivoInput.files[0];
                var extensionesPermitidas = ['gif', 'png', 'jpg', 'webp', 'jpeg' , 'svg'];
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
                var deporteNombre = this.value;
                // Limitar el valor a 100 caracteres
                if (deporteNombre.length > 100) {
                    this.value = deporteNombre.slice(0, 100);
                }
            });
            // Función para limitar la cantidad de dígitos en el campo de descripcion
            document.getElementById('descripcion').addEventListener('input', function() {
                // Obtener el valor actual del campo de celular
                var deporteDescripcion = this.value;
                // Limitar el valor a 10 caracteres
                if (deporteDescripcion.length > 300) {
                    this.value = deporteDescripcion.slice(0, 300);
                }
            });
        </script>
<?php
    } else {
        header("Location: ../public/index.php");
        exit();
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
include '../includes/footer.php'; ?>