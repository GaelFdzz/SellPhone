<?php
include '../config/database.php';

// Verificar conexión
if ($conexion->connect_error) {
    die("Connection failed: " . $conexion->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $correo = $_POST["correo"];
    $contrasena = $_POST["contrasena"];

    // Obtener el usuario de la base de datos
    $sql = "SELECT Id_Usuario, Contrasena FROM Usuarios WHERE Correo = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("s", $correo);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($id, $stored_password);

    if ($stmt->num_rows > 0) {
        $stmt->fetch();
        if ($contrasena === $stored_password) {
            session_start();
            $_SESSION['id_usuario'] = $id;
            echo "Login exitoso.";
            header("Location: ../views/home/index.php");
            exit();
        } else {
            echo "Contraseña incorrecta.";
        }
    } else {
        echo "No se encontró una cuenta con ese correo.";
    }
    $stmt->close();
}

$conexion->close();
?>
