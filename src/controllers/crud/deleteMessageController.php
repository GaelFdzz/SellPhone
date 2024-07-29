<?php
// Iniciar sesión
session_start();
// Incluir la conexión a la base de datos
include '../../config/database.php';

// Verificar si se ha recibido una solicitud de eliminación
if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $id_mensaje = intval($_GET['id']);

    // Eliminar el mensaje de la base de datos
    $sql = "DELETE FROM Mensajes WHERE Id_Mensaje = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("i", $id_mensaje);
    $stmt->execute();

    // Redirigir de vuelta a la página de gestión de productos
    header("Location: /src/views/product/crud.php");
    exit();
}
?>
