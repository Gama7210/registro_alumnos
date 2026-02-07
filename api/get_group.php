<?php
require __DIR__ . '/../inc/db.php';
$id = intval($_GET['id'] ?? 0);
if(!$id) json_response(['error'=>'Falta id']);
$stmt = $pdo->prepare('SELECT g.*, c.nombre as carrera_nombre, c.abreviatura FROM grupos g JOIN carreras c ON c.id=g.carrera_id WHERE g.id=?');
$stmt->execute([$id]);
$row = $stmt->fetch();
json_response(['grupo'=>$row]);
