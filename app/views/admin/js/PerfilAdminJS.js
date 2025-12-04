async function abrirModalPerfil() {
    try {
        const response = await fetch('/app/controllers/AdminController.php?action=getPerfil', {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        });
        if (!response.ok) {
            throw new Error('Error al cargar perfil');
        }
        const data = await response.json();
        if (data.status === 'ok') {
            document.getElementById('perfil-nombre').value = data.admin.nombre || '';
            document.getElementById('perfil-correo').value = data.admin.correo || '';
            const modal = document.getElementById('modal-perfil-admin');
            modal.classList.remove('d-none');
            modal.classList.add('active');
        } else {
            showToast('error', 'No se pudo cargar el perfil');
        }
    } catch (error) {
        console.error('Error cargando perfil:', error);
        showToast('error', 'Error al cargar el perfil');
    }
}
function cerrarModalPerfil() {
    const modal = document.getElementById('modal-perfil-admin');
    modal.classList.remove('active');
    modal.classList.add('d-none');
}
document.addEventListener('click', function(e) {
    const modal = document.getElementById('modal-perfil-admin');
    if (e.target === modal) {
        cerrarModalPerfil();
    }
});