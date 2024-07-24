<?php
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
    $sql = "SELECT Nombre, Descripcion, Precio, Imagen FROM Productos WHERE Id_Producto = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("i", $id_producto);
    $stmt->execute();
    $stmt->bind_result($nombre, $descripcion, $precio, $imagen);
    $stmt->fetch();
    $stmt->close();

    // Obtener reseñas del producto
    $sql = "SELECT Usuario, Comentario, Calificacion FROM Resenas WHERE Id_Producto = ?";
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
    <link rel="stylesheet" href="../../../public/css/detailResena.css">
    <link rel="stylesheet" href="../../../public/css/navbar.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>

<body>
    <!-- Barra de navegación -->
    <header>
        <div class="logo">Sellphone</div>
        <nav>
            <a href="/src/views/home/index.php">Tienda</a>
            <a href="/src/views/home/soporteContacto.php">Contacto</a>
            <a href="#">Carrito</a>
            <a href="#">Mi perfil</a>
        </nav>
    </header>

    <!-- Detalles del producto -->
    <div class="product-details">
        <h1><?php echo htmlspecialchars($nombre ?? 'Producto desconocido'); ?></h1>
        <img src="../../../public/images/<?php echo htmlspecialchars($imagen ?? 'default.jpg'); ?>" alt="<?php echo htmlspecialchars($nombre ?? 'Producto desconocido'); ?>">
        <p class="price">Precio: $<?php echo number_format($precio ?? 0, 2); ?></p>
        <p class="description"><?php echo nl2br(htmlspecialchars($descripcion ?? 'Descripción no disponible')); ?></p>

        <!-- Agregar al carrito -->
        <form action="addToCart.php" method="post">
            <input type="hidden" name="id_producto" value="<?php echo $id_producto; ?>">
            <button type="submit">Agregar al Carrito</button>
        </form>
    </div>

    <!-- Sección de Reseñas -->
    <div class="reviews">
        <h2>Reseñas de Usuarios</h2>
        <?php if (isset($resenas) && $resenas->num_rows > 0) : ?>
            <?php while ($resena = $resenas->fetch_assoc()) : ?>
                <div class="review">
                    <h3><?php echo htmlspecialchars($resena['Usuario']); ?></h3>
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