<?php
// src/turno/crear_turno.php

// Mostrar errores en desarrollo
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');

// Incluir configuración y obtener PDO
require_once __DIR__ . '/../config/config.php';
$pdo = getConnection();

// Leer el cuerpo JSON
$raw = file_get_contents('php://input');
$data = json_decode($raw, true);

// Depuración rápida (comenta en producción)
// file_put_contents(__DIR__.'/debug.log', $raw . "\n", FILE_APPEND);

$nombre    = trim($data['nombre']    ?? '');
$apellido  = trim($data['apellido']  ?? '');
$motivo_id = (int)  ($data['motivo_id'] ?? 0);

if (!$nombre || !$apellido || !$motivo_id) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Faltan datos requeridos.'
    ]);
    exit;
}

try {
    // Obtener motivo y prefijo + puesto
    $stmt = $pdo->prepare("
        SELECT m.prefijo, pm.puesto_id
        FROM motivos m
        JOIN puestos_motivos pm ON m.id = pm.motivo_id
        WHERE m.id = ?
        LIMIT 1
    ");
    $stmt->execute([$motivo_id]);
    $motivo = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$motivo) {
        http_response_code(404);
        echo json_encode([
            'success' => false,
            'message' => 'Motivo no encontrado.'
        ]);
        exit;
    }

    // Calcular correlativo del día
    $stmt = $pdo->prepare("
        SELECT COUNT(*) AS cnt 
        FROM turnos 
        WHERE motivo_id = ? 
          AND DATE(fecha_creacion) = CURDATE()
    ");
    $stmt->execute([$motivo_id]);
    $cnt = (int)$stmt->fetchColumn();
    $num = $cnt + 1;
    $numero_turno = $motivo['prefijo'] . str_pad($num, 3, '0', STR_PAD_LEFT);

    // Insertar el turno
    $stmt = $pdo->prepare("
        INSERT INTO turnos 
          (nombre, apellido, motivo_id, puesto_id, numero_turno, estado, fecha_creacion)
        VALUES (?, ?, ?, ?, ?, 'espera', NOW())
    ");
    $stmt->execute([
        $nombre,
        $apellido,
        $motivo_id,
        $motivo['puesto_id'],
        $numero_turno
    ]);

    // Responder OK
    echo json_encode([
        'success' => true,
        'numero_turno' => $numero_turno
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error al crear turno: ' . $e->getMessage()
    ]);
}
