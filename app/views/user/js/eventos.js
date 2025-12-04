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
            container.innerHTML = `
                <div class="col-12">
                    <div class="alert alert-info" role="alert">
                        <i class="bi bi-info-circle"></i> No hay eventos programados actualmente.
                    </div>
                </div>
            `;
            return;
        }

        const hoy = new Date();
        hoy.setHours(0, 0, 0, 0);
        
        const eventosFuturos = data.filter(evento => {
            if (!evento.fecha) return false;
            const fechaEvento = new Date(evento.fecha + 'T00:00:00');
            return fechaEvento >= hoy;
        }).sort((a, b) => new Date(a.fecha + 'T00:00:00') - new Date(b.fecha + 'T00:00:00'));

        if (eventosFuturos.length === 0) {
            container.innerHTML = `
                <div class="col-12">
                    <div class="alert alert-info" role="alert">
                        <i class="bi bi-info-circle"></i> No hay eventos próximos programados.
                    </div>
                </div>
            `;
            return;
        }

        container.innerHTML = eventosFuturos.map(evento => `
            <div class="col-12 mb-4">
                <div class="card card-promo-horizontal">
                    <div class="row g-0">
                        <div class="col-md-4">
                            ${evento.imagen ? `
                                <img src="/public/images/evento/${evento.imagen}" 
                                     class="img-fluid rounded-start h-100" 
                                     style="object-fit: cover;"
                                     alt="${evento.nombre}">
                            ` : `
                                <div class="card-img-placeholder-horizontal d-flex align-items-center justify-content-center h-100">
                                    <svg width="60" height="60" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                        <line x1="16" y1="2" x2="16" y2="6"></line>
                                        <line x1="8" y1="2" x2="8" y2="6"></line>
                                        <line x1="3" y1="10" x2="21" y2="10"></line>
                                    </svg>
                                </div>
                            `}
                        </div>
                        <div class="col-md-8">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <h5 class="card-title fw-bold mb-0">${evento.nombre}</h5>
                                    <span class="badge bg-info text-dark">🎉 EVENTO</span>
                                </div>
                                <p class="card-text text-muted mt-3">${evento.descripcion || 'Sin descripción'}</p>
                                <div class="card-footer-info mt-3">
                                    <div class="d-flex align-items-center mb-1">
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="me-2" style="flex-shrink: 0;">
                                            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                            <line x1="16" y1="2" x2="16" y2="6"></line>
                                            <line x1="8" y1="2" x2="8" y2="6"></line>
                                            <line x1="3" y1="10" x2="21" y2="10"></line>
                                        </svg>
                                        <small><strong>Fecha:</strong> ${formatearFecha(evento.fecha)}</small>
                                    </div>
                                    ${evento.hora_inicio ? `
                                        <div class="d-flex align-items-center">
                                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="me-2" style="flex-shrink: 0;">
                                                <circle cx="12" cy="12" r="10"></circle>
                                                <polyline points="12 6 12 12 16 14"></polyline>
                                            </svg>
                                            <small><strong>Horario:</strong> ${formatearHora(evento.hora_inicio)}${evento.hora_fin ? ' - ' + formatearHora(evento.hora_fin) : ''}</small>
                                        </div>
                                    ` : ''}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `).join('');

    } catch (error) {
        console.error('Error al cargar eventos:', error);
        container.innerHTML = `
            <div class="col-12">
                <div class="alert alert-danger" role="alert">
                    <i class="bi bi-x-circle"></i> <strong>Error al cargar los eventos.</strong> Por favor, intenta más tarde.
                </div>
            </div>
        `;
    }
}

function formatearFecha(fecha) {
    if (!fecha) return '';
    const d = new Date(fecha + 'T00:00:00');
    const opciones = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
    return d.toLocaleDateString('es-MX', opciones);
}

function formatearHora(hora) {
    if (!hora) return '';
    let [horas, minutos] = hora.split(':');
    horas = parseInt(horas);
    const periodo = horas >= 12 ? 'PM' : 'AM';
    horas = horas % 12 || 12;
    return `${horas}:${minutos} ${periodo}`;
}
