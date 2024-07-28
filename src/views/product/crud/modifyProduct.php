<?php
// Iniciar sesión
session_start();
// Incluir la conexión a la base de datos
include '../../../config/database.php';

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['usuario_id'])) {
    // Redirigir a la página de inicio de sesión si no ha iniciado sesión
    header("Location: /src/views/user/login.php");
    exit();
}

// Obtener el ID del producto
$id_producto = $_GET['id'];

// Consultar los datos del producto
$sql = "SELECT * FROM Productos WHERE Id_Producto = ?";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("i", $id_producto);
$stmt->execute();
$result = $stmt->get_result();
$producto = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modificar Producto</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js"></script>
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
            <a href="/src/views/order/cart.php">Carrito</a>
        </nav>
    </header>
    <main>
        <h1>Modificar Producto</h1>
        <form action="/src/controllers/crud/updateProductController.php" method="post" enctype="multipart/form-data">
            <input type="hidden" name="id_producto" value="<?php echo $producto['Id_Producto']; ?>">
            <div class="mb-3">
                <label for="nombre" class="form-label">Nombre del Producto</label>
                <input type="text" class="form-control" id="nombre" name="nombre" value="<?php echo htmlspecialchars($producto['Nombre']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="descripcion" class="form-label">Descripción</label>
                <textarea class="form-control" id="descripcion" name="descripcion" required><?php echo htmlspecialchars($producto['Descripcion']); ?></textarea>
            </div>
            <div class="mb-3">
                <label for="precio" class="form-label">Precio</label>
                <input type="number" step="0.01" class="form-control" id="precio" name="precio" value="<?php echo htmlspecialchars($producto['Precio']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="stock" class="form-label">Stock</label>
                <input type="number" class="form-control" id="stock" name="stock" value="<?php echo htmlspecialchars($producto['Stock']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="id_categoria" class="form-label">Categoría</label>
                <select class="form-select" id="id_categoria" name="id_categoria" required>
                    <!-- Aquí debes consultar y mostrar las categorías disponibles -->
                    <?php
                    $sql = "SELECT Id_Categoria, Nombre FROM Categorias";
                    $result = $conexion->query($sql);
                    while ($categoria = $result->fetch_assoc()) {
                        $selected = $categoria['Id_Categoria'] == $producto['Id_Categoria'] ? 'selected' : '';
                        echo "<option value='{$categoria['Id_Categoria']}' $selected>{$categoria['Nombre']}</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="mb-3">
                <label for="imagen" class="form-label">Imagen del Producto (deja en blanco para mantener la imagen actual)</label>
                <input type="file" class="form-control" id="imagen" name="imagen">
            </div>
            <button type="submit" class="btn btn-primary">Guardar Cambios</button>
        </form>
    </main>
</body>
</html>
