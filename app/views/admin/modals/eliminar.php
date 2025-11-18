<section id="delete-overlay" class="delete-overlay d-none">
    <div class="eliminar-box">
        <h2 id="eliminar-title">Eliminar elemento</h2>
        <p id="eliminar-message">¿Estás seguro de eliminar este elemento? Esta acción no se puede deshacer.</p>

        <form id="eliminar-form">
            <input type="hidden" name="id" id="eliminar-id" value="">
            <input type="hidden" name="controller" id="eliminar-controller" value="">
            <input type="hidden" name="action" id="eliminar-action" value="eliminar">
        </form>

        <div class="d-flex flex-wrap gap-2 mt-3">
            <button id="btn-confirmar-eliminar" class="btn btn-sm" data-bs-toggle="tooltip">Confirmar</button>
            <button id="btn-cancelar-eliminar" class="btn btn-sm" data-bs-toggle="tooltip">Cancelar</button>
        </div>
    </div>
</section>