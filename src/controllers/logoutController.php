<?php
// Iniciar sesión
session_start();

// Destruir todas las sesiones
session_destroy();

// Redirigir a la página de inicio de sesión
header("Location: /src/views/user/login.php");
exit();
?>
