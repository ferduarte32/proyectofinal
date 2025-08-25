<?php
// src/test_sesion.php
session_start();
header('Content-Type: application/json');

echo json_encode([
    'logueado'     => isset($_SESSION['operador_id']),
    'operador_id'  => $_SESSION['operador_id'] ?? null,
    // Cambiamos 'nombre' por 'usuario' para reflejar la key que sí seteaste
    'usuario'      => $_SESSION['usuario'] ?? null,
    'es_admin'     => $_SESSION['es_admin'] ?? false,
]);
