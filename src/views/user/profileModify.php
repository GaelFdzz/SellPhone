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

// Consultar la información del usuario
$sql = "SELECT Nombre, Apellido, Correo FROM Usuarios WHERE Id_Usuario = ?";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Obtener el rol del usuario
$sql = "SELECT Id_Rol FROM Usuarios WHERE Id_Usuario = ?";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$rol = $row['Id_Rol'];

// Obtener la página actual del navegador
$current_page = basename($_SERVER['REQUEST_URI'], ".php");
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Perfil</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="/public/css/profile.css">
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
            <?php if ($rol === 1) : ?>
                <a href="/src/views/product/register.php">Dashboard</a>
            <?php endif; ?>
            <a href="#" class="<?php echo $current_page == 'profile' ? 'active' : ''; ?>" id="perfilDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">Mi perfil</a>
            <ul class="dropdown-menu" aria-labelledby="perfilDropdown">
                <li><a class="dropdown-item" href="../user/profile.php">Ver perfil</a></li>
                <li>
                    <hr class="dropdown-divider">
                </li>
                <li><a class="dropdown-item" href="/src/controllers/logoutController.php">Cerrar sesión</a></li>
            </ul>
        </nav>
    </header>

    <main class="container mt-5">
        <h1>Editar Perfil</h1>
        <form method="POST" action="/src/controllers/userController.php">
            <div class="mb-3">
                <label for="nombre" class="form-label">Nombre</label>
                <input type="text" class="form-control" id="nombre" name="nombre" value="<?php echo htmlspecialchars($user['Nombre']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="apellido" class="form-label">Apellido</label>
                <input type="text" class="form-control" id="apellido" name="apellido" value="<?php echo htmlspecialchars($user['Apellido']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="correo" class="form-label">Correo</label>
                <input type="email" class="form-control" id="correo" name="correo" value="<?php echo htmlspecialchars($user['Correo']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="contrasena" class="form-label">Nueva Contraseña (Dejar en blanco para no cambiarla)</label>
                <input type="password" class="form-control" id="contrasena" name="contrasena">
            </div>
            <button type="submit" class="btn btn-primary">Guardar Cambios</button>
        </form>
        <a href="profile.php">Regresar a tu perfil</a>
    </main>
</body>

</html>
