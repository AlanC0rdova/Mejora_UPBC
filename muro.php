<?php
session_start();
if (!isset($_SESSION['matricula'])) {
    header("Location: inicio.php");
    exit();
}

require 'conexion.php';

$esAdmin = ($_SESSION['rol'] ?? '') === 'ADMIN';
$nombreUsuario =$_SESSION['nombre'] ?? 'Usuario';
$matriculaUsuario =$_SESSION['matricula'];
$inicial = strtoupper(substr($nombreUsuario, 0, 1));

// Consulta SQL incluyendo la columna categoria
$query = "SELECT f.id, f.url, f.comentario, f.ranking, f.categoria, f.matricula_usuario, u.nombre 
          FROM fotos f 
          LEFT JOIN usuarios u ON f.matricula_usuario = u.matricula 
          ORDER BY f.id DESC";

$resultado = $conexion->query($query);
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Muro - Mejora UPBC</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>

  <!-- HEADER BANNER -->
  <header class="header-banner">
    <div class="header-left">
      <a href="subir.php" class="icon-btn btn-plus" title="Nuevo reporte / Publicación">
        <svg viewBox="0 0 24 24"><path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/></svg>
      </a>
      <?php if ($esAdmin): ?>
        <a href="admin.php" class="icon-btn btn-admin-header" title="Panel de Administración">
          <svg viewBox="0 0 24 24"><path d="M12 1L3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-4zm0 10.99h7c-.53 4.12-3.28 7.79-7 8.94V12H5V6.3l7-3.11v8.8z"/></svg>
        </a>
      <?php endif; ?>
    </div>

    <div class="header-right">
      <button class="icon-btn" title="Notificaciones" onclick="toggleNotificationsPopover()">
        <svg viewBox="0 0 24 24"><path d="M12 22c1.1 0 2-.9 2-2h-4c0 1.1.89 2 2 2zm6-6v-5c0-3.07-1.64-5.64-4.5-6.32V4c0-.83-.67-1.5-1.5-1.5s-1.5.67-1.5 1.5v.68C7.63 5.36 6 7.92 6 11v5l-2 2v1h16v-1l-2-2z"/></svg>
      </button>
      <a href="muro.php" class="icon-btn btn-gold" title="Inicio">
        <svg viewBox="0 0 24 24"><path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z"/></svg>
      </a>
      <button class="hamburger-btn" title="Menú" onclick="toggleSidebar()">
        <span></span>
        <span></span>
        <span></span>
      </button>
    </div>

    <!-- POPOVER NOTIFICACIONES -->
    <div id="notifications-popover" class="popover hidden">
      <div class="popover-header">Notificaciones recientes</div>
      <div class="popover-item">Has iniciado sesión correctamente.</div>
      <div class="popover-item">Muro sincronizado con la base de datos.</div>
    </div>
  </header>

  <!-- SIDEBAR LATERAL -->
  <aside id="sidebar" class="sidebar hidden">
    <button class="sidebar-close-btn" onclick="toggleSidebar()">❯</button>
    <div class="sidebar-profile">
      <div class="avatar-placeholder"><?php echo $inicial; ?></div>
      <h2><?php echo htmlspecialchars($nombreUsuario); ?></h2>
      <p><?php echo htmlspecialchars($matriculaUsuario); ?></p>
      <?php if ($esAdmin): ?>
        <span class="badge-admin">ADMIN</span>
      <?php endif; ?>
    </div>
    <hr class="sidebar-divider">
    <nav class="sidebar-menu">
      <a href="muro.php" class="sidebar-link">
        <svg class="sidebar-icon" viewBox="0 0 24 24"><path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z"/></svg>
        Muro Principal
      </a>
      <a href="subir.php" class="sidebar-link">
        <svg class="sidebar-icon" viewBox="0 0 24 24"><path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/></svg>
        Nueva Publicación
      </a>
      <?php if ($esAdmin): ?>
        <a href="admin.php" class="sidebar-link sidebar-link-admin">
          <svg class="sidebar-icon" viewBox="0 0 24 24">
            <path d="M12 1L3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-4zm0 10.99h7c-.53 4.12-3.28 7.79-7 8.94V12H5V6.3l7-3.11v8.8z"/>
          </svg>
          Panel Admin
        </a>
      <?php endif; ?>
    </nav>
    <div class="sidebar-footer">
      <a href="logout.php" class="sidebar-link sidebar-link-logout">
        <svg class="sidebar-icon" viewBox="0 0 24 24"><path d="M10.09 15.59L11.5 17l5-5-5-5-1.41 1.41L12.67 11H3v2h9.67l-2.58 2.59zM19 3H5c-1.11 0-2 .9-2 2v4h2V5h14v14H5v-4H3v4c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2z"/></svg>
        Cerrar sesión
      </a>
    </div>
  </aside>

  <!-- CONTENIDO PRINCIPAL -->
  <main class="main-container">
    <div class="feed-header">
      <h2>Muro de Escalaciones</h2>
      <a href="subir.php" class="btn-submit">+ Nueva Foto</a>
    </div>

    <div id="feed">
      <?php if ($resultado and$resultado->num_rows > 0): ?>
          <?php while ($post =$resultado->fetch_assoc()): ?>
              <div class="post-card" id="post-<?php echo $post['id']; ?>">
                  <img src="<?php echo htmlspecialchars($post['url']); ?>" alt="Post">
                  <div class="post-body">
                      
                      <div class="post-meta-row">
                        <p class="post-author">
                          <strong>@<?php echo htmlspecialchars($post['nombre'] ?? $post['matricula_usuario']); ?>:</strong>
                        </p>
                        <span class="badge-categoria badge-<?php echo strtolower($post['categoria'] ?? 'insumo'); ?>">
                          <?php echo htmlspecialchars($post['categoria'] ?? 'Insumo'); ?>
                        </span>
                      </div>

                      <p class="post-desc"><?php echo htmlspecialchars($post['comentario']); ?></p>
                      
                      <div class="post-actions">
                          <div class="post-votes">
                              <button onclick="puntuacion(<?php echo $post['id']; ?>, 'mas')" class="btn-star" title="Votar positivo">⭐</button>
                              
                              <?php if ($esAdmin): ?>
                                  <button onclick="puntuacion(<?php echo $post['id']; ?>, 'menos')" class="btn-minus" title="Restar punto (Admin)">➖</button>
                              <?php endif; ?>

                              <span id="rank-<?php echo $post['id']; ?>" class="rank-counter"><?php echo $post['ranking']; ?></span>
                          </div>
                          
                          <?php if ($esAdmin): ?>
                              <button onclick="eliminarPost(<?php echo $post['id']; ?>)" class="btn-danger">Eliminar</button>
                          <?php endif; ?>
                      </div>
                  </div>
              </div>
          <?php endwhile; ?>
      <?php else: ?>
          <p class="feed-empty">No hay fotos aún. ¡Sé el primero en subir una!</p>
      <?php endif; ?>
    </div>
  </main>

  <footer class="footer-bar"></footer>

  <script>
    function toggleSidebar() {
      document.getElementById('sidebar').classList.toggle('hidden');
    }

    function toggleNotificationsPopover() {
      document.getElementById('notifications-popover').classList.toggle('hidden');
    }

    function puntuacion(id, accion) {
        const formData = new FormData();
        formData.append('id', id);
        formData.append('accion', accion);

        fetch('votar.php', {
            method: 'POST',
            body: formData
        })
        .then(response => {
            if (response.ok) {
                const rankSpan = document.getElementById(`rank-${id}`);
                let actual = parseInt(rankSpan.textContent);
                rankSpan.textContent = (accion === 'mas') ? actual + 1 : actual - 1;
            } else if (response.status === 403) {
                alert("No tienes permisos para realizar esta acción.");
            }
        });
    }

    function eliminarPost(id) {
        if (confirm("¿Seguro que deseas eliminar esta publicación?")) {
            const formData = new FormData();
            formData.append('id', id);

            fetch('eliminar.php', {
                method: 'POST',
                body: formData
            })
            .then(response => {
                if (response.ok) {
                    const postElem = document.getElementById(`post-${id}`);
                    if (postElem) postElem.remove();
                } else {
                    alert("Solo los administradores pueden borrar publicaciones.");
                }
            });
        }
    }
  </script>
</body>
</html>