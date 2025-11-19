/**
 * ReservaAdminJS.js
 * 
 * Maneja las acciones de confirmar y cancelar reservas.
 * Incluye validaciones y actualización automática del estado de las mesas.
 */

document.addEventListener('DOMContentLoaded', function() {
    
    // Manejar botones de confirmar reserva
    const botonesConfirmar = document.querySelectorAll('.btn-confirmar-reserva');
    botonesConfirmar.forEach(boton => {
        boton.addEventListener('click', async function() {
            const idReserva = this.getAttribute('data-id');
            
            if (!idReserva) {
                showToast('error', 'ID de reserva no válido');
                return;
            }

            // Preguntar confirmación
            if (!confirm('¿Estás seguro de confirmar esta reserva?')) {
                return;
            }

            try {
                const formData = new FormData();
                formData.append('id', idReserva);

                const response = await fetch('../../app/controllers/ReservaController.php?action=confirmar', {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: formData
                });

                if (!response.ok) {
                    const text = await response.text();
                    console.error('Error response:', text);
                    showToast('error', 'Error del servidor al confirmar la reserva');
                    return;
                }

                const result = await response.json();

                if (result.status === 'ok') {
                    showToast('success', 'Reserva confirmada correctamente');
                    
                    // Recargar la página para actualizar el estado
                    setTimeout(() => {
                        location.reload();
                    }, 1500);
                } else if (result.message === 'reservation_not_pending') {
                    showToast('error', 'La reserva ya fue procesada anteriormente');
                } else if (result.message === 'mesa_occupied') {
                    showToast('error', 'La mesa seleccionada ya está ocupada');
                } else if (result.message === 'mesa_not_active') {
                    showToast('error', 'La mesa no está activa');
                } else {
                    showToast('error', result.detail || result.message || 'Error al confirmar la reserva');
                }
            } catch (error) {
                console.error('Error:', error);
                showToast('error', 'Error al procesar la solicitud');
            }
        });
    });

    // Manejar botones de cancelar reserva
    const botonesCancelar = document.querySelectorAll('.btn-cancelar-reserva');
    botonesCancelar.forEach(boton => {
        boton.addEventListener('click', async function() {
            const idReserva = this.getAttribute('data-id');
            
            if (!idReserva) {
                showToast('error', 'ID de reserva no válido');
                return;
            }

            // Preguntar confirmación
            if (!confirm('¿Estás seguro de cancelar esta reserva? Esta acción no se puede deshacer.')) {
                return;
            }

            try {
                const formData = new FormData();
                formData.append('id', idReserva);

                const response = await fetch('../../app/controllers/ReservaController.php?action=cancelar', {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: formData
                });

                if (!response.ok) {
                    const text = await response.text();
                    console.error('Error response:', text);
                    showToast('error', 'Error del servidor al cancelar la reserva');
                    return;
                }

                const result = await response.json();

                if (result.status === 'ok') {
                    showToast('success', 'Reserva cancelada correctamente. La mesa ha sido liberada.');
                    
                    // Recargar la página para actualizar el estado
                    setTimeout(() => {
                        location.reload();
                    }, 1500);
                } else if (result.message === 'already_cancelled') {
                    showToast('error', 'La reserva ya fue cancelada anteriormente');
                } else if (result.message === 'reservation_not_found') {
                    showToast('error', 'La reserva no existe');
                } else {
                    showToast('error', result.detail || result.message || 'Error al cancelar la reserva');
                }
            } catch (error) {
                console.error('Error:', error);
                showToast('error', 'Error al procesar la solicitud');
            }
        });
    });
});
