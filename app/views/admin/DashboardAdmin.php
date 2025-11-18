<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administrador - Rancho la Joya</title>
    <!--Link fuentes de Google-->
    <link href="https://fonts.googleapis.com/css2?family=Alfa+Slab+One&family=Cabin:ital,wght@0,400..700;1,400..700&family=Playfair+Display:ital,wght@0,400..900;1,400..900&display=swap" rel="stylesheet">
    <!--Link Bootstrap CSS-->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <!--Link bootstrap ICONOS-->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <!--Link Estilos-->
    <link rel="stylesheet" href="/app/views/admin/admin-style.css">
</head>
<body>
    <div class="d-flex">
        <!--Side bar-->
        <?php include 'SidebarAdmin.php'; ?>
        
        <!--Contenido de la pagina-->
        <div class="content flex-grow-1 p-3">
            <!--Inicio-->
            <?php include 'IndexAdmin.php'; ?>
            <!--Promociones-->
            <?php include 'PromocionAdmin.php'; ?>
            <!--Eventos-->
            <?php include 'EventoAdmin.php'; ?>
            <!--Menu-->
            <?php include 'ProductoAdmin.php'; ?>
            <!--Reservas-->
            <?php include 'ReservaAdmin.php'; ?>
            <!--Mesas-->
            <?php include 'DisponibilidadAdmin.php'; ?>
            <!-- Modales -->
            <?php include 'modals/crear-producto.php'; ?>
            <?php include 'modals/editar-producto.php'; ?>
            <?php include 'modals/crear-promocion.php'; ?>
            <?php include 'modals/editar-promocion.php'; ?>
            <?php include 'modals/crear-evento.php'; ?>
            <?php include 'modals/editar-evento.php'; ?>
            <?php include 'modals/eliminar.php'; ?>
            <?php include 'modals/crear-disponibilidad-mesas.php'; ?>
            <!-- Toast container -->
            <div id="toast-container" class="toast-container position-fixed bottom-0 end-0 p-3" aria-live="polite" aria-atomic="true"></div>
        </div>
    <!--Link Boostrap JS-->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
    <!--Script JS divididos por responsabilidad-->
    <script src="/app/views/admin/js/utils.js"></script>
    <script src="/app/views/admin/js/nav.js"></script>
    <script src="/app/views/admin/js/EliminarAdminJS.js"></script>
    <script src="/app/views/admin/js/EditarAdminJS.js"></script>
    <script src="/app/views/admin/js/ProductoAdminJS.js"></script>
    <script src="/app/views/admin/js/PromocionAdminJS.js"></script>
    <script src="/app/views/admin/js/EventoAdminJS.js"></script>
    <script src="/app/views/admin/js/mesas.js"></script>
</body>
</html>