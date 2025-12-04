let todosLosEventos = [];
function normalizarHora(hora) {
    if (!hora) return hora;
    const partes = hora.split(':');
    if (partes.length >= 2) {
        return `${String(partes[0]).padStart(2, '0')}:${String(partes[1]).padStart(2, '0')}`;
    }
    return hora;
}
document.addEventListener('DOMContentLoaded', () => {
    inicializarEventosDesdeTabla();
    filtrarEventos('todos');
    const btnsFiltroEvento = document.querySelectorAll('[data-filtro-evento]');
    btnsFiltroEvento.forEach(btn => {
        btn.addEventListener('click', () => {
            btnsFiltroEvento.forEach(b => b.classList.remove('filter-btn-active'));
            btn.classList.add('filter-btn-active');
            const filtro = btn.getAttribute('data-filtro-evento');
            filtrarEventos(filtro);
        });
    });
    normalizarInputsHora();
    const textareas = document.querySelectorAll('#modal-crear-evento #descripcion, #modal-editar-evento #descripcion');
    textareas.forEach(textarea => {
        textarea.addEventListener('input', function() {
            this.style.height = 'auto';
            this.style.height = this.scrollHeight + 'px';
        });
        const modal = textarea.closest('.modal-overlay');
        if (modal) {
            const observer = new MutationObserver(() => {
                if (modal.classList.contains('active')) {
                    textarea.style.height = 'auto';
                    textarea.style.height = textarea.scrollHeight + 'px';
                }
            });
            observer.observe(modal, { attributes: true, attributeFilter: ['class'] });
        }
    });
});
function normalizarInputsHora() {
    const normalizarHora = (input) => {
        if (!input || !input.value) return;
        const partes = input.value.split(':');
        if (partes.length >= 2) {
            const horas = String(partes[0]).padStart(2, '0');
            const minutos = String(partes[1]).padStart(2, '0');
            input.value = `${horas}:${minutos}`;
        }
    };
    document.querySelectorAll('input[type="time"]').forEach(input => {
        input.addEventListener('blur', function() {
            normalizarHora(this);
        });
        input.addEventListener('change', function() {
            normalizarHora(this);
        });
    });
    const observer = new MutationObserver((mutations) => {
        mutations.forEach((mutation) => {
            mutation.addedNodes.forEach((node) => {
                if (node.nodeType === 1) {
                    const inputs = node.querySelectorAll ? node.querySelectorAll('input[type="time"]') : [];
                    inputs.forEach(input => {
                        input.addEventListener('blur', function() {
                            normalizarHora(this);
                        });
                        input.addEventListener('change', function() {
                            normalizarHora(this);
                        });
                    });
                }
            });
        });
    });
    observer.observe(document.body, { childList: true, subtree: true });
}
function inicializarEventosDesdeTabla() {
    const tbody = document.querySelector('#evento tbody');
    if (!tbody) return;
    const filas = tbody.querySelectorAll('tr');
    todosLosEventos = [];
    filas.forEach(fila => {
        const celdas = fila.querySelectorAll('td');
        if (celdas.length >= 5) {
            const btnEditar = fila.querySelector('.btn-editar');
            if (btnEditar) {
                todosLosEventos.push({
                    id_evento: btnEditar.getAttribute('data-id'),
                    nombre: celdas[0].textContent.trim(),
                    descripcion: celdas[1].textContent.trim(),
                    fecha: celdas[2].textContent.trim(),
                    hora_inicio: celdas[3].textContent.trim(),
                    hora_fin: celdas[4].textContent.trim()
                });
            }
        }
    });
}
function filtrarEventos(filtro) {
    let eventosFiltrados;
    const hoy = new Date().toISOString().split('T')[0];
    if (filtro === 'todos') {
        eventosFiltrados = todosLosEventos;
    } else if (filtro === 'proximos') {
        eventosFiltrados = todosLosEventos.filter(e => e.fecha >= hoy);
    } else if (filtro === 'pasados') {
        eventosFiltrados = todosLosEventos.filter(e => e.fecha < hoy);
    }
    renderizarEventos(eventosFiltrados);
}
function renderizarEventos(eventos) {
    const tbody = document.querySelector('#evento tbody');
    if (!tbody) return;
    tbody.innerHTML = '';
    if (eventos.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="7" class="text-center p-4 text-muted">
                    No hay eventos para este filtro
                </td>
            </tr>
        `;
        return;
    }    
    eventos.forEach(ev => {
        const tr = document.createElement('tr');
        const imagenHTML = ev.imagen 
            ? `<img src="/public/images/evento/${ev.imagen}" 
                    alt="${ev.nombre}" 
                    class="img-thumbnail" 
                    style="width: 60px; height: 60px; object-fit: cover;">`
            : `<div class="d-flex align-items-center justify-content-center" 
                    style="width: 60px; height: 60px; background-color: #f8f9fa; border: 1px solid #dee2e6; border-radius: 0.25rem;">
                   <small class="text-muted" style="font-size: 0.7rem; text-align: center;">Sin<br>imagen</small>
               </div>`;
        tr.innerHTML = `
            <td>${ev.nombre}</td>
            <td>${ev.descripcion}</td>
            <td>${ev.fecha}</td>
            <td>${ev.hora_inicio}</td>
            <td>${ev.hora_fin || '-'}</td>
            <td class="text-center">${imagenHTML}</td>
            <td class="text-center">
                <div class="d-flex gap-1 justify-content-center flex-wrap">
                    <button class="btn btn-editar" data-id="${ev.id_evento}" data-controller="Evento">
                        <i class="bi bi-pencil-square"></i>
                    </button>
                    <button class="btn btn-eliminar" data-id="${ev.id_evento}" data-controller="Evento">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>
            </td>
        `;
        tbody.appendChild(tr);
    });
    document.querySelectorAll('.btn-editar[data-controller="Evento"]').forEach(btn => {
        btn.addEventListener('click', () => {
            abrirEditar(btn.dataset.id, 'Evento');
        });
    });
    document.querySelectorAll('.btn-eliminar[data-controller="Evento"]').forEach(btn => {
        btn.addEventListener('click', () => {
            abrirEliminar(btn.dataset.id, 'Evento', 'eliminar', {
                title: 'Eliminar evento',
                message: '¿Estás seguro de eliminar este evento?'
            });
        });
    });
}
async function cargarEventos() {
    const tbody = document.querySelector('#evento tbody');
    if (!tbody) return;
    try {
        const response = await fetch('/app/controllers/EventoController.php?action=index', {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        });
        if (!response.ok) throw new Error('Error al cargar eventos');
        const eventos = await response.json();
        todosLosEventos = eventos;
        const btnActivo = document.querySelector('[data-filtro-evento].filter-btn-active');
        const filtroActual = btnActivo ? btnActivo.getAttribute('data-filtro-evento') : 'todos';
        filtrarEventos(filtroActual);
    } catch (error) {
        console.error('Error al cargar eventos:', error);
        tbody.innerHTML = `
            <tr>
                <td colspan="6" class="text-center p-4 text-danger">
                    Error al cargar eventos. Intenta recargar la página.
                </td>
            </tr>
        `;
    }
}
const btnGuardarEvento = document.getElementById('btn-guardar-evento');
if (btnGuardarEvento) btnGuardarEvento.addEventListener('click', (e) => {
    e.preventDefault();
    const modal = document.getElementById('modal-crear-evento');
    if (!modal) return;
    const nombre = modal.querySelector('#nombre') ? modal.querySelector('#nombre').value : '';
    const descripcion = modal.querySelector('#descripcion') ? modal.querySelector('#descripcion').value : '';
    const fecha = modal.querySelector('#fecha') ? modal.querySelector('#fecha').value : '';
    const horaInicio = modal.querySelector('#horaInicio') ? modal.querySelector('#horaInicio').value : '';
    const horaFin = modal.querySelector('#horaFin') ? modal.querySelector('#horaFin').value : '';
    const imagenEl = modal.querySelector('#imagen');
    let horaInicioNormalizada = horaInicio;
    let horaFinNormalizada = horaFin;
    if (horaInicio) {
        const partesInicio = horaInicio.split(':');
        if (partesInicio.length >= 2) {
            horaInicioNormalizada = `${String(partesInicio[0]).padStart(2, '0')}:${String(partesInicio[1]).padStart(2, '0')}`;
        }
    }
    if (horaFin) {
        const partesFin = horaFin.split(':');
        if (partesFin.length >= 2) {
            horaFinNormalizada = `${String(partesFin[0]).padStart(2, '0')}:${String(partesFin[1]).padStart(2, '0')}`;
        }
    }
    let data = new FormData();
    data.append('nombre', nombre);
    data.append('descripcion', descripcion);
    data.append('fecha', fecha);
    data.append('hora_inicio', horaInicioNormalizada);
    if (horaFinNormalizada) data.append('hora_fin', horaFinNormalizada);
    if (imagenEl && imagenEl.files && imagenEl.files[0]) {
        data.append('imagen', imagenEl.files[0]);
    }
    fetch('/app/controllers/EventoController.php?action=guardar', { method: 'POST', headers: { 'X-Requested-With': 'XMLHttpRequest' }, body: data })
        .then(parseResponse)
        .then(resp => {
            if (resp && resp.status === 'ok') {
                showToast('success', '✓ Evento creado exitosamente');
                const form = document.getElementById('form-crear-evento');
                if (form) form.reset();
                modal.classList.remove('active');
                cargarEventos();
            }
            else {
                let errorMsg = 'Error al crear evento';
                if (resp.errors && Array.isArray(resp.errors)) {
                    if (resp.errors.includes('nombre_required')) errorMsg = 'El nombre del evento es requerido';
                    else if (resp.errors.includes('fecha_invalid')) errorMsg = 'La fecha del evento no es válida';
                    else if (resp.errors.includes('hora_inicio_invalid')) errorMsg = 'La hora de inicio no es válida';
                    else if (resp.errors.includes('hora_fin_invalid')) errorMsg = 'La hora de fin no es válida';
                    else if (resp.errors.includes('imagen_too_large')) errorMsg = 'La imagen es demasiado grande (máx. 2MB)';
                    else if (resp.errors.includes('imagen_invalid_type')) errorMsg = 'Formato de imagen no válido (solo JPG/PNG)';
                } else if (resp.message) {
                    errorMsg = resp.message;
                }
                showToast('error', '✗ ' + errorMsg);
            }
        })
        .catch(err => { console.error(err); showToast('error','✗ Error de red al crear evento'); });
});
const btnCancelarEvento = document.getElementById('btn-cancelar-evento');
if (btnCancelarEvento) btnCancelarEvento.addEventListener('click', (e) => {
    e.preventDefault();
    const modal = document.getElementById('modal-crear-evento');
    if (modal) {
        modal.classList.remove('active');
    }
    const form = document.getElementById('form-crear-evento');
    if (form) form.reset();
});
const btnEditarEvento = document.getElementById('btn-editar-evento');
if (btnEditarEvento) btnEditarEvento.addEventListener('click', (e) => {
    e.preventDefault();
    const modal = document.getElementById('modal-editar-evento');
    if (!modal) return;
    const id = modal.querySelector('#id') ? modal.querySelector('#id').value : '';
    const nombre = modal.querySelector('#nombre') ? modal.querySelector('#nombre').value : '';
    const descripcion = modal.querySelector('#descripcion') ? modal.querySelector('#descripcion').value : '';
    const fecha = modal.querySelector('#fecha') ? modal.querySelector('#fecha').value : '';
    const horaInicio = modal.querySelector('#horaInicio') ? modal.querySelector('#horaInicio').value : '';
    const horaFin = modal.querySelector('#horaFin') ? modal.querySelector('#horaFin').value : '';
    const imagenEl = modal.querySelector('#imagen');
    const horaInicioNormalizada = normalizarHora(horaInicio);
    const horaFinNormalizada = normalizarHora(horaFin);
    let data = new FormData();
    data.append('id', id);
    data.append('nombre', nombre);
    data.append('descripcion', descripcion);
    data.append('fecha', fecha);
    data.append('hora_inicio', horaInicioNormalizada);
    if (horaFinNormalizada) data.append('hora_fin', horaFinNormalizada);
    if (imagenEl && imagenEl.files && imagenEl.files[0]) {
        data.append('imagen', imagenEl.files[0]);
    }
    fetch('/app/controllers/EventoController.php?action=actualizar', { method: 'POST', headers: { 'X-Requested-With': 'XMLHttpRequest' }, body: data })
        .then(parseResponse)
        .then(resp => {
            if (resp && resp.status === 'ok') {
                showToast('success', '✓ Evento actualizado exitosamente');
                modal.classList.remove('active');
                cargarEventos();
            }
            else {
                let errorMsg = 'Error al actualizar evento';
                if (resp.errors && Array.isArray(resp.errors)) {
                    if (resp.errors.includes('id_required')) errorMsg = 'ID del evento no proporcionado';
                    else if (resp.errors.includes('nombre_required')) errorMsg = 'El nombre del evento es requerido';
                    else if (resp.errors.includes('fecha_invalid')) errorMsg = 'La fecha del evento no es válida';
                    else if (resp.errors.includes('hora_inicio_invalid')) errorMsg = 'La hora de inicio no es válida';
                    else if (resp.errors.includes('hora_fin_invalid')) errorMsg = 'La hora de fin no es válida';
                    else if (resp.errors.includes('imagen_too_large')) errorMsg = 'La imagen es demasiado grande (máx. 2MB)';
                    else if (resp.errors.includes('imagen_invalid_type')) errorMsg = 'Formato de imagen no válido (solo JPG/PNG)';
                } else if (resp.message) {
                    errorMsg = resp.message;
                }
                showToast('error', '✗ ' + errorMsg);
            }
        })
        .catch(err => { console.error(err); showToast('error','✗ Error de red al actualizar evento'); });
});
const btnCancelarEditarEvento = document.getElementById('btn-cancelar-editar-evento');
if (btnCancelarEditarEvento) btnCancelarEditarEvento.addEventListener('click', (e) => {
    e.preventDefault();
    const modal = document.getElementById('modal-editar-evento');
    if (modal) {
        modal.classList.remove('active');
    }
});