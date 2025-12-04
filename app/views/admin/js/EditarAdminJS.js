function abrirEditar(id, controller) {
    if (!id || !controller) return;
    const data = new FormData();
    data.append("id", id);
    fetch(`/app/controllers/${controller}Controller.php?action=obtener`, {
        method: "POST",
        headers: { 'X-Requested-With': 'XMLHttpRequest' },
        body: data
    })
    .then(async response => {
        const text = await response.text();
        console.log('Response status:', response.status);
        console.log('Response text:', text);
        try {
            return JSON.parse(text);
        } catch (e) {
            console.error('Error parsing JSON:', e);
            throw new Error('Respuesta no válida del servidor: ' + text.substring(0, 200));
        }
    })
    .then(p => {
        if (controller === 'Promocion') {
            const modal = document.querySelector("#modal-editar-promocion");
            if (!modal) return;
            modal.classList.add("active");
            modal.classList.remove("d-none");
            modal.querySelector("#id").value = p.id_promocion;
            modal.querySelector("#nombre").value = p.nombre;
            const descripcionTextarea = modal.querySelector("#descripcion");
            if (descripcionTextarea) {
                descripcionTextarea.value = p.descripcion || '';
                descripcionTextarea.style.height = 'auto';
                descripcionTextarea.style.height = descripcionTextarea.scrollHeight + 'px';
            }
            modal.querySelector("#fechaInicio").value = p.fecha_inicio;
            modal.querySelector("#fechaFin").value = p.fecha_fin;
            const estadoSelect = modal.querySelector("#estado");
            if (estadoSelect) estadoSelect.value = p.estado || '';
            const selectProductos = modal.querySelector("#productos");
            const idsSeleccionados = Array.isArray(p.productos) ? p.productos.map(String) : [];
            const aplicarPreseleccion = () => {
                if (!selectProductos) return;
                const setIds = new Set(idsSeleccionados);
                Array.from(selectProductos.options).forEach(option => {
                    option.selected = setIds.has(String(option.value));
                });
            };
            if (selectProductos) {
                if (selectProductos.options.length === 0 && typeof cargarProductosEnSelect === 'function') {
                    Promise.resolve(cargarProductosEnSelect()).then(() => {
                        aplicarPreseleccion();
                    }).catch(() => {
                    });
                } else {
                    aplicarPreseleccion();
                }
            }
            const imagenInput = modal.querySelector("#imagen");
            if (imagenInput) imagenInput.value = '';
        } else if (controller === 'Evento') {
            const modal = document.querySelector("#modal-editar-evento");
            if (!modal) return;
            modal.classList.add("active");
            modal.classList.remove("d-none");
            modal.querySelector("#id").value = p.id_evento;
            modal.querySelector("#nombre").value = p.nombre;
            const descripcionTextarea = modal.querySelector("#descripcion");
            if (descripcionTextarea) {
                descripcionTextarea.value = p.descripcion || '';
                descripcionTextarea.style.height = 'auto';
                descripcionTextarea.style.height = descripcionTextarea.scrollHeight + 'px';
            }
            modal.querySelector("#fecha").value = p.fecha;
            modal.querySelector("#horaInicio").value = p.hora_inicio;
            modal.querySelector("#horaFin").value = p.hora_fin;
            const imagenInfo = modal.querySelector("#imagen-actual-info");
            if (imagenInfo && p.tiene_imagen) {
                imagenInfo.classList.remove('d-none');
            } else if (imagenInfo) {
                imagenInfo.classList.add('d-none');
            }
            const imagenInput = modal.querySelector("#imagen");
            if (imagenInput) imagenInput.value = '';
        } else if (controller === 'Producto') {
            const modal = document.querySelector("#modal-editar-producto");
            if (!modal) return;
            modal.classList.add("active");
            modal.classList.remove("d-none");
            modal.querySelector("#id").value = p.id_producto;
            modal.querySelector("#nombre").value = p.nombre;
            modal.querySelector("#precio").value = p.precio;
            const categoriaSelect = modal.querySelector("#categoria");
            if (categoriaSelect) categoriaSelect.value = p.categoria || '';
        }
    })
    .catch(err => { 
        console.error('Error completo al obtener registro:', err); 
        showToast('error','No se pudo cargar el registro: ' + err.message); 
    });
}
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll(".btn-editar").forEach(btn => {
        btn.onclick = () => {
            const id = btn.dataset.id;
            const controller = btn.dataset.controller || 'Promocion';
            abrirEditar(id, controller);
        };
    });
});