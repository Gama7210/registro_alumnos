<?php require 'inc/db.php'; ?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Alumnos Registrados</title>
  <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<div class="container">
  <div class="nav">
    <a href="grupos_nuevo.php">📋 Registro de Grupos</a>
    <a href="alumnos_nuevo.php">👤 Registro Alumnos</a>
    <a href="registrados_nuevo.php" class="active">📊 Alumnos Registrados</a>
    <a href="catalogos_nuevo.php">⚙️ Catálogos</a>
  </div>

  <div class="card fade-in">
    <h2>Alumnos Registrados</h2>
    <p style="color:var(--text-light);margin-bottom:20px;font-size:14px">Gestiona todos los alumnos registrados en el sistema</p>
    <div id="tabla"></div>
  </div>

  <!-- Modal para editar alumno -->
  <div class="modal" id="modalEditar">
    <div class="modal-box">
      <h3 style="margin-bottom:16px">Editar Alumno</h3>
      <div class="form-row">
        <label>Nombre</label>
        <input id="editNombre" />
      </div>
      <div class="form-row">
        <label>Apellido Paterno</label>
        <input id="editApePaterno" />
      </div>
      <div class="form-row">
        <label>Apellido Materno</label>
        <input id="editApeMaterno" />
      </div>
      <div class="form-row">
        <label>Grupo</label>
        <div class="select-wrap" style="flex:1">
          <select id="editGrupo"></select>
        </div>
      </div>
      <div class="form-row" style="justify-content:flex-end;gap:8px;margin-top:20px">
        <button onclick="cerrarModal()" class="btn secondary">Cancelar</button>
        <button onclick="guardarEdicion()" class="btn primary">Guardar</button>
      </div>
    </div>
  </div>

  <style>
    .modal { display:none; }
    .modal.open { display:flex; position:fixed; inset:0; background:rgba(0,0,0,0.5); align-items:center; justify-content:center; z-index:100; }
    .modal-box { background:#fff; padding:24px; border-radius:12px; min-width:400px; box-shadow:0 8px 32px rgba(0,0,0,0.15); }
  </style>

  <script>
    let alumnoEditando = 0;

    async function cargar(){
      const r = await fetch('api/get_alumnos.php');
      const j = await r.json();
      const tabla = j.alumnos || [];
      const el = document.getElementById('tabla');
      el.innerHTML = '<table><thead><tr><th>ID</th><th>Alumno</th><th>Grupo</th><th>Acciones</th></tr></thead><tbody>' + tabla.map(a=>{
        const cls = a.activo==1? 'status-green':'status-red';
        const puedeEditar = a.activo==1;
        return `<tr class="${cls}">
          <td>#${a.id}</td>
          <td><strong>${a.nombre} ${a.ape_paterno} ${a.ape_materno || ''}</strong></td>
          <td><span class="pill primary">${a.codigo}</span></td>
          <td class="actions">
            <button onclick="abrirEditar(${a.id})" class="btn icon" title="${puedeEditar? 'Editar':'No se puede editar (INACTIVO)'}" ${puedeEditar? '':'disabled'} style="${!puedeEditar? 'opacity:0.4;cursor:not-allowed':''}">✏️</button>
            <button onclick="toggle(${a.id},1)" class="btn icon success" title="Activar">✔</button>
            <button onclick="toggle(${a.id},0)" class="btn icon danger" title="Inactivar">✖</button>
          </td>
        </tr>`
      }).join('') + '</tbody></table>';
    }

    async function abrirEditar(id){
      const r = await fetch('api/get_alumno.php?id='+id);
      const j = await r.json();
      if(j.alumno && j.alumno.activo==1){
        alumnoEditando = id;
        document.getElementById('editNombre').value = j.alumno.nombre;
        document.getElementById('editApePaterno').value = j.alumno.ape_paterno;
        document.getElementById('editApeMaterno').value = j.alumno.ape_materno || '';
        
        const rg = await fetch('api/get_groups.php');
        const jg = await rg.json();
        const select = document.getElementById('editGrupo');
        select.innerHTML = jg.grupos.map(g=>`<option value="${g.id}" ${g.id==j.alumno.grupo_id? 'selected':''}>${g.codigo}</option>`).join('');
        
        document.getElementById('modalEditar').classList.add('open');
      } else {
        alert('No se puede editar un alumno inactivo');
      }
    }

    function cerrarModal(){
      document.getElementById('modalEditar').classList.remove('open');
      alumnoEditando = 0;
    }

    async function guardarEdicion(){
      const payload = {
        id: alumnoEditando,
        nombre: document.getElementById('editNombre').value.trim(),
        ape_paterno: document.getElementById('editApePaterno').value.trim(),
        ape_materno: document.getElementById('editApeMaterno').value.trim(),
        grupo_id: document.getElementById('editGrupo').value
      };
      if(!payload.nombre || !payload.ape_paterno || !payload.grupo_id) {
        alert('Por favor completa todos los campos');
        return;
      }
      const r = await fetch('api/save_alumno.php',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify(payload)});
      const j = await r.json();
      if(j.success){ 
        alert('✓ Alumno actualizado');
        cerrarModal();
        await cargar();
      } else {
        alert('Error al actualizar');
      }
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
