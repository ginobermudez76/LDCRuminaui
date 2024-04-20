<?php
include '../includes/config.php'; //incluyendo la conexión de la base de datos

include '../includes/header.php'; //incluyendo la cabecera común

if (!isset($_SESSION['usuario_admin'])) {
    echo "<script>window.location.href='../admin/login.php';</script>";
    exit();
}

$usuario_id = $_SESSION['usuario_id'];
$main = 'Escenario';
try {
    // Consultar el rol del usuario en la base de datos
    $stmt = $conn->prepare("SELECT rol FROM usuarios WHERE id = :usuario_id");
    $stmt->bindParam(':usuario_id', $usuario_id, PDO::PARAM_INT);
    $stmt->execute();

    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    // Verificar si el usuario tiene el rol de Publicista
    if ($usuario['rol'] == 7) {
        // Mostrar el elemento del menú para publicista
        //logica para obtener la lista de escenarios de la base de datos
        try {
            $stmt = $conn->prepare("SELECT * FROM escenarios");
            $stmt->execute();

            $escenarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
?>
        <div class="container mt-5 mr-5">
            <h2 class="gestionar">Escenarios ofertados</h2>
            <button type="button" class="btn btn-primary mb-4" data-bs-toggle="modal" data-bs-target="#agregarEscenarioModal">Agregar escenario +</button>
        </div>
        <!-- Modal para agregar escenario -->
        <div class="modal fade" id="agregarEscenarioModal" tabindex="-1" aria-labelledby="agregarEscenarioModalLabel" aria-hidden="true"">
    <div class=" modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="agregarEscenarioModalLabel">Agregar escenario</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="formEscenario" method="post" enctype="multipart/form-data" onsubmit="return validarCamposInsert()">
                        <div class="mb-3">
                            <label for="nombre" class="form-label">Nombre del Escenario</label>
                            <input type="text" class="form-control" id="nombre" name="nombre" required maxlength="100">
                        </div>
                        <div class="mb-3">
                            <label for="ubicacion" class="form-label">Ubicación (enlace de mapa de la ubicación en google maps)</label>
                            <textarea class="form-control" id="ubicacion" name="ubicacion" rows="3" required maxlength="5000"></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="direccion" class="form-label">Dirección</label>
                            <input type="text" class="form-control" id="direccion" name="direccion" required maxlength="500">
                        </div>
                        <div class="mb-3">
                            <label for="telefono" class="form-label">Teléfono</label>
                            <input type="text" class="form-control" id="telefono" name="telefono" required maxlength="10">
                        </div>
                        <div class="mb-3">
                            <label for="supervisor" class="form-label">Supervisor</label>
                            <input type="text" class="form-control" id="supervisor" name="supervisor" required maxlength="250">
                        </div>
                        <div class="mb-3">
                            <label for="celular" class="form-label">Celular del supervisor</label>
                            <input type="text" class="form-control" id="celular" name="celular" required maxlength="10">
                        </div>
                        <div class="mb-3">
                            <label for="imagen" class="form-label">Imagen</label>
                            <input type="file" class="form-control" id="imagen" name="imagen" required></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">Agregar nuevo escenario</button>
                    </form>
                </div>
            </div>
        </div>
        </div>
        <div class="container">
            <div class="row" id="tablaEscenarios">
            </div>
        </div>

        <script>
            $("#tablaEscenarios").load("tablaEscenarios.php");
            $(document).ready(function() {

            });
        </script>

        <?php include 'validar.php';
        include 'limitar.php';
        include 'cargar.php';
        ?>

        <script>
            function trim(str) {
                return str.replace(/^\s+|\s+$/g, '');
            }

            function validarCamposEdit() {
                var nombreEscenario = trim(document.getElementById("nombreEdit").value);
                var ubicacionEscenario = trim(document.getElementById("ubicacionEdit").value);
                var direccionEscenario = trim(document.getElementById("direccionEdit").value);
                var telefonoEscenario = trim(document.getElementById("telefonoEdit").value);
                var supervisorEscenario = trim(document.getElementById("supervisorEdit").value);
                var celularEscenario = trim(document.getElementById("celularEdit").value);

                if (nombreEscenario === "" || ubicacionEscenario === "" || direccionEscenario === "" || telefonoEscenario === "" || supervisorEscenario === "" || celularEscenario === "") {
                    alert("Todos los campos son obligatorios");
                    return false;
                }

                var archivoInput = document.getElementById("imagenEdit");
                var archivo = archivoInput.files[0];
                if (archivo && !(/\.(jpg|jpeg|png|gif)$/i).test(archivo.name)) {
                    alert("Formato de imagen no válido");
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
include '../includes/footer.php'; ?>