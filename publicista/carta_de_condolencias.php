<?php
include '../includes/config.php'; // Incluyendo la conexión a la base de datos
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
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            // Obtener los datos del formulario
            $mensaje = $_POST['mensaje'];
            if (trim($mensaje) == '') {
                $error = "El mensaje no debe estar vacío";
            } else {
                if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] == 0) {
                    $directorioDestino = "../uploads/cartaCondolencia/";

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
                            //la imagen se cargo correctamente

                        } else {
                            $error = "Hubo un error al cargar la imagen";
                        }
                    } else {
                        $error = "El archivo no es una imagen";
                    }
                } else {
                    // Manejo en el caso de que la imagen no se cargue
                    $archivoImagen = "";
                }

                try {
                    $fecha_eliminar = date('Y-m-d H:i:s', strtotime('+1 week'));
                    $stmt = $conn->prepare("INSERT INTO carta_condolencias(imagen, mensaje, fecha_eliminar) VALUES (?, ?, ?)");
                    $stmt->execute([$archivoImagen, $mensaje, $fecha_eliminar]);

                    // Redirigir después de agregar
                    header("Location: carta_de_condolencias.php");
                    exit();
                } catch (PDOException $e) {
                    echo "Error: " . $e->getMessage();
                }
            }
        }
?>
        <div class="container mt-4">
            <h2 class="gestionar">Carta de condolencias</h2>
            <form action="carta_de_condolencias.php" method="post" enctype="multipart/form-data" onsubmit="return validarCamposEvento()">
                <div class="mb-3">
                    <label for="descripcion" class="form-label">Mensaje</label>
                    <textarea type="text" class="form-control" id="mensaje" name="mensaje"></textarea>
                </div>
                <div class="mb-3">
                    <label for="imagen" class="form-label">Imagen</label>
                    <input type="file" class="form-control" id="imagen" name="imagen" required></textarea>
                </div>
                <button type="submit" class="btn btn-primary">Mostrar</button>
            </form>
        </div>

        <script>
            function validarCamposEvento() {
                var mensajeCon = document.getElementById("mensaje").value;
                if (mensajeCon.trim() === "") {
                    alert("El mensaje no debe estar vacío");
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

            // Función para limitar la cantidad de dígitos en el campo de mensaje
            document.getElementById('mensaje').addEventListener('input', function() {
                // Obtener el valor actual del campo de mensaje
                var mensajeCondolencia = this.value;
                // Limitar el valor a 700 caracteres
                if (mensajeCondolencia.length > 700) {
                    this.value = mensajeCondolencia.slice(0, 700);
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
include '../includes/footer.php'
?>
