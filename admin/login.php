<?php
include '../includes/config.php';
include '../includes/header.php'; // Incluyendo la cabecera común
?>

<div class="container">
    <div class="row justify-content-center align-items-center min-vh-100">
        <div class="col-md-4">
            <?php if (isset($_SESSION['error_login'])) { ?>
                <div class="alert alert-danger">
                    <?php echo $_SESSION['error_login']; ?>
                </div>
                <?php unset($_SESSION['error_login']); ?>
            <?php } ?>            
            <div class="card">
                <div class="card-header text-center">
                    <h3 class="gestionar">Iniciar Sesión</h3>
                </div>
                <div class="card-body">
                    <form action="processLogin.php" method="post">
                        <div class="mb-3">
                            <label for="username" class="form-label"><h6>Nombre de Usuario</h6></label>
                            <input type="text" class="form-control" id="username" name="nombre_usuario" required>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label"><h6>Contraseña</h6></label>
                            <input type="password" class="form-control" id="password" name="contrasena" required>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Iniciar Sesión</button>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<?php include '../includes/footer.php'; ?>


