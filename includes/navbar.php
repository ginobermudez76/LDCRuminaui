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
        <a class="navbar-brand" href="#">
            <img src="../img/logo.png" alt="Logo" class="rounded-circle" width="65" height="65">
            Liga Deportiva Cantonal de Rumiñahui
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link active" aria-current="page" href="../public/index.php">Inicio</a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        Escuelas
                    </a>
                    <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                        <?php foreach ($deportes as $deporte) : ?>
                            <a class="dropdown-item" href="#"><?= $deporte['nombre'] ?></a>
                        <?php endforeach; ?>
                    </div>
                </li>
                <li class="nav-item">
                    <a class="nav-link active" aria-current="page" href="../public/#Section_eventos">Eventos</a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        Acceso
                    </a>
                    <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                    <a class="nav-link active" aria-current="page" href="../admin/login.php">Administrativo</a>
                    <a class="nav-link active" aria-current="page" href="../admin/login.php">Personal</a>
                    </div>
                </li>
            </ul>
        </div>
    </div>
</nav>
