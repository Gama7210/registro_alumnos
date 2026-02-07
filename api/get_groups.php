<?php
require __DIR__ . '/../inc/db.php';

$all = isset($_GET['all']) && intval($_GET['all'])===1;

$colCheck = $pdo->prepare("SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='grupos' AND COLUMN_NAME='activo'");
$colCheck->execute();
$hasActivo = $colCheck->fetchColumn() > 0;

if($hasActivo && !$all){
	$sql = 'SELECT g.*, c.nombre AS carrera, t.nombre AS turno FROM grupos g JOIN carreras c ON c.id=g.carrera_id JOIN turnos t ON t.id=g.turno_id WHERE COALESCE(g.activo,1)=1 ORDER BY g.id DESC';
}else{
	$sql = 'SELECT g.*, c.nombre AS carrera, t.nombre AS turno FROM grupos g JOIN carreras c ON c.id=g.carrera_id JOIN turnos t ON t.id=g.turno_id ORDER BY g.id DESC';
}

$stmt = $pdo->query($sql);
$grupos = $stmt->fetchAll();
json_response(['grupos'=>$grupos]);
