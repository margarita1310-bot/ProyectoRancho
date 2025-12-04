let todasLasPromociones = [];
document.addEventListener('DOMContentLoaded', () => {
    inicializarPromocionesDesdeTabla();
    filtrarPromociones('todas');
    cargarProductosEnSelect();
    const btnsFiltroPromocion = document.querySelectorAll('[data-filtro-promocion]');
    btnsFiltroPromocion.forEach(btn => {
        btn.addEventListener('click', () => {
            btnsFiltroPromocion.forEach(b => b.classList.remove('filter-btn-active'));
            btn.classList.add('filter-btn-active');
            const filtro = btn.getAttribute('data-filtro-promocion');
            filtrarPromociones(filtro);
        });
    });
    const textareas = document.querySelectorAll('#modal-crear-promocion #descripcion, #modal-editar-promocion #descripcion');
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
function inicializarPromocionesDesdeTabla() {
    const tbody = document.querySelector('#promocion tbody');
    if (!tbody) return;
    
    const filas = tbody.querySelectorAll('tr');
    todasLasPromociones = [];
    
    filas.forEach(fila => {
        const celdas = fila.querySelectorAll('td');
        if (celdas.length >= 7) {
            const btnEditar = fila.querySelector('.btn-editar');
            if (btnEditar) {
                // Extraer productos_nombres de la celda de productos (índice 2)
                const productosCell = celdas[2];
                let productos_nombres = '';
                if (productosCell) {
                    const small = productosCell.querySelector('small');
                    if (small && !small.classList.contains('text-muted')) {
                        productos_nombres = small.textContent.trim();
                    }
                }
                
                todasLasPromociones.push({
                    id_promocion: btnEditar.getAttribute('data-id'),
                    nombre: celdas[0].textContent.trim(),
                    descripcion: celdas[1].textContent.trim(),
                    productos_nombres: productos_nombres,
                    fecha_inicio: celdas[3].textContent.trim(),
                    fecha_fin: celdas[4].textContent.trim(),
                    estado: celdas[5].textContent.trim()
                });
            }
        }
    });
}

/**
 * Filtrar promociones según criterio
 */
function filtrarPromociones(filtro) {
    let promocionesFiltradas;
    
    if (filtro === 'todas') {
        promocionesFiltradas = todasLasPromociones;
    } else {
        promocionesFiltradas = todasLasPromociones.filter(p => 
            p.estado && p.estado === filtro
        );
    }
    
    renderizarPromociones(promocionesFiltradas);
}

/**
 * Renderizar tabla de promociones
 */
function renderizarPromociones(promociones) {
    const tbody = document.querySelector('#promocion tbody');
    if (!tbody) return;
    
    tbody.innerHTML = '';
    
    if (promociones.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="7" class="text-center p-4 text-muted">
                    No hay promociones para este filtro
                </td>
            </tr>
        `;
        return;
    }
    
    promociones.forEach(pr => {
        const productosText = pr.productos_nombres ? 
            `<small>${pr.productos_nombres}</small>` : 
            '<small class="text-muted">Sin productos</small>';
            
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td>${pr.nombre}</td>
            <td>${pr.descripcion}</td>
            <td>${productosText}</td>
            <td>${pr.fecha_inicio}</td>
            <td>${pr.fecha_fin}</td>
            <td>${pr.estado}</td>
            <td class="text-center">
                <div class="d-flex gap-1 justify-content-center flex-wrap">
                    <button class="btn btn-editar" data-id="${pr.id_promocion}" data-controller="Promocion">
                        <i class="bi bi-pencil-square"></i>
                    </button>
                    <button class="btn btn-eliminar" data-id="${pr.id_promocion}" data-controller="Promocion">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>
            </td>
        `;
        tbody.appendChild(tr);
    });
    
    // Reactivar event listeners
    document.querySelectorAll('.btn-editar[data-controller="Promocion"]').forEach(btn => {
        btn.addEventListener('click', () => {
            abrirEditar(btn.dataset.id, 'Promocion');
        });
    });
    
    document.querySelectorAll('.btn-eliminar[data-controller="Promocion"]').forEach(btn => {
        btn.addEventListener('click', () => {
            abrirEliminar(btn.dataset.id, 'Promocion', 'eliminar', {
                title: 'Eliminar promoción',
                message: '¿Estás seguro de eliminar esta promoción?'
            });
        });
    });
}

/**
 * Recarga dinámicamente la tabla de promociones
 */
async function cargarPromociones() {
    const tbody = document.querySelector('#promocion tbody');
    if (!tbody) return;
    
    try {
        const response = await fetch('/app/controllers/PromocionController.php?action=index', {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        });
        
        if (!response.ok) throw new Error('Error al cargar promociones');
        
        const promociones = await response.json();
        
        // Guardar todas las promociones
        todasLasPromociones = promociones;
        
        // Renderizar con el filtro actual
        const btnActivo = document.querySelector('[data-filtro-promocion].filter-btn-active');
        const filtroActual = btnActivo ? btnActivo.getAttribute('data-filtro-promocion') : 'todas';
        filtrarPromociones(filtroActual);
        
    } catch (error) {
        console.error('Error al cargar promociones:', error);
        tbody.innerHTML = `
            <tr>
                <td colspan="7" class="text-center p-4 text-danger">
                    Error al cargar promociones. Intenta recargar la página.
                </td>
            </tr>
        `;
    }
}

/**
 * Cargar productos en los selectores de ambos modales
 */
async function cargarProductosEnSelect() {
    try {
        const response = await fetch('/app/controllers/PromocionController.php?action=getProductos', {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        });
        
        if (!response.ok) throw new Error('Error al cargar productos');
        
        const productos = await response.json();
        
        // Llenar selector del modal crear
        const selectCrear = document.querySelector('#modal-crear-promocion #productos');
        if (selectCrear) {
            selectCrear.innerHTML = productos.map(p => 
                `<option value="${p.id_producto}">${p.nombre} - ${p.categoria}</option>`
            ).join('');
        }
        
        // Llenar selector del modal editar
        const selectEditar = document.querySelector('#modal-editar-promocion #productos');
        if (selectEditar) {
            selectEditar.innerHTML = productos.map(p => 
                `<option value="${p.id_producto}">${p.nombre} - ${p.categoria}</option>`
            ).join('');
        }
        
    } catch (error) {
        console.error('Error al cargar productos:', error);
    }
}
function getProductosSeleccionados(modalId) {
    const select = document.querySelector(`#${modalId} #productos`);
    if (!select) return [];
    const selected = Array.from(select.selectedOptions);
    return selected.map(option => option.value);
}
const btnGuardarPromocion = document.getElementById('btn-guardar-promocion');
if (btnGuardarPromocion) btnGuardarPromocion.addEventListener('click', (e) => {
    e.preventDefault();
    const modal = document.getElementById('modal-crear-promocion');
    if (!modal) return;
    const nombre = modal.querySelector('#nombre') ? modal.querySelector('#nombre').value.trim() : '';
    const descripcion = modal.querySelector('#descripcion') ? modal.querySelector('#descripcion').value.trim() : '';
    const fechaInicio = modal.querySelector('#fechaInicio') ? modal.querySelector('#fechaInicio').value : '';
    const fechaFin = modal.querySelector('#fechaFin') ? modal.querySelector('#fechaFin').value : '';
    const estado = modal.querySelector('#estado') ? modal.querySelector('#estado').value : '';
    const imagenEl = modal.querySelector('#imagen');
    const productosSeleccionados = getProductosSeleccionados('modal-crear-promocion');
    if (!nombre) {
        showToast('error', '✗ El nombre es requerido');
        return;
    }
    if (!descripcion) {
        showToast('error', '✗ La descripción es requerida');
        return;
    }
    if (!estado) {
        showToast('error', '✗ Debes seleccionar un estado');
        return;
    }
    let data = new FormData();
    data.append('nombre', nombre);
    data.append('descripcion', descripcion);
    data.append('fecha_inicio', fechaInicio);
    data.append('fecha_fin', fechaFin);
    data.append('estado', estado);
    productosSeleccionados.forEach(id => {
        data.append('productos[]', id);
    });
    if (imagenEl && imagenEl.files && imagenEl.files[0]) {
        data.append('imagen', imagenEl.files[0]);
    }
    fetch('/app/controllers/PromocionController.php?action=guardar', { method: 'POST', headers: { 'X-Requested-With': 'XMLHttpRequest' }, body: data })
        .then(async response => {
            const text = await response.text();
            console.log('Response status:', response.status);
            console.log('Response text:', text);
            try {
                return JSON.parse(text);
            } catch (e) {
                console.error('Error parsing JSON:', e);
                throw new Error('Respuesta no válida del servidor: ' + text.substring(0, 100));
            }
        })
        .then(resp => {
            if (resp && resp.status === 'ok') {
                showToast('success', '✓ Promoción creada exitosamente');
                const form = document.getElementById('form-crear-promocion');
                if (form) form.reset();
                modal.classList.remove('active');
                cargarPromociones();
            }
            else {
                let errorMsg = 'Error al crear promoción';
                if (resp.message) errorMsg = resp.message;
                else if (resp.errors && Array.isArray(resp.errors)) {
                    if (resp.errors.includes('nombre_required')) errorMsg = 'El nombre de la promoción es requerido';
                    else if (resp.errors.includes('descripcion_required')) errorMsg = 'La descripción es requerida';
                    else if (resp.errors.includes('estado_required')) errorMsg = 'El estado es requerido';
                    else if (resp.errors.includes('imagen_too_large')) errorMsg = 'La imagen es demasiado grande (máx. 2MB)';
                    else if (resp.errors.includes('imagen_invalid_type')) errorMsg = 'Formato de imagen no válido (solo JPG/PNG)';
                    else errorMsg = resp.errors.join(', ');
                }
                showToast('error','✗ ' + errorMsg);
            }
        })
        .catch(err => { 
            console.error('Error completo:', err); 
            showToast('error','✗ Error: ' + err.message); 
        });
});
const btnCancelarPromocion = document.getElementById('btn-cancelar-promocion');
if (btnCancelarPromocion) btnCancelarPromocion.addEventListener('click', (e) => {
    e.preventDefault();
    const modal = document.getElementById('modal-crear-promocion');
    if (modal) {
        modal.classList.remove('active');
    }
    const form = document.getElementById('form-crear-promocion');
    if (form) form.reset();
});
const btnEditarPromocion = document.getElementById('btn-editar-promocion');
if (btnEditarPromocion) btnEditarPromocion.addEventListener('click', (e) => {
    e.preventDefault();
    const modal = document.getElementById('modal-editar-promocion');
    if (!modal) return;
    const id = modal.querySelector('#id') ? modal.querySelector('#id').value : '';
    const nombre = modal.querySelector('#nombre') ? modal.querySelector('#nombre').value : '';
    const descripcion = modal.querySelector('#descripcion') ? modal.querySelector('#descripcion').value : '';
    const fechaInicio = modal.querySelector('#fechaInicio') ? modal.querySelector('#fechaInicio').value : '';
    const fechaFin = modal.querySelector('#fechaFin') ? modal.querySelector('#fechaFin').value : '';
    const estado = modal.querySelector('#estado') ? modal.querySelector('#estado').value : '';
    const imagenEl = modal.querySelector('#imagen');
    const productosSeleccionados = getProductosSeleccionados('modal-editar-promocion');
    let data = new FormData();
    data.append('id', id);
    data.append('nombre', nombre);
    data.append('descripcion', descripcion);
    data.append('fecha_inicio', fechaInicio);
    data.append('fecha_fin', fechaFin);
    data.append('estado', estado);
    productosSeleccionados.forEach(id => {
        data.append('productos[]', id);
    });
    
    if (imagenEl && imagenEl.files && imagenEl.files[0]) {
        data.append('imagen', imagenEl.files[0]);
    }

    fetch('/app/controllers/PromocionController.php?action=actualizar', { method: 'POST', headers: { 'X-Requested-With': 'XMLHttpRequest' }, body: data })
        .then(parseResponse)
        .then(resp => {
            if (resp && resp.status === 'ok') {
                showToast('success', '✓ Promoción actualizada exitosamente');
                modal.classList.remove('active');
                cargarPromociones();
            }
            else {
                let errorMsg = 'Error al actualizar promoción';
                if (resp.message) errorMsg = resp.message;
                else if (resp.errors && Array.isArray(resp.errors)) {
                    if (resp.errors.includes('id_required')) errorMsg = 'ID de la promoción no proporcionado';
                    else if (resp.errors.includes('nombre_required')) errorMsg = 'El nombre de la promoción es requerido';
                    else if (resp.errors.includes('descripcion_required')) errorMsg = 'La descripción es requerida';
                    else if (resp.errors.includes('estado_required')) errorMsg = 'El estado es requerido';
                    else if (resp.errors.includes('imagen_too_large')) errorMsg = 'La imagen es demasiado grande (máx. 2MB)';
                    else if (resp.errors.includes('imagen_invalid_type')) errorMsg = 'Formato de imagen no válido (solo JPG/PNG)';
                }
                showToast('error','✗ ' + errorMsg);
            }
        })
        .catch(err => { console.error(err); showToast('error','✗ Error de red al actualizar promoción'); });
});

// Cancelar editar promoción
const btnCancelarEditarPromocion = document.getElementById('btn-cancelar-editar-promocion');
if (btnCancelarEditarPromocion) btnCancelarEditarPromocion.addEventListener('click', (e) => {
    e.preventDefault();
    const modal = document.getElementById('modal-editar-promocion');
    if (modal) {
        modal.classList.remove('active');
    }
});
