/**
 * DisponibilidadAdminJS.js
 * 
 * Gestión de disponibilidad de mesas por fecha.
 * Permite crear y actualizar la cantidad de mesas disponibles.
 */

// Guardar disponibilidad de mesas
const btnGuardarMesas = document.getElementById('btn-guardar-mesas');
if (btnGuardarMesas) btnGuardarMesas.addEventListener('click', async (e) => {
    e.preventDefault();
    
    const modal = document.getElementById('modal-create-mesas');
    if (!modal) return;
    
    const fecha = modal.querySelector('#mesas-fecha') ? modal.querySelector('#mesas-fecha').value : '';
    const cantidad = modal.querySelector('#mesas-cantidad') ? modal.querySelector('#mesas-cantidad').value : '';
    
    if (!fecha || !cantidad) {
        showToast('Error', 'Todos los campos son obligatorios', 'danger');
        return;
    }
    
    const formData = new FormData();
    formData.append('fecha', fecha);
    formData.append('cantidad', cantidad);
    
    try {
        const response = await fetch('../../app/controllers/DisponibilidadController.php?action=guardar', {
            method: 'POST',
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            body: formData
        });
        
        if (!response.ok) {
            const text = await response.text();
            console.error('Error response:', text);
            showToast('Error', 'Error del servidor', 'danger');
            return;
        }
        
        const data = await response.json();
        
        if (data.status === 'ok') {
            showToast('Éxito', 'Disponibilidad guardada correctamente', 'success');
            modal.classList.remove('active');
            modal.classList.add('d-none');
            const form = document.getElementById('form-create-mesas');
            if (form) form.reset();
            location.reload();
        } else if (data.status === 'error' && data.message === 'has_reservations') {
            showToast('Error', data.detail || 'No se puede modificar, existen reservas activas', 'warning');
        } else {
            showToast('Error', 'Error al guardar disponibilidad', 'danger');
        }
    } catch (error) {
        console.error('Error:', error);
        showToast('Error', 'Error de red al guardar disponibilidad', 'danger');
    }
});

// Cancelar modal de disponibilidad
const btnCancelarMesas = document.getElementById('btn-cancelar-mesas');
if (btnCancelarMesas) btnCancelarMesas.addEventListener('click', (e) => {
    e.preventDefault();
    const modal = document.getElementById('modal-create-mesas');
    if (modal) {
        modal.classList.remove('active');
        modal.classList.add('d-none');
    }
    const form = document.getElementById('form-create-mesas');
    if (form) form.reset();
});
