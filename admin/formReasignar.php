<?php
session_start();
include '../includes/config.php'; //incluyendo la conexión de la base de datos


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
    if ($usuario['rol'] == 4 || $usuario['rol'] == 3 || $usuario['rol'] == 2 || $usuario['rol'] == 1 || $usuario['rol'] == 9) {
        //obtener lista de tipo de solicitud
        try {
            $stmt = $conn->prepare("SELECT id_tipo, name_tipo FROM solicitud_tipo");
            $stmt->execute();
            $tipo = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }

        try {
            // Consultar usuarios con el nombre de su rol
            $stmt = $conn->prepare("SELECT u.id as id, u.nombre_usuario as nombre_usuario, r.rol_name AS rol 
                                            FROM usuarios u
                                            LEFT JOIN roles r ON u.rol = r.id_rol
                                            WHERE u.rol <> 5 AND u.id <> :usuario_id");
            $stmt->bindParam(':usuario_id', $usuario_id, PDO::PARAM_INT);
            $stmt->execute();
            $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
?>
        <form action="reasignarSolicitud.php" method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="tipoReasignar" class="form-label"><strong>Tipo</strong></label>
                <!-- Agrega el select de tipos -->
                <select id="tipoReasignar" class="form-select">
                    <option value="">
                        Seleccione un tipo
                    </option>
                    <?php foreach ($tipo as $tiporea) : ?>
                        <?php if ($tiporea['name_tipo'] !== $solicitud['tipo']) : ?>
                            <option value="<?php echo $tiporea['id_tipo']; ?>">
                                <?php echo htmlspecialchars($tiporea['name_tipo']); ?>
                            </option>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="mb-3">
                <label for="reasignarUser" class="form-label">Enviar directamente</label>
                <input type="hidden" id="checkMostrarUsuariosHidden" name="checkMostrarUsuariosHidden" value="0">
            </div>
            <div class="mb-3" id="divUsuarioReasignar" style="display: none;">
                <label for="usuarioReasignar" class="form-label"><strong>Usuario</strong></label>
                <!-- Agrega el select de usuarios -->
                <select id="usuarioReasignar" class="form-select">
                    <option value="">
                        Seleccione un usuario
                    </option>
                    <?php foreach ($usuarios as $usuariosrea) : ?>
                        <option value="<?php echo $usuariosrea['id']; ?>">
                            <?php echo htmlspecialchars($usuariosrea['rol']); ?>: <?php echo htmlspecialchars($usuariosrea['nombre_usuario']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="button" id="btnReasignar" class="btn btn-success"><i class="fa-solid fa-check"></i>Reasignar</button>
            <button id="Cancelar" class="btn btn-danger" data-bs-dismiss="modal"><i class="fa-solid fa-xmark dism"></i>Cancelar</button>
        </form>
<?php

    } else {
        header("Location: ../public/index.php");
        exit();
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
} ?>