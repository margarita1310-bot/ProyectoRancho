 /*
 * Producto: crear / editar
 */
// Crear nuevo producto
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

    fetch("ProductoController.php?action=guardar", { method: "POST", headers: { 'X-Requested-With': 'XMLHttpRequest' }, body: data})
    .then(parseResponse)
    .then(resp => {
        if (resp && resp.status === 'ok') location.reload();
        else showToast('error', 'Error al crear producto: ' + (resp.message || JSON.stringify(resp)));
    })
    .catch(err => { console.error(err); showToast('error', 'Error de red al crear producto'); });
};

// Cancelar crear producto
const btnCancelarProducto = document.getElementById('btn-cancelar-producto');
if (btnCancelarProducto) btnCancelarProducto.addEventListener('click', (e) => {
    e.preventDefault();
    const modal = document.getElementById('modal-crear-producto');
    if (modal) {
        modal.classList.remove('active');
        modal.classList.add('d-none');
    }
    const form = document.getElementById('form-crear-producto');
    if (form) form.reset();
});

// Editar producto existente
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

    fetch("ProductoController.php?action=actualizar", { method: "POST", headers: { 'X-Requested-With': 'XMLHttpRequest' }, body: data })
    .then(parseResponse)
    .then(resp => {
        if (resp && resp.status === 'ok') location.reload();
        else showToast('error', 'Error al actualizar producto: ' + (resp.message || JSON.stringify(resp)));
    })
    .catch(err => { console.error(err); showToast('error', 'Error de red al actualizar producto'); });
};

// Cancelar editar producto
const btnCancelarEditarProducto = document.getElementById('btn-cancelar-editar-producto');
if (btnCancelarEditarProducto) btnCancelarEditarProducto.addEventListener('click', (e) => {
    e.preventDefault();
    const modal = document.getElementById('modal-editar-producto');
    if (modal) {
        modal.classList.remove('active');
        modal.classList.add('d-none');
    }
});