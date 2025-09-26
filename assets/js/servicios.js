// /assets/js/servicios.js
// Rellenar modal editar servicio
const modalServicio = document.getElementById('modalServicio');
modalServicio && modalServicio.addEventListener('show.bs.modal', ev => {
  const b = ev.relatedTarget; if (!b) return;
  const d = b.dataset;
  document.getElementById('svId').value        = d.id || '';
  document.getElementById('svCodigo').value    = d.codigo || '';
  document.getElementById('svNombre').value    = d.nombre || '';
  document.getElementById('svIdSalas').value   = d.idsalas || 0;     // NUEVO
  document.getElementById('svCapacidad').value = d.capacidad || 0;
});

