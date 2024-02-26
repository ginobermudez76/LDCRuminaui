<?php
include '../includes/config.php'; // Incluyendo la conexión de la base de datos
include '../includes/header.php'; // Incluyendo la cabecera común

if (!isset($_SESSION['usuario_admin'])) {
    header("Location: /Ayudantias-1/admin/login.php");
    exit();
}
$usuario_id = $_SESSION['usuario_id'];

try {
    // Consultar el rol del usuario en la base de datos
    $stmt = $conn->prepare("SELECT rol FROM usuarios WHERE id = :usuario_id");
    $stmt->bindParam(':usuario_id', $usuario_id, PDO::PARAM_INT);
    $stmt->execute();

    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    // Verificar si el usuario tiene el rol de Administrador
    if ($usuario['rol'] == 8) {
        // Mostrar el elemento del menú Administrar


        try {
            $stmt = $conn->prepare("SELECT id_rol, rol_name FROM roles");
            $stmt->execute();
            $roles = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            // Obtener los datos del formulario
            $nombre = $_POST['nombre'];
            $snombre = $_POST['snombre'];
            $apellido = $_POST['apellido'];
            $sapellido = $_POST['sapellido'];
            $cedula = $_POST['cedula'];
            $celular = $_POST['celular'];
            $correo = $_POST['mail'];
            $user = $_POST['username'];
            $pass = $_POST['contrasena'];
            $rol = $_POST['rolid'];
            $fechanac = $_POST['fecha_n'];



            try {
                $hash = password_hash($pass, PASSWORD_DEFAULT);
                $stmt = $conn->prepare("INSERT INTO usuarios (primer_nombre, segundo_nombre, primer_apellido, segundo_apellido, cedula, celular, correo, nombre_usuario, contrasena, rol, fecha_nac ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([$nombre, $snombre, $apellido, $sapellido, $cedula, $celular, $correo, $user, $hash, $rol, $fechanac]);

                // Redirigir después de agregar
                header("Location: " . $_SERVER['PHP_SELF']);
                exit();
            } catch (PDOException $e) {
                $mensaje = "Error: " . $e->getMessage();
            }
        }
?>

        <div class="container">
            <div class="row justify-content-center align-items-center min-vh-100">
                <div class="col-md-4">
                    <h2 class="gestionar">Registro de Usuario</h2>

                    <?php if (isset($mensaje)) : ?>
                        <div class="alert alert-danger" role="alert">
                            <?php echo $mensaje; ?>
                        </div>
                    <?php endif; ?>

                    <form name="formregistraruser" id="formregistraruser" action="register.php" method="post" enctype="multipart/form-data" onsubmit="return validarFormulario()">
                        <!-- Campos del formulario -->
                        <div class="mb-3">
                            <input type="text" class="form-control" id="nombre" name="nombre" placeholder="Nombre" required>
                        </div>

                        <div class="mb-3">
                            <input type="text" class="form-control" id="snombre" name="snombre" placeholder="Segundo nombre">
                        </div>

                        <div class="mb-3">
                            <input type="text" class="form-control" id="apellido" name="apellido" placeholder="Apellido" required>
                        </div>
                        <div class="mb-3">
                            <input type="text" class="form-control" id="sapellido" name="sapellido" placeholder="Segundo apellido">
                        </div>

                        <div class="mb-3">
                            <input type="text" class="form-control" id="cedula" name="cedula" placeholder="Cédula" maxlength="10" required>
                        </div>

                        <div class="mb-3">
                            <input type="text" class="form-control" id="celular" name="celular" placeholder="Celular" maxlength="10">
                        </div>

                        <div class="mb-3">
                            <input type="email" class="form-control" id="mail" name="mail" placeholder="Correo electrónico">
                        </div>
                        <div class="mb-3">
                            <select class="form-control" id="rolid" name="rolid" required>
                                <option value="">Seleccione un rol</option> <!-- Opción predeterminada -->
                                <?php foreach ($roles as $roles) : ?>
                                    <option value="<?php echo $roles['id_rol']; ?>"><?php echo htmlspecialchars($roles['rol_name']); ?></option>
                                <?php endforeach; ?>
                            </select>

                        </div>

                        <div class="mb-3">
                            <input type="date" class="form-control" id="fecha_n" name="fecha_n" required>
                        </div>

                        <div class="mb-3">
                            <input type="text" class="form-control" id="username" name="username" placeholder="Usuario" required>

                        </div>

                        <div class="mb-3">
                            <input type="text" class="form-control" id="contrasena" name="contrasena" placeholder="Contraseña" required>
                        </div>

                        <button type="submit" class="btn btn-danger" name="registrar" id="registrar">Registrar</button>

                    </form>
                </div>
            </div>
        </div>

        <script>
            function actualizarCampos() {
                var nombre = document.getElementById("nombre").value.toLowerCase().normalize("NFD").replace(/[\u0300-\u036f]/g, '').replace(/[^a-z]/g, '');
                var apellido = document.getElementById("apellido").value.toLowerCase().normalize("NFD").replace(/[\u0300-\u036f]/g, '').replace(/[^a-z]/g, '');
                var cedula = document.getElementById("cedula").value.slice(-4);
                var contrasena = apellido.charAt(0).toUpperCase() + apellido.slice(1) + cedula + "@" + new Date().getFullYear();
                var username = nombre + "." + apellido + cedula + "@ldcruminahui.com";

                document.getElementById("username").value = username;
                document.getElementById("contrasena").value = contrasena;
            }

            document.getElementById("nombre").addEventListener("input", actualizarCampos);
            document.getElementById("apellido").addEventListener("input", actualizarCampos);
            document.getElementById("cedula").addEventListener("input", actualizarCampos);

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

                // Validación de selección de rol
                var rolSeleccionado = document.getElementById("rolid").value;
                if (rolSeleccionado === "") {
                    alert("Por favor, seleccione un rol.");
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

            // Función para limitar la cantidad de dígitos en el campo de celular
            document.getElementById('celular').addEventListener('input', function() {
                // Obtener el valor actual del campo de celular
                var celular = this.value;
                // Limitar el valor a 10 caracteres
                if (celular.length > 10) {
                    this.value = celular.slice(0, 10);
                }
            });

            // Función para limitar la cantidad de dígitos en el campo de cedula
            document.getElementById('cedula').addEventListener('input', function() {
                // Obtener el valor actual del campo de cedula
                var cedula = this.value;
                // Limitar el valor a 10 caracteres
                if (cedula.length > 10) {
                    this.value = cedula.slice(0, 10);
                }
            });
            document.getElementById("formregistraruser").addEventListener("submit", function(event) {
                event.preventDefault(); // Evitar el envío del formulario por defecto

                // Validar el formulario
                if (validarFormulario()) {
                    // Obtener el nombre de usuario del formulario
                    var username = document.getElementById("username").value;

                    // Realizar una petición AJAX para verificar si el nombre de usuario ya existe
                    var xhr = new XMLHttpRequest();
                    xhr.open("POST", "verificar_usuario.php", true);
                    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
                    xhr.onreadystatechange = function() {
                        if (xhr.readyState == 4 && xhr.status == 200) {
                            var response = xhr.responseText;
                            if (response === "existe") {
                                alert("El nombre de usuario ya está en uso. Por favor, elige otro.");
                            } else {
                                // Si el nombre de usuario no existe, enviar el formulario
                                document.getElementById("formregistraruser").submit();
                            }
                        }
                    };
                    xhr.send("username=" + username);
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