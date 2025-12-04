/**
 * mesasJS
 * Script para gestionar mesas, disponibilidad y asignación de reservas
 * Incluye carga de disponibilidades, renderización dinámica y confirmación de reservas
 */

/**
 * Obtiene la disponibilidad de mesas para una fecha específica
 * Realiza solicitud AJAX al servidor
 *
 * @async
 * @param {string} fecha Fecha en formato YYYY-MM-DD (default: hoy)
 * @return {Promise<Array>} Array con datos de disponibilidad
 */
async function getMesas(fecha) {
    try {
        // Usar fecha actual si no se proporciona
        if (!fecha) {
            fecha = new Date().toISOString().slice(0, 10);
        }

        // Realizar solicitud AJAX
        const res = await fetch(`/app/controllers/DisponibilidadController.php?action=listar&fecha=${fecha}`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        });

        // Parsear respuesta
        const data = await parseResponse(res);
        if (!data) {
            return [];
        }

        // Extraer cantidad de mesas (soporta múltiples nombres de propiedades)
        const cantidad = data.cantidad ?? data.cantidad_mesas ?? data.cantidad;
        const id = data.id ?? null;

        // Retornar array con disponibilidad formateada
        return [{
            date: fecha,
            count: parseInt(cantidad || 0, 10),
            id
        }];
    } catch (e) {
        // Registrar error y retornar array vacío
        console.error('Error getMesas:', e);
        return [];
    }
}

/**
 * Renderiza las mesas en la tabla
 * Crea filas con estado de reservas y botones de acción
 *
 * @async
 * @return {void}
 */
async function renderMesas() {
    // Obtener tbody
    const tbody = document.getElementById('mesas-tbody');
    if (!tbody) {
        return;
    }

    // Obtener disponibilidad para hoy
    const hoy = new Date().toISOString().slice(0, 10);
    const list = await getMesas(hoy);

    // Limpiar tabla
    tbody.innerHTML = '';

    // Mostrar mensaje si no hay disponibilidad
    if (!list || list.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="5" class="text-center p-4 text-muted">
                    No hay disponibilidad registrada
                </td>
            </tr>
        `;
        return;
    }

    // Iterar sobre disponibilidades
    list.forEach((item) => {
        // Obtener cantidad de mesas
        const count = parseInt(item.count || 0, 10);

        // Filtrar reservas para la fecha
        const reservasForDate = (window.reservasHoy || []).filter(r => r.fecha === item.date.toString());

        // Crear fila para cada mesa disponible
        for (let i = 1; i <= count; i++) {
            const tr = document.createElement('tr');

            // Buscar reserva asignada a esta mesa
            let reservaAsignada = reservasForDate.find(r => r.mesa && parseInt(r.mesa, 10) === i);

            // Buscar reserva sin mesa asignada
            let reservaPendiente = null;
            if (!reservaAsignada) {
                reservaPendiente = reservasForDate.find(r => !r.mesa || r.mesa === '' || r.mesa === null);
            }

            // Variables para datos de la fila
            let cliente = '-';
            let hora = '';
            let accionesHtml = '';

            // Determinar acciones según estado
            if (reservaAsignada) {
                // Reserva asignada a mesa
                cliente = reservaAsignada.nombre || reservaAsignada.cliente || '-';
                hora = reservaAsignada.hora || '';

                if (reservaAsignada.estado === 'pendiente') {
                    // Si está pendiente: mostrar botones para confirmar o cancelar
                    accionesHtml = `
                        <button class="btn btn-sm btn-confirm-reserva" data-id="${reservaAsignada.id_reserva}">Confirmar</button>
                        <button class="btn btn-sm btn-decline-reserva" data-id="${reservaAsignada.id_reserva}">Cancelar</button>
                    `;
                } else {
                    // Si está confirmada: mostrar badge y botón para cancelar
                    accionesHtml = `<span class="badge badge-confirmada">Confirmada</span> <button class="btn btn-sm btn-decline-reserva" data-id="${reservaAsignada.id_reserva}">Cancelar</button>`;
                }
            } else if (reservaPendiente) {
                // Reserva sin mesa: mostrar botón para asignar
                cliente = reservaPendiente.nombre || reservaPendiente.cliente || '-';
                hora = reservaPendiente.hora || '';
                accionesHtml = `<button class="btn btn-sm btn-assign-reserva" data-id="${reservaPendiente.id_reserva}" data-mesa="${i}">Asignar y Confirmar</button>`;
            } else {
                // Mesa libre: mostrar botones para editar/eliminar disponibilidad
                accionesHtml = `<button class="btn btn-sm btn-edit-mesas" data-date="${item.date}">Editar Disponibilidad</button> <button class="btn btn-sm btn-delete-mesas" data-date="${item.date}">Eliminar Disponibilidad</button>`;
            }

            // Construir HTML de la fila
            tr.innerHTML = `
                <td>${i}</td>
                <td>${cliente}</td>
                <td>${item.date}</td>
                <td>${hora || ''}</td>
                <td class="text-center">
                    <div class="d-flex gap-1 justify-content-center">${accionesHtml}</div>
                </td>
            `;

            // Agregar fila a tabla
            tbody.appendChild(tr);
        }
    });
}

/**
 * Evento: Cuando el DOM está completamente cargado
 * Inicializa reservas, renderiza mesas y configura event listeners
 */
document.addEventListener('DOMContentLoaded', () => {
    // Validar que exista tabla de mesas
    const mesasTBody = document.getElementById('mesas-tbody');
    if (!mesasTBody) {
        return;
    }

    // Inicializar input de fecha con la fecha actual
    const fechaInput = document.getElementById('mesas-fecha');
    if (fechaInput) {
        fechaInput.value = new Date().toISOString().slice(0, 10);
    }

    // Obtener fecha de hoy
    const hoy = new Date().toISOString().slice(0, 10);
    window.reservasHoy = [];

    // Cargar reservas desde el servidor
    fetch(`/app/controllers/ReservaController.php?action=listar&fecha=${hoy}`, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
        .then(parseResponse)
        .then(data => {
            window.reservasHoy = data || [];
            renderMesas();
        })
        .catch(err => {
            console.error('No se pudieron cargar reservas:', err);
            renderMesas();
        });

    // Evento: Botón crear mesas
    const btnCreateMesas = document.getElementById('btn-create-mesas');
    if (btnCreateMesas) {
        btnCreateMesas.addEventListener('click', () => {
            // Abrir modal
            const modal = document.getElementById('modal-create-mesas');
            if (modal) {
                modal.classList.remove('d-none');
                modal.classList.add('active');
            }

            // Establecer fecha a hoy
            const f = document.getElementById('mesas-fecha');
            if (f) {
                f.value = hoy;
            }
        });
    }

    // Evento: Botón cancelar en modal
    const btnCancelarMesas = document.getElementById('btn-cancelar-mesas');
    if (btnCancelarMesas) {
        btnCancelarMesas.addEventListener('click', (e) => {
            e.preventDefault();

            // Cerrar modal
            const modal = document.getElementById('modal-create-mesas');
            if (modal) {
                modal.classList.remove('active');
                modal.classList.add('d-none');
            }

            // Limpiar formulario
            const form = document.getElementById('form-create-mesas');
            if (form) {
                form.reset();
            }
        });
    }

    // Evento: Botón guardar disponibilidad
    const btnGuardarMesas = document.getElementById('btn-guardar-mesas');
    if (btnGuardarMesas) {
        btnGuardarMesas.addEventListener('click', (e) => {
            e.preventDefault();

            // Obtener valores del formulario
            const f = document.getElementById('mesas-fecha');
            const c = document.getElementById('mesas-cantidad');
            if (!f || !c) {
                return showToast('warning', 'Campos incompletos');
            }

            const fecha = f.value;
            const cantidad = parseInt(c.value || '0', 10);

            // Validar que sea para hoy
            if (fecha !== hoy) {
                return showToast('warning', 'Solo puedes agregar o actualizar la disponibilidad para hoy.');
            }

            // Crear FormData
            let data = new FormData();
            data.append('fecha', fecha);
            data.append('cantidad', cantidad);

            // Realizar petición POST
            fetch('/app/controllers/DisponibilidadController.php?action=guardar', {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                body: data
            })
                .then(parseResponse)
                .then(resp => {
                    if (resp && resp.status === 'ok') {
                        // Cerrar modal
                        const modal = document.getElementById('modal-create-mesas');
                        if (modal) {
                            modal.classList.remove('active');
                            modal.classList.add('d-none');
                        }

                        // Recargar reservas y renderizar
                        return fetch(`/app/controllers/ReservaController.php?action=listar&fecha=${hoy}`, {
                            headers: { 'X-Requested-With': 'XMLHttpRequest' }
                        })
                            .then(parseResponse)
                            .then(d => {
                                window.reservasHoy = d || [];
                                renderMesas();
                            });
                    } else {
                        showToast('error', 'Error al guardar disponibilidad: ' + (resp.message || JSON.stringify(resp)));
                    }
                })
                .catch(err => {
                    console.error(err);
                    showToast('error', 'Error de red al guardar disponibilidad');
                });
        });
    }

    // Evento: Event delegation en tabla de mesas
    const mesasTbody = document.getElementById('mesas-tbody');
    if (mesasTbody) {
        mesasTbody.addEventListener('click', async (e) => {
            const target = e.target;

            // Botón editar disponibilidad
            if (target.classList.contains('btn-edit-mesas')) {
                const date = target.dataset.date;
                try {
                    // Cargar disponibilidad
                    const res = await fetch(`/app/controllers/DisponibilidadController.php?action=listar&fecha=${date}`, {
                        headers: { 'X-Requested-With': 'XMLHttpRequest' }
                    });
                    const item = await parseResponse(res);

                    if (!item) {
                        return showToast('error', 'Registro no encontrado');
                    }

                    // Abrir modal
                    const modal = document.getElementById('modal-create-mesas');
                    if (modal) {
                        modal.classList.remove('d-none');
                        modal.classList.add('active');
                    }

                    // Llenar formulario
                    const f = document.getElementById('mesas-fecha');
                    const c = document.getElementById('mesas-cantidad');
                    if (f) {
                        f.value = item.fecha || date;
                    }
                    if (c) {
                        c.value = item.cantidad ?? item.count ?? 0;
                    }
                } catch (err) {
                    console.error(err);
                    showToast('error', 'Error al cargar registro de disponibilidad');
                }
            }

            // Botón eliminar disponibilidad
            if (target.classList.contains('btn-delete-mesas')) {
                const date = target.dataset.date;
                try {
                    // Cargar disponibilidad
                    const res = await fetch(`/app/controllers/DisponibilidadController.php?action=listar&fecha=${date}`, {
                        headers: { 'X-Requested-With': 'XMLHttpRequest' }
                    });
                    const item = await parseResponse(res);

                    if (!item || !item.id) {
                        return showToast('warning', 'No hay registro para eliminar');
                    }

                    // Crear FormData para eliminar
                    let data = new FormData();
                    data.append('id', item.id);

                    // Realizar petición de eliminación
                    const del = await fetch('/app/controllers/DisponibilidadController.php?action=eliminar', {
                        method: 'POST',
                        headers: { 'X-Requested-With': 'XMLHttpRequest' },
                        body: data
                    });
                    const resp = await parseResponse(del);

                    if (resp && resp.status === 'ok') {
                        // Recargar reservas y renderizar
                        const r = await fetch(`/app/controllers/ReservaController.php?action=listar&fecha=${hoy}`, {
                            headers: { 'X-Requested-With': 'XMLHttpRequest' }
                        });
                        window.reservasHoy = await parseResponse(r);
                        renderMesas();
                    } else {
                        showToast('error', 'Error al eliminar: ' + (resp.message || JSON.stringify(resp)));
                    }
                } catch (err) {
                    console.error(err);
                    showToast('error', 'Error de red al eliminar disponibilidad');
                }
            }

            // Botón asignar reserva a mesa
            if (target.classList.contains('btn-assign-reserva')) {
                const id = target.dataset.id;
                const mesa = target.dataset.mesa;

                if (!id) {
                    return showToast('error', 'ID de reserva no encontrado');
                }

                // Crear FormData
                let data = new FormData();
                data.append('id', id);
                data.append('mesa', mesa);

                // Realizar petición POST
                fetch('/app/controllers/ReservaController.php?action=confirmar', {
                    method: 'POST',
                    headers: { 'X-Requested-With': 'XMLHttpRequest' },
                    body: data
                })
                    .then(parseResponse)
                    .then(resp => {
                        if (resp && resp.status === 'ok') {
                            // Recargar y renderizar
                            fetch(`/app/controllers/ReservaController.php?action=listar&fecha=${new Date().toISOString().slice(0, 10)}`)
                                .then(parseResponse)
                                .then(d => {
                                    window.reservasHoy = d || [];
                                    renderMesas();
                                });
                        } else {
                            showToast('error', 'Error al asignar: ' + (resp.message || JSON.stringify(resp)));
                        }
                    })
                    .catch(err => {
                        console.error(err);
                        showToast('error', 'Error de red al asignar reserva');
                    });
            }

            // Botón confirmar reserva
            if (target.classList.contains('btn-confirm-reserva')) {
                const id = target.dataset.id;

                if (!id) {
                    return showToast('error', 'ID de reserva no encontrado');
                }

                // Crear FormData
                let data = new FormData();
                data.append('id', id);

                // Realizar petición POST
                fetch('/app/controllers/ReservaController.php?action=confirmar', {
                    method: 'POST',
                    headers: { 'X-Requested-With': 'XMLHttpRequest' },
                    body: data
                })
                    .then(parseResponse)
                    .then(resp => {
                        if (resp && resp.status === 'ok') {
                            // Recargar y renderizar
                            fetch(`/app/controllers/ReservaController.php?action=listar&fecha=${new Date().toISOString().slice(0, 10)}`)
                                .then(parseResponse)
                                .then(d => {
                                    window.reservasHoy = d || [];
                                    renderMesas();
                                });
                        } else {
                            showToast('error', 'Error al confirmar: ' + (resp.message || JSON.stringify(resp)));
                        }
                    })
                    .catch(err => {
                        console.error(err);
                        showToast('error', 'Error de red al confirmar reserva');
                    });
            }

            // Botón cancelar/declinar reserva
            if (target.classList.contains('btn-decline-reserva')) {
                const id = target.dataset.id;

                if (!id) {
                    return showToast('error', 'ID de reserva no encontrado');
                }

                // Abrir diálogo de eliminación
                abrirEliminar(id, 'Reserva', 'cancelar', {
                    title: 'Cancelar reserva',
                    message: '¿Seguro que quieres cancelar esta reserva?'
                });
            }
        });
    }
});