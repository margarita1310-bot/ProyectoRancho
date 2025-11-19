/**
 * ReservaAdminJS.js
 * 
 * Maneja las acciones de confirmar y cancelar reservas.
 * Incluye validaciones y actualización automática del estado de las mesas.
 */

// Confirmar reserva
document.addEventListener('click', function(e) {
    if (e.target.classList.contains('btn-confirmar-reserva')) {
        const idReserva = e.target.getAttribute('data-id');
        
        if (!idReserva) {
            showToast('Error', 'ID de reserva no válido', 'danger');
            return;
        }

        if (!confirm('¿Estás seguro de confirmar esta reserva?')) return;

        const formData = new FormData();
        formData.append('id', idReserva);

        fetch('../../app/controllers/ReservaController.php?action=confirmar', {
            method: 'POST',
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            body: formData
        })
        .then(async response => {
            if (!response.ok) {
                const text = await response.text();
                console.error('Error response:', text);
                throw new Error('Error del servidor');
            }
            return response.json();
        })
        .then(result => {
            if (result.status === 'ok') {
                showToast('Éxito', 'Reserva confirmada correctamente', 'success');
                setTimeout(() => location.reload(), 1500);
            } else if (result.message === 'reservation_not_pending') {
                showToast('Error', 'La reserva ya fue procesada anteriormente', 'warning');
            } else if (result.message === 'mesa_occupied') {
                showToast('Error', 'La mesa seleccionada ya está ocupada', 'warning');
            } else if (result.message === 'mesa_not_active') {
                showToast('Error', 'La mesa no está activa', 'warning');
            } else {
                showToast('Error', result.detail || result.message || 'Error al confirmar la reserva', 'danger');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('Error', 'Error al procesar la solicitud', 'danger');
        });
    }
});

// Cancelar reserva
document.addEventListener('click', function(e) {
    if (e.target.classList.contains('btn-cancelar-reserva')) {
        const idReserva = e.target.getAttribute('data-id');
        
        if (!idReserva) {
            showToast('Error', 'ID de reserva no válido', 'danger');
            return;
        }

        if (!confirm('¿Estás seguro de cancelar esta reserva? Esta acción no se puede deshacer.')) return;

        const formData = new FormData();
        formData.append('id', idReserva);

        fetch('../../app/controllers/ReservaController.php?action=cancelar', {
            method: 'POST',
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            body: formData
        })
        .then(async response => {
            if (!response.ok) {
                const text = await response.text();
                console.error('Error response:', text);
                throw new Error('Error del servidor');
            }
            return response.json();
        })
        .then(result => {
            if (result.status === 'ok') {
                showToast('Éxito', 'Reserva cancelada correctamente. La mesa ha sido liberada.', 'success');
                setTimeout(() => location.reload(), 1500);
            } else if (result.message === 'already_cancelled') {
                showToast('Error', 'La reserva ya fue cancelada anteriormente', 'warning');
            } else if (result.message === 'reservation_not_found') {
                showToast('Error', 'La reserva no existe', 'warning');
            } else {
                showToast('Error', result.detail || result.message || 'Error al cancelar la reserva', 'danger');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('Error', 'Error al procesar la solicitud', 'danger');
        });
    }
});

