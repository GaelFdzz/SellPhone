<?php
include '../../config/database.php';

// Verificar conexión
if ($conexion->connect_error) {
    die("Connection failed: " . $conexion->connect_error);
}

// Inicializar variables
$mensaje = "";
$nombre_producto = $descripcion_producto = $precio_producto = $imagen_producto = null;

// Obtener el ID del producto de la URL
$id_producto = isset($_GET['id']) ? intval($_GET['id']) : 0;

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
    <link rel="stylesheet" href="../../../public/css/resena.css">
</head>

<body>
    <header>
        <div class="logo">Sellphone</div>
        <nav>
            <a href="/src/views/home/index.php">Tienda</a>
            <a href="/src/views/home/soporteContacto.php">Contacto</a>
            <a href="#">Carrito</a>
            <a href="#">Mi perfil</a>
        </nav>
    </header>

    <h1>Agregar Reseña</h1>

    <?php if (!empty($mensaje)) : ?>
        <p><?php echo htmlspecialchars($mensaje); ?></p>
    <?php endif; ?>

    <!-- Mostrar detalles del producto -->
    <div class="product-details">
        <h2><?php echo htmlspecialchars($nombre_producto ?? 'Nombre del producto'); ?></h2>
        <img src="../../../public/images/<?php echo htmlspecialchars($imagen_producto ?? 'default.jpg'); ?>" alt="<?php echo htmlspecialchars($nombre_producto ?? 'Nombre del producto'); ?>">
        <p class="description"><?php echo nl2br(htmlspecialchars($descripcion_producto ?? 'Descripción no disponible')); ?></p>
    </div>

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
    <a href="detail.php?id=<?php echo $id_producto; ?>">Volver a los detalles del producto</a>
</body>

</html>