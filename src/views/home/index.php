<?php
//Incluir la conexion a la base de datos
include '../../config/database.php';

// Consulta para obtener productos
$sql = "SELECT nombre, precio FROM Productos";
$result = $conexion->query($sql);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SellPhone</title>
    <link rel="stylesheet" href="styles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js" integrity="sha384-0pUGZvbkm6XF6gxjEnlmuGrJXVbNuzT9qBBavbLwCsOGabYfZo0T0to5eqruptLy" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="../../../public/css/homePage.css">
</head>
<body>
    <header>
        <div class="logo">Sellphone</div>
        <nav>
            <a href="#">Tienda</a>
            <a href="#">Acerca de</a>
            <a href="#">Contacto</a>
            <a href="#">Carrito</a>
            <a href="#">Mi perfil</a>
        </nav>
    </header>
    <main>
        <h1>Catalogo de productos</h1>
        <div class="product-grid">
            <?php
            if ($result->num_rows > 0) {
                // Salida de datos de cada fila
                while($row = $result->fetch_assoc()) {
                    echo '<div class="product">';
                    //echo '<img src="images/' . $row["imagen"] . '" alt="' . $row["nombre"] . '">';
                    echo '<img src="../public/images/iphone15.png">';
                    echo '<h2>' . $row["nombre"] . '</h2>';
                    echo '<p>$' . number_format($row["precio"] ?? 0, 2) . '</p>';
                    echo '<button>AÑADIR AL CARRITO</button>';
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
