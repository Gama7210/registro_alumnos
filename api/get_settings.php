<?php
header('Content-Type: application/json');
$path = __DIR__ . '/../data/settings.json';
if(!file_exists($path)){
  echo json_encode(['groups_default_active'=>1]);
  exit;
}
$s = json_decode(file_get_contents($path), true);
if(!$s) $s = ['groups_default_active'=>1];
echo json_encode($s);
