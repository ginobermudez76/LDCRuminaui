<?php
include '../includes/config.php';
try {
    $stmt = $conn->prepare("SELECT imagen, mensaje FROM carta_condolencias");
    $stmt->execute();
    $condolencias = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $stmt->closeCursor();
} catch (PDOException $e) {
    echo "Error al obtener carta de condolencias: " . $e->getMessage();
}
?>

<div id="carouselExampleControls" class="carousel slide" data-ride="carousel">
  <div class="carousel-inner">
    <?php foreach ($condolencias as $key => $condolencia): ?>
    <div class="carousel-item <?php echo ($key == 0) ? 'active' : ''; ?>">
      <div class="row">
        <div class="col-md-6">
          <img src="<?php echo $condolencia['imagen']; ?>" class="d-block w-100" alt="Imagen">
        </div>
        <div class="col-md-6">
          <div class="mensaje">
            <?php echo $condolencia['mensaje']; ?>
          </div>
        </div>
      </div>
    </div>
    <?php endforeach; ?>
  </div>
  <a class="carousel-control-prev" href="#carouselExampleControls" role="button" data-slide="prev">
    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
    <span class="sr-only">Previous</span>
  </a>
  <a class="carousel-control-next" href="#carouselExampleControls" role="button" data-slide="next">
    <span class="carousel-control-next-icon" aria-hidden="true"></span>
    <span class="sr-only">Next</span>
  </a>
</div>
