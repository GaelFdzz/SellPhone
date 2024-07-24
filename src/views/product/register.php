<?php
// Iniciar sesión
session_start();

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['usuario_id'])) {
    // Redirigir a la página de inicio de sesión si no ha iniciado sesión
    header("Location: /src/views/user/login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar Producto</title>
    <link rel="stylesheet" href="styles.css">
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
    <main>
        <h1>Registrar Producto</h1>
        <form action="../../controllers/productController.php" method="post" enctype="multipart/form-data">
            <label for="nombre">Nombre del Producto:</label>
            <input type="text" id="nombre" name="nombre" required>
            
            <label for="descripcion">Descripción:</label>
            <textarea id="descripcion" name="descripcion" required></textarea>
            
            <label for="precio">Precio:</label>
            <input type="number" id="precio" name="precio" step="0.01" required>
            
            <label for="stock">Stock:</label>
            <input type="number" id="stock" name="stock" required>
            
            <label for="id_categoria">Categoría:</label>
            <select id="id_categoria" name="id_categoria" required>
                <!-- Opciones de categoría deberían cargarse desde la base de datos -->
                <option value="1">Celular</option>
            </select>
            
            <label for="imagen">Imagen del Producto:</label>
            <input type="file" id="imagen" name="imagen" accept="image/*" required>
            
            <button type="submit">Registrar Producto</button>
        </form>
    </main>
</body>
</html>
