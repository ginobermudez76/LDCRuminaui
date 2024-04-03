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

<nav class="navbar navbar-expand-lg navbar-light">
    <div class="container-fluid">
        <a href="../public/index.php">
            <img class="logo" src="../img/logoX_LDCR.png" alt="Logo" class="rounded-circle" width="65" height="65">
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0 pattaya-regular">
                <li class="nav-item">
                    <a class="nav-link" href="">Inicio</a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        Nosotros
                    </a>
                    <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                        <a class="nav-link active" aria-current="page" href="#">Historia</a>
                        <a class="nav-link active" aria-current="page" href="#">Misión</a>
                        <a class="nav-link active" aria-current="page" href="#">Visión</a>
                        <a class="nav-link active" aria-current="page" href="#">Directorio</a>
                    </div>
                </li>

                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        Servicios
                    </a>
                    <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                        <a class="nav-link active" aria-current="page" href="#">Escuelas permanentes</a>
                        <a class="nav-link active" aria-current="page" href="../public/escenariosDeportivos.php">Escenarios deportivos</a>
                        <a class="nav-link active" aria-current="page" href="../public/documentosDisponibles.php">Descarga de documentos</a>
                        <a class="nav-link active" aria-current="page" href="#">Cursos vacacionales</a>
                    </div>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="../public/noticias.php">Noticias</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="">Eventos</a>
                </li>
            </ul>
            <ul class="navbar-nav ms-auto pattaya-regular">


                <?php

                // Verificar si el usuario está autenticado
                if (isset($_SESSION['usuario_admin'])) {
                ?>
                    <li class="nav-item">
                        <a class="nav-link" aria-current="page" href="../admin/solicitudes.php">Solicitudes</a>
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
                                <a class="nav-link" aria-current="page" href="../admin/register.php">Registrar usuarios</a>
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
                                    <a class="nav-link active" aria-current="page" href="../publicista/eventos.php">Eventos</a>
                                    <a class="nav-link active" aria-current="page" href="../publicista/deportes.php">Deportes</a>
                                    <a class="nav-link active" aria-current="page" href="../publicista/deportistas_destacados.php">Deportistas destacados</a>
                                    <a class="nav-link active" aria-current="page" href="../publicista/logros.php">Logros</a>
                                    <a class="nav-link active" aria-current="page" href="../publicista/noticias.php">Noticias</a>
                                    <a class="nav-link active" aria-current="page" href="../publicista/carta_de_condolencias.php">Carta de condolencias</a>
                                    <a class="nav-link active" aria-current="page" href="../publicista/documentos.php">Formatos de documento</a>
                                    <a class="nav-link active" aria-current="page" href="../publicista/escenarios.php">Escenarios</a>
                                </div>
                            </li>
                        <?php
                        }
                        if ($usuario['rol'] == 4 || $usuario['rol'] == 3 || $usuario['rol'] == 2 || $usuario['rol'] == 1 || $usuario['rol'] == 9) {
                        ?>
                            <li class="nav-item">
                                <a class="nav-link" aria-current="page" href="../admin/vsolicitudencargado.php">Solicitudes asignadas</a>
                            </li>
                <?php
                        }
                    } catch (PDOException $e) {
                        echo "Error: " . $e->getMessage();
                    }
                }
                ?>

                <!-- El enlace a continuación es para la función de inicio/cierre de sesión que puede cambiar dependiendo del estado de la sesión -->
                <li class="nav-item">
                    <?php if (isset($_SESSION['usuario_admin'])) : ?>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle sesion logout" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <svg class="icon" version="1.1" id="Capa_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" width="510px" height="510px" viewBox="0 0 510 510" style="enable-background:new 0 0 510 510;" xml:space="preserve">
                            <g>
                                <g id="account-circle">
                                    <path d="M255,0C114.75,0,0,114.75,0,255s114.75,255,255,255s255-114.75,255-255S395.25,0,255,0z M255,76.5
                                                    c43.35,0,76.5,33.15,76.5,76.5s-33.15,76.5-76.5,76.5c-43.35,0-76.5-33.15-76.5-76.5S211.65,76.5,255,76.5z M255,438.6
                                                    c-63.75,0-119.85-33.149-153-81.6c0-51,102-79.05,153-79.05S408,306,408,357C374.85,405.45,318.75,438.6,255,438.6z" />
                                </g>
                            </g>
                        </svg>
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
                    <a class="nav-link sesion login" href="../admin/login.php">
                        <svg class="icon" version="1.1" id="Capa_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" width="510px" height="510px" viewBox="0 0 510 510" style="enable-background:new 0 0 510 510;" xml:space="preserve">
                            <g>
                                <g id="account-circle">
                                    <path d="M255,0C114.75,0,0,114.75,0,255s114.75,255,255,255s255-114.75,255-255S395.25,0,255,0z M255,76.5
                                            c43.35,0,76.5,33.15,76.5,76.5s-33.15,76.5-76.5,76.5c-43.35,0-76.5-33.15-76.5-76.5S211.65,76.5,255,76.5z M255,438.6
                                            c-63.75,0-119.85-33.149-153-81.6c0-51,102-79.05,153-79.05S408,306,408,357C374.85,405.45,318.75,438.6,255,438.6z" />
                                </g>
                            </g>
                        </svg>
                        Login
                    </a>
                </li>
            <?php } ?>
            </ul>

            </ul>
        </div>
    </div>
</nav>