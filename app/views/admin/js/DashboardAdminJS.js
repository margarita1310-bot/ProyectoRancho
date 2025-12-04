/**
 * DashboardAdminJS
 * Script para cargar y mostrar las estadísticas del dashboard del administrador
 * Incluye contadores de promociones, eventos, reservas y disponibilidad de mesas
 */

/**
 * Evento: Ejecutar cuando el DOM está completamente cargado
 * Inicia la carga de estadísticas del servidor
 */
document.addEventListener('DOMContentLoaded', function () {
    cargarEstadisticas();
});

/**
 * Carga las estadísticas del sistema desde el servidor
 * Obtiene contadores de promociones, eventos, reservas y mesas disponibles
 * Actualiza los elementos del DOM con los valores obtenidos
 *
 * @async
 * @return {void}
 */
async function cargarEstadisticas() {
    try {
        // Realizar solicitud AJAX al controlador de administrador
        const response = await fetch('../../../../app/controllers/AdminController.php?action=getEstadisticas', {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        });

        // Validar que la respuesta sea exitosa
        if (!response.ok) {
            throw new Error('Error al cargar estadísticas');
        }

        // Parsear la respuesta JSON
        const data = await response.json();

        // Verificar que el servidor devolvió un estado exitoso
        if (data.status === 'ok') {
            // Actualizar contador de promociones
            document.getElementById('cant-promos').textContent = data.promociones || 0;

            // Actualizar contador de eventos
            document.getElementById('cant-eventos').textContent = data.eventos || 0;

            // Actualizar contador de reservas
            document.getElementById('cant-reservas').textContent = data.reservas || 0;

            // Actualizar contador de mesas disponibles
            document.getElementById('mesas-disponibles').textContent = data.mesas || 0;
        }
    } catch (error) {
        // Registrar cualquier error en la consola para depuración
        console.error('Error cargando estadísticas:', error);
    }
}
