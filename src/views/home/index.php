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

// Inicializar variables de búsqueda y filtros
$search = isset($_GET['search']) ? $_GET['search'] : '';
$min_price = isset($_GET['min_price']) ? $_GET['min_price'] : '';
$max_price = isset($_GET['max_price']) ? $_GET['max_price'] : '';

// Consulta SQL con búsqueda y filtros
$sql = "SELECT Id_Producto, Nombre, Precio, Stock, Imagen FROM Productos WHERE 1=1";

if ($search) {
    $sql .= " AND Nombre LIKE ?";
}

if ($min_price) {
    $sql .= " AND Precio >= ?";
}

if ($max_price) {
    $sql .= " AND Precio <= ?";
}

$stmt = $conexion->prepare($sql);

// Vincular parámetros dinámicamente
$params = [];
$types = '';

if ($search) {
    $types .= 's';
    $params[] = '%' . $search . '%';
}

if ($min_price) {
    $types .= 'd';
    $params[] = $min_price;
}

if ($max_price) {
    $types .= 'd';
    $params[] = $max_price;
}

if ($params) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SellPhone</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="../../../public/css/homePage.css">
    <link rel="stylesheet" href="../../../public/css/navbar.css">
</head>
<body>
    <header>
        <div class="logo">
            <a href="" class="sellphone">SellPhone</a>
        </div>
        <div class="nav-center">
            <form class="search-form" method="GET" action="">
                <input type="text" class="form-control" name="search" placeholder="Buscar" value="<?php echo htmlspecialchars($search); ?>">
                <div class="dropdown">
                    <button class="btn btn-secondary dropdown-toggle" type="button" id="filterDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                        Más Filtros
                    </button>
                    <ul class="dropdown-menu" aria-labelledby="filterDropdown">
                        <li class="px-4 py-3">
                            <div class="mb-3">
                                <label for="min_price" class="form-label">Precio mínimo</label>
                                <input type="number" step="0.01" class="form-control" id="min_price" name="min_price" placeholder="Precio mínimo" value="<?php echo htmlspecialchars($min_price); ?>">
                            </div>
                            <div class="mb-3">
                                <label for="max_price" class="form-label">Precio máximo</label>
                                <input type="number" step="0.01" class="form-control" id="max_price" name="max_price" placeholder="Precio máximo" value="<?php echo htmlspecialchars($max_price); ?>">
                            </div>
                            <button type="submit" class="filter-button">Filtrar</button>
                        </li>
                    </ul>
                </div>
            </form>
        </div>
        <nav>
            <a href="/src/views/home/index.php" class="<?php echo $current_page == 'index' ? 'active' : ''; ?>">Tienda</a>
            <a href="/src/views/home/soporteContacto.php">Contacto</a>
            <a href="/src/views/order/cart.php">Carrito</a>
            <?php if ($rol === 1) : ?>
                <a href="/src/views/product/crud.php">Dashboard</a>
            <?php endif; ?>
            <a href="#" class="dropdown-toggle" id="perfilDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">Mi perfil</a>
            <ul class="dropdown-menu" aria-labelledby="perfilDropdown">
                <li><a class="dropdown-item" href="../user/profile.php">Ver perfil</a></li>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item" href="/src/controllers/logoutController.php">Cerrar sesión</a></li>
            </ul>
        </nav>
    </header>
    <main>
        <h1>Catálogo de productos</h1>
        <div class="product-grid">
            <?php
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo '<div class="card">';
                    $imagen = htmlspecialchars(!empty($row["Imagen"]) ? $row["Imagen"] : '../../public/imagesUploaded/default.png');
                    echo '<img src="' . $imagen . '">';
                    echo '<h2>' . htmlspecialchars($row["Nombre"]) . '</h2>';
                    echo '<p>$' . number_format($row["Precio"], 2) . ' MXN' . '</p>';
                    if ($row["Stock"] > 0) {
                        echo '<a href="../../controllers/cartController.php?producto_id=' . $row["Id_Producto"] . '" class="cart-button">Agregar al carrito</a>';
                    } else {
                        echo '<button class="cart-button disabled" disabled>Agotado</button>';
                    }
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
