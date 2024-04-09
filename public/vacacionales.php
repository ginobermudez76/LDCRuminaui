<?php
include '../includes/header.php';
include '../includes/config.php';
try {
    $stmt = $conn->prepare("CALL ObtenerInfoCursos()");
    $stmt->execute();
    $eventos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $stmt->closeCursor();
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
if (empty($eventos)) {
    echo '<h2>No hay cursos vacacionales por ahora</h2>';
} else {
?>
<div class="vacionales">
    <div class="container-vacacional">
    <?php foreach ($eventos as $evento) : ?> 
        <div class="info-vacacional">
            <h5><?php echo htmlspecialchars($evento['nombre_evento']); ?></h5>
            <p><?php echo htmlspecialchars($evento['nombre_deporte']); ?></p>
            <div class="fechas">
                <p>Inicio: <?php echo htmlspecialchars($evento['fecha_inicio']); ?></p>
                <p>Fin: <?php echo htmlspecialchars($evento['fecha_fin']); ?></p>
            </div>
            <div class="info-estado">
                <p><?php echo htmlspecialchars($evento['estado']); ?></p>
                <p><?php echo htmlspecialchars($evento['inscripciones']); ?></p>
            </div>
            <div class="mas-info">
                <p><?php echo htmlspecialchars($evento['descripcion']); ?></p>
                <a type="button" class="btn btn-primary" href="../public/contacto.php">Contacto</a>
            </div>
        </div>

        <div class="imagen-boton">
            <img src="<?php echo htmlspecialchars($evento['imagen']); ?>" alt="<?php echo htmlspecialchars($evento['nombre_evento']); ?>">
            <p>Ver mas fotos</p>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<?php
}
include '../includes/footer.php';
?>