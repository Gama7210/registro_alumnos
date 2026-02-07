<?php
require __DIR__ . '/../inc/db.php';
$in = json_decode(file_get_contents('php://input'), true);
if(!$in) json_response(['error'=>'no input']);
$id = intval($in['id'] ?? 0);
$activo = intval($in['activo'] ?? 0);
if(!$id) json_response(['error'=>'missing id']);
$stmt = $pdo->prepare('UPDATE grupos SET activo=? WHERE id=?');
$stmt->execute([$activo, $id]);
json_response(['success'=>true]);
