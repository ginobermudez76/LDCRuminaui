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
            function validarCamposEvento() {
                var nombreDeporte = document.getElementById("nombre").value;
                if (nombreDeporte === "") {
                    alert("El deporte debe tener un nombre");
                    return false;
                }
                var archivoInput = document.getElementById("imagen");


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
<?php
    } else {
        header("Location: ../public/index.php");
        exit();
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
include '../includes/footer.php'; ?>