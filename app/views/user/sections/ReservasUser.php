<!--Elementos de las reservaciones-->
<section id="reservar" class="reservar">
    <div class="side-left">
        <h1>Reservaciones</h1>
        <p>Importante: Las reservaciones no pueden modificarse.
            Si necesitas cancelar, envíanos un mensaje por WhatsApp al 7721698550.
            ¡Gracias por tu comprensión!
        </p>
        <!--Formulario-->
        <form id="form-reserva" class="text-start reservar">
            <!--Inputs con detalles del cliente-->
            <h2>Detalles del cliente</h2>
            <div class="mb-3">
                <label for="nombre" class="form-label">Nombre Completo</label>
                <div class="input-group flex-nowrap">
                    <span class="input-group-text" id="nombre-addon">
                        <i class="bi bi-person" aria-hidden="true"></i>
                        <span class="visually-hidden">Nombre Completo</span>
                    </span>
                    <input type="text" class="form-control" id="nombre" name="nombre" placeholder="Ej. Juan Peréz" aria-describedby="nombre-addon" required>
                </div>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Correo electrónico</label>
                <div class="input-group flex-nowrap">
                    <span class="input-group-text" id="correo-addon">
                        <i class="bi bi-envelope-at" aria-hidden="true"></i>
                        <span class="visually-hidden">Correo electrónico</span>
                    </span>
                    <input type="email" class="form-control" id="email" name="email" placeholder="tucorreo@gmail.com" aria-describedby="correo-addon" required>
                </div>
            </div>
            <div class="mb-3">
                <label for="telefono" class="form-label">Teléfono celular</label>
                <div class="input-group flex-nowrap">
                    <span class="input-group-text" id="telefono-addon">
                        <i class="bi bi-phone" aria-hidden="true"></i>
                        <span class="visually-hidden">Teléfono celular</span>
                    </span>
                    <input type="tel" class="form-control" id="telefono" name="telefono" placeholder="Ej. 7721698550" aria-describedby="telefono-addon" required>
                </div>
            </div>

            <!--Inputs con detalles de la reservación-->
            <h2>Detalles de la reservación</h2>
            <div class="input-group mb-3">
                <label for="personas" class="form-label">Número de personas</label>
                <div class="input-group flex-nowrap">
                    <span class="input-group-text" id="personas-addon">
                        <i class="bi bi-people" aria-hidden="true"></i>
                        <span class="visually-hidden">Número de personas</span>
                    </span>
                    <select class="form-select" id="personas" name="personas" aria-describedby="personas-addon" required>
                        <option selected disabled value="">Selecciona una opción</option>
                        <option value="1">1 persona</option>
                        <option value="2">2 personas</option>
                        <option value="3">3 personas</option>
                        <option value="4">4 personas</option>
                        <option value="5">5 personas</option>
                        <option value="6">6 personas</option>
                        <option value="7">7 personas</option>
                        <option value="8">8 personas</option>
                        <option value="9">9 personas</option>
                        <option value="10">10 personas</option>
                    </select>
                </div>
            </div>
            <div class="mb-3">
                <label for="fecha" class="form-label">Fecha de la reservación</label>
                <div class="input-group flex-nowrap">
                    <span class="input-group-text" id="fecha-addon">
                        <i class="bi bi-calendar-event" aria-hidden="true"></i>
                        <span class="visually-hidden">Fecha de la reservación</span>
                    </span>
                    <input type="date" class="form-control" id="fecha" name="fecha" aria-describedby="fecha-addon" min="<?php echo date('Y-m-d'); ?>" required>
                </div>
            </div>
            <div class="mb-3">
                <label for="id_mesa" class="form-label">Mesa</label>
                <div class="input-group flex-nowrap">
                    <span class="input-group-text" id="mesa-addon">
                        <i class="bi bi-table" aria-hidden="true"></i>
                        <span class="visually-hidden">Mesa</span>
                    </span>
                    <select class="form-select" id="id_mesa" name="id_mesa" aria-describedby="mesa-addon" required>
                        <option selected disabled value="">Primero selecciona una fecha</option>
                    </select>
                </div>
                <div class="alert alert-info d-flex align-items-center mt-2" role="alert">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="me-2">
                        <circle cx="12" cy="12" r="10"></circle>
                        <line x1="12" y1="16" x2="12" y2="12"></line>
                        <line x1="12" y1="8" x2="12.01" y2="8"></line>
                    </svg>
                    <span>Selecciona una fecha para ver las mesas disponibles.</span>
                </div>
            </div>
            <div class="mb-3">
                <label for="hora" class="form-label">Hora de la reservación</label>
                <div class="input-group flex-nowrap">
                    <span class="input-group-text" id="hora-addon">
                        <i class="bi bi-clock" aria-hidden="true"></i>
                        <span class="visually-hidden">Hora de la reservación</span>
                    </span>
                        <input type="time" class="form-control" id="hora" name="hora" aria-describedby="hora-addon" required disabled>
                </div>
                <div class="alert alert-info d-flex align-items-center mt-2" role="alert" id="hora-help">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="me-2">
                        <circle cx="12" cy="12" r="10"></circle>
                        <line x1="12" y1="16" x2="12" y2="12"></line>
                        <line x1="12" y1="8" x2="12.01" y2="8"></line>
                    </svg>
                    <span>Selecciona una fecha para ver el horario disponible.</span>
                </div>
            </div>
            <button type="submit" class="btn">Reservar</button>
        </form>
    </div>
    <div class="side-right">
        <img src="/public/images/image-1.jpg" alt="Rancho La Joya">
    </div>
</section>
