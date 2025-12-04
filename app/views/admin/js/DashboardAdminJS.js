document.addEventListener('DOMContentLoaded', function() {
    cargarEstadisticas();
});
async function cargarEstadisticas() {
    try {
        const response = await fetch('../../../../app/controllers/AdminController.php?action=getEstadisticas', {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        });
        if (!response.ok) {
            throw new Error('Error al cargar estadísticas');
        }
        const data = await response.json();
        if (data.status === 'ok') {
            // Actualizar contadores
            document.getElementById('cant-promos').textContent = data.promociones || 0;
            document.getElementById('cant-eventos').textContent = data.eventos || 0;
            document.getElementById('cant-reservas').textContent = data.reservas || 0;
            document.getElementById('mesas-disponibles').textContent = data.mesas || 0;
        }
    } catch (error) {
        console.error('Error cargando estadísticas:', error);
    }
}