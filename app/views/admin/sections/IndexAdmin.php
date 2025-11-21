<!--Inicio-->
<div id="inicio" class="d-none">
    <div class="title">
        <h1 class="dashboard-title">
            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display: inline-block; vertical-align: middle; margin-right: 12px;">
                <rect x="3" y="3" width="7" height="7"></rect>
                <rect x="14" y="3" width="7" height="7"></rect>
                <rect x="14" y="14" width="7" height="7"></rect>
                <rect x="3" y="14" width="7" height="7"></rect>
            </svg>
            Panel General
        </h1>
        <p class="dashboard-subtitle">Resumen de actividades del restaurante</p>
    </div>

    <!-- Alert de disponibilidad -->
    <div id="alert-disponibilidad" class="alert alert-disponibilidad d-none">
        <div class="alert-icon">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path>
                <line x1="12" y1="9" x2="12" y2="13"></line>
                <line x1="12" y1="17" x2="12.01" y2="17"></line>
            </svg>
        </div>
        <div class="alert-content">
            <strong>Atención</strong>
            <p>Aún no has configurado la disponibilidad del restaurante hoy.</p>
        </div>
        <button class="btn btn-config">Configurar Ahora</button>
    </div>

    <!-- Tarjetas de estadísticas -->
    <div class="row g-4">
        <!-- Promociones activas -->
        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6">
            <div class="stats-card stats-card-promociones">
                <div class="stats-icon">
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"></circle>
                        <path d="M12 6v6l4 2"></path>
                    </svg>
                </div>
                <div class="stats-content">
                    <h3 class="stats-number" id="cant-promos">0</h3>
                    <p class="stats-label">Promociones activas</p>
                </div>
                <div class="stats-footer">
                    <span class="stats-badge">Vigentes</span>
                </div>
            </div>
        </div>

        <!-- Eventos próximos -->
        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6">
            <div class="stats-card stats-card-eventos">
                <div class="stats-icon">
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                        <line x1="16" y1="2" x2="16" y2="6"></line>
                        <line x1="8" y1="2" x2="8" y2="6"></line>
                        <line x1="3" y1="10" x2="21" y2="10"></line>
                    </svg>
                </div>
                <div class="stats-content">
                    <h3 class="stats-number" id="cant-eventos">0</h3>
                    <p class="stats-label">Eventos próximos</p>
                </div>
                <div class="stats-footer">
                    <span class="stats-badge">Programados</span>
                </div>
            </div>
        </div>

        <!-- Reservas pendientes -->
        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6">
            <div class="stats-card stats-card-reservas">
                <div class="stats-icon">
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                        <circle cx="8.5" cy="7" r="4"></circle>
                        <polyline points="17 11 19 13 23 9"></polyline>
                    </svg>
                </div>
                <div class="stats-content">
                    <h3 class="stats-number" id="cant-reservas">0</h3>
                    <p class="stats-label">Reservas pendientes</p>
                </div>
                <div class="stats-footer">
                    <span class="stats-badge">Por confirmar</span>
                </div>
            </div>
        </div>

        <!-- Mesas disponibles -->
        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6">
            <div class="stats-card stats-card-mesas">
                <div class="stats-icon">
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                        <polyline points="9 22 9 12 15 12 15 22"></polyline>
                    </svg>
                </div>
                <div class="stats-content">
                    <h3 class="stats-number" id="mesas-disponibles">0</h3>
                    <p class="stats-label">Mesas disponibles</p>
                </div>
                <div class="stats-footer">
                    <span class="stats-badge">Libres hoy</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Sección de acceso rápido -->
    <div class="quick-access-section">
        <h2 class="section-title">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display: inline-block; vertical-align: middle; margin-right: 8px;">
                <circle cx="12" cy="12" r="10"></circle>
                <polyline points="12 6 12 12 16 14"></polyline>
            </svg>
            Acceso Rápido
        </h2>
        <div class="row g-3">
            <div class="col-lg-3 col-md-4 col-sm-6">
                <div class="quick-card" onclick="mostrarContenido('promocion')">
                    <div class="quick-icon">📢</div>
                    <h4>Promociones</h4>
                    <p>Gestionar ofertas</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-4 col-sm-6">
                <div class="quick-card" onclick="mostrarContenido('evento')">
                    <div class="quick-icon">🎉</div>
                    <h4>Eventos</h4>
                    <p>Programar actividades</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-4 col-sm-6">
                <div class="quick-card" onclick="mostrarContenido('reserva')">
                    <div class="quick-icon">📋</div>
                    <h4>Reservas</h4>
                    <p>Ver solicitudes</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-4 col-sm-6">
                <div class="quick-card" onclick="mostrarContenido('disponibilidad')">
                    <div class="quick-icon">🪑</div>
                    <h4>Disponibilidad</h4>
                    <p>Configurar mesas</p>
                </div>
            </div>
        </div>
    </div>
</div>