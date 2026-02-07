<?php
require __DIR__ . '/../inc/db.php';
$carrera_id = intval($_GET['carrera_id'] ?? 0);
$turno_id = intval($_GET['turno_id'] ?? 0);
$grado = intval($_GET['grado'] ?? 0);
if(!$carrera_id || !$turno_id || !$grado){ json_response(['error'=>'Faltan parámetros']); }

$stmt = $pdo->prepare('SELECT MAX(seq) AS m FROM grupos WHERE carrera_id=? AND turno_id=? AND grado=?');
$stmt->execute([$carrera_id,$turno_id,$grado]);
$row = $stmt->fetch();
$next = ($row && $row['m'])? intval($row['m'])+1 : 1;

$c = $pdo->prepare('SELECT nombre, abreviatura FROM carreras WHERE id=?'); $c->execute([$carrera_id]); $c = $c->fetch();
$t = $pdo->prepare('SELECT inicial FROM turnos WHERE id=?'); $t->execute([$turno_id]); $t = $t->fetch();
$ab = isset($c['abreviatura'])? trim($c['abreviatura']): '';
$nombreCarrera = $c['nombre'] ?? '';

function make_abrev($ab, $name){
	if($ab && strlen($ab) >= 2) return strtoupper($ab);
	// crear abreviatura por iniciales de palabras significativas
	$skip = ['en','de','del','la','el','y','licenciatura','ingeniería','licenciaturas','area','área','áreas'];
	$parts = preg_split('/\s+/', $name);
	$letters = [];
	foreach($parts as $p){
		$pp = mb_strtolower(preg_replace('/[^\p{L}]/u','',$p));
		if(!$pp) continue;
		if(in_array($pp, $skip)) continue;
		$letters[] = mb_strtoupper(mb_substr($pp,0,1));
	}
	if(count($letters) == 0) return strtoupper(substr($name,0,3));
	return implode('', array_slice($letters,0,3));
}

$abrev = make_abrev($ab, $nombreCarrera);
$ini = $t['inicial'] ?? 'X';
$codigo = sprintf('%s%d%03d-%s', $abrev, $grado, $next, $ini);
json_response(['next_seq'=>$next,'codigo'=>$codigo]);
