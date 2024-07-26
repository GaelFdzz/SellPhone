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

//Obtener la página actual del navegador
$current_page = basename($_SERVER['REQUEST_URI'], ".php");
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sellphone - Contacto</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js" integrity="sha384-0pUGZvbkm6XF6gxjEnlmuGrJXVbNuzT9qBBavbLwCsOGabYfZo0T0to5eqruptLy" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="../../../public/css/soporteContacto.css">
    <link rel="stylesheet" href="../../../public/css/navbar.css">
</head>

<body>
    <header>
        <div class="logo">Sellphone</div>
        <nav>
            <a href="/src/views/home/index.php">Tienda</a>
            <a href="/src/views/home/index.php" class="<?php echo $current_page == 'soporteContacto' ? 'active' : ''; ?>">Contacto</a>
            <a href="#">Carrito</a>
            <!-- Mostrar o no el enlace para el dashboard según el rol del usuario -->
            <?php if ($rol === 1) : ?>
                <a href="/src/views/product/register.php">Dashboard</a>
            <?php endif; ?>
            <!-- Menu dropdown para el usuario logueado -->
            <a href="#" class="dropdown-toggle" id="perfilDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">Mi perfil</a>
            <ul class="dropdown-menu" aria-labelledby="perfilDropdown">
                <li><a class="dropdown-item" href="#">Ver perfil</a></li>
                <li>
                    <hr class="dropdown-divider">
                </li>
                <li><a class="dropdown-item" href="/src/controllers/logoutController.php">Cerrar sesión</a></li>
            </ul>
        </nav>
    </header>
    <main>
        <section id="about">
            <h1>Contáctanos</h1>
            <p>En Sellphone, estamos comprometidos con brindarte el mejor servicio en la compra y venta de equipos de segunda mano. Si tienes alguna pregunta, inquietud o necesitas asistencia, no dudes en ponerte en contacto con nosotros. Puedes comunicarte a través de nuestros canales digitales. Nuestro equipo universitario está aquí para ayudarte en lo que necesites. ¡Esperamos poder atenderte pronto!</p>
            <p>Email: sellphonecun@gmail.com</p>
            <p>Teléfono: 9985396831</p>
            <p>Ubicación: Cancún Q.Roo, Benito Juárez</p>
        </section>
        <section id="about">
            <h2>Acerca de la empresa</h2>
            <p>Sellphone es una empresa dedicada a la venta de equipos electrónicos. Nos esforzamos por ofrecer productos de calidad a precios accesibles y un excelente servicio al cliente. Nuestro equipo está compuesto por estudiantes universitarios comprometidos con brindarte la mejor atención y asistencia en todas tus compras.</p>
        </section>
        <section id="faqs">
            <h2>FAQs</h2>
            <h3>¿Qué tipo de productos venden?</h3>
            <p>Vendemos una amplia variedad de equipos electrónicos de teléfonos móviles.</ <h3>¿Cómo puedo contactarlos?</h3>
            <p>Puedes contactarnos a través de nuestro correo electrónico, número de teléfono o el formulario de contacto en nuestra página web.</p>
            <h3>¿Tienen una tienda física?</h3>
            <p>Actualmente operamos únicamente en línea, pero ofrecemos envíos rápidos y seguros a todo México.</p>
        </section>
    </main>

    <!--Start of Tawk.to Script-->
    <script type="text/javascript">
        var Tawk_API = Tawk_API || {},
            Tawk_LoadStart = new Date();
        (function() {
            var s1 = document.createElement("script"),
                s0 = document.getElementsByTagName("script")[0];
            s1.async = true;
            s1.src = 'https://embed.tawk.to/6693163d32dca6db2caf009c/1i2n8tob5';
            s1.charset = 'UTF-8';
            s1.setAttribute('crossorigin', '*');
            s0.parentNode.insertBefore(s1, s0);
        })();
    </script>
    <!--End of Tawk.to Script-->
</body>

</html>