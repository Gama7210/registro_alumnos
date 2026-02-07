<?php require 'inc/db.php'; ?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Configuración de Catálogos</title>
  <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<div class="container">
  <div class="nav">
    <a href="grupos.php">Registro de Grupos</a>
    <a href="alumnos.php">Registro Alumnos</a>
    <a href="registrados.php">Alumnos Registrados</a>
    <a href="catalogos.php" class="active">Configuración de Catálogos</a>
  </div>

  <div class="card fade-in">
    <h2>Configuración de Catálogos</h2>
    <h3>Carreras</h3>
    <div id="carreras"></div>
    <h3>Turnos</h3>
    <div id="turnos"></div>
  </div>

  <script>
    async function cargar(){
      const r = await fetch('api/get_catalogs.php');
      const j = await r.json();
      document.getElementById('carreras').innerHTML = '<table>' + j.carreras.map(c=>`<tr><td>${c.nombre}</td><td>${c.abreviatura}</td><td><button onclick="toggleCarrera(${c.id},${c.activo})">${c.activo==1? 'Desactivar':'Activar'}</button></td></tr>`).join('') + '</table>';
      document.getElementById('turnos').innerHTML = '<table>' + j.turnos.map(t=>`<tr><td>${t.nombre}</td><td>${t.inicial}</td><td>${t.activo==1?'Activo':'Inactivo'}</td></tr>`).join('') + '</table>';
    }
    async function toggleCarrera(id, cur){
      await fetch('api/toggle_carrera.php',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({id, activo: cur?0:1})});
      cargar();
    }
    cargar();
  </script>

</div>
</body>
</html>
