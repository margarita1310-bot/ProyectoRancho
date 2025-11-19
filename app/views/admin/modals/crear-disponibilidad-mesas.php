<section id="modal-crear-disponibilidad" class="modal-overlay">
    <div class="modal-content">
        <h2>Configurar Disponibilidad de Mesas</h2>
        <form id="form-crear-disponibilidad" class="text-start">
            <div class="row">
                <div class="mb-3">
                    <label class="form-label">Fecha</label>
                    <input type="date" id="disponibilidad-fecha" name="fecha" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Cantidad de mesas</label>
                    <input type="number" id="disponibilidad-cantidad" name="cantidad" class="form-control" min="1" max="50" required>
                    <small class="form-text text-muted">Ingrese el número de mesas disponibles (1-50)</small>
                </div>
            </div>
            <div class="alert alert-info" role="alert">
                <i class="bi bi-info-circle"></i> Al crear la disponibilidad, se activarán automáticamente las mesas del 1 hasta la cantidad indicada.
            </div>
            <div id="alerta-reservas" class="alert alert-warning d-none" role="alert">
                <i class="bi bi-exclamation-triangle"></i> No se puede modificar la disponibilidad porque ya existen reservas activas para esta fecha.
            </div>
            <div class="d-flex flex-wrap gap-2 mt-3">
                <button type="submit" id="btn-guardar-disponibilidad" class="btn btn-primary">
                    <i class="bi bi-save"></i> Guardar
                </button>
                <button type="button" id="btn-cancelar-disponibilidad" class="btn btn-secondary" onclick="document.getElementById('modal-crear-disponibilidad').classList.remove('active')">
                    <i class="bi bi-x-circle"></i> Cancelar
                </button>
            </div>
        </form>
    </div>
</section>