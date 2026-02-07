async function getJSON(url, opts){
  const r = await fetch(url, opts);
  try{ return await r.json(); }catch(e){ return null; }
}

function qs(sel){return document.querySelector(sel)}
function qsa(sel){return document.querySelectorAll(sel)}

async function fillCarreras(selectEl){
  const res = await getJSON('api/get_catalogs.php');
  if(res && res.carreras){
    selectEl.innerHTML = res.carreras.map(c=>`<option value="${c.id}">${c.nombre}</option>`).join('');
  }
}

async function fillGroups(selectEl){
  const res = await getJSON('api/get_groups.php');
  if(res && res.grupos){
    selectEl.innerHTML = res.grupos.map(g=>`<option value="${g.id}">${g.codigo}</option>`).join('');
  }
}

async function nextCodigoPreview(carrera_id, turno_id, grado){
  const res = await getJSON(`api/next_group_seq.php?carrera_id=${carrera_id}&turno_id=${turno_id}&grado=${grado}`);
  return res;
}

function showToast(msg){
  let t = document.getElementById('toast');
  if(!t){ t = document.createElement('div'); t.id='toast'; t.className='toast'; document.body.appendChild(t); }
  t.textContent = msg; t.classList.add('show');
  setTimeout(()=>t.classList.remove('show'), 2800);
}

export { getJSON, fillCarreras, fillGroups, nextCodigoPreview, showToast, qs };
