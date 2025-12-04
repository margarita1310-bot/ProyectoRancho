<div id="mesa" class="d-none">
    <div class="section-header">
        <div class="section-title-wrapper">
            <h1 class="section-title-modern">
                <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="section-icon">
                    <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                    <polyline points="9 22 9 12 15 12 15 22"></polyline>
                </svg>
                Mesas
            </h1>
            <p class="section-subtitle">Estado actual de las mesas del bar</p>
        </div>
        <button id="btn-crear-disponibilidad" class="btn-action-primary" onclick="document.getElementById('modal-create-mesas').classList.add('active'); document.getElementById('mesas-fecha').value = new Date().toISOString().slice(0,10);">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="10"></circle>
                <line x1="12" y1="8" x2="12" y2="16"></line>
                <line x1="8" y1="12" x2="16" y2="12"></line>
            </svg>
            Crear disponibilidad
        </button>
    </div>
    <div class="table-responsive">
        <table class="table table-striped table-hover align-middle">
            <thead>
                <tr>
                    <th>Mesa</th>
                    <th>Cliente</th>
                    <th>Teléfono</th>
                    <th>Estado</th>
                </tr>
            </thead>
            <tbody>
                <?php $mesas = $mesas ?? []; ?>
                <?php if (!empty($mesas)): ?>
                    <?php foreach ($mesas as $mesa): ?>
                        <tr>
                            <td>Mesa <?= htmlspecialchars($mesa['numero']) ?></td>
                            <td>
                                <?php if (!empty($mesa['nombre'])): ?>
                                    <?= htmlspecialchars($mesa['nombre']) ?>
                                <?php else: ?>
                                    <span class="text-muted">-</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if (!empty($mesa['telefono'])): ?>
                                    <?= htmlspecialchars($mesa['telefono']) ?>
                                <?php else: ?>
                                    <span class="text-muted">-</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($mesa['estado'] === 'Ocupada'): ?>
                                    <span class="badge bg-danger">Ocupada</span>
                                <?php else: ?>
                                    <span class="badge bg-success">Disponible</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="4" class="text-center">No hay mesas activas. Crea una disponibilidad primero.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>