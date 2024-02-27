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
        // Mostrar el elemento del menú Administrar
//logica para obtener la lista de eventos de la base de datos
try {
    $stmt = $conn->prepare("SELECT e.id, e.nombre, e.descripcion, e.fecha_inicio, e.fecha_fin, e.imagen, d.nombre AS nombre_deporte 
                            FROM eventos e
                            INNER JOIN deportes d ON e.deporte_id = d.id");
    $stmt->execute();

    $eventos = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}        
?>

<div class="container mt-4">
    <h2 class="gestionar">Centro de control de eventos</h2>
    <a href="agregar_evento.php" class="btn btn-primary mb-4">Agregar Evento</a>
    <div class="table-responsive">
    <table class="table">
        <thead>
            <tr>
                <th>Id</th>
                <th>Imagen</th>
                <th>Nombre</th>
                <th>Descripción</th>
                <th>Deporte</th>
                <th>Fecha de inicio</th>
                <th>Fecha de finalización</th>
                <th>Galeria de imagenes</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($eventos as $evento) : ?>
                <tr>
                    <td><?php echo htmlspecialchars($evento['id']); ?></td>
                    <td>
                        <?php if (isset($evento['imagen']) && $evento['imagen']) : ?>
                            <img src="<?php echo htmlspecialchars($evento['imagen']); ?>" alt="<?php echo htmlspecialchars($evento['nombre']); ?>" style="width: 100px; height: auto;">
                        <?php else : ?>
                            <p>Sin Imagen</p>
                        <?php endif; ?>
                    </td>
                    <td><?php echo htmlspecialchars($evento['nombre']); ?></td>
                    <td><?php echo htmlspecialchars($evento['descripcion']); ?></td>
                    <td><?php echo htmlspecialchars($evento['nombre_deporte']); ?></td>
                    <td><?php echo htmlspecialchars($evento['fecha_inicio']); ?></td>
                    <td><?php echo htmlspecialchars($evento['fecha_fin']); ?></td>
                    <td>
                        <a href="galeria_de_imagenes.php?id=<?php echo $evento['id']; ?>&nombre=<?php echo urlencode($evento['nombre']); ?>&tipo=Evento" class="btn btn-secondary btn-sm">Agregar</a>
                        <a href="eliminar_selecciones.php?id=<?php echo $evento['id']; ?>&nombre=<?php echo urlencode($evento['nombre']); ?>&tipo=Evento" class="btn btn-danger btn-sm">Borrar</a>
                    </td>
                    <td>
                        <a href="editar_evento.php?id=<?php echo $evento['id']; ?>" class="btn btn-secondary btn-sm">Editar</a>
                        <button class="btn btn-danger btn-sm" onclick="confirmarEliminacion(<?php echo $evento['id']; ?>)">Eliminar</button>
                    </td>
                </tr>
            <?php endforeach; ?>
            <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
        </tbody>
    </table>
    </div>
</div>

<script>
    function confirmarEliminacion(idEvento) {
        var confirmacion = confirm("¿Está seguro que desea eliminar este evento?");

        if (confirmacion) {
            // Usuario hizo clic en "Aceptar", enviar solicitud a eliminar_evento.php
            eliminarEvento(idEvento);
        } else {
            // Usuario hizo clic en "Cancelar", no hacer nada
        }
    }

    function eliminarEvento(idEvento) {
        // Utiliza jQuery para enviar una solicitud AJAX a eliminar_evento.php
        $.ajax({
            type: "POST",
            url: "eliminar_evento.php",
            data: {
                id: idEvento
            },
            success: function(response) {
                // Manejar la respuesta, si es necesario
                console.log(response);

                // Puedes recargar la página o actualizar la lista de productos de alguna manera
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
}else{
    header("Location: ../public/index.php");
    exit();
}
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
include '../includes/footer.php'; ?>