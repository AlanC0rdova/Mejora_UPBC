<?php
session_start();
if (!isset($_SESSION['matricula'])) {
    header("Location: inicio.php");
    exit();
}

require 'conexion.php';

$mensajeError = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $comentario = trim($_POST['comentario'] ?? '');
    $categoria  = trim($_POST['categoria'] ?? 'Insumo');
    $matricula  = $_SESSION['matricula'];

    if (!in_array($categoria, ['Insumo', 'Mobiliario'])) {
        $categoria = 'Insumo';
    }

    if (isset($_FILES['foto']) and $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
        $directorioSubidas = 'uploads/';
        if (!is_dir($directorioSubidas)) {
            mkdir($directorioSubidas, 0777, true);
        }

        $extension = strtolower(pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION));
        $extensionesPermitidas = ['jpg', 'jpeg', 'png', 'webp'];

        if (in_array($extension, $extensionesPermitidas)) {
            $nombreArchivo = uniqid('foto_', true) . '.' . $extension;
            $rutaDestino = $directorioSubidas . $nombreArchivo;

            if (move_uploaded_file($_FILES['foto']['tmp_name'], $rutaDestino)) {
                $stmt = $conexion->prepare("INSERT INTO fotos (url, comentario, ranking, matricula_usuario, categoria) VALUES (?, ?, 0, ?, ?)");
                $stmt->bind_param("ssss", $rutaDestino, $comentario, $matricula, $categoria);
                
                if ($stmt->execute()) {
                    header("Location: muro.php");
                    exit();
                } else {
                    $mensajeError = "Error al registrar en la base de datos.";
                }
                $stmt->close();
            } else {
                $mensajeError = "Error al guardar el archivo en el servidor.";
            }
        } else {
            $mensajeError = "Formato no permitido. Solo imágenes JPG, PNG o WEBP.";
        }
    } else {
        $mensajeError = "Debes seleccionar una imagen para la publicación.";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nueva Publicación - Mejora UPBC</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

  <!-- HEADER -->
  <header class="header-banner">
    <div class="header-left">
      <a href="muro.php" class="icon-btn btn-gold" title="Volver al Muro">
        <svg viewBox="0 0 24 24"><path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z"/></svg>
      </a>
      <span class="header-title-text">Nuevo Reporte</span>
    </div>
  </header>

  <main class="main-container">
    <div class="card-form">
      <h2 class="card-title">Crear Publicación</h2>

      <?php if (!empty($mensajeError)): ?>
        <div class="alert-box alert-error">
          <?php echo htmlspecialchars($mensajeError); ?>
        </div>
      <?php endif; ?>

      <form action="subir.php" method="POST" enctype="multipart/form-data">
        <label class="form-label">Selecciona la Foto</label>
        <input type="file" name="foto" class="form-input" accept="image/*" required>

        <label class="form-label">Categoría del Reporte</label>
        <select name="categoria" class="form-input" required>
            <option value="Insumo">Insumo</option>
            <option value="Mobiliario">Mobiliario</option>
        </select>

        <label class="form-label">Comentario o Descripción</label>
        <textarea name="comentario" class="form-textarea" placeholder="Describe el problema detectado..." required></textarea>

        <button type="submit" class="btn-submit btn-block">Publicar Reporte</button>
      </form>
    </div>
  </main>

  <footer class="footer-bar"></footer>
</body>
</html>