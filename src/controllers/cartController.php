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
$producto_id = isset($_GET['producto_id']) ? intval($_GET['producto_id']) : 0;

// Función para agregar producto al carrito
function addToCart($conexion, $usuario_id, $producto_id) {
    $carrito_id = null;
    // Obtener el ID del carrito del usuario
    $sql = "SELECT Id_Carrito FROM Carrito WHERE Id_Usuario = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("i", $usuario_id);
    $stmt->execute();
    $stmt->bind_result($carrito_id);
    $stmt->fetch();
    $stmt->close();

    // Si el usuario no tiene un carrito, crear uno
    if (!$carrito_id) {
        $sql = "INSERT INTO Carrito (Id_Usuario) VALUES (?)";
        $stmt = $conexion->prepare($sql);
        $stmt->bind_param("i", $usuario_id);
        $stmt->execute();
        $carrito_id = $stmt->insert_id;
        $stmt->close();
    }

    $cantidad = null; // Inicializar la cantidad vacía
    // Verificar si el producto ya está en el carrito
    $sql = "SELECT Cantidad FROM Detalles_Carrito WHERE Id_Carrito = ? AND Id_Producto = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("ii", $carrito_id, $producto_id);
    $stmt->execute();
    $stmt->bind_result($cantidad);
    $stmt->fetch();
    $stmt->close();

    if ($cantidad) {
        // Si el producto ya está en el carrito, actualizar la cantidad
        $sql = "UPDATE Detalles_Carrito SET Cantidad = Cantidad + 1 WHERE Id_Carrito = ? AND Id_Producto = ?";
    } else {
        // Si el producto no está en el carrito, agregarlo
        $sql = "INSERT INTO Detalles_Carrito (Id_Carrito, Id_Producto, Cantidad, Precio) SELECT ?, Id_Producto, 1, Precio FROM Productos WHERE Id_Producto = ?";
    }

    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("ii", $carrito_id, $producto_id);
    $stmt->execute();
    $stmt->close();
}

// Agregar el producto al carrito
if ($producto_id > 0) {
    addToCart($conexion, $usuario_id, $producto_id);
    $mensaje = "Producto añadido al carrito";
    // Redirigir al carrito de compras
    header("Location: /src/views/order/cart.php?mensaje=" . urlencode($mensaje));
    exit();
} else {
    // Redirigir al catálogo si no se proporciona un ID de producto válido
    header("Location: /src/views/home/index.php");
    exit();
}
?>
