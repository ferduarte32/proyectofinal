<?php
// src/turno/llamar_turno.php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');

require_once __DIR__ . '/../config/config.php';

session_start();
$operador_id = $_SESSION['operador_id'] ?? null;
$puesto_id = $_SESSION['puesto_id'] ?? null;

if (!$operador_id || !$puesto_id) {
    http_response_code(401);
    echo json_encode([
        'success' => false,
        'message' => 'No autorizado.'
    ]);
    exit;
}

// Leer datos JSON enviados
$data = json_decode(file_get_contents('php://input'), true);
$turno_id = $data['turno_id'] ?? null;

if (!$turno_id) {
    echo json_encode([
        'success' => false,
        'message' => 'ID de turno no especificado.'
    ]);
    exit;
}

try {
    $pdo = getConnection();

    $stmt = $pdo->prepare("
        UPDATE turnos
        SET estado = 'llamado', operador_id = ?, puesto_llamado_id = ?, fecha_llamado = NOW()
        WHERE id = ? AND estado = 'espera'
    ");

    $stmt->execute([$operador_id, $puesto_id, $turno_id]);

    if ($stmt->rowCount() === 0) {
        echo json_encode([
            'success' => false,
            'message' => 'No se pudo llamar al turno. Puede que ya haya sido llamado o no exista.'
        ]);
        exit;
    }

    echo json_encode([
        'success' => true,
        'message' => 'Turno llamado correctamente.'
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error al llamar el turno: ' . $e->getMessage()
    ]);
}
