<?php

include 'includes/config.php';
include 'header.php';
include 'includes/navbar.php';


try {
    $stmt = $conn->prepare("Select sol_id, sol_nombre, sol_descripcion, tsol_categoria, sol_precio FROM tipo_solicitud");
    $stmt->execute();

    $solicitudes = $stmt->fetchAll();
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = $_POST['sol_nombre'];
    $descripcion = $_POST['sol_descripcion'];
    $categoria = $_POST['tsol_categoria'];
    $precio = $_POST['sol_precio'];

    //Insertar en la base de datos (con o sin imagen)

    try {
        $stmt = $conn->prepare("INSERT INTO tipo_solicitud (sol_nombre, sol_descripcion, tsol_categoria, sol_precio) VALUES (?,?,?)");
        $stmt->execute([$nombre, $descripcion, $categoria, $precio]);

        //Redirigir desde agregar
        header("Location: Agregarsolicitud.php");
        exit();
    } catch (PDOException $e) {
        echo "Error" . $e->getMessage();
    }
}

?>





<div class="container mt-4">
    <h2> Agregar Solicitudes</h2>

    <!-- Button trigger modal -->
    <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#agregar">
        Agregar +
    </button>
    <br>
    <br>
    <h5>Solicitudes actuales</h5>
    <!-- Modal -->
    <div class="modal fade" id="agregar" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        aria-labelledby="agregar" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="agregar">Agregar Solicitud</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Formulario -->
                    <form action="Agregarsolicitud.php" method="post" enctype="multipart/form-data">

                        <div class="mb-3">
                            <label for="nombre" class="form-label"> Nombre Solicitud</label>
                            <input type="text" id="sol_nombre" name="sol_nombre">
                        </div>

                        <div class="mb-3">
                            <label for="descripcion" class="form-label"> Descripción</label>
                            <textarea type="text" class="form-control" id="sol_descripcion" name="sol_descripcion"
                                required></textarea>
                        </div>

                        <div class="mb-3">
                            <label for="categoria" class="form-label"> Requiere categoria</label>
                            <select name="tsol_categoria" id=tsol_categoria>
                                <option> </option>
                                <option value="Si">Si</option>
                                <option value="No">No</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="precio" class="form-label"> Precio</label>
                            <input type="number" class="form-control" step="0.01" id="sol_precio" name="sol_precio"
                                required>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                            <button type="submit" class="btn btn-primary">Agregar</button>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>

    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre Solicitud</th>
                <th>Detalle</th>
                <th>Req.Categoria</th>
                <th>Precio</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($solicitudes as $solicitud): ?>
                <tr>
                    <td>
                        <?php echo htmlspecialchars($solicitud['sol_id']); ?>
                    </td>

                    <td>
                        <?php echo htmlspecialchars($solicitud['sol_nombre']); ?>
                    </td>

                    <td>
                        <?php echo htmlspecialchars($solicitud['sol_descripcion']); ?>
                    </td>

                    <td>
                        <?php echo htmlspecialchars($solicitud['tsol_categoria']); ?>
                    </td>

                    <td>
                        <?php echo htmlspecialchars($solicitud['sol_precio']); ?>
                    </td>

                    <td>
                        <a href="editar_solicitud.php?id=<?php echo $solicitud['sol_id']; ?>"
                            class="btn btn-secondary btn-sm">
                            <i class="fa-solid fa-pen" style="color: #ffffff;"></i>
                            Editar
                        </a>
                        <a href="eliminar_solicitud.php?id=<?php echo $solicitud['sol_id']; ?>"
                            class="btn btn-danger btn-sm">
                            <i class="fa-solid fa-trash" style="color: #fffff;"></i>
                            Eliminar </a>
                    </td>

                </tr>
            <?php endforeach; ?>
        </tbody>

    </table>

</div>
