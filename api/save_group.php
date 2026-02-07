<?php
require __DIR__ . '/../inc/db.php';
$data = json_decode(file_get_contents('php://input'), true);
if(!$data) json_response(['error'=>'No data']);
$carrera_id = intval($data['carrera_id'] ?? 0);
$turno_id = intval($data['turno_id'] ?? 0);
$grado = intval($data['grado'] ?? 0);
if(!$carrera_id || !$turno_id || !$grado) json_response(['error'=>'Faltan parámetros']);

$pdo->beginTransaction();
try{
  $stmt = $pdo->prepare('SELECT MAX(seq) AS m FROM grupos WHERE carrera_id=? AND turno_id=? AND grado=? FOR UPDATE');
  $stmt->execute([$carrera_id,$turno_id,$grado]);
  $row = $stmt->fetch();
  $next = ($row && $row['m'])? intval($row['m'])+1 : 1;

  $c = $pdo->prepare('SELECT nombre, abreviatura FROM carreras WHERE id=?'); $c->execute([$carrera_id]); $c = $c->fetch();
  $t = $pdo->prepare('SELECT inicial FROM turnos WHERE id=?'); $t->execute([$turno_id]); $t = $t->fetch();
  $ab = isset($c['abreviatura'])? trim($c['abreviatura']): '';
  $nombreCarrera = $c['nombre'] ?? '';

  function make_abrev($ab, $name){
      if($ab && strlen($ab) >= 2) return strtoupper($ab);
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

    $ins = $pdo->prepare('INSERT INTO grupos (carrera_id, turno_id, grado, seq, codigo) VALUES (?,?,?,?,?)');
    $ins->execute([$carrera_id,$turno_id,$grado,$next,$codigo]);

    // aplicar estado por defecto según configuración (si la columna existe)
    $lastId = $pdo->lastInsertId();
    $defaultActive = 1;
    $settingsFile = __DIR__ . '/../data/settings.json';
    if(file_exists($settingsFile)){
      $s = json_decode(file_get_contents($settingsFile), true);
      if(isset($s['groups_default_active'])) $defaultActive = intval($s['groups_default_active']);
    }

    $colCheck = $pdo->prepare("SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='grupos' AND COLUMN_NAME='activo'");
    $colCheck->execute();
    $hasActivo = $colCheck->fetchColumn() > 0;
    if($hasActivo){
      $u = $pdo->prepare('UPDATE grupos SET activo=? WHERE id=?');
      $u->execute([$defaultActive, $lastId]);
    }

    $pdo->commit();
    json_response(['success'=>true,'codigo'=>$codigo,'activo'=>$defaultActive]);
}catch(Exception $e){ $pdo->rollBack(); json_response(['error'=>$e->getMessage()]); }
