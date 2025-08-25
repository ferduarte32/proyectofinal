<?php
// src/auth/middleware.php
session_start();

$current = basename($_SERVER['PHP_SELF']);

// Rutas públicas (no necesitan login)
$publicPages = [
    'login.php',
    'logout.php',
    'ultimo_turno_llamado.php'
];

// Si no está logueado...
if (!isset($_SESSION['operador_id'])) {
    // Si es un AJAX de /turno/, devolvemos 401 JSON
    if (strpos($_SERVER['REQUEST_URI'], '/src/turno/') !== false) {
        http_response_code(401);
        header('Content-Type: application/json');
        echo json_encode([
            'success' => false,
            'message' => 'No autorizado'
        ]);
        exit;
    }
    // Si no es ruta pública, redirigir al login
    if (!in_array($current, $publicPages)) {
        header('Location: ../../public/login.php');
        exit;
    }
}

// Control de acceso admin
$adminPages = ['gestion_puestos.php','gestion_operadores.php','gestion_motivos.php','reportes.php'];
if (in_array($current, $adminPages) && empty($_SESSION['es_admin'])) {
    // Si fuese AJAX de turno, podrías devolver JSON 403 aquí también
    http_response_code(403);
    echo 'Acceso denegado';
    exit;
}
