<?php
require __DIR__ . '/../inc/db.php';

$all = isset($_GET['all']) && intval($_GET['all'])===1;

$colCheck = $pdo->prepare("SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='grados' AND COLUMN_NAME='activo'");
$colCheck->execute();
$hasActivo = $colCheck->fetchColumn() > 0;

if($hasActivo && !$all){
	$stmt = $pdo->query("SELECT id, grado, COALESCE(activo,1) AS activo FROM grados WHERE COALESCE(activo,1)=1 ORDER BY grado");
}else{
	$stmt = $pdo->query("SELECT id, grado, COALESCE(activo,1) AS activo FROM grados ORDER BY grado");
}

$grados = $stmt->fetchAll();
json_response(['grados'=>$grados]);
