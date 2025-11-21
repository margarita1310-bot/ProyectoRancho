<?php
/**
 * index-admin.php
 * Punto de entrada para administradores
 * Redirige al login del administrador
 */

header("Location: /app/controllers/LoginController.php?action=login");
exit;
?>
