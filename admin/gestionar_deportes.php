<?php
include '../includes/config.php'; //incluyendo la conexión de la base de datos

include '../includes/header.php'; //incluyendo la cabecera común

if (!isset($_SESSION['usuario_admin'])) {
    header("Location: /Ayudantias-1/admin/login.php");
    exit();
}
//logica para obtener la lista de deportes de la base de datos
try {
    $stmt = $conn->prepare("SELECT id, nombre, descripcion, imagen FROM deportes");
    $stmt->execute();

    $deporte = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
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
        // Mostrar el elemento del menú para publicista
?>

        <div class="container mt-4">
            <h2 class="gestionar">Deportes ofertados</h2>
            <a href="agregar_deporte.php" class="btn btn-primary mb-4">Agregar Deporte</a>

            <table class="table">
                <thead>
                    <tr>
                        <th>Id</th>
                        <th>Imagen</th>
                        <th>Nombre</th>
                        <th>Descripción</th>
                        <th>Galería de imagenes</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($deporte as $deporte) : ?>
                        <tr>
                            <td><?php echo htmlspecialchars($deporte['id']); ?></td>
                            <td>
                                <?php if (isset($deporte['imagen']) && $deporte['imagen']) : ?>
                                    <img src="<?php echo htmlspecialchars($deporte['imagen']); ?>" alt="<?php echo htmlspecialchars($deporte['nombre']); ?>" style="width: 100px; height: auto;">
                                <?php else : ?>
                                    <p>Sin Imagen</p>
                                <?php endif; ?>
                            </td>
                            <td><?php echo htmlspecialchars($deporte['nombre']); ?></td>
                            <td><?php echo htmlspecialchars($deporte['descripcion']); ?></td>
                            <td>
                                <a href="galeria_de_imagenes.php?id=<?php echo $deporte['id']; ?>&nombre=<?php echo $deporte['nombre']; ?> &tipo=<?php echo 'Deporte'; ?>" class="btn btn-secondary btn-sm">Agregar</a>
                                <a href="eliminar_selecciones.php?id=<?php echo $deporte['id']; ?>&nombre=<?php echo $deporte['nombre']; ?> &tipo=<?php echo 'Deporte'; ?>" class="btn btn-danger btn-sm">Borrar</a>
                            </td>
                            <td>
                                <a href="editar_deporte.php?id=<?php echo $deporte['id']; ?>" class="btn btn-secondary btn-sm">Editar</a>
                                <button class="btn btn-danger btn-sm" onclick="confirmarEliminacion(<?php echo $deporte['id']; ?>)">Eliminar</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
                </tbody>
            </table>
        </div>

        <!-- Luego Bootstrap JS -->

        <!-- Y finalmente, tu script personalizado -->
        <script>
            function confirmarEliminacion(idDeporte) {
                var confirmacion = confirm("¿Está seguro que desea eliminar este deporte?");

                if (confirmacion) {
                    // Usuario hizo clic en "Aceptar", enviar solicitud a eliminar_deporte.php
                    eliminarDeporte(idDeporte);
                } else {
                    // Usuario hizo clic en "Cancelar", no hacer nada
                }
            }

            function eliminarDeporte(idDeporte) {
                // Utiliza jQuery para enviar una solicitud AJAX a eliminar_evento.php
                $.ajax({
                    type: "POST",
                    url: "eliminar_deporte.php",
                    data: {
                        id: idDeporte
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
        </tbody>
        </table>
        </div>

<?php
    } else {
        header("Location: /Ayudantias-1/public/index.php");
        exit();
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
include '../includes/footer.php'; ?>