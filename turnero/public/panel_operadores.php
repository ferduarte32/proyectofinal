<?php
// public/panel_operadores.php

// 1) Mostrar errores sólo en desarrollo
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// 2) Configuración de la cookie para toda /turnero/ (antes de session_start)
ini_set('session.cookie_path', '/turnero');
ini_set('session.cookie_httponly', 1);

// 3) Cargar configuración general (BASE_URL) y middleware (session_start + validación de usuario)
require_once __DIR__ . '/../src/config/config.php';
require_once __DIR__ . '/../src/auth/middleware.php';

// 4) Ahora la sesión ya está iniciada y validada como “usuario”.
//    Si necesitas que sólo operadores accedan, chequealo aquí:
if (!isset($_SESSION['operador_id'])) {
    header('Location: ' . BASE_URL . 'public/login.php');
    exit;
}

$usuario = $_SESSION['usuario'];
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Panel de Operadores</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;700&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="<?= BASE_URL ?>public/css/style.css" />
</head>
<body>
  <header class="site-header">
    <div class="container" style="display: flex; align-items: center; justify-content: space-between;">
      <a href="<?= BASE_URL ?>public/index.html" class="logo">
        <img src="<?= BASE_URL ?>public/assets/logo/gigared_logo.png" alt="Gigared" height="40"/>
      </a>
      <nav class="main-nav" style="display: flex; align-items: center; gap: 20px;">
        <ul style="display: flex; gap: 15px; list-style: none; margin: 0; padding: 0;">
          <li><a href="<?= BASE_URL ?>public/index.html">Inicio</a></li>
          <li><a href="<?= BASE_URL ?>public/sacar_turno.html">Sacar Turno</a></li>
          <li><a href="<?= BASE_URL ?>public/panel_operadores.php" class="active">Panel Operadores</a></li>
          <li><a href="<?= BASE_URL ?>public/reporte_operador.html">Reportes</a></li>
          <li><a href="<?= BASE_URL ?>src/admin/gestion_puestos.php">Administración</a></li>
        </ul>
        <div style="color: white; font-weight: 600;">
          Usuario: <?= htmlspecialchars($usuario) ?> |
          <a href="<?= BASE_URL ?>src/auth/logout.php" style="color: #E5007A; text-decoration: none;">Salir</a>
        </div>
      </nav>
    </div>
  </header>

  <main class="container">
    <h1>Panel de Operadores</h1>
    <table class="table" id="tabla-turnos">
      <thead>
        <tr>
          <th># Turno</th>
          <th>Nombre</th>
          <th>Motivo</th>
          <th>Espera (min)</th>
          <th>Acciones</th>
        </tr>
      </thead>
      <tbody id="turnos-body">
        <!-- Filas dinámicas -->
      </tbody>
    </table>

    <!-- Modales... iguales a tu versión actual ... -->
  </main>

  <footer class="site-footer">
    <div class="container">
      <p>&copy; 2025 fercode.com . Todos los derechos reservados.</p>
    </div>
  </footer>

  <script>
    window.APP_BASE = '<?= BASE_URL ?>public';
  </script>
  <script src="<?= BASE_URL ?>public/js/operador.js?v=<?= filemtime(__DIR__ . '/js/operador.js') ?>"></script>
</body>
</html>
