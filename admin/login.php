<?php
include '../includes/config.php';
include '../includes/header.php'; // Incluyendo la cabecera común
?>
<div class="iniciar-sesion">
    <div class="contenedor-sesion">
            <?php if (isset($_SESSION['error_login'])) {?>
                <div class="alert alert-danger">
                    <?php echo $_SESSION['error_login']; ?>
                </div>
                <?php unset($_SESSION['error_login']); ?>
            <?php } ?>            
            <div class="card custom-card">
                <div class="card-header text-center">
                    <h3 class="gestionar">Iniciar Sesión</h3>
                </div>
                <div class="card-body">
                    <form action="processLogin.php" method="post">
                        <div class="mb-3">
                            <label for="username" class="form-label" style="color:white;"><h6>Nombre de Usuario</h6></label>
                            <input type="text" class="form-control" id="username" name="nombre_usuario" required>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label" style="color:white;"><h6>Contraseña</h6></label>
                            <input type="password" class="form-control" id="password" name="contrasena" required>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Iniciar Sesión</button>
                    </form>
                </div>

        </div>

</div>
</div>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<?php include '../includes/footer.php'; ?>


