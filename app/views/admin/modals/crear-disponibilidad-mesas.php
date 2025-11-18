<section id="modal-create-mesas" class="modal-overlay">
    <div class="modal-content">
        <h2>Configurar Mesas - Disponibilidad</h2>
        <form id="form-create-mesas" class="text-start">
            <div class="row">
                <div class="mb-3">
                    <label class="form-label">Fecha</label>
                    <input type="date" id="mesas-fecha" class="form-control" readonly>
                </div>
                <div class="mb-3">
                    <label class="form-label">Número de mesas</label>
                    <input type="number" id="mesas-cantidad" class="form-control" min="0" value="0">
                </div>
            </div>
            <div class="d-flex flex-wrap gap-2 mt-3">
                <button id="btn-guardar-mesas" class="btn btn-sm">Guardar</button>
                <button id="btn-cancelar-mesas" class="btn btn-sm btn-cancel">Cancelar</button>
            </div>
        </form>
        <p class="mt-2" style="font-size:12px;color:#fff;opacity:0.8">Nota: solo puedes agregar o actualizar la disponibilidad para el día de hoy.</p>
    </div>
</section>