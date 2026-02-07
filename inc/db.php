<?php
// Configuración de conexión PDO
$DB_HOST = '127.0.0.1';
$DB_NAME = 'registro_alumnos';
$DB_USER = 'root';
$DB_PASS = '724058';

try {
    $pdo = new PDO("mysql:host=$DB_HOST;dbname=$DB_NAME;charset=utf8mb4", $DB_USER, $DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'No se pudo conectar a la base de datos: ' . $e->getMessage()]);
    exit;
}

function json_response($data){
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data);
    exit;
}

?>
