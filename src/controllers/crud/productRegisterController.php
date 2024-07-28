<?php
// Incluir la conexión a la base de datos
include '../../config/database.php';

// Verificar conexión
if ($conexion->connect_error) {
    die("Connection failed: " . $conexion->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = $_POST["nombre"];
    $descripcion = $_POST["descripcion"];
    $precio = $_POST["precio"];
    $stock = $_POST["stock"];
    $id_categoria = $_POST["id_categoria"];

    // Manejar la carga de la imagen
    $target_dir = realpath(__DIR__ . '/../../../public/imagesUploaded/') . '/';
    $imageFileType = strtolower(pathinfo($_FILES["imagen"]["name"], PATHINFO_EXTENSION));
    $target_file = $target_dir . uniqid('', true) . '.' . $imageFileType; // Genera un nombre de archivo único

    $uploadOk = 1;

    // Verificar si el archivo es una imagen real
    $check = getimagesize($_FILES["imagen"]["tmp_name"]);
    if ($check !== false) {
        $uploadOk = 1;
    } else {
        echo "El archivo no es una imagen.";
        $uploadOk = 0;
    }

    // Verificar el tamaño del archivo (debe ser menor a 1MB)
    if ($_FILES["imagen"]["size"] > 1000000) {
        echo "Lo siento, tu archivo es demasiado grande.";
        $uploadOk = 0;
    }

    // Permitir ciertos formatos de archivo
    if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg") {
        echo "Lo siento, solo se permiten archivos JPG, JPEG y PNG";
        $uploadOk = 0;
    }

    // Verificar si $uploadOk es 0 debido a un error
    if ($uploadOk == 0) {
        echo "Lo siento, tu archivo no fue subido.";
    // Si todo está bien, intenta subir el archivo
    } else {
        if (move_uploaded_file($_FILES["imagen"]["tmp_name"], $target_file)) {
            // Obtener la ruta relativa de la imagen para almacenarla en la base de datos
            $relative_target_file = '/public/imagesUploaded/' . basename($target_file);

            // Insertar nuevo producto en la base de datos
            $sql = "INSERT INTO Productos (Nombre, Descripcion, Precio, Stock, Id_Categoria, Imagen) VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $conexion->prepare($sql);
            $stmt->bind_param("ssdiis", $nombre, $descripcion, $precio, $stock, $id_categoria, $relative_target_file);

            if ($stmt->execute()) {
                echo "El producto ha sido registrado con éxito.";
                header('Location: /src/views/product/crud.php');
            } else {
                echo "Error: " . $sql . "<br>" . $conexion->error;
            }
            $stmt->close();
        } else {
            echo "Lo siento, hubo un error al subir tu archivo.";
            header('Location: /src/views/product/crud.php');
            exit();
        }
    }
}

$conexion->close();
?>
