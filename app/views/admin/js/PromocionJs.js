 /*
 * Promociones: crear / editar
 * Nota: La imagen es OPCIONAL en crear y editar
 * Si se proporciona, se valida en el servidor (MIME, tamaño)
 * Si NO se proporciona, se guarda null en la BD
 */

// Crear nueva promoción
const btnGuardarPromocion = document.getElementById('btn-guardar-promocion');
if (btnGuardarPromocion) btnGuardarPromocion.addEventListener('click', (e) => {
    e.preventDefault();
    const modal = document.getElementById('modal-crear-promocion');
    if (!modal) return;
    const nombre = modal.querySelector('#nombre') ? modal.querySelector('#nombre').value : '';
    const descripcion = modal.querySelector('#descripcion') ? modal.querySelector('#descripcion').value : '';
    const fechaInicio = modal.querySelector('#fechaInicio') ? modal.querySelector('#fechaInicio').value : '';
    const fechaFin = modal.querySelector('#fechaFin') ? modal.querySelector('#fechaFin').value : '';
    const estado = modal.querySelector('#estado') ? modal.querySelector('#estado').value : '';
    const imagenEl = modal.querySelector('#imagen');

    let data = new FormData();
    data.append('nombre', nombre);
    data.append('descripcion', descripcion);
    data.append('fecha_inicio', fechaInicio);
    data.append('fecha_fin', fechaFin);
    data.append('estado', estado);
    if (imagenEl && imagenEl.files && imagenEl.files[0]) {
        data.append('imagen', imagenEl.files[0]);
    }

    fetch('PromocionController.php?action=guardar', { method: 'POST', headers: { 'X-Requested-With': 'XMLHttpRequest' }, body: data })
        .then(parseResponse)
        .then(resp => {
            if (resp && resp.status === 'ok') location.reload();
            else showToast('error','Error al crear promoción: ' + (resp.message || JSON.stringify(resp)));
        })
        .catch(err => { console.error(err); showToast('error','Error de red al crear promoción'); });
});

// Cancelar crear promoción
const btnCancelarPromocion = document.getElementById('btn-cancelar-promocion');
if (btnCancelarPromocion) btnCancelarPromocion.addEventListener('click', (e) => {
    e.preventDefault();
    const modal = document.getElementById('modal-crear-promocion');
    if (modal) {
        modal.classList.remove('active');
        modal.classList.add('d-none');
    }
    const form = document.getElementById('form-crear-promocion');
    if (form) form.reset();
});

// Editar promoción existente
const btnEditarPromocion = document.getElementById('btn-editar-promocion');
if (btnEditarPromocion) btnEditarPromocion.addEventListener('click', (e) => {
    e.preventDefault();
    const modal = document.getElementById('modal-editar-promocion');
    if (!modal) return;
    const id = modal.querySelector('#id') ? modal.querySelector('#id').value : '';
    const nombre = modal.querySelector('#nombre') ? modal.querySelector('#nombre').value : '';
    const descripcion = modal.querySelector('#descripcion') ? modal.querySelector('#descripcion').value : '';
    const fechaInicio = modal.querySelector('#fechaInicio') ? modal.querySelector('#fechaInicio').value : '';
    const fechaFin = modal.querySelector('#fechaFin') ? modal.querySelector('#fechaFin').value : '';
    const estado = modal.querySelector('#estado') ? modal.querySelector('#estado').value : '';
    const imagenEl = modal.querySelector('#imagen');

    let data = new FormData();
    data.append('id', id);
    data.append('nombre', nombre);
    data.append('descripcion', descripcion);
    data.append('fecha_inicio', fechaInicio);
    data.append('fecha_fin', fechaFin);
    data.append('estado', estado);
    if (imagenEl && imagenEl.files && imagenEl.files[0]) {
        data.append('imagen', imagenEl.files[0]);
    }

    fetch('PromocionController.php?action=actualizar', { method: 'POST', headers: { 'X-Requested-With': 'XMLHttpRequest' }, body: data })
        .then(parseResponse)
        .then(resp => {
            if (resp && resp.status === 'ok') location.reload();
            else showToast('error','Error al actualizar promoción: ' + (resp.message || JSON.stringify(resp)));
        })
        .catch(err => { console.error(err); showToast('error','Error de red al actualizar promoción'); });
});

// Cancelar editar promoción
const btnCancelarEditarPromocion = document.getElementById('btn-cancelar-editar-promocion');
if (btnCancelarEditarPromocion) btnCancelarEditarPromocion.addEventListener('click', (e) => {
    e.preventDefault();
    const modal = document.getElementById('modal-editar-promocion');
    if (modal) {
        modal.classList.remove('active');
        modal.classList.add('d-none');
    }
});
