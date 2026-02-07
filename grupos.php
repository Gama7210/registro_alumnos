<?php
require 'inc/db.php';
$carreras = $pdo->query('SELECT * FROM carreras WHERE activo=1 ORDER BY nombre')->fetchAll();
$turnos = $pdo->query('SELECT * FROM turnos WHERE activo=1 ORDER BY nombre')->fetchAll();
$grados = $pdo->query('SELECT id, grado FROM grados WHERE activo=1 ORDER BY grado')->fetchAll();
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Registro de Grupos</title>
  <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<div class="container">
  <div class="nav">
    <a href="grupos.php" class="active">Registro de Grupos</a>
    <a href="alumnos.php">Registro Alumnos</a>
    <a href="registrados.php">Alumnos Registrados</a>
    <a href="catalogos.php">Configuración de Catálogos</a>
  </div>

  <div class="card fade-in">
    <h2>Registro de Grupos</h2>
    <div class="form-row">
      <label>Carrera</label>
      <div class="select-wrap" style="flex:1">
      <select id="carrera" >
        <?php foreach($carreras as $c): ?>
          <option value="<?= $c['id'] ?>" data-abrev="<?= htmlspecialchars($c['abreviatura']) ?>"><?= htmlspecialchars($c['nombre']) ?></option>
        <?php endforeach; ?>
      </select>
      </div>
      <button id="addCarrera" class="btn secondary">Registrar Carrera</button>
    </div>

    <div class="form-row">
      <label style="width:120px">Turno</label>
      <select id="turno">
        <?php foreach($turnos as $t): ?>
          <option value="<?= $t['id'] ?>" data-inicial="<?= $t['inicial'] ?>"><?= htmlspecialchars($t['nombre']) ?></option>
        <?php endforeach; ?>
      </select>
      <label style="width:120px">Grado</label>
      <select id="grado">
        <?php foreach($grados as $g): ?>
          <option value="<?= $g['grado'] ?>"><?= $g['grado'] ?></option>
        <?php endforeach; ?>
      </select>
    </div>

    <div class="form-row">
      <label style="width:120px">Grupo (auto)</label>
      <input id="codigoPreview" readonly />
      <button id="registrar" class="btn">Registrar</button>
    </div>

    <div class="list card" id="listaGrupos">
      <h3>Grupos existentes</h3>
      <div id="gruposTable"></div>
    </div>
  </div>
  <script type="module">
    import { nextCodigoPreview, fillGroups, showToast } from './assets/js/app.js';

    const carrera = document.getElementById('carrera');
    const turno = document.getElementById('turno');
    const grado = document.getElementById('grado');
    const codigoPreview = document.getElementById('codigoPreview');
    const registrar = document.getElementById('registrar');

    async function actualizarPreview(){
      const res = await nextCodigoPreview(carrera.value, turno.value, grado.value);
      if(res && res.codigo){ codigoPreview.value = res.codigo; }
    }

    carrera.addEventListener('change', actualizarPreview);
    turno.addEventListener('change', actualizarPreview);
    grado.addEventListener('change', actualizarPreview);

    async function cargarGrupos(){
      const r = await fetch('api/get_groups.php');
      const j = await r.json();
      const tabla = j.grupos || [];
      const el = document.getElementById('gruposTable');
      el.innerHTML = '<table><tr><th>ID</th><th>Código</th><th>Carrera</th><th>Turno</th><th>Grado</th><th>Acciones</th></tr>' + tabla.map(g=>`<tr><td>${g.id}</td><td>${g.codigo}</td><td>${g.carrera}</td><td>${g.turno}</td><td>${g.grado}</td><td><button onclick="editarGrupo(${g.id})">✎</button> <button onclick="toggleGrupo(${g.id},${g.activo})">${g.activo==1? 'Inactivar':'Activar'}</button></td></tr>`).join('') + '</table>';
    }

    registrar.addEventListener('click', async ()=>{
      const data = { carrera_id: carrera.value, turno_id: turno.value, grado: grado.value };
      const resp = await fetch('api/save_group.php',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify(data)});
      const json = await resp.json();
      if(json.success){ showToast('Grupo registrado: '+json.codigo); await cargarGrupos(); await actualizarPreview(); }
      else showToast('Error al registrar');
    });

    window.editarGrupo = async function(id){
      // abrir edición en la misma página (simple): redirect con param
      window.location.href = 'grupos.php?edit_id='+id;
    }

    window.toggleGrupo = async function(id, cur){
      await fetch('api/update_group.php',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({id:id, activo: cur?0:1})});
      await cargarGrupos();
    }

    // inicializar
    actualizarPreview();
    cargarGrupos();

    // Si viene ?edit_id en URL cargar para editar
    const params = new URLSearchParams(window.location.search);
    if(params.get('edit_id')){
      const r = await fetch('api/get_group.php?id='+params.get('edit_id'));
      const j = await r.json();
      if(j.grupo){
        // intentar seleccionar la carrera/turno y grado
        carrera.value = j.grupo.carrera_id;
        turno.value = j.grupo.turno_id;
        grado.value = j.grupo.grado;
        actualizarPreview();
        registrar.textContent = 'Actualizar';
      }
    }
  </script>
</div>
</body>
</html>
