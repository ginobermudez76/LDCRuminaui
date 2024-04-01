<?php
session_start();
include '../includes/config.php';

if (!isset($_SESSION['usuario_admin'])) {
    echo "<script>window.location.href='../admin/login.php';</script>";
    exit();
}
?>
<div class="container mt-4">
    <div class="row justify-content-center align-items-center min-vh-100">
        <div class="col-md-4">
            <h2>Cambiar contraseña</h2>
            <form name="cambiarpass" id="cambiarpass" method="post" enctype="multipart/form-data" onsubmit="return validarContraseña()">
                <!-- Campos del formulario -->
                <div class="mb-3">
                    <input type="password" class="form-control" id="pass" name="pass" placeholder="Contraseña actual" required>
                    <!-- Campo para la nueva contraseña -->
                    <div class="input-group-append">
                        <button class="btn btn-outline-secondary" type="button" id="showPassButton">Mostrar</button>
                    </div>
                </div>
                <div class="mb-3">
                    <input type="password" class="form-control" id="newpass" name="newpass" placeholder="Nueva contraseña">
                    <!-- Campo para la nueva contraseña -->
                    <div class="input-group-append">
                        <button class="btn btn-outline-secondary" type="button" id="showNewPassButton">Mostrar</button>
                    </div>
                </div>
                <div class="mb-3">
                    <input type="password" class="form-control" id="repitpass" name="repitpass" placeholder="Repita su nueva contraseña" required>
                    <!-- Campo para repetir la nueva contraseña -->
                    <div class="input-group-append">
                        <button class="btn btn-outline-secondary" type="button" id="showRepitPassButton">Mostrar</button>
                    </div>
                </div>

                <button type="submit" class="btn btn-danger" name="changePass" id="changePass">Cambiar contraseña</button>

            </form>
        </div>

    </div>
</div>

<script>
    function validarContraseña() {

        var newpass = document.getElementById("newpass").value;
        var repitpass = document.getElementById("repitpass").value;
        if (newpass != repitpass) {
            alert("Las contraseñas no coinciden.");
            return false;
        }
        return true;

    }
    $(document).ready(function() {
        $('#showNewPassButton').click(function() {
            var newpassInput = $('#newpass');
            var type = newpassInput.attr('type');
            if (type === 'password') {
                newpassInput.attr('type', 'text');
                $(this).text('Ocultar');
            } else {
                newpassInput.attr('type', 'password');
                $(this).text('Mostrar');
            }
        });

        $('#showRepitPassButton').click(function() {
            var repitpassInput = $('#repitpass');
            var type = repitpassInput.attr('type');
            if (type === 'password') {
                repitpassInput.attr('type', 'text');
                $(this).text('Ocultar');
            } else {
                repitpassInput.attr('type', 'password');
                $(this).text('Mostrar');
            }
        });
        $('#showPassButton').click(function() {
            var repitpassInput = $('#pass');
            var type = passInput.attr('type');
            if (type === 'password') {
                passInput.attr('type', 'text');
                $(this).text('Ocultar');
            } else {
                passInput.attr('type', 'password');
                $(this).text('Mostrar');
            }
        });
    });
</script>
<script>
    $('#changePass').click(function() {
        var formData = new FormData($('#cambiarpass')[0]);
        $.ajax({
            url: 'changepass.php',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                var jsonData = JSON.parse(response);
                if (jsonData.success) {
                    // Mostrar mensaje de éxito
                    alert(jsonData.message);
                    if (jsonData.redirect) {
                        // Redireccionar a logout.php
                        window.location.href = 'logout.php';
                    }
                    $("#cambiarpass")[0].reset(); // También puedes restablecer el formulario aquí si lo deseas
                } else {
                    // Mostrar mensaje de error
                    alert(jsonData.message);
                }
            }

        });


    });
</script>