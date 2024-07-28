<?php
// Configuración de la base de datos
include '../config/database.php';

// Verificar conexión
if ($conexion->connect_error) {
    die("Connection failed: " . $conexion->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = $_POST["nombre"];
    $email = $_POST["correo"];
    $password = $_POST["contrasena"];
    $apellido = $_POST["apellido"];

    // Verificar si el email ya está registrado
    $sql = "SELECT id_usuario FROM Usuarios WHERE correo = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        echo "El email ya está registrado.";
    } else {
        // Insertar nuevo usuario
        $sql = "INSERT INTO Usuarios (Nombre, Apellido, Correo, Contrasena, Id_Rol) VALUES (?, ?, ?, ?, 3)";
        $stmt = $conexion->prepare($sql);
        $stmt->bind_param("ssss", $nombre, $apellido, $email, $password);

        if ($stmt->execute()) {
            echo "Registro exitoso.";
            header('location: /src/views/user/login.php');
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    }
    $stmt->close();
}

$conexion->close();
?>
