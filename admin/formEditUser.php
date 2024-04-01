<?php
session_start();
include '../includes/config.php';

if (!isset($_SESSION['usuario_admin'])) {
    echo "<script>window.location.href='../admin/login.php';</script>";
    exit();
}
$usuario_id = $_SESSION['usuario_id'];


try {
    $stmt = $conn->prepare("SELECT * FROM usuarios WHERE id =(:usuario_id)");
    $stmt->bindParam(':usuario_id', $usuario_id, PDO::PARAM_INT);
    $stmt->execute();
    // Recuperar un solo registro
    $usuario2 = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
<div class="edit">
<div class="row justify-content-center align-items-center min-vh-100">
        <div class="col-md-4">
            <h2>Editar información</h2>

            <?php if (isset($mensaje)) : ?>
                <div class="alert alert-danger" role="alert">
                    <?php echo $mensaje; ?>
                </div>
            <?php endif; ?>

            <form name="formEditarUser" id="formregistraruser" action="editarInfo.php" method="post" enctype="multipart/form-data" onsubmit="return validarFormulario()">
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
