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
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Registro de Grupos</title>
  <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<div class="container">
  <div class="nav">
    <a href="grupos_nuevo.php" class="active">📋 Registro de Grupos</a>
    <a href="alumnos_nuevo.php">👤 Registro Alumnos</a>
    <a href="registrados_nuevo.php">📊 Alumnos Registrados</a>
    <a href="catalogos_nuevo.php">⚙️ Catálogos</a>
  </div>

  <div class="card fade-in">
    <h2>Registro de Grupos</h2>
    <p style="color:var(--text-light);margin-bottom:20px;font-size:14px">Crea grupos automáticos seleccionando carrera, turno y grado</p>
    
    <div class="form-row">
      <label>Carrera</label>
      <div class="select-wrap" style="flex:1">
        <select id="carrera">
          <?php foreach($carreras as $c): ?>
            <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['nombre']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
    </div>

    <div class="form-row">
      <label>Turno</label>
      <div class="select-wrap" style="flex:1">
        <select id="turno">
          <?php foreach($turnos as $t): ?>
            <option value="<?= $t['id'] ?>"><?= htmlspecialchars($t['nombre']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <label>Grado</label>
      <div class="select-wrap" style="flex:1">
        <select id="grado">
          <?php foreach($grados as $g): ?>
            <option value="<?= $g['grado'] ?>"><?= $g['grado'] ?></option>
          <?php endforeach; ?>
        </select>
      </div>
    </div>

    <div class="form-row">
      <label>Código Grupo</label>
      <input id="codigoPreview" readonly style="background:rgba(99,102,241,0.05);font-weight:600;color:var(--primary)" />
      <button id="registrar" class="btn primary">+ Registrar</button>
    </div>

    <div style="margin-top:28px;">
      <h3>Grupos Creados</h3>
      <div id="gruposTable"></div>
    </div>
  </div>

  <!-- Modal para editar grupo -->
  <div class="modal" id="modalEditarGrupo">
    <div class="modal-box">
      <h3 style="margin-bottom:16px">Editar Grupo</h3>
      <div class="form-row">
        <label>Carrera</label>
        <div class="select-wrap" style="flex:1">
          <select id="editCarrera">
            <?php foreach($carreras as $c): ?>
              <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['nombre']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
      </div>
      <div class="form-row">
        <label>Turno</label>
        <div class="select-wrap" style="flex:1">
          <select id="editTurno">
            <?php foreach($turnos as $t): ?>
              <option value="<?= $t['id'] ?>"><?= htmlspecialchars($t['nombre']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
      </div>
      <div class="form-row">
        <label>Grado</label>
        <div class="select-wrap" style="flex:1">
          <select id="editGrado">
            <?php foreach($grados as $g): ?>
              <option value="<?= $g['grado'] ?>"><?= $g['grado'] ?></option>
            <?php endforeach; ?>
          </select>
        </div>
      </div>
      <div class="form-row" style="justify-content:flex-end;gap:8px;margin-top:20px">
        <button onclick="cerrarModalGrupo()" class="btn secondary">Cancelar</button>
        <button onclick="guardarEditarGrupo()" class="btn primary">Guardar</button>
      </div>
    </div>
  </div>

  <style>
    .modal { display:none; }
    .modal.open { display:flex; position:fixed; inset:0; background:rgba(0,0,0,0.5); align-items:center; justify-content:center; z-index:100; }
    .modal-box { background:#fff; padding:24px; border-radius:12px; min-width:400px; box-shadow:0 8px 32px rgba(0,0,0,0.15); }
  </style>

  <script type="module">
    import { nextCodigoPreview, showToast } from './assets/js/app.js';

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
      el.innerHTML = '<table><thead><tr><th>ID</th><th>Código</th><th>Carrera</th><th>Turno</th><th>Grado</th></tr></thead><tbody>' + tabla.map(g=>`
        <tr onclick="abrirEditarGrupo(${g.id})" style="cursor:pointer">
          <td>#${g.id}</td>
          <td><strong>${g.codigo}</strong></td>
          <td>${g.carrera}</td>
          <td>${g.turno}</td>
          <td>${g.grado}</td>
        </tr>
      `).join('') + '</tbody></table>';
    }

    let grupoEditando = 0;

    async function abrirEditarGrupo(id){
      const r = await fetch('api/get_group.php?id='+id);
      const j = await r.json();
      if(j.grupo){
        grupoEditando = id;
        document.getElementById('editCarrera').value = j.grupo.carrera_id;
        document.getElementById('editTurno').value = j.grupo.turno_id;
        document.getElementById('editGrado').value = j.grupo.grado;
        document.getElementById('modalEditarGrupo').classList.add('open');
      }
    }

    function cerrarModalGrupo(){
      document.getElementById('modalEditarGrupo').classList.remove('open');
      grupoEditando = 0;
    }

    async function guardarEditarGrupo(){
      const payload = {
        id: grupoEditando,
        carrera_id: document.getElementById('editCarrera').value,
        turno_id: document.getElementById('editTurno').value,
        grado: document.getElementById('editGrado').value
      };
      const r = await fetch('api/update_group.php',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify(payload)});
      const j = await r.json();
      if(j.success){
        alert('✓ Grupo actualizado');
        cerrarModalGrupo();
        await cargarGrupos();
      } else {
        alert('Error al actualizar');
      }
    }

    registrar.addEventListener('click', async ()=>{
      const data = { carrera_id: carrera.value, turno_id: turno.value, grado: grado.value };
      const resp = await fetch('api/save_group.php',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify(data)});
      const json = await resp.json();
      if(json.success){ showToast('✓ Grupo registrado: '+json.codigo); await cargarGrupos(); await actualizarPreview(); }
      else showToast('✗ Error al registrar');
    });

    actualizarPreview();
    cargarGrupos();
  </script>
</div>
</body>
</html>
