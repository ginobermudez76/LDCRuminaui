<?php

include '../includes/config.php'; //incluyendo la conexión de la base de datos
include '../includes/header.php'; //incluyendo la cabecera común

if (!isset($_SESSION['usuario_admin'])) {
    echo "<script>window.location.href='../admin/login.php';</script>";
    exit();
}
$usuario_id = $_SESSION['usuario_id'];
$main = 'Noticia';
try {
    // Consultar el rol del usuario en la base de datos
    $stmt = $conn->prepare("SELECT rol FROM usuarios WHERE id = :usuario_id");
    $stmt->bindParam(':usuario_id', $usuario_id, PDO::PARAM_INT);
    $stmt->execute();

    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    // Verificar si el usuario tiene el rol de Publicista
    if ($usuario['rol'] == 7) {
?>
        <div class="container mt-5 mr-5">
            <h2 class="gestionar">Noticias</h2>
            <button type="button" class="btn btn-primary mb-4" data-bs-toggle="modal" data-bs-target="#agregarNoticiaModal">Publicar una noticia</button>
        </div>
        <!-- Modal para agregar deportista destacado -->
        <div class="modal fade" id="agregarNoticiaModal" tabindex="-1" aria-labelledby="agregarNoticiaModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="agregarNoticiaModalLabel">Publicar noticia</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="formNoticia" autocomplete="off" method="post" enctype="multipart/form-data" onsubmit="return validarCamposInsert()">
                            <div class="mb-3">
                                <label for="titulo" class="form-label">Titulo</label>
                                <input type="text" class="form-control" id="titulo" name="titulo"></input>
                            </div>
                            <div class="mb-3">
                                <label for="imagen" class="form-label">Imagen</label>
                                <input type="file" class="form-control" id="imagen" name="imagen" required></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="cuerpo" class="form-label">Cuerpo de la noticia</label>
                                <textarea class="form-control" id="cuerpo" name="cuerpo" rows="3" required maxlength="5000"></textarea>
                            </div>

                            <div class="mb-3">
                                <button type="submit" class="btn btn-primary">Publicar</button>
                            </div>

                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div class="container">
            <div class="row" id="tablaNoticias">
            </div>
        </div>
        <?php include 'validar.php';
        include 'limitar.php';
        include 'cargar.php';
        ?>
<script>
        function trim(str) {
        return str.replace(/^\s+|\s+$/g, '');
    }
            function validarCamposEdit() {

                var tituloNoticia = document.getElementById("tituloEdit").value;
                tituloNoticia1 = trim(tituloNoticia);
                if (tituloNoticia1 === "") {
                    alert("El titulo no puede quedar vacio.");
                    return false;
                }
                var cuerpoNoticia = document.getElementById("cuerpoEdit").value;
                cuerpoNoticia1 = trim(cuerpoNoticia);
                if (cuerpoNoticia1 === "") {
                    alert("La noticia debe tener un cuerpo.");
                    return false;
                }
                return true;
            }
        </script>

<?php
    } else {
        echo "<script>window.location.href='../public/index.php';</script>";
        exit();
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
include '../includes/footer.php';
?>