<?php

session_start();

include '../includes/config.php';
include '../includes/header.php'; //incluyendo la cabecera común
//SOLO ESTA LA GUI (Interfaz de usuario Front End)
?>

<div class="container">
    <!-- Centrado Vertical y Horizontal -->
    <div class="row justify-content-center align-items-center min-vh-100">
        <div class="col-md-4">
            <!-- Tarjeta de Inicio de Sesión -->
            <div class="card">
                <div class="card-header text-center">
                    <h3 class="gestionar">Solicitud</h3>
                </div>
                <div class="card-body">
                    <!-- Formulario -->
                    <form action="agregar_solicitud.php" method="post">
                        <div class="mb-3">
                            <label for="username" class="form-label">N.ro Solicitud</label>
                            <select name="id">
                                <option value="1">1. Solicitud...</option>
                                <option value="2">2. Solicitud...</option>
                                <option value="3">3. Solicitud...</option>
                                <option value="4">4. Solicitud...</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="descripcion" class="form-label">Descripcion</label>
                            <input type="textarea" class="form-control" id="descripcion" name="descripcion" readonly>
                        </div>

                        <div class="mb-3">
                            <label for="edad" class="form-label">Edad</label>
                            <input type="text" list="numeros">
                            <datalist id="numeros">
                                <option value="1">1</option>
                                <option value="2">2</option>
                                ...
                                <option value="100">100</option>
                            </datalist>
                        </div>

                        <div class="mb-3">
                            <label for="monto" class="form-label">Monto</label>
                            <input type="text" class="form-control" id="monto" name="monto" readonly>
                        </div>

                        <button type="submit" class="btn btn-primary w-100">Enviar</button>

                    </form>
                </div>
            </div>
            <!-- Mensaje de Error si es necesario -->
            <?php if (!empty($error_login)) { ?>
                <div class="alert alert-danger">
                    <?php echo $error_login; ?>
                </div>
            <?php } ?>
        </div>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<?php include '../includes/footer.php'; ?>