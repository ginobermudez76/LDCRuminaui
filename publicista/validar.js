/*script para la validacion de los campos obligatorios no estén vacios*/
$("#tablaCartas").load("tablaCartas.php");

function trim(str) {
  /*funcion para quitar espacios vacios 
    usable para todos los inputs obligatorios
    la implementación es en base a que required de html se puede vulnerar 
    poniendo espacios*/
  return str.replace(/^\s+|\s+$/g, "");
}
//fuciones para validar que los campos no queden vacios
function validarCamposInsert() {
  var nombre = document.getElementById("nombre").value;
  if (nombre) {
    var nombreInput = nombre.value;
    if (nombreInput.trim() === "") {
      alert("El nombre no puede quedar vacio");
      return false;
    }
    nombre.addEventListener("input", function () {
      /*funcion para limitar la cantidad
    de caracteres en el input, max-lengt de html puede ser vulnerado 
    haciendo copy paste*/ var nombreLimit = this.value;
      if (nombreLimit.length > 100) {
        this.value = nombreLimit.slice(0, 100);
      }
    });
  }

  var tituloInput = document.getElementById("titulo");
  if (tituloInput) {
    var tituloNoticia = tituloInput.value;
    if (tituloNoticia.trim() === "") {
      /*usando la funcion anterior "trim" 
      se quitan todos los espacios y se compara con una cadena vacia*/
      alert("El título no puede quedar vacío."); /*alert que indica que el input
        esta vacio*/
      return false; /* retornar falso para impedir el envio del form*/
    }
    tituloInput.addEventListener("input", function () {
      /*funcion para limitar la cantidad
      de caracteres en el input, max-lengt de html puede ser vulnerado 
      haciendo copy paste*/ var titulo = this.value;
      if (titulo.length > 100) {
        this.value = titulo.slice(0, 100);
      }
    });
  }

  /*select tipo*/
  var tipoInput = document.getElementById("tipoLogro");
  if (tipoInput) {
    var tipo = tipoInput.value;
    if (tipo === "") {
      alert("Seleccione un tipo de logro");
      return false;
    }
  }
  /*select deportes*/
  var deporteInput = document.getElementById("deporte_id");
  if (deporteInput) {
    var deporte = deporteInput.value;
    if (deporte === "") {
      alert("El deporte no puede quedar vacio");
      return false;
    }
  }
  /*input cuerpo*/
  var cuerpoInput = document.getElementById("cuerpo");
  if (cuerpoInput) {
    var cuerpoNoticia = cuerpoInput.value;
    if (cuerpoNoticia.trim() === "") {
      alert("La noticia debe tener un cuerpo.");
      return false;
    }
    cuerpoInput.addEventListener("input", function () {
      var cuerpo = this.value;
      if (cuerpo.length > 5000) {
        this.value = cuerpo.slice(0, 5000);
      }
    });
  }
  /*textarea mensaje*/
  var mensajeInput = document.getElementById("mensaje");
  if (mensajeInput) {
    var mensajeCon = mensajeInput.value;
    if (mensajeCon.trim() === "") {
      alert("El mensaje no debe estar vacío");
      return false;
    }
    mensajeInput.addEventListener("input", function () {
      var mensaje = this.value;
      if (mensaje.length > 5000) {
        this.value = mensaje.slice(0, 5000);
      }
    });
  }
  /*file para la imagen*/
  var archivoInput = document.getElementById("imagen");
  if (archivoInput) {
    var archivo = archivoInput.files[0];
    if (!archivo) {
      alert("Debe seleccionar un archivo de imagen.");
      return false;
    }
    /*funcion para la comprobación de la extencio de la imagen
      se puede ajustar si el servidor de alojamiento permite otros 
      formatos*/
    var extensionesPermitidas = ["gif", "png", "jpg", "webp", "jpeg"];
    var extension = archivo.name.split(".").pop().toLowerCase();
    if (!extensionesPermitidas.includes(extension)) {
      alert("Formato de imagen no soportado");
      return false;
    }
  }

  return true;
}
/*funcion para deshabilitar el input del file
  de la imagen si se marca el checkbox*/
function deshabilitarInputImagen() {
  var checkbox = document.getElementById("checkDImagen");
  var inputImagen = document.getElementById("imagenEdit");

  if (checkbox.checked) {
    inputImagen.disabled = true;
  } else {
    inputImagen.disabled = false;
  }
}
/* funcion para desabilitar el chebox si se carga una imagen*/
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
