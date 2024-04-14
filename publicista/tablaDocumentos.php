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
        // Mostrar el elemento del menú para publicista
        //logica para obtener la lista de documentos de la base de datos
        try {
            $stmt = $conn->prepare("SELECT * FROM documentos");
            $stmt->execute();

            $documentos = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
                            <th>Documento</th>
                            <th>Nombre</th>
                            <th>Descripción</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($documentos as $documento) : ?>
                            <tr>
                                <td><?php echo htmlspecialchars($documento['id']); ?></td>
                                <td>
                                    <?php if (isset($documento['documento']) && $documento['documento']) : ?>
                                        <a href="<?php echo htmlspecialchars($documento['documento']); ?>" target="_blank">Ver documento</a>
                                    <?php else : ?>
                                        <p>No hay documento</p>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo empty($documento['nombre']) ? 'No se proporcionó nombre' : htmlspecialchars($documento['nombre']); ?></td>
                                <td>
                                    <?php if (empty($documento['descripcion'])) : ?>
                                        <p>No hay descripción</p>
                                    <?php else : ?>
                                        <?php echo htmlspecialchars($documento['descripcion']); ?>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <button type="button" class="btn btn-secondary btn-sm" onclick="loadForm(<?php echo $documento['id']; ?>)">Editar</button>
                                    <button class="btn btn-danger btn-sm" onclick="confirmarEliminacion(<?php echo $documento['id']; ?>)">Eliminar</button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

            </div>
        </div>

        <div id="modalEditDocumentos" class="modal edit">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editarDocumentoModalLabel">Editar documento</h5>
                </div>
                <div id="formContent"></div>
            </div>
        </div>
        <script>
            // Función para abrir el modal
            function openModal() {
                var modal = document.getElementById("modalEditDocumentos");
                modal.style.display = "block";
            }

            // Función para cerrar el modal
            function closeModal() {
                var modal = document.getElementById("modalEditDocumentos");
                modal.style.display = "none";
            }

            // Cierra el modal si se hace clic fuera de él
            window.onclick = function(event) {
                var modal = document.getElementById("modalEditDocumentos");
                if (event.target == modal) {
                    modal.style.display = "none";
                }
            }

            // Carga el formulario desde el otro script PHP cuando se abre el modal
            function loadForm(idDocumento) {
                var xhttp = new XMLHttpRequest();
                xhttp.onreadystatechange = function() {
                    if (this.readyState == 4 && this.status == 200) {
                        document.getElementById("formContent").innerHTML = this.responseText;
                        document.getElementById("idDocumentoEdit").value = idDocumento; // Establecer el ID del documentita en el formulario
                        openModal(); // Abre el modal después de cargar el contenido
                        document.getElementById('nombreEdit').addEventListener('input', function() {
                            // Obtener el valor actual del campo de celular
                            var documentoNombre = this.value;
                            // Limitar el valor a 100 caracteres
                            if (documentoNombre.length > 200) {
                                this.value = documentoNombre.slice(0, 200);
                            }
                        });
                        // Función para limitar la cantidad de dígitos en el campo de descripcion
                        document.getElementById('descripcionEdit').addEventListener('input', function() {
                            // Obtener el valor actual del campo de celular
                            var documentoDescripcion = this.value;
                            // Limitar el valor a 10 caracteres
                            if (documentoDescripcion.length > 2000) {
                                this.value = documentoDescripcion.slice(0, 2000);
                            }
                        });
                    }
                };
                xhttp.open("GET", "formEditDocumento.php?id=" + idDocumento, true); // Pasar el ID del documento en la URL
                xhttp.send();
            }
        </script>
        <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
        <script>
            function confirmarEliminacion(idDocumento) {
                var confirmacion = confirm("¿Está seguro que desea eliminar este documento?");

                if (confirmacion) {
                    // Usuario hizo clic en "Aceptar", enviar solicitud a eliminar_documento.php
                    eliminarDocumento(idDocumento);
                } else {
                    // Usuario hizo clic en "Cancelar", no hacer nada
                }
            }

            function eliminarDocumento(idDocumento) {
                // Utiliza jQuery para enviar una solicitud AJAX a eliminar_documento.php
                $.ajax({
                    type: "POST",
                    url: "eliminarDocumento.php",
                    data: {
                        id: idDocumento
                    },
                    success: function(response) {
                        // Manejar la respuesta, si es necesario
                        console.log(response);
                        alert(response);
                        // Puedes recargar la página o actualizar la lista de documentos de alguna manera
                        $("#tablaDocumentos").load("tablaDocumento.php");
                    },
                    error: function(error) {
                        // Manejar errores si es necesario
                        alert(response);
                        console.error(error);
                    }
                });
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