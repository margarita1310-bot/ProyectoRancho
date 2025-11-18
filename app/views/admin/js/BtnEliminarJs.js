// Modal eliminar reutilizable
const deleteOverlay = document.getElementById('delete-overlay');
const eliminarTitle = document.getElementById('eliminar-title');
const eliminarMessage = document.getElementById('eliminar-message');
const eliminarIdInput = document.getElementById('eliminar-id');
const eliminarControllerInput = document.getElementById('eliminar-controller');
const eliminarActionInput = document.getElementById('eliminar-action');
const btnConfirmarEliminar = document.getElementById('btn-confirmar');
const btnCancelarEliminar = document.getElementById('btn-cancelar-eliminar');

function abrirEliminar(id, controller, action = 'eliminar', opts = {}){
    if (eliminarIdInput) eliminarIdInput.value = id || '';
    if (eliminarControllerInput) eliminarControllerInput.value = controller || '';
    if (eliminarActionInput) eliminarActionInput.value = action || 'eliminar';
    if (eliminarTitle) eliminarTitle.textContent = opts.title || 'Eliminar elemento';
    if (eliminarMessage) eliminarMessage.textContent = opts.message || '¿Estás seguro de eliminar este elemento? Esta acción no se puede deshacer.';
    try {
        const box = deleteOverlay ? deleteOverlay.querySelector('.eliminar-box') : null;
        if (box) {
            box.classList.remove('danger','warn');
            if (action === 'declinar') {
                box.classList.add('danger');
                if (btnConfirmarEliminar) { btnConfirmarEliminar.textContent = 'Declinar'; btnConfirmarEliminar.className = 'btn btn-sm btn-warning'; }
            } else if (action === 'eliminar') {
                box.classList.add('danger');
                if (btnConfirmarEliminar) { btnConfirmarEliminar.textContent = 'Eliminar'; btnConfirmarEliminar.className = 'btn btn-sm btn-danger'; }
            } else {
                if (btnConfirmarEliminar) { btnConfirmarEliminar.textContent = 'Confirmar'; btnConfirmarEliminar.className = 'btn btn-sm btn-primary'; }
            }
        }
    } catch (e) { console.warn('No se pudo aplicar estilo al modal delete:', e); }
    if (deleteOverlay) {
        deleteOverlay.classList.remove('d-none');
        deleteOverlay.classList.add('active');
    }
}

function cerrarEliminar(){
    if (deleteOverlay) {
        deleteOverlay.classList.remove('active');
        deleteOverlay.classList.add('d-none');
    }
    if (eliminarIdInput) eliminarIdInput.value = '';
    if (eliminarControllerInput) eliminarControllerInput.value = '';
    if (eliminarActionInput) eliminarActionInput.value = 'eliminar';
    try {
        const box = deleteOverlay ? deleteOverlay.querySelector('.eliminar-box') : null;
        if (box) box.classList.remove('danger','warn');
        if (btnConfirmarEliminar) { btnConfirmarEliminar.textContent = 'Confirmar'; btnConfirmarEliminar.className = 'btn btn-sm'; }
    } catch (e) {}
}

// Abrir modal al pulsar cualquier botón con clase .btn-delete
document.querySelectorAll('.btn-eliminar').forEach(btn => {
    btn.addEventListener('click', () => {
        const id = btn.dataset.id || btn.getAttribute('data-id') || '';
        const controller = btn.dataset.controller || btn.getAttribute('data-controller') || 'Promocion';
        const title = btn.dataset.title || null;
        const message = btn.dataset.message || null;
        abrirEliminar(id, controller, 'eliminar', { title, message });
    });
});

// Cancelar
if (btnCancelarEliminar) {
    btnCancelarEliminar.addEventListener('click', () => cerrarEliminar());
}

// Confirmar (usa parseResponse y showToast)
if (btnConfirmarEliminar) {
    btnConfirmarEliminar.addEventListener('click', (e) => {
        e.preventDefault();
        const id = eliminarIdInput ? eliminarIdInput.value : '';
        const controller = eliminarControllerInput ? eliminarControllerInput.value || 'Promocion' : 'Promocion';
        const action = eliminarActionInput && eliminarActionInput.value ? eliminarActionInput.value : 'eliminar';

        if (!id) {
            showToast('error','ID no proporcionado. No se puede eliminar.');
            cerrarEliminar();
            return;
        }

        let data = new FormData();
        data.append('id', id);

        fetch(`${controller}Controller.php?action=${action}`, {
            method: 'POST',
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            body: data
        })
        .then(parseResponse)
        .then(resp => {
            cerrarEliminar();
            if (resp && resp.status === 'ok') location.reload();
            else showToast('error','Error al eliminar: ' + (resp.message || JSON.stringify(resp)));
        })
        .catch(err => {
            console.error(err);
            showToast('error','Ocurrió un error al eliminar.');
            cerrarEliminar();
        });
    });
}
