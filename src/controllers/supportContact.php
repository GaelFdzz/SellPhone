<?php
session_start();
include '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = $_POST['name'];
    $correo_electronico = $_POST['email'];
    $asunto = $_POST['subject'];
    $mensaje = $_POST['message'];

    $sql = "INSERT INTO Mensajes (Nombre, Correo_Electronico, Asunto, Mensaje) VALUES (?, ?, ?, ?)";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("ssss", $nombre, $correo_electronico, $asunto, $mensaje);

    if ($stmt->execute()) {
        $_SESSION['mensaje_exito'] = "Mensaje enviado correctamente.";
    } else {
        $_SESSION['mensaje_error'] = "Error al enviar el mensaje: " . $stmt->error;
    }

    header('Location: /src/views/home/soporteContacto.php');
    exit();
}
