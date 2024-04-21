<?php
session_start();
include '../includes/config.php'; //incluyendo la conexión de la base de datos

if (!isset($_SESSION['usuario_admin'])) {
    echo "<script>window.location.href='../admin/login.php';</script>";
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
            $stmt = $conn->prepare("SELECT e.id, e.nombre, e.descripcion, e.fecha_inicio, e.fecha_fin, e.inscripciones, e.imagen, d.nombre AS nombre_deporte 
                            FROM cursos e
                            INNER JOIN deportes d ON e.deporte_id = d.id");
            $stmt->execute();

            $eventos = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
?>
        <div class="container mt-4">
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
                            <th>Inscripciones</th>
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
                                <td>
                                    <?php if (!empty($evento['descripcion'])) : ?>

                                        <?php echo htmlspecialchars($evento['descripcion']); ?>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo htmlspecialchars($evento['nombre_deporte']); ?></td>
                                <td><?php echo htmlspecialchars($evento['fecha_inicio']); ?></td>
                                <td><?php echo htmlspecialchars($evento['fecha_fin']); ?></td>
                                <td>
                                    <?php if ($evento['inscripciones'] == 'Abiertas') { ?>
                                        <button id="btn_<?php echo $evento['id']; ?>" class="btn btn-secondary btn-sm inscripcion-abierta" onclick="ConfirmarInscripcion(<?php echo $evento['id']; ?>, 'Cerradas')" onmouseover="cambiarTextoBoton(<?php echo $evento['id']; ?>)" onmouseout="restaurarTextoBoton(<?php echo $evento['id']; ?>)">Abiertas</button>
                                    <?php } elseif ($evento['inscripciones'] == 'Cerradas') { ?>
                                        <button id="btn_<?php echo $evento['id']; ?>" class="btn btn-danger btn-sm inscripcion-cerrada" onclick="ConfirmarInscripcion(<?php echo $evento['id']; ?>, 'Abiertas')" onmouseover="cambiarTextoBoton(<?php echo $evento['id']; ?>)" onmouseout="restaurarTextoBoton(<?php echo $evento['id']; ?>)">Cerradas</button>
                                    <?php } ?>
                                </td>

                                <script>
                                    // Función para cambiar el texto del botón al pasar el mouse sobre él
                                    function cambiarTextoBoton(id) {
                                        var boton = document.getElementById('btn_' + id);
                                        if (boton.classList.contains('inscripcion-abierta')) {
                                            boton.innerText = 'Cerrar';
                                        } else if (boton.classList.contains('inscripcion-cerrada')) {
                                            boton.innerText = 'Abrir';
                                        }
                                    }

                                    // Función para restaurar el texto original del botón al quitar el mouse
                                    function restaurarTextoBoton(id) {
                                        var boton = document.getElementById('btn_' + id);
                                        if (boton.classList.contains('inscripcion-abierta')) {
                                            boton.innerText = 'Abiertas';
                                        } else if (boton.classList.contains('inscripcion-cerrada')) {
                                            boton.innerText = 'Cerradas';
                                        }
                                    }
                                </script>

                                <td>
                                    <a href="galeria_de_imagenes.php?id=<?php echo $evento['id']; ?>&nombre=<?php echo urlencode($evento['nombre']); ?>&tipo=Curso" class="btn btn-secondary btn-sm">Agregar</a>
                                    <a href="eliminar_selecciones.php?id=<?php echo $evento['id']; ?>&nombre=<?php echo urlencode($evento['nombre']); ?>&tipo=Curso" class="btn btn-danger btn-sm">Borrar</a>
                                </td>
                                <td>
                                    <button type="button" class="btn btn-secondary btn-sm" onclick="loadForm(<?php echo $evento['id']; ?>)">Editar</button>
                                    <button class="btn btn-danger btn-sm" onclick="confirmarEliminacion(<?php echo $evento['id']; ?>)">Eliminar</button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
                    </tbody>
                </table>
            </div>
        </div>
        <div id="modalEditCursos" class="modal edit">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editarCursoModalLabel">Editar curso</h5>
                </div>
                <div id="formContent"></div>
            </div>
        </div>

<?php
    } else {
        echo "<script>window.location.href='../public/index.php';</script>";
        exit();
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>