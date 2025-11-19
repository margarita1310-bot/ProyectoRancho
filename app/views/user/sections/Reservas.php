<!--Elementos de las reservaciones-->
<section id="reservar" class="reservar">
    <div class="side-left">
        <h1>Reservaciones</h1>
        <p>Importante: Las reservaciones no pueden modificarse.
            Si necesitas cancelar, envíanos un mensaje por WhatsApp al 7721698550.
            ¡Gracias por tu comprensión!
        </p>
        <!--Formulario-->
        <form id="form-reservar" class="text-start reservar">
            <!--Inputs con detalles del cliente-->
            <h2>Detalles del cliente</h2>
            <div class="mb-3">
                <label for="InputNombre" class="form-label">Nombre Completo</label>
                <div class="input-group flex-nowrap">
                    <span class="input-group-text" id="nombre-addon">
                        <i class="bi bi-person" aria-hidden="true"></i>
                        <span class="visually-hidden">Nombre Completo</span>
                    </span>
                    <input type="text" class="form-control" id="InputNombre" placeholder="Ej. Juan Peréz" aria-describedby="nombre-addon" required>
                </div>
            </div>
            <div class="mb-3">
                <label for="InputEmail" class="form-label">Correo electrónico</label>
                <div class="input-group flex-nowrap">
                    <span class="input-group-text" id="correo-addon">
                        <i class="bi bi-envelope-at" aria-hidden="true"></i>
                        <span class="visually-hidden">Correo electrónico</span>
                    </span>
                    <input type="email" class="form-control" id="InputEmail" placeholder="tucorreo@gmail.com" aria-describedby="correo-addon" required>
                </div>
            </div>
            <div class="mb-3">
                <label for="InputTelefono" class="form-label">Teléfono celular</label>
                <div class="input-group flex-nowrap">
                    <span class="input-group-text" id="telefono-addon">
                        <i class="bi bi-phone" aria-hidden="true"></i>
                        <span class="visually-hidden">Teléfono celular</span>
                    </span>
                    <input type="tel" class="form-control" id="InputTelefono" placeholder="Ej. 7721698550" aria-describedby="telefono-addon" required>
                </div>
            </div>

            <!--Inputs con detalles de la reservación-->
            <h2>Detalles de la reservación</h2>
            <div class="input-group mb-3">
                <label for="InputPersonas" class="form-label">Número de personas</label>
                <div class="input-group flex-nowrap">
                    <span class="input-group-text" id="personas-addon">
                        <i class="bi bi-people" aria-hidden="true"></i>
                        <span class="visually-hidden">Número de personas</span>
                    </span>
                    <select class="form-select" id="InputPersonas" aria-describedby="personas-addon" required>
                        <option selected disabled>Selecciona una opción</option>
                        <option value="2">2 personas</option>
                        <option value="3">3 personas</option>
                        <option value="4">4 personas</option>
                        <option value="5">5 personas</option>
                        <option value="6">+6 personas</option>
                    </select>
                </div>
            </div>
            <div class="mb-3">
                <label for="InputFecha" class="form-label">Fecha de la reservación</label>
                <div class="input-group flex-nowrap">
                    <span class="input-group-text" id="fecha-addon">
                        <i class="bi bi-calendar-event" aria-hidden="true"></i>
                        <span class="visually-hidden">Fecha de la reservación</span>
                    </span>
                    <input type="date" class="form-control" id="InputFecha" aria-describedby="fecha-addon" required>
                </div>
            </div>
            <div class="mb-3">
                <label for="InputHora" class="form-label">Hora de la reservación</label>
                <div class="input-group flex-nowrap">
                    <span class="input-group-text" id="hora-addon">
                        <i class="bi bi-clock" aria-hidden="true"></i>
                        <span class="visually-hidden">Hora de la reservación</span>
                    </span>
                    <input type="time" class="form-control" id="InputHora" aria-describedby="hora-addon" required>
                </div>
            </div>
            <button type="submit" class="btn">Reservar</button>
        </form>
    </div>
    <div class="side-right">
        <img src="/public/images/image-1.jpg" alt="Rancho La Joya">
    </div>
</section>
