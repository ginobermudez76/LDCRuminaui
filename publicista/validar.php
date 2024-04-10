<script>
  var main = "<?php echo htmlspecialchars($main) ?>";
/*tablas*/
$("#tablaDeportes").load("tablaDeportes.php");
$("#tablaCartas").load("tablaCartas.php");

function trim(str) {
  return str.replace(/^\s+|\s+$/g, "");
}
function validarCamposEvento() {
  var mensajeCon = document.getElementById("mensaje").value;
  if (mensajeCon.trim() === "") {
    alert("El mensaje no debe estar vacío");
    return false;
  }

  var archivoInput = document.getElementById("imagen");

  var archivo = archivoInput.files[0];
  var extensionesPermitidas = ["gif", "png", "jpg", "webp", "jpeg"];
  var extension = archivo.name.split(".").pop().toLowerCase();

  if (!extensionesPermitidas.includes(extension)) {
    alert("Formato no soportado");
    return false;
  }

  return true;
}
// Función para limitar la cantidad de dígitos en el campo de mensaje
document.getElementById("mensaje").addEventListener("input", function () {
  // Obtener el valor actual del campo de mensaje
  var mensajeCondolencia = this.value;
  // Limitar el valor a 700 caracteres
  if (mensajeCondolencia.length > 5000) {
    this.value = mensajeCondolencia.slice(0, 5000);
  }
});

function deshabilitarInputImagen() {
  var checkbox = document.getElementById("checkDImagen");
  var inputImagen = document.getElementById("imagenEdit");

  if (checkbox.checked) {
    inputImagen.disabled = true;
  } else {
    inputImagen.disabled = false;
  }
}

function deshabilitarCheckbox() {
  var checkbox = document.getElementById("checkDImagen");
  var inputImagen = document.getElementById("imagenEdit");

  if (inputImagen.value) {
    checkbox.disabled = true;
  } else {
    checkbox.disabled = false;
  }
}

function validarCamposEdit() {
  var mensajeCon = document.getElementById("mensajeEdit").value;
  if (mensajeCon.trim() === "") {
    alert("El mensaje no debe estar vacío");
    return false;
  }
  var nombreDeporte = document.getElementById("nombreEdit").value;
  // Utilizamos la función trim para eliminar espacios en blanco al principio y al final
  nombreDeporte1 = trim(nombreDeporte);

  if (nombreDeporte1 === "") {
    alert("El deporte debe tener un nombre");
    return false;
  }

  var imagenInputEdit = document.getElementById("imagenEdit");
  var imagen = imagenInputEdit.files[0];
  var extensionesPermitidas = ["gif", "png", "jpg", "webp", "jpeg", "svg"];
  var extension = imagen.name.split(".").pop().toLowerCase();

  if (!extensionesPermitidas.includes(extension)) {
    alert("Formato no soportado");
    return false;
  }
  return true;
}
// Función para manejar la respuesta del servidor

</script>