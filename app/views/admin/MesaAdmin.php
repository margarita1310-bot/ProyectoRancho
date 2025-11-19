<div id="mesa" class="d-none">
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
        <h1 class="title">
            Mesas
            <i class="bi bi-info-circle ms-2" data-bs-toggle="tooltip"
            title="Aquí se muestran las mesas activas y su estado actual."></i>
        </h1>
        <button id="btn-crear-disponibilidad" class="btn" onclick="document.getElementById('modal-crear-disponibilidad').classList.add('active')">
            <i class="bi bi-plus-circle me-2"></i>Crear disponibilidad
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