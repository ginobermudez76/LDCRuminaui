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
            $descripcion = $_POST['descripcion'];
            $fecha_ini = $_POST['fecha_ini'];
            $fecha_f = $_POST['fecha_f'];
            $deporte_id = $_POST['deporte_id'];

            if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] == 0) {
                $directorioDestino = "../uploads/eventos/";

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
                $stmt = $conn->prepare("INSERT INTO eventos (nombre, descripcion, fecha_inicio, fecha_fin, deporte_id, imagen) VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->execute([$nombre, $descripcion, $fecha_ini, $fecha_f, $deporte_id, $archivoImagen]);

                // Redirigir después de agregar
                header("Location: gestionar_eventos.php");
                exit();
            } catch (PDOException $e) {
                echo "Error: " . $e->getMessage();
            }
        }
?>

        <div class="container mt-4">
            <h2>Agregar Evento</h2>
            <?php if (!empty($error)) : ?>
                <div class="alert alert-danger">
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>
            <form action="agregar_evento.php" method="post" enctype="multipart/form-data" onsubmit="return validarCamposEvento()">
                <div class="mb-3">
                    <label for="nombre" class="form-label">Nombre</label>
                    <input type="text" class="form-control" id="nombre" name="nombre" required>
                </div>
                <div class="mb-3">
                    <label for="descripcion" class="form-label">Descripción</label>
                    <textarea type="text" class="form-control" id="descripcion" name="descripcion"></textarea>
                </div>
                <div class="mb-3">
                    <label for="deporte_id" class="form-label">Deporte</label>

                    <select class="form-control" id="deporte_id" name="deporte_id" required>
                        <option value="">Seleccione un deporte</option>
                        <?php foreach ($deportes as $deporte) : ?>
                            <option value="<?php echo $deporte['id']; ?>"><?php echo htmlspecialchars($deporte['nombre']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="nombre" class="form-label">Inicio</label>
                    <input type="date" class="form-control" id="fecha_ini" name="fecha_ini" requerid>
                </div>
                <div class="mb-3">
                    <label for="nombre" class="form-label">Fin</label>
                    <input type="date" class="form-control" id="fecha_f" name="fecha_f" requerid>
                </div>
                <div class="mb-3">
                    <label for="imagen" class="form-label">Imagen principal</label>
                    <input type="file" class="form-control" id="imagen" name="imagen" requerid></textarea>
                </div>

                <button type="submit" class="btn btn-primary">Publicar evento</button>
            </form>
        </div>
        <script>
            function validarCamposEvento() {
                // Validación de selección de tipo
                var deporteSeleccionado = document.getElementById("deporte_id").value;
                if (deporteSeleccionado === "") {
                    alert("Por favor, seleccione un deporte");
                    return false;
                }
                var nombreEvento = document.getElementById("nombre").value;
                if (nombreEvento === "") {
                    alert("El evento debe tener un nombre");
                    return false;
                }

                // Validación de la fecha de inicio
                var fechaInicio = document.getElementById("fecha_ini").value;
                var fechaInicioArray = fechaInicio.split("-");
                if (fechaInicioArray.length !== 3) {
                    alert("Por favor, introduzca una fecha de inicio válida.");
                    return false;
                }
                var yearInicio = fechaInicioArray[0];
                var monthInicio = fechaInicioArray[1];
                var dayInicio = fechaInicioArray[2];

                // Verificar si el año tiene 4 dígitos
                if (yearInicio.length !== 4 || isNaN(yearInicio)) {
                    alert("Por favor, introduzca un año entre 0001 y 9999 en la fecha de inicio.");
                    return false;
                }

                // Crear objeto de fecha de inicio y verificar si es válida
                var fechaInicioObjeto = new Date(yearInicio, monthInicio - 1, dayInicio);
                if (isNaN(fechaInicioObjeto.getTime())) {
                    alert("Por favor, introduzca una fecha de inicio válida.");
                    return false;
                }

                // Validación de la fecha de finalización
                var fechaFin = document.getElementById("fecha_f").value;
                var fechaFinArray = fechaFin.split("-");
                if (fechaFinArray.length !== 3) {
                    alert("Por favor, introduzca una fecha de finalización válida.");
                    return false;
                }
                var yearFin = fechaFinArray[0];
                var monthFin = fechaFinArray[1];
                var dayFin = fechaFinArray[2];

                // Verificar si el año tiene 4 dígitos
                if (yearFin.length !== 4 || isNaN(yearFin)) {
                    alert("Por favor, introduzca un año entre 0001 y 9999 en la fecha de finalización.");
                    return false;
                }

                // Crear objeto de fecha de finalización y verificar si es válida
                var fechaFinObjeto = new Date(yearFin, monthFin - 1, dayFin);
                if (isNaN(fechaFinObjeto.getTime())) {
                    alert("Por favor, introduzca una fecha de finalización válida.");
                    return false;
                }

                // Validación de la fecha de inicio
                var fechaInicio = document.getElementById("fecha_ini").value;
                var fechaInicioObjeto = new Date(fechaInicio);
                if (isNaN(fechaInicioObjeto.getTime())) {
                    alert("Por favor, introduzca una fecha de inicio válida.");
                    return false;
                }

                // Validación de la fecha de finalización
                var fechaFin = document.getElementById("fecha_f").value;
                var fechaFinObjeto = new Date(fechaFin);
                if (isNaN(fechaFinObjeto.getTime())) {
                    alert("Por favor, introduzca una fecha de finalización válida.");
                    return false;
                }

                // Verificar que la fecha de finalización no sea menor que la fecha de inicio
                if (fechaFinObjeto < fechaInicioObjeto) {
                    alert("La fecha de finalización no puede ser menor que la fecha de inicio.");
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
        header("Location: /Ayudantias-1/public/index.php");
        exit();
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
include '../includes/footer.php'; ?>