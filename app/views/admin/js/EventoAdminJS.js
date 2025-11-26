 /*
 * Eventos: crear / editar
 * Nota: La imagen es OPCIONAL en crear y editar
 * Si se proporciona, se valida en el servidor (MIME, tamaño)
 * Si NO se proporciona, simplemente no se sube archivo (no se guarda en BD)
 */

// Variable global para almacenar todos los eventos
let todosLosEventos = [];

/**
 * Configurar filtros de eventos
 */
document.addEventListener('DOMContentLoaded', () => {
    // Inicializar eventos desde la tabla existente
    inicializarEventosDesdeTabla();
    
    const btnsFiltroEvento = document.querySelectorAll('[data-filtro-evento]');
    btnsFiltroEvento.forEach(btn => {
        btn.addEventListener('click', () => {
            // Actualizar botón activo
            btnsFiltroEvento.forEach(b => b.classList.remove('filter-btn-active'));
            btn.classList.add('filter-btn-active');
            
            // Filtrar
            const filtro = btn.getAttribute('data-filtro-evento');
            filtrarEventos(filtro);
        });
    });
});

/**
 * Inicializa los eventos desde la tabla HTML existente
 */
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

/**
 * Filtrar eventos según criterio
 */
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

/**
 * Renderizar tabla de eventos
 */
function renderizarEventos(eventos) {
    const tbody = document.querySelector('#evento tbody');
    if (!tbody) return;
    
    tbody.innerHTML = '';
    
    if (eventos.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="6" class="text-center p-4">
                    <div class="alert alert-info d-inline-flex align-items-center mb-0" role="alert">
                        <svg class="bi flex-shrink-0 me-2" width="20" height="20" role="img">
                            <use xlink:href="#info-fill"/>
                        </svg>
                        <div>No hay eventos para este filtro</div>
                    </div>
                </td>
            </tr>
        `;
        return;
    }
    
    eventos.forEach(ev => {
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td>${ev.nombre}</td>
            <td>${ev.descripcion}</td>
            <td>${ev.fecha}</td>
            <td>${ev.hora_inicio}</td>
            <td>${ev.hora_fin || '-'}</td>
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
    
    // Reactivar event listeners
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

/**
 * Recarga dinámicamente la tabla de eventos
 */
async function cargarEventos() {
    const tbody = document.querySelector('#evento tbody');
    if (!tbody) return;
    
    try {
        const response = await fetch('/app/controllers/EventoController.php?action=index', {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        });
        
        if (!response.ok) throw new Error('Error al cargar eventos');
        
        const eventos = await response.json();
        
        // Guardar todos los eventos
        todosLosEventos = eventos;
        
        // Renderizar con el filtro actual
        const btnActivo = document.querySelector('[data-filtro-evento].filter-btn-active');
        const filtroActual = btnActivo ? btnActivo.getAttribute('data-filtro-evento') : 'todos';
        filtrarEventos(filtroActual);
        
    } catch (error) {
        console.error('Error al cargar eventos:', error);
        tbody.innerHTML = `
            <tr>
                <td colspan="6" class="text-center p-4">
                    <div class="alert alert-danger d-inline-flex align-items-center mb-0" role="alert">
                        <svg class="bi flex-shrink-0 me-2" width="20" height="20" role="img">
                            <use xlink:href="#x-circle-fill"/>
                        </svg>
                        <div><strong>Error al cargar eventos.</strong> Intenta recargar la página.</div>
                    </div>
                </td>
            </tr>
        `;
    }
}

// Crear nuevo evento
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

    let data = new FormData();
    data.append('nombre', nombre);
    data.append('descripcion', descripcion);
    data.append('fecha', fecha);
    data.append('hora_inicio', horaInicio);
    if (horaFin) data.append('hora_fin', horaFin);
    if (imagenEl && imagenEl.files && imagenEl.files[0]) {
        data.append('imagen', imagenEl.files[0]);
    }

    fetch('/app/controllers/EventoController.php?action=guardar', { method: 'POST', headers: { 'X-Requested-With': 'XMLHttpRequest' }, body: data })
        .then(parseResponse)
        .then(resp => {
            if (resp && resp.status === 'ok') {
                showToast('success', 'Evento creado exitosamente');
                const form = document.getElementById('form-crear-evento');
                if (form) form.reset();
                modal.classList.remove('active');
                cargarEventos();
            }
            else showToast('error','Error al crear evento: ' + (resp.message || JSON.stringify(resp)));
        })
        .catch(err => { console.error(err); showToast('error','Error de red al crear evento'); });
});

// Cancelar crear evento
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

// Editar evento existente
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

    let data = new FormData();
    data.append('id', id);
    data.append('nombre', nombre);
    data.append('descripcion', descripcion);
    data.append('fecha', fecha);
    data.append('hora_inicio', horaInicio);
    if (horaFin) data.append('hora_fin', horaFin);
    if (imagenEl && imagenEl.files && imagenEl.files[0]) {
        data.append('imagen', imagenEl.files[0]);
    }

    
    fetch('/app/controllers/EventoController.php?action=actualizar', { method: 'POST', headers: { 'X-Requested-With': 'XMLHttpRequest' }, body: data })
        .then(parseResponse)
        .then(resp => {
            if (resp && resp.status === 'ok') {
                showToast('success', 'Evento actualizado exitosamente');
                modal.classList.remove('active');
                cargarEventos();
            }
            else showToast('error','Error al actualizar evento: ' + (resp.message || JSON.stringify(resp)));
        })
        .catch(err => { console.error(err); showToast('error','Error de red al actualizar evento'); });
});

// Cancelar editar evento
const btnCancelarEditarEvento = document.getElementById('btn-cancelar-editar-evento');
if (btnCancelarEditarEvento) btnCancelarEditarEvento.addEventListener('click', (e) => {
    e.preventDefault();
    const modal = document.getElementById('modal-editar-evento');
    if (modal) {
        modal.classList.remove('active');
    }
});
