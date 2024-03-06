<?php

include '../includes/config.php';
include '../includes/header.php';

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

        // Tomar los valores enviados por los botones de gestionar_eventos.php o gestionar_deportes.php
        if (isset($_GET['id']) && isset($_GET['nombre']) && isset($_GET['tipo'])) {
            $idTipo = $_GET['id'];
            $nombreTipo = urldecode($_GET['nombre']);
            $tipoGaleria = $_GET['tipo'];
        }

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            // Obtener datos del formulario
            $tipo = $_POST['tipo_galeria'];
            $nombre = $_POST['nombre_tipo'];
            $id = $_POST['id_tp'];

            // Crear una carpeta para las imágenes
            $carpetaImagenes = "";

            if ($tipo == "Evento") {
                $carpetaImagenes = "../uploads/eventos/" . $nombre . "_" . $id;
            } elseif ($tipo == "Deporte") {
                $carpetaImagenes = "../uploads/deportes/" . $nombre . "_" . $id;
            }

            if (!file_exists($carpetaImagenes)) {
                mkdir($carpetaImagenes, 0777, true);
            }

            // Procesar las imágenes
            $archivosImagen = [];
            if (!empty($_FILES['imagenes']['name'][0])) {
                foreach ($_FILES['imagenes']['tmp_name'] as $key => $tmp_name) {
                    $nombreArchivo = pathinfo($_FILES['imagenes']['name'][$key], PATHINFO_FILENAME);
                    $extensionArchivo = pathinfo($_FILES['imagenes']['name'][$key], PATHINFO_EXTENSION);
                    $archivoImagen = $carpetaImagenes . "/" . $nombreArchivo . '.' . $extensionArchivo;

                    // Verificar si el archivo ya existe y renombrarlo si es necesario
                    $contador = 1;
                    while (file_exists($archivoImagen)) {
                        $nombreArchivo = pathinfo($_FILES['imagenes']['name'][$key], PATHINFO_FILENAME) . '_' . $contador;
                        $archivoImagen = $carpetaImagenes . "/" . $nombreArchivo . '.' . $extensionArchivo;
                        $contador++;
                    }

                    $tipoArchivo = strtolower(pathinfo($archivoImagen, PATHINFO_EXTENSION));

                    $check = getimagesize($tmp_name);

                    if ($check !== false) {
                        if (move_uploaded_file($tmp_name, $archivoImagen)) {
                            // La imagen se cargó correctamente
                            $archivosImagen[] = $archivoImagen;
                        } else {
                            $error = "Hubo un error al cargar una o más imágenes";
                        }
                    } else {
                        $error = "Uno o más archivos no son imágenes válidas";
                    }
                }
            }

            try {
                // Insertar las rutas de las imágenes en la base de datos
                foreach ($archivosImagen as $rutaImagen) {
                    // Aquí es donde necesitas definir $rutaCarpeta
                    $rutaCarpeta = $carpetaImagenes;

                    $stmtImagen = $conn->prepare("INSERT INTO galeria_imagenes (tipo, id_tipo, nombre, ruta_imagenes, ruta_carpeta) VALUES (?, ?, ?, ?, ?)");
                    $stmtImagen->execute([$tipo, $id, $nombre, $rutaImagen, $rutaCarpeta]);
                }

                // Redirigir después de agregar
                if ($tipo == "Evento") {
                    header("Location: gestionar_eventos.php");
                } elseif ($tipo == "Deporte") {
                    header("Location: gestionar_deportes.php");
                }

                exit();
            } catch (PDOException $e) {
                echo "Error: " . $e->getMessage();
            }
        }
?>

        <div class="container mt-4">
            <h2>Agregar imagenes</h2>
            <?php if (!empty($error)) : ?>
                <div class="alert alert-danger">
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>
            <form action="galeria_de_imagenes.php" method="post" enctype="multipart/form-data">
                <div class="mb-3">
                    <label for="input1" class="form-label">Tipo</label>
                    <input type="text" class="form-control" id="tipo_galeria" name="tipo_galeria" value="<?php echo htmlspecialchars($tipoGaleria); ?>" readonly>
                </div>

                <div class="mb-3">
                    <label for="input2" class="form-label">Nombre</label>
                    <input type="text" class="form-control" id="nombre_tipo" name="nombre_tipo" value="<?php echo htmlspecialchars($nombreTipo); ?>" readonly>
                </div>

                <div class="mb-3">
                    <label for="input3" class="form-label">ID</label>
                    <input type="text" class="form-control" id="id_tp" name="id_tp" value="<?php echo htmlspecialchars($idTipo); ?>" readonly>
                </div>
                <div class="mb-3">
                    <label for="imagenes" class="form-label">Galería de imágenes</label>
                    <input type="file" class="form-control" id="imagenes" name="imagenes[]" multiple required>
                </div>

                <button type="submit" class="btn btn-primary">Guardar galería</button>
            </form>
        </div>
<?php
    } else {
        header("Location: ../public/index.php");
        exit();
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
include '../includes/footer.php'; ?>