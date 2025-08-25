<?php
// src/admin/gestion_operadores.php
require_once __DIR__ . '/../auth/middleware.php';

require_once __DIR__ . '/../config/config.php';
$pdo = getConnection();

// Handle create/update/delete
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['create'])) {
        $usuario = trim($_POST['usuario']);
        $clave   = password_hash($_POST['clave'], PASSWORD_DEFAULT);
        $puesto  = (int)$_POST['puesto_id'];
        if ($usuario && $puesto) {
            $stmt = $pdo->prepare("INSERT INTO operadores (usuario, clave, puesto_id, activo) VALUES (?, ?, ?, 1)");
            $stmt->execute([$usuario, $clave, $puesto]);
            $message = 'Operador creado correctamente.';
        }
    }
    if (isset($_POST['toggle'])) {
        $id = (int)$_POST['id'];
        $stmt = $pdo->prepare("UPDATE operadores SET activo = 1 - activo WHERE id = ?");
        $stmt->execute([$id]);
    }
    if (isset($_POST['edit'])) {
        $id      = (int)$_POST['id'];
        $usuario = trim($_POST['usuario']);
        $puesto  = (int)$_POST['puesto_id'];
        if ($usuario && $puesto) {
            $sql = "UPDATE operadores SET usuario = ?, puesto_id = ? ";
            $params = [$usuario, $puesto];
            if (!empty($_POST['clave'])) {
                $sql .= ", clave = ? ";
                $params[] = password_hash($_POST['clave'], PASSWORD_DEFAULT);
            }
            $sql .= "WHERE id = ?";
            $params[] = $id;
            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            $message = 'Operador actualizado correctamente.';
        }
    }
}

// Fetch data
$puestos = $pdo->query("SELECT id, nombre FROM puestos WHERE activo = 1")->fetchAll(PDO::FETCH_ASSOC);
$ops = $pdo->query("SELECT o.*, p.nombre AS puesto FROM operadores o JOIN puestos p ON o.puesto_id = p.id ORDER BY o.id")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Gestión de Operadores</title>
  <link rel="stylesheet" href="../../public/css/style.css">
</head>
<body>
  <header>
    <h1>Admin › Operadores</h1>
    <nav>
      <a href="gestion_puestos.php">Puestos</a> |
      <a href="gestion_motivos.php">Motivos</a> |
      <a href="reportes.php">Reportes</a>
    </nav>
    <?php if($message): ?><p class="alert success"><?=htmlspecialchars($message)?></p><?php endif;?>
  </header>

  <section>
    <h2>Crear nuevo operador</h2>
    <form method="post">
      <input type="text" name="usuario" placeholder="Usuario" required>
      <input type="password" name="clave" placeholder="Contraseña" required>
      <select name="puesto_id" required>
        <option value="">Seleccioná Puesto</option>
        <?php foreach($puestos as $p): ?>
          <option value="<?= $p['id'] ?>"><?= htmlspecialchars($p['nombre']) ?></option>
        <?php endforeach; ?>
      </select>
      <button type="submit" name="create">Crear</button>
    </form>
  </section>

  <section>
    <h2>Listado de operadores</h2>
    <table>
      <thead>
        <tr><th>ID</th><th>Usuario</th><th>Puesto</th><th>Activo</th><th>Acciones</th></tr>
      </thead>
      <tbody>
      <?php foreach($ops as $o): ?>
        <tr>
          <td><?= $o['id'] ?></td>
          <td>
            <form method="post" style="display:inline">
              <input type="hidden" name="id" value="<?= $o['id'] ?>">
              <input type="text" name="usuario" value="<?= htmlspecialchars($o['usuario']) ?>" required>
          </td>
          <td>
              <select name="puesto_id" required>
              <?php foreach($puestos as $p): ?>
                <option value="<?= $p['id'] ?>" <?= $p['id']==$o['puesto_id']?'selected':'' ?>>
                  <?= htmlspecialchars($p['nombre']) ?>
                </option>
              <?php endforeach; ?>
              </select>
          </td>
          <td><?= $o['activo'] ? 'Sí' : 'No' ?></td>
          <td>
              <input type="password" name="clave" placeholder="Nueva clave">
              <button type="submit" name="edit">✏️</button>
            </form>
            <form method="post" style="display:inline">
              <input type="hidden" name="id" value="<?= $o['id'] ?>">
              <button type="submit" name="toggle"><?= $o['activo'] ? 'Desactivar' : 'Activar' ?></button>
            </form>
          </td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  </section>
</body>
</html>
