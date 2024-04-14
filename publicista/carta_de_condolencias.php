<?php
include '../includes/config.php'; // Incluyendo la conexión a la base de datos
include '../includes/header.php';
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
            <form id="formCartas" method="post" enctype="multipart/form-data" onsubmit="return validarCamposInsert()">
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
    <script>
      function handleResponse(response) {
        if (response.success) {
          alertify.success(response.message);
          $("#formCartas")[0].reset();
          // Recargar la TABLA
          $("#tablaCartas").load("tablaCartas.php");
        } else {
          alertify.error(response.message);
        }
      }

      $(document).ready(function() {
        // Manejar el envío del formulario
        $("#formCartas").submit(function(event) {
          event.preventDefault(); // Evitar el envío del formulario por defecto
          var formData = new FormData($(this)[0]); // Obtener los datos del formulario
          $.ajax({
            url: "insertarCarta.php",
            type: "POST",
            data: formData,
            async: false,
            success: function(response) {
              handleResponse(JSON.parse(response));
            },
            cache: false,
            contentType: false,
            processData: false,
          });
          return false;
        });
      });

      function confirmarEliminacion(idCarta) {
        var confirmacion = confirm(
          "¿Está seguro que desea eliminar. Esta acción no se puede deshacer.?"
        );

        if (confirmacion) {
          // Usuario hizo clic en "Aceptar", enviar solicitud a eliminarCarta.php
          eliminarCarta(idCarta);
        } else {
          // Usuario hizo clic en "Cancelar", no hacer nada
        }
      }

      function eliminarCarta(idCarta) {
        // Utiliza jQuery para enviar una solicitud AJAX a eliminarCarta.php
        $.ajax({
          type: "POST",
          url: "eliminarCarta.php",
          data: {
            id: idCarta,
          },
          success: function(response) {
            // Manejar la respuesta, si es necesario
            console.log(response);

            // Puedes recargar la página o actualizar la lista de deportes de alguna manera
            $("#tablaCartas").load("tablaCartas.php");
          },
          error: function(error) {
            // Manejar errores si es necesario
            console.error(error);
          },
        });
      }
      // Función para abrir el modal
      function openModal() {
        var modal = document.getElementById("modalEditCartas");
        modal.style.display = "block";
      }

      // Función para cerrar el modal
      function closeModal() {
        var modal = document.getElementById("modalEditCartas");
        modal.style.display = "none";
      }

      // Cierra el modal si se hace clic fuera de él
      window.onclick = function(event) {
        var modal = document.getElementById("modalEditCartas");
        if (event.target == modal) {
          modal.style.display = "none";
        }
      };

      // Carga el formulario desde el otro script PHP cuando se abre el modal
      function loadForm(idCarta) {
        var xhttp = new XMLHttpRequest();
        xhttp.onreadystatechange = function() {
          if (this.readyState == 4 && this.status == 200) {
            document.getElementById("formContent").innerHTML = this.responseText;
            document.getElementById("idCartaEdit").value = idCarta; // Establecer el ID del deportita en el formulario
            openModal(); // Abre el modal después de cargar el contenido
            // Función para limitar la cantidad de dígitos en el campo de mensaje
            document.getElementById("mensajeEdit").addEventListener("input", function() {
              // Obtener el valor actual del campo de mensaje
              var mensajeCondolencia = this.value;
              // Limitar el valor a 700 caracteres
              if (mensajeCondolencia.length > 5000) {
                this.value = mensajeCondolencia.slice(0, 5000);
              }
            });
          }
        };
        xhttp.open("GET", "formEditCarta.php?id=" + idCarta, true); // Pasar el ID de la carta en la URL
        xhttp.send();
      }
    </script>
    <?php include 'validar.php' ?> 
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