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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_producto = $_POST["id_producto"];
    $nombre = $_POST["nombre"];
    $descripcion = $_POST["descripcion"];
    $precio = $_POST["precio"];
    $stock = $_POST["stock"];
    $id_categoria = $_POST["id_categoria"];
    $imagen = $_FILES["imagen"]["name"];

    // Si se ha subido una nueva imagen, procesarla
    if (!empty($imagen)) {
        // Consultar la imagen actual del producto
        $sql = "SELECT Imagen FROM Productos WHERE Id_Producto = ?";
        $stmt = $conexion->prepare($sql);
        $stmt->bind_param("i", $id_producto);
        $stmt->execute();
        $result = $stmt->get_result();
        $producto = $result->fetch_assoc();

        // Eliminar la imagen anterior del servidor
        $imagen_path = realpath(__DIR__ . '/../../public/imagesUploaded/') . '/' . basename($producto['Imagen']);
        if (file_exists($imagen_path)) {
            unlink($imagen_path);
        }

        // Manejar la carga de la nueva imagen
        $target_dir = realpath(__DIR__ . '/../../public/imagesUploaded/') . '/';
        $imageFileType = strtolower(pathinfo($_FILES["imagen"]["name"], PATHINFO_EXTENSION));
        $target_file = $target_dir . uniqid('', true) . '.' . $imageFileType;

        // Verificar si el archivo es una imagen real
        $check = getimagesize($_FILES["imagen"]["tmp_name"]);
        if ($check !== false) {
            // Verificar el tamaño del archivo
            if ($_FILES["imagen"]["size"] > 500000) {
                echo "Lo siento, tu archivo es demasiado grande.";
                exit();
            }

            // Permitir ciertos formatos de archivo
            if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg") {
                echo "Lo siento, solo se permiten archivos JPG, JPEG y PNG.";
                exit();
            }

            // Intentar subir el archivo
            if (move_uploaded_file($_FILES["imagen"]["tmp_name"], $target_file)) {
                $imagen = '/public/imagesUploaded/' . basename($target_file);
            } else {
                echo "Lo siento, hubo un error al subir tu archivo.";
                exit();
            }
        } else {
            echo "El archivo no es una imagen.";
            exit();
        }
    } else {
        // Mantener la imagen actual
        $sql = "SELECT Imagen FROM Productos WHERE Id_Producto = ?";
        $stmt = $conexion->prepare($sql);
        $stmt->bind_param("i", $id_producto);
        $stmt->execute();
        $result = $stmt->get_result();
        $producto = $result->fetch_assoc();
        $imagen = $producto['Imagen'];
    }

    // Actualizar el producto en la base de datos
    $sql = "UPDATE Productos SET Nombre = ?, Descripcion = ?, Precio = ?, Stock = ?, Id_Categoria = ?, Imagen = ? WHERE Id_Producto = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("ssdiisi", $nombre, $descripcion, $precio, $stock, $id_categoria, $imagen, $id_producto);

    if ($stmt->execute()) {
        // Redirigir de vuelta a la página de gestión de productos
        header("Location: /src/views/product/crud.php");
        exit();
    } else {
        echo "Error al actualizar el producto.";
    }
}
?>
