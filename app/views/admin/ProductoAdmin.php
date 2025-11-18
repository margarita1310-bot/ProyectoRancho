<div id="menu" class="d-none">
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
        <h1 class="title">
            Menú
            <i class="bi bi-info-circle ms-2" data-bs-toggle="tooltip"
            title="Gestión de los elementos del menú (tacos, botellas, etc.)"></i>
        </h1>
        <button id="btn-crear-producto" class="btn" onclick="document.getElementById('modal-crear-producto').classList.add('active')">
            <i class="bi bi-plus-circle me-2"></i>Crear Producto
        </button>
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