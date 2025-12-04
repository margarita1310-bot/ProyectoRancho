/**
 * ProductoAdminJS
 * Script para gestionar productos (crear, editar, eliminar, filtrar)
 * Incluye carga de productos, renderización dinámica y validación de formularios
 */

// Variable global para almacenar todos los productos cargados
let todosLosProductos = [];

/**
 * Evento: Cuando el DOM está completamente cargado
 * Inicializa productos y configura event listeners
 */
document.addEventListener('DOMContentLoaded', () => {
    // Cargar productos desde tabla HTML
    inicializarProductosDesdeTabla();

    // Aplicar filtro por defecto
    filtrarProductos('todos');

    // Configurar botones de filtro
    const btnsFiltroProducto = document.querySelectorAll('[data-filtro-producto]');
    btnsFiltroProducto.forEach(btn => {
        btn.addEventListener('click', () => {
            // Remover clase activa de todos los botones
            btnsFiltroProducto.forEach(b => b.classList.remove('filter-btn-active'));

            // Agregar clase activa al botón clickeado
            btn.classList.add('filter-btn-active');

            // Obtener y aplicar filtro
            const filtro = btn.getAttribute('data-filtro-producto');
            filtrarProductos(filtro);
        });
    });
});

/**
 * Inicializa el array de productos extrayendo datos de la tabla HTML
 * Usado al cargar la página para llenar todosLosProductos
 *
 * @return {void}
 */
function inicializarProductosDesdeTabla() {
    // Obtener tbody
    const tbody = document.querySelector('#menu tbody');
    if (!tbody) {
        return;
    }

    // Obtener todas las filas de la tabla
    const filas = tbody.querySelectorAll('tr');
    todosLosProductos = [];

    // Iterar sobre filas y extraer datos
    filas.forEach(fila => {
        const celdas = fila.querySelectorAll('td');
        if (celdas.length >= 3) {
            const btnEditar = fila.querySelector('.btn-editar');
            if (btnEditar) {
                todosLosProductos.push({
                    id_producto: btnEditar.getAttribute('data-id'),
                    nombre: celdas[0].textContent.trim(),
                    precio: celdas[1].textContent.trim(),
                    categoria: celdas[2].textContent.trim()
                });
            }
        }
    });
}

/**
 * Filtra productos según criterio especificado
 * Soporta: todos, o por categoría específica
 *
 * @param {string} filtro Tipo de filtro a aplicar
 * @return {void}
 */
function filtrarProductos(filtro) {
    let productosFiltrados;

    // Aplicar filtro según criterio
    if (filtro === 'todos') {
        productosFiltrados = todosLosProductos;
    } else {
        // Filtrar por categoría
        productosFiltrados = todosLosProductos.filter(p => p.categoria && p.categoria === filtro);
    }

    // Renderizar productos filtrados
    renderizarProductos(productosFiltrados);
}

/**
 * Renderiza productos en la tabla HTML
 * Crea filas con datos y botones de edición/eliminación
 *
 * @param {Array} productos Array de productos a renderizar
 * @return {void}
 */
function renderizarProductos(productos) {
    // Obtener tbody
    const tbody = document.querySelector('#menu tbody');
    if (!tbody) {
        return;
    }

    // Limpiar tabla
    tbody.innerHTML = '';

    // Mostrar mensaje si no hay productos
    if (productos.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="4" class="text-center p-4 text-muted">
                    No hay productos para este filtro
                </td>
            </tr>
        `;
        return;
    }

    // Iterar sobre productos y crear filas
    productos.forEach(p => {
        const tr = document.createElement('tr');

        // Construir HTML de la fila
        tr.innerHTML = `
            <td>${p.nombre}</td>
            <td>${p.precio}</td>
            <td>${p.categoria}</td>
            <td class="text-center">
                <div class="d-flex gap-1 justify-content-center flex-wrap">
                    <button class="btn btn-editar" data-id="${p.id_producto}" data-controller="Producto">
                        <i class="bi bi-pencil-square"></i>
                    </button>
                    <button class="btn btn-eliminar" data-id="${p.id_producto}" data-controller="Producto">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>
            </td>
        `;

        // Agregar fila a tabla
        tbody.appendChild(tr);
    });

    // Asignar listeners a botones de edición
    document.querySelectorAll('.btn-editar[data-controller="Producto"]').forEach(btn => {
        btn.addEventListener('click', () => {
            const id = btn.dataset.id;
            abrirEditar(id, 'Producto');
        });
    });

    // Asignar listeners a botones de eliminación
    document.querySelectorAll('.btn-eliminar[data-controller="Producto"]').forEach(btn => {
        btn.addEventListener('click', () => {
            const id = btn.dataset.id;
            abrirEliminar(id, 'Producto', 'eliminar', {
                title: 'Eliminar producto',
                message: '¿Estás seguro de eliminar este producto?'
            });
        });
    });
}

/**
 * Carga productos desde el servidor y recarga la tabla
 * Mantiene el filtro activo después de cargar
 *
 * @async
 * @return {void}
 */
async function cargarProductos() {
    // Obtener tbody
    const tbody = document.querySelector('#menu tbody');
    if (!tbody) {
        return;
    }

    try {
        // Realizar solicitud AJAX
        const response = await fetch('/app/controllers/ProductoController.php?action=index', {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        });

        // Validar respuesta
        if (!response.ok) {
            throw new Error('Error al cargar productos');
        }

        // Parsear JSON
        const productos = await response.json();
        todosLosProductos = productos;

        // Obtener filtro activo
        const btnActivo = document.querySelector('[data-filtro-producto].filter-btn-active');
        const filtroActual = btnActivo ? btnActivo.getAttribute('data-filtro-producto') : 'todos';

        // Aplicar filtro
        filtrarProductos(filtroActual);
    } catch (error) {
        // Registrar error
        console.error('Error al cargar productos:', error);

        // Mostrar mensaje de error
        tbody.innerHTML = `
            <tr>
                <td colspan="4" class="text-center p-4 text-danger">
                    Error al cargar productos. Intenta recargar la página.
                </td>
            </tr>
        `;
    }
}

/**
 * Evento: Botón guardar producto
 * Valida datos y realiza petición POST para crear producto
 */
const btnGuardarProducto = document.getElementById('btn-guardar-producto');
if (btnGuardarProducto) {
    btnGuardarProducto.addEventListener('click', (e) => {
        e.preventDefault();

        // Obtener modal
        const modal = document.getElementById('modal-crear-producto');
        if (!modal) {
            return;
        }

        // Obtener valores del formulario
        const nombre = modal.querySelector('#nombre') ? modal.querySelector('#nombre').value : '';
        const precio = modal.querySelector('#precio') ? modal.querySelector('#precio').value : '';
        const categoria = modal.querySelector('#categoria') ? modal.querySelector('#categoria').value : '';

        // Crear FormData
        let data = new FormData();
        data.append('nombre', nombre);
        data.append('precio', precio);
        data.append('categoria', categoria);

        // Realizar petición POST
        fetch('/app/controllers/ProductoController.php?action=guardar', {
            method: 'POST',
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            body: data
        })
            .then(parseResponse)
            .then(resp => {
                // Validar respuesta exitosa
                if (resp && resp.status === 'ok') {
                    showToast('success', '✓ Producto creado exitosamente');
                    const form = document.getElementById('form-crear-producto');
                    if (form) {
                        form.reset();
                    }
                    modal.classList.remove('active');
                    cargarProductos();
                } else {
                    // Procesar errores
                    let errorMsg = 'Error al crear producto';
                    if (resp.message) {
                        errorMsg = resp.message;
                    } else if (resp.errors && Array.isArray(resp.errors)) {
                        if (resp.errors.includes('nombre_required')) {
                            errorMsg = 'El nombre del producto es requerido';
                        } else if (resp.errors.includes('precio_invalid')) {
                            errorMsg = 'El precio no es válido';
                        } else if (resp.errors.includes('categoria_required')) {
                            errorMsg = 'La categoría es requerida';
                        }
                    }
                    showToast('error', '✗ ' + errorMsg);
                }
            })
            .catch(err => {
                console.error(err);
                showToast('error', '✗ Error de red al crear producto');
            });
    });
}

/**
 * Evento: Botón cancelar creación de producto
 * Cierra modal y limpia formulario
 */
const btnCancelarProducto = document.getElementById('btn-cancelar-producto');
if (btnCancelarProducto) {
    btnCancelarProducto.addEventListener('click', (e) => {
        e.preventDefault();

        // Cerrar modal
        const modal = document.getElementById('modal-crear-producto');
        if (modal) {
            modal.classList.remove('active');
        }

        // Limpiar formulario
        const form = document.getElementById('form-crear-producto');
        if (form) {
            form.reset();
        }
    });
}

/**
 * Evento: Botón editar producto
 * Valida datos y realiza petición POST para actualizar producto
 */
const btnEditarProducto = document.getElementById('btn-editar-producto');
if (btnEditarProducto) {
    btnEditarProducto.addEventListener('click', (e) => {
        e.preventDefault();

        // Obtener modal
        const modal = document.getElementById('modal-editar-producto');
        if (!modal) {
            return;
        }

        // Obtener valores del formulario
        const id = modal.querySelector('#id') ? modal.querySelector('#id').value : '';
        const nombre = modal.querySelector('#nombre') ? modal.querySelector('#nombre').value : '';
        const precio = modal.querySelector('#precio') ? modal.querySelector('#precio').value : '';
        const categoria = modal.querySelector('#categoria') ? modal.querySelector('#categoria').value : '';

        // Crear FormData
        let data = new FormData();
        data.append('id', id);
        data.append('nombre', nombre);
        data.append('precio', precio);
        data.append('categoria', categoria);

        // Realizar petición POST
        fetch('/app/controllers/ProductoController.php?action=actualizar', {
            method: 'POST',
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            body: data
        })
            .then(parseResponse)
            .then(resp => {
                // Validar respuesta exitosa
                if (resp && resp.status === 'ok') {
                    showToast('success', '✓ Producto actualizado exitosamente');
                    modal.classList.remove('active');
                    cargarProductos();
                } else {
                    // Procesar errores
                    let errorMsg = 'Error al actualizar producto';
                    if (resp.message) {
                        errorMsg = resp.message;
                    } else if (resp.errors && Array.isArray(resp.errors)) {
                        if (resp.errors.includes('id_required')) {
                            errorMsg = 'ID del producto no proporcionado';
                        } else if (resp.errors.includes('nombre_required')) {
                            errorMsg = 'El nombre del producto es requerido';
                        } else if (resp.errors.includes('precio_invalid')) {
                            errorMsg = 'El precio no es válido';
                        } else if (resp.errors.includes('categoria_required')) {
                            errorMsg = 'La categoría es requerida';
                        }
                    }
                    showToast('error', '✗ ' + errorMsg);
                }
            })
            .catch(err => {
                console.error(err);
                showToast('error', '✗ Error de red al actualizar producto');
            });
    });
}

/**
 * Evento: Botón cancelar edición de producto
 * Cierra modal sin guardar cambios
 */
const btnCancelarEditarProducto = document.getElementById('btn-cancelar-editar-producto');
if (btnCancelarEditarProducto) {
    btnCancelarEditarProducto.addEventListener('click', (e) => {
        e.preventDefault();

        // Cerrar modal
        const modal = document.getElementById('modal-editar-producto');
        if (modal) {
            modal.classList.remove('active');
        }
    });
}