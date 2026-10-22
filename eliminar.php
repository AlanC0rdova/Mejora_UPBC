<?php
session_start();
require 'conexion.php';

if (!isset($_SESSION['matricula'])) {
    http_response_code(401);
    exit("No autorizado");
}

$rol = $_SESSION['rol'] ?? '';

// Verificación estricta del rol ADMIN
if ($rol !== 'ADMIN') {
    http_response_code(403);
    exit("Acceso denegado. Solo administradores pueden borrar publicaciones.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = intval($_POST['id'] ?? 0);

    // Obtener la ruta del archivo para eliminar la imagen físicamente
    $stmtSelect = $conexion->prepare("SELECT url FROM fotos WHERE id = ?");
    $stmtSelect->bind_param("i", $id);
    $stmtSelect->execute();
    $res = $stmtSelect->get_result();

    if ($res->num_rows === 1) {
        $foto = $res->fetch_assoc();

        if (!empty($foto['url']) && file_exists($foto['url'])) {
            unlink($foto['url']); // Elimina el archivo del servidor
        }

        $stmtDelete = $conexion->prepare("DELETE FROM fotos WHERE id = ?");
        $stmtDelete->bind_param("i", $id);
        $stmtDelete->execute();
        $stmtDelete->close();

        echo "ok";
    }
    $stmtSelect->close();
    $conexion->close();
}
?>