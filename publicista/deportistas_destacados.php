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

        // Obtener la lista de tipo de deportes
        try {
            $stmt = $conn->prepare("SELECT id, nombre FROM deportes");
            $stmt->execute();
            $deportes = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
?>
        <div class="container mt-4">
            <h2 class="gestionar">Deportistas destacados</h2>
            <form id="formDeportistas" enctype="multipart/form-data" method="post" onsubmit="return validarCampos()">
                <div class="mb-3">
                    <label for="Nombre" class="form-label">Nombre del deportista</label>
                    <input type="text" class="form-control" id="nombre" name="nombre"></input>
                </div>
                <div class="mb-3">
                    <label for="imagen" class="form-label">Imagen</label>
                    <input type="file" class="form-control" id="imagen" name="imagen" required></textarea>
                </div>
                <select class="form-control" id="deporte_id" name="deporte_id">
                    <option value="">Seleccione un deporte</option>
                    <?php foreach ($deportes as $deporte) : ?>
                        <option value="<?php echo $deporte['id']; ?>"><?php echo htmlspecialchars($deporte['nombre']); ?></option>
                    <?php endforeach; ?>
                </select>
                <button type="submit" class="btn btn-primary" id="btnEnviar">Publicar</button>
            </form>
        </div>
        <div class="container">
            <div class="row" id="tablaDeportista">
            </div>
        </div>
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
        </script>
        <script>
            $("#tablaDeportista").load("tablaDeportistas.php"); //load es una funcion de Jquery
            $(document).ready(function() {


                $('#btnEnviar').click(function() {
                    var formData = new FormData($('#formDeportistas')[0]);
                    $.ajax({
                        url: 'insertardeportista.php',
                        type: 'POST',
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function(response) {
                            var jsonData = JSON.parse(response);
                            if (jsonData.success) {
                                // Mostrar mensaje de éxito
                                alert(jsonData.message);
                                $("#tablaDeportista").load("tablaDeportistas.php");
                                $("#formDeportistas")[0].reset();
                            } else {
                                // Mostrar mensaje de error
                                alert(jsonData.message);
                            }
                        }
                    });


                });
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
include '../includes/footer.php';
?>