<section id="reservar" class="reservar">
    <div class="side-left">
        <h1>Reservaciones</h1>
        <p>Importante: Las reservaciones no pueden modificarse.
            Si necesitas cancelar, envíanos un mensaje por WhatsApp al 7721698550.
            ¡Gracias por tu comprensión!
        </p>
        <form id="form-reserva" class="text-start reservar">
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
                <div class="col-12 mt-2">
                    <div class="alert alert-info" role="alert">
                        <i class="bi bi-info-circle"></i> Selecciona una fecha para ver las mesas disponibles.
                    </div>
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
                <div class="col-12 mt-2">
                    <div class="alert alert-info" role="alert">
                        <i class="bi bi-info-circle"></i> Selecciona una fecha para ver el horario disponible. Solo se permiten reservas cada 30 minutos (ej: 11:00, 11:30, 12:00).
                    </div>
                </div>
            </div>
            <button type="submit" class="btn">Reservar</button>
        </form>
    </div>
    <div class="side-right">
        <img src="/public/images/image-1.jpg" alt="Rancho La Joya">
    </div>
</section>
