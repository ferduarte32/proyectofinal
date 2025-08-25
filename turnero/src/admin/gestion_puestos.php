<?php
// src/admin/gestion_puestos.php
require_once __DIR__ . '/../config/config.php';
$pdo = getConnection();
require_once __DIR__ . '/../auth/middleware.php';

// Handle create/update/delete
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['create'])) {
        $nombre = trim($_POST['nombre']);
        if ($nombre) {
            $stmt = $pdo->prepare("INSERT INTO puestos (nombre, activo) VALUES (?, 1)");
            $stmt->execute([$nombre]);
            $message = 'Puesto creado correctamente.';
        }
    }
    if (isset($_POST['toggle'])) {
        $id = (int)$_POST['id'];
        // Toggle active flag
        $stmt = $pdo->prepare("UPDATE puestos SET activo = 1 - activo WHERE id = ?");
        $stmt->execute([$id]);
    }
    if (isset($_POST['edit'])) {
        $id = (int)$_POST['id'];
        $nombre = trim($_POST['nombre']);
        if ($nombre) {
            $stmt = $pdo->prepare("UPDATE puestos SET nombre = ? WHERE id = ?");
            $stmt->execute([$nombre, $id]);
            $message = 'Puesto actualizado correctamente.';
        }
    }
}

// Fetch all
$stmt = $pdo->query("SELECT * FROM puestos ORDER BY id");
$puestos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Gestión de Puestos</title>
  <link rel="stylesheet" href="../../public/css/style.css">
</head>
<body>
  <header>
    <h1>Admin › Puestos</h1>
    <nav>
      <a href="gestion_operadores.php">Operadores</a> |
      <a href="gestion_motivos.php">Motivos</a> |
      <a href="reportes.php">Reportes</a>
    </nav>
    <?php if($message): ?><p class="alert success"><?=htmlspecialchars($message)?></p><?php endif;?>
  </header>

  <section>
    <h2>Crear nuevo puesto</h2>
    <form method="post">
      <input type="text" name="nombre" placeholder="Nombre del puesto" required>
      <button type="submit" name="create">Crear</button>
    </form>
  </section>

  <section>
    <h2>Listado de puestos</h2>
    <table>
      <thead>
        <tr><th>ID</th><th>Nombre</th><th>Activo</th><th>Acciones</th></tr>
      </thead>
      <tbody>
      <?php foreach($puestos as $p): ?>
        <tr>
          <td><?= $p['id'] ?></td>
          <td>
            <form method="post" style="display:inline">
              <input type="hidden" name="id" value="<?= $p['id'] ?>">
              <input type="text" name="nombre" value="<?= htmlspecialchars($p['nombre']) ?>" required>
              <button type="submit" name="edit">✏️</button>
            </form>
          </td>
          <td><?= $p['activo'] ? 'Sí' : 'No' ?></td>
          <td>
            <form method="post" style="display:inline">
              <input type="hidden" name="id" value="<?= $p['id'] ?>">
              <button type="submit" name="toggle">
                <?= $p['activo'] ? 'Desactivar' : 'Activar' ?>
              </button>
            </form>
          </td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  </section>
</body>
</html>
