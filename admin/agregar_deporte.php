<?php 
session_start();
if (!isset($_SESSION['usuario_admin'])) {
    header("Location: /Ayudantias-1/admin/login.php");
    exit();
}
include '../includes/config.php'; //incluyebdo la conexion de la base de datos
include '../includes/header.php'; //incluyendlo la cabecera comun



if($_SERVER["REQUEST_METHOD"]=="POST"){

    $nombre = $_POST['nombre'];
    $descripcion = $_POST['descripcion'];


    if(isset($_FILES['imagen']) && $_FILES['imagen']['error'] == 0){

        $directorioDestino = "../uploads/deportes/";

        $archivoImagen = $directorioDestino . basename($_FILES['imagen']['name']);
        
        $tipoArchivo = strtolower(pathinfo($archivoImagen, PATHINFO_EXTENSION));

        $check = getimagesize($_FILES["imagen"]["tmp_name"]);

        if($check != false){
            if(move_uploaded_file($_FILES["imagen"]["tmp_name"], $archivoImagen)){
                //la imagen se cargo correctamente

            }else{
                $error = "Hubo un error al cargar la imagen";
            }
        }else{
            $error ="El archivo no es una imagen";
        }
    }else{
        //manejo en el caso de que la imagen no se cargue una imagen
        $archivoImagen = "";
    }

    //insertar en la base de datos (con o sin imagen)

    try{
        $stmt = $conn -> prepare ("INSERT INTO deportes (nombre, descripcion, imagen) VALUES (?, ?, ?)");
        $stmt -> execute([$nombre, $descripcion, $archivoImagen]);

        //redirigir despues de agregar

        header("Location: gestionar_deportes.php");
        exit();
    }catch(PDOException $e){
        echo "Error: " . $e ->getMessage();

    }
}
?>

<div class="container mt-4">
    <h2>Agregar Deporte</h2>
    <?php if(!empty($error)):?>
    <div class="alert alert-danger">
    <?php echo $error; ?>
    </div>
    <?php endif;?>
         <form action ="agregar_deporte.php" method="post" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="nombre" class="form-label">Nombre del Deporte</label>
                <input type="text" class="form-control" id="nombre" name="nombre" requerid>
            </div>
            <div class="mb-3">
                <label for="descripcion" class="form-label">Descripción</label>
                <textarea type="text" class="form-control" id="descripcion" name="descripcion" requerid></textarea>
            </div>
            <div class="mb-3">
                <label for="imagen" class="form-label">Imagen</label>
                <input type="file" class="form-control" id="imagen" name="imagen" requerid></textarea>
            </div>
            <button type="submit" class= "btn btn-primary">Agregar nuevo deporte</button>
        </form> 
</div>
<?php include '../includes/footer.php';?>