<?php
include '../includes/config.php'; // Incluyendo la conexión a la base de datos

try {
    $stmt = $conn->prepare("SELECT nombre FROM deportes");
    $stmt->execute();

    $deportes = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}

?>

<nav class="navbar navbar-expand-lg bg-light navbar-light">
    <div class="container-fluid">
        <a class="navbar-brand" href="../public/index.php"  style="max-width: 500px; overflow: hidden; text-overflow: ellipsis;">
            <img src="../img/logo.png" alt="Logo" class="rounded-circle" width="65" height="65">
            Liga Deportiva Cantonal de Rumiñahui
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item dropdown">

                <?php


                // Verificar si el usuario está autenticado
                if (isset($_SESSION['usuario_admin'])) {
                ?>
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="../admin/tbsolicitud.php">Solicitudes</a>
                    </li>

                    <?php
                    // El usuario está autenticado, acceder al ID del usuario
                    $usuario_id = $_SESSION['usuario_id'];

                    // Verificar si el usuario tiene el rol de publicista

                    try {
                        // Consultar el rol del usuario en la base de datos
                        $stmt = $conn->prepare("SELECT rol FROM usuarios WHERE id = :usuario_id");
                        $stmt->bindParam(':usuario_id', $usuario_id, PDO::PARAM_INT);
                        $stmt->execute();

                        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
                        // Verificar si el usuario tiene el rol de Publicista
                        if ($usuario['rol'] == 8) {
                            // Mostrar el elemento del menú de publicista
                        ?>
                            <li class="nav-item">
                                <a class="nav-link active" aria-current="page" href="../admin/register.php">Registrar usuarios</a>
                            </li>
                        <?php
                        }
                        // Verificar si el usuario tiene el rol de Publicista
                        if ($usuario['rol'] == 7) {
                            // Mostrar el elemento del menú de publicista
                        ?>
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    Publicar
                                </a>
                                <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                                    <a class="nav-link active" aria-current="page" href="../publicista/gestionar_eventos.php">Eventos</a>
                                    <a class="nav-link active" aria-current="page" href="../publicista/gestionar_deportes.php">Deportes</a>
                                    <a class="nav-link active" aria-current="page" href="../publicista/carta_de_condolencias.php">Carta de condolencias</a>
                                </div>
                            </li>
                        <?php
                        }
                        if ($usuario['rol'] == 4 || $usuario['rol'] == 3 || $usuario['rol'] == 2 || $usuario['rol'] == 1) {
                        ?>
                            <li class="nav-item">
                                <a class="nav-link active" aria-current="page" href="../admin/vsolicitudencargado.php">Solicitudes asignadas</a>
                            </li>
                <?php
                        }
                    } catch (PDOException $e) {
                        echo "Error: " . $e->getMessage();
                    }
                }


                ?>
            </ul>
            <ul class="navbar-nav ms-auto">
                <!-- El enlace a continuación es para la función de inicio/cierre de sesión que puede cambiar dependiendo del estado de la sesión -->
                <li class="nav-item">

                    <?php if (isset($_SESSION['usuario_admin'])) : ?>
                        <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    Cuenta
                                </a>
                                <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                                    <a class="nav-link active" aria-current="page" href="../admin/cuenta.php">Perfil</a>
                                    <a class="nav-link" href="../admin/logout.php">Cerrar Sesión</a>
                                </div>
                            </li>
                    <?php endif; ?>
                </li>
                <?php if (!isset($_SESSION['usuario_admin'])) { ?>
                    <li class="nav-item">
                    <a class="nav-link" href="../admin/login.php">Iniciar sesión</a>
                    </li>
                <?php } ?>
            </ul>

            </ul>
        </div>
    </div>
</nav>