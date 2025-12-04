<!DOCTYPE html>
<html lang="es">
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
    <!-- Iconos SVG de Bootstrap -->
    <svg xmlns="http://www.w3.org/2000/svg" style="display: none;">
        <symbol id="exclamation-triangle-fill" fill="currentColor" viewBox="0 0 16 16">
            <path d="M8.982 1.566a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767L8.982 1.566zM8 5c.535 0 .954.462.9.995l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 5.995A.905.905 0 0 1 8 5zm.002 6a1 1 0 1 1 0 2 1 1 0 0 1 0-2z"/>
        </symbol>
        <symbol id="info-fill" fill="currentColor" viewBox="0 0 16 16">
            <path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm.93-9.412-1 4.705c-.07.34.029.533.304.533.194 0 .487-.07.686-.246l-.088.416c-.287.346-.92.598-1.465.598-.703 0-1.002-.422-.808-1.319l.738-3.468c.064-.293.006-.399-.287-.47l-.451-.081.082-.381 2.29-.287zM8 5.5a1 1 0 1 1 0-2 1 1 0 0 1 0 2z"/>
        </symbol>
        <symbol id="x-circle-fill" fill="currentColor" viewBox="0 0 16 16">
            <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zM5.354 4.646a.5.5 0 1 0-.708.708L7.293 8l-2.647 2.646a.5.5 0 0 0 .708.708L8 8.707l2.646 2.647a.5.5 0 0 0 .708-.708L8.707 8l2.647-2.646a.5.5 0 0 0-.708-.708L8 7.293 5.354 4.646z"/>
        </symbol>
    </svg>

    <div class="d-flex">
        <!--Side bar-->
        <?php include 'sections/SidebarAdmin.php'; ?>
        
        <!--Contenido de la pagina-->
        <div class="content flex-grow-1 p-3">
            <!--Inicio-->
            <?php include 'sections/IndexAdmin.php'; ?>
            <!--Promociones-->
            <?php include 'sections/PromocionAdmin.php'; ?>
            <!--Eventos-->
            <?php include 'sections/EventoAdmin.php'; ?>
            <!--Menu-->
            <?php include 'sections/ProductoAdmin.php'; ?>
            <!--Reservas-->
            <?php include 'sections/ReservaAdmin.php'; ?>
            <!--Mesas-->
            <?php include 'sections/DisponibilidadAdmin.php'; ?>
            <!-- Modales -->
            <?php include 'modals/crear-producto.php'; ?>
            <?php include 'modals/editar-producto.php'; ?>
            <?php include 'modals/crear-promocion.php'; ?>
            <?php include 'modals/editar-promocion.php'; ?>
            <?php include 'modals/crear-evento.php'; ?>
            <?php include 'modals/editar-evento.php'; ?>
            <?php include 'modals/eliminar.php'; ?>
            <?php include 'modals/crear-disponibilidad-mesas.php'; ?>
            <?php include 'modals/editar-disponibilidad-mesas.php'; ?>
            <?php include 'modals/perfil-admin.php'; ?>
            <div id="toast-container" class="toast-container position-fixed bottom-0 end-0 p-3" style="z-index: 9999;" aria-live="polite" aria-atomic="true"></div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
    
    <script src="/app/views/admin/js/utils.js"></script>
    <script src="/app/views/admin/js/nav.js"></script>
    <script src="/app/views/admin/js/EditarAdminJS.js"></script>
    <script src="/app/views/admin/js/EliminarAdminJS.js"></script>
    <script src="/app/views/admin/js/ProductoAdminJS.js"></script>
    <script src="/app/views/admin/js/PromocionAdminJS.js"></script>
    <script src="/app/views/admin/js/EventoAdminJS.js"></script>
    <script src="/app/views/admin/js/DisponibilidadAdminJS.js"></script>
    <script src="/app/views/admin/js/ReservaAdminJS.js"></script>
    <script src="/app/views/admin/js/mesas.js"></script>
    <script src="/app/views/admin/js/PerfilAdminJS.js"></script>
    <script src="/app/views/admin/js/DashboardAdminJS.js"></script>
    <script src="/app/views/admin/js/ux-admin-improvements.js"></script>
</body>
</html>