let todosLosProductos = [];
document.addEventListener('DOMContentLoaded', () => {
    inicializarProductosDesdeTabla();
    filtrarProductos('todos');
    const btnsFiltroProducto = document.querySelectorAll('[data-filtro-producto]');
    btnsFiltroProducto.forEach(btn => {
        btn.addEventListener('click', () => {
            btnsFiltroProducto.forEach(b => b.classList.remove('filter-btn-active'));
            btn.classList.add('filter-btn-active');
            const filtro = btn.getAttribute('data-filtro-producto');
            filtrarProductos(filtro);
        });
    });
});
function inicializarProductosDesdeTabla() {
    const tbody = document.querySelector('#menu tbody');
    if (!tbody) return;
    const filas = tbody.querySelectorAll('tr');
    todosLosProductos = [];
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
function filtrarProductos(filtro) {
    let productosFiltrados;
    if (filtro === 'todos') {
        productosFiltrados = todosLosProductos;
    } else {
        productosFiltrados = todosLosProductos.filter(p => 
            p.categoria && p.categoria === filtro
        );
    }
    renderizarProductos(productosFiltrados);
}
function renderizarProductos(productos) {
    const tbody = document.querySelector('#menu tbody');
    if (!tbody) return;  
    tbody.innerHTML = '';
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
    productos.forEach(p => {
        const tr = document.createElement('tr');
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
        tbody.appendChild(tr);
    });
    document.querySelectorAll('.btn-editar[data-controller="Producto"]').forEach(btn => {
        btn.addEventListener('click', () => {
            const id = btn.dataset.id;
            abrirEditar(id, 'Producto');
        });
    });
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
async function cargarProductos() {
    const tbody = document.querySelector('#menu tbody');
    if (!tbody) return;
    try {
        const response = await fetch('/app/controllers/ProductoController.php?action=index', {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        });
        if (!response.ok) throw new Error('Error al cargar productos');
        const productos = await response.json();
        todosLosProductos = productos;
        const btnActivo = document.querySelector('[data-filtro-producto].filter-btn-active');
        const filtroActual = btnActivo ? btnActivo.getAttribute('data-filtro-producto') : 'todos';
        filtrarProductos(filtroActual);
    } catch (error) {
        console.error('Error al cargar productos:', error);
        tbody.innerHTML = `
            <tr>
                <td colspan="4" class="text-center p-4 text-danger">
                    Error al cargar productos. Intenta recargar la página.
                </td>
            </tr>
        `;
    }
}
const btnGuardarProducto = document.getElementById("btn-guardar-producto");
if (btnGuardarProducto) btnGuardarProducto.onclick = (e) => {
    e.preventDefault();
    const modal = document.getElementById("modal-crear-producto");
    if (!modal) return;
    const nombre = modal.querySelector("#nombre") ? modal.querySelector("#nombre").value : '';
    const precio = modal.querySelector("#precio") ? modal.querySelector("#precio").value : '';
    const categoria = modal.querySelector("#categoria") ? modal.querySelector("#categoria").value : '';
    let data = new FormData();
    data.append("nombre", nombre);
    data.append("precio", precio);
    data.append("categoria", categoria);
    fetch("/app/controllers/ProductoController.php?action=guardar", { method: "POST", headers: { 'X-Requested-With': 'XMLHttpRequest' }, body: data})
    .then(parseResponse)
    .then(resp => {
        if (resp && resp.status === 'ok') {
            showToast('success', '✓ Producto creado exitosamente');
            const form = document.getElementById('form-crear-producto');
            if (form) form.reset();
            modal.classList.remove('active');
            cargarProductos();
        }
        else {
            let errorMsg = 'Error al crear producto';
            if (resp.message) errorMsg = resp.message;
            else if (resp.errors && Array.isArray(resp.errors)) {
                if (resp.errors.includes('nombre_required')) errorMsg = 'El nombre del producto es requerido';
                else if (resp.errors.includes('precio_invalid')) errorMsg = 'El precio no es válido';
                else if (resp.errors.includes('categoria_required')) errorMsg = 'La categoría es requerida';
            }
            showToast('error', '✗ ' + errorMsg);
        }
    })
    .catch(err => { console.error(err); showToast('error', '✗ Error de red al crear producto'); });
};
const btnCancelarProducto = document.getElementById('btn-cancelar-producto');
if (btnCancelarProducto) btnCancelarProducto.addEventListener('click', (e) => {
    e.preventDefault();
    const modal = document.getElementById('modal-crear-producto');
    if (modal) {
        modal.classList.remove('active');
    }
    const form = document.getElementById('form-crear-producto');
    if (form) form.reset();
});
const btnEditarProducto = document.getElementById("btn-editar-producto");
if (btnEditarProducto) btnEditarProducto.onclick = (e) => {
    e.preventDefault();
    const modal = document.getElementById("modal-editar-producto");
    if (!modal) return;
    const id = modal.querySelector("#id") ? modal.querySelector("#id").value : '';
    const nombre = modal.querySelector("#nombre") ? modal.querySelector("#nombre").value : '';
    const precio = modal.querySelector("#precio") ? modal.querySelector("#precio").value : '';
    const categoria = modal.querySelector("#categoria") ? modal.querySelector("#categoria").value : '';
    let data = new FormData();
    data.append("id", id);
    data.append("nombre", nombre);
    data.append("precio", precio);
    data.append("categoria", categoria);
    fetch("/app/controllers/ProductoController.php?action=actualizar", { method: "POST", headers: { 'X-Requested-With': 'XMLHttpRequest' }, body: data })
    .then(parseResponse)
    .then(resp => {
        if (resp && resp.status === 'ok') {
            showToast('success', '✓ Producto actualizado exitosamente');
            modal.classList.remove('active');
            cargarProductos();
        }
        else {
            let errorMsg = 'Error al actualizar producto';
            if (resp.message) errorMsg = resp.message;
            else if (resp.errors && Array.isArray(resp.errors)) {
                if (resp.errors.includes('id_required')) errorMsg = 'ID del producto no proporcionado';
                else if (resp.errors.includes('nombre_required')) errorMsg = 'El nombre del producto es requerido';
                else if (resp.errors.includes('precio_invalid')) errorMsg = 'El precio no es válido';
                else if (resp.errors.includes('categoria_required')) errorMsg = 'La categoría es requerida';
            }
            showToast('error', '✗ ' + errorMsg);
        }
    })
    .catch(err => { console.error(err); showToast('error', '✗ Error de red al actualizar producto'); });
};
const btnCancelarEditarProducto = document.getElementById('btn-cancelar-editar-producto');
if (btnCancelarEditarProducto) btnCancelarEditarProducto.addEventListener('click', (e) => {
    e.preventDefault();
    const modal = document.getElementById('modal-editar-producto');
    if (modal) {
        modal.classList.remove('active');
    }
});