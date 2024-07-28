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

// Obtener el ID del usuario de la sesión
$usuario_id = $_SESSION['usuario_id'];

// Consultar el rol del usuario
$sql = "SELECT Id_Rol FROM Usuarios WHERE Id_Usuario = ?";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

$rol = $row['Id_Rol'];

// Obtener la página actual del navegador
$current_page = basename($_SERVER['REQUEST_URI'], ".php");

// Funciones para controlar el carrito
function getCartIdByUserId($conexion, $userId)
{
    $cartId = null;
    $sql = "SELECT Id_Carrito FROM Carrito WHERE Id_Usuario = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $stmt->bind_result($cartId);
    $stmt->fetch();
    $stmt->close();
    return $cartId;
}

function getCartDetails($conexion, $userId)
{
    $cartId = getCartIdByUserId($conexion, $userId);
    $sql = "SELECT p.Nombre, dc.Cantidad, dc.Precio, dc.Id_Detalle_Carrito, dc.Id_Producto 
            FROM Detalles_Carrito dc 
            JOIN Productos p ON dc.Id_Producto = p.Id_Producto 
            WHERE dc.Id_Carrito = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("i", $cartId);
    $stmt->execute();
    $result = $stmt->get_result();
    $cartItems = [];
    while ($row = $result->fetch_assoc()) {
        $cartItems[] = $row;
    }
    $stmt->close();
    return $cartItems;
}

$cartItems = getCartDetails($conexion, $usuario_id);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SellPhone - Carrito de Compras</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js" integrity="sha384-0pUGZvbkm6XF6gxjEnlmuGrJXVbNuzT9qBBavbLwCsOGabYfZo0T0to5eqruptLy" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="/public/css/navbar.css">
</head>

<body>
    <header>
        <div class="logo">
            <a href="" class="sellphone">SellPhone</a>
        </div>
        <nav>
            <a href="/src/views/home/index.php">Tienda</a>
            <a href="/src/views/home/soporteContacto.php">Contacto</a>
            <a href="/src/views/order/cart.php" class="<?php echo $current_page == 'cart' ? 'active' : ''; ?>">Carrito</a>
            <!-- Mostrar o no el enlace para el dashboard según el rol del usuario -->
            <?php if ($rol === 1) : ?>
                <a href="/src/views/product/crud.php">Dashboard</a>
            <?php endif; ?>
            <!-- Menu dropdown para el usuario logueado -->
            <a href="#" class="dropdown-toggle" id="perfilDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">Mi perfil</a>
            <ul class="dropdown-menu" aria-labelledby="perfilDropdown">
                <li><a class="dropdown-item" href="#">Ver perfil</a></li>
                <li>
                    <hr class="dropdown-divider">
                </li>
                <li><a class="dropdown-item" href="/src/controllers/logoutController.php">Cerrar sesión</a></li>
            </ul>
        </nav>
    </header>

    <div class="container mt-5">
        <div class="row">
            <div class="col-6">
                <h4>Detalles de pago</h4>
            </div>
            <div class="col-6">
                <h1 class="mb-4">Mi Carrito</h1>
                <?php if (count($cartItems) > 0) : ?>
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Producto</th>
                                <th>Cantidad</th>
                                <th>Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($cartItems as $item) : ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($item['Nombre']); ?></td>
                                    <td>
                                        <form method="POST" action="cart.php" class="d-inline">
                                            <input type="hidden" name="detalle_id" value="<?php echo $item['Id_Detalle_Carrito']; ?>">
                                            <p><?php echo $item['Cantidad']; ?></p>
                                        </form>
                                    </td>
                                    <td>$<?php echo number_format($item['Cantidad'] * $item['Precio'], 2); ?> MXN</td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="2" class="text-end">Total</td>
                                <td colspan="2">
                                    $<?php $total = array_sum(array_map(function ($item) {
                                            return $item['Cantidad'] * $item['Precio'];
                                        }, $cartItems));
                                        echo number_format($total, 2);
                                        ?> MXN
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                <?php else : ?>
                    <p>No hay productos en el carrito.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- METODO DE PAGO PAYPAL -->
    <div id="paypal-button-container"></div>
    <script src="https://www.paypal.com/sdk/js?client-id=Ae8EgWhLe7KGILyGEak5gtHegfFrkZlyj9yS5WgDPA8wh4YicK7Wc88-8qsemzqsWZonvLZ4AWDRzy3y&currency=MXN"></script>

    <script>
        paypal.Buttons({
            style: {
                shape: 'sharp',
                layout: 'vertical',
                label: 'pay'
            },
            createOrder: function(data, actions) {
                return actions.order.create({
                    purchase_units: [{
                        amount: {
                            value: <?php echo $total; ?>
                        }
                    }]
                });
            },
            onApprove: function(data, actions) {
                return actions.order.capture().then(function(details) {
                    return fetch('/src/views/order/capturePays.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json'
                            },
                            body: JSON.stringify(details)
                        }).then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                alert('Pago realizado con éxito.');
                                window.location.href = '/src/views/order/success.php';
                            } else {
                                alert('Error al procesar el pago: ' + data.message);
                            }
                        });
                });
            },
            onCancel: function(data) {
                alert('El pago ha sido cancelado.');
            }
        }).render('#paypal-button-container');
    </script>
</body>

</html>