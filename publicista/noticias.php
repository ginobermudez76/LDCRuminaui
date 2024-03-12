<?php

include '../includes/config.php'; //incluyendo la conexión de la base de datos
include '../includes/header.php'; //incluyendo la cabecera común

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
    if ($usuario['rol'] == 7) {
?>
        <div class="container mt-4">
            <h2 class="gestionar">Noticias</h2>
            <form action="insertarNoticia.php" method="post" enctype="multipart/form-data" onsubmit="return validarCamposNoticias()">
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
                    <textarea class="form-control" id="cuerpo" name="cuerpo" rows="3" required></textarea>
                </div>
                <button type="submit" class="btn btn-primary">Publicar</button>
            </form>
        </div>
        <div class="container">
            <div class="row" id="tablaNoticias">
            </div>
        </div>
        <script>
            function validarCamposNoticias() {

                var tituloNoticia = document.getElementById("titulo").value;
                if (tituloNoticia === "") {
                    alert("El titulo no puede quedar vacio.");
                    return false;
                }
                var cuerpoNoticia = document.getElementById("cuerpo").value;
                if (cuerpoNoticia === "") {
                    alert("La noticia debe tener un cuerpo.");
                    return false;
                }
                return true;
            }
        </script>
                <script>
            $("#tablaNoticias").load("tablaNoticias.php"); //load es una funcion de Jquery
            $(document).ready(function() {

            });
        </script>
<?php
    } else {
        header("Location: ../public/index.php");
        exit();
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
include '../includes/footer.php';
?>