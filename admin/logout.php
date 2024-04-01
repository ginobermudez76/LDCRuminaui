<?php
session_start();

session_unset();//eliminar todas las variables de la sesion 

session_destroy();//destruir la session

echo "<script>window.location.href='../admin/login.php';</script>";//redirigir al usuario al login
exit();

?>