<?php
$contrasena = 'contraseña123';
$hash = password_hash($contrasena, PASSWORD_DEFAULT);
echo $hash;
?>