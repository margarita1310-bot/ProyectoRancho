<div id="evento" class="d-none">
    <div class="section-header">
        <div class="section-title-wrapper">
            <h1 class="section-title-modern">
                <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="section-icon">
                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                    <line x1="16" y1="2" x2="16" y2="6"></line>
                    <line x1="8" y1="2" x2="8" y2="6"></line>
                    <line x1="3" y1="10" x2="21" y2="10"></line>
                </svg>
                Eventos
            </h1>
            <p class="section-subtitle">Organiza y gestiona eventos especiales</p>
        </div>
        <button id="btn-crear-evento" class="btn-action-primary" onclick="document.getElementById('modal-crear-evento').classList.add('active')">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="10"></circle>
                <line x1="12" y1="8" x2="12" y2="16"></line>
                <line x1="8" y1="12" x2="16" y2="12"></line>
            </svg>
            Crear Evento
        </button>
    </div>
    
    <!-- Filtros -->
    <div class="filter-section">
        <div class="filter-group">
            <button type="button" class="filter-btn filter-btn-active" data-filtro-evento="todos">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="8" y1="6" x2="21" y2="6"></line>
                    <line x1="8" y1="12" x2="21" y2="12"></line>
                    <line x1="8" y1="18" x2="21" y2="18"></line>
                    <line x1="3" y1="6" x2="3.01" y2="6"></line>
                    <line x1="3" y1="12" x2="3.01" y2="12"></line>
                    <line x1="3" y1="18" x2="3.01" y2="18"></line>
                </svg>
                Todos
            </button>
            <button type="button" class="filter-btn filter-btn-success" data-filtro-evento="proximos">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                    <line x1="16" y1="2" x2="16" y2="6"></line>
                    <line x1="8" y1="2" x2="8" y2="6"></line>
                    <line x1="3" y1="10" x2="21" y2="10"></line>
                    <polyline points="8 14 10 16 16 10"></polyline>
                </svg>
                Próximos
            </button>
            <button type="button" class="filter-btn filter-btn-secondary" data-filtro-evento="pasados">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                    <line x1="16" y1="2" x2="16" y2="6"></line>
                    <line x1="8" y1="2" x2="8" y2="6"></line>
                    <line x1="3" y1="10" x2="21" y2="10"></line>
                    <line x1="8" y1="14" x2="16" y2="14"></line>
                </svg>
                Pasados
            </button>
        </div>
    </div>
    
    <div class="table-responsive">
        <table class="table table-striped table-hover align-middle">
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Descripción</th>
                    <th>Fecha</th>
                    <th>Hora inicio</th>
                    <th>Hora fin</th>
                    <th class="text-center">Imagen</th>
                    <th class="text-center">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php $evento = $evento ?? []; ?>
                <?php if (!empty($evento)): ?>
                    <?php foreach ($evento as $ev): ?>
                        <tr>
                            <td><?= htmlspecialchars($ev['nombre']) ?></td>
                            <td><?= htmlspecialchars($ev['descripcion']) ?></td>
                            <td><?= htmlspecialchars($ev['fecha']) ?></td>
                            <td><?= htmlspecialchars($ev['hora_inicio']) ?></td>
                            <td><?= htmlspecialchars($ev['hora_fin']) ?></td>
                            <td class="text-center">
                                <?php
                                $id = $ev['id_evento'];
                                $imagenEncontrada = null;
                                $dirImagenes = __DIR__ . '/../../../public/images/evento/';
                                foreach (['jpg', 'png'] as $ext) {
                                    if (file_exists($dirImagenes . $id . '.' . $ext)) {
                                        $imagenEncontrada = $id . '.' . $ext;
                                        break;
                                    }
                                }
                                ?>
                                <?php if ($imagenEncontrada): ?>
                                    <img src="/public/images/evento/<?= $imagenEncontrada ?>" 
                                         alt="<?= htmlspecialchars($ev['nombre']) ?>" 
                                         class="img-thumbnail" 
                                         style="width: 60px; height: 60px; object-fit: cover;">
                                <?php else: ?>
                                    <div class="d-flex align-items-center justify-content-center" 
                                         style="width: 60px; height: 60px; background-color: #f8f9fa; border: 1px solid #dee2e6; border-radius: 0.25rem;">
                                        <small class="text-muted" style="font-size: 0.7rem; text-align: center;">Sin<br>imagen</small>
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <div class="d-flex gap-1 justify-content-center flex-wrap">
                                    <button class="btn btn-editar" data-id="<?= $ev['id_evento'] ?>" data-controller="Evento">
                                        <i class="bi bi-pencil-square"></i>
                                    </button>
                                    <button class="btn btn-eliminar" data-id="<?= $ev['id_evento'] ?>" data-controller="Evento">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="7" class="text-center">No hay eventos</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>