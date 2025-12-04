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
if (btnCancelarEliminar) {
    btnCancelarEliminar.addEventListener('click', () => cerrarEliminar());
}
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
        console.log('Eliminando:', { id, controller, action });
        console.log('URL:', `../../../../app/controllers/${controller}Controller.php?action=${action}`);
        fetch(`../../../../app/controllers/${controller}Controller.php?action=${action}`, {
            method: 'POST',
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            body: data
        })
        .then(response => {
            console.log('Response status:', response.status);
            return response.text().then(text => {
                console.log('Response text:', text);
                try {
                    return JSON.parse(text);
                } catch (e) {
                    console.error('Error parsing JSON:', e);
                    console.error('Raw response:', text);
                    throw new Error('Respuesta no válida del servidor');
                }
            });
        })
        .then(resp => {
            cerrarEliminar();
            if (resp && resp.status === 'ok') {
                let mensaje = 'Elemento eliminado correctamente';
                if (controller === 'Evento') mensaje = '✓ Evento eliminado correctamente';
                else if (controller === 'Producto') mensaje = '✓ Producto eliminado correctamente';
                else if (controller === 'Promocion') mensaje = '✓ Promoción eliminada correctamente';
                else if (controller === 'Reserva' || controller === 'Reservas') mensaje = '✓ Reserva eliminada correctamente';
                showToast('success', mensaje);
                if (controller === 'Producto' && typeof cargarProductos === 'function') {
                    cargarProductos();
                } else if (controller === 'Promocion' && typeof cargarPromociones === 'function') {
                    cargarPromociones();
                } else if (controller === 'Evento' && typeof cargarEventos === 'function') {
                    cargarEventos();
                } else if ((controller === 'Reserva' || controller === 'Reservas') && typeof renderMesas === 'function') {
                    const hoy = new Date().toISOString().slice(0,10);
                    fetch(`/app/controllers/ReservaController.php?action=listar&fecha=${hoy}`, { 
                        headers: { 'X-Requested-With': 'XMLHttpRequest' } 
                    })
                    .then(response => response.json())
                    .then(data => {
                        window.reservasHoy = data || [];
                        renderMesas();
                    })
                    .catch(err => console.error('Error recargando mesas:', err));
                } else {
                    setTimeout(() => location.reload(), 1000);
                }
            } else {
                let errorMsg = 'Error al eliminar';
                if (resp.message) errorMsg = resp.message;
                else if (resp.errors && Array.isArray(resp.errors)) {
                    if (resp.errors.includes('id_required')) errorMsg = 'ID no proporcionado';
                    else if (resp.errors.includes('not_found')) errorMsg = 'Elemento no encontrado';
                    else if (resp.errors.includes('delete_failed')) errorMsg = 'No se pudo eliminar el elemento';
                }
                showToast('error', '✗ ' + errorMsg);
            }
        })
        .catch(err => {
            cerrarEliminar();
            console.error(err);
            showToast('error', '✗ ' + (err.message || 'Ocurrió un error al eliminar'));
        });
    });
}