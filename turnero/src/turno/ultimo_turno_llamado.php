<?php
// Mostrar errores para depuración (desactivalo en producción)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../config/config.php';

header('Content-Type: application/json');

$puesto_id = isset($_GET['puesto_id']) ? (int) $_GET['puesto_id'] : null;

try {
    $pdo = getConnection();

    if ($puesto_id) {
        // Último turno llamado de ese puesto
        $stmt = $pdo->prepare(
            "SELECT t.id, t.numero_turno, t.nombre, t.apellido, p.nombre AS puesto, t.fecha_llamado
             FROM turnos t
             JOIN puestos p ON t.puesto_llamado_id = p.id
             WHERE t.estado = 'llamado' AND t.puesto_llamado_id = ?
             ORDER BY t.fecha_llamado DESC
             LIMIT 1"
        );
        $stmt->execute([$puesto_id]);
    } else {
        // Último turno llamado en general
        $stmt = $pdo->query(
            "SELECT t.id, t.numero_turno, t.nombre, t.apellido, p.nombre AS puesto, t.fecha_llamado
             FROM turnos t
             JOIN puestos p ON t.puesto_llamado_id = p.id
             WHERE t.estado = 'llamado'
             ORDER BY t.fecha_llamado DESC
             LIMIT 1"
        );
    }

    $turno = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($turno) {
        echo json_encode([
            'success' => true,
            'turno'   => $turno
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'No hay turnos llamados aún.'
        ]);
    }
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error al obtener el último turno: ' . $e->getMessage()
    ]);
}
