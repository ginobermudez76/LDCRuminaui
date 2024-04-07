document.addEventListener("DOMContentLoaded", function () {
  // Tu código JavaScript aquí
  // Agregar listeners de eventos a elementos terminados en "Edit"
  // Función para limitar la cantidad de dígitos en el campo de celular
  document.getElementById("nombreEdit").addEventListener("input", function () {
    // Obtener el valor actual del campo de celular
    var escenarioNombreEdit = this.value;
    // Limitar el valor a 100 caracteres
    if (escenarioNombreEdit.length > 100) {
      this.value = escenarioNombreEdit.slice(0, 100);
    }
  });
  document
    .getElementById("ubicacionEdit")
    .addEventListener("input", function () {
      // Obtener el valor actual del campo de celular
      var ubicacionEdit = this.value;
      // Limitar el valor a 100 caracteres
      if (ubicacionEdit.length > 5000) {
        this.value = ubicacionEdit.slice(0, 5000);
      }
    });
  document
    .getElementById("direccionEdit")
    .addEventListener("input", function () {
      // Obtener el valor actual del campo de celular
      var direccionEdit = this.value;
      // Limitar el valor a 100 caracteres
      if (direccionEdit.length > 500) {
        this.value = direccionEdit.slice(0, 500);
      }
    });
  document
    .getElementById("telefonoEdit")
    .addEventListener("input", function () {
      // Obtener el valor actual del campo de celular
      var telefonoEdit = this.value;
      // Limitar el valor a 100 caracteres
      if (telefonoEdit.length > 10) {
        this.value = telefonoEdit.slice(0, 10);
      }
    });
  document.getElementById("celularEdit").addEventListener("input", function () {
    // Obtener el valor actual del campo de celular
    var celularEdit = this.value;
    // Limitar el valor a 100 caracteres
    if (celularEdit.length > 10) {
      this.value = celularEdit.slice(0, 10);
    }
  });
  document
    .getElementById("supervisorEdit")
    .addEventListener("input", function () {
      // Obtener el valor actual del campo de celular
      var supervisorEdit = this.value;
      // Limitar el valor a 100 caracteres
      if (supervisorEdit.length > 250) {
        this.value = supervisorEdit.slice(0, 250);
      }
    });
  // Función para limitar la cantidad de dígitos en los campos de teléfono y celular
  document
    .getElementById("telefonoEdit")
    .addEventListener("input", function () {
      // Obtener el valor actual del campo de teléfono
      var telefonoEdit = this.value;
      // Quitar todos los caracteres que no sean números
      var numerosTelefonoEdit = telefonoEdit.replace(/\D/g, "");
      // Actualizar el valor del campo con solo los números
      this.value = numerosTelefonoEdit;
    });

  document.getElementById("celularEdit").addEventListener("input", function () {
    // Obtener el valor actual del campo de celular
    var celularEdit = this.value;
    // Quitar todos los caracteres que no sean números
    var numerosCelularEdit = celularEdit.replace(/\D/g, "");
    // Actualizar el valor del campo con solo los números
    this.value = numerosCelularEdit;
  });
              // Función para limitar la cantidad de dígitos en el campo de celular
              document.getElementById('nombre').addEventListener('input', function() {
                // Obtener el valor actual del campo de celular
                var escenarioNombre = this.value;
                // Limitar el valor a 100 caracteres
                if (escenarioNombre.length > 100) {
                    this.value = escenarioNombre.slice(0, 100);
                }
            });
            document.getElementById('ubicacion').addEventListener('input', function() {
                // Obtener el valor actual del campo de celular
                var ubicacion = this.value;
                // Limitar el valor a 100 caracteres
                if (ubicacion.length > 5000) {
                    this.value = ubicacion.slice(0, 5000);
                }
            });
            document.getElementById('direccion').addEventListener('input', function() {
                // Obtener el valor actual del campo de celular
                var direccion = this.value;
                // Limitar el valor a 100 caracteres
                if (direccion.length > 500) {
                    this.value = direccion.slice(0, 500);
                }
            });
            document.getElementById('telefono').addEventListener('input', function() {
                // Obtener el valor actual del campo de celular
                var telefono = this.value;
                // Limitar el valor a 100 caracteres
                if (telefono.length > 10) {
                    this.value = telefono.slice(0, 10);
                }
            });
            document.getElementById('celular').addEventListener('input', function() {
                // Obtener el valor actual del campo de celular
                var celular = this.value;
                // Limitar el valor a 100 caracteres
                if (celular.length > 10) {
                    this.value = celular.slice(0, 10);
                }
            });
            document.getElementById('supervisor').addEventListener('input', function() {
                // Obtener el valor actual del campo de celular
                var supervisor = this.value;
                // Limitar el valor a 100 caracteres
                if (supervisor.length > 250) {
                    this.value = supervisor.slice(0, 250);
                }
            });
            document.getElementById('telefono').addEventListener('input', function() {
                // Obtener el valor actual del campo de teléfono
                var telefono = this.value;
                // Quitar todos los caracteres que no sean números
                var numerosTelefono = telefono.replace(/\D/g, '');
                // Actualizar el valor del campo con solo los números
                this.value = numerosTelefono;
            });

            document.getElementById('celular').addEventListener('input', function() {
                // Obtener el valor actual del campo de celular
                var celular = this.value;
                // Quitar todos los caracteres que no sean números
                var numerosCelular = celular.replace(/\D/g, '');
                // Actualizar el valor del campo con solo los números
                this.value = numerosCelular;
            });
});
