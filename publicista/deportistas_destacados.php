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

        // Obtener la lista de tipo de deportes
        try {
            $stmt = $conn->prepare("SELECT id, nombre FROM deportes");
            $stmt->execute();
            $deportes = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
?>
        <div class="container mt-5 mr-5">
            <h2 class="gestionar">Deportistas destacados</h2>
            <button type="button" class="btn btn-primary mb-4" data-bs-toggle="modal" data-bs-target="#agregarDeportistaModal">Agregar deportista +</button>
        </div>
        <!-- Modal para agregar deportista destacado -->
        <div class="modal fade" id="agregarDeportistaModal" tabindex="-1" aria-labelledby="agregarDeportistaModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="agregarDeportistaModalLabel">Agregar deportista</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="formDeportistas" enctype="multipart/form-data" method="post" onsubmit="return validarCampos()">
                            <div class="mb-3">
                                <label for="Nombre" class="form-label">Nombre del deportista</label>
                                <input type="text" class="form-control" id="nombre" name="nombre" required maxlength="200"></input>
                            </div>
                            <div class="mb-3">
                                <label for="imagen" class="form-label">Imagen</label>
                                <input type="file" class="form-control" id="imagen" name="imagen" required></textarea>
                            </div>
                            <select class="form-control" id="deporte_id" name="deporte_id" required>
                                <option value="">Seleccione un deporte</option>
                                <?php foreach ($deportes as $deporte) : ?>
                                    <option value="<?php echo $deporte['id']; ?>"><?php echo htmlspecialchars($deporte['nombre']); ?></option>
                                <?php endforeach; ?>
                            </select>
                            <button type="submit" class="btn btn-primary" id="btnEnviar">Publicar</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="container">
            <div class="row" id="tablaDeportista">
            </div>
        </div>
        <script>
            $("#tablaDeportista").load("tablaDeportistas.php");
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
                $('#formDeportistas').submit(function(event) {
                    event.preventDefault(); // Evitar el envío del formulario por defecto
                    var formData = new FormData($(this)[0]); // Obtener los datos del formulario
                    $.ajax({
                        url: 'insertardeportista.php',
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



        <script>
            function validarCampos() {

                var nombreEvento = document.getElementById("nombre").value;
                if (nombreEvento === "") {
                    alert("El nombre no puede quedar vacio");
                    return false;
                }
                var nombredeporte = document.getElementById("deporte_id").value;
                if (nombredeporte === "") {
                    alert("El deporte no puede quedar vacio");
                    return false;
                }
                var imagenin = document.getElementById("imagen").value;
                if (imagenin === "") {
                    alert("La imagen es obligatoria");
                    return false;
                }

                var archivoInput = document.getElementById("imagen");
                var archivo = archivoInput.files[0];
                var extensionesPermitidas = ['gif', 'png', 'jpg', 'webp', 'jpeg', 'svg'];
                var extension = archivo.name.split('.').pop().toLowerCase();

                if (!extensionesPermitidas.includes(extension)) {
                    alert("Formato no soportado");
                    return false;
                }
                return true;
            }
            document.getElementById('nombre').addEventListener('input', function() {
                // Obtener el valor actual del campo de celular
                var Nombre = this.value;
                // Limitar el valor a 100 caracteres
                if (Nombre.length > 100) {
                    this.value = Nombre.slice(0, 100);
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
            function trim(str) {
                return str.replace(/^\s+|\s+$/g, '');
            }

            function validarCamposEdit() {

                var nombreEdit = document.getElementById("nombreEdit").value;
                nombreEdit1 = trim(nombreEdit);
                if (nombreEdit1 === "") {
                    alert("El nombre no puede quedar vacio");
                    return false;
                }
                var nombredeporteEdit = document.getElementById("deporte_idEdit").value;
                if (nombredeporteEdit === "") {
                    alert("El deporte no puede quedar vacio");
                    return false;
                }
                var imagenEditIn = document.getElementById("imagenEdit").value;
                if (imagenEditin === "") {
                    alert("La imagen es obligatoria");
                    return false;
                }

                var imagenInputEdit = document.getElementById("imagenEdit");
                var imagen = imagenInputEdit.files[0];
                var extensionesPermitidas = ['gif', 'png', 'jpg', 'webp', 'jpeg', 'svg'];
                var extension = imagen.name.split('.').pop().toLowerCase();

                if (!extensionesPermitidas.includes(extension)) {
                    alert("Formato no soportado");
                    return false;
                }
                return true;
            }
        </script>
<?php
    } else {
        echo "<script>window.location.href='../public/index.php';</script>";
        exit();
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
include '../includes/footer.php';
?>