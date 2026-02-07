<?php
header('Content-Type: application/json');
$input = json_decode(file_get_contents('php://input'), true);
if(!$input) { echo json_encode(['error'=>'no input']); exit; }
$path = __DIR__ . '/../data';
if(!is_dir($path)) mkdir($path, 0755, true);
$file = $path . '/settings.json';
$current = [];
if(file_exists($file)) $current = json_decode(file_get_contents($file), true) ?: [];
$updated = array_merge($current, $input);
file_put_contents($file, json_encode($updated, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
echo json_encode(['success'=>true,'settings'=>$updated]);
