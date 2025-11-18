<div id="promocion" class="d-none">
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
        <h1 class="title">
            Promociones
            <i class="bi bi-info-circle ms-2" data-bs-toggle="tooltip"
            title="Aquí podrás gestionar las promociones activas y crear nuevas."></i>
        </h1>
        <button id="btn-crear-promocion" class="btn" onclick="document.getElementById('modal-crear-promocion').classList.add('active')">
            <i class="bi bi-plus-circle me-2"></i>Crear promoción
        </button>
    </div>
    <div class="table-responsive">
        <table class="table table-striped table-hover align-middle">
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Descripción</th>
                    <th>Fecha inicio</th>
                    <th>Fecha fin</th>
                    <th>Estado</th>
                    <th class="text-center">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php $promocion = $promocion ?? []; ?>
                <?php if (!empty($promocion)): ?>
                    <?php foreach ($promocion as $pr): ?>
                        <tr>
                            <td><?= htmlspecialchars($pr['nombre']) ?></td>
                            <td><?= htmlspecialchars($pr['descripcion']) ?></td>
                            <td><?= htmlspecialchars($pr['fecha_inicio']) ?></td>
                            <td><?= htmlspecialchars($pr['fecha_fin']) ?></td>
                            <td><?= htmlspecialchars($pr['estado']) ?></td>
                            <td class="text-center">
                                <div class="d-flex gap-1 justify-content-center flex-wrap">
                                    <button class="btn btn-editar" data-id="<?= $pr['id_promocion'] ?>" data-controller="Promocion">
                                        <i class="bi bi-pencil-square"></i>
                                    </button>
                                    <button class="btn btn-eliminar" data-id="<?= $pr['id_promocion'] ?>" data-controller="Promocion">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="6" class="text-center">No hay promociones</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>