/**
 * PerfilAdminJS.js
 * 
 * Gestiona el modal de perfil del administrador.
 * Carga y muestra los datos del administrador logueado.
 */

/**
 * abrirModalPerfil()
 * 
 * Abre el modal de perfil y carga los datos del administrador.
 */
async function abrirModalPerfil() {
    console.log('Abriendo modal de perfil...');
    try {
        const response = await fetch('../../app/controllers/AdminController.php?action=getPerfil', {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        });

        console.log('Response status:', response.status);

        if (!response.ok) {
            throw new Error('Error al cargar perfil');
        }

        const data = await response.json();
        console.log('Datos recibidos:', data);

        if (data.status === 'ok') {
            document.getElementById('perfil-nombre').value = data.admin.nombre || '';
            document.getElementById('perfil-correo').value = data.admin.correo || '';
            
            const modal = document.getElementById('modal-perfil-admin');
            modal.classList.remove('d-none');
            modal.classList.add('active');
        } else {
            showToast('Error', 'No se pudo cargar el perfil', 'danger');
        }
    } catch (error) {
        console.error('Error cargando perfil:', error);
        showToast('Error', 'Error al cargar el perfil', 'danger');
    }
}

/**
 * cerrarModalPerfil()
 * 
 * Cierra el modal de perfil.
 */
function cerrarModalPerfil() {
    const modal = document.getElementById('modal-perfil-admin');
    modal.classList.remove('active');
    modal.classList.add('d-none');
}

// Cerrar modal al hacer clic fuera
document.addEventListener('click', function(e) {
    const modal = document.getElementById('modal-perfil-admin');
    if (e.target === modal) {
        cerrarModalPerfil();
    }
});
