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

// Consultar la información del usuario
$sql = "SELECT Nombre, Apellido, Correo, Contrasena FROM Usuarios WHERE Id_Usuario = ?";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Consultar el historial de compras del usuario
$sql = "SELECT p.Fecha_Pedido, p.Total_Precio, p.Estado, dp.Cantidad, dp.Precio AS PrecioProducto, pr.Nombre AS NombreProducto
        FROM Pedidos p
        JOIN Detalle_Pedidos dp ON p.Id_Pedido = dp.Id_Pedido
        JOIN Productos pr ON dp.Id_Producto = pr.Id_Producto
        WHERE p.Id_Usuario = ?";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$historial_compras = $stmt->get_result();

// Obtener el rol del usuario
$sql = "SELECT Id_Rol FROM Usuarios WHERE Id_Usuario = ?";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$rol = $row['Id_Rol'];

// Obtener la página actual del navegador
$current_page = basename($_SERVER['REQUEST_URI'], ".php");
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Perfil</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="/public/css/profile.css">
    <link rel="stylesheet" href="/public/css/navbar.css">
    <script>
        function togglePasswordVisibility() {
            var passwordField = document.getElementById("passwordField");
            var toggleButton = document.getElementById("toggleButton");
            if (passwordField.type === "password") {
                passwordField.type = "text";
                toggleButton.textContent = "Ocultar";
            } else {
                passwordField.type = "password";
                toggleButton.textContent = "Mostrar";
            }
        }
    </script>
</head>

<body>
    <header>
        <div class="logo">
            <a href="" class="sellphone">SellPhone</a>
        </div>
        <nav>
            <a href="/src/views/home/index.php">Tienda</a>
            <a href="/src/views/home/soporteContacto.php">Contacto</a>
            <a href="/src/views/order/cart.php">Carrito</a>
            <?php if ($rol === 1) : ?>
                <a href="/src/views/product/crud.php">Dashboard</a>
            <?php endif; ?>
            <a href="#" class="<?php echo $current_page == 'profile' ? 'active' : ''; ?>" id="perfilDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">Mi perfil</a>
            <ul class="dropdown-menu" aria-labelledby="perfilDropdown">
                <li><a class="dropdown-item" href="../user/profile.php">Ver perfil</a></li>
                <li>
                    <hr class="dropdown-divider">
                </li>
                <li><a class="dropdown-item" href="/src/controllers/logoutController.php">Cerrar sesión</a></li>
            </ul>
        </nav>
    </header>

    <main class="container mt-5">
        <h1>Mi Perfil</h1>
        <h2>Información Personal</h2>
        <p><strong>Nombre:</strong> <?php echo htmlspecialchars($user['Nombre']); ?></p>
        <p><strong>Apellido:</strong> <?php echo htmlspecialchars($user['Apellido']); ?></p>
        <p><strong>Correo:</strong> <?php echo htmlspecialchars($user['Correo']); ?></p>
        <p><strong>Contraseña:</strong> 
            <input type="password" id="passwordField" value="<?php echo htmlspecialchars($user['Contrasena']); ?>" readonly style="border: none; background: none;">
            <button id="toggleButton" class="btn btn-secondary btn-sm" onclick="togglePasswordVisibility()">Mostrar</button>
        </p>
        <a href="profileModify.php" class="btn btn-primary">Editar Perfil</a>

        <h2 class="mt-5">Historial de Compras</h2>
        <?php
        $pedidos = [];
        while ($compra = $historial_compras->fetch_assoc()) {
            $pedidos[$compra['Fecha_Pedido']]['Total_Precio'] = $compra['Total_Precio'];
            $pedidos[$compra['Fecha_Pedido']]['Estado'] = $compra['Estado'];
            $pedidos[$compra['Fecha_Pedido']]['Productos'][] = [
                'NombreProducto' => $compra['NombreProducto'],
                'Cantidad' => $compra['Cantidad'],
                'PrecioProducto' => $compra['PrecioProducto']
            ];
        }
        ?>

        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Fecha</th>
                    <th>Total</th>
                    <th>Estado</th>
                    <th>Productos</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($pedidos as $fecha => $pedido) : ?>
                    <tr>
                        <td><?php echo htmlspecialchars($fecha); ?></td>
                        <td><?php echo htmlspecialchars($pedido['Total_Precio']); ?></td>
                        <td><?php echo htmlspecialchars($pedido['Estado']); ?></td>
                        <td>
                            <ul class="listProduct">
                                <?php foreach ($pedido['Productos'] as $producto) : ?>
                                    <li>
                                        <?php echo htmlspecialchars($producto['NombreProducto']); ?> - 
                                        Cantidad: <?php echo htmlspecialchars($producto['Cantidad']); ?>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </main>
</body>

</html>
