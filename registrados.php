<?php require 'inc/db.php'; ?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Alumnos Registrados</title>
  <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<div class="container">
  <div class="nav">
    <a href="grupos.php">Registro de Grupos</a>
    <a href="alumnos.php">Registro Alumnos</a>
    <a href="registrados.php" class="active">Alumnos Registrados</a>
    <a href="catalogos.php">Configuración de Catálogos</a>
  </div>

  <div class="card fade-in">
    <h2>Alumnos Registrados</h2>
    <div id="tabla"></div>
  </div>

  <script>
    async function cargar(){
      const r = await fetch('api/get_alumnos.php');
      const j = await r.json();
      const tabla = j.alumnos || [];
      const el = document.getElementById('tabla');
      el.innerHTML = '<table><tr><th>ID</th><th>Alumno</th><th>Grupo</th><th>Acciones</th></tr>' + tabla.map(a=>{
        const cls = a.activo==1? 'status-green':'status-red';
        return `<tr class="${cls}"><td>${a.id}</td><td>${a.nombre} ${a.ape_paterno} ${a.ape_materno||''}</td><td>${a.codigo}</td><td class="actions"><button title="Editar" onclick="irEditar(${a.id})">🔁</button><button title="Activar" onclick="toggle(${a.id},1)">✔</button><button title="Inactivar" onclick="toggle(${a.id},0)">✖</button></td></tr>`
      }).join('') + '</table>';
    }

    function irEditar(id){
      // redirigir a la página de registro con modo edición
      window.location.href = 'alumnos.php?edit_id=' + id;
    }

    async function toggle(id, val){
      await fetch('api/toggle_alumno.php',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({id, activo: val})});
      cargar();
    }

    cargar();
  </script>
</div>
</body>
</html>
