/*
 * promociones.js
 * Carga y muestra promociones activas desde la base de datos
 */

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
            container.innerHTML = '<p class="text-center">No hay promociones disponibles.</p>';
            return;
        }

        // Filtrar solo promociones activas/disponibles y vigentes
        const hoy = new Date();
        hoy.setHours(0, 0, 0, 0);
        
        const promocionesActivas = data.filter(promo => {
            if (promo.estado !== 'Disponible') return false;
            
            // Verificar fecha de vigencia
            if (promo.fecha_fin) {
                const fechaFin = new Date(promo.fecha_fin);
                if (fechaFin < hoy) return false;
            }
            
            return true;
        });

        if (promocionesActivas.length === 0) {
            container.innerHTML = '<p class="text-center">No hay promociones disponibles actualmente.</p>';
            return;
        }

        // Renderizar promociones
        container.innerHTML = promocionesActivas.map(promo => `
            <div class="col-md-4 mb-4">
                <div class="card card-promo h-100">
                    <div class="card-img-wrapper">
                        ${promo.imagen ? `
                            <img src="/public/images/promocion/${promo.imagen}" 
                                 class="card-img-top" 
                                 alt="${promo.nombre}">
                        ` : `
                            <div class="card-img-top card-img-placeholder">
                                <i class="fas fa-tag fa-3x"></i>
                            </div>
                        `}
                        <div class="card-badge-promo">🔥 OFERTA</div>
                    </div>
                    <div class="card-body">
                        <h5 class="card-title text-dark fw-bold">${promo.nombre}</h5>
                        <p class="card-text text-muted">${promo.descripcion || ''}</p>
                        ${promo.fecha_inicio && promo.fecha_fin ? `
                            <div class="card-footer-info">
                                <i class="fas fa-calendar-alt me-2"></i>
                                <small>Del ${formatearFecha(promo.fecha_inicio)} al ${formatearFecha(promo.fecha_fin)}</small>
                            </div>
                        ` : ''}
                    </div>
                </div>
            </div>
        `).join('');

    } catch (error) {
        console.error('Error al cargar promociones:', error);
        container.innerHTML = '<p class="text-center text-danger">Error al cargar las promociones.</p>';
    }
}

function formatearFecha(fecha) {
    if (!fecha) return '';
    const d = new Date(fecha);
    const opciones = { year: 'numeric', month: 'long', day: 'numeric' };
    return d.toLocaleDateString('es-MX', opciones);
}
