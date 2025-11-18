<section id="modal-crear-evento" class="modal-overlay">
    <div class="modal-content">
        <h2>Nuevo Evento</h2>
        <form id="form-crear-evento" class="text-start">
            <div class="row">
                <div class="col-md-6">
                    <h3>Información general</h3>
                    <div class="mb-3">
                        <label class="form-label">Nombre de el evento</label>
                        <input type="text" class="form-control" id="nombre" placeholder="Ej. Fiesta temática">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Descripción</label>
                        <input type="text" class="form-control" id="descripcion" placeholder="Describe el evento...">
                    </div>
                </div>
                <div class="col-md-6">
                    <h3>Fecha y hora</h3>
                    <div class="mb-3">
                        <label class="form-label">Fecha</label>
                        <input type="date" class="form-control" id="fecha">
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Hora de inicio</label>
                                <input type="time" class="form-control" id="horaInicio">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Hora de finalización</label>
                                <input type="time" class="form-control" id="horaFin">
                            </div>
                        </div>
                    </div>
                    <h3>Imagen</h3>
                    <input type="file" id="imagen" class="form-control">
                </div>
            </div>
            <div class="d-flex flex-wrap gap-2 mt-3">
                <button id="btn-guardar-evento" class="btn">Guardar</button>
                <button id="btn-cancelar-evento" class="btn">Cancelar</button>
            </div>
        </form>
    </div>
</section>