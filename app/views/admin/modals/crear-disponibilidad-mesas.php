<section id="modal-crear-disponibilidad-mesas" class="modal-overlay">
    <div class="modal-content">
        <h2>Nueva Disponibilidad de Mesas</h2>
        <form id="form-disponibilidad" class="text-start">
            <div class="row">
                <div class="mb-3">
                    <label class="form-label">Fecha</label>
                    <input type="date" id="disp-fecha" name="fecha" class="form-control" min="<?php echo date('Y-m-d'); ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Cantidad de mesas</label>
                    <input type="number" id="disp-cantidad" name="cantidad" class="form-control" min="1" max="100" required>
                </div>
            </div>
            <div class="alert alert-info" role="alert">
                <i class="bi bi-info-circle"></i> Al crear la disponibilidad, se activarán automáticamente las primeras mesas del catálogo hasta la cantidad indicada.
            </div>
            <div class="d-flex gap-2 mt-3">
                <button type="submit" id="btn-guardar-disponibilidad" class="btn btn-sm">Guardar</button>
                <button type="button" id="btn-cancelar-disponibilidad" class="btn btn-sm">Cancelar</button>
            </div>
        </form>
    </div>
</section>