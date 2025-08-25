<?php
// src/turno/obtener_pendientes.php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');

require_once __DIR__ . '/../config/config.php';
$pdo = getConnection();

session_start();
$puesto_id = $_SESSION['puesto_id'] ?? null;

if (!$puesto_id) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'No autorizado']);
    exit;
}

try {
    $stmt = $pdo->prepare("
        SELECT 
          t.id,
          t.numero_turno,
          t.nombre,
          t.apellido,
          m.nombre AS motivo,
          TIMESTAMPDIFF(MINUTE, t.fecha_creacion, NOW()) AS minutos_espera
        FROM turnos t
        JOIN motivos m ON t.motivo_id = m.id
        JOIN puestos_motivos pm ON m.id = pm.motivo_id
        WHERE t.estado = 'espera'
          AND pm.puesto_id = ?
        ORDER BY t.fecha_creacion ASC
    ");
    $stmt->execute([$puesto_id]);
    $turnos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['success' => true, 'turnos' => $turnos]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
