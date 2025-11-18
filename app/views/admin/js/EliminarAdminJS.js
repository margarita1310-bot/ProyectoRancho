const deleteOverlay = document.getElementById('delete-overlay');
const eliminarIdInput = document.getElementById('eliminar-id');
const eliminarControllerInput = document.getElementById('eliminar-controller');
const eliminarActionInput = document.getElementById('eliminar-action');
const eliminarTitle = document.getElementById('eliminar-title');
const eliminarMessage = document.getElementById('eliminar-message');
const btnConfirmarEliminar = document.getElementById('btn-confirmar-eliminar');
const btnCancelarEliminar = document.getElementById('btn-cancelar-eliminar');

function abrirEliminar(id, controller, action = 'eliminar', opts = {}) {
    if (eliminarIdInput) eliminarIdInput.value = id || '';
    if (eliminarControllerInput) eliminarControllerInput.value = controller || '';
    if (eliminarActionInput) eliminarActionInput.value = action || 'eliminar';
    if (eliminarTitle) eliminarTitle.textContent = opts.title || 'Eliminar elemento';
    if (eliminarMessage) eliminarMessage.textContent = opts.message || '¿Estás seguro de eliminar este elemento?';

    if (deleteOverlay) {
        deleteOverlay.classList.remove('d-none');
        deleteOverlay.classList.add('active');
    }
}

function cerrarEliminar() {
    if (deleteOverlay) {
        deleteOverlay.classList.remove('active');
        deleteOverlay.classList.add('d-none');
    }
    if (eliminarIdInput) eliminarIdInput.value = '';
    if (eliminarControllerInput) eliminarControllerInput.value = '';
    if (eliminarActionInput) eliminarActionInput.value = 'eliminar';
}

// Abrir modal al pulsar cualquier botón con clase .btn-eliminar
document.querySelectorAll('.btn-eliminar').forEach(btn => {
    btn.addEventListener('click', () => {
        const id = btn.dataset.id;
        const controller = btn.dataset.controller || 'Promocion';
        const action = btn.dataset.action || 'eliminar';
        const title = btn.dataset.title || null;
        const message = btn.dataset.message || null;
        abrirEliminar(id, controller, action, { title, message });
    });
});

// Cancelar
if (btnCancelarEliminar) {
    btnCancelarEliminar.addEventListener('click', () => cerrarEliminar());
}

// Confirmar
if (btnConfirmarEliminar) {
    btnConfirmarEliminar.addEventListener('click', e => {
        e.preventDefault();
        const id = eliminarIdInput.value;
        const controller = eliminarControllerInput.value || 'Promocion';
        const action = eliminarActionInput.value || 'eliminar';

        if (!id) {
            showToast('error', 'ID no proporcionado. No se puede eliminar.');
            cerrarEliminar();
            return;
        }

        const data = new FormData();
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
            else showToast('error', 'Error al eliminar: ' + (resp.message || JSON.stringify(resp)));
        })
        .catch(err => {
            console.error(err);
            showToast('error', 'Ocurrió un error al eliminar.');
            cerrarEliminar();
        });
    });
}
