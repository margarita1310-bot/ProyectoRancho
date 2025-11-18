<button id="btn-menu" class="toggle-btn">
    <i class="bi bi-list"></i>
</button>

<div class="sidebar p-3">
    <h4 class="mb-4">Panel de Administración</h4>
    <hr>
    <ul class="nav flex-column">
        <li class="nav-item">
            <a href="#" class="nav-link active" onclick="mostrarContenido('inicio')">
                <i class="bi bi-speedometer2 me-2"></i> Inicio
            </a>
        </li>
        <li class="nav-item">
            <a href="#" class="nav-link" onclick="mostrarContenido('promocion')">
                <i class="bi bi-percent me-2"></i> Promociones
            </a>
        </li>
        <li class="nav-item">
            <a href="#" class="nav-link" onclick="mostrarContenido('evento')">
                <i class="bi bi-calendar-event me-2"></i> Eventos
            </a>
        </li>
        <li class="nav-item">
            <a href="#" class="nav-link" onclick="mostrarContenido('menu')">
                <i class="bi bi-list-ul me-2"></i> Menú
            </a>
        </li>
        <li class="nav-item">
            <a href="#" class="nav-link" onclick="mostrarContenido('reservas')">
                <i class="bi bi-journal-check me-2"></i> Reservas
            </a>
        </li>
        <li class="nav-item">
            <a href="#" class="nav-link" onclick="mostrarContenido('mesas')">
                <i class="bi bi-grid-3x3 me-2"></i> Mesas
            </a>
        </li>
    </ul>
    <hr>
    <div class="dropdown">
        <a href="#" class="d-flex align-items-center link-dark text-decoration-none dropdown-toggle" id="dropdownUser2" data-bs-toggle="dropdown" aria-expanded="false">
            <img src="/public/images/logo.jpg" alt="" width="32" height="32" class="rounded-circle me-2">
            <strong>Rancho La Joya</strong>
        </a>
        <ul class="dropdown-menu text-small shadow" aria-labelledby="dropdownUser2">
            <li><a class="dropdown-item" href="#">Perfil</a></li>
            <li><hr class="dropdown-divider"></li>
            <li><a class="dropdown-item" href="../../app/controllers/AdminController.php?action=logout">Cerrar sesión</a></li>
        </ul>
    </div>
</div>