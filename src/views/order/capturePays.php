<?php
session_start();
// Incluir la conexión a la base de datos
include '../../config/database.php';

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['usuario_id'])) {
    // Redirigir a la página de inicio de sesión si no ha iniciado sesión
    header("Location: /src/views/user/login.php");
    exit();
}

// Obtener el ID del usuario de la sesión
$usuario_id = $_SESSION['usuario_id'];

$json = file_get_contents('php://input');
$datos = json_decode($json, true);

if (is_array($datos) && isset($datos['id']) && isset($datos['purchase_units'][0])) {
    $id_transaction = $datos['id'];
    $amount = $datos['purchase_units'][0]['amount']['value'];
    $currency = $datos['purchase_units'][0]['amount']['currency_code'];
    $status = $datos['status'];
    $create_time = $datos['create_time'];
    $update_time = $datos['update_time'];

    // Convertir las fechas al formato adecuado
    $create_date = date('Y-m-d', strtotime($create_time));
    $update_date = date('Y-m-d', strtotime($update_time));

    // Comenzar una transacción
    $conexion->begin_transaction();

    try {
        // Guardar la transacción en la tabla Pagos
        $sql = "INSERT INTO Pagos (Monto, Fecha_Pago, Id_Usuario) VALUES (?, ?, ?)";
        $stmt = $conexion->prepare($sql);
        if (!$stmt) {
            throw new Exception("Error preparando la consulta: " . $conexion->error);
        }
        $stmt->bind_param("dsi", $amount, $create_date, $usuario_id);
        if (!$stmt->execute()) {
            throw new Exception("Error ejecutando la consulta: " . $stmt->error);
        }
        $pago_id = $stmt->insert_id;

        // Crear el pedido en la tabla Pedidos
        $sql = "INSERT INTO Pedidos (Fecha_Pedido, Estado, Id_Usuario, Id_Pago, Total_Cantidad, Total_Precio) VALUES (?, ?, ?, ?, 0, 0)";
        $estado_pedido = 'PAGADO';
        $stmt = $conexion->prepare($sql);
        if (!$stmt) {
            throw new Exception("Error preparando la consulta: " . $conexion->error);
        }
        $stmt->bind_param("ssii", $create_date, $estado_pedido, $usuario_id, $pago_id);
        if (!$stmt->execute()) {
            throw new Exception("Error ejecutando la consulta: " . $stmt->error);
        }
        $pedido_id = $stmt->insert_id;

        // Obtener los detalles del carrito
        $cartItems = getCartDetails($conexion, $usuario_id);
        if (!$cartItems) {
            throw new Exception("Error obteniendo detalles del carrito.");
        }

        // Variables para calcular el total del pedido
        $totalCantidad = 0;
        $totalPrecio = 0.0;

        // Agregar detalles del pedido en la tabla Detalle_Pedidos
        foreach ($cartItems as $item) {
            $sql = "INSERT INTO Detalle_Pedidos (Cantidad, Precio, Id_Pedido, Id_Producto) VALUES (?, ?, ?, ?)";
            $stmt = $conexion->prepare($sql);
            if (!$stmt) {
                throw new Exception("Error preparando la consulta: " . $conexion->error);
            }
            $stmt->bind_param("idii", $item['Cantidad'], $item['Precio'], $pedido_id, $item['Id_Producto']);
            if (!$stmt->execute()) {
                throw new Exception("Error ejecutando la consulta: " . $stmt->error);
            }

            // Sumar los totales
            $totalCantidad += $item['Cantidad'];
            $totalPrecio += $item['Cantidad'] * $item['Precio'];
        }

        // Actualizar la tabla Pedidos con los valores calculados
        $sql = "UPDATE Pedidos SET Total_Cantidad = ?, Total_Precio = ? WHERE Id_Pedido = ?";
        $stmt = $conexion->prepare($sql);
        if (!$stmt) {
            throw new Exception("Error preparando la consulta: " . $conexion->error);
        }
        $stmt->bind_param("idi", $totalCantidad, $totalPrecio, $pedido_id);
        if (!$stmt->execute()) {
            throw new Exception("Error ejecutando la consulta: " . $stmt->error);
        }

        // Limpiar el carrito
        $cartId = getCartIdByUserId($conexion, $usuario_id);
        if (!$cartId) {
            throw new Exception("Error obteniendo el ID del carrito.");
        }
        $sql = "DELETE FROM Detalles_Carrito WHERE Id_Carrito = ?";
        $stmt = $conexion->prepare($sql);
        if (!$stmt) {
            throw new Exception("Error preparando la consulta: " . $conexion->error);
        }
        $stmt->bind_param("i", $cartId);
        if (!$stmt->execute()) {
            throw new Exception("Error ejecutando la consulta: " . $stmt->error);
        }

        // Confirmar la transacción
        $conexion->commit();

        echo json_encode(['success' => true]);
    } catch (Exception $e) {
        // Revertir la transacción en caso de error
        $conexion->rollback();
        echo json_encode(['success' => false, 'message' => 'Error al procesar el pago: ' . $e->getMessage()]);
    }

    $stmt->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Datos incompletos recibidos.']);
}
?>

<?php
// Función para obtener los detalles del carrito
function getCartDetails($conexion, $usuario_id) {
    $sql = "SELECT d.Cantidad, p.Precio, d.Id_Producto
            FROM Detalles_Carrito d
            JOIN Productos p ON d.Id_Producto = p.Id_Producto
            JOIN Carrito c ON d.Id_Carrito = c.Id_Carrito
            WHERE c.Id_Usuario = ?";
    $stmt = $conexion->prepare($sql);
    if (!$stmt) {
        throw new Exception("Error preparando la consulta: " . $conexion->error);
    }
    $stmt->bind_param("i", $usuario_id);
    if (!$stmt->execute()) {
        throw new Exception("Error ejecutando la consulta: " . $stmt->error);
    }
    $result = $stmt->get_result();
    $cartItems = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    return $cartItems;
}

// Función para obtener el ID del carrito por el ID del usuario
function getCartIdByUserId($conexion, $usuario_id) {
    $cartId = null;
    $sql = "SELECT Id_Carrito FROM Carrito WHERE Id_Usuario = ?";
    $stmt = $conexion->prepare($sql);
    if (!$stmt) {
        throw new Exception("Error preparando la consulta: " . $conexion->error);
    }
    $stmt->bind_param("i", $usuario_id);
    if (!$stmt->execute()) {
        throw new Exception("Error ejecutando la consulta: " . $stmt->error);
    }
    $stmt->bind_result($cartId);
    $stmt->fetch();
    $stmt->close();
    return $cartId;
}
?>
