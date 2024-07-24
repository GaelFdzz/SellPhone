<?php
// Iniciar sesión
session_start();

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['usuario_id'])) {
    // Redirigir a la página de inicio de sesión si no ha iniciado sesión
    header("Location: /src/views/user/login.php");
    exit();
}

// Incluir la conexión a la base de datos
include '../../config/database.php';

$current_page = basename($_SERVER['REQUEST_URI'], ".php");

// Consulta para obtener productos
$sql = "SELECT Id_Producto, Nombre, Precio, Imagen FROM Productos";
$result = $conexion->query($sql);
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SellPhone</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js" integrity="sha384-0pUGZvbkm6XF6gxjEnlmuGrJXVbNuzT9qBBavbLwCsOGabYfZo0T0to5eqruptLy" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="../../../public/css/homePage.css">
    <link rel="stylesheet" href="../../../public/css/navbar.css">
</head>

<body>
    <header>
        <div class="logo">Sellphone</div>
        <nav>
            <a href="/src/views/home/index.php" class="<?php echo $current_page == 'index' ? 'active' : ''; ?>">Tienda</a>
            <a href="/src/views/home/soporteContacto.php">Contacto</a>
            <a href="#">Carrito</a>
            <a href="#" class="dropdown-toggle" id="perfilDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">Mi perfil</a>
            <ul class="dropdown-menu" aria-labelledby="perfilDropdown">
                <li><a class="dropdown-item" href="#">Ver perfil</a></li>
                <li><a class="dropdown-item" href="/src/views/user/settings.php">Configuraciones</a></li>
                <li>
                    <hr class="dropdown-divider">
                </li>
                <li><a class="dropdown-item" href="/src/controllers/logoutController.php">Cerrar sesión</a></li>
            </ul>
        </nav>
    </header>
    <main>
        <h1>Catálogo de productos</h1>
        <div class="product-grid">
            <?php
            if ($result->num_rows > 0) {
                // Salida de datos de cada fila
                while ($row = $result->fetch_assoc()) {
                    echo '<div class="card">';
                    // Usar htmlspecialchars para evitar XSS y asegurarse de que la imagen no sea nula
                    $imagen = htmlspecialchars(!empty($row["Imagen"]) ? $row["Imagen"] : '../../../public/imagesUploaded/default.png');
                    // Depuración de la ruta de la imagen
                    echo '<img src="../' . $imagen . '">';
                    echo '<h2>' . htmlspecialchars($row["Nombre"]) . '</h2>';
                    echo '<p>$' . number_format($row["Precio"], 2) . '</p>';
                    echo '<a href="../product/detail.php?id=' . $row["Id_Producto"] . '" class="details-button">Ver Detalles</a>';
                    echo '</div>';
                }
            } else {
                echo "0 productos encontrados";
            }
            $conexion->close();
            ?>
        </div>
    </main>
</body>

</html>
