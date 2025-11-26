 /*
 * BtnEditarJs.js
 * 
 * Abre modales de edición cargando datos existentes del servidor.
 * 
 * Funcionalidad:
 * - Detecta clicks en botones .btn-editar
 * - Obtiene el ID y tipo de controlador del elemento
 * - Fetcha datos actuales desde {Controller}Controller.php?action=obtener
 * - Carga datos en el modal correspondiente (Producto, Promoción o Evento)
 * - Para imagen: siempre limpia el campo (es OPCIONAL, no se pre-carga imagen anterior)
 * 
 * Nota sobre imágenes:
 * - El campo #imagen se limpia siempre (value = '')
 * - Permite al usuario OPCIONALMENTE subir nueva imagen
 * - Si NO sube imagen: mantiene el archivo existente en la carpeta (controlador lo maneja)
 * - Si sube imagen: reemplaza la anterior
 */

/**
 * Función global para abrir modales de edición
 * Se usa desde los archivos de filtrado cuando se regeneran las tablas dinámicamente
 */
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
            modal.querySelector("#descripcion").value = p.descripcion;
            modal.querySelector("#fechaInicio").value = p.fecha_inicio;
            modal.querySelector("#fechaFin").value = p.fecha_fin;
            const estadoSelect = modal.querySelector("#estado");
            if (estadoSelect) estadoSelect.value = p.estado || '';
            
            // Pre-seleccionar productos asociados
            const selectProductos = modal.querySelector("#productos");
            if (selectProductos && p.productos && Array.isArray(p.productos)) {
                Array.from(selectProductos.options).forEach(option => {
                    option.selected = p.productos.includes(option.value);
                });
            }
            
            // Limpiar campo de imagen (es opcional, no cargar imagen anterior)
            const imagenInput = modal.querySelector("#imagen");
            if (imagenInput) imagenInput.value = '';
        } else if (controller === 'Evento') {
            const modal = document.querySelector("#modal-editar-evento");
            if (!modal) return;
            modal.classList.add("active");
            modal.classList.remove("d-none");
            modal.querySelector("#id").value = p.id_evento;
            modal.querySelector("#nombre").value = p.nombre;
            modal.querySelector("#descripcion").value = p.descripcion;
            modal.querySelector("#fecha").value = p.fecha;
            modal.querySelector("#horaInicio").value = p.hora_inicio;
            modal.querySelector("#horaFin").value = p.hora_fin;
            // Limpiar campo de imagen (es opcional, no cargar imagen anterior)
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
    // Handlers para abrir modales de edición (btn-editar)
    document.querySelectorAll(".btn-editar").forEach(btn => {
        btn.onclick = () => {
            const id = btn.dataset.id;
            const controller = btn.dataset.controller || 'Promocion';
            abrirEditar(id, controller);
        };
    });
});
