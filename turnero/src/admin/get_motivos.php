<?php
// src/admin/get_motivos.php

// Incluimos la configuración y obtenemos PDO
require_once __DIR__ . '/../config/config.php';
header('Content-Type: application/json');

try {
    // Conexión
    $pdo = getConnection();

    // Consulta de motivos activos
    $stmt = $pdo->prepare("
        SELECT id, nombre
        FROM motivos
        WHERE activo = 1
        ORDER BY nombre
    ");
    $stmt->execute();
    $motivos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'motivos' => $motivos
    ]);
} catch (Exception $e) {
    // Cualquier fallo devolvemos JSON con error
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error al obtener los motivos: ' . $e->getMessage()
    ]);
}
