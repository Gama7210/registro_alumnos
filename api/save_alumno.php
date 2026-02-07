<?php
require __DIR__ . '/../inc/db.php';
$data = json_decode(file_get_contents('php://input'), true);
if(!$data) json_response(['error'=>'No data']);

$id = intval($data['id'] ?? 0);
$nombre = trim($data['nombre'] ?? '');
$ape_paterno = trim($data['ape_paterno'] ?? '');
$ape_materno = trim($data['ape_materno'] ?? '');
$grupo_id = intval($data['grupo_id'] ?? 0);

if(!$nombre || !$ape_paterno || !$grupo_id) json_response(['error'=>'Faltan datos']);

if($id){
  $up = $pdo->prepare('UPDATE alumnos SET nombre=?, ape_paterno=?, ape_materno=?, grupo_id=? WHERE id=?');
  $up->execute([$nombre,$ape_paterno,$ape_materno,$grupo_id,$id]);
  json_response(['success'=>true]);
} else {
  $ins = $pdo->prepare('INSERT INTO alumnos (nombre,ape_paterno,ape_materno,grupo_id) VALUES (?,?,?,?)');
  $ins->execute([$nombre,$ape_paterno,$ape_materno,$grupo_id]);
  json_response(['success'=>true,'id'=>$pdo->lastInsertId()]);
}
