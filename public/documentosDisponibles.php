<?php
include '../includes/header.php';
include '../includes/config.php';
try {
    $stmt = $conn->prepare("SELECT * FROM documentos");
    $stmt->execute();

    $documentos = $stmt->fetchAll();
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
<div class="container mt-4 documentos">
    <h2 class="descarga-documentos">Descarga de formatos de documentos</h2>
    <div class="row">
        <?php foreach ($documentos as $documento) : ?>
            <div class="col-md-4 mb-4">
                <div class="card documento">
                    <div class="image-container">
                        <img src="../img/documento.png">
                        <div class="infoDocumento">
                            <h3><?php echo htmlspecialchars($documento['nombre']); ?></h3>
                            <?php if (isset($documento['documento']) && $documento['documento']) : ?>
                                <a href="<?php echo htmlspecialchars($documento['documento']); ?>" target="_blank">Ver documento</a>
                            <?php else : ?>
                                <p>No hay documento</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>
<?php
include '../includes/footer.php';
?>
