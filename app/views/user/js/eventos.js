/*
 * eventos.js
 * Carga y muestra eventos próximos desde la base de datos
 */

document.addEventListener('DOMContentLoaded', cargarEventos);

async function cargarEventos() {
    const container = document.getElementById('eventos-container');
    if (!container) return;

    try {
        const response = await fetch('/app/controllers/UserController.php?action=getEventos', {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        });
        
        if (!response.ok) throw new Error('Error al cargar eventos');
        
        const data = await response.json();
        
        if (!data || !Array.isArray(data)) {
            container.innerHTML = '<p class="text-center">No hay eventos programados.</p>';
            return;
        }

        // Filtrar solo eventos futuros o del día actual
        const hoy = new Date();
        hoy.setHours(0, 0, 0, 0);
        
        const eventosFuturos = data.filter(evento => {
            if (!evento.fecha) return false;
            const fechaEvento = new Date(evento.fecha);
            return fechaEvento >= hoy;
        }).sort((a, b) => new Date(a.fecha) - new Date(b.fecha)); // Ordenar por fecha

        if (eventosFuturos.length === 0) {
            container.innerHTML = '<p class="text-center">No hay eventos próximos programados.</p>';
            return;
        }

        // Renderizar eventos
        container.innerHTML = eventosFuturos.map(evento => `
            <div class="col-md-4 mb-4">
                <div class="card card-evento h-100">
                    <div class="card-img-wrapper">
                        ${evento.imagen ? `
                            <img src="/public/images/evento/${evento.imagen}" 
                                 class="card-img-top" 
                                 alt="${evento.nombre}">
                        ` : `
                            <div class="card-img-top card-img-placeholder card-img-evento">
                                <i class="fas fa-calendar-alt fa-3x"></i>
                            </div>
                        `}
                        <div class="card-badge-evento">🎉 EVENTO</div>
                    </div>
                    <div class="card-body">
                        <h5 class="card-title text-dark fw-bold">${evento.nombre}</h5>
                        <p class="card-text text-muted">${evento.descripcion || ''}</p>
                        <div class="card-footer-info">
                            <div class="mb-2">
                                <i class="fas fa-calendar text-primary me-2"></i>
                                <small>${formatearFecha(evento.fecha)}</small>
                            </div>
                            ${evento.hora_inicio ? `
                                <div>
                                    <i class="fas fa-clock text-success me-2"></i>
                                    <small>${evento.hora_inicio}${evento.hora_fin ? ' - ' + evento.hora_fin : ''}</small>
                                </div>
                            ` : ''}
                        </div>
                    </div>
                </div>
            </div>
        `).join('');

    } catch (error) {
        console.error('Error al cargar eventos:', error);
        container.innerHTML = '<p class="text-center text-danger">Error al cargar los eventos.</p>';
    }
}

function formatearFecha(fecha) {
    if (!fecha) return '';
    const d = new Date(fecha);
    const opciones = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
    return d.toLocaleDateString('es-MX', opciones);
}
