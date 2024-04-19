<script>

  /* variables compartidas a las funciones de este archivo y al archivo limitar.php*/
  var archivoInput = document.getElementById("imagen");
  var mensajeInput = document.getElementById("mensaje");
  var tituloInput = document.getElementById("titulo");
  var nombreInput = document.getElementById("nombre");
  var cuerpoInput = document.getElementById("cuerpo");
  var deporteInput = document.getElementById("deporte_id");
  var ubicacionInput = document.getElementById("ubicacion");
  var direccionInput = document.getElementById("direccion");
  var telefonoInput = document.getElementById("telefono");
  var supervisorInput = document.getElementById("supervisor");
  var celularInput = document.getElementById("celular");
  var documentoInput = document.getElementById("documento");
  var descripcionInput = document.getElementById('descripcion');
  var fechaInicio = document.getElementById("fecha_ini").value;
  var fechaFin = document.getElementById("fecha_f").value;
  /*funcion para quitar los espacios*/
  function trim(str) {
    return str.replace(/^\s+|\s+$/g, "");
  }
  /*funcion para validar que los campos no esten vacios*/
  function validarCamposInsert() {
    if (nombreInput) {
      var nombreInsert = nombreInput.value;
      if (nombreInsert.trim() === "") {
        alert("El nombre no puede quedar vacío.");
        return false;
      }

    }

    if (archivoInput) {
      /*Se comprueba que el input este presente en el script caso contrario se sigue con las demas validaciones*/
      if (!archivoInput || !archivoInput.files || archivoInput.files.length === 0) {
        /*verificar si se inserto la imagen*/
        alert("No se inserto imagen.");
        return false;
      }
      var archivo = archivoInput.files[0];
      var extensionesPermitidas = ["gif", "png", "jpg", "webp", "jpeg"]; /*Lista de formato de imagnes permitidas*/
      var extension = archivo.name.split(".").pop().toLowerCase(); /*separa el archivo y se optiene lo que sigue despues del punto*/

      if (!extensionesPermitidas.includes(extension)) {
        alert("Formato no soportado");
        return false;
      }
    }
    if (documentoInput) {
      if (!documentoInput || !documentoInput.files || documentoInputfiles.length === 0) {
        alert("Debe insertar un documento");
        return false;
      }
      var archivo = documentoInput.files[0];
      var extensionesPermitidas = ['pdf'];
      var extension = archivo.name.split('.').pop().toLowerCase();

      if (!extensionesPermitidas.includes(extension)) {
        alert("Solo se admite PDF.");
        return false;
      }

    }

    if (fechaInicio) {
      // Validación de la fecha de inicio
      var fechaInicioArray = fechaInicio.split("-");
      if (fechaInicioArray.length !== 3) {
        alert("Por favor, introduzca una fecha de inicio válida.");
        return false;
      }
      var yearInicio = fechaInicioArray[0];
      var monthInicio = fechaInicioArray[1];
      var dayInicio = fechaInicioArray[2];

      // Verificar si el año tiene 4 dígitos
      if (yearInicio.length !== 4 || isNaN(yearInicio)) {
        alert("Por favor, introduzca un año entre 0001 y 9999 en la fecha de inicio.");
        return false;
      }

      // Crear objeto de fecha de inicio y verificar si es válida
      var fechaInicioObjeto = new Date(yearInicio, monthInicio - 1, dayInicio);
      if (isNaN(fechaInicioObjeto.getTime())) {
        alert("Por favor, introduzca una fecha de inicio válida.");
        return false;
      }
    }

    if (fechaFin) {
      // Validación de la fecha de finalización

      var fechaFinArray = fechaFin.split("-");
      if (fechaFinArray.length !== 3) {
        alert("Por favor, introduzca una fecha de finalización válida.");
        return false;
      }
      var yearFin = fechaFinArray[0];
      var monthFin = fechaFinArray[1];
      var dayFin = fechaFinArray[2];

      // Verificar si el año tiene 4 dígitos
      if (yearFin.length !== 4 || isNaN(yearFin)) {
        alert("Por favor, introduzca un año entre 0001 y 9999 en la fecha de finalización.");
        return false;
      }

      // Crear objeto de fecha de finalización y verificar si es válida
      var fechaFinObjeto = new Date(yearFin, monthFin - 1, dayFin);
      if (isNaN(fechaFinObjeto.getTime())) {
        alert("Por favor, introduzca una fecha de finalización válida.");
        return false;
      }

      // Validación de la fecha de inicio
      var fechaInicioObjeto = new Date(fechaInicio);
      if (isNaN(fechaInicioObjeto.getTime())) {
        alert("Por favor, introduzca una fecha de inicio válida.");
        return false;
      }

      // Validación de la fecha de finalización
      var fechaFinObjeto = new Date(fechaFin);
      if (isNaN(fechaFinObjeto.getTime())) {
        alert("Por favor, introduzca una fecha de finalización válida.");
        return false;
      }

      // Verificar que la fecha de finalización no sea menor que la fecha de inicio
      if (fechaFinObjeto < fechaInicioObjeto) {
        alert("La fecha de finalización no puede ser menor que la fecha de inicio.");
        return false;
      }
    }

    if (tituloInput) {
      var tituloNoticia = tituloInput.value;
      if (tituloNoticia.trim() === "") {
        /* usando la funcion trim validamos que quitando los espacios no se envie una cadena vacia*/
        alert("El título no puede quedar vacío."); /*alert mostrado para advertir al usuario*/
        return false; /* retornar falso para impedir el envio del form*/
      }

    }

    if (mensajeInput) {
      var mensajeCon = mensajeInput.value;
      if (mensajeCon.trim() === "") {
        alert("El mensaje no debe estar vacío");
        return false;
      }
    }

    if (cuerpoInput) {
      var cuerpoNoticia = cuerpoInput.value;
      if (cuerpoNoticia.trim() === "") {
        alert("La noticia debe tener un cuerpo.");
        return false;
      }

    }

    if (deporteInput) {
      var deporte = deporteInput.value;
      if (deporte === "") {
        alert("El deporte no puede quedar vacio");
        return false;
      }
    }

    if (ubicacionInput) {
      var ubicacion = ubicacionInput.value;
      var ubicacion1 = trim(ubicacion);
      if (ubicacion1 === "") {
        alert("El escenario debe tener un enlace de ubicación");
        return false;
      }

    }


    if (direccionInput) {
      var direccion = direccionInput.value;
      var direccion1 = trim(direccion);
      if (direccion1 === "") {
        alert("El escenario debe tener la dirección");
        return false;
      }

    }


    if (telefonoInput) {
      var telefono = telefonoInput.value;
      var telefono1 = trim(telefono);
      if (telefono1 === "") {
        alert("Debe proporcionar un número de contacto");
        return false;
      }

    }


    if (supervisorInput) {
      var supervisor = supervisorInput.value;
      var supervisor1 = trim(supervisor);
      if (supervisor1 === "") {
        alert("Debe asignar un encargado para este escenario");
        return false;
      }

    }


    if (celularInput) {
      var celular = celularInput.value;
      var celular1 = trim(celular);
      if (celular1 === "") {
        alert("Debe proporcionar el número de contacto del supervisor");
        return false;
      }

    }

    return true;
  }
</script>