<section id="modal-editar-disponibilidad-mesas" class="modal-overlay">
    <div class="modal-content">
        <h2>Editar Disponibilidad de Mesas</h2>
        <form id="form-editar-disponibilidad" class="text-start">
            <input type="hidden" id="edit-disp-id" name="id">
            <input type="hidden" id="edit-disp-fecha" name="fecha">
            <div class="row">
                <div class="mb-3">
                    <label class="form-label">Fecha</label>
                    <input type="text" id="edit-disp-fecha-display" class="form-control" disabled>
                </div>
                <div class="mb-3">
                    <label class="form-label">Cantidad de mesas</label>
                    <input type="number" id="edit-disp-cantidad" name="cantidad" class="form-control" min="1" max="100" required>
                </div>
            </div>
            <div class="alert alert-info" role="alert">
                <i class="bi bi-info-circle"></i> Al actualizar la disponibilidad, se ajustarán las mesas activas según la nueva cantidad.
            </div>
            <div class="d-flex gap-2 mt-3">
                <button type="submit" id="btn-actualizar-disponibilidad" class="btn btn-sm">Actualizar</button>
                <button type="button" id="btn-cancelar-editar-disponibilidad" class="btn btn-sm">Cancelar</button>
            </div>
        </form>
    </div>
</section>
