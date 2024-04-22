<script>
    var main = "<?php echo $main ?>";
    /* declarar la variable main que contiene 
       el nombre de la clase con la que se trabajara*/
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
    /* enviar el form al script de insersión */
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

    // Función para abrir el modal de edicion
    function openModal() {
        var modal = document.getElementById("modalEdit" + main + "s");
        modal.style.display = "block";
    }

    // Función para cerrar el modal de edicion
    function closeModal() {
        var modal = document.getElementById("modalEdit" + main + "s");
        modal.style.display = "none";
    }

    // Cierra el modal  de edicion si se hace clic fuera de él
    window.onclick = function(event) {
        var modal = document.getElementById("modalEdit" + main + "s");
        if (event.target == modal) {
            modal.style.display = "none";
        }
    }

    function loadForm(idEditar) {
    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            document.getElementById("formContent").innerHTML = this.responseText;
            document.getElementById("id" + main + "Edit").value = idEditar; // Establecer el ID del documentita en el formulario
            openModal(); // Abre el modal después de cargar el contenido
            
            // Función para verificar la existencia del elemento y limitar la cantidad de caracteres
            function setupElementListener(elementId, maxLength, numericOnly) {
                var element = document.getElementById(elementId);
                if (element) {
                    element.addEventListener('input', function() {
                        var value = this.value;
                        if (value.length > maxLength) {
                            this.value = value.slice(0, maxLength);
                        }
                        if (numericOnly) {
                            this.value = this.value.replace(/\D/g, '');
                        }
                    });
                }
            }
            
            // Configurar cada elemento con su respectiva función de limitación de caracteres y eliminación de caracteres no numéricos
            setupElementListener('nombreEdit', 200);
            setupElementListener('descripcionEdit', 2000);
            setupElementListener('mensajeEdit', 5000);
            setupElementListener('ubicacionEdit', 5000);
            setupElementListener('direccionEdit', 500);
            setupElementListener('telefonoEdit', 10, true);
            setupElementListener('celularEdit', 10, true);
            setupElementListener('supervisorEdit', 250);
            setupElementListener('tituloEdit', 100);
            setupElementListener('cuerpoEdit', 5000);

        }
    };
    xhttp.open("GET", "formEdit" + main + ".php?id=" + idEditar, true);
    xhttp.send();
}

    function confirmarEliminacion(idEliminar) {
        var confirmacion = confirm("¿Está seguro que desea eliminar este elemento?");

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