<?php
require __DIR__ . '/../inc/db.php';
$stmt = $pdo->query('SELECT a.*, g.codigo FROM alumnos a JOIN grupos g ON g.id=a.grupo_id ORDER BY a.id DESC');
$al = $stmt->fetchAll();
json_response(['alumnos'=>$al]);
