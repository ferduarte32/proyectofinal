<?php

require_once __DIR__ . '/../auth/middleware.php';
require_once __DIR__ . '/../config/config.php';



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
