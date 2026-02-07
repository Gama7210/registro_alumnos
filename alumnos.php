<?php
require 'inc/db.php';
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Registro Alumnos</title>
  <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<div class="container">
  <div class="nav">
    <a href="grupos.php">Registro de Grupos</a>
    <a href="alumnos.php" class="active">Registro Alumnos</a>
    <a href="registrados.php">Alumnos Registrados</a>
    <a href="catalogos.php">Configuración de Catálogos</a>
  </div>

  <div class="card fade-in">
    <h2 id="tituloForm">Registro de Alumnos</h2>
    <div class="form-row">
      <input id="nombre" placeholder="Nombre" />
      <input id="ape_paterno" placeholder="Apellido Paterno" />
      <input id="ape_materno" placeholder="Apellido Materno" />
    </div>

    <div class="form-row">
      <label style="width:120px">Seleccionar Grupo</label>
      <div class="select-wrap" style="flex:1"><select id="grupo_select"></select></div>
      <button id="registrar" class="btn">Registrar</button>
      <button id="cancelEdit" class="btn secondary hidden">Cancelar</button>
    </div>
  </div>

  <script type="module">
    import { fillGroups, showToast, qs } from './assets/js/app.js';
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
        registrar.textContent = 'Actualizar';
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
      if(j.success){ showToast(editingId? 'Alumno actualizado':'Alumno registrado'); nombre.value='';ape_paterno.value='';ape_materno.value=''; editingId=0; registrar.textContent='Registrar'; tituloForm.textContent='Registro de Alumnos'; cancelEdit.classList.add('hidden'); }
      else showToast('Error');
    });

    cancelEdit.addEventListener('click', ()=>{
      editingId=0; nombre.value='';ape_paterno.value='';ape_materno.value=''; registrar.textContent='Registrar'; tituloForm.textContent='Registro de Alumnos'; cancelEdit.classList.add('hidden');
    });

    // Si viene ?edit_id= en la URL
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
