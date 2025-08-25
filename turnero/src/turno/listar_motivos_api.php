<?php
// src/turno/listar_motivos_api.php

// Mostrar errores en desarrollo (desactiva en producción)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');

// Incluir configuración de base de datos
require_once __DIR__ . '/../config/config.php';
$pdo = getConnection();

try {
    $stmt = $pdo->query("
        SELECT m.id, m.nombre, m.prefijo
        FROM motivos m
        WHERE m.activo = 1
        ORDER BY m.nombre
    ");
    $motivos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'motivos' => $motivos
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error al listar motivos: ' . $e->getMessage()
    ]);
}
