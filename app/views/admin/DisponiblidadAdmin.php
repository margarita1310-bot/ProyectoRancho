<div id="mesa" class="d-none">
<div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
        <h1 class="title">
            Mesas
            <i class="bi bi-info-circle ms-2" data-bs-toggle="tooltip"
            title="Configura el número de mesas disponibles por día."></i>
        </h1>
        <button id="btn-crear-disponibilidad" class="btn btn-sm" onclick="document.getElementById('modal-crear-disponibilidad').classList.add('active')">
            <i class="bi bi-plus-circle me-2"></i>"Crear disponibilidad
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
                    <th>Estado</th>
                </tr>
            </thead>
            <tbody id="mesas-tbody">
                <!-- filas generadas por JS: fecha | cantidad -->
            </tbody>
        </table>
    </div>
</div>