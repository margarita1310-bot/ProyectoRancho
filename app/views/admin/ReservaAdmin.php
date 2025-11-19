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
                            <td>
                                <?php if ($res['id_mesa']): ?>
                                    Mesa <?= htmlspecialchars($res['id_mesa']) ?>
                                <?php else: ?>
                                    <span class="text-muted">Sin asignar</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($res['estado'] === 'pendiente'): ?>
                                    <span class="badge bg-warning text-dark">Pendiente</span>
                                <?php elseif ($res['estado'] === 'confirmada'): ?>
                                    <span class="badge bg-success">Confirmada</span>
                                <?php else: ?>
                                    <span class="badge bg-danger">Cancelada</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <div class="d-flex gap-1 justify-content-center flex-wrap">
                                    <?php if ($res['estado'] === 'pendiente'): ?>
                                        <button class="btn btn-confirmar-reserva btn-sm btn-success" 
                                                data-id="<?= $res['id_reserva'] ?>" 
                                                data-controller="Reserva"
                                                title="Confirmar reserva">
                                            <i class="bi bi-check-circle"></i> Confirmar
                                        </button>
                                        <button class="btn btn-cancelar-reserva btn-sm btn-danger" 
                                                data-id="<?= $res['id_reserva'] ?>" 
                                                data-controller="Reserva"
                                                title="Cancelar reserva">
                                            <i class="bi bi-x-circle"></i> Cancelar
                                        </button>
                                    <?php elseif ($res['estado'] === 'confirmada'): ?>
                                        <button class="btn btn-cancelar-reserva btn-sm btn-danger" 
                                                data-id="<?= $res['id_reserva'] ?>" 
                                                data-controller="Reserva"
                                                title="Cancelar reserva">
                                            <i class="bi bi-x-circle"></i> Cancelar
                                        </button>
                                    <?php else: ?>
                                        <span class="text-muted">No hay acciones</span>
                                    <?php endif; ?>
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