<!-- Modal Perfil Administrador -->
<div id="modal-perfil-admin" class="modal-overlay">>
    <div class="modal-content">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2>Mi Perfil</h2>
            <button type="button" class="btn-close btn-close-white" onclick="cerrarModalPerfil()"></button>
        </div>
        
        <form id="form-perfil-admin">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="perfil-nombre" class="form-label">Nombre completo</label>
                    <input type="text" class="form-control" id="perfil-nombre" name="nombre" readonly>
                </div>
                
                <div class="col-md-6 mb-3">
                    <label for="perfil-correo" class="form-label">Correo electrónico</label>
                    <input type="email" class="form-control" id="perfil-correo" name="correo" readonly>
                </div>
            </div>
            
            <hr class="my-4" style="border-color: #854507;">
            
            <div class="text-center">
                <p class="text-muted mb-3">Para modificar tus datos, contacta al administrador del sistema</p>
                <button type="button" class="btn btn-secondary" onclick="cerrarModalPerfil()">Cerrar</button>
            </div>
        </form>
    </div>
</div>
