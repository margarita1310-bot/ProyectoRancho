<!-- Modal Perfil Administrador -->
<div id="modal-perfil-admin" class="modal-overlay">
    <div class="modal-content modal-perfil">
        <!-- Header del perfil -->
        <div class="perfil-header">
            <div class="perfil-cover">
                <svg class="perfil-pattern" width="100%" height="100%" xmlns="http://www.w3.org/2000/svg">
                    <defs>
                        <pattern id="pattern-circles" x="0" y="0" width="40" height="40" patternUnits="userSpaceOnUse">
                            <circle cx="20" cy="20" r="2" fill="rgba(255,255,255,0.1)"/>
                        </pattern>
                    </defs>
                    <rect x="0" y="0" width="100%" height="100%" fill="url(#pattern-circles)"/>
                </svg>
            </div>
            
            <button type="button" class="perfil-close-btn" onclick="cerrarModalPerfil()">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="18" y1="6" x2="6" y2="18"></line>
                    <line x1="6" y1="6" x2="18" y2="18"></line>
                </svg>
            </button>
            
            <div class="perfil-avatar-container">
                <div class="perfil-avatar">
                    <svg width="60" height="60" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                        <circle cx="12" cy="7" r="4"></circle>
                    </svg>
                </div>
                <div class="perfil-badge">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M12 2L15.09 8.26L22 9.27L17 14.14L18.18 21.02L12 17.77L5.82 21.02L7 14.14L2 9.27L8.91 8.26L12 2z"></path>
                    </svg>
                </div>
            </div>
        </div>
        
        <!-- Contenido del perfil -->
        <div class="perfil-body">
            <div class="perfil-title-section">
                <h2 class="perfil-title">Mi Perfil</h2>
                <p class="perfil-subtitle">Información de la cuenta de administrador</p>
            </div>
            
            <form id="form-perfil-admin" class="perfil-form">
                <div class="perfil-info-grid">
                    <!-- Campo Nombre -->
                    <div class="perfil-field">
                        <div class="perfil-field-header">
                            <div class="perfil-field-icon perfil-field-icon-primary">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                    <circle cx="12" cy="7" r="4"></circle>
                                </svg>
                            </div>
                            <label for="perfil-nombre" class="perfil-label">Nombre completo</label>
                        </div>
                        <div class="perfil-field-input-wrapper">
                            <input type="text" class="perfil-input" id="perfil-nombre" name="nombre" readonly>
                        </div>
                    </div>
                    
                    <!-- Campo Correo -->
                    <div class="perfil-field">
                        <div class="perfil-field-header">
                            <div class="perfil-field-icon perfil-field-icon-success">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                                    <polyline points="22,6 12,13 2,6"></polyline>
                                </svg>
                            </div>
                            <label for="perfil-correo" class="perfil-label">Correo electrónico</label>
                        </div>
                        <div class="perfil-field-input-wrapper">
                            <input type="email" class="perfil-input" id="perfil-correo" name="correo" readonly>
                        </div>
                    </div>
                    
                    <!-- Campo Rol (decorativo) -->
                    <div class="perfil-field">
                        <div class="perfil-field-header">
                            <div class="perfil-field-icon perfil-field-icon-warning">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M12 2L15.09 8.26L22 9.27L17 14.14L18.18 21.02L12 17.77L5.82 21.02L7 14.14L2 9.27L8.91 8.26L12 2z"></path>
                                </svg>
                            </div>
                            <label class="perfil-label">Rol</label>
                        </div>
                        <div class="perfil-field-input-wrapper">
                            <div class="perfil-role-badge">
                                <span class="perfil-role-text">Administrador</span>
                                <span class="perfil-role-icon">👑</span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Campo Estado (decorativo) -->
                    <div class="perfil-field">
                        <div class="perfil-field-header">
                            <div class="perfil-field-icon perfil-field-icon-info">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                                    <polyline points="22 4 12 14.01 9 11.01"></polyline>
                                </svg>
                            </div>
                            <label class="perfil-label">Estado de la cuenta</label>
                        </div>
                        <div class="perfil-field-input-wrapper">
                            <div class="perfil-status-badge perfil-status-active">
                                <span class="perfil-status-dot"></span>
                                <span>Activa</span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Info adicional -->
                <div class="perfil-info-card">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"></circle>
                        <line x1="12" y1="16" x2="12" y2="12"></line>
                        <line x1="12" y1="8" x2="12.01" y2="8"></line>
                    </svg>
                    <div>
                        <p class="perfil-info-title">Información importante</p>
                        <p class="perfil-info-text">Para modificar tus datos personales o solicitar cambios en tu cuenta, contacta al administrador del sistema.</p>
                    </div>
                </div>
                
                <!-- Botones de acción -->
                <div class="perfil-actions">
                    <button type="button" class="perfil-btn perfil-btn-secondary" onclick="cerrarModalPerfil()">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="18" y1="6" x2="6" y2="18"></line>
                            <line x1="6" y1="6" x2="18" y2="18"></line>
                        </svg>
                        Cerrar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
