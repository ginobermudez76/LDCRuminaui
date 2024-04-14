<script>
    if (tituloInput) {
        /* comprobar que el input exista en el archivo, caso contrario se sigue con las demas limitaciones*/
        tituloInput.addEventListener('input', function() {
            /*evento de escucha para el input*/
            // Obtener el valor actual del input
            var tituloInput = this.value;
            // Limitar el valor a 100 caracteres
            if (tituloInput.length > 100) {
                this.value = tituloInput.slice(0, 100);
            }


        });
    }

    if (nombreInput) {
        nombreInput.addEventListener("input", function() {
            var nombre = this.value;
            if (nombre.length > 100) {
                this.value = nombre.slice(0, 100);
            }
        });
    }

    if (mensajeInput) {
        mensajeInput.addEventListener("input", function() {
            var mensaje = this.value;
            if (mensaje.length > 5000) {
                this.value = mensaje.slice(0, 5000);
            }
        });
    }
    if (cuerpoInput) {
        cuerpoInput.addEventListener("input", function() {
            var cuerpo = this.value;
            if (cuerpo.length > 5000) {
                this.value = cuerpo.slice(0, 5000);
            }
        });
    }
    if (ubicacionInput) {
        ubicacionInput.addEventListener("input", function() {
            var ubicacion = this.value;
            if (ubicacion.length > 5000) {
                this.value = ubicacion.slice(0, 5000);
            }
        });
    }
    if (direccionInput) {
        direccionInput.addEventListener("input", function() {
            var direccion = this.value;
            if (direccion.length > 500) {
                this.value = direccion.slice(0, 500);
            }
        });
    }
    if (telefonoInput) {
        telefonoInput.addEventListener("input", function() {
            var telefono = this.value;
            var numerosTelefono = telefono.replace(/\D/g, '');
            this.value = numerosTelefono;
            if (numerosTelefono.length > 10) {
                this.value = numerosTelefono.slice(0, 10);
            }
        });
    }
    if (supervisorInput) {
        supervisorInput.addEventListener("input", function() {
            var supervisor = this.value;
            if (supervisor.length > 250) {
                this.value = supervisor.slice(0, 250);
            }
        });
    }
    if (celularInput) {
        celularInput.addEventListener("input", function() {
            var celular = this.value;
            var numerosCelular = celular.replace(/\D/g, '');
            this.value = numerosCelular;
            if (numerosCelular.length > 10) {
                this.value = numerosCelular.slice(0, 10);
            }
        });
    }
    if (descripcionInput) {
        descripcionInput.addEventListener('input', function() {
            // Obtener el valor actual del campo de celular
            var documentoDescripcion = this.value;
            // Limitar el valor a 10 caracteres
            if (documentoDescripcion.length > 2000) {
                this.value = documentoDescripcion.slice(0, 2000);
            }
        });
    }
</script>