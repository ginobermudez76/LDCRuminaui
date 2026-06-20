<?php
session_start();
$_SESSION = array();

// Elimina la cookie de sesión si existe
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}
session_unset();//eliminar todas las variables de la sesion 

session_destroy();//destruir la session

echo "<script>window.location.href='../admin/login.php';</script>";//redirigir al usuario al login
exit();

?>