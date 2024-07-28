<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login y Registro</title>
    <link rel="stylesheet" href="../../../public/css/loginPage.css">
    <script src="../../../public/js/login_Register.js" defer></script>
</head>
<body>
    <div class="container">
        <div class="form-container sign-up-container">
            <form action="../../controllers/registerController.php" method="post">
                <h1>Crear Cuenta</h1>
                <input type="text" name="nombre" placeholder="Nombre" required>
                <input type="text" name="apellido" placeholder="Apellido" required>
                <input type="email" name="correo" placeholder="Email" required>
                <input type="password" name="contrasena" placeholder="Contraseña" required>
                <button type="submit">Registrarse</button>
            </form>
        </div>
        <div class="form-container sign-in-container">
            <form action="../../controllers/loginController.php" method="post">
                <h1>Iniciar Sesión</h1>
                <input type="email" name="correo" placeholder="Email" required>
                <input type="password" name="contrasena" placeholder="Contraseña" required>
                <button type="submit">Ingresar</button>
            </form>
        </div>
        <div class="overlay-container">
            <div class="overlay">
                <div class="overlay-panel overlay-left">
                    <h1>Registrate en SellPhone!</h1>
                    <p>Cree su cuenta utilizando su correo electonico y su contraseña</p>
                    <p>ó</p>
                    <button class="ghost" id="signIn">Iniciar Sesión</button>
                </div>
                <div class="overlay-panel overlay-right">
                    <h1>Bienvenido de nuevo!</h1>
                    <p>Ingrese sus correo y contraseña para acceder a la plataforma</p>
                    <p>ó</p>
                    <button class="ghost" id="signUp">Registrarse</button>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
