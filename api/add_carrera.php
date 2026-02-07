<?php
require __DIR__ . '/../inc/db.php';
$data = json_decode(file_get_contents('php://input'), true);
if(!$data) json_response(['error'=>'No data']);
$nombre = trim($data['nombre'] ?? '');
$abreviatura = trim($data['abreviatura'] ?? '');
if(!$nombre || !$abreviatura) json_response(['error'=>'Faltan datos']);
$ins = $pdo->prepare('INSERT INTO carreras (nombre, abreviatura, activo) VALUES (?,?,1)');
$ins->execute([$nombre,$abreviatura]);
json_response(['success'=>true,'id'=>$pdo->lastInsertId()]);
