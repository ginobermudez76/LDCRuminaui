<?php

include '../includes/config.php'; //incluyebdo la conexion de la base de datos
include '../includes/header.php'; //incluyendlo la cabecera comun

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
        // Mostrar el elemento del menú Administrarsdasdasdasdasdas

        if ($_SERVER["REQUEST_METHOD"] == "POST") {

            $nombre = $_POST['nombre'];
            $descripcion = $_POST['descripcion'];

            if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] == 0) {

                $directorioDestino = "../uploads/deportes/";
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
                $stmt = $conn->prepare("INSERT INTO deportes (nombre, descripcion, imagen) VALUES (?, ?, ?)");
                $stmt->execute([$nombre, $descripcion, $archivoImagen]);

                //redirigir despues de agregar

                header("Location: gestionar_deportes.php");
                exit();
            } catch (PDOException $e) {
                echo "Error: " . $e->getMessage();
            }
        }
?>

        <div class="container mt-4">
            <h2>Agregar Deporte</h2>
            <?php if (!empty($error)) : ?>
                <div class="alert alert-danger">
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>
            <form action="agregar_deporte.php" method="post" enctype="multipart/form-data" onsubmit="return validarCamposEvento()">
                <div class="mb-3">
                    <label for="nombre" class="form-label">Nombre del Deporte</label>
                    <input type="text" class="form-control" id="nombre" name="nombre" requerid>
                </div>
                <div class="mb-3">
                    <label for="descripcion" class="form-label">Descripción</label>
                    <textarea type="text" class="form-control" id="descripcion" name="descripcion" requerid></textarea>
                </div>
                <div class="mb-3">
                    <label for="imagen" class="form-label">Imagen</label>
                    <input type="file" class="form-control" id="imagen" name="imagen" requerid></textarea>
                </div>
                <button type="submit" class="btn btn-primary">Agregar nuevo deporte</button>
            </form>
        </div>
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

<?php
    } else {
        header("Location: ../public/index.php");
        exit();
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
include '../includes/footer.php'; ?>