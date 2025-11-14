<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio de Sesión - Rancho La Joya</title>
    <!--Link fuentes de Google-->
    <link href="https://fonts.googleapis.com/css2?family=Alfa+Slab+One&family=Cabin:ital,wght@0,400..700;1,400..700&family=Playfair+Display:ital,wght@0,400..900;1,400..900&display=swap" rel="stylesheet">
    <!--Link Bootstrap CSS-->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <!--Link bootstrap ICONOS-->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <!--Link Estilos-->
    <link rel="stylesheet" href="/app/views/login/login-style.css">
</head>
<body>
    <main>
        <!--Elementos de las reservaciones-->
        <section id="login" class="login">
            <div class="side-left">
                <img src="/public/images/logo.jpg" alt="">
            </div>
            <div class="side-right">
                <h1>Iniciar Sesión</h1>
                <p>
                    Estás ingresando al panel de gestión de El Bar del Rancho La Joya. Este espacio está reservado para personal autorizado. Por favor, procede con responsabilidad.
                </p>
                <!--Formulario-->
                <form class="text-start" id="login-form" method="POST" action="/app/controllers/LoginController.php?action=autenticar">
                    <!--Inputs-->
                    <div>
                        <div class="mb-3">
                            <label for="correo" class="form-label">Correo:</label>
                            <input type="email" class="form-control" id="correo" name="correo" required>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Contraseña:</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>
                        <button type="submit">Ingresar</button>
                        <?php if (!empty($error)): ?>
                            <p class="text-danger" mt-3 text-center><?= $error ?></p>
                        <?php endif; ?>
                    </div>
                </form>
            </div>
        </section>
    </main>
</body>
</html>