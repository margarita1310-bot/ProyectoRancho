/**
 * EventoAdminJS
 * Script para gestionar eventos (crear, editar, eliminar, filtrar)
 * Incluye normalización de horas, renderización dinámica y validación de formularios
 */

// Variable global para almacenar todos los eventos cargados
let todosLosEventos = [];

/**
 * Normaliza una hora al formato HH:MM
 * Completa con ceros a la izquierda si es necesario
 *
 * @param {string} hora Hora en formato H:M o HH:MM
 * @return {string} Hora formateada como HH:MM
 */
function normalizarHora(hora) {
    // Retornar hora original si está vacía
    if (!hora) {
        return hora;
    }

    // Dividir hora en partes (horas y minutos)
    const partes = hora.split(':');
    if (partes.length >= 2) {
        return `${String(partes[0]).padStart(2, '0')}:${String(partes[1]).padStart(2, '0')}`;
    }

    // Retornar hora original si no es válida
    return hora;
}

/**
 * Normaliza todos los inputs de tipo time en la página
 * Aplica formato HH:MM al perder el foco o cambiar valor
 *
 * @return {void}
 */
function normalizarInputsHora() {
    // Función auxiliar para normalizar un input
    const normalizarInput = (input) => {
        if (!input || !input.value) {
            return;
        }

        const partes = input.value.split(':');
        if (partes.length >= 2) {
            const horas = String(partes[0]).padStart(2, '0');
            const minutos = String(partes[1]).padStart(2, '0');
            input.value = `${horas}:${minutos}`;
        }
    };

    // Aplicar normalización a inputs existentes
    document.querySelectorAll('input[type="time"]').forEach(input => {
        input.addEventListener('blur', function () {
            normalizarInput(this);
        });
        input.addEventListener('change', function () {
            normalizarInput(this);
        });
    });

    // Observer para aplicar a inputs creados dinámicamente
    const observer = new MutationObserver((mutations) => {
        mutations.forEach((mutation) => {
            mutation.addedNodes.forEach((node) => {
                if (node.nodeType === 1) {
                    const inputs = node.querySelectorAll ? node.querySelectorAll('input[type="time"]') : [];
                    inputs.forEach(input => {
                        input.addEventListener('blur', function () {
                            normalizarInput(this);
                        });
                        input.addEventListener('change', function () {
                            normalizarInput(this);
                        });
                    });
                }
            });
        });
    });
    observer.observe(document.body, { childList: true, subtree: true });
}

/**
 * Inicializa el array de eventos extrayendo datos de la tabla HTML
 * Usado al cargar la página para llenar todosLosEventos
 *
 * @return {void}
 */
function inicializarEventosDesdeTabla() {
    // Obtener tbody de la tabla de eventos
    const tbody = document.querySelector('#evento tbody');
    if (!tbody) {
        return;
    }

    // Obtener todas las filas de la tabla
    const filas = tbody.querySelectorAll('tr');
    todosLosEventos = [];

    // Iterar sobre filas y extraer datos
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
 * Filtra eventos según criterio especificado
 * Soporta: todos, próximos, pasados
 *
 * @param {string} filtro Tipo de filtro a aplicar
 * @return {void}
 */
function filtrarEventos(filtro) {
    let eventosFiltrados;

    // Obtener fecha actual
    const hoy = new Date().toISOString().split('T')[0];

    // Aplicar filtro según criterio
    if (filtro === 'todos') {
        eventosFiltrados = todosLosEventos;
    } else if (filtro === 'proximos') {
        eventosFiltrados = todosLosEventos.filter(e => e.fecha >= hoy);
    } else if (filtro === 'pasados') {
        eventosFiltrados = todosLosEventos.filter(e => e.fecha < hoy);
    }

    // Renderizar eventos filtrados
    renderizarEventos(eventosFiltrados);
}

/**
 * Renderiza eventos en la tabla HTML
 * Crea filas con datos y botones de edición/eliminación
 *
 * @param {Array} eventos Array de eventos a renderizar
 * @return {void}
 */
function renderizarEventos(eventos) {
    // Obtener tbody
    const tbody = document.querySelector('#evento tbody');
    if (!tbody) {
        return;
    }

    // Limpiar tabla
    tbody.innerHTML = '';

    // Mostrar mensaje si no hay eventos
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

    // Iterar sobre eventos y crear filas
    eventos.forEach(ev => {
        const tr = document.createElement('tr');

        // HTML de imagen o placeholder
        const imagenHTML = ev.imagen
            ? `<img src="/public/images/evento/${ev.imagen}" 
                    alt="${ev.nombre}" 
                    class="img-thumbnail" 
                    style="width: 60px; height: 60px; object-fit: cover;">`
            : `<div class="d-flex align-items-center justify-content-center" 
                    style="width: 60px; height: 60px; background-color: #f8f9fa; border: 1px solid #dee2e6; border-radius: 0.25rem;">
                   <small class="text-muted" style="font-size: 0.7rem; text-align: center;">Sin<br>imagen</small>
               </div>`;

        // Construir HTML de la fila
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

        // Agregar fila a tabla
        tbody.appendChild(tr);
    });

    // Asignar listeners a botones de edición
    document.querySelectorAll('.btn-editar[data-controller="Evento"]').forEach(btn => {
        btn.addEventListener('click', () => {
            abrirEditar(btn.dataset.id, 'Evento');
        });
    });

    // Asignar listeners a botones de eliminación
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
 * Carga eventos desde el servidor y recarga la tabla
 * Mantiene el filtro activo después de cargar
 *
 * @async
 * @return {void}
 */
async function cargarEventos() {
    // Obtener tbody
    const tbody = document.querySelector('#evento tbody');
    if (!tbody) {
        return;
    }

    try {
        // Realizar solicitud AJAX
        const response = await fetch('/app/controllers/EventoController.php?action=index', {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        });

        // Validar respuesta
        if (!response.ok) {
            throw new Error('Error al cargar eventos');
        }

        // Parsear JSON
        const eventos = await response.json();
        todosLosEventos = eventos;

        // Obtener filtro activo
        const btnActivo = document.querySelector('[data-filtro-evento].filter-btn-active');
        const filtroActual = btnActivo ? btnActivo.getAttribute('data-filtro-evento') : 'todos';

        // Aplicar filtro
        filtrarEventos(filtroActual);
    } catch (error) {
        // Registrar error
        console.error('Error al cargar eventos:', error);

        // Mostrar mensaje de error
        tbody.innerHTML = `
            <tr>
                <td colspan="6" class="text-center p-4 text-danger">
                    Error al cargar eventos. Intenta recargar la página.
                </td>
            </tr>
        `;
    }
}

/**
 * Evento: Botón guardar evento
 * Valida datos y realiza petición POST para crear evento
 */
const btnGuardarEvento = document.getElementById('btn-guardar-evento');
if (btnGuardarEvento) {
    btnGuardarEvento.addEventListener('click', (e) => {
        // Prevenir envío por defecto
        e.preventDefault();

        // Obtener modal
        const modal = document.getElementById('modal-crear-evento');
        if (!modal) {
            return;
        }

        // Obtener valores del formulario
        const nombre = modal.querySelector('#nombre') ? modal.querySelector('#nombre').value : '';
        const descripcion = modal.querySelector('#descripcion') ? modal.querySelector('#descripcion').value : '';
        const fecha = modal.querySelector('#fecha') ? modal.querySelector('#fecha').value : '';
        const horaInicio = modal.querySelector('#horaInicio') ? modal.querySelector('#horaInicio').value : '';
        const horaFin = modal.querySelector('#horaFin') ? modal.querySelector('#horaFin').value : '';
        const imagenEl = modal.querySelector('#imagen');

        // Normalizar horas
        const horaInicioNormalizada = normalizarHora(horaInicio);
        const horaFinNormalizada = normalizarHora(horaFin);

        // Crear FormData
        let data = new FormData();
        data.append('nombre', nombre);
        data.append('descripcion', descripcion);
        data.append('fecha', fecha);
        data.append('hora_inicio', horaInicioNormalizada);
        if (horaFinNormalizada) {
            data.append('hora_fin', horaFinNormalizada);
        }
        if (imagenEl && imagenEl.files && imagenEl.files[0]) {
            data.append('imagen', imagenEl.files[0]);
        }

        // Realizar petición POST
        fetch('/app/controllers/EventoController.php?action=guardar', {
            method: 'POST',
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            body: data
        })
            .then(parseResponse)
            .then(resp => {
                // Validar respuesta exitosa
                if (resp && resp.status === 'ok') {
                    showToast('success', '✓ Evento creado exitosamente');
                    const form = document.getElementById('form-crear-evento');
                    if (form) {
                        form.reset();
                    }
                    modal.classList.remove('active');
                    cargarEventos();
                } else {
                    // Procesar errores
                    let errorMsg = 'Error al crear evento';
                    if (resp.errors && Array.isArray(resp.errors)) {
                        if (resp.errors.includes('nombre_required')) {
                            errorMsg = 'El nombre del evento es requerido';
                        } else if (resp.errors.includes('fecha_invalid')) {
                            errorMsg = 'La fecha del evento no es válida';
                        } else if (resp.errors.includes('hora_inicio_invalid')) {
                            errorMsg = 'La hora de inicio no es válida';
                        } else if (resp.errors.includes('hora_fin_invalid')) {
                            errorMsg = 'La hora de fin no es válida';
                        } else if (resp.errors.includes('imagen_too_large')) {
                            errorMsg = 'La imagen es demasiado grande (máx. 2MB)';
                        } else if (resp.errors.includes('imagen_invalid_type')) {
                            errorMsg = 'Formato de imagen no válido (solo JPG/PNG)';
                        }
                    } else if (resp.message) {
                        errorMsg = resp.message;
                    }
                    showToast('error', '✗ ' + errorMsg);
                }
            })
            .catch(err => {
                console.error(err);
                showToast('error', '✗ Error de red al crear evento');
            });
    });
}

/**
 * Evento: Botón cancelar creación de evento
 * Cierra modal y limpia formulario
 */
const btnCancelarEvento = document.getElementById('btn-cancelar-evento');
if (btnCancelarEvento) {
    btnCancelarEvento.addEventListener('click', (e) => {
        e.preventDefault();
        const modal = document.getElementById('modal-crear-evento');
        if (modal) {
            modal.classList.remove('active');
        }
        const form = document.getElementById('form-crear-evento');
        if (form) {
            form.reset();
        }
    });
}

/**
 * Evento: Botón editar evento
 * Valida datos y realiza petición POST para actualizar evento
 */
const btnEditarEvento = document.getElementById('btn-editar-evento');
if (btnEditarEvento) {
    btnEditarEvento.addEventListener('click', (e) => {
        e.preventDefault();

        const modal = document.getElementById('modal-editar-evento');
        if (!modal) {
            return;
        }

        // Obtener valores del formulario
        const id = modal.querySelector('#id') ? modal.querySelector('#id').value : '';
        const nombre = modal.querySelector('#nombre') ? modal.querySelector('#nombre').value : '';
        const descripcion = modal.querySelector('#descripcion') ? modal.querySelector('#descripcion').value : '';
        const fecha = modal.querySelector('#fecha') ? modal.querySelector('#fecha').value : '';
        const horaInicio = modal.querySelector('#horaInicio') ? modal.querySelector('#horaInicio').value : '';
        const horaFin = modal.querySelector('#horaFin') ? modal.querySelector('#horaFin').value : '';
        const imagenEl = modal.querySelector('#imagen');

        // Normalizar horas
        const horaInicioNormalizada = normalizarHora(horaInicio);
        const horaFinNormalizada = normalizarHora(horaFin);

        // Crear FormData
        let data = new FormData();
        data.append('id', id);
        data.append('nombre', nombre);
        data.append('descripcion', descripcion);
        data.append('fecha', fecha);
        data.append('hora_inicio', horaInicioNormalizada);
        if (horaFinNormalizada) {
            data.append('hora_fin', horaFinNormalizada);
        }
        if (imagenEl && imagenEl.files && imagenEl.files[0]) {
            data.append('imagen', imagenEl.files[0]);
        }

        // Realizar petición POST
        fetch('/app/controllers/EventoController.php?action=actualizar', {
            method: 'POST',
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            body: data
        })
            .then(parseResponse)
            .then(resp => {
                if (resp && resp.status === 'ok') {
                    showToast('success', '✓ Evento actualizado exitosamente');
                    modal.classList.remove('active');
                    cargarEventos();
                } else {
                    let errorMsg = 'Error al actualizar evento';
                    if (resp.errors && Array.isArray(resp.errors)) {
                        if (resp.errors.includes('id_required')) {
                            errorMsg = 'ID del evento no proporcionado';
                        } else if (resp.errors.includes('nombre_required')) {
                            errorMsg = 'El nombre del evento es requerido';
                        } else if (resp.errors.includes('fecha_invalid')) {
                            errorMsg = 'La fecha del evento no es válida';
                        } else if (resp.errors.includes('hora_inicio_invalid')) {
                            errorMsg = 'La hora de inicio no es válida';
                        } else if (resp.errors.includes('hora_fin_invalid')) {
                            errorMsg = 'La hora de fin no es válida';
                        } else if (resp.errors.includes('imagen_too_large')) {
                            errorMsg = 'La imagen es demasiado grande (máx. 2MB)';
                        } else if (resp.errors.includes('imagen_invalid_type')) {
                            errorMsg = 'Formato de imagen no válido (solo JPG/PNG)';
                        }
                    } else if (resp.message) {
                        errorMsg = resp.message;
                    }
                    showToast('error', '✗ ' + errorMsg);
                }
            })
            .catch(err => {
                console.error(err);
                showToast('error', '✗ Error de red al actualizar evento');
            });
    });
}

/**
 * Evento: Botón cancelar edición de evento
 * Cierra modal sin guardar cambios
 */
const btnCancelarEditarEvento = document.getElementById('btn-cancelar-editar-evento');
if (btnCancelarEditarEvento) {
    btnCancelarEditarEvento.addEventListener('click', (e) => {
        e.preventDefault();
        const modal = document.getElementById('modal-editar-evento');
        if (modal) {
            modal.classList.remove('active');
        }
    });
}

/**
 * Evento: Cuando el DOM está completamente cargado
 * Inicializa eventos, filtros, textareas y normalización de horas
 */
document.addEventListener('DOMContentLoaded', () => {
    // Cargar eventos desde la tabla HTML
    inicializarEventosDesdeTabla();

    // Aplicar filtro por defecto
    filtrarEventos('todos');

    // Configurar botones de filtro
    const btnsFiltroEvento = document.querySelectorAll('[data-filtro-evento]');
    btnsFiltroEvento.forEach(btn => {
        btn.addEventListener('click', () => {
            // Remover clase activa de todos los botones
            btnsFiltroEvento.forEach(b => b.classList.remove('filter-btn-active'));

            // Agregar clase activa al botón clickeado
            btn.classList.add('filter-btn-active');

            // Obtener y aplicar filtro
            const filtro = btn.getAttribute('data-filtro-evento');
            filtrarEventos(filtro);
        });
    });

    // Normalizar inputs de hora
    normalizarInputsHora();

    // Configurar textareas con auto-resize
    const textareas = document.querySelectorAll('#modal-crear-evento #descripcion, #modal-editar-evento #descripcion');
    textareas.forEach(textarea => {
        // Event listener para ajustar altura al escribir
        textarea.addEventListener('input', function () {
            this.style.height = 'auto';
            this.style.height = this.scrollHeight + 'px';
        });

        // Observer para ajustar altura cuando se abre modal
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