// /assets/js/medicos.js
// Buscar en tabla completa (pane medicos - buscar)
const buscadorPane = document.getElementById('buscadorPane');
const tbodyBuscar = document.querySelector('#tablaBuscar tbody');
if (buscadorPane && tbodyBuscar) {
  buscadorPane.addEventListener('input', () => {
    const q = (buscadorPane.value || '').toLowerCase();
    [...tbodyBuscar.rows].forEach(tr => {
      tr.style.display = tr.innerText.toLowerCase().includes(q) ? '' : 'none';
    });
  });
}

// ====== Validaciones MX ======
const reTel  = /^\d{10}$/;
const reCurp = /^([A-Z][AEIOU][A-Z]{2})(\d{2})(0[1-9]|1[0-2])(0[1-9]|[12]\d|3[01])[HM](AS|BC|BS|CC|CL|CM|CS|CH|DF|DG|GT|GR|HG|JC|MC|MN|MS|NT|NL|OC|PL|QT|QR|SP|SL|SR|TC|TS|TL|VZ|YN|ZS|NE)[B-DF-HJ-NP-TV-Z]{3}[0-9A-Z]\d$/i;
const reRfc  = /^([A-ZÑ&]{3,4})(\d{2})(0[1-9]|1[0-2])(0[1-9]|[12]\d|3[01])[A-Z0-9]{2}[0-9A-Z]?$/i;
const reMail = /^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/;

const upTrim = el => el.value = (el.value || '').toUpperCase().trim();
const digitsOnly = el => el.value = (el.value || '').replace(/\D+/g, '');

const valTel  = el => { digitsOnly(el); return el.value === '' || reTel.test(el.value); };
const valCurp = el => { upTrim(el);     return el.value === '' || reCurp.test(el.value); };
const valRfc  = el => { upTrim(el);     return el.value === '' || reRfc.test(el.value); };
const valMail = el => el.value === '' || (el.checkValidity() && reMail.test(el.value));

function setValidity(el, ok){
  if(ok){ el.classList.remove('is-invalid'); el.classList.add('is-valid'); }
  else{  el.classList.remove('is-valid'); el.classList.add('is-invalid'); }
}

// registrar
const rTel  = document.getElementById('rTel');
const rCurp = document.getElementById('rCurp');
const rRfc  = document.getElementById('rRfc');
const rMail = document.getElementById('rCorreo');

rTel && rTel.addEventListener('input',  e => setValidity(e.target, valTel(e.target)));
rCurp && rCurp.addEventListener('input', e => setValidity(e.target, valCurp(e.target)));
rRfc && rRfc.addEventListener('input',  e => setValidity(e.target, valRfc(e.target)));
rMail && rMail.addEventListener('input', e => setValidity(e.target, valMail(e.target)));

// modal editar
const modalEditar = document.getElementById('modalEditarMedico');
modalEditar && modalEditar.addEventListener('show.bs.modal', ev => {
  const b = ev.relatedTarget; if (!b) return;
  const d = b.dataset;
  const $ = id => document.getElementById(id);

  $('eId').value  = d.id || '';
  $('eNum').value = d.num || '';
  $('eNom').value = d.nom || '';
  $('eApe').value = d.ape || '';
  $('eEsp').value = d.esp || 'Anestesiología';
  $('eSer').value = d.ser || '';

  const selTurno = $('eTur'), selTcon = $('eTcon');
  if (selTurno) selTurno.value = d.idturno || '';
  if (selTcon)  selTcon.value  = d.idtcon  || '';

  $('eTel').value  = d.tel || '';
  $('eCor').value  = d.cor || '';
  $('eIng').value  = d.ing || '';
  $('eCm').value   = d.cm || '';
  $('eCa').value   = d.ca || '';
  $('eHe').value   = d.he || '';
  $('eHs').value   = d.hs || '';
  $('eDc').value   = d.dc || '';
  $('eCurp').value = d.curp || '';
  $('eRfc').value  = d.rfc || '';
});

modalEditar && modalEditar.addEventListener('shown.bs.modal', () => {
  const eTel  = document.getElementById('eTel');
  const eCurp = document.getElementById('eCurp');
  const eRfc  = document.getElementById('eRfc');
  const eMail = document.getElementById('eCor');
  eTel  && setValidity(eTel,  valTel(eTel));
  eCurp && setValidity(eCurp, valCurp(eCurp));
  eRfc  && setValidity(eRfc,  valRfc(eRfc));
  eMail && setValidity(eMail, valMail(eMail));
});

function validateForm(form){
  let ok = true;
  const tel  = form.querySelector('input[name="TELEFONO"]');
  const curp = form.querySelector('input[name="CURP"]');
  const rfc  = form.querySelector('input[name="RFC"]');
  const mail = form.querySelector('input[name="CORREO"]');

  if (tel){  const v = valTel(tel);  setValidity(tel, v);  ok = ok && v; }
  if (curp){ const v = valCurp(curp); setValidity(curp, v); ok = ok && v; }
  if (rfc){  const v = valRfc(rfc);  setValidity(rfc, v);  ok = ok && v; }
  if (mail){ const v = valMail(mail); setValidity(mail, v); ok = ok && v; }

  form.classList.add('was-validated');
  return ok;
}

document.getElementById('formRegistrarMedico')?.addEventListener('submit', e => { if(!validateForm(e.target)) e.preventDefault(); });
document.getElementById('formEditarMedico')?.addEventListener('submit', e => { if(!validateForm(e.target)) e.preventDefault(); });
