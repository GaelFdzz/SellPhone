<?php
// Iniciar sesión
session_start();
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

// Verificar conexión
if ($conexion->connect_error) {
    die("Connection failed: " . $conexion->connect_error);
}

// Inicializar variables
$mensaje = "";
$nombre_producto = $descripcion_producto = $precio_producto = $imagen_producto = null;

// Obtener el ID del producto de la URL
$id_producto = isset($_GET['id']) ? intval($_GET['id']) : 0;

$usuario_id = $_SESSION['usuario_id'];

// Verificar si el usuario ha comprado el producto
$sql = "SELECT COUNT(*) as total 
        FROM Detalle_Pedidos 
        INNER JOIN Pedidos ON Detalle_Pedidos.Id_Pedido = Pedidos.Id_Pedido
        WHERE Detalle_Pedidos.Id_Producto = ? AND Pedidos.Id_Usuario = ?";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("ii", $id_producto, $usuario_id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

if ($row['total'] == 0) {
    $mensaje = 'Solo los clientes que han comprado este producto pueden dejar una reseña.';
} else {
    // Procesar la reseña si el usuario ha comprado el producto
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && $id_producto > 0) {
        $usuario = $_POST['usuario'];
        $comentario = $_POST['comentario'];
        $calificacion = intval($_POST['calificacion']);

        if (!empty($usuario) && !empty($comentario) && $calificacion > 0 && $calificacion <= 5) {
            $sql = "INSERT INTO Resenas (Id_Producto, Usuario, Comentario, Calificacion) VALUES (?, ?, ?, ?)";
            $stmt = $conexion->prepare($sql);
            $stmt->bind_param("isss", $id_producto, $usuario, $comentario, $calificacion);
            $stmt->execute();
            $stmt->close();
            $mensaje = "Reseña agregada exitosamente.";
            header("Location: detail.php?id=$id_producto"); // Redirigir a los detalles del producto
            exit();
        } else {
            $mensaje = "Por favor, complete todos los campos correctamente.";
        }
    }
}

// Obtener detalles del producto
if ($id_producto > 0) {
    $sql = "SELECT Nombre, Descripcion, Precio, Imagen FROM Productos WHERE Id_Producto = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("i", $id_producto);
    $stmt->execute();
    $stmt->bind_result($nombre_producto, $descripcion_producto, $precio_producto, $imagen_producto);
    $stmt->fetch();
    $stmt->close();
}

$conexion->close();
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agregar Reseña</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js" integrity="sha384-0pUGZvbkm6XF6gxjEnlmuGrJXVbNuzT9qBBavbLwCsOGabYfZo0T0to5eqruptLy" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="../../../public/css/navbar.css">
    <link rel="stylesheet" href="../../../public/css/resena.css">
</head>

<body>
    <header>
        <div class="logo">
            <a href="" class="sellphone">SellPhone</a>
        </div>
        <nav>
            <a href="/src/views/home/index.php">Tienda</a>
            <a href="/src/views/home/soporteContacto.php">Contacto</a>
            <a href="#">Carrito</a>
            <!-- Mostrar o no el enlace para el dashboard según el rol del usuario -->
            <?php if ($rol === 1) : ?>
                <a href="/src/views/product/crud.php">Dashboard</a>
            <?php endif; ?>
            <!-- Menu dropdown para el usuario logueado -->
            <a href="#" class="dropdown-toggle" id="perfilDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">Mi perfil</a>
            <ul class="dropdown-menu" aria-labelledby="perfilDropdown">
                <li><a class="dropdown-item" href="/src/views/user/profile.php">Ver perfil</a></li>
                <li>
                    <hr class="dropdown-divider">
                </li>
                <li><a class="dropdown-item" href="/src/controllers/logoutController.php">Cerrar sesión</a></li>
            </ul>
        </nav>
    </header>

    <h1>Agregar Reseña</h1>

    <?php if (!empty($mensaje)) : ?>
        <p class="mensaje-advertencia"><?php echo htmlspecialchars($mensaje); ?></p>
    <?php endif; ?>

    <!-- Mostrar detalles del producto -->
    <div class="product-details">
        <h2><?php echo htmlspecialchars($nombre_producto ?? 'Nombre del producto'); ?></h2>
        <img src="<?php echo htmlspecialchars($imagen_producto ?? 'default.jpg'); ?>" alt="<?php echo htmlspecialchars($nombre_producto ?? 'Nombre del producto'); ?>">
        <p class="description"><?php echo nl2br(htmlspecialchars($descripcion_producto ?? 'Descripción no disponible')); ?></p>
    </div>

    <?php if ($row['total'] > 0): ?>
    <!-- Formulario para agregar una reseña -->
    <form action="" method="post">
        <input type="hidden" name="id_producto" value="<?php echo $id_producto; ?>">

        <label for="usuario">Nombre:</label>
        <input type="text" id="usuario" name="usuario" required>

        <label for="comentario">Comentario:</label>
        <textarea id="comentario" name="comentario" rows="4" required></textarea>

        <label for="calificacion">Calificación:</label>
        <select id="calificacion" name="calificacion" required>
            <option value="" disabled selected>Selecciona una calificación</option>
            <option value="1">1 - Muy mala</option>
            <option value="2">2 - Mala</option>
            <option value="3">3 - Regular</option>
            <option value="4">4 - Buena</option>
            <option value="5">5 - Excelente</option>
        </select>

        <button type="submit">Enviar Reseña</button>
    </form>
    <?php endif; ?>
    
    <div class="back-to-review-container">
        <a class="back-to-review" href="detail.php?id=<?php echo $id_producto; ?>">Volver a los detalles del producto</a>
    </div>

</body>

</html>
