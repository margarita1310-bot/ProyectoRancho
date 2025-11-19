<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rancho la Joya</title>
    <!--Link fuentes de Google-->
    <link href="https://fonts.googleapis.com/css2?family=Alfa+Slab+One&family=Cabin:ital,wght@0,400..700;1,400..700&family=Playfair+Display:ital,wght@0,400..900;1,400..900&display=swap" rel="stylesheet">
    <!--Link Bootstrap CSS-->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <!--Link bootstrap ICONOS-->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <!--Link Estilos-->
    <link rel="stylesheet" href="/style.css">
</head>
<body>

    <!--Elementos del header-->
    <header>
        <nav class="navbar navbar-expand-lg">
            <div class="container-md">
                <!--Icono de rancho-->
                <a id="principal" class="navbar-brand d-flex align-items-center" href="principal.html">
                    <img src="/images/logo.jpg" alt="Logo" width="48" height="48" class="me-2">
                    Rancho La Joya
                </a>
                <!--Menú responsivo-->
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarTogglerDemo02" aria-controls="navbarTogglerDemo02" aria-expanded="false" aria-label="Toggle navigation">
                    <span><i class="bi bi-list"></i></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarTogglerDemo02">
                    <!--Enlaces a otras paginas-->
                    <ul class="navbar-nav mx-auto mb-2 mb-lg-0">
                        <li class="nav-item ">
                            <a class="nav-link" href="#promociones">Promociones</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#eventos">Eventos</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#menu">Menú</a>
                        </li>
                    </ul>
                    <!--Botón reservar-->
                    <a href="#reservar">
                        <button class="btn">Reservar una mesa</button>
                    </a>
                </div>
            </div>
        </nav>
    </header>

    <!--Contenido de la pagina-->
    <main>

        <!--Principal-->
        <section class="img-principal">
            <div class="target">
                <h1>Rancho La Joya</h1>
                <p>
                    Bienvenido a Rancho La Joya.
                    Elige tu fecha, reserva tu espacio y prepárate para desconectar.
                    Así de fácil.
                </p>
                <a href="#reservar">
                    <button class="btn">¡Reserva ahora!</button>
                </a>    
            </div>
        </section>
        
        <!--Elementos de las reservaciones-->
        <section id="reservar" class="reservar">
            <div class="side-left">
                <h1>Reservaciones</h1>
                <p>Importante: Las reservaciones no pueden modificarse.
                    Si necesitas cancelar, envíanos un mensaje por WhatsApp al 7721698550.
                    ¡Gracias por tu comprensión!
                </p>
                <!--Formulario-->
                <form class="text-start reservar">
                    <!--Inputs con detalles del cliente-->
                    <h2>Detalles del cliente</h2>
                    <div class="mb-3">
                        <label for="InputNombre" class="form-label">Nombre Completo</label>
                        <div class="input-group flex-nowrap">
                            <span class="input-group-text" id="nombre-addon">
                                <i class="bi bi-person" aria-hidden="true"></i>
                                <span class="visually-hidden">Nombre Completo</span>
                            </span>
                            <input type="text" class="form-control" id="InputNombre" placeholder="Ej. Juan Peréz" aria-describedby="nombre-addon">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="InputEmail" class="form-label">Correo electrónico</label>
                        <div class="input-group flex-nowrap">
                            <span class="input-group-text" id="correo-addon">
                                <i class="bi bi-envelope-at" aria-hidden="true"></i>
                                <span class="visually-hidden">Correo electrónico</span>
                            </span>
                            <input type="email" class="form-control" id="InputEmail" placeholder="tucorreo@gmail.com" aria-describedby="correo-addon">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="InputTelefono" class="form-label">Teléfono celular</label>
                        <div class="input-group flex-nowrap">
                            <span class="input-group-text" id="telefono-addon">
                                <i class="bi bi-phone" aria-hidden="true"></i>
                                <span class="visually-hidden">Teléfono celular</span>
                            </span>
                            <input type="tel" class="form-control" id="InputTelefono" placeholder="Ej. 7721698550" aria-describedby="telefono-addon">
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
                            <select class="form-select" id="InputPersonas" aria-describedby="personas-addon">
                                <option selected disabled>Selecciona una opción</option>
                                <option value="1">2 personas</option>
                                <option value="2">3 personas</option>
                                <option value="3">4 personas</option>
                                <option value="4">5 personas</option>
                                <option value="5">+6 personas</option>
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
                            <input type="date" class="form-control" id="InputFecha" aria-describedby="fecha-addon">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="InputHora" class="form-label">Hora de la reservación</label>
                        <div class="input-group flex-nowrap">
                            <span class="input-group-text" id="hora-addon">
                                <i class="bi bi-clock" aria-hidden="true"></i>
                                <span class="visually-hidden">Hora de la reservación</span>
                            </span>
                            <input type="time" class="form-control" id="InputHora" aria-describedby="hora-addon">
                        </div>
                    </div>
                    <button type="submit" class="btn">Reservar</button>
                </form>
            </div>
            <div class="side-right">
                <img src="/images/image-1.jpg" alt="">
            </div>
        </section>

        <!--Elementos de las promociones-->
        <section id="promociones" class="promociones">
            <div class="side-left">
                <img src="/images/image-1.jpg" alt="">
            </div>
            <div class="side-right">
                <h1>Promociones</h1>
                <p>Importante: Las promociones están sujetas a cambios sin previo aviso.
                    <br>Consulta nuestras redes sociales para conocer las ofertas vigentes.
                </p>
                <div class="btn-group" role="group" aria-label="Basic outlined example">
                    <button type="button" class="btn">Lunes</button>
                    <button type="button" class="btn">Martes</button>
                    <button type="button" class="btn">Miercoles</button>
                    <button type="button" class="btn">Jueves</button>
                    <button type="button" class="btn">Viernes</button>
                    <button type="button" class="btn">Sabado</button>
                    <button type="button" class="btn">Domingo</button>
                </div>

                <div class="card mb-3" style="max-width: 540px;">
                    <div class="row g-0">
                        <div class="col-md-4">
                            <img src="..." class="img-fluid rounded-start" alt="...">
                        </div>
                        <div class="col-md-8">
                            <div class="card-body">
                                <h5 class="card-title">Card title</h5>
                                <p class="card-text">This is a wider card with supporting text below as a natural lead-in to additional content. This content is a little bit longer.</p>
                                <p class="card-text"><small class="text-body-secondary">Last updated 3 mins ago</small></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!--Elemenos de los eventos-->
        <section id="eventos" class="eventos">
            <div class="side-left">
                <h1>Eventos</h1>
                <p>Las dinámicas, promociones y horarios del evento están sujetos a cambios sin previo aviso. Consulta nuestras redes sociales para información actualizada.</p>
                <div class="card" style="width: 18rem;">
                    <img src="..." class="card-img-top" alt="...">
                    <div class="card-body">
                        <h5 class="card-title">Halloween Party</h5>
                        <p class="card-text">Los Bros de Sinaloa, DJ Zeta, Herencia Sagrada. ¡Ven con tu mejor disfraz!</p>
                        <a href="#" class="btn btn-primary">Dispnible hasta 10/11/2025</a>
                    </div>
                </div>
            </div>

            <div class="side-right">
                <img src="/images/image-1.jpg" alt="">
            </div>
        </section>

        <!--Elementos de el menu-->
        <section id="menu" class="menu">
            <div class="contenedor-menu">
                <h2>Menú</h2>
                <div class="nav-menu">
                    <a href="#" class="active">Bebidas</a>
                    <a href="#">Comida</a>
                </div>
                <h3>Botellas</h3>
                <div class="items">
                    <div class="item">
                        <h3>Rancho Azul</h3>
                        <span>$200</span>
                    </div>
                    <div class="item">
                        <h3>Rancho Escondido sabores</h3>
                        <span>$230</span>
                    </div>
                    <div class="item">
                        <h3>Centenario tequila</h3>
                        <span>$500</span>
                    </div>
                    <div class="item">
                        <h3>José Cuervo especial</h3>
                        <span>$450</span>
                    </div>
                    <div class="item">
                        <h3>José Cuervo tradicional</h3>
                        <span>$900</span>
                    </div>
                    <div class="item">
                        <h3>Don Julio</h3>
                        <span>$1500</span>
                    </div>
                    <div class="item">
                        <h3>Bacardi blanco</h3>
                        <span>$400</span>
                    </div>
                    <div class="item">
                        <h3>Bacardi sabores</h3>
                        <span>$400</span>
                    </div>
                    <div class="item">
                        <h3>Red Label</h3>
                        <span>$500</span>
                    </div>
                    <div class="item">
                        <h3>Buchanan's</h3>
                        <span>$1300</span>
                    </div>
                </div>
                <h3>Cervezas</h3>
                <div class="items">
                    <div class="item">
                        <h3>Variedad de cervezas de 1/2</h3>
                        <span>$30</span>
                    </div>
                    <div class="item">
                        <h3>Variedad de caguamas</h3>
                        <span>$50</span>
                    </div>
                    <div class="item">
                        <h3>Micheladas 1/2 litro</h3>
                        <span>$50</span>
                    </div>
                    <div class="item">
                        <h3>Micheladas litro</h3>
                        <span>$80</span>
                    </div>
                    <div class="item">
                        <h3>Cheladas 1/2 litro</h3>
                        <span>$50</span>
                    </div>
                    <div class="item">
                        <h3>Cheladas litro</h3>
                        <span>$80</span>
                    </div>
                </div>
                <h3>Cocteles</h3>
                <div class="items">
                    <div class="item">
                        <h3>Azulitos</h3>
                        <span>1/2 $50<br>1L $80</span>
                    </div>
                    <div class="item">
                        <h3>Mojitos</h3>
                        <span>1/2 $50<br>1L $80</span>
                    </div>
                    <div class="item">
                        <h3>Pantera rosa</h3>
                        <span>1/2 $50<br>1L $80</span>
                    </div>
                    <div class="item">
                        <h3>Sangria</h3>
                        <span>1/2 $50<br>1L $100</span>
                    </div>
                    <div class="item">
                        <h3>Quemadita</h3>
                        <span>$60</span>
                    </div>
                    <div class="item">
                        <h3>Paloma</h3>
                        <span>$60</span>
                    </div>
                </div>
                <h3>Cubas</h3>
                <div class="items">
                    <div class="item">
                        <h3>Tequila y Vodka</h3>
                        <span>$80</span>
                    </div>
                    <div class="item">
                        <h3>Whisky</h3>
                        <span>$80</span>
                    </div>
                </div>
                <h3>Shots</h3>
                <div class="items">
                    <div class="item">
                        <h3>Vodka, Tequila y Whisky</h3>
                        <span>$50</span>
                    </div>
                </div>
            </div>
        </section>

        <!--Sección de redes sociales-->
        <section id="nosotros" class="nosostros">
            <!--Links a redes-->
            <div class="side-left">
                <h1>Siguenos</h1>
                <p class="usuario">@Rancho La Joya</p>
                <p>Entérate de nuestras promos en bebidas y eventos especiales.
                    <br>Síguenos en redes sociales y sé parte del plan.
                    <br>Aquí siempre hay algo que celebrar… ¡y tú eres el invitado especial!
                </p>
                <div class="redes">
                    <a href="" class="whatsapp"><i class="bi bi-whatsapp"></i></a>
                    <a href="" class="instagram"><i class="bi bi-instagram"></i></a>
                    <a href="" class="facebook"><i class="bi bi-facebook"></i></a>
                </div>
            </div>
            <!--Galeria de imagenes-->
            <div class="side-right">
                <div class="galeria">
                    <img src="" alt="">
                    <img src="" alt="">
                    <img src="" alt="">
                    <img src="" alt="">
                    <img src="" alt="">
                    <img src="" alt="">
                    <img src="" alt="">
                    <img src="" alt="">
                    <img src="" alt="">
                </div>
            </div>
        </section>
    </main>

    <!--Elementos del footer-->
    <footer>
        <div class="info">
            <!--Enlaces a paginas-->
            <div class="side-left">
                <a id="principal" class="navbar-brand d-flex align-items-center" href="principal.html">
                    <img src="/images/logo.jpg" alt="Logo" width="72" height="72" class="me-2">
                    <h1>Rancho La Joya</h1>
                </a>
                <ul class="nav">
                    <li class="nav-item ">
                        <a class="nav-link" href="promociones.html">Promociones</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="eventos.html">Eventos</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="menu.html">Menú</a>
                    </li>
                </ul>
                <p>Derechos reservados &copy;Rancho La Joya</p>
            </div>

            <!--Horario-->
            <div class="side-right">
                <h2>Horario</h2>
                <ul>
                    <li><strong>Lunes</strong> 11am-10pm</li>
                    <li><strong>Martes</strong> 10am-10pm</li>
                    <li><strong>Miércoles</strong> 10:30am-10pm</li>
                    <li><strong>Jueves</strong> 11am-11:30pm</li>
                    <li><strong>Viernes</strong> 11am-10pm</li>
                    <li><strong>Sábado</strong> Cerrado</li>
                    <li><strong>Domingo</strong> 3pm-9pm</li>
                </ul>
            </div>
            
            <!--Mapa-->
            <div class="mapa">
                <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d59800.0719073562!2d-99.20514638448975!3d20.485535137682106!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x85d15f26cc7ea4bb%3A0xebb331359764f313!2sRancho%20la%20JOYA!5e0!3m2!1ses!2smx!4v1761843440142!5m2!1ses!2smx" width="320" height="200" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                <p>Gral. Lázaro Cárdenas, Col. La Joya, 42325 Ixmiquilpan, Hgo.</p>
            </div>
        </div>
    </footer>
    <!--Link Boostrap JS-->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
    <!--Link JS-->
</body>
</html>