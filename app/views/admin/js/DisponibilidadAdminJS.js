document.addEventListener('DOMContentLoaded', () => {
    cargarDisponibilidades();
    const formDisp = document.getElementById('form-disponibilidad');
    if (formDisp) {
        formDisp.addEventListener('submit', crearDisponibilidad);
    }
    const btnCancelar = document.getElementById('btn-cancelar-disponibilidad');
    if (btnCancelar) {
        btnCancelar.addEventListener('click', cerrarModal);
    }
    const formEditarDisp = document.getElementById('form-editar-disponibilidad');
    if (formEditarDisp) {
        formEditarDisp.addEventListener('submit', actualizarDisponibilidad);
    }
    const btnCancelarEditar = document.getElementById('btn-cancelar-editar-disponibilidad');
    if (btnCancelarEditar) {
        btnCancelarEditar.addEventListener('click', cerrarModalEditar);
    }
});
function cerrarModal() {
    const modal = document.getElementById('modal-crear-disponibilidad-mesas');
    if (modal) {
        modal.classList.remove('active');
        const form = document.getElementById('form-disponibilidad');
        if (form) form.reset();
    }
}
async function cargarDisponibilidades() {
    const tbody = document.querySelector('#tabla-disponibilidad tbody');
    if (!tbody) return;
    try {
        const response = await fetch('../../../../app/controllers/DisponibilidadController.php?action=listarTodas', {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        });
        if (!response.ok) throw new Error('Error al cargar disponibilidades');
        const disponibilidades = await response.json();
        tbody.innerHTML = '';
        if (disponibilidades.length === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="4" class="text-center p-4 text-muted">
                        No hay disponibilidades configuradas. Crea la primera usando el formulario.
                    </td>
                </tr>
            `;
            return;
        }
        disponibilidades.forEach(disp => {
            const tr = document.createElement('tr');
            const fechaFormateada = formatearFecha(disp.fecha);
            const tieneReservas = disp.tiene_reservas;
            const estado = tieneReservas 
                ? '<span class="badge badge-con-reservas">Con reservas</span>' 
                : '<span class="badge badge-disponible">Disponible</span>';
            tr.innerHTML = `
                <td>${fechaFormateada}</td>
                <td><span class="badge bg-primary">${disp.cantidad} mesas</span></td>
                <td>${estado}</td>
                <td class="text-center">
                    <div class="d-flex gap-1 justify-content-center flex-wrap">
                        <button class="btn btn-editar" 
                                onclick="editarDisponibilidad(${disp.id}, '${disp.fecha}', ${disp.cantidad})"
                                ${tieneReservas ? 'disabled' : ''}>
                            <i class="bi bi-pencil-square"></i>
                        </button>
                        <button class="btn btn-eliminar" 
                                onclick="eliminarDisponibilidad(${disp.id}, '${disp.fecha}')"
                                ${tieneReservas ? 'disabled' : ''}>
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>
                </td>
            `; 
            tbody.appendChild(tr);
        });
    } catch (error) {
        console.error('Error al cargar disponibilidades:', error);
        tbody.innerHTML = `
            <tr>
                <td colspan="4" class="text-center p-4 text-danger">
                    Error al cargar las disponibilidades. Intenta recargar la página.
                </td>
            </tr>
        `;
    }
}
async function crearDisponibilidad(e) {
    e.preventDefault();
    const formData = new FormData(e.target);
    const fecha = formData.get('fecha');
    const cantidad = formData.get('cantidad');
    const hoy = new Date();
    hoy.setHours(0, 0, 0, 0);
    const fechaSeleccionada = new Date(fecha + 'T00:00:00');
    if (fechaSeleccionada < hoy) {
        showToast('error', 'No puedes crear disponibilidad para fechas pasadas');
        return;
    }
    try {
        const response = await fetch('../../../../app/controllers/DisponibilidadController.php?action=guardar', {
            method: 'POST',
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            body: formData
        });
        const resultado = await response.json();
        if (response.ok && resultado.status === 'ok') {
            showToast('success', 'Disponibilidad creada exitosamente');
            e.target.reset();
            cerrarModal();
            cargarDisponibilidades();
        } else if (response.status === 409) {
            showToast('error', resultado.detail || 'Ya existen reservas para esta fecha');
        } else {
            showToast('error', resultado.message || resultado.detail || 'Error al crear disponibilidad');
        }
    } catch (error) {
        console.error('Error al crear disponibilidad:', error);
        showToast('error', 'Error al crear la disponibilidad: ' + error.message);
    }
}
async function eliminarDisponibilidad(id, fecha) {
    if (!confirm(`¿Estás seguro de eliminar la disponibilidad del ${formatearFecha(fecha)}?`)) {
        return;
    }
    try {
        const formData = new FormData();
        formData.append('id', id);
        const response = await fetch('../../../../app/controllers/DisponibilidadController.php?action=eliminar', {
            method: 'POST',
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            body: formData
        });
        const resultado = await response.json();
        if (response.ok && resultado.status === 'ok') {
            showToast('success', 'Disponibilidad eliminada exitosamente');
            cargarDisponibilidades();
        } else {
            throw new Error(resultado.message || 'Error al eliminar');
        }
        
    } catch (error) {
        console.error('Error:', error);
        showToast('error', 'Error al eliminar la disponibilidad');
    }
}
function editarDisponibilidad(id, fecha, cantidad) {
    const modal = document.getElementById('modal-editar-disponibilidad-mesas');
    if (!modal) return;
    document.getElementById('edit-disp-id').value = id;
    document.getElementById('edit-disp-fecha').value = fecha;
    document.getElementById('edit-disp-fecha-display').value = formatearFecha(fecha);
    document.getElementById('edit-disp-cantidad').value = cantidad;
    modal.classList.add('active');
}
async function actualizarDisponibilidad(e) {
    e.preventDefault();
    const formData = new FormData(e.target);
    const id = formData.get('id');
    const fecha = formData.get('fecha');
    const cantidad = formData.get('cantidad');
    try {
        const response = await fetch('../../../../app/controllers/DisponibilidadController.php?action=actualizar', {
            method: 'POST',
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            body: formData
        });
        const resultado = await response.json();
        if (response.ok && resultado.status === 'ok') {
            showToast('success', 'Disponibilidad actualizada exitosamente');
            cerrarModalEditar();
            cargarDisponibilidades();
        } else if (response.status === 409) {
            showToast('error', resultado.detail || 'No se puede modificar porque hay reservas activas');
        } else {
            showToast('error', resultado.message || resultado.detail || 'Error al actualizar disponibilidad');
        }
    } catch (error) {
        console.error('Error al actualizar disponibilidad:', error);
        showToast('error', 'Error al actualizar la disponibilidad: ' + error.message);
    }
}
function cerrarModalEditar() {
    const modal = document.getElementById('modal-editar-disponibilidad-mesas');
    if (modal) {
        modal.classList.remove('active');
        const form = document.getElementById('form-editar-disponibilidad');
        if (form) form.reset();
    }
}
function formatearFecha(fecha) {
    const opciones = { year: 'numeric', month: 'long', day: 'numeric' };
    return new Date(fecha + 'T00:00:00').toLocaleDateString('es-MX', opciones);
}