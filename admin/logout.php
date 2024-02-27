<?php
session_start();

session_unset();//eliminar todas las variables de la sesion 

session_destroy();//destruir la session

header('Location: ../admin/login.php');//redirigir al usuario al login
exit();

?>