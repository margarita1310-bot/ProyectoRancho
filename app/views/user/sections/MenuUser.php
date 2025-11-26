<!--Elementos del menú-->
<section id="menu" class="menu py-5">
    <div class="container">
        <h2 class="text-center mb-4">Nuestro Menú</h2>
        
        <!-- Controles de vista -->
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
            <!-- Botones de filtro -->
            <div class="text-center flex-grow-1">
                <button id="btn-todos" class="btn btn-filter btn-outline-primary mx-2 active">Todos</button>
                <button id="btn-botellas" class="btn btn-filter btn-outline-primary mx-2">Botellas</button>
                <button id="btn-shots" class="btn btn-filter btn-outline-primary mx-2">Shots</button>
                <button id="btn-cubas" class="btn btn-filter btn-outline-primary mx-2">Cubas</button>
                <button id="btn-cervezas" class="btn btn-filter btn-outline-primary mx-2">Cervezas</button>
                <button id="btn-cocteles" class="btn btn-filter btn-outline-primary mx-2">Cocteles</button>
                <button id="btn-tacos" class="btn btn-filter btn-outline-primary mx-2">Tacos</button>
            </div>
            
            <!-- Toggle de vista completa -->
            <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" id="toggle-vista-completa" checked>
                <label class="form-check-label text-white" for="toggle-vista-completa">
                    Ver menú completo
                </label>
            </div>
        </div>
        
        <div id="menu-container" class="row">
            <!-- El menú se cargará dinámicamente aquí -->
            <p class="text-center w-100">Cargando menú...</p>
        </div>
    </div>
</section>
