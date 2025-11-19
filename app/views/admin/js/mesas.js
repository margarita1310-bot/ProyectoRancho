// Mesas: persistir disponibilidad vía API (DisponibilidadController.php)
async function getMesas(fecha) {
    try {
        if (!fecha) fecha = new Date().toISOString().slice(0,10);
        const res = await fetch(`../../app/controllers/DisponibilidadController.php?action=listar&fecha=${fecha}`, { headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' } });
        const data = await parseResponse(res);
        if (!data) return [];
        const cantidad = data.cantidad ?? data.cantidad_mesas ?? data.cantidad_mesas ?? data.cantidad;
        const id = data.id ?? null;
        return [{ date: fecha, count: parseInt(cantidad||0,10), id }];
    } catch (e) {
        console.error('Error getMesas', e);
        return [];
    }
}

async function renderMesas() {
    const tbody = document.getElementById('mesas-tbody');
    if (!tbody) return;
    const hoy = new Date().toISOString().slice(0,10);
    const list = await getMesas(hoy);
    tbody.innerHTML = '';
    if (!list || list.length === 0) {
        tbody.innerHTML = '<tr><td colspan="5" class="text-center">No hay disponibilidad registrada</td></tr>';
        return;
    }
    let globalIndex = 1;
    list.forEach((item) => {
        const count = parseInt(item.count || 0, 10);
        const reservasForDate = (window.reservasHoy || []).filter(r => r.fecha === item.date.toString());
        for (let i = 1; i <= count; i++) {
            const tr = document.createElement('tr');
            let reservaAsignada = reservasForDate.find(r => r.mesa && parseInt(r.mesa,10) === i);
            let reservaPendiente = null;
            if (!reservaAsignada) reservaPendiente = reservasForDate.find(r => !r.mesa || r.mesa === '' || r.mesa === null);

            let cliente = '-';
            let hora = '';
            let accionesHtml = '';

            if (reservaAsignada) {
                cliente = reservaAsignada.nombre || reservaAsignada.cliente || '-';
                hora = reservaAsignada.hora || '';
                if (reservaAsignada.estado === 'pendiente') {
                    accionesHtml = `
                        <button class="btn btn-sm btn-confirm-reserva" data-id="${reservaAsignada.id_reserva}">Confirmar</button>
                        <button class="btn btn-sm btn-decline-reserva" data-id="${reservaAsignada.id_reserva}">Cancelar</button>
                    `;
                } else {
                    accionesHtml = `<span class="badge bg-success">Confirmada</span> <button class="btn btn-sm btn-decline-reserva" data-id="${reservaAsignada.id_reserva}">Cancelar</button>`;
                }
            } else if (reservaPendiente) {
                cliente = reservaPendiente.nombre || reservaPendiente.cliente || '-';
                hora = reservaPendiente.hora || '';
                accionesHtml = `<button class="btn btn-sm btn-assign-reserva" data-id="${reservaPendiente.id_reserva}" data-mesa="${i}">Asignar y Confirmar</button>`;
            } else {
                accionesHtml = `<button class="btn btn-sm btn-edit-mesas" data-date="${item.date}">Editar Disponibilidad</button> <button class="btn btn-sm btn-delete-mesas" data-date="${item.date}">Eliminar Disponibilidad</button>`;
            }

            tr.innerHTML = `
                <td>${i}</td>
                <td>${cliente}</td>
                <td>${item.date}</td>
                <td>${hora || ''}</td>
                <td class="text-center">
                    <div class="d-flex gap-1 justify-content-center">${accionesHtml}</div>
                </td>
            `;
            tbody.appendChild(tr);
            globalIndex++;
        }
    });
}

// Inicializar campos del modal de mesas y handlers (usando API)
document.addEventListener('DOMContentLoaded', () => {
    // Solo ejecutar si estamos en la vista de mesas
    const mesasTBody = document.getElementById('mesas-tbody');
    if (!mesasTBody) return;
    
    const fechaInput = document.getElementById('mesas-fecha');
    if (fechaInput) fechaInput.value = new Date().toISOString().slice(0,10);

    const hoy = new Date().toISOString().slice(0,10);
    window.reservasHoy = [];
    fetch(`../../app/controllers/ReservaController.php?action=listar&fecha=${hoy}`, { headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' } })
        .then(parseResponse)
        .then(data => { window.reservasHoy = data || []; renderMesas(); })
        .catch(err => { console.error('No se pudieron cargar reservas:', err); renderMesas(); });

    const btnCreateMesas = document.getElementById('btn-create-mesas');
    if (btnCreateMesas) btnCreateMesas.addEventListener('click', () => {
        const modal = document.getElementById('modal-create-mesas');
        if (modal) { modal.classList.remove('d-none'); modal.classList.add('active'); }
        const f = document.getElementById('mesas-fecha'); if (f) f.value = hoy;
    });

    // Cancelar modal de disponibilidad
    const btnCancelarMesas = document.getElementById('btn-cancelar-mesas');
    if (btnCancelarMesas) btnCancelarMesas.addEventListener('click', (e) => {
        e.preventDefault();
        const modal = document.getElementById('modal-create-mesas');
        if (modal) { modal.classList.remove('active'); modal.classList.add('d-none'); }
        const form = document.getElementById('form-create-mesas');
        if (form) form.reset();
    });

    // Guardar disponibilidad
    const btnGuardarMesas = document.getElementById('btn-guardar-mesas');
    if (btnGuardarMesas) btnGuardarMesas.addEventListener('click', (e) => {
        e.preventDefault();
        const f = document.getElementById('mesas-fecha');
        const c = document.getElementById('mesas-cantidad');
        if (!f || !c) return showToast('warning','Campos incompletos');
        const fecha = f.value;
        const cantidad = parseInt(c.value || '0', 10);
        if (fecha !== hoy) return showToast('warning','Solo puedes agregar o actualizar la disponibilidad para hoy.');

        let data = new FormData(); data.append('fecha', fecha); data.append('cantidad', cantidad);
        fetch('../../app/controllers/DisponibilidadController.php?action=guardar', { method: 'POST', headers: { 'X-Requested-With': 'XMLHttpRequest' }, body: data })
            .then(parseResponse)
            .then(resp => {
                if (resp && resp.status === 'ok') {
                    const modal = document.getElementById('modal-create-mesas');
                    if (modal) { modal.classList.remove('active'); modal.classList.add('d-none'); }
                    return fetch(`../../app/controllers/ReservaController.php?action=listar&fecha=${hoy}`, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                        .then(parseResponse).then(d=>{ window.reservasHoy = d||[]; renderMesas(); });
                } else {
                        showToast('error','Error al guardar disponibilidad: ' + (resp.message || JSON.stringify(resp)));
                }
            })
            .catch(err => { console.error(err); showToast('error','Error de red al guardar disponibilidad'); });
    });

    // Delegación en tbody
    const mesasTbody = document.getElementById('mesas-tbody');
    mesasTbody && mesasTbody.addEventListener('click', async (e) => {
        const target = e.target;
        if (target.classList.contains('btn-edit-mesas')) {
            const date = target.dataset.date;
            try {
                const res = await fetch(`../../app/controllers/DisponibilidadController.php?action=listar&fecha=${date}`, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
                const item = await parseResponse(res);
                if (!item) return showToast('error','Registro no encontrado');
                const modal = document.getElementById('modal-create-mesas');
                if (modal) { modal.classList.remove('d-none'); modal.classList.add('active'); }
                const f = document.getElementById('mesas-fecha');
                const c = document.getElementById('mesas-cantidad');
                if (f) f.value = item.fecha || date;
                if (c) c.value = item.cantidad ?? item.count ?? 0;
            } catch (err) { console.error(err); showToast('error','Error al cargar registro de disponibilidad'); }
        }

        if (target.classList.contains('btn-delete-mesas')) {
            const date = target.dataset.date;
            try {
                const res = await fetch(`../../app/controllers/DisponibilidadController.php?action=listar&fecha=${date}`, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
                const item = await parseResponse(res);
                if (!item || !item.id) return showToast('warning','No hay registro para eliminar');
                let data = new FormData(); data.append('id', item.id);
                const del = await fetch('../../app/controllers/DisponibilidadController.php?action=eliminar', { method: 'POST', headers: { 'X-Requested-With': 'XMLHttpRequest' }, body: data });
                const resp = await parseResponse(del);
                if (resp && resp.status === 'ok') {
                    const r = await fetch(`../../app/controllers/ReservaController.php?action=listar&fecha=${hoy}`, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
                    window.reservasHoy = await parseResponse(r);
                    renderMesas();
                } else showToast('error','Error al eliminar: ' + (resp.message || JSON.stringify(resp)));
            } catch (err) { console.error(err); showToast('error','Error de red al eliminar disponibilidad'); }
        }

        if (target.classList.contains('btn-assign-reserva')) {
            const id = target.dataset.id;
            const mesa = target.dataset.mesa;
            if (!id) return showToast('error','ID de reserva no encontrado');
            let data = new FormData(); data.append('id', id); data.append('mesa', mesa);
            fetch('../../app/controllers/ReservaController.php?action=confirmar', { method: 'POST', headers: { 'X-Requested-With': 'XMLHttpRequest' }, body: data })
                .then(parseResponse)
                .then(resp => { if (resp && resp.status === 'ok') { fetch(`../../app/controllers/ReservaController.php?action=listar&fecha=${new Date().toISOString().slice(0,10)}`).then(parseResponse).then(d=>{window.reservasHoy=d||[]; renderMesas();}); } else showToast('error','Error al asignar: '+(resp.message||JSON.stringify(resp))); })
                .catch(err => { console.error(err); showToast('error','Error de red al asignar reserva'); });
        }

        if (target.classList.contains('btn-confirm-reserva')) {
            const id = target.dataset.id;
            if (!id) return showToast('error','ID de reserva no encontrado');
            let data = new FormData(); data.append('id', id);
            fetch('../../app/controllers/ReservaController.php?action=confirmar', { method: 'POST', headers: { 'X-Requested-With': 'XMLHttpRequest' }, body: data })
                .then(parseResponse)
                .then(resp => { if (resp && resp.status === 'ok') { fetch(`../../app/controllers/ReservaController.php?action=listar&fecha=${new Date().toISOString().slice(0,10)}`).then(parseResponse).then(d=>{window.reservasHoy=d||[]; renderMesas();}); } else showToast('error','Error al confirmar: '+(resp.message||JSON.stringify(resp))); })
                .catch(err => { console.error(err); showToast('error','Error de red al confirmar reserva'); });
        }

        if (target.classList.contains('btn-decline-reserva')) {
            const id = target.dataset.id;
            if (!id) return showToast('error','ID de reserva no encontrado');
            abrirDelete(id, 'Reservas', 'declinar', { title: 'Declinar reserva', message: '¿Seguro que quieres declinar (eliminar) esta reserva?' });
        }
    });
});
