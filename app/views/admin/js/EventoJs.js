 /*
 * Eventos: crear / editar
 * Nota: La imagen es OPCIONAL en crear y editar
 * Si se proporciona, se valida en el servidor (MIME, tamaño)
 * Si NO se proporciona, se guarda null en la BD
 */

// Crear nuevo evento
const btnGuardarEvento = document.getElementById('btn-guardar-evento');
if (btnGuardarEvento) btnGuardarEvento.addEventListener('click', (e) => {
    e.preventDefault();
    const modal = document.getElementById('modal-crear-evento');
    if (!modal) return;
    const nombre = modal.querySelector('#nombre') ? modal.querySelector('#nombre').value : '';
    const descripcion = modal.querySelector('#descripcion') ? modal.querySelector('#descripcion').value : '';
    const fecha = modal.querySelector('#fecha') ? modal.querySelector('#fecha').value : '';
    const horaInicio = modal.querySelector('#horaInicio') ? modal.querySelector('#horaInicio').value : '';
    const horaFin = modal.querySelector('#horaFin') ? modal.querySelector('#horaFin').value : '';
    const imagenEl = modal.querySelector('#imagen');

    let data = new FormData();
    data.append('nombre', nombre);
    data.append('descripcion', descripcion);
    data.append('fecha', fecha);
    data.append('hora_inicio', horaInicio);
    if (horaFin) data.append('hora_fin', horaFin);
    if (imagenEl && imagenEl.files && imagenEl.files[0]) {
        data.append('imagen', imagenEl.files[0]);
    }

    fetch('EventoController.php?action=guardar', { method: 'POST', headers: { 'X-Requested-With': 'XMLHttpRequest' }, body: data })
        .then(parseResponse)
        .then(resp => {
            if (resp && resp.status === 'ok') location.reload();
            else showToast('error','Error al crear evento: ' + (resp.message || JSON.stringify(resp)));
        })
        .catch(err => { console.error(err); showToast('error','Error de red al crear evento'); });
});

// Cancelar crear evento
const btnCancelarEvento = document.getElementById('btn-cancelar-evento');
if (btnCancelarEvento) btnCancelarEvento.addEventListener('click', (e) => {
    e.preventDefault();
    const modal = document.getElementById('modal-crear-evento');
    if (modal) {
        modal.classList.remove('active');
        modal.classList.add('d-none');
    }
    const form = document.getElementById('form-crear-evento');
    if (form) form.reset();
});

// Editar evento existente
const btnEditarEvento = document.getElementById('btn-editar-evento');
if (btnEditarEvento) btnEditarEvento.addEventListener('click', (e) => {
    e.preventDefault();
    const modal = document.getElementById('modal-editar-evento');
    if (!modal) return;
    const id = modal.querySelector('#id') ? modal.querySelector('#id').value : '';
    const nombre = modal.querySelector('#nombre') ? modal.querySelector('#nombre').value : '';
    const descripcion = modal.querySelector('#descripcion') ? modal.querySelector('#descripcion').value : '';
    const fecha = modal.querySelector('#fecha') ? modal.querySelector('#fecha').value : '';
    const horaInicio = modal.querySelector('#horaInicio') ? modal.querySelector('#horaInicio').value : '';
    const horaFin = modal.querySelector('#horaFin') ? modal.querySelector('#horaFin').value : '';
    const imagenEl = modal.querySelector('#imagen');

    let data = new FormData();
    data.append('id', id);
    data.append('nombre', nombre);
    data.append('descripcion', descripcion);
    data.append('fecha', fecha);
    data.append('hora_inicio', horaInicio);
    if (horaFin) data.append('hora_fin', horaFin);
    if (imagenEl && imagenEl.files && imagenEl.files[0]) {
        data.append('imagen', imagenEl.files[0]);
    }

    
    fetch('EventoController.php?action=actualizar', { method: 'POST', headers: { 'X-Requested-With': 'XMLHttpRequest' }, body: data })
        .then(parseResponse)
        .then(resp => {
            if (resp && resp.status === 'ok') location.reload();
            else showToast('error','Error al actualizar evento: ' + (resp.message || JSON.stringify(resp)));
        })
        .catch(err => { console.error(err); showToast('error','Error de red al actualizar evento'); });
});

// Cancelar editar evento
const btnCancelarEditEvento = document.getElementById('btn-cancelar-editar-evento');
if (btnCancelarEditEvento) btnCancelarEditEvento.addEventListener('click', (e) => {
    e.preventDefault();
    const modal = document.getElementById('modal-editar-evento');
    if (modal) {
        modal.classList.remove('active');
        modal.classList.add('d-none');
    }
});
