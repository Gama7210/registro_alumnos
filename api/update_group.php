<?php
require __DIR__ . '/../inc/db.php';
$data = json_decode(file_get_contents('php://input'), true);
if(!$data) json_response(['error'=>'No data']);
$id = intval($data['id'] ?? 0);
$activo = isset($data['activo'])? intval($data['activo']) : null;
$grado = isset($data['grado'])? intval($data['grado']) : null;
$carrera_id = isset($data['carrera_id'])? intval($data['carrera_id']) : null;
$turno_id = isset($data['turno_id'])? intval($data['turno_id']) : null;

if(!$id) json_response(['error'=>'Falta id']);
$sets = [];
$params = [];
if($activo !== null){ $sets[] = 'activo=?'; $params[] = $activo; }
if($grado !== null){ $sets[] = 'grado=?'; $params[] = $grado; }
if($carrera_id !== null){ $sets[] = 'carrera_id=?'; $params[] = $carrera_id; }
if($turno_id !== null){ $sets[] = 'turno_id=?'; $params[] = $turno_id; }
if(count($sets)==0) json_response(['error'=>'Nada para actualizar']);
$params[] = $id;
$sql = 'UPDATE grupos SET ' . implode(',', $sets) . ' WHERE id=?';
$up = $pdo->prepare($sql);
$up->execute($params);
json_response(['success'=>true]);
