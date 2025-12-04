/**
 * DisponibilidadAdminJS
 * Script para gestionar la disponibilidad de mesas por fecha
 * Incluye funciones para crear, editar, eliminar y cargar disponibilidades
 */

/**
 * Evento: Ejecutar cuando el DOM está completamente cargado
 * Inicializa todos los event listeners para los formularios y botones de disponibilidad
 */
document.addEventListener('DOMContentLoaded', () => {
    // Cargar disponibilidades existentes al iniciar
    cargarDisponibilidades();

    // Configurar event listener para formulario de creación
    const formDisp = document.getElementById('form-disponibilidad');
    if (formDisp) {
        formDisp.addEventListener('submit', crearDisponibilidad);
    }

    // Configurar event listener para botón cancelar (crear)
    const btnCancelar = document.getElementById('btn-cancelar-disponibilidad');
    if (btnCancelar) {
        btnCancelar.addEventListener('click', cerrarModal);
    }

    // Configurar event listener para formulario de edición
    const formEditarDisp = document.getElementById('form-editar-disponibilidad');
    if (formEditarDisp) {
        formEditarDisp.addEventListener('submit', actualizarDisponibilidad);
    }

    // Configurar event listener para botón cancelar (editar)
    const btnCancelarEditar = document.getElementById('btn-cancelar-editar-disponibilidad');
    if (btnCancelarEditar) {
        btnCancelarEditar.addEventListener('click', cerrarModalEditar);
    }
});

/**
 * Cierra el modal de creación de disponibilidad
 * Remueve la clase 'active' y resetea el formulario
 *
 * @return {void}
 */
function cerrarModal() {
    // Obtener el modal de creación
    const modal = document.getElementById('modal-crear-disponibilidad-mesas');
    if (modal) {
        // Remover clase active para ocultarlo
        modal.classList.remove('active');

        // Resetear el formulario
        const form = document.getElementById('form-disponibilidad');
        if (form) {
            form.reset();
        }
    }
}

/**
 * Carga todas las disponibilidades desde el servidor
 * Renderiza la tabla con las disponibilidades existentes
 * Muestra estado de disponibilidad y controles para editar/eliminar
 *
 * @async
 * @return {void}
 */
async function cargarDisponibilidades() {
    // Obtener el tbody de la tabla de disponibilidades
    const tbody = document.querySelector('#tabla-disponibilidad tbody');
    if (!tbody) {
        return;
    }

    try {
        // Realizar solicitud AJAX para obtener disponibilidades
        const response = await fetch('../../../../app/controllers/DisponibilidadController.php?action=listarTodas', {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        });

        // Validar que la respuesta sea exitosa
        if (!response.ok) {
            throw new Error('Error al cargar disponibilidades');
        }

        // Parsear respuesta JSON
        const disponibilidades = await response.json();

        // Limpiar la tabla
        tbody.innerHTML = '';

        // Mostrar mensaje si no hay disponibilidades
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

        // Iterar sobre cada disponibilidad y crear filas de tabla
        disponibilidades.forEach(disp => {
            // Crear nueva fila
            const tr = document.createElement('tr');

            // Formatear la fecha
            const fechaFormateada = formatearFecha(disp.fecha);

            // Determinar si tiene reservas
            const tieneReservas = disp.tiene_reservas;

            // Crear badge de estado
            const estado = tieneReservas
                ? '<span class="badge badge-con-reservas">Con reservas</span>'
                : '<span class="badge badge-disponible">Disponible</span>';

            // Construir contenido HTML de la fila
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

            // Agregar fila a la tabla
            tbody.appendChild(tr);
        });
    } catch (error) {
        // Registrar error en consola
        console.error('Error al cargar disponibilidades:', error);

        // Mostrar mensaje de error en la tabla
        tbody.innerHTML = `
            <tr>
                <td colspan="4" class="text-center p-4 text-danger">
                    Error al cargar las disponibilidades. Intenta recargar la página.
                </td>
            </tr>
        `;
    }
}

/**
 * Crea una nueva disponibilidad después de validaciones
 * Valida que la fecha no sea del pasado
 * Realiza petición POST al servidor y recarga la tabla
 *
 * @param {Event} e Evento del formulario
 * @async
 * @return {void}
 */
async function crearDisponibilidad(e) {
    // Prevenir envío de formulario por defecto
    e.preventDefault();

    // Obtener datos del formulario
    const formData = new FormData(e.target);
    const fecha = formData.get('fecha');
    const cantidad = formData.get('cantidad');

    // Obtener fecha actual (inicio del día)
    const hoy = new Date();
    hoy.setHours(0, 0, 0, 0);

    // Parsear fecha seleccionada
    const fechaSeleccionada = new Date(fecha + 'T00:00:00');

    // Validar que la fecha no sea del pasado
    if (fechaSeleccionada < hoy) {
        showToast('error', 'No puedes crear disponibilidad para fechas pasadas');
        return;
    }

    try {
        // Realizar solicitud POST para crear disponibilidad
        const response = await fetch('../../../../app/controllers/DisponibilidadController.php?action=guardar', {
            method: 'POST',
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            body: formData
        });

        // Parsear respuesta
        const resultado = await response.json();

        // Validar respuesta exitosa
        if (response.ok && resultado.status === 'ok') {
            showToast('success', 'Disponibilidad creada exitosamente');
            e.target.reset();
            cerrarModal();
            cargarDisponibilidades();
        } else if (response.status === 409) {
            // Error: Ya existen reservas para esta fecha
            showToast('error', resultado.detail || 'Ya existen reservas para esta fecha');
        } else {
            // Otros errores del servidor
            showToast('error', resultado.message || resultado.detail || 'Error al crear disponibilidad');
        }
    } catch (error) {
        // Registrar error en consola
        console.error('Error al crear disponibilidad:', error);
        showToast('error', 'Error al crear la disponibilidad: ' + error.message);
    }
}

/**
 * Elimina una disponibilidad después de confirmación del usuario
 * Valida confirmación antes de enviar solicitud DELETE
 * Recarga la tabla después de eliminación exitosa
 *
 * @param {number} id ID de la disponibilidad a eliminar
 * @param {string} fecha Fecha de la disponibilidad (para mostrar en confirmación)
 * @async
 * @return {void}
 */
async function eliminarDisponibilidad(id, fecha) {
    // Pedir confirmación al usuario
    if (!confirm(`¿Estás seguro de eliminar la disponibilidad del ${formatearFecha(fecha)}?`)) {
        return;
    }

    try {
        // Crear FormData con el ID
        const formData = new FormData();
        formData.append('id', id);

        // Realizar solicitud POST para eliminar
        const response = await fetch('../../../../app/controllers/DisponibilidadController.php?action=eliminar', {
            method: 'POST',
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            body: formData
        });

        // Parsear respuesta
        const resultado = await response.json();

        // Validar eliminación exitosa
        if (response.ok && resultado.status === 'ok') {
            showToast('success', 'Disponibilidad eliminada exitosamente');
            cargarDisponibilidades();
        } else {
            // Lanzar error si no fue exitoso
            throw new Error(resultado.message || 'Error al eliminar');
        }
    } catch (error) {
        // Registrar error en consola
        console.error('Error:', error);
        showToast('error', 'Error al eliminar la disponibilidad');
    }
}

/**
 * Abre el modal de edición con los datos de la disponibilidad
 * Carga los valores actuales en los campos del formulario
 *
 * @param {number} id ID de la disponibilidad a editar
 * @param {string} fecha Fecha de la disponibilidad
 * @param {number} cantidad Cantidad actual de mesas
 * @return {void}
 */
function editarDisponibilidad(id, fecha, cantidad) {
    // Obtener el modal de edición
    const modal = document.getElementById('modal-editar-disponibilidad-mesas');
    if (!modal) {
        return;
    }

    // Cargar datos en el formulario
    document.getElementById('edit-disp-id').value = id;
    document.getElementById('edit-disp-fecha').value = fecha;
    document.getElementById('edit-disp-fecha-display').value = formatearFecha(fecha);
    document.getElementById('edit-disp-cantidad').value = cantidad;

    // Mostrar el modal
    modal.classList.add('active');
}

/**
 * Actualiza una disponibilidad existente
 * Valida cambios y realiza petición POST al servidor
 * Recarga la tabla después de actualización exitosa
 *
 * @param {Event} e Evento del formulario
 * @async
 * @return {void}
 */
async function actualizarDisponibilidad(e) {
    // Prevenir envío de formulario por defecto
    e.preventDefault();

    // Obtener datos del formulario
    const formData = new FormData(e.target);
    const id = formData.get('id');
    const fecha = formData.get('fecha');
    const cantidad = formData.get('cantidad');

    try {
        // Realizar solicitud POST para actualizar
        const response = await fetch('../../../../app/controllers/DisponibilidadController.php?action=actualizar', {
            method: 'POST',
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            body: formData
        });

        // Parsear respuesta
        const resultado = await response.json();

        // Validar actualización exitosa
        if (response.ok && resultado.status === 'ok') {
            showToast('success', 'Disponibilidad actualizada exitosamente');
            cerrarModalEditar();
            cargarDisponibilidades();
        } else if (response.status === 409) {
            // Error: No se puede modificar porque hay reservas activas
            showToast('error', resultado.detail || 'No se puede modificar porque hay reservas activas');
        } else {
            // Otros errores del servidor
            showToast('error', resultado.message || resultado.detail || 'Error al actualizar disponibilidad');
        }
    } catch (error) {
        // Registrar error en consola
        console.error('Error al actualizar disponibilidad:', error);
        showToast('error', 'Error al actualizar la disponibilidad: ' + error.message);
    }
}

/**
 * Cierra el modal de edición de disponibilidad
 * Remueve la clase 'active' y resetea el formulario
 *
 * @return {void}
 */
function cerrarModalEditar() {
    // Obtener el modal de edición
    const modal = document.getElementById('modal-editar-disponibilidad-mesas');
    if (modal) {
        // Remover clase active para ocultarlo
        modal.classList.remove('active');

        // Resetear el formulario
        const form = document.getElementById('form-editar-disponibilidad');
        if (form) {
            form.reset();
        }
    }
}

/**
 * Formatea una fecha en formato legible para el usuario (es-MX)
 * Ejemplo: "2025-12-03" → "3 de diciembre de 2025"
 *
 * @param {string} fecha Fecha en formato YYYY-MM-DD
 * @return {string} Fecha formateada en español
 */
function formatearFecha(fecha) {
    // Opciones de formato para toLocaleDateString
    const opciones = { year: 'numeric', month: 'long', day: 'numeric' };

    // Crear objeto Date y formatear en español (México)
    return new Date(fecha + 'T00:00:00').toLocaleDateString('es-MX', opciones);
}
