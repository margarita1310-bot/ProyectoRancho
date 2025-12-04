/**
 * PerfilAdminJS
 * Script para gestionar la visualización y edición del perfil del administrador
 * Incluye carga de datos del perfil desde servidor y manejo de modal
 */

/**
 * Abre el modal del perfil del administrador
 * Carga datos actuales desde el servidor
 *
 * @async
 * @return {void}
 */
async function abrirModalPerfil() {
    try {
        // Realizar solicitud AJAX para obtener datos del perfil
        const response = await fetch('/app/controllers/AdminController.php?action=getPerfil', {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        });

        // Validar respuesta HTTP
        if (!response.ok) {
            throw new Error('Error al cargar perfil');
        }

        // Parsear JSON
        const data = await response.json();

        // Validar respuesta del servidor
        if (data.status === 'ok') {
            // Llenar formulario con datos del perfil
            document.getElementById('perfil-nombre').value = data.admin.nombre || '';
            document.getElementById('perfil-correo').value = data.admin.correo || '';

            // Abrir modal
            const modal = document.getElementById('modal-perfil-admin');
            modal.classList.remove('d-none');
            modal.classList.add('active');
        } else {
            showToast('error', 'No se pudo cargar el perfil');
        }
    } catch (error) {
        // Registrar error
        console.error('Error cargando perfil:', error);

        // Mostrar mensaje de error
        showToast('error', 'Error al cargar el perfil');
    }
}

/**
 * Cierra el modal del perfil del administrador
 *
 * @return {void}
 */
function cerrarModalPerfil() {
    // Obtener modal
    const modal = document.getElementById('modal-perfil-admin');

    // Remover clase activa y agregar clase oculta
    modal.classList.remove('active');
    modal.classList.add('d-none');
}

/**
 * Evento: Click en modal para cerrar (backdrop)
 * Cierra el modal cuando se hace click fuera del contenido
 */
document.addEventListener('click', function (e) {
    // Obtener modal
    const modal = document.getElementById('modal-perfil-admin');

    // Validar que sea click en el fondo del modal
    if (e.target === modal) {
        cerrarModalPerfil();
    }
});