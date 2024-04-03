<?php
session_start();
include '../includes/config.php'; // incluyendo la conexión de la base de datos
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
        $idEscenario = isset($_GET['id']) ? $_GET['id'] : null;
        try {
            $stmt = $conn->prepare("SELECT * FROM escenarios");
            $stmt->execute();
            $escenarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
        if ($idEscenario) {
            try {
                $stmt = $conn->prepare("SELECT * FROM escenarios WHERE id = :id");
                $stmt->bindParam(':id', $idEscenario, PDO::PARAM_INT);
                $stmt->execute();
                $escenario = $stmt->fetch(PDO::FETCH_ASSOC);
            } catch (PDOException $e) {
                echo "Error: " . $e->getMessage();
            }
        } else {
            echo "ID de solicitud no proporcionado";
            exit();
        }
?>
        <form id="formEscenarioEdit" action="editarEscenario.php" method="post" enctype="multipart/form-data" onsubmit="return validarCamposEdit()">
            <div class="mb-3">
                <label for="nombre" class="form-label">Nombre del Escenario</label>
                <input type="text" class="form-control" id="nombreEdit" name="nombreEdit" required value="<?php echo htmlspecialchars($escenario['nombre']); ?>">
            </div>
            <div class="mb-3">
                <label for="ubicacion" class="form-label">Ubicación</label>
                <textarea class="form-control" id="ubicacionEdit" name="ubicacionEdit" rows="3"><?php echo htmlspecialchars($escenario['ubicacion']); ?></textarea>
            </div>
            <div class="mb-3">
                <label for="direccion" class="form-label">Dirección</label>
                <input type="text" class="form-control" id="direccionEdit" name="direccionEdit" required value="<?php echo htmlspecialchars($escenario['direccion']); ?>">

            </div>
            <div class="mb-3">
                <label for="telefono" class="form-label">Teléfono</label>
                <input type="text" class="form-control" id="telefonoEdit" name="telefonoEdit" required value="<?php echo htmlspecialchars($escenario['telefono']); ?>">
            </div>
            <div class="mb-3">
                <label for="supervisor" class="form-label">Supervisor</label>
                <input type="text" class="form-control" id="supervisorEdit" name="supervisorEdit" required value="<?php echo htmlspecialchars($escenario['supervisor']); ?>">
            </div>
            <div class="mb-3">
                <label for="celular" class="form-label">Celular del supervisor</label>
                <input type="text" class="form-control" id="celularEdit" name="celularEdit" required value="<?php echo htmlspecialchars($escenario['celular']); ?>">
            </div>
            <div class="mb-3">
                <label for="imagen" class="form-label">Imagen</label><br>
                <input type="checkbox" class="form-check-input" id="checkDImagen" name="checkDImagen" onchange="deshabilitarInputImagen()">

                <label class="form-check-label" for="checkEjemplo">Eliminar</label>
                <?php if (isset($escenario['imagen']) && $escenario['imagen']) : ?>
                    <a href="<?php echo htmlspecialchars($escenario['imagen']); ?>" target="_blank">Imagen anterior</a>
                <?php else : ?>
                    <a>No hay imagen</a>
                <?php endif; ?>
                <input type="file" class="form-control" id="imagenEdit" name="imagenEdit" value="<?php echo htmlspecialchars($escenario['imagen']); ?>" onchange="deshabilitarCheckbox()">
            </div>

            <input type="hidden" id="idEscenarioEdit" name="idEscenarioEdit" value="<?php echo $idEscenario; ?>">
            <button type="submit" class="btn btn-primary" id="btnEnviarEdit">Editar escenario</button>
        </form>

        <script>
            function validarCamposEdit() {
                var nombreEscenario = document.getElementById("nombreEdit").value;
                var ubicacionEscenario = document.getElementById("ubicacionEdit").value;
                var direccionEscenario = document.getElementById("direccionEdit").value;
                var telefonoEscenario = document.getElementById("telefonoEdit").value;
                var supervisorEscenario = document.getElementById("supervisorEdit").value;
                var celularEscenario = document.getElementById("celularEdit").value;

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
?>