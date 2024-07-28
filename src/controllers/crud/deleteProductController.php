<?php
// Iniciar sesión
session_start();
// Incluir la conexión a la base de datos
include '../../config/database.php';

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['usuario_id'])) {
    // Redirigir a la página de inicio de sesión si no ha iniciado sesión
    header("Location: /src/views/user/login.php");
    exit();
}

// Obtener la acción y el ID del producto
$action = $_GET['action'];
$id_producto = $_GET['id'];

if ($action === 'delete') {
    // Consultar la imagen del producto antes de eliminarlo
    $sql = "SELECT Imagen FROM Productos WHERE Id_Producto = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("i", $id_producto);
    $stmt->execute();
    $result = $stmt->get_result();
    $producto = $result->fetch_assoc();

    // Eliminar los registros en detalle_pedidos que referencian el producto
    $sql = "DELETE FROM Detalle_Pedidos WHERE Id_Producto = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("i", $id_producto);
    $stmt->execute();

    // Eliminar los registros en resenas que referencian el producto
    $sql = "DELETE FROM Resenas WHERE Id_Producto = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("i", $id_producto);
    $stmt->execute();

    // Eliminar el producto de la base de datos
    $sql = "DELETE FROM Productos WHERE Id_Producto = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("i", $id_producto);

    if ($stmt->execute()) {
        // Eliminar la imagen del servidor
        $imagen_path = realpath(__DIR__ . '/../../public/imagesUploaded/') . '/' . basename($producto['Imagen']);
        if (file_exists($imagen_path)) {
            unlink($imagen_path);
        }
        // Redirigir de vuelta a la página de gestión de productos
        header("Location: /src/views/product/crud.php");
        exit();
    } else {
        echo "Error al eliminar el producto.";
    }
}
?>
