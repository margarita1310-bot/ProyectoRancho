<!--Inicio-->
<div id="inicio" class="d-none">
    <h1 class="title">Panel General</h1>

    <!-- Alert de disponibilidad -->
    <div id="alert-disponibilidad" class="alert alert-warning d-none">
        ⚠ Aún no has configurado la disponibilidad del restaurante hoy.
        <button class="btn btn-dark btn-sm ms-3">Configurar</button>
    </div>

    <!-- Tarjetas -->
    <div class="row g-3">
        <!-- Promociones activas -->
        <div class="col-md-3">
            <div class="card card-resumen">
                <div class="card-body text-center">
                    <h3 id="cant-promos">0</h3>
                    <p class="card-title">Promociones activas</p>
                </div>
            </div>
        </div>

        <!-- Eventos próximos -->
        <div class="col-md-3">
            <div class="card card-resumen">
                <div class="card-body text-center">
                    <h3 id="cant-eventos">0</h3>
                    <p class="card-title">Eventos próximos</p>
                </div>
            </div>
        </div>

        <!-- Reservas pendientes -->
        <div class="col-md-3">
            <div class="card card-resumen">
                <div class="card-body text-center">
                    <h3 id="cant-reservas">0</h3>
                    <p class="card-title">Reservas pendientes</p>
                </div>
            </div>
        </div>

        <!-- Mesas -->
        <div class="col-md-3">
            <div class="card card-resumen">
                <div class="card-body text-center">
                    <h3 id="mesas-disponibles">0</h3>
                    <p class="card-title">Mesas disponibles</p>
                </div>
            </div>
        </div>
    </div>
</div>