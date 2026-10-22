<?php
$host = "localhost";
$user = "root";     // Usuario por defecto de XAMPP / MySQL
$pass = "";         // Contraseña por defecto (vacía)
$db   = "registros";

$conexion = new mysqli($host, $user, $pass, $db);

if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

$conexion->set_charset("utf8mb4");
?>