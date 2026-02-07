<?php
require 'inc/db.php';
$carreras = $pdo->query('SELECT * FROM carreras WHERE activo=1 ORDER BY nombre')->fetchAll();
$turnos = $pdo->query('SELECT * FROM turnos WHERE activo=1 ORDER BY nombre')->fetchAll();
$grados = $pdo->query('SELECT id, grado FROM grados WHERE activo=1 ORDER BY grado')->fetchAll();
$grupos = $pdo->query('SELECT g.id,g.codigo FROM grupos g WHERE g.activo=1 ORDER BY g.codigo')->fetchAll();
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Panel - Registro</title>
  <link rel="stylesheet" href="assets/css/style.css">
  <style>
    .board{display:flex;gap:18px}
    .panel{flex:1;min-width:260px}
    .panel .big-btn{padding:14px 18px;font-size:16px;border-radius:12px}
    .panel h3{margin:0 0 12px 0}
    .student-row{display:flex;gap:8px;margin-bottom:10px}
    .student-row input{flex:1}
    .code-box{font-weight:700;background:rgba(255,255,255,0.03);padding:8px;border-radius:8px}
  </style>
</head>
<body>
<div class="container">
  <div class="nav">
    <a href="panel.php" class="active">Tablero</a>
    <a href="grupos.php">Sólo Grupos</a>
    <a href="alumnos.php">Sólo Alumnos</a>
    <a href="registrados.php">Alumnos Registrados</a>
    <a href="catalogos.php">Catálogos</a>
  </div>

  <div class="board">
    <!-- Left: Registro de Alumnos (mini) -->
    <div class="panel card fade-in">
      <h3>Registro de Alumnos</h3>
      <div class="student-row">
        <input id="p_nombre" placeholder="Nombre" />
        <input id="p_ape_p" placeholder="Apellido P" />
      </div>
      <div class="student-row">
        <input id="p_ape_m" placeholder="Apellido M" />
      </div>
      <div class="form-row">
        <label>Grupo</label>
        <div class="select-wrap" style="flex:1"><select id="p_grupo"></select></div>
      </div>
      <div style="text-align:center;margin-top:12px">
        <button id="p_registrar" class="btn big-btn">● Registrar</button>
      </div>
    </div>

    <!-- Center: Registro de Grupos (principal) -->
    <div class="panel card fade-in">
      <h3>Registro de Grupos</h3>
      <div class="form-row">
        <label>Carrera</label>
        <div class="select-wrap" style="flex:1">
          <select id="m_carrera">
            <?php foreach($carreras as $c): ?>
              <option value="<?= $c['id'] ?>" data-abrev="<?= htmlspecialchars($c['abreviatura']) ?>"><?= htmlspecialchars($c['nombre']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
      </div>
      <div class="form-row">
        <label>Turno</label>
        <select id="m_turno">
          <?php foreach($turnos as $t): ?>
            <option value="<?= $t['id'] ?>" data-inicial="<?= $t['inicial'] ?>"><?= htmlspecialchars($t['nombre']) ?></option>
          <?php endforeach; ?>
        </select>
        <label>Grado</label>
        <select id="m_grado">
          <?php foreach($grados as $g): ?>
            <option value="<?= $g['grado'] ?>"><?= $g['grado'] ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="form-row">
        <label>Grupo</label>
        <div class="code-box" id="m_codigo">—</div>
      </div>
      <div style="text-align:center;margin-top:12px">
        <button id="m_registrar" class="btn big-btn">Registrar</button>
      </div>
      <div style="margin-top:14px">
        <small class="pill">Autoincremento por carrera/turno/grado</small>
      </div>
    </div>

    <!-- Right: Alumnos Registrados (tabla) -->
    <div class="panel card fade-in">
      <h3>Alumnos Registrados</h3>
      <div id="p_tabla"></div>
    </div>
  </div>
</div>

<script type="module">
  import { fillGroups, nextCodigoPreview, showToast } from './assets/js/app.js';
  // elementos
  const p_nombre = document.getElementById('p_nombre');
  const p_ape_p = document.getElementById('p_ape_p');
  const p_ape_m = document.getElementById('p_ape_m');
  const p_grupo = document.getElementById('p_grupo');
  const p_registrar = document.getElementById('p_registrar');

  const m_carrera = document.getElementById('m_carrera');
  const m_turno = document.getElementById('m_turno');
  const m_grado = document.getElementById('m_grado');
  const m_codigo = document.getElementById('m_codigo');
  const m_registrar = document.getElementById('m_registrar');

  async function cargarTabla(){
    const r = await fetch('api/get_alumnos.php');
    const j = await r.json();
    const tabla = j.alumnos || [];
    const el = document.getElementById('p_tabla');
    el.innerHTML = '<table><tr><th>ID</th><th>Alumno</th><th>Grupo</th><th>Acc</th></tr>' + tabla.map(a=>{
      const cls = a.activo==1? 'status-green':'status-red';
      return `<tr class="${cls}"><td>${a.id}</td><td>${a.nombre} ${a.ape_paterno} ${a.ape_materno||''}</td><td>${a.codigo}</td><td><button onclick="irEditar(${a.id})">✎</button></td></tr>`
    }).join('') + '</table>';
  }

  window.irEditar = function(id){ window.location.href = 'alumnos.php?edit_id='+id; }

  // llenar selects
  fillGroups(p_grupo);

  async function actualizarPreview(){
    const res = await nextCodigoPreview(m_carrera.value, m_turno.value, m_grado.value);
    if(res && res.codigo) m_codigo.textContent = res.codigo;
  }

  m_carrera.addEventListener('change', actualizarPreview);
  m_turno.addEventListener('change', actualizarPreview);
  m_grado.addEventListener('change', actualizarPreview);

  m_registrar.addEventListener('click', async ()=>{
    const data = { carrera_id: m_carrera.value, turno_id: m_turno.value, grado: m_grado.value };
    const resp = await fetch('api/save_group.php',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify(data)});
    const json = await resp.json();
    if(json.success){ showToast('Grupo registrado: '+json.codigo); await actualizarPreview(); await fillGroups(p_grupo); await cargarTabla(); }
    else showToast('Error al registrar');
  });

  p_registrar.addEventListener('click', async ()=>{
    const payload = { nombre: p_nombre.value.trim(), ape_paterno: p_ape_p.value.trim(), ape_materno: p_ape_m.value.trim(), grupo_id: p_grupo.value };
    const r = await fetch('api/save_alumno.php',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify(payload)});
    const j = await r.json();
    if(j.success){ showToast('Alumno registrado'); p_nombre.value='';p_ape_p.value='';p_ape_m.value=''; await cargarTabla(); }
    else showToast('Error');
  });

  // inicializar
  actualizarPreview();
  cargarTabla();
</script>
</body>
</html>
