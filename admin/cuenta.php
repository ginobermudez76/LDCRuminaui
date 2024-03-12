<?php
include '../includes/config.php';
include '../includes/header.php';
if (!isset($_SESSION['usuario_admin'])) {
    header("Location: ../admin/login.php");
    exit();
}
?>
<div class="container">
    <div class="row" id="usuarioInfo">

    </div>


    <div class="row" id="editUser">

    </div>
    <div id="editPass">

    </div>
</div>

<script>
    $(document).ready(function() {
        $("#usuarioInfo").load("infoUsuario.php");
    });

    $(document).ready(function() {
        $("#editUser").load("formEditUser.php");
    });

    $(document).ready(function() {
        $("#editPass").load("formEditPass.php");
    });
</script>

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