<?php
// src/admin/gestion_motivos.php
require_once __DIR__ . '/../auth/middleware.php';
require_once __DIR__ . '/../config/config.php';
$pdo = getConnection();

// Fetch puestos for association
$puestos = $pdo->query("SELECT id, nombre FROM puestos WHERE activo = 1")->fetchAll(PDO::FETCH_ASSOC);

$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Create
    if (isset($_POST['create'])) {
        $nombre = trim($_POST['nombre']);
        $prefijo = strtoupper(trim($_POST['prefijo']));
        $asociados = $_POST['puestos'] ?? [];
        if ($nombre && $prefijo) {
            $pdo->beginTransaction();
            $stmt = $pdo->prepare("INSERT INTO motivos (nombre, prefijo, activo) VALUES (?, ?, 1)");
            $stmt->execute([$nombre, $prefijo]);
            $motivo_id = $pdo->lastInsertId();
            $stmt = $pdo->prepare("INSERT INTO puestos_motivos (puesto_id, motivo_id) VALUES (?, ?)");
            foreach ($asociados as $pid) {
                $stmt->execute([(int)$pid, $motivo_id]);
            }
            $pdo->commit();
            $message = 'Motivo creado correctamente.';
        }
    }
    // Toggle active
    if (isset($_POST['toggle'])) {
        $id = (int)$_POST['id'];
        $stmt = $pdo->prepare("UPDATE motivos SET activo = 1 - activo WHERE id = ?");
        $stmt->execute([$id]);
    }
    // Edit
    if (isset($_POST['edit'])) {
        $id = (int)$_POST['id'];
        $nombre = trim($_POST['nombre']);
        $prefijo = strtoupper(trim($_POST['prefijo']));
        $asociados = $_POST['puestos'] ?? [];
        if ($nombre && $prefijo) {
            $pdo->beginTransaction();
            $stmt = $pdo->prepare("UPDATE motivos SET nombre = ?, prefijo = ? WHERE id = ?");
            $stmt->execute([$nombre, $prefijo, $id]);
            // Reemplazar asociaciones
            $pdo->prepare("DELETE FROM puestos_motivos WHERE motivo_id = ?")->execute([$id]);
            $stmt = $pdo->prepare("INSERT INTO puestos_motivos (puesto_id, motivo_id) VALUES (?, ?)");
            foreach ($asociados as $pid) {
                $stmt->execute([(int)$pid, $id]);
            }
            $pdo->commit();
            $message = 'Motivo actualizado correctamente.';
        }
    }
}

// Fetch motivos
$stmt = $pdo->query("SELECT * FROM motivos ORDER BY id");
$motivos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Gestión de Motivos</title>
  <link rel="stylesheet" href="../../public/css/style.css">
</head>
<body>
  <header>
    <h1>Admin › Motivos</h1>
    <nav>
      <a href="gestion_puestos.php">Puestos</a> |
      <a href="gestion_operadores.php">Operadores</a> |
      <a href="reportes.php">Reportes</a>
    </nav>
    <?php if($message): ?><p class="alert success"><?=htmlspecialchars($message)?></p><?php endif;?>
  </header>

  <section>
    <h2>Crear nuevo motivo</h2>
    <form method="post">
      <input type="text" name="nombre" placeholder="Nombre del motivo" required>
      <input type="text" name="prefijo" placeholder="Prefijo (p.ej. RT)" required>
      <label>Asociar a puestos:</label><br>
      <?php foreach($puestos as $p): ?>
        <label>
          <input type="checkbox" name="puestos[]" value="<?= $p['id'] ?>"> <?= htmlspecialchars($p['nombre']) ?>
        </label><br>
      <?php endforeach; ?>
      <button type="submit" name="create">Crear</button>
    </form>
  </section>

  <section>
    <h2>Listado de motivos</h2>
    <table>
      <thead>
        <tr><th>ID</th><th>Nombre</th><th>Prefijo</th><th>Act.</th><th>Puestos</th><th>Acciones</th></tr>
      </thead>
      <tbody>
      <?php foreach($motivos as $m): ?>
        <?php
          // Fetch associated puestos
          $stmt2 = $pdo->prepare("SELECT p.nombre FROM puestos p 
                                   JOIN puestos_motivos pm ON p.id=pm.puesto_id 
                                   WHERE pm.motivo_id = ?");
          $stmt2->execute([$m['id']]);
          $asoc = $stmt2->fetchAll(PDO::FETCH_COLUMN);
        ?>
        <tr>
          <td><?= $m['id'] ?></td>
          <td><?= htmlspecialchars($m['nombre']) ?></td>
          <td><?= htmlspecialchars($m['prefijo']) ?></td>
          <td><?= $m['activo'] ? 'Sí' : 'No' ?></td>
          <td><?= implode(', ', $asoc) ?></td>
          <td>
            <form method="post" style="display:inline">
              <input type="hidden" name="id" value="<?= $m['id'] ?>">
              <button type="submit" name="toggle"><?= $m['activo'] ? 'Desactivar' : 'Activar' ?></button>
            </form>
            <form method="get" action="" style="display:inline">
              <!-- Podrías enlazar a un formulario de edición más completo -->
            </form>
          </td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  </section>
</body>
</html>
