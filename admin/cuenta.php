<?php
include '../includes/config.php';
include '../includes/header.php';
if (!isset($_SESSION['usuario_admin'])) {
    header("Location: ../admin/login.php");
    exit();
}
$usuario_id = $_SESSION['usuario_id'];
// Llamar al procedimiento almacenado para obtener del usuario
try {
    $stmt = $conn->prepare("CALL info_usuario(:usuario_id)");
    $stmt->bindParam(':usuario_id', $usuario_id, PDO::PARAM_INT);
    $stmt->execute();
    // Recuperar un solo registro
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
try {
    $stmt = $conn->prepare("SELECT * FROM usuarios WHERE id =(:usuario_id)");
    $stmt->bindParam(':usuario_id', $usuario_id, PDO::PARAM_INT);
    $stmt->execute();
    // Recuperar un solo registro
    $usuario2 = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
// Procesar la nueva imagen si se proporciona
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = $_POST['nombre'];
    $snombre = $_POST['snombre'];
    $apellido = $_POST['apellido'];
    $sapellido = $_POST['sapellido'];
    $cedula = $_POST['cedula'];
    $celular = $_POST['celular'];
    $email = $_POST['mail'];
    $cumple = $_POST['fecha_n'];

    // Actualizar la base de datos con la nueva imagen o la imagen anterior
    $stmt = $conn->prepare("UPDATE usuarios SET primer_nombre=?, segundo_nombre=?, primer_apellido=?, segundo_apellido=?, cedula=?, celular=?, correo=?, fecha_nac=? WHERE id=$usuario_id");
    $stmt->execute([$nombre, $snombre, $apellido, $sapellido, $cedula, $celular, $email, $cumple]);

    header("refresh:2;url=cuenta.php");
    exit();
}
?>

<div class='info'>
    <h2>Información personal</h2>
    <label>Nombre: <?php echo htmlspecialchars($usuario['persona']); ?></label><br>
    <label>Rol: <?php echo htmlspecialchars($usuario['rol']); ?></label><br>
    <label>No. cédula: <?php echo htmlspecialchars($usuario['cedula']); ?></label><br>
    <label>Celular: <?php echo htmlspecialchars($usuario['celular']); ?></label><br>
    <label>E-mail: <?php echo htmlspecialchars($usuario['correo']); ?></label><br>
    <label>Nombre de usuario: <?php echo htmlspecialchars($usuario['usuario']); ?></label><br>
    <label>Cumpleaños: <?php echo htmlspecialchars($usuario['cumpleanos']); ?></label><br>
</div>
<div class="container mt-4">
    <div class="row justify-content-center align-items-center min-vh-100">
        <div class="col-md-4">
            <h2>Editar información</h2>

            <?php if (isset($mensaje)) : ?>
                <div class="alert alert-danger" role="alert">
                    <?php echo $mensaje; ?>
                </div>
            <?php endif; ?>

            <form name="formregistraruser" id="formregistraruser" action="cuenta.php" method="post" enctype="multipart/form-data" onsubmit="return validarFormulario()">
                <!-- Campos del formulario -->
                <div class="mb-3">
                    <input type="text" class="form-control" id="nombre" name="nombre" placeholder="Nombre" value="<?php echo htmlspecialchars($usuario2['primer_nombre']) ?>" required>
                </div>
                <div class="mb-3">
                    <input type="text" class="form-control" id="snombre" name="snombre" value="<?php echo htmlspecialchars($usuario2['segundo_nombre']) ?>" placeholder="Segundo nombre">
                </div>
                <div class="mb-3">
                    <input type="text" class="form-control" id="apellido" name="apellido" placeholder="Apellido" value="<?php echo htmlspecialchars($usuario2['primer_apellido']) ?>" required>
                </div>
                <div class="mb-3">
                    <input type="text" class="form-control" id="sapellido" name="sapellido" placeholder="Segundo apellido" value="<?php echo htmlspecialchars($usuario2['segundo_apellido']) ?>">
                </div>
                <div class="mb-3">
                    <input type="text" class="form-control" id="cedula" name="cedula" placeholder="Cédula" maxlength="10" required value="<?php echo htmlspecialchars($usuario2['cedula']) ?>">
                </div>
                <div class="mb-3">
                    <input type="text" class="form-control" id="celular" name="celular" placeholder="Celular" maxlength="10" value="<?php echo htmlspecialchars($usuario2['celular']) ?>">
                </div>
                <div class="mb-3">
                    <input type="email" class="form-control" id="mail" name="mail" placeholder="Correo electrónico" value="<?php echo htmlspecialchars($usuario2['correo']) ?>">
                </div>
                <div class="mb-3">
                    <input type="date" class="form-control" id="fecha_n" name="fecha_n" value="<?php echo htmlspecialchars($usuario2['fecha_nac']) ?>" required>
                </div>
                <button type="submit" class="btn btn-danger" name="registrar" id="registrar">Guardar cambios</button>

            </form>
        </div>

    </div>
</div>
<script>
    function validarFormulario() {
        // Validación del campo de celular
        var celular = document.getElementById("celular").value;
        if (!/^\d{10}$/.test(celular)) {
            alert("Por favor, introduzca un número de celular válido de 10 dígitos.");
            return false;
        }
        // Validación del campo de cédula
        var cedula = document.getElementById("cedula").value;
        if (!/^\d{10}$/.test(cedula)) {
            alert("Por favor, introduzca un número de cédula válido de 10 dígitos.");
            return false;
        }

        // Validación del campo de correo electrónico
        var correo = document.getElementById("mail").value;
        var expresionCorreo = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!expresionCorreo.test(correo)) {
            alert("Por favor, introduzca una dirección de correo electrónico válida.");
            return false;
        }

        // Validación de la fecha de nacimiento
        var fecha = document.getElementById("fecha_n").value;
        var fechaArray = fecha.split("-");
        if (fechaArray.length !== 3) {
            alert("Por favor, introduzca una fecha de nacimiento válida.");
            return false;
        }
        var year = fechaArray[0];
        var month = fechaArray[1];
        var day = fechaArray[2];

        // Verificar si el año tiene 4 dígitos
        if (year.length !== 4 || isNaN(year)) {
            alert("Por favor, introduzca un año entre 0001 y 9999.");
            return false;
        }

        // Crear un objeto de fecha y verificar si es válida
        var dateObject = new Date(year, month - 1, day); // Month is 0-based
        if (isNaN(dateObject.getTime())) {
            alert("Por favor, introduzca una fecha de nacimiento válida.");
            return false;
        }
        // Todas las validaciones pasaron, devolvemos true
        return true;
    }
    // Función para limitar la cantidad de dígitos en el campo de cedula
    document.getElementById('cedula').addEventListener('input', function() {
        // Obtener el valor actual del campo de cedula
        var cedula = this.value;
        // Limitar el valor a 10 caracteres
        if (cedula.length > 10) {
            this.value = cedula.slice(0, 10);
        }
    });
    document.getElementById('nombre').addEventListener('input', function() {
        // Obtener el valor actual del campo de nombre
        var nombre = this.value;
        // Limitar el valor a 45 caracteres
        if (nombre.length > 45) {
            this.value = nombre.slice(0, 45);
        }
    });
    document.getElementById('snombre').addEventListener('input', function() {
        // Obtener el valor actual del campo de snombre
        var snombre = this.value;
        // Limitar el valor a 45 caracteres
        if (snombre.length > 45) {
            this.value = snombre.slice(0, 45);
        }
    });
    document.getElementById('apellido').addEventListener('input', function() {
        // Obtener el valor actual del campo de apellido
        var apellido = this.value;
        // Limitar el valor a 45 caracteres
        if (apellido.length > 45) {
            this.value = apellido.slice(0, 45);
        }
    });
    document.getElementById('sapellido').addEventListener('input', function() {
        // Obtener el valor actual del campo de cedula
        var sapellido = this.value;
        // Limitar el valor a 45 caracteres
        if (sapellido.length > 45) {
            this.value = sapellido.slice(0, 45);
        }
    });
    document.getElementById('mail').addEventListener('input', function() {
        // Obtener el valor actual del campo de cedula
        var mail = this.value;
        // Limitar el valor a 45 caracteres
        if (mail.length > 100) {
            this.value = mail.slice(0, 100);
        }
    });
</script>
<?php
include '../includes/footer.php'
?>