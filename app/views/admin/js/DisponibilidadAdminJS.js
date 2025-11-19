/**
 * DisponibilidadAdminJS.js
 * 
 * Maneja la creación y gestión de disponibilidad de mesas.
 * Incluye validaciones para evitar modificar cuando existen reservas activas.
 */

document.addEventListener('DOMContentLoaded', function() {
    const formCrearDisponibilidad = document.getElementById('form-crear-disponibilidad');
    const modalCrearDisponibilidad = document.getElementById('modal-crear-disponibilidad');
    const alertaReservas = document.getElementById('alerta-reservas');

    // Manejar envío del formulario
    if (formCrearDisponibilidad) {
        formCrearDisponibilidad.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const fecha = document.getElementById('disponibilidad-fecha').value;
            const cantidad = document.getElementById('disponibilidad-cantidad').value;

            // Validaciones básicas
            if (!fecha || !cantidad) {
                showToast('error', 'Por favor completa todos los campos');
                return;
            }

            if (cantidad < 1 || cantidad > 50) {
                showToast('error', 'La cantidad de mesas debe estar entre 1 y 50');
                return;
            }

            try {
                const formData = new FormData();
                formData.append('fecha', fecha);
                formData.append('cantidad', cantidad);

                const response = await fetch('../../app/controllers/DisponibilidadController.php?action=guardar', {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: formData
                });

                if (!response.ok) {
                    const text = await response.text();
                    console.error('Error response:', text);
                    showToast('error', 'Error del servidor al guardar la disponibilidad');
                    return;
                }

                const result = await response.json();

                if (result.status === 'ok') {
                    showToast('success', 'Disponibilidad guardada correctamente');
                    modalCrearDisponibilidad.classList.remove('active');
                    formCrearDisponibilidad.reset();
                    
                    // Recargar la página para mostrar las mesas actualizadas
                    setTimeout(() => {
                        location.reload();
                    }, 1500);
                } else if (result.message === 'has_reservations') {
                    showToast('error', 'No se puede modificar la disponibilidad porque existen reservas activas para esta fecha');
                    if (alertaReservas) {
                        alertaReservas.classList.remove('d-none');
                    }
                } else {
                    showToast('error', result.detail || result.message || 'Error al guardar la disponibilidad');
                }
            } catch (error) {
                console.error('Error:', error);
                showToast('error', 'Error al procesar la solicitud');
            }
        });
    }

    // Limpiar alertas al abrir el modal
    const btnCrearDisponibilidad = document.getElementById('btn-crear-disponibilidad');
    if (btnCrearDisponibilidad) {
        btnCrearDisponibilidad.addEventListener('click', function() {
            if (alertaReservas) {
                alertaReservas.classList.add('d-none');
            }
            if (formCrearDisponibilidad) {
                formCrearDisponibilidad.reset();
            }
        });
    }
});
