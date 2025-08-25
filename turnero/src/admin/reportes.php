<?php
require_once __DIR__ . '/../auth/middleware.php';

// src/admin/reportes.php
require_once __DIR__ . '/../config/config.php';
$pdo = getConnection();

// Procesar filtros
$desde = $_GET['desde']   ?? date('Y-m-01');
$hasta = $_GET['hasta']   ?? date('Y-m-d');
$puesto = (int)($_GET['puesto'] ?? 0);
$operador = (int)($_GET['operador'] ?? 0);
$motivo   = (int)($_GET['motivo']   ?? 0);

// Cargar listados para filtros
$puestos   = $pdo->query("SELECT id, nombre FROM puestos")->fetchAll(PDO::FETCH_ASSOC);
$operadores= $pdo->query("SELECT id, usuario FROM operadores")->fetchAll(PDO::FETCH_ASSOC);
$motivos   = $pdo->query("SELECT id, nombre FROM motivos")->fetchAll(PDO::FETCH_ASSOC);

// Armar WHERE dinámico
$where = ["t.fecha_finalizado BETWEEN ? AND ?"];
$params= [$desde . ' 00:00:00', $hasta . ' 23:59:59'];
if ($puesto)    { $where[] = "t.puesto_llamado_id = ?";    $params[] = $puesto; }
if ($operador) { $where[] = "t.operador_id = ?";          $params[] = $operador; }
if ($motivo)   { $where[] = "t.motivo_id = ?";            $params[] = $motivo; }
$sqlWhere = implode(' AND ', $where);

// Query principal
$sql = "
  SELECT o.usuario AS Operador, p.nombre AS Puesto, m.nombre AS Motivo,
         COUNT(*) AS Turnos,
         SUM(t.resultado = 'Cumplido') AS Cumplidos,
         SUM(t.resultado <> 'Cumplido') AS NoCumplidos,
         ROUND(AVG(TIMESTAMPDIFF(MINUTE, t.fecha_llamado, t.fecha_finalizado)),1) AS TiempoPromedio
  FROM turnos t
  JOIN operadores o ON t.operador_id = o.id
  JOIN puestos p    ON t.puesto_llamado_id = p.id
  JOIN motivos m    ON t.motivo_id = m.id
  WHERE $sqlWhere
  GROUP BY t.operador_id, t.puesto_llamado_id, t.motivo_id
  ORDER BY o.usuario
";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Reportes de Atención</title>
  <link rel="stylesheet" href="../../public/css/style.css">
</head>
<body>
  <header>
    <h1>Admin › Reportes</h1>
    <nav>
      <a href="gestion_puestos.php">Puestos</a> |
      <a href="gestion_operadores.php">Operadores</a> |
      <a href="gestion_motivos.php">Motivos</a>
    </nav>
  </header>

  <section>
    <h2>Filtros</h2>
    <form method="get">
      <label>Desde: <input type="date" name="desde" value="<?=htmlspecialchars($desde)?>"></label>
      <label>Hasta: <input type="date" name="hasta" value="<?=htmlspecialchars($hasta)?>"></label>
      <label>Puesto:
        <select name="puesto">
          <option value="0">Todos</option>
          <?php foreach($puestos as $p): ?>
            <option value="<?= $p['id'] ?>" <?= $p['id']==$puesto?'selected':'' ?>>
              <?= htmlspecialchars($p['nombre']) ?>
            </option>
          <?php endforeach; ?>
        </select>
      </label>
      <label>Operador:
        <select name="operador">
          <option value="0">Todos</option>
          <?php foreach($operadores as $o): ?>
            <option value="<?= $o['id'] ?>" <?= $o['id']==$operador?'selected':'' ?>>
              <?= htmlspecialchars($o['usuario']) ?>
            </option>
          <?php endforeach; ?>
        </select>
      </label>
      <label>Motivo:
        <select name="motivo">
          <option value="0">Todos</option>
          <?php foreach($motivos as $m): ?>
            <option value="<?= $m['id'] ?>" <?= $m['id']==$motivo?'selected':'' ?>>
              <?= htmlspecialchars($m['nombre']) ?>
            </option>
          <?php endforeach; ?>
        </select>
      </label>
      <button type="submit">Generar</button>
    </form>
  </section>

  <section>
    <h2>Resultados</h2>
    <table>
      <thead>
        <tr>
          <?php if(!empty($rows)): ?>
            <?php foreach(array_keys($rows[0]) as $col): ?>
              <th><?=htmlspecialchars($col)?></th>
            <?php endforeach; ?>
          <?php endif; ?>
        </tr>
      </thead>
      <tbody>
        <?php foreach($rows as $r): ?>
          <tr>
            <?php foreach($r as $cell): ?>
              <td><?=htmlspecialchars($cell)?></td>
            <?php endforeach; ?>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </section>
</body>
</html>
