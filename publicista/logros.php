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
        if ($_SERVER["REQUEST_METHOD"] == "POST") {

            $nombre = $_POST['nombre'];
            $depor = $_POST['deporte_id'];
            if (trim($nombre) == '') {
                $error = "El nombre no puede estar vacio.";
            } else {

            if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] == 0) {

                $directorioDestino = "../uploads/deportes/logros/";
                $archivoImagen = $directorioDestino . basename($_FILES['imagen']['name']);
                $tipoArchivo = strtolower(pathinfo($archivoImagen, PATHINFO_EXTENSION));
                $check = getimagesize($_FILES["imagen"]["tmp_name"]);

                if ($check != false) {
                    // Verificar si el archivo ya existe y renombrarlo si es necesario
                    $contador = 1;
                    $nombreArchivo = pathinfo($_FILES['imagen']['name'], PATHINFO_FILENAME);
                    $extensionArchivo = pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION);
                    $archivo = $directorioDestino . $nombreArchivo . '.' . $extensionArchivo;

                    while (file_exists($archivo)) {
                        $nombreArchivo = pathinfo($_FILES['imagen']['name'], PATHINFO_FILENAME) . '_' . $contador;
                        $archivo = $directorioDestino . $nombreArchivo . '.' . $extensionArchivo;
                        $contador++;
                    }

                    $archivoImagen = $archivo;

                    if (move_uploaded_file($_FILES["imagen"]["tmp_name"], $archivoImagen)) {
                        // la imagen se cargó correctamente
                    } else {
                        $error = "Hubo un error al cargar la imagen";
                    }
                } else {
                    $error = "El archivo no es una imagen";
                }
            } else {
                // manejo en el caso de que la imagen no se cargue una imagen
                $archivoImagen = "";
            }

            //insertar en la base de datos (con o sin imagen)

            try {
                $stmt = $conn->prepare("INSERT INTO logros (titulo, deporte_id, imagen) VALUES (?, ?, ?)");
                $stmt->execute([$nombre, $depor, $archivoImagen]);

                //redirigir despues de agregar

                header("Location: gestionar_deportes.php");
                exit();
            } catch (PDOException $e) {
                echo "Error: " . $e->getMessage();
            }
        }
        }   
?>
        <div class="container mt-4">
            <h2 class="gestionar">Logros</h2>
            <form action="logros.php" method="post" enctype="multipart/form-data" onsubmit="return validarCampos()">
                <div class="mb-3">
                    <label for="Nombre" class="form-label">Nombre del logro</label>
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
                <button type="submit" class="btn btn-primary">Publicar</button>
            </form>
        </div>
        <script>
            function validarCamposEvento() {

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
                return true;
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
include '../includes/footer.php';
?>