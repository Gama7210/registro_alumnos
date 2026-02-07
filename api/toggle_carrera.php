<?php
require __DIR__ . '/../inc/db.php';
$data = json_decode(file_get_contents('php://input'), true);
if(!$data) json_response(['error'=>'No data']);
$id = intval($data['id'] ?? 0);
$activo = intval($data['activo'] ?? 0);
if(!$id) json_response(['error'=>'Faltan datos']);
$up = $pdo->prepare('UPDATE carreras SET activo=? WHERE id=?');
$up->execute([$activo,$id]);
json_response(['success'=>true]);
