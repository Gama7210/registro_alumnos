<?php require 'inc/db.php'; ?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Configuración de Catálogos</title>
  <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<div class="container">
  <div class="nav">
    <a href="grupos_nuevo.php">📋 Registro de Grupos</a>
    <a href="alumnos_nuevo.php">👤 Registro Alumnos</a>
    <a href="registrados_nuevo.php">📊 Alumnos Registrados</a>
    <a href="catalogos_nuevo.php" class="active">⚙️ Catálogos</a>
  </div>

  <div class="card fade-in">
    <h2>Configuración de Catálogos</h2>
    <p style="color:var(--text-light);margin-bottom:20px;font-size:14px">Activa o desactiva carreras, turnos y grados del sistema</p>

    <h3>Registrar Nueva Carrera</h3>
    <div class="form-row">
      <input id="nuevaCarrera" placeholder="Nombre de carrera (ej: Ingeniería Civil)" style="flex:1" />
      <input id="nuevaAbrev" placeholder="Abreviatura (ej: IC)" style="width:120px" />
      <button id="agregarCarrera" class="btn primary">+ Agregar</button>
    </div>

    <h3 style="margin-top:24px">Carreras Disponibles</h3>
    <div id="carreras"></div>

    <h3 style="margin-top:32px">Turnos Disponibles</h3>
    <div id="turnos"></div>

    <h3 style="margin-top:32px">Grados Disponibles</h3>
    <div id="grados"></div>

    <h3 style="margin-top:32px">Configuración de Grupos</h3>
    <div class="form-row">
      <label>Inactivar nuevos grupos</label>
      <div style="display:flex;align-items:center;gap:12px;">
        <label style="display:flex;align-items:center;gap:8px"><input id="opt_inactivar_nuevos" type="checkbox" /> Inactivar por defecto</label>
        <button id="saveSettings" class="btn secondary">Guardar</button>
      </div>
    </div>

    <h3 style="margin-top:18px">Grupos Registrados</h3>
    <div id="grupos_list"></div>
  </div>

  <script>
    async function cargar(){
      const r = await fetch('api/get_catalogs.php');
      const j = await r.json();
      
      document.getElementById('carreras').innerHTML = '<table><thead><tr><th>Carrera</th><th>Abreviatura</th><th>Estado</th><th>Acción</th></tr></thead><tbody>' + 
        j.carreras.map(c=>`
          <tr ${c.activo==1? 'class="status-green"':'class="status-red"'}>
            <td>${c.nombre}</td>
            <td><code style="background:rgba(99,102,241,0.1);padding:4px 8px;border-radius:4px">${c.abreviatura}</code></td>
            <td>${c.activo==1? '<span class="pill primary">Activo</span>':'<span class="pill">Inactivo</span>'}</td>
            <td><button onclick="toggleCarrera(${c.id},${c.activo})" class="btn icon ${c.activo==1? 'danger':'success'}">${c.activo==1? '✖':'✔'}</button></td>
          </tr>
        `).join('') + '</tbody></table>';
      
      document.getElementById('turnos').innerHTML = '<table><thead><tr><th>Turno</th><th>Inicial</th><th>Estado</th><th>Acción</th></tr></thead><tbody>' + 
        j.turnos.map(t=>`
          <tr ${t.activo==1? 'class="status-green"':'class="status-red"'}>
            <td><strong>${t.nombre}</strong></td>
            <td><code style="background:rgba(99,102,241,0.1);padding:4px 8px;border-radius:4px">${t.inicial}</code></td>
            <td>${t.activo==1? '<span class="pill primary">Activo</span>':'<span class="pill">Inactivo</span>'}</td>
            <td><button onclick="toggleTurno(${t.id},${t.activo})" class="btn icon ${t.activo==1? 'danger':'success'}">${t.activo==1? '✖':'✔'}</button></td>
          </tr>
        `).join('') + '</tbody></table>';

      const rg = await fetch('api/get_grados.php?all=1');
      const jg = await rg.json();
      document.getElementById('grados').innerHTML = '<table><thead><tr><th>Grado</th><th>Estado</th><th>Acción</th></tr></thead><tbody>' + 
        jg.grados.map(g=>`
          <tr ${g.activo==1? 'class="status-green"':'class="status-red"'}>
            <td><strong>${g.grado}</strong></td>
            <td>${g.activo==1? '<span class="pill primary">Activo</span>':'<span class="pill">Inactivo</span>'}</td>
            <td><button onclick="toggleGrado(${g.id},${g.activo})" class="btn icon ${g.activo==1? 'danger':'success'}">${g.activo==1? '✖':'✔'}</button></td>
          </tr>
        `).join('') + '</tbody></table>';
    }

    async function toggleCarrera(id, cur){
      await fetch('api/toggle_carrera.php',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({id, activo: cur?0:1})});
      cargar();
    }

    async function toggleTurno(id, cur){
      await fetch('api/toggle_turno.php',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({id, activo: cur?0:1})});
      cargar();
    }

    async function toggleGrado(id, cur){
      await fetch('api/toggle_grado.php',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({id, activo: cur?0:1})});
      cargar();
    }

    async function cargarSettings(){
      const r = await fetch('api/get_settings.php');
      const s = await r.json();
      document.getElementById('opt_inactivar_nuevos').checked = s.groups_default_active == 0 ? true : false;
    }

    document.getElementById('saveSettings').addEventListener('click', async ()=>{
      const inact = document.getElementById('opt_inactivar_nuevos').checked ? 0 : 1;
      await fetch('api/update_settings.php',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({groups_default_active: inact})});
      alert('Configuración guardada');
    });

    async function cargarGrupos(){
      const r = await fetch('api/get_groups.php?all=1');
      const j = await r.json();
      document.getElementById('grupos_list').innerHTML = '<table><thead><tr><th>Código</th><th>Carrera</th><th>Grado</th><th>Turno</th><th>Estado</th><th>Acción</th></tr></thead><tbody>' +
        j.grupos.map(g=>`
          <tr ${g.activo==1? 'class="status-green"':'class="status-red"'}>
            <td><strong>${g.codigo}</strong></td>
            <td>${g.carrera}</td>
            <td>${g.grado}</td>
            <td>${g.turno}</td>
            <td>${g.activo==1? '<span class="pill primary">Activo</span>':'<span class="pill">Inactivo</span>'}</td>
            <td><button onclick="toggleGrupo(${g.id},${g.activo})" class="btn icon ${g.activo==1? 'danger':'success'}">${g.activo==1? '✖':'✔'}</button></td>
          </tr>
        `).join('') + '</tbody></table>';
    }

    async function toggleGrupo(id, cur){
      await fetch('api/toggle_grupo.php',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({id, activo: cur?0:1})});
      cargar();
      cargarGrupos();
    }

    document.getElementById('agregarCarrera').addEventListener('click', async ()=>{
      const nombre = document.getElementById('nuevaCarrera').value.trim();
      const abrev = document.getElementById('nuevaAbrev').value.trim().toUpperCase();
      if(!nombre || !abrev){ alert('Por favor completa todos los campos'); return; }
      const r = await fetch('api/add_carrera.php',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({nombre, abreviatura: abrev})});
      const j = await r.json();
      if(j.success){ alert('✓ Carrera agregada'); document.getElementById('nuevaCarrera').value=''; document.getElementById('nuevaAbrev').value=''; await cargar(); }
      else alert('Error');
    });

    cargar();
    cargarSettings();
    cargarGrupos();
  </script>
</div>
</body>
</html>
