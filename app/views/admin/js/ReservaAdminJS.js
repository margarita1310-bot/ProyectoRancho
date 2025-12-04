/**
 * ReservaAdminJS
 * Script para gestionar y visualizar reservas por fecha
 * Incluye carga por fecha, confirmación y cancelación de reservas
 */

/**
 * Evento: Cuando el DOM está completamente cargado
 * Inicializa filtro de fecha y carga reservas
 */
document.addEventListener('DOMContentLoaded', () => {
    // Obtener input de filtro por fecha
    const filtroFecha = document.getElementById('filtro-fecha-reserva');

    if (filtroFecha) {
        // Establecer fecha de hoy por defecto
        const hoy = new Date().toISOString().split('T')[0];
        filtroFecha.value = hoy;

        // Cargar reservas al cambiar la fecha
        filtroFecha.addEventListener('change', cargarReservasPorFecha);

        // Cargar reservas iniciales
        cargarReservasPorFecha();
    }
});

/**
 * Carga y renderiza las reservas para la fecha seleccionada
 * Maneja errores y muestra mensajes de estado
 *
 * @async
 * @return {void}
 */
async function cargarReservasPorFecha() {
    // Obtener referencias a elementos DOM
    const filtroFecha = document.getElementById('filtro-fecha-reserva');
    const tbody = document.querySelector('#tabla-reservas tbody');
    const alerta = document.getElementById('alerta-sin-disponibilidad');

    if (!filtroFecha || !tbody) {
        return;
    }

    // Obtener fecha seleccionada
    const fecha = filtroFecha.value;

    // Validar que se seleccionó una fecha
    if (!fecha) {
        tbody.innerHTML = '<tr><td colspan="7" class="text-center text-muted">Selecciona una fecha para ver las reservas</td></tr>';
        alerta.classList.add('d-none');
        return;
    }

    try {
        // Mostrar loading
        tbody.innerHTML = '<tr><td colspan="7" class="text-center"><div class="spinner-border spinner-border-sm me-2"></div>Cargando reservas...</td></tr>';
        alerta.classList.add('d-none');

        // Realizar solicitud AJAX
        const response = await fetch(`../../../../app/controllers/ReservaController.php?action=obtenerReservasPorFecha&fecha=${fecha}`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        });

        // Registrar estado de respuesta
        console.log('Response status:', response.status);

        // Leer como texto primero para debugging
        const responseText = await response.text();
        console.log('Response text:', responseText);

        // Validar respuesta HTTP
        if (!response.ok) {
            throw new Error(`Error del servidor: ${response.status}`);
        }

        // Parsear JSON
        let resultado;
        try {
            resultado = JSON.parse(responseText);
        } catch (e) {
            console.error('Error parseando JSON:', e);
            throw new Error('Respuesta inválida del servidor');
        }

        // Si no hay disponibilidad configurada
        if (!resultado.disponibilidad) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="7" class="text-center p-4 text-warning">
                        No hay disponibilidad configurada para esta fecha
                    </td>
                </tr>
            `;
            alerta.classList.remove('d-none');
            return;
        }

        // Si no hay mesas activas
        if (resultado.mesas.length === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="7" class="text-center p-4 text-muted">
                        No hay mesas activas para esta fecha
                    </td>
                </tr>
            `;
            alerta.classList.add('d-none');
            return;
        }

        // Limpiar tabla y ocultar alerta
        tbody.innerHTML = '';
        alerta.classList.add('d-none');

        // Renderizar fila por cada mesa
        resultado.mesas.forEach(fila => {
            const tr = document.createElement('tr');

            // Determinar badge de estado de la reserva
            let estadoBadge = '<span class="text-muted">-</span>';
            if (fila.estado === 'pendiente') {
                estadoBadge = '<span class="badge badge-pendiente">Pendiente</span>';
            } else if (fila.estado === 'confirmada') {
                estadoBadge = '<span class="badge badge-confirmada">Confirmada</span>';
            } else if (fila.estado === 'cancelada') {
                estadoBadge = '<span class="badge bg-danger">Cancelada</span>';
            }

            // Determinar botones de acción según estado
            let acciones = '<span class="text-muted">-</span>';
            if (fila.tiene_reserva && fila.estado) {
                if (fila.estado === 'pendiente') {
                    acciones = `
                        <button class="btn btn-confirmar-reserva btn-sm btn-confirmar-custom" 
                                data-id="${fila.id_reserva}" 
                                title="Confirmar reserva">
                            <i class="bi bi-check-circle"></i>
                        </button>
                        <button class="btn btn-cancelar-reserva btn-sm btn-cancelar-custom" 
                                data-id="${fila.id_reserva}" 
                                title="Cancelar reserva">
                            <i class="bi bi-x-circle"></i>
                        </button>
                    `;
                } else if (fila.estado === 'confirmada') {
                    acciones = `
                        <button class="btn btn-cancelar-reserva btn-sm btn-cancelar-custom" 
                                data-id="${fila.id_reserva}" 
                                title="Cancelar reserva">
                            <i class="bi bi-x-circle"></i>
                        </button>
                    `;
                }
            }

            // Construir HTML de la fila
            tr.innerHTML = `
                <td><strong>Mesa ${fila.numero_mesa}</strong></td>
                <td>${fila.folio}</td>
                <td>${fila.cliente_nombre}</td>
                <td>${fila.hora}</td>
                <td>${fila.num_personas}</td>
                <td>${estadoBadge}</td>
                <td class="text-center">
                    <div class="d-flex gap-1 justify-content-center flex-wrap">
                        ${acciones}
                    </div>
                </td>
            `;

            // Agregar fila a tabla
            tbody.appendChild(tr);
        });
    } catch (error) {
        // Registrar error
        console.error('Error al cargar reservas:', error);

        // Mostrar mensaje de error
        tbody.innerHTML = `
            <tr>
                <td colspan="7" class="text-center p-4 text-danger">
                    Error al cargar las reservas. Intenta de nuevo.
                </td>
            </tr>
        `;
    }
}

/**
 * Evento: Click en botón confirmar reserva
 * Confirma una reserva pendiente
 */
document.addEventListener('click', function (e) {
    // Obtener botón si existe
    const btn = e.target.closest('.btn-confirmar-reserva');
    if (!btn) {
        return;
    }

    // Obtener ID de reserva
    const idReserva = btn.getAttribute('data-id');

    // Validar ID
    if (!idReserva) {
        showToast('error', 'ID de reserva no válido');
        return;
    }

    // Pedir confirmación
    if (!confirm('¿Estás seguro de confirmar esta reserva?')) {
        return;
    }

    // Crear FormData
    const formData = new FormData();
    formData.append('id', idReserva);

    // Realizar petición POST
    fetch('/app/controllers/ReservaController.php?action=confirmar', {
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
                showToast('success', 'Reserva confirmada correctamente');
                cargarReservasPorFecha();
            } else if (result.message === 'reservation_not_pending') {
                showToast('error', 'La reserva ya fue procesada anteriormente');
            } else if (result.message === 'mesa_occupied') {
                showToast('error', 'La mesa seleccionada ya está ocupada');
            } else if (result.message === 'mesa_not_active') {
                showToast('error', 'La mesa no está activa');
            } else {
                showToast('error', result.detail || result.message || 'Error al confirmar la reserva');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('error', 'Error al procesar la solicitud');
        });
});

/**
 * Evento: Click en botón cancelar reserva
 * Cancela una reserva pendiente o confirmada
 */
document.addEventListener('click', function (e) {
    // Obtener botón si existe
    const btn = e.target.closest('.btn-cancelar-reserva');
    if (!btn) {
        return;
    }

    // Obtener ID de reserva
    const idReserva = btn.getAttribute('data-id');

    // Validar ID
    if (!idReserva) {
        showToast('error', 'ID de reserva no válido');
        return;
    }

    // Pedir confirmación
    if (!confirm('¿Estás seguro de cancelar esta reserva? Esta acción no se puede deshacer.')) {
        return;
    }

    // Crear FormData
    const formData = new FormData();
    formData.append('id', idReserva);

    // Realizar petición POST
    fetch('/app/controllers/ReservaController.php?action=cancelar', {
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
                showToast('success', 'Reserva cancelada correctamente');
                cargarReservasPorFecha();
            } else if (result.message === 'already_cancelled') {
                showToast('error', 'La reserva ya fue cancelada anteriormente');
            } else if (result.message === 'reservation_not_found') {
                showToast('error', 'La reserva no existe');
            } else {
                showToast('error', result.detail || result.message || 'Error al cancelar la reserva');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('error', 'Error al procesar la solicitud');
        });
});
