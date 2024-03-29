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
        // Mostrar el elemento del menú Administrar
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
            <h2 class="gestionar">Logros</h2>
            <form action="insertarLogro.php" method="post" enctype="multipart/form-data" onsubmit="return validarCampos()">
                <div class="mb-3">
                    <label for="Nombre" class="form-label">Nombre del logro</label>
                    <input type="text" class="form-control" id="nombre" name="nombre"></input>
                </div>
                <div class="mb-3">
                <label for="Tipo" class="form-label">Tipo</label>
                <select class="form-control" id="tipoLogro" name="tipoLogro">
                        <option value="">Seleccione un tipo</option>
                        <option value="Medalla">Medalla</option>
                        <option value="Copa">Copa</option>
                        <option value="Reconocimiento">Reconocimiento</option>
                </select>
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
                <button type="submit" class="btn btn-primary">Publicar</button>
            </form>
        </div>
        <div class="container">
            <div class="row" id="tablaLogros">
            </div>
        </div>
        <script>
            function validarCamposEvento() {

                var nombreEvento = document.getElementById("nombre").value;
                if (nombreEvento === "") {
                    alert("El nombre no puede quedar vacio");
                    return false;
                }
                var nombreEvento = document.getElementById("tipoLogro").value;
                if (nombreEvento === "") {
                    alert("Seleccione un tipo de logro");
                    return false;
                }
                var nombredeporte = document.getElementById("deporte_id").value;
                if (nombredeporte === "") {
                    alert("El deporte no puede quedar vacio");
                    return false;
                }
                return true;
            }
        </script>
        <script>
            $("#tablaLogros").load("tablaLogros.php");
            $(document).ready(function() {

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