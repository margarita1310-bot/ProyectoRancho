<section id="modal-editar-producto" class="modal-overlay">
    <div class="modal-content">
        <h2>Editar Producto</h2>
        <form id="form-editar-producto" class="text-start">
            <input type="hidden" id="id">
            <div class="row">
                <div class="mb-3">
                    <label class="form-label">Producto</label>
                    <input type="text" id="nombre" class="form-control" placeholder="Ej. Bacardi">
                </div>
                <div class="mb-3">
                    <label class="form-label">Precio</label>
                    <input type="number" id="precio" class="form-control" placeholder="Ej. $200">
                </div>
                <div class="mb-3">
                    <label class="form-label">Categoria</label>
                    <select class="form-select" id="categoria">
                        <option selected disabled>Selecciona una opción</option>
                        <option value="Botellas">Botellas</option>
                        <option value="Shots">Shots</option>
                        <option value="Cubas">Cubas</option>
                        <option value="Cervezas">Cervezas</option>
                        <option value="Cocteles">Cocteles</option>
                        <option value="Tacos">Tacos</option>
                    </select>
                </div>
            </div>
            <div class="d-flex flex-wrap gap-2 mt-3">
                <button id="btn-editar-producto" class="btn btn-sm">Actualizar</button>
                <button id="btn-cancelar-editar-producto" class="btn btn-sm">Cancelar</button>
            </div>
        </form>
    </div>
</section>