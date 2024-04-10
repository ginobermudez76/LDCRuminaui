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
        <div id="modalEditCurso" class="modal edit">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editarEventoModalLabel">Editar curso</h5>
                </div>
                <div id="formContent"></div>
            </div>
        </div>
        <script>
    // Función para abrir el modal
    function openModal() {
        var modal = document.getElementById("modalEditCurso");
        modal.style.display = "block";
    }

    // Función para cerrar el modal
    function closeModal() {
        var modal = document.getElementById("modalEditCurso");
        modal.style.display = "none";
    }

    // Cierra el modal si se hace clic fuera de él
    window.onclick = function(event) {
        var modal = document.getElementById("modalEditCurso");
        if (event.target == modal) {
            modal.style.display = "none";
        }
    }

    // Carga el formulario desde el otro script PHP cuando se abre el modal
    function loadForm(idEvento) {
        var xhttp = new XMLHttpRequest();
        xhttp.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
                document.getElementById("formContent").innerHTML = this.responseText;
                document.getElementById("idEventoEdit").value = idEvento; // Establecer el ID del deportita en el formulario
                openModal(); // Abre el modal después de cargar el contenido
                
                // Aplica las funciones de límite de caracteres
                document.getElementById('nombreEdit').addEventListener('input', function() {
                    // Obtener el valor actual del campo de celular
                    var deporteNombre = this.value;
                    // Limitar el valor a 100 caracteres
                    if (deporteNombre.length > 100) {
                        this.value = deporteNombre.slice(0, 100);
                    }
                });

                // Función para limitar la cantidad de dígitos en el campo de descripcion
                document.getElementById('descripcionEdit').addEventListener('input', function() {
                    // Obtener el valor actual del campo de celular
                    var deporteDescripcion = this.value;
                    // Limitar el valor a 300 caracteres
                    if (deporteDescripcion.length > 300) {
                        this.value = deporteDescripcion.slice(0, 300);
                    }
                });
            }
        };
        xhttp.open("GET", "formEditCurso.php?id=" + idEvento, true); // Pasar el ID del deporte en la URL
        xhttp.send();
    }
</script>

        <script>
            function trim(str) {
                return str.replace(/^\s+|\s+$/g, '');
            }

            function validarCamposEdit() {

                // Validación de selección de tipo
                var deporteSeleccionado = document.getElementById("deporte_idEdit").value;
                if (deporteSeleccionado === "") {
                    alert("Por favor, seleccione un deporte");
                    return false;
                }
                var nombreEvento = document.getElementById("nombreEdit").value;
                nombreEvento1 = trim(nombreEvento);
                if (nombreEvento1 === "") {
                    alert("El evento debe tener un nombre");
                    return false;
                }

                // Validación de la fecha de inicio
                var fechaInicio = document.getElementById("fecha_iniEdit").value;
                var fechaInicioArray = fechaInicio.split("-");
                if (fechaInicioArray.length !== 3) {
                    alert("Por favor, introduzca una fecha de inicio válida.");
                    return false;
                }
                var yearInicio = fechaInicioArray[0];
                var monthInicio = fechaInicioArray[1];
                var dayInicio = fechaInicioArray[2];

                // Verificar si el año tiene 4 dígitos
                if (yearInicio.length !== 4 || isNaN(yearInicio)) {
                    alert("Por favor, introduzca un año entre 0001 y 9999 en la fecha de inicio.");
                    return false;
                }

                // Crear objeto de fecha de inicio y verificar si es válida
                var fechaInicioObjeto = new Date(yearInicio, monthInicio - 1, dayInicio);
                if (isNaN(fechaInicioObjeto.getTime())) {
                    alert("Por favor, introduzca una fecha de inicio válida.");
                    return false;
                }

                // Validación de la fecha de finalización
                var fechaFin = document.getElementById("fecha_fEdit").value;
                var fechaFinArray = fechaFin.split("-");
                if (fechaFinArray.length !== 3) {
                    alert("Por favor, introduzca una fecha de finalización válida.");
                    return false;
                }
                var yearFin = fechaFinArray[0];
                var monthFin = fechaFinArray[1];
                var dayFin = fechaFinArray[2];

                // Verificar si el año tiene 4 dígitos
                if (yearFin.length !== 4 || isNaN(yearFin)) {
                    alert("Por favor, introduzca un año entre 0001 y 9999 en la fecha de finalización.");
                    return false;
                }

                // Crear objeto de fecha de finalización y verificar si es válida
                var fechaFinObjeto = new Date(yearFin, monthFin - 1, dayFin);
                if (isNaN(fechaFinObjeto.getTime())) {
                    alert("Por favor, introduzca una fecha de finalización válida.");
                    return false;
                }

                // Validación de la fecha de inicio
                var fechaInicio = document.getElementById("fecha_iniEdit").value;
                var fechaInicioObjeto = new Date(fechaInicio);
                if (isNaN(fechaInicioObjeto.getTime())) {
                    alert("Por favor, introduzca una fecha de inicio válida.");
                    return false;
                }

                // Validación de la fecha de finalización
                var fechaFin = document.getElementById("fecha_fEdit").value;
                var fechaFinObjeto = new Date(fechaFin);
                if (isNaN(fechaFinObjeto.getTime())) {
                    alert("Por favor, introduzca una fecha de finalización válida.");
                    return false;
                }

                // Verificar que la fecha de finalización no sea menor que la fecha de inicio
                if (fechaFinObjeto < fechaInicioObjeto) {
                    alert("La fecha de finalización no puede ser menor que la fecha de inicio.");
                    return false;
                }


                var archivoInput = document.getElementById("imagenEdit");


                var archivo = archivoInput.files[0];
                var extensionesPermitidas = ['gif', 'png', 'jpg', 'webp', 'jpeg'];
                var extension = archivo.name.split('.').pop().toLowerCase();

                if (!extensionesPermitidas.includes(extension)) {
                    alert("Formato no soportado");
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
?>