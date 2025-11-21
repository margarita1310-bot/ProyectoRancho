<div id="reserva" class="d-none">
    <div class="section-header">
        <div class="section-title-wrapper">
            <h1 class="section-title-modern">
                <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="section-icon">
                    <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                    <circle cx="8.5" cy="7" r="4"></circle>
                    <polyline points="17 11 19 13 23 9"></polyline>
                </svg>
                Reservas
            </h1>
            <p class="section-subtitle">Consulta y administra las reservaciones</p>
        </div>
        <div class="filter-date-wrapper">
            <label for="filtro-fecha-reserva" class="filter-date-label">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                    <line x1="16" y1="2" x2="16" y2="6"></line>
                    <line x1="8" y1="2" x2="8" y2="6"></line>
                    <line x1="3" y1="10" x2="21" y2="10"></line>
                </svg>
                Filtrar por fecha:
            </label>
            <input type="date" id="filtro-fecha-reserva" class="form-control-modern">
        </div>
    </div>
    <div id="alerta-sin-disponibilidad" class="alert-modern alert-modern-warning d-none">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path>
            <line x1="12" y1="9" x2="12" y2="13"></line>
            <line x1="12" y1="17" x2="12.01" y2="17"></line>
        </svg>
        <span>No hay disponibilidad configurada para esta fecha. Por favor, configura la disponibilidad primero.</span>
    </div>
    <div class="table-responsive">
        <table class="table table-striped table-hover align-middle" id="tabla-reservas">
            <thead>
                <tr>
                    <th>Mesa</th>
                    <th>Folio</th>
                    <th>Cliente</th>
                    <th>Hora</th>
                    <th>Personas</th>
                    <th>Estado</th>
                    <th class="text-center">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td colspan="7" class="text-center text-muted">
                        Selecciona una fecha para ver las reservas
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>