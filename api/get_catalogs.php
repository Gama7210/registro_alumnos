<?php
require __DIR__ . '/../inc/db.php';
$c = $pdo->query('SELECT id,nombre,abreviatura,activo FROM carreras ORDER BY nombre')->fetchAll();
$t = $pdo->query('SELECT id,nombre,inicial,activo FROM turnos ORDER BY id')->fetchAll();
json_response(['carreras'=>$c,'turnos'=>$t]);
