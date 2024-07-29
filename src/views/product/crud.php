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

// Consultar todos los productos
$sql = "
    SELECT p.Id_Producto, p.Nombre, p.Descripcion, p.Precio, p.Stock, p.Imagen, c.Nombre AS Categoria, 
    IFNULL(SUM(dp.Cantidad), 0) AS Cantidad_Vendida
    FROM Productos p
    JOIN Categorias c ON p.Id_Categoria = c.Id_Categoria
    LEFT JOIN Detalle_Pedidos dp ON p.Id_Producto = dp.Id_Producto
    GROUP BY p.Id_Producto
    ";
$result = $conexion->query($sql);

// Consultar todos los mensajes
$sql_mensajes = "SELECT Id_Mensaje, Nombre, Correo_Electronico, Asunto, Mensaje, Fecha_Recibido FROM Mensajes ORDER BY Fecha_Recibido DESC";
$result_mensajes = $conexion->query($sql_mensajes);
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Productos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="/public/css/navbar.css">
    <link rel="stylesheet" href="/public/css/crud.css">
    <script>
        function confirmarEliminacion(id) {
            if (confirm('¿Estás seguro de que deseas eliminar este producto?')) {
                window.location.href = '/src/controllers/crud/deleteProductController.php?action=delete&id=' + id;
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
                <a href="/src/views/product/crud.php" class="<?php echo $current_page == 'crud' ? 'active' : ''; ?>">Dashboard</a>
            <?php endif; ?>
            <a href="#" class="dropdown-toggle" id="perfilDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">Mi perfil</a>
            <ul class="dropdown-menu" aria-labelledby="perfilDropdown">
                <li><a class="dropdown-item" href="../user/profile.php">Ver perfil</a></li>
                <li>
                    <hr class="dropdown-divider">
                </li>
                <li><a class="dropdown-item" href="/src/controllers/logoutController.php">Cerrar sesión</a></li>
            </ul>
        </nav>
    </header>
    <main>
        <h1>Gestión de Productos</h1>
        <a href="crud/registerProduct.php">Agregar Producto</a>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Producto</th>
                    <th>Imagen</th>
                    <th>Visibilidad</th>
                    <th>Existencias</th>
                    <th>Precio</th>
                    <th>Ventas</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($producto = $result->fetch_assoc()) : ?>
                    <tr>
                        <td><?php echo htmlspecialchars($producto['Nombre']); ?></td>
                        <td><img src="<?php echo htmlspecialchars($producto['Imagen']); ?>" alt="Imagen del producto" width="50"></td>
                        <td>Público</td>
                        <td><?php echo htmlspecialchars($producto['Stock']); ?></td>
                        <td>MXN <?php echo htmlspecialchars($producto['Precio']); ?></td>
                        <td><?php echo htmlspecialchars($producto['Cantidad_Vendida']); ?></td> <!-- Mostrar la cantidad vendida -->
                        <td>
                            <button onclick="window.location.href='/src/views/product/crud/modifyProduct.php?id=<?php echo $producto['Id_Producto']; ?>'">Modificar</button>
                            <button onclick="confirmarEliminacion(<?php echo $producto['Id_Producto']; ?>)">Eliminar</button>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <h1>Mensajes Recibidos</h1>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Correo</th>
                    <th>Asunto</th>
                    <th>Mensaje</th>
                    <th>Fecha de Envío</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($mensaje = $result_mensajes->fetch_assoc()) : ?>
                    <tr>
                        <td><?php echo htmlspecialchars($mensaje['Nombre']); ?></td>
                        <td><?php echo htmlspecialchars($mensaje['Correo_Electronico']); ?></td>
                        <td><?php echo htmlspecialchars($mensaje['Asunto']); ?></td>
                        <td><?php echo htmlspecialchars($mensaje['Mensaje']); ?></td>
                        <td><?php echo htmlspecialchars($mensaje['Fecha_Recibido']); ?></td>
                        <td>
                            <button onclick="confirmarEliminacionMensaje(<?php echo $mensaje['Id_Mensaje']; ?>)">Eliminar</button>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <script>
            function confirmarEliminacionMensaje(id) {
                if (confirm('¿Estás seguro de que deseas eliminar este mensaje?')) {
                    window.location.href = '/src/controllers/crud/deleteMessageController.php?action=delete&id=' + id;
                }
            }
        </script>
    </main>
</body>

</html>