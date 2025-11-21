<!DOCTYPE html>
<html lang="es">
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
    <link rel="stylesheet" href="/app/views/user/user-style.css">
</head>
<body>

    <!--Header / Navbar-->
    <?php include 'sections/Navbar.php'; ?>

    <!--Contenido de la pagina-->
    <main>
        <!--Principal-->
        <?php include 'sections/Principal.php'; ?>
        
        <!--Reservaciones-->
        <?php include 'sections/Reservas.php'; ?>
        
        <!--Promociones-->
        <?php include 'sections/Promociones.php'; ?>
        
        <!--Eventos-->
        <?php include 'sections/Eventos.php'; ?>
        
        <!--Menu-->
        <?php include 'sections/Menu.php'; ?>
        
        <!--Nosotros / Redes Sociales-->
        <?php include 'sections/Nosotros.php'; ?>
    </main>

    <!--Footer-->
    <?php include 'sections/Footer.php'; ?>
    
    <!--Link Bootstrap JS-->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
    <!--Scripts JS-->
    <script src="/app/views/user/js/reservas.js"></script>
    <script src="/app/views/user/js/promociones.js"></script>
    <script src="/app/views/user/js/eventos.js"></script>
    <script src="/app/views/user/js/menu.js"></script>
</body>
</html>
