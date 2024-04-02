<?php
include '../includes/config.php';
include '../includes/header.php';

try{
    $stmt = $conn -> prepare("SELECT id, titulo, imagen FROM noticias");
    $stmt -> execute();

    $noticias = $stmt ->fetchAll();
}catch(PDOException $e){
    echo "Error: " . $e ->getMessage();
}
?>
<div class="container mt-4 noticias">
    <h2>NOTICIAS</h2>
    <div class="row">
        <?php foreach ($noticias as $noticia) : ?>
            <div class="col-md-4 mb-4">
                <div class="card h-100 position-relative">
                    <?php if ($noticia['imagen']) : ?>
                        <img src="../uploads/noticias/<?php echo htmlspecialchars(basename($noticia['imagen'])); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($noticia['titulo']); ?>" style="width: 100%; height: 500px;">
                    <?php endif; ?>
                    <div class="overlayN">
                    <div class="d-flex flex-column align-items-center">
                            <p><?php echo htmlspecialchars($noticia['titulo']); ?></p>
                            <a href="noticiaDetalle.php?id=<?php echo $noticia['id']; ?>">Ver más</a>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>
<?php
include '../includes/footer.php'
?>
