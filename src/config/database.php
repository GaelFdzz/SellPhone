<?php

// Configuración de la base de datos
$servername = "localhost";
$username = "root";
$password = "password";
$dbname = "sellphone";

// Crear conexión
$conexion = mysqli_connect('localhost', 'root', 'password', 'sellphone');

// Verificar conexión
if (!$conexion) {
    die("Conexion fallida: " . mysqli_connect_error());
}
