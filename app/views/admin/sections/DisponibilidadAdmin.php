<!-- Sección de Disponibilidad de Mesas -->
<div id="disponibilidad" class="d-none">
    <div class="section-header">
        <div class="section-title-wrapper">
            <h1 class="section-title-modern">
                <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="section-icon">
                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                    <line x1="16" y1="2" x2="16" y2="6"></line>
                    <line x1="8" y1="2" x2="8" y2="6"></line>
                    <line x1="3" y1="10" x2="21" y2="10"></line>
                    <path d="M8 14h8"></path>
                    <path d="M8 18h8"></path>
                </svg>
                Disponibilidad de Mesas
            </h1>
            <p class="section-subtitle">Configura cuántas mesas estarán disponibles por fecha</p>
        </div>
        <button id="btn-crear-disponibilidad" class="btn-action-primary" onclick="document.getElementById('modal-crear-disponibilidad-mesas').classList.add('active')">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="10"></circle>
                <line x1="12" y1="8" x2="12" y2="16"></line>
                <line x1="8" y1="12" x2="16" y2="12"></line>
            </svg>
            Crear Disponibilidad
        </button>
    </div>
    <div class="table-responsive">
        <table class="table table-striped table-hover align-middle" id="tabla-disponibilidad">
            <thead>
                <tr>
                    <th>Fecha</th>
                    <th>Cantidad de Mesas</th>
                    <th>Estado</th>
                    <th class="text-center">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <!-- Los datos se cargarán dinámicamente con JavaScript -->
                <tr>
                    <td colspan="4" class="text-center text-muted">
                        <div class="spinner-border spinner-border-sm me-2" role="status">
                            <span class="visually-hidden">Cargando...</span>
                        </div>
                        Cargando disponibilidades...
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
