<div id="menu" class="d-none">
    <div class="section-header">
        <div class="section-title-wrapper">
            <h1 class="section-title-modern">
                <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="section-icon">
                    <path d="M3 2v7c0 1.1.9 2 2 2h4a2 2 0 0 0 2-2V2"></path>
                    <path d="M7 2v20"></path>
                    <path d="M21 15V2v0a5 5 0 0 0-5 5v6c0 1.1.9 2 2 2h3Zm0 0v7"></path>
                </svg>
                Menú
            </h1>
            <p class="section-subtitle">Administra productos y bebidas del menú</p>
        </div>
        <button id="btn-crear-producto" class="btn-action-primary" onclick="document.getElementById('modal-crear-producto').classList.add('active')">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="10"></circle>
                <line x1="12" y1="8" x2="12" y2="16"></line>
                <line x1="8" y1="12" x2="16" y2="12"></line>
            </svg>
            Crear Producto
        </button>
    </div>
    
    <!-- Filtros -->
    <div class="filter-section">
        <div class="filter-group filter-group-wrap">
            <button type="button" class="filter-btn filter-btn-active" data-filtro-producto="todos">
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
            <button type="button" class="filter-btn filter-btn-warning" data-filtro-producto="Botellas">
                🍾 Botellas
            </button>
            <button type="button" class="filter-btn filter-btn-warning" data-filtro-producto="Shots">
                🥃 Shots
            </button>
            <button type="button" class="filter-btn filter-btn-warning" data-filtro-producto="Cubas">
                🥤 Cubas
            </button>
            <button type="button" class="filter-btn filter-btn-warning" data-filtro-producto="Cervezas">
                🍺 Cervezas
            </button>
            <button type="button" class="filter-btn filter-btn-info" data-filtro-producto="Cocteles">
                🍸 Cocteles
            </button>
            <button type="button" class="filter-btn filter-btn-danger" data-filtro-producto="Tacos">
                🌮 Tacos
            </button>
        </div>
    </div>
    
    <div class="table-responsive">
        <table class="table table-striped table-hover align-middle">
            <thead>
                <tr>
                    <th>Producto</th>
                    <th>Precio</th>
                    <th>Categoria</th>
                    <th class="text-center">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php $producto = $producto ?? []; ?>
                <?php if (!empty($producto)): ?>
                <?php foreach ($producto as $p): ?>
                <tr>
                    <td><?= htmlspecialchars ($p['nombre']) ?></td>
                    <td><?= htmlspecialchars ($p['precio']) ?></td>
                    <td><?= htmlspecialchars ($p['categoria']) ?></td>
                    <td class="text-center">
                        <div class="d-flex gap-1 justify-content-center flex-wrap">
                            <button class="btn btn-editar" data-id="<?= $p['id_producto'] ?>" data-controller="Producto">
                                <i class="bi bi-pencil-square"></i>
                            </button>
                            <button class="btn btn-eliminar" data-id="<?= $p['id_producto'] ?>" data-controller="Producto">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="4" class="text-center">No hay productos</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>