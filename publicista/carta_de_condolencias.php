<?php
include '../includes/config.php'; // Incluyendo la conexión a la base de datos
include '../includes/header.php';
if (!isset($_SESSION['usuario_admin'])) {
  echo "<script>window.location.href='../admin/login.php';</script>";
  exit();
}
$usuario_id = $_SESSION['usuario_id'];
$main =  'Carta';
try {
  // Consultar el rol del usuario en la base de datos
  $stmt = $conn->prepare("SELECT rol FROM usuarios WHERE id = :usuario_id");
  $stmt->bindParam(':usuario_id', $usuario_id, PDO::PARAM_INT);
  $stmt->execute();

  $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

  // Verificar si el usuario tiene el rol de Publicista
  if ($usuario['rol'] == 7) {

?>
    <div class="container mt-5 mr-5">
      <h2 class="gestionar">Carta de condolencias</h2>
      <button type="button" class="btn btn-primary mb-4" data-bs-toggle="modal" data-bs-target="#agregarCartaModal">Publicar carta de condolencias</button>
    </div>
    <!-- Modal para agregar deportista destacado -->
    <div class="modal fade" id="agregarCartaModal" tabindex="-1" aria-labelledby="agregarDeportistaModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="agregarCartaModalLabel">Mostrar carta de condolencias</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <form id="formCarta" method="post" enctype="multipart/form-data" onsubmit="return validarCamposInsert()">
              <div class="mb-3">
                <label for="descripcion" class="form-label">Mensaje</label>
                <textarea type="text" class="form-control" id="mensaje" name="mensaje" required maxlength="5000"></textarea>
              </div>
              <div class="mb-3">
                <label for="imagen" class="form-label">Imagen</label>
                <input type="file" class="form-control" id="imagen" name="imagen" required></textarea>
              </div>
              <button type="submit" class="btn btn-primary">Mostrar</button>
            </form>
          </div>
        </div>
      </div>
    </div>

    <div class="container">
      <div class="row" id="tablaCartas">
      </div>
    </div>

    <?php include 'validar.php' ;
    include 'limitar.php';
    include 'cargar.php';?> 
<?php
  } else {
    echo "<script>window.location.href='../public/index.php';</script>";
    exit();
  }
} catch (PDOException $e) {
  echo "Error: " . $e->getMessage();
}
include '../includes/footer.php'
?>