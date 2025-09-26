// /assets/js/salas.js
// Rellenar modal editar sala
const modalSala = document.getElementById('modalSala');
modalSala && modalSala.addEventListener('show.bs.modal', ev => {
  const b = ev.relatedTarget; if (!b) return;
  const d = b.dataset;
  document.getElementById('saId').value          = d.id || '';
  document.getElementById('saIdServicio').value  = d.idservicio || '';
  document.getElementById('saNombre').value      = d.nombre || '';
  document.getElementById('saEquipamiento').value= d.equipamiento || '';
  document.getElementById('saActiva').checked    = (d.activa == '1');
});
