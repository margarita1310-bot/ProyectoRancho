<section id="modal-editar-evento" class="modal-overlay">
    <div class="modal-content">
        <h2>Editar Evento</h2>
        <form id="form-editar-evento" class="text-start">
            <input type="hidden" id="id">
            <div class="row">
                <div class="col-md-6">
                    <h3>Información general</h3>
                    <div class="mb-3">
                        <label class="form-label">Nombre de el evento</label>
                        <input type="text" id="nombre" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Descripción</label>
                        <input type="text" id="descripcion" class="form-control">
                    </div>
                </div>
                <div class="col-md-6">
                    <h3>Fecha y hora</h3>
                    <div class="mb-3">
                        <label class="form-label">Fecha</label>
                        <input type="date" id="fecha" class="form-control">
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Hora de inicio</label>
                                <input type="time" id="horaInicio" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Hora de finalización</label>
                                <input type="time" id="horaFin" class="form-control">
                            </div>
                        </div>
                    </div>
                    <h3>Imagen</h3>
                    <input type="file" id="imagen" class="form-control">
                </div>
            </div>
            <div class="d-flex gap-2 mt-3">
                <button id="btn-editar-evento" class="btn btn-sm">Actualizar</button>
                <button id="btn-cancelar-editar-evento" class="btn btn-sm">Cancelar</button>
            </div>
        </form>
    </div>
</section>