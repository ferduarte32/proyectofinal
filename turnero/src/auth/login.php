<?php
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
