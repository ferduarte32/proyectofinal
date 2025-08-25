src\admin\gestion_motivos.php <?php
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
src\admin\gestion_operadores.php <?php
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
src\admin\gestion_puestos.php <?php
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
src\admin\get_motivos.php <?php
require_once '../../src/config/db.php';

header('Content-Type: application/json');

try {
    $stmt = $pdo->prepare("SELECT id, nombre FROM motivos WHERE activo = 1 ORDER BY nombre");
    $stmt->execute();
    $motivos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'motivos' => $motivos
    ]);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error al obtener los motivos: ' . $e->getMessage()
    ]);
}
src\admin\get_puestos.php <?php
require_once '../../src/config/config.php';

header('Content-Type: application/json');

try {
    $pdo = getConnection();
    $stmt = $pdo->prepare("SELECT id, nombre FROM puestos WHERE activo = 1 ORDER BY nombre");
    $stmt->execute();
    $puestos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'puestos' => $puestos
    ]);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error al obtener puestos: ' . $e->getMessage()
    ]);
}
src\admin\reportes.php <?php
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
src\auth\login.php<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../config/config.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../../public/login.html');
    exit;
}

$usuario = trim($_POST['usuario'] ?? '');
$clave   = $_POST['clave'] ?? '';

if (!$usuario || !$clave) {
    header('Location: ../../public/login.html?error=Campos+vacíos');
    exit;
}

try {
    $pdo = getConnection();
    $stmt = $pdo->prepare("SELECT id, usuario, clave, puesto_id, activo, es_admin FROM operadores WHERE usuario = ? LIMIT 1");
    $stmt->execute([$usuario]);
    $op = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$op || !$op['activo'] || !password_verify($clave, $op['clave'])) {
        header('Location: ../../public/login.html?error=Credenciales+inválidas');
        exit;
    }

    $_SESSION['operador_id'] = $op['id'];
    $_SESSION['usuario']     = $op['usuario'];
    $_SESSION['puesto_id']   = $op['puesto_id'];
    $_SESSION['es_admin']    = (int)$op['es_admin'] === 1;

    // Redirigir según rol
    if ($_SESSION['es_admin']) {
        header('Location: ../../public/index.html');
    } else {
        header('Location: ../../public/panel_operadores.html');
    }
    exit;
} catch (Exception $e) {
    header('Location: ../../public/login.html?error=Error+interno');
    exit;
}
src\auth\logout.php <?php
// src/auth/logout.php
session_start();
session_unset();
session_destroy();

// Redirige al login
header('Location: ../../public/login.php');
exit;
src\auth\middleware.php<?php
// src/auth/middleware.php
session_start();

// Rutas que no requieren autenticación
$publicPages = [
    'login.php',
    'logout.php',
    '../turno/ultimo_turno_llamado.php'
];

$current = basename($_SERVER['PHP_SELF']);

// Si no está autenticado y no es página pública, redirige al login
if (!isset($_SESSION['operador_id']) && !in_array($current, $publicPages)) {
    header('Location: ../../public/login.php');
    exit;
}

// Páginas exclusivas para admin
$adminPages = ['gestion_puestos.php','gestion_operadores.php','gestion_motivos.php','reportes.php'];
if (in_array($current, $adminPages) && empty($_SESSION['es_admin'])) {
    header('HTTP/1.1 403 Forbidden');
    echo 'Acceso denegado';
    exit;
}
src\config\config.php<?php
// config/config.php

// Datos de conexión a la base de datos
define('DB_HOST',     'localhost');
define('DB_NAME',     'c2690274_turnero');
define('DB_USER',     'c2690274_turnero');
define('DB_PASS',     'KO74papivu');

// URL base de la aplicación
define('BASE_URL',    'https://fercode.com/turnero/');

// Charset
define('DB_CHARSET',  'utf8mb4');

// Opciones de PDO
define('PDO_OPTIONS', [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
]);

/**
 * Devuelve una conexión PDO lista para usar
 *
 * @return \PDO
 */
function getConnection() {
    $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=' . DB_CHARSET;
    return new PDO($dsn, DB_USER, DB_PASS, PDO_OPTIONS);
}
src\templates\footer.php <?php
// src/templates/footer.php
?>
  </main>
  <footer class="site-footer">
    <div class="container">
      <p>&copy; 2025 Gigared. Todos los derechos reservados.</p>
    </div>
  </footer>
</body>
</html>
src\templates\header.php<?php
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
src\tools\generar_hash.php <?php
echo password_hash('fer32', PASSWORD_DEFAULT);
src\turno\crear_turno.php<?php
require_once '../../src/config/db.php';

header('Content-Type: application/json');

// Leer los datos enviados en formato JSON
$data = json_decode(file_get_contents("php://input"), true);

$nombre = trim($data['nombre'] ?? '');
$apellido = trim($data['apellido'] ?? '');
$motivo_id = $data['motivo_id'] ?? null;

if (!$nombre || !$apellido || !$motivo_id) {
    echo json_encode([
        'success' => false,
        'message' => 'Faltan datos requeridos.'
    ]);
    exit;
}

try {
    // Obtener el motivo y su prefijo
    $stmt = $pdo->prepare("SELECT m.id, m.nombre as motivo, m.prefijo, p.id as puesto_id
        FROM motivos m
        JOIN puestos_motivos pm ON m.id = pm.motivo_id
        JOIN puestos p ON pm.puesto_id = p.id
        WHERE m.id = ?
        LIMIT 1
    ");
    $stmt->execute([$motivo_id]);
    $motivo = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$motivo) {
        echo json_encode([
            'success' => false,
            'message' => 'Motivo inválido.'
        ]);
        exit;
    }

    // Generar número correlativo
    $stmt = $pdo->prepare("SELECT COUNT(*) as cantidad FROM turnos WHERE motivo_id = ? AND DATE(fecha_creacion) = CURDATE()");
    $stmt->execute([$motivo_id]);
    $countData = $stmt->fetch(PDO::FETCH_ASSOC);
    $numero_correlativo = $countData['cantidad'] + 1;

    $numero_turno = $motivo['prefijo'] . str_pad($numero_correlativo, 3, '0', STR_PAD_LEFT);

    // Insertar el turno
    $stmt = $pdo->prepare("INSERT INTO turnos (nombre, apellido, motivo_id, puesto_id, numero_turno, estado, fecha_creacion)
        VALUES (?, ?, ?, ?, ?, 'espera', NOW())");
    $stmt->execute([
        $nombre,
        $apellido,
        $motivo_id,
        $motivo['puesto_id'],
        $numero_turno
    ]);

    echo json_encode([
        'success' => true,
        'message' => 'Turno generado exitosamente.',
        'numero_turno' => $numero_turno
    ]);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error al crear el turno: ' . $e->getMessage()
    ]);
}
src\turno\derivar_turno.php <?php
require_once __DIR__ . '/../auth/middleware.php';
require_once '../../src/config/db.php';

header('Content-Type: application/json');

// Validación del operador logueado
session_start();
if (!isset($_SESSION['operador_id'])) {
    echo json_encode([
        'success' => false,
        'message' => 'No autorizado.'
    ]);
    exit;
}

$operador_id = $_SESSION['operador_id'];

// Leer los datos enviados por JSON
$data = json_decode(file_get_contents("php://input"), true);
$turno_id = $data['turno_id'] ?? null;
$puesto_destino_id = $data['puesto_destino_id'] ?? null;

if (!$turno_id || !$puesto_destino_id) {
    echo json_encode([
        'success' => false,
        'message' => 'Datos incompletos.'
    ]);
    exit;
}

try {
    // Verificar que el turno exista
    $stmt = $pdo->prepare("SELECT * FROM turnos WHERE id = ?");
    $stmt->execute([$turno_id]);
    $turno = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$turno) {
        echo json_encode([
            'success' => false,
            'message' => 'El turno no existe.'
        ]);
        exit;
    }

    // Actualizar el turno con el nuevo puesto destino y marcarlo como derivado
    $stmt = $pdo->prepare("
        UPDATE turnos 
        SET puesto_id = ?, estado = 'espera', derivado_por = ?, fecha_derivado = NOW()
        WHERE id = ?
    ");
    $stmt->execute([$puesto_destino_id, $operador_id, $turno_id]);

    echo json_encode([
        'success' => true,
        'message' => 'Turno derivado correctamente.'
    ]);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error al derivar el turno: ' . $e->getMessage()
    ]);
}
src\turno\finalizar_turno.php<?php
require_once __DIR__ . '/../auth/middleware.php';
require_once '../../src/config/db.php';


header('Content-Type: application/json');

session_start();
if (!isset($_SESSION['operador_id'])) {
    echo json_encode([
        'success' => false,
        'message' => 'No autorizado.'
    ]);
    exit;
}

$operador_id = $_SESSION['operador_id'];

// Leer los datos enviados por JSON
$data = json_decode(file_get_contents("php://input"), true);
$turno_id = $data['turno_id'] ?? null;
$resultado = $data['resultado'] ?? null;
$observaciones = $data['observaciones'] ?? '';

if (!$turno_id || !$resultado) {
    echo json_encode([
        'success' => false,
        'message' => 'Datos incompletos.'
    ]);
    exit;
}

try {
    // Verificar que el turno exista
    $stmt = $pdo->prepare("SELECT * FROM turnos WHERE id = ?");
    $stmt->execute([$turno_id]);
    $turno = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$turno) {
        echo json_encode([
            'success' => false,
            'message' => 'El turno no existe.'
        ]);
        exit;
    }

    // Actualizar el estado del turno a "finalizado"
    $stmt = $pdo->prepare("
        UPDATE turnos
        SET estado = 'finalizado',
            resultado = ?,
            observaciones = ?,
            fecha_finalizado = NOW()
        WHERE id = ?
    ");
    $stmt->execute([$resultado, $observaciones, $turno_id]);

    echo json_encode([
        'success' => true,
        'message' => 'Turno finalizado correctamente.'
    ]);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error al finalizar el turno: ' . $e->getMessage()
    ]);
}
src\turno\llamar_turno.php<?php
require_once __DIR__ . '/../auth/middleware.php';

require_once '../../src/config/db.php';


header('Content-Type: application/json');

// Validación del operador logueado
session_start();
if (!isset($_SESSION['operador_id'], $_SESSION['puesto_id'])) {
    echo json_encode([
        'success' => false,
        'message' => 'No autorizado.'
    ]);
    exit;
}

$operador_id = $_SESSION['operador_id'];
$puesto_id = $_SESSION['puesto_id'];

// Validación de datos recibidos
$data = json_decode(file_get_contents("php://input"), true);
$turno_id = $data['turno_id'] ?? null;

if (!$turno_id) {
    echo json_encode([
        'success' => false,
        'message' => 'ID de turno no especificado.'
    ]);
    exit;
}

try {
    // Cambiar el estado del turno a "llamado"
    $stmt = $pdo->prepare("
        UPDATE turnos 
        SET estado = 'llamado', operador_id = ?, puesto_llamado_id = ?, fecha_llamado = NOW() 
        WHERE id = ? AND estado = 'espera'
    ");
    $stmt->execute([$operador_id, $puesto_id, $turno_id]);

    if ($stmt->rowCount() === 0) {
        echo json_encode([
            'success' => false,
            'message' => 'No se pudo llamar al turno. Puede que ya haya sido llamado.'
        ]);
        exit;
    }

    echo json_encode([
        'success' => true,
        'message' => 'Turno llamado correctamente.'
    ]);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error al llamar el turno: ' . $e->getMessage()
    ]);
}
src\turno\obtener_pendientes.php <?php
require_once __DIR__ . '/../auth/middleware.php';

require_once '../../src/config/db.php';


header('Content-Type: application/json');

// Validación del operador logueado (ejemplo básico, ajustalo a tu sistema de sesión)
session_start();
if (!isset($_SESSION['operador_id'], $_SESSION['puesto_id'])) {
    echo json_encode([
        'success' => false,
        'message' => 'No autorizado.'
    ]);
    exit;
}

$puesto_id = $_SESSION['puesto_id'];

try {
    // Buscar turnos pendientes para el puesto del operador
    $stmt = $pdo->prepare("
        SELECT t.id, t.nombre, t.apellido, t.prefijo, t.numero_correlativo, t.estado, 
               m.nombre AS motivo, t.fecha_creacion, TIMESTAMPDIFF(MINUTE, t.fecha_creacion, NOW()) AS minutos_espera
        FROM turnos t
        JOIN motivos m ON t.motivo_id = m.id
        WHERE t.estado = 'espera' 
        AND m.puesto_id = ?
        ORDER BY t.fecha_creacion ASC
    ");
    $stmt->execute([$puesto_id]);
    $turnos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'turnos' => $turnos
    ]);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error al obtener turnos: ' . $e->getMessage()
    ]);
}
src\turno\ultimo_turno_llamado.php<?php
require_once '../../src/config/db.php';

header('Content-Type: application/json');

// Pantalla pública o TV: devuelve el último turno llamado por puesto
$puesto_id = isset($_GET['puesto_id']) ? (int) $_GET['puesto_id'] : null;

try {
    if ($puesto_id) {
        // Obtener último turno llamado de ese puesto
        $stmt = $pdo->prepare(
            "SELECT t.id, t.numero_turno, t.nombre, t.apellido, m.nombre AS motivo, t.fecha_llamado
             FROM turnos t
             JOIN motivos m ON t.motivo_id = m.id
             WHERE t.estado = 'llamado' AND t.puesto_llamado_id = ?
             ORDER BY t.fecha_llamado DESC
             LIMIT 1"
        );
        $stmt->execute([$puesto_id]);
    } else {
        // Si no se especifica puesto, devolver el último en general
        $stmt = $pdo->query(
            "SELECT t.id, t.numero_turno, t.nombre, t.apellido, m.nombre AS motivo, t.fecha_llamado
             FROM turnos t
             JOIN motivos m ON t.motivo_id = m.id
             WHERE t.estado = 'llamado'
             ORDER BY t.fecha_llamado DESC
             LIMIT 1"
        );
    }

    $turno = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($turno) {
        echo json_encode([
            'success' => true,
            'turno'   => $turno
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'No hay turnos llamados aún.'
        ]);
    }
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error al obtener el último turno: ' . $e->getMessage()
    ]);
}
