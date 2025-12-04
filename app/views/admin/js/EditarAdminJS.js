/**
 * EditarAdminJS
 * Script para gestionar la edición de promociones, eventos y productos
 * Abre modales con datos precargados y maneja formularios de edición
 */

/**
 * Abre el modal de edición con datos del registro seleccionado
 * Realiza petición AJAX para obtener los datos del servidor
 * Carga datos en los campos del formulario según el tipo de controlador
 *
 * @param {number} id ID del registro a editar
 * @param {string} controller Tipo de controlador (Promocion, Evento, Producto)
 * @return {void}
 */
function abrirEditar(id, controller) {
    // Validar que se proporcionaron ID y controlador
    if (!id || !controller) {
        return;
    }

    // Crear FormData con el ID del registro
    const data = new FormData();
    data.append("id", id);

    // Realizar solicitud AJAX para obtener datos del registro
    fetch(`/app/controllers/${controller}Controller.php?action=obtener`, {
        method: "POST",
        headers: { 'X-Requested-With': 'XMLHttpRequest' },
        body: data
    })
        .then(async response => {
            // Obtener texto de respuesta para depuración
            const text = await response.text();
            console.log('Response status:', response.status);
            console.log('Response text:', text);

            // Intentar parsear JSON
            try {
                return JSON.parse(text);
            } catch (e) {
                // Si no es JSON válido, registrar error
                console.error('Error parsing JSON:', e);
                throw new Error('Respuesta no válida del servidor: ' + text.substring(0, 200));
            }
        })
        .then(p => {
            // Procesar según el tipo de controlador
            if (controller === 'Promocion') {
                // Obtener modal de edición de promoción
                const modal = document.querySelector("#modal-editar-promocion");
                if (!modal) {
                    return;
                }

                // Mostrar modal
                modal.classList.add("active");
                modal.classList.remove("d-none");

                // Cargar datos básicos en el formulario
                modal.querySelector("#id").value = p.id_promocion;
                modal.querySelector("#nombre").value = p.nombre;

                // Cargar descripción con ajuste automático de altura
                const descripcionTextarea = modal.querySelector("#descripcion");
                if (descripcionTextarea) {
                    descripcionTextarea.value = p.descripcion || '';
                    descripcionTextarea.style.height = 'auto';
                    descripcionTextarea.style.height = descripcionTextarea.scrollHeight + 'px';
                }

                // Cargar fechas de inicio y fin
                modal.querySelector("#fechaInicio").value = p.fecha_inicio;
                modal.querySelector("#fechaFin").value = p.fecha_fin;

                // Cargar estado
                const estadoSelect = modal.querySelector("#estado");
                if (estadoSelect) {
                    estadoSelect.value = p.estado || '';
                }

                // Cargar productos seleccionados
                const selectProductos = modal.querySelector("#productos");
                const idsSeleccionados = Array.isArray(p.productos) ? p.productos.map(String) : [];

                // Función para preseleccionar productos
                const aplicarPreseleccion = () => {
                    if (!selectProductos) {
                        return;
                    }

                    // Crear Set con IDs para búsqueda rápida
                    const setIds = new Set(idsSeleccionados);

                    // Marcar opciones seleccionadas
                    Array.from(selectProductos.options).forEach(option => {
                        option.selected = setIds.has(String(option.value));
                    });
                };

                // Aplicar preselección o cargar productos si es necesario
                if (selectProductos) {
                    if (selectProductos.options.length === 0 && typeof cargarProductosEnSelect === 'function') {
                        // Si no hay opciones, intentar cargar productos
                        Promise.resolve(cargarProductosEnSelect()).then(() => {
                            aplicarPreseleccion();
                        }).catch(() => {
                            // Ignorar errores en carga de productos
                        });
                    } else {
                        // Aplicar preselección directamente
                        aplicarPreseleccion();
                    }
                }

                // Limpiar campo de imagen
                const imagenInput = modal.querySelector("#imagen");
                if (imagenInput) {
                    imagenInput.value = '';
                }
            } else if (controller === 'Evento') {
                // Obtener modal de edición de evento
                const modal = document.querySelector("#modal-editar-evento");
                if (!modal) {
                    return;
                }

                // Mostrar modal
                modal.classList.add("active");
                modal.classList.remove("d-none");

                // Cargar datos básicos en el formulario
                modal.querySelector("#id").value = p.id_evento;
                modal.querySelector("#nombre").value = p.nombre;

                // Cargar descripción con ajuste automático de altura
                const descripcionTextarea = modal.querySelector("#descripcion");
                if (descripcionTextarea) {
                    descripcionTextarea.value = p.descripcion || '';
                    descripcionTextarea.style.height = 'auto';
                    descripcionTextarea.style.height = descripcionTextarea.scrollHeight + 'px';
                }

                // Cargar fecha del evento
                modal.querySelector("#fecha").value = p.fecha;

                // Cargar horas de inicio y fin
                modal.querySelector("#horaInicio").value = p.hora_inicio;
                modal.querySelector("#horaFin").value = p.hora_fin;

                // Mostrar/ocultar indicador de imagen actual
                const imagenInfo = modal.querySelector("#imagen-actual-info");
                if (imagenInfo && p.tiene_imagen) {
                    imagenInfo.classList.remove('d-none');
                } else if (imagenInfo) {
                    imagenInfo.classList.add('d-none');
                }

                // Limpiar campo de imagen
                const imagenInput = modal.querySelector("#imagen");
                if (imagenInput) {
                    imagenInput.value = '';
                }
            } else if (controller === 'Producto') {
                // Obtener modal de edición de producto
                const modal = document.querySelector("#modal-editar-producto");
                if (!modal) {
                    return;
                }

                // Mostrar modal
                modal.classList.add("active");
                modal.classList.remove("d-none");

                // Cargar datos del producto
                modal.querySelector("#id").value = p.id_producto;
                modal.querySelector("#nombre").value = p.nombre;
                modal.querySelector("#precio").value = p.precio;

                // Cargar categoría
                const categoriaSelect = modal.querySelector("#categoria");
                if (categoriaSelect) {
                    categoriaSelect.value = p.categoria || '';
                }
            }
        })
        .catch(err => {
            // Registrar error en consola
            console.error('Error completo al obtener registro:', err);

            // Mostrar mensaje de error al usuario
            showToast('error', 'No se pudo cargar el registro: ' + err.message);
        });
}

/**
 * Evento: Ejecutar cuando el DOM está completamente cargado
 * Asigna event listeners a todos los botones de edición
 */
document.addEventListener('DOMContentLoaded', function () {
    // Obtener todos los botones de edición
    document.querySelectorAll(".btn-editar").forEach(btn => {
        // Asignar evento click a cada botón
        btn.onclick = () => {
            // Obtener ID del registro desde atributo data
            const id = btn.dataset.id;

            // Obtener tipo de controlador (por defecto Promocion)
            const controller = btn.dataset.controller || 'Promocion';

            // Abrir modal de edición
            abrirEditar(id, controller);
        };
    });
});
