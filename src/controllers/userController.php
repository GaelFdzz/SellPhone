<?php
// Iniciar sesión
session_start();
// Incluir la conexión a la base de datos
include '../config/database.php';

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['usuario_id'])) {
    // Redirigir a la página de inicio de sesión si no ha iniciado sesión
    header("Location: /src/views/user/login.php");
    exit();
}

// Obtener el ID del usuario de la sesión
$usuario_id = $_SESSION['usuario_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = $_POST['nombre'];
    $apellido = $_POST['apellido'];
    $correo = $_POST['correo'];
    $contrasena = !empty($_POST['contrasena']) ? $_POST['contrasena'] : null;

    $sql = "UPDATE Usuarios SET Nombre = ?, Apellido = ?, Correo = ?" . ($contrasena ? ", Contrasena = ?" : "") . " WHERE Id_Usuario = ?";
    $stmt = $conexion->prepare($sql);
    if ($contrasena) {
        $stmt->bind_param("ssssi", $nombre, $apellido, $correo, $contrasena, $usuario_id);
    } else {
        $stmt->bind_param("sssi", $nombre, $apellido, $correo, $usuario_id);
    }
    if ($stmt->execute()) {
        // Redirigir a la página de perfil después de la actualización exitosa
        header("Location: /src/views/user/profile.php");
    } else {
        // Manejar errores si la actualización falla
        echo "Error al actualizar el perfil.";
    }
    exit();
} else {
    // Redirigir si no se recibió una solicitud POST
    header("Location: /src/views/user/profile.php");
    exit();
}
?>
