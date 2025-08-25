<?php
// src/templates/header.php
session_start(); // ya estará activa, pero por seguridad
// Para saber en qué carpeta estamos y ajustar rutas:
$base = str_repeat('../', substr_count(__DIR__, '/')); 
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width,initial-scale=1"/>
  <title><?= htmlspecialchars($title ?? 'Turnero') ?></title>
  <link href="<?= $base ?>public/css/style.css" rel="stylesheet"/>
</head>
<body>
  <header class="site-header">
    <div class="container">
      <a href="<?= $base ?>public/index.html" class="logo">
        <img src="<?= $base ?>public/assets/logo/gigared_logo.png" alt="Gigared" height="40"/>
      </a>
      <nav class="main-nav">
        <ul>
          <?php if(!empty($_SESSION['es_admin'])): ?>
            <li><a href="<?= $base ?>src/admin/gestion_puestos.php">Puestos</a></li>
            <li><a href="<?= $base ?>src/admin/gestion_operadores.php">Operadores</a></li>
            <li><a href="<?= $base ?>src/admin/gestion_motivos.php">Motivos</a></li>
            <li><a href="<?= $base ?>src/admin/reportes.php">Reportes</a></li>
          <?php else: ?>
            <li><a href="<?= $base ?>public/panel_operadores.html">Panel</a></li>
          <?php endif; ?>
          <li><a href="<?= $base ?>src/auth/logout.php">Salir (<?= htmlspecialchars($_SESSION['usuario'] ?? '') ?>)</a></li>
        </ul>
      </nav>
    </div>
  </header>
  <main class="container">
