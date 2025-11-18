<div id="mesas" class="d-none">
<div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
        <h1 class="title">
            Mesas
            <i class="bi bi-info-circle ms-2" data-bs-toggle="tooltip"
            title="Configura el número de mesas disponibles por día."></i>
        </h1>
        <button id="btn-create-mesas" class="btn btn-sm" onclick="document.getElementById('modal-create-mesas').classList.add('active')">
            <i class="bi bi-plus-circle" data-bs-toggle="tooltip"
            title="Agregar Disponibilidad"></i>
        </button>
    </div>
    <div class="table-responsive">
        <table class="table table-striped table-hover align-middle">
            <thead>
                <tr>
                    <th>Numero</th>
                    <th>Cliente</th>
                    <th>Fecha</th>
                    <th>Hora</th>
                    <th class="text-center">Acciones</th>
                </tr>
            </thead>
            <tbody id="mesas-tbody">
                <!-- filas generadas por JS: fecha | cantidad -->
            </tbody>
        </table>
    </div>
</div>