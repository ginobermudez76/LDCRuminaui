<?php
include '../includes/config.php';
include '../includes/header.php';

if (isset($_GET['id'])) {
    $idNoticia = $_GET['id'];

    // Obtener la información del producto basada en el ID recibido
    try {
        $stmt = $conn->prepare("SELECT * FROM noticias WHERE id = :id");
        $stmt->bindParam(':id', $idNoticia);
        $stmt->execute();
        $noticia = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$idNoticia) {
            echo "ID de producto no válido";
            exit();
        }

        // Aquí puedes usar $producto para acceder a los detalles del producto
        $titulo = $noticia['titulo'];
        $fecha = $noticia['fecha'];
        $cuerpo = $noticia['cuerpo'];
        $imagen = $noticia['imagen'];


    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
} else {
    echo "ID de noticia no proporcionado";
    exit();
}
?>

<div class="container mt-4" id="detalleProducto">
    <!-- Contenido del Producto -->
    <div class="row">
        <!-- Carrusel en la columna izquierda -->
        <div class="col-md-6">

        <img src="../uploads/noticias/<?php echo htmlspecialchars(basename($imagen)); ?>" class="d-block w-100" alt="<?php echo htmlspecialchars($titulo); ?>">

        </div>
        <div class="col-md-6 detalles-producto">
            <h2><?php echo htmlspecialchars($titulo); ?></h2>
            <h><?php echo htmlspecialchars($fecha); ?></h>
            <p><?php echo htmlspecialchars($cuerpo); ?></p>
        </div>
    </div>
</div>
<?php
include '../includes/footer.php'
?>