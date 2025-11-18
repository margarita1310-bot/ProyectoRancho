<div id="evento" class="d-none">
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
        <h1 class="title">
            Eventos
            <i class="bi bi-info-circle ms-2" data-bs-toggle="tooltip"
            title="Sección para la gestión de eventos próximos o pasados."></i>
        </h1>
        <button id="btn-crear-evento" class="btn btn-sm" onclick="document.getElementById('modal-crear-evento').classList.add('active')">
            <i class="bi bi-plus-circle me-2"></i>Crear Evento
        </button>
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
                    <tr><td colspan="6" class="text-center">No hay eventos</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>