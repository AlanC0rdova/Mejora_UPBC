<?php
session_start();
require 'conexion.php';
//sentencia login
if (!isset($_SESSION['matricula'])) {
    http_response_code(401);
    exit("No autorizado");
}
// checa si es admin
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = intval($_POST['id']);
    $accion = $_POST['accion'] ?? 'mas';
    $rol = $_SESSION['rol'] ?? '';

    if ($accion === 'menos') {
        // Solo el ADMIN puede restar puntos
        if ($rol !== 'ADMIN') {
            http_response_code(403);
            exit("Solo administradores pueden bajar la puntuación.");
        }
        $stmt = $conexion->prepare("UPDATE fotos SET ranking = ranking - 1 WHERE id = ?");
    } else {
        // Sumar puntos (permitido para cualquier usuario autenticado)
        $stmt = $conexion->prepare("UPDATE fotos SET ranking = ranking + 1 WHERE id = ?");
    }

    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        echo "ok";
    } else {
        http_response_code(500);
    }
    $stmt->close();
}
?>