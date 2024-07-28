<?php
// Iniciar sesión
session_start();
include '../../config/database.php';
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

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['usuario_id'])) {
    // Redirigir a la página de inicio de sesión si no ha iniciado sesión
    header("Location: /src/views/user/login.php");
    exit();
}

include '../../config/database.php';

// Verificar conexión
if ($conexion->connect_error) {
    die("Connection failed: " . $conexion->connect_error);
}

// Inicializar variables
$nombre = $descripcion = $precio = $imagen = null;

// Obtener el ID del producto de la URL
$id_producto = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id_producto > 0) {
    // Obtener detalles del producto
    $sql = "SELECT Nombre, Descripcion, Precio, Stock, Imagen FROM Productos WHERE Id_Producto = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("i", $id_producto);
    $stmt->execute();
    $stmt->bind_result($nombre, $descripcion, $precio, $stock, $imagen);
    $stmt->fetch();
    $stmt->close();

    // Obtener reseñas del producto
    $sql = "SELECT Usuario, Comentario, Calificacion, Fecha FROM Resenas WHERE Id_Producto = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("i", $id_producto);
    $stmt->execute();
    $resenas = $stmt->get_result();
    $stmt->close();
} else {
    $mensaje = "Producto no encontrado.";
}

$conexion->close();
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalles del Producto</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js" integrity="sha384-0pUGZvbkm6XF6gxjEnlmuGrJXVbNuzT9qBBavbLwCsOGabYfZo0T0to5eqruptLy" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="../../../public/css/detailResena.css">
    <link rel="stylesheet" href="../../../public/css/navbar.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>

<body>
    <!-- Barra de navegación -->
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

    <!-- Detalles del producto -->
    <div class="product-details">
        <h1><?php echo htmlspecialchars($nombre ?? 'Producto desconocido'); ?></h1>
        <img src="<?php echo htmlspecialchars($imagen ?? 'default.jpg'); ?>" alt="<?php echo htmlspecialchars($nombre ?? 'Producto desconocido'); ?>">
        <p class="price">Precio: $<?php echo number_format($precio ?? 0, 2); ?></p>
        <?php
        if ($stock > 0) {
            echo '<p class="stock">Stock: ' . htmlspecialchars($stock) . '</p>';
        } else {
            echo '<p class="stock out-of-stock">'. 'Stock: ' . htmlspecialchars($stock) .'</p>';
        }
        ?>
        <p class="description"><?php echo nl2br(htmlspecialchars($descripcion ?? 'Descripción no disponible')); ?></p>

        <!-- Agregar al carrito -->
        <?php
        if ($stock > 0) {
            echo '<a href="../../controllers/cartController.php?producto_id=' . $id_producto . '" class="cart-button">Agregar al carrito</a>';
        } else {
            echo '<button class="cart-button disabled" disabled>Agotado</button>';
        }
        ?>
    </div>

    <!-- Sección de Reseñas -->
    <div class="reviews">
        <h2>Reseñas de Usuarios</h2>
        <?php if (isset($resenas) && $resenas->num_rows > 0) : ?>
            <?php while ($resena = $resenas->fetch_assoc()) : ?>
                <div class="review">
                    <h3><?php echo htmlspecialchars($resena['Usuario']); ?></h3>
                    <p class="review-date"><?php echo htmlspecialchars($resena['Fecha']); ?></p>
                    <p><?php echo htmlspecialchars($resena['Comentario']); ?></p>
                    <p class="star-rating">
                        <?php
                        $calificacion = intval($resena['Calificacion']);
                        // Mostrar 5 estrellas en total
                        for ($i = 1; $i <= 5; $i++) {
                            if ($i <= $calificacion) {
                                echo '<i class="fa fa-star"></i>'; // Estrella llena
                            } else {
                                echo '<i class="fa fa-star-o"></i>'; // Estrella vacía
                            }
                        }
                        ?>
                    </p>
                </div>
            <?php endwhile; ?>
        <?php else : ?>
            <p>No hay reseñas para este producto.</p>
        <?php endif; ?>

        <!-- Enlace para agregar una nueva reseña -->
        <a href="resena.php?id=<?php echo $id_producto; ?>" class="add-review-button">Agregar Reseña</a>
    </div>
</body>

</html>