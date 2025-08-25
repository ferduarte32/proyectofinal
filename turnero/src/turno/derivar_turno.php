<?php
// src/turno/derivar_turno.php

// Mostrar errores para depuración (desactívalo en producción)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Configuración de la cookie de sesión (antes de session_start())
ini_set('session.cookie_path', '/turnero');
ini_set('session.cookie_httponly', 1);

// Cabecera JSON
header('Content-Type: application/json');

// Cargar config (sin ini_set) y luego auth/middleware (que hace session_start())
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../auth/middleware.php';

// Ahora ya hay $_SESSION iniciado por middleware.php
// Validación de operador logueado (además del usuario que ya validó el middleware)
if (!isset($_SESSION['operador_id'])) {
    http_response_code(401);
    echo json_encode([
        'success' => false,
        'message' => 'No autorizado. Debés ser operador.'
    ]);
    exit;
}

$operador_id       = $_SESSION['operador_id'];

// Leer los datos enviados por JSON
$data               = json_decode(file_get_contents('php://input'), true);
$turno_id           = $data['turno_id']           ?? null;
$puesto_destino_id  = $data['puesto_destino_id']  ?? null;

if (!$turno_id || !$puesto_destino_id) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Datos incompletos.'
    ]);
    exit;
}

try {
    // Obtener conexión
    $pdo = getConnection();

    // Verificar que el turno exista
    $stmt = $pdo->prepare("SELECT id FROM turnos WHERE id = ?");
    $stmt->execute([$turno_id]);
    if (!$stmt->fetch()) {
        http_response_code(404);
        echo json_encode([
            'success' => false,
            'message' => 'El turno no existe.'
        ]);
        exit;
    }

    // Actualizar el turno como derivado
    $stmt = $pdo->prepare("
        UPDATE turnos 
        SET 
            puesto_id      = ?,
            estado         = 'espera',
            derivado_por   = ?,
            fecha_derivado = NOW()
        WHERE id = ?
    ");
    $stmt->execute([$puesto_destino_id, $operador_id, $turno_id]);

    echo json_encode([
        'success' => true,
        'message' => 'Turno derivado correctamente.'
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error al derivar el turno: ' . $e->getMessage()
    ]);
}
