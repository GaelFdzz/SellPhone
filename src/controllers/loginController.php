<?php
// Iniciar sesión
session_start();

// Incluir la conexión a la base de datos
include '../config/database.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $correo = $_POST["correo"];
    $contrasena = $_POST["contrasena"];

    // Consulta para verificar las credenciales del usuario
    $sql = "SELECT Id_Usuario FROM Usuarios WHERE Correo = ? AND Contrasena = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("ss", $correo, $contrasena);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows == 1) {
        // Usuario y contraseña correctos
        $stmt->bind_result($usuario_id);
        $stmt->fetch();
        $_SESSION['usuario_id'] = $usuario_id; // Configurar la variable de sesión

        // Redirigir al usuario a la página principal
        header("Location: /src/views/home/index.php");
        exit();
    } else {
        echo "Correo o contraseña incorrectos";
    }
    $stmt->close();
}

$conexion->close();
?>
