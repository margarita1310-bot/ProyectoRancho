/**
 * EliminarAdminJS
 * Script para gestionar la eliminación de registros (promociones, eventos, productos, reservas)
 * Muestra modal de confirmación y realiza peticiones AJAX para eliminar datos
 */

// ====== ELEMENTOS DEL DOM ======

// Overlay/modal de confirmación de eliminación
const deleteOverlay = document.getElementById('delete-overlay');

// Campo oculto para almacenar ID del registro a eliminar
const eliminarIdInput = document.getElementById('eliminar-id');

// Campo oculto para almacenar tipo de controlador
const eliminarControllerInput = document.getElementById('eliminar-controller');

// Campo oculto para almacenar acción a realizar
const eliminarActionInput = document.getElementById('eliminar-action');

// Título del modal de confirmación
const eliminarTitle = document.getElementById('eliminar-title');

// Mensaje del modal de confirmación
const eliminarMessage = document.getElementById('eliminar-message');

// Botón para confirmar la eliminación
const btnConfirmarEliminar = document.getElementById('btn-confirmar-eliminar');

// Botón para cancelar la eliminación
const btnCancelarEliminar = document.getElementById('btn-cancelar-eliminar');

/**
 * Abre el modal de confirmación de eliminación
 * Carga los datos del registro a eliminar en campos ocultos
 * Personaliza título y mensaje según opciones proporcionadas
 *
 * @param {number} id ID del registro a eliminar
 * @param {string} controller Tipo de controlador (Promocion, Evento, Producto, Reserva)
 * @param {string} action Acción a realizar (por defecto: 'eliminar')
 * @param {Object} opts Opciones personalizadas {title, message}
 * @return {void}
 */
function abrirEliminar(id, controller, action = 'eliminar', opts = {}) {
    // Cargar datos en campos ocultos del formulario
    if (eliminarIdInput) {
        eliminarIdInput.value = id || '';
    }

    if (eliminarControllerInput) {
        eliminarControllerInput.value = controller || '';
    }

    if (eliminarActionInput) {
        eliminarActionInput.value = action || 'eliminar';
    }

    // Establecer título personalizado o por defecto
    if (eliminarTitle) {
        eliminarTitle.textContent = opts.title || 'Eliminar elemento';
    }

    // Establecer mensaje personalizado o por defecto
    if (eliminarMessage) {
        eliminarMessage.textContent = opts.message || '¿Estás seguro de eliminar este elemento?';
    }

    // Mostrar el modal
    if (deleteOverlay) {
        deleteOverlay.classList.remove('d-none');
        deleteOverlay.classList.add('active');
    }
}

/**
 * Cierra el modal de confirmación de eliminación
 * Limpia los campos ocultos y remueve clases de visualización
 *
 * @return {void}
 */
function cerrarEliminar() {
    // Ocultar modal
    if (deleteOverlay) {
        deleteOverlay.classList.remove('active');
        deleteOverlay.classList.add('d-none');
    }

    // Limpiar campos del formulario
    if (eliminarIdInput) {
        eliminarIdInput.value = '';
    }

    if (eliminarControllerInput) {
        eliminarControllerInput.value = '';
    }

    if (eliminarActionInput) {
        eliminarActionInput.value = 'eliminar';
    }
}

/**
 * Evento: Asigna listeners a todos los botones de eliminación
 * Cada botón dispara abrirEliminar() con sus datos específicos
 */
document.querySelectorAll('.btn-eliminar').forEach(btn => {
    btn.addEventListener('click', () => {
        // Obtener datos del botón desde atributos data-*
        const id = btn.dataset.id;
        const controller = btn.dataset.controller || 'Promocion';
        const action = btn.dataset.action || 'eliminar';
        const title = btn.dataset.title || null;
        const message = btn.dataset.message || null;

        // Abrir modal de confirmación con datos extraídos
        abrirEliminar(id, controller, action, { title, message });
    });
});

/**
 * Evento: Botón Cancelar
 * Cierra el modal sin realizar eliminación
 */
if (btnCancelarEliminar) {
    btnCancelarEliminar.addEventListener('click', () => cerrarEliminar());
}

/**
 * Evento: Botón Confirmar eliminación
 * Realiza petición POST al servidor para eliminar el registro
 * Recarga datos según el tipo de controlador
 */
if (btnConfirmarEliminar) {
    btnConfirmarEliminar.addEventListener('click', e => {
        // Prevenir envío de formulario por defecto
        e.preventDefault();

        // Obtener datos del formulario oculto
        const id = eliminarIdInput.value;
        const controller = eliminarControllerInput.value || 'Promocion';
        const action = eliminarActionInput.value || 'eliminar';

        // Validar que se proporcionó un ID
        if (!id) {
            showToast('error', 'ID no proporcionado. No se puede eliminar.');
            cerrarEliminar();
            return;
        }

        // Crear FormData con el ID
        const data = new FormData();
        data.append('id', id);

        // Log de depuración
        console.log('Eliminando:', { id, controller, action });
        console.log('URL:', `../../../../app/controllers/${controller}Controller.php?action=${action}`);

        // Realizar petición AJAX para eliminar
        fetch(`../../../../app/controllers/${controller}Controller.php?action=${action}`, {
            method: 'POST',
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            body: data
        })
            .then(response => {
                // Log de depuración
                console.log('Response status:', response.status);

                // Obtener y parsear respuesta
                return response.text().then(text => {
                    console.log('Response text:', text);

                    // Intentar parsear como JSON
                    try {
                        return JSON.parse(text);
                    } catch (e) {
                        // Si falla el parseo, registrar errores
                        console.error('Error parsing JSON:', e);
                        console.error('Raw response:', text);
                        throw new Error('Respuesta no válida del servidor');
                    }
                });
            })
            .then(resp => {
                // Cerrar modal
                cerrarEliminar();

                // Procesar respuesta exitosa
                if (resp && resp.status === 'ok') {
                    // Personalizar mensaje según tipo de controlador
                    let mensaje = 'Elemento eliminado correctamente';
                    if (controller === 'Evento') {
                        mensaje = '✓ Evento eliminado correctamente';
                    } else if (controller === 'Producto') {
                        mensaje = '✓ Producto eliminado correctamente';
                    } else if (controller === 'Promocion') {
                        mensaje = '✓ Promoción eliminada correctamente';
                    } else if (controller === 'Reserva' || controller === 'Reservas') {
                        mensaje = '✓ Reserva eliminada correctamente';
                    }

                    // Mostrar notificación de éxito
                    showToast('success', mensaje);

                    // Recargar datos según tipo de controlador
                    if (controller === 'Producto' && typeof cargarProductos === 'function') {
                        // Recargar tabla de productos
                        cargarProductos();
                    } else if (controller === 'Promocion' && typeof cargarPromociones === 'function') {
                        // Recargar tabla de promociones
                        cargarPromociones();
                    } else if (controller === 'Evento' && typeof cargarEventos === 'function') {
                        // Recargar tabla de eventos
                        cargarEventos();
                    } else if ((controller === 'Reserva' || controller === 'Reservas') && typeof renderMesas === 'function') {
                        // Recargar mesas para reservas
                        const hoy = new Date().toISOString().slice(0, 10);
                        fetch(`/app/controllers/ReservaController.php?action=listar&fecha=${hoy}`, {
                            headers: { 'X-Requested-With': 'XMLHttpRequest' }
                        })
                            .then(response => response.json())
                            .then(data => {
                                window.reservasHoy = data || [];
                                renderMesas();
                            })
                            .catch(err => console.error('Error recargando mesas:', err));
                    } else {
                        // Si no hay función de recarga específica, recargar página
                        setTimeout(() => location.reload(), 1000);
                    }
                } else {
                    // Procesar respuesta de error
                    let errorMsg = 'Error al eliminar';

                    // Obtener mensaje de error personalizado
                    if (resp.message) {
                        errorMsg = resp.message;
                    } else if (resp.errors && Array.isArray(resp.errors)) {
                        // Mapear códigos de error a mensajes legibles
                        if (resp.errors.includes('id_required')) {
                            errorMsg = 'ID no proporcionado';
                        } else if (resp.errors.includes('not_found')) {
                            errorMsg = 'Elemento no encontrado';
                        } else if (resp.errors.includes('delete_failed')) {
                            errorMsg = 'No se pudo eliminar el elemento';
                        }
                    }

                    // Mostrar notificación de error
                    showToast('error', '✗ ' + errorMsg);
                }
            })
            .catch(err => {
                // Cerrar modal en caso de error
                cerrarEliminar();

                // Registrar error en consola
                console.error(err);

                // Mostrar notificación de error al usuario
                showToast('error', '✗ ' + (err.message || 'Ocurrió un error al eliminar'));
            });
    });
}
