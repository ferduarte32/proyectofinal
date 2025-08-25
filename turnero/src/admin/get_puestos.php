<?php
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
