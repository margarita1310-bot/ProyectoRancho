<div id="reserva" class="d-none">
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
        <h1 class="title">
            Reservas
            <i class="bi bi-info-circle ms-2" data-bs-toggle="tooltip"
            title="Consulta y administra las reservaciones realizadas."></i>
        </h1>
    </div>
    <div class="table-responsive">
        <table class="table table-striped table-hover align-middle">
            <thead>
                <tr>
                    <th>Folio</th>
                    <th>Cliente</th>
                    <th>Fecha</th>
                    <th>Hora</th>
                    <th>Personas</th>
                    <th>Mesa</th>
                    <th>Estado</th>
                    <th class="text-center">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php $reserva = $reserva ?? []; ?>
                <?php if (!empty($reserva)): ?>
                    <?php foreach ($reserva as $res): ?>
                        <tr>
                            <td><?= htmlspecialchars($res['folio']) ?></td>
                            <td><?= htmlspecialchars($res['nombre']) ?></td>
                            <td><?= htmlspecialchars($res['fecha']) ?></td>
                            <td><?= htmlspecialchars($res['hora']) ?></td>
                            <td><?= htmlspecialchars($res['num_personas']) ?></td>
                            <td><?= htmlspecialchars($res['id_mesa']) ?></td>
                            <td><?= htmlspecialchars($res['estado']) ?></td>
                            <td class="text-center">
                                <div class="d-flex gap-1 justify-content-center flex-wrap">
                                    <button class="btn btn-confirmar-reserva" data-id="<?= $res['id_reserva'] ?>" data-controller="Reserva">
                                        <i class="bi bi-check-circle"></i>Confirmar
                                    </button>
                                    <button class="btn btn-cancelar-reserva" data-id="<?= $res['id_reserva'] ?>" data-controller="Reserva">
                                        <i class="bi bi-x-circle"></i>Cancelar
                                    </button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="8" class="text-center">No hay reservaciones</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>