<div class="d-flex justify-content-between align-items-center mb-3">
    <h1>
        Promociones
        <i class="bi bi-info-circle ms-2" data-bs-toggle="tooltip"
        title="Aquí podrás gestionar las promociones activas y crear nuevas."></i>
    </h1>
    <button id="btn-create-promociones" class="btn btn-sm" onclick="abrirModal('crear','promociones')">
        <i class="bi bi-plus-circle me-2"></i>Agregar promoción
    </button>
</div>
<div class="table-responsive">
    <table class="table table-striped table-hover align-middle">
        <thead>
            <tr>
                <th>Nombre</th>
                <th>Descripción</th>
                <th>Fecha inicio</th>
                <th>Fecha fin</th>
                <th>Estado</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td id="nombre-promocion"></td>
                <td id="descripcion-promocion"></td>
                <td id="fecha-inicio-promocion"></td>
                <td id="fecha-fin-promocion"></td>
                <td id="estado-promocion"></td>
            </tr>
        </tbody>
    </table>
</div>