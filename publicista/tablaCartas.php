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
            $stmt = $conn->prepare("SELECT * FROM carta_condolencias");
            $stmt->execute();
            $cartas = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
                            <th>Imagen</th>
                            <th>Mensaje</th>
                            <th>Fecha de eliminación</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($cartas as $carta) : ?>
                            <tr>
                                <td><?php echo htmlspecialchars($carta['id']); ?></td>
                                <td>
                                    <?php if (isset($carta['imagen']) && $carta['imagen']) : ?>
                                        <img src="<?php echo htmlspecialchars($carta['imagen']); ?>" alt="<?php echo htmlspecialchars($carta['mensaje']); ?>" style="width: 100px; height: auto;">
                                    <?php else : ?>
                                        <p>Sin Imagen</p>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo htmlspecialchars($carta['mensaje']); ?></td>
                                <td><?php echo htmlspecialchars($carta['fecha_eliminar']); ?></td>
                                <td>
                                    <button type="button" class="btn btn-secondary btn-sm" onclick="loadForm(<?php echo $carta['id']; ?>)">Editar</button>
                                    <button class="btn btn-danger btn-sm" onclick="confirmarEliminacion(<?php echo $carta['id']; ?>)">Eliminar</button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div id="modalEditCartas" class="modal">
            <div class="modal-content">
                <span class="close">&times;</span>
                <div id="formContent"></div>
            </div>
        </div>
        <script>
    // Función para abrir el modal
    function openModal() {
        var modal = document.getElementById("modalEditCartas");
        modal.style.display = "block";
    }

    // Función para cerrar el modal
    function closeModal() {
        var modal = document.getElementById("modalEditCartas");
        modal.style.display = "none";
    }

    // Cierra el modal si se hace clic fuera de él
    window.onclick = function(event) {
        var modal = document.getElementById("modalEditCartas");
        if (event.target == modal) {
            modal.style.display = "none";
        }
    }

    // Carga el formulario desde el otro script PHP cuando se abre el modal
    function loadForm(idCarta) {
    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            document.getElementById("formContent").innerHTML = this.responseText;
            document.getElementById("idCartaEdit").value = idCarta; // Establecer el ID del deportita en el formulario
            openModal(); // Abre el modal después de cargar el contenido
        }
    };
    xhttp.open("GET", "formEditCarta.php?id=" + idCarta, true); // Pasar el ID de la carta en la URL
    xhttp.send();
}

</script>
        <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
        <script>
            function confirmarEliminacion(idCarta) {
                var confirmacion = confirm("¿Está seguro que desea eliminar. Esta acción no se puede deshacer.?");

                if (confirmacion) {
                    // Usuario hizo clic en "Aceptar", enviar solicitud a eliminarCarta.php
                    eliminarCarta(idCarta);
                } else {
                    // Usuario hizo clic en "Cancelar", no hacer nada
                }
            }

            function eliminarCarta(idCarta) {
                // Utiliza jQuery para enviar una solicitud AJAX a eliminarCarta.php
                $.ajax({
                    type: "POST",
                    url: "eliminarCarta.php",
                    data: {
                        id: idCarta
                    },
                    success: function(response) {
                        // Manejar la respuesta, si es necesario
                        console.log(response);

                        // Puedes recargar la página o actualizar la lista de deportes de alguna manera
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
