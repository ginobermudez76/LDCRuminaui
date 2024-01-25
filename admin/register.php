<?php
include '../includes/config.php'; // Incluyendo la conexión de la base de datos
include '../includes/header.php'; // Incluyendo la cabecera común

// Definir variables e inicializar con valores vacíos
$primer_nombre = $segundo_nombre = $primer_apellido = $segundo_apellido = $cedula = $celular = $correo = $usuario = $contrasena = "";
$cedula_err = "";

// Procesar datos del formulario cuando se envía el formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validar cédula
    if (empty(trim($_POST["cedula"]))) {
        $cedula_err = "Por favor ingrese la cédula.";
    } else {
        // Preparar una declaración de selección
        $sql = "SELECT id FROM usuarios WHERE cedula = :cedula";
        if ($stmt = $conn->prepare($sql)) {
            // Establecer parámetros
            $stmt->bindParam(':cedula', $_POST["cedula"], PDO::PARAM_STR);

            // Intentar ejecutar la declaración preparada
            if ($stmt->execute()) {
                /* almacenar resultado */
                $result = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($result) {
                    $cedula_err = "Esta cédula ya está registrada.";
                } else {
                    $cedula = trim($_POST["cedula"]);
                }
            } else {
                echo "Al parecer algo salió mal.";
            }
        }

        // Cerrar declaración
        $stmt = null;
    }
   // Si la cédula no está repetida, procede a generar usuario y contraseña
   if (empty($cedula_err)) {
    // Generar nombre de usuario aleatorio
    $usuario = generarNombreUsuario($primer_nombre, $segundo_nombre, $primer_apellido, $segundo_apellido);

    // Generar contraseña
    $contrasena = generarContrasena($primer_apellido, substr($cedula, -4), date("Y"));


    // Mostrar el usuario y contraseña generados en sus campos correspondientes
    $usuario = htmlspecialchars($usuario);
    $contrasena = htmlspecialchars($contrasena);
}
}

// Función para generar nombre de usuario aleatorio
function generarNombreUsuario($nombre1, $nombre2, $apellido1, $apellido2) {
$nombres = [$nombre1, $nombre2, $apellido1, $apellido2];
shuffle($nombres);
return strtolower(implode("_", $nombres));
}

// Función para generar contraseña
function generarContrasena($apellido, $ultimosCuatroDigitosCedula, $ano) {
return $apellido . $ultimosCuatroDigitosCedula . "@" . $ano;
}
?>

<div class="container">
    <div class="row justify-content-center align-items-center min-vh-100">
        <div class="col-md-4">
            <h2>Registro de Usuario</h2>

            <form action="register.php" method="post" enctype="multipart/form-data">
                <!-- Campos del formulario -->
                <label for="primer_nombre">Primer Nombre:</label>
                <input type="text" id="primer_nombre" name="primer_nombre" required>

                <label for="segundo_nombre">Segundo Nombre:</label>
                <input type="text" id="segundo_nombre" name="segundo_nombre">

                <label for="primer_apellido">Primer Apellido:</label>
                <input type="text" id="primer_apellido" name="primer_apellido" required>

                <label for="segundo_apellido">Segundo Apellido:</label>
                <input type="text" id="segundo_apellido" name="segundo_apellido">

                <label for="cedula">Cédula:</label>
                <input type="text" id="cedula" name="cedula" required>
                <span class="text-danger"><?php echo $cedula_err; ?></span>

                <label for="celular">Número de Celular:</label>
                <input type="tel" id="celular" name="celular" required>

                <label for="correo">Correo Electrónico:</label>
                <input type="email" id="correo" name="correo" required>

                <!-- Botón para generar usuario y contraseña -->
                <input type="submit" value="Generar usuario y contraseña" name="generar">

                <!-- Mostrar usuario y contraseña generados -->
                <?php if (!empty($usuario) && !empty($contrasena)): ?>
                    <div class="mt-3">
                        <strong>Usuario:</strong> <?php echo $usuario; ?><br>
                        <strong>Contraseña:</strong> <?php echo $contrasena; ?>
                    </div>
                <?php endif; ?>

                <!-- Otros campos del formulario -->

                <input  value="Registrar" name="registrar">
            </form>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>