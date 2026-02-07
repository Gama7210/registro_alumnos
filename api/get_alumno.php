<?php
require __DIR__ . '/../inc/db.php';
$id = intval($_GET['id'] ?? 0);
if(!$id) json_response(['error'=>'Falta id']);
$stmt = $pdo->prepare('SELECT a.*, g.codigo FROM alumnos a JOIN grupos g ON g.id=a.grupo_id WHERE a.id=?');
$stmt->execute([$id]);
$row = $stmt->fetch();
json_response(['alumno'=>$row]);
