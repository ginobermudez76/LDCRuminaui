<?php
session_start();
include '../includes/config.php'; // incluyendo la conexión de la base de datos
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
        try {
            $stmt = $conn->prepare("SELECT * FROM noticias");
            $stmt->execute();
            $noticias = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
?>
        <div class="container mt-5">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Id</th>
                            <th>Título</th>
                            <th>Imagen</th>
                            <th>Cuerpo</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($noticias as $noticia) : ?>
                            <tr>
                                <td><?php echo htmlspecialchars($noticia['id']); ?></td>
                                <td><?php echo htmlspecialchars($noticia['titulo']); ?></td>
                                <td>
                                    <?php if (isset($noticia['imagen']) && $noticia['imagen']) : ?>
                                        <img src="<?php echo htmlspecialchars($noticia['imagen']); ?>" alt="<?php echo htmlspecialchars($noticia['titulo']); ?>" style="width: 100px; height: 50px;">
                                    <?php else : ?>
                                        <p>Sin Imagen</p>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo htmlspecialchars($noticia['cuerpo']); ?></td>
                                <td>
                                    <button type="button" class="btn btn-secondary btn-sm" onclick="loadForm(<?php echo $noticia['id']; ?>)">Editar</button>
                                    <button class="btn btn-danger btn-sm" onclick="confirmarEliminacion(<?php echo $noticia['id']; ?>)">Eliminar</button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div id="modalEditNoticias" class="modal edit">
            <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Editar noticia</h5>
            </div>
                <div id="formContent"></div>
            </div>
        </div>
        <script>
    // Función para abrir el modal
    function openModal() {
        var modal = document.getElementById("modalEditNoticias");
        modal.style.display = "block";
    }

    // Función para cerrar el modal
    function closeModal() {
        var modal = document.getElementById("modalEditNoticias");
        modal.style.display = "none";
    }

    // Cierra el modal si se hace clic fuera de él
    window.onclick = function(event) {
        var modal = document.getElementById("modalEditNoticias");
        if (event.target == modal) {
            modal.style.display = "none";
        }
    }

    // Carga el formulario desde el otro script PHP cuando se abre el modal
    function loadForm(idNoticia) {
    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            document.getElementById("formContent").innerHTML = this.responseText;
            document.getElementById("idNoticiaEdit").value = idNoticia; // Establecer el ID de la noticia en el formulario
            openModal(); // Abre el modal después de cargar el contenido
        }
    };
    xhttp.open("GET", "formEditNoticia.php?id=" + idNoticia, true); // Pasar el ID de la noticia en la URL
    xhttp.send();
}

</script>
        <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
        <script>
            function confirmarEliminacion(idNoticia) {
                var confirmacion = confirm("¿Está seguro que desea eliminar. Esta acción no se puede deshacer.?");

                if (confirmacion) {
                    // Usuario hizo clic en "Aceptar", enviar solicitud a eliminarNoticia.php
                    eliminarNoticia(idNoticia);
                } else {
                    // Usuario hizo clic en "Cancelar", no hacer nada
                }
            }

            function eliminarNoticia(idNoticia) {
                // Utiliza jQuery para enviar una solicitud AJAX a eliminarNoticia.php
                $.ajax({
                    type: "POST",
                    url: "eliminarNoticia.php",
                    data: {
                        id: idNoticia
                    },
                    success: function(response) {
                        // Manejar la respuesta, si es necesario
                        console.log(response);

                        // Puedes recargar la página o actualizar la lista de noticias de alguna manera
                        location.reload();
                    },
                    error: function(error) {
                        // Manejar errores si es necesario
                        console.error(error);
                    }
                });
            }
        </script>
<?php
    } else {
        header("Location: ../public/index.php");
        exit();
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
