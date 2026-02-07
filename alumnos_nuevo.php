<?php require 'inc/db.php'; ?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Registro Alumnos</title>
  <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<div class="container">
  <div class="nav">
    <a href="grupos_nuevo.php">📋 Registro de Grupos</a>
    <a href="alumnos_nuevo.php" class="active">👤 Registro Alumnos</a>
    <a href="registrados_nuevo.php">📊 Alumnos Registrados</a>
    <a href="catalogos_nuevo.php">⚙️ Catálogos</a>
  </div>

  <div class="card fade-in">
    <h2 id="tituloForm">Registro de Alumnos</h2>
    <p style="color:var(--text-light);margin-bottom:20px;font-size:14px">Registra nuevos alumnos con su información completa</p>
    
    <div class="form-row">
      <label>Nombre</label>
      <input id="nombre" placeholder="Ej: Juan" />
    </div>

    <div class="form-row">
      <label>Apellido Paterno</label>
      <input id="ape_paterno" placeholder="Ej: García" />
    </div>

    <div class="form-row">
      <label>Apellido Materno</label>
      <input id="ape_materno" placeholder="Ej: López" />
    </div>

    <div class="form-row">
      <label>Seleccionar Grupo</label>
      <div class="select-wrap" style="flex:1">
        <select id="grupo_select"></select>
      </div>
    </div>

    <div class="form-row" style="justify-content:center;gap:12px;margin-top:24px">
      <button id="registrar" class="btn primary">+ Registrar</button>
      <button id="cancelEdit" class="btn secondary hidden">Cancelar</button>
    </div>
  </div>

  <script type="module">
    import { fillGroups, showToast } from './assets/js/app.js';
    const grupoSelect = document.getElementById('grupo_select');
    const nombre = document.getElementById('nombre');
    const ape_paterno = document.getElementById('ape_paterno');
    const ape_materno = document.getElementById('ape_materno');
    const registrar = document.getElementById('registrar');
    const tituloForm = document.getElementById('tituloForm');
    const cancelEdit = document.getElementById('cancelEdit');
    let editingId = 0;

    async function cargarGrupos(){
      await fillGroups(grupoSelect);
    }

    async function loadForEdit(id){
      const r = await fetch('api/get_alumno.php?id='+id);
      const j = await r.json();
      if(j.alumno){
        nombre.value = j.alumno.nombre;
        ape_paterno.value = j.alumno.ape_paterno;
        ape_materno.value = j.alumno.ape_materno || '';
        grupoSelect.value = j.alumno.grupo_id;
        editingId = id;
        registrar.textContent = '✏️ Actualizar';
        tituloForm.textContent = 'Actualizar Alumno';
        cancelEdit.classList.remove('hidden');
      }
    }

    registrar.addEventListener('click', async ()=>{
      const payload = {
        id: editingId || undefined,
        nombre: nombre.value.trim(),
        ape_paterno: ape_paterno.value.trim(),
        ape_materno: ape_materno.value.trim(),
        grupo_id: grupoSelect.value
      };
      const r = await fetch('api/save_alumno.php',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify(payload)});
      const j = await r.json();
      if(j.success){ 
        showToast(editingId? '✓ Alumno actualizado':'✓ Alumno registrado'); 
        nombre.value='';ape_paterno.value='';ape_materno.value=''; 
        editingId=0; registrar.textContent='+ Registrar'; 
        tituloForm.textContent='Registro de Alumnos'; 
        cancelEdit.classList.add('hidden'); 
      }
      else showToast('✗ Error');
    });

    cancelEdit.addEventListener('click', ()=>{
      editingId=0; nombre.value='';ape_paterno.value='';ape_materno.value=''; 
      registrar.textContent='+ Registrar'; 
      tituloForm.textContent='Registro de Alumnos'; 
      cancelEdit.classList.add('hidden');
    });

    const params = new URLSearchParams(window.location.search);
    if(params.get('edit_id')){
      await cargarGrupos();
      await loadForEdit(params.get('edit_id'));
    } else {
      cargarGrupos();
    }
  </script>
</div>
</body>
</html>
