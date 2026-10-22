<?php
session_start();
require 'conexion.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $matricula = trim($_POST['matricula'] ?? '');
    $password = trim($_POST['password'] ?? '');

    $stmt = $conexion->prepare("SELECT matricula, pass, nombre, rol FROM usuarios WHERE matricula = ?");
    $stmt->bind_param("s", $matricula);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows === 1) {
        $fila = $resultado->fetch_assoc();
        
        // Soporta contraseñas en texto plano actuales o con password_hash
        if ($password === $fila['pass'] || password_verify($password, $fila['pass'])) {
            $_SESSION['matricula'] = $fila['matricula'];
            $_SESSION['nombre'] = $fila['nombre'];
            $_SESSION['rol'] = $fila['rol'];
            
            header("Location: muro.php");
            exit();
        } else {
            echo "<script>alert('Contraseña incorrecta.'); window.location.href='inicio.php';</script>";
        }
    } else {
        echo "<script>alert('La matrícula no está registrada.'); window.location.href='inicio.php';</script>";
    }

    $stmt->close();
    $conexion->close();
}
?>