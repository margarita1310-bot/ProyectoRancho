<?php

/**
 * Funciones de autenticación y autorización del sistema
 * Valida permisos de acceso para administradores y usuarios
 */

/**
 * Verifica que el usuario actual sea un administrador autenticado.
 * Si no está autenticado, lo redirige al login o retorna error JSON si es solicitud AJAX.
 * 
 * @return void Redirige o termina la ejecución si no hay autenticación
 */
function ensureAdmin()
{
    // Iniciar sesión si no está activa
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    // Verificar si el admin está autenticado
    if (!isset($_SESSION['admin'])) {
        // Detectar si es una solicitud AJAX
        $isAjax = false;

        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
            $isAjax = true;
        }

        if (!$isAjax && !empty($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false) {
            $isAjax = true;
        }

        // Retornar respuesta según el tipo de solicitud
        if ($isAjax) {
            // Para solicitudes AJAX: respuesta JSON
            header('Content-Type: application/json; charset=utf-8');
            http_response_code(401);
            echo json_encode(['status' => 'error', 'message' => 'unauthorized']);
            exit;
        }

        // Para solicitudes normales: redirigir a login
        header("Location: ../../app/controllers/LoginController.php?action=login");
        exit;
    }
}
?>