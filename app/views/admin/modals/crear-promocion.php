<section id="modal-crear-promocion" class="modal-overlay">
    <div class="modal-content">
        <h2>Nueva Promoción</h2>
        <form id="form-crear-promocion" class="text-start">
            <div class="row">
                <div class="col-md-6">
                    <h3>Información general</h3>
                    <div class="mb-3">
                        <label class="form-label">Nombre</label>
                        <input type="text" id="nombre" class="form-control" placeholder="Ej. 2x1 en bebidas">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Descripción</label>
                        <input type="text" id="descripcion" class="form-control" placeholder="Describe la promoción...">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Productos incluidos</label>
                        <select id="productos" class="form-select" multiple size="5">
                            <!-- Los productos se cargarán dinámicamente -->
                        </select>
                        <small class="text-muted">Mantén presionado Ctrl/Cmd para seleccionar múltiples productos</small>
                    </div>
                </div>
                <div class="col-md-6">
                    <h3>Vigencia</h3>
                    <div class="row">
                        <div class="col-md-6">
                            <label class="form-label">Fecha inicio</label>
                            <input type="date" id="fechaInicio" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Fecha fin</label>
                            <input type="date" id="fechaFin" class="form-control">
                        </div>
                    </div>
                    <div class="mb-3 mt-2">
                        <label class="form-label">Estado</label>
                        <select id="estado" class="form-select">
                            <option disabled selected>Selecciona una opción</option>
                            <option value="Disponible">Disponible</option>
                            <option value="No disponible">No disponible</option>
                        </select>
                    </div>
                    <h3>Imagen</h3>
                    <input type="file" id="imagen" class="form-control">
                </div>
            </div>
            <div class="d-flex gap-2 mt-3">
                <button id="btn-guardar-promocion" class="btn btn-sm">Guardar</button>
                <button id="btn-cancelar-promocion" class="btn btn-sm">Cancelar</button>
            </div>
        </form>
    </div>
</section>
