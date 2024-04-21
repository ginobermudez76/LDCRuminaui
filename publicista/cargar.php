<script>
    var main = "<?php echo $main ?>";
    $("#tabla" + main + "s").load("tabla" + main + "s.php");
    $(document).ready(function() {
        var main = "<?php echo $main ?>";
        <?php $main ?>
        $("#tabla" + main + "s").load("tabla" + main + "s.php");

    });

    // Función para manejar la respuesta del servidor
    function handleResponse(response) {
        if (response.success) {
            alertify.success(response.message);
            // Recargar la página después de 1.5 segundos
            $('#form' + main)[0].reset();
            $("#tabla" + main + "s").load("tabla" + main + "s.php");
        } else {
            alertify.error(response.message);
        }
    }

    $(document).ready(function() {
        // Manejar el envío del formulario
        $('#form' + main).submit(function(event) {
            event.preventDefault(); // Evitar el envío del formulario por defecto
            var formData = new FormData($(this)[0]); // Obtener los datos del formulario
            $.ajax({
                url: 'insertar' + main + '.php',
                type: 'POST',
                data: formData,
                async: false,
                success: function(response) {
                    handleResponse(JSON.parse(response));
                },
                cache: false,
                contentType: false,
                processData: false
            });
            return false;
        });
    });

    // Función para abrir el modal
    function openModal() {
        var modal = document.getElementById("modalEdit" + main + "s");
        modal.style.display = "block";
    }

    // Función para cerrar el modal
    function closeModal() {
        var modal = document.getElementById("modalEdit" + main + "s");
        modal.style.display = "none";
    }

    // Cierra el modal si se hace clic fuera de él
    window.onclick = function(event) {
        var modal = document.getElementById("modalEdit" + main + "s");
        if (event.target == modal) {
            modal.style.display = "none";
        }
    }

    // Carga el formulario desde el otro script PHP cuando se abre el modal
    function loadForm(idEditar) {
        var xhttp = new XMLHttpRequest();
        xhttp.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
                document.getElementById("formContent").innerHTML = this.responseText;
                document.getElementById("id" + main + "Edit").value = idEditar; // Establecer el ID del documentita en el formulario
                openModal(); // Abre el modal después de cargar el contenido
                document.getElementById('nombreEdit').addEventListener('input', function() {
                    // Obtener el valor actual del campo de celular
                    var documentoNombre = this.value;
                    // Limitar el valor a 100 caracteres
                    if (documentoNombre.length > 200) {
                        this.value = documentoNombre.slice(0, 200);
                    }
                });
                // Función para limitar la cantidad de dígitos en el campo de descripcion
                document.getElementById('descripcionEdit').addEventListener('input', function() {
                    // Obtener el valor actual del campo de celular
                    var documentoDescripcion = this.value;
                    // Limitar el valor a 10 caracteres
                    if (documentoDescripcion.length > 2000) {
                        this.value = documentoDescripcion.slice(0, 2000);
                    }
                });
                document.getElementById("mensajeEdit").addEventListener("input", function() {
                    // Obtener el valor actual del campo de mensaje
                    var mensajeCondolencia = this.value;
                    // Limitar el valor a 700 caracteres
                    if (mensajeCondolencia.length > 5000) {
                        this.value = mensajeCondolencia.slice(0, 5000);
                    }
                });
                document.getElementById('ubicacionEdit').addEventListener('input', function() {
                    // Obtener el valor actual del campo de celular
                    var ubicacion = this.value;
                    // Limitar el valor a 100 caracteres
                    if (ubicacion.length > 5000) {
                        this.value = ubicacion.slice(0, 5000);
                    }
                });
                document.getElementById('direccionEdit').addEventListener('input', function() {
                    // Obtener el valor actual del campo de celular
                    var direccion = this.value;
                    // Limitar el valor a 100 caracteres
                    if (direccion.length > 500) {
                        this.value = direccion.slice(0, 500);
                    }
                });
                document.getElementById('telefonoEdit').addEventListener('input', function() {
                    // Obtener el valor actual del campo de celular
                    var telefono = this.value;
                    // Limitar el valor a 100 caracteres
                    if (telefono.length > 10) {
                        this.value = telefono.slice(0, 10);
                    }
                });
                document.getElementById('celularEdit').addEventListener('input', function() {
                    // Obtener el valor actual del campo de celular
                    var celular = this.value;
                    // Limitar el valor a 100 caracteres
                    if (celular.length > 10) {
                        this.value = celular.slice(0, 10);
                    }
                });
                document.getElementById('supervisorEdit').addEventListener('input', function() {
                    // Obtener el valor actual del campo de celular
                    var supervisor = this.value;
                    // Limitar el valor a 100 caracteres
                    if (supervisor.length > 250) {
                        this.value = supervisor.slice(0, 250);
                    }
                });
                document.getElementById('telefonoEdit').addEventListener('input', function() {
                    // Obtener el valor actual del campo de teléfono
                    var telefono = this.value;
                    // Quitar todos los caracteres que no sean números
                    var numerosTelefono = telefono.replace(/\D/g, '');
                    // Actualizar el valor del campo con solo los números
                    this.value = numerosTelefono;
                });

                document.getElementById('celularEdit').addEventListener('input', function() {
                    // Obtener el valor actual del campo de celular
                    var celular = this.value;
                    // Quitar todos los caracteres que no sean números
                    var numerosCelular = celular.replace(/\D/g, '');
                    // Actualizar el valor del campo con solo los números
                    this.value = numerosCelular;
                });
                document.getElementById('tituloEdit').addEventListener('input', function() {
                    // Obtener el valor actual del campo de celular
                    var titulo = this.value;
                    // Limitar el valor a 100 caracteres
                    if (titulo.length > 100) {
                        this.value = titulo.slice(0, 100);
                    }
                });
                document.getElementById('cuerpoEdit').addEventListener('input', function() {
                    // Obtener el valor actual del campo de celular
                    var cuerpo = this.value;
                    // Limitar el valor a 100 caracteres
                    if (cuerpo.length > 5000) {
                        this.value = cuerpo.slice(0, 5000);
                    }
                });
            }
        };
        xhttp.open("GET", "formEdit" + main + ".php?id=" + idEditar, true); // Pasar el ID del documento en la URL
        xhttp.send();
    }

    function confirmarEliminacion(idEliminar) {
        var confirmacion = confirm("¿Está seguro que desea eliminar este documento?");

        if (confirmacion) {
            // Usuario hizo clic en "Aceptar", enviar solicitud a eliminar_documento.php
            eliminarDocumento(idEliminar);
        } else {
            // Usuario hizo clic en "Cancelar", no hacer nada
        }
    }

    function eliminarDocumento(idEliminar) {
        // Utiliza jQuery para enviar una solicitud AJAX a eliminar_documento.php
        $.ajax({
            type: "POST",
            url: "eliminar" + main + ".php",
            data: {
                id: idEliminar
            },
            success: function(response) {
                // Manejar la respuesta, si es necesario
                console.log(response);
                alert(response);
                // Puedes recargar la página o actualizar la lista de documentos de alguna manera
                $("#tabla" + main + "s").load("tabla" + main + "s.php");
            },
            error: function(error) {
                // Manejar errores si es necesario
                alert(response);
                console.error(error);
            }
        });
    }
</script>