
document.addEventListener('DOMContentLoaded', cargarPromociones);

async function cargarPromociones() {
    const container = document.getElementById('promociones-container');
    if (!container) return;

    try {
        const response = await fetch('/app/controllers/UserController.php?action=getPromociones', {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        });
        
        if (!response.ok) throw new Error('Error al cargar promociones');
        
        const data = await response.json();
        
        if (!data || !Array.isArray(data)) {
            container.innerHTML = `
                <div class="col-12">
                    <div class="alert alert-info" role="alert">
                        <i class="bi bi-info-circle"></i> No hay promociones disponibles actualmente.
                    </div>
                </div>
            `;
            return;
        }

        const hoy = new Date();
        hoy.setHours(0, 0, 0, 0);
        
        const promocionesActivas = data.filter(promo => {
            if (promo.estado !== 'Disponible') return false;
            
            if (promo.fecha_fin) {
                const fechaFin = new Date(promo.fecha_fin);
                if (fechaFin < hoy) return false;
            }
            
            return true;
        });

        if (promocionesActivas.length === 0) {
            container.innerHTML = `
                <div class="col-12">
                    <div class="alert alert-info" role="alert">
                        <i class="bi bi-info-circle"></i> No hay promociones disponibles en este momento.
                    </div>
                </div>
            `;
            return;
        }
        
        container.innerHTML = promocionesActivas.map(promo => `
            <div class="col-12 mb-4">
                <div class="card card-promo-horizontal">
                    <div class="row g-0">
                        <div class="col-md-4">
                            ${promo.imagen ? `
                                <img src="/public/images/promocion/${promo.imagen}" 
                                     class="img-fluid rounded-start h-100" 
                                     style="object-fit: cover;"
                                     alt="${promo.nombre}">
                            ` : `
                                <div class="card-img-placeholder-horizontal d-flex align-items-center justify-content-center h-100">
                                    <svg width="60" height="60" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"></path>
                                        <line x1="7" y1="7" x2="7.01" y2="7"></line>
                                    </svg>
                                </div>
                            `}
                        </div>
                        <div class="col-md-8">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <h5 class="card-title fw-bold mb-0">${promo.nombre}</h5>
                                    <span class="badge bg-warning text-dark">🔥 OFERTA</span>
                                </div>
                                <p class="card-text text-muted mt-3">${promo.descripcion || 'Sin descripción'}</p>
                                ${promo.productos_nombres ? `
                                    <div class="productos-info">
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="me-2" style="display: inline-block; vertical-align: middle;">
                                            <circle cx="9" cy="21" r="1"></circle>
                                            <circle cx="20" cy="21" r="1"></circle>
                                            <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
                                        </svg>
                                        <small><strong>Productos incluidos:</strong> ${promo.productos_nombres}</small>
                                    </div>
                                ` : ''}
                                ${promo.fecha_inicio && promo.fecha_fin ? `
                                    <div class="card-footer-info mt-3">
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="me-2">
                                            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                            <line x1="16" y1="2" x2="16" y2="6"></line>
                                            <line x1="8" y1="2" x2="8" y2="6"></line>
                                            <line x1="3" y1="10" x2="21" y2="10"></line>
                                        </svg>
                                        <small><strong>Vigencia:</strong> ${formatearFecha(promo.fecha_inicio)} al ${formatearFecha(promo.fecha_fin)}</small>
                                    </div>
                                ` : ''}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `).join('');

    } catch (error) {
        console.error('Error al cargar promociones:', error);
        container.innerHTML = `
            <div class="col-12">
                <div class="alert alert-danger" role="alert">
                    <i class="bi bi-x-circle"></i> <strong>Error al cargar las promociones.</strong> Por favor, intenta más tarde.
                </div>
            </div>
        `;
    }
}

function formatearFecha(fecha) {
    if (!fecha) return '';
    const d = new Date(fecha);
    const opciones = { year: 'numeric', month: 'long', day: 'numeric' };
    return d.toLocaleDateString('es-MX', opciones);
}
