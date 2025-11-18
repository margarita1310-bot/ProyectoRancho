<?php
 /*
 * Auth.php
 * 
 * Módulo de autenticación y autorización.
 * Verifica que el usuario tenga sesión de administrador activa antes de acceder a páginas protegidas.
 * 
 * Función principal:
 * - ensureAdmin(): Verifica sesión y controla acceso según tipo de petición (AJAX o no)
 */

 /*
 * ensureAdmin()
 * 
 * Verifica que exista una sesión de administrador activa.
 * Si no existe, responde diferente según el tipo de petición:
 * - AJAX: Retorna JSON 401 (unauthorized)
 * - HTML: Redirige a formulario de login
 * 
 * Detección de AJAX:
 * 1. Header: X-Requested-With: XMLHttpRequest (estándar jQuery)
 * 2. Header: Accept: application/json
 * 
 * Respuesta AJAX (401 Unauthorized):
 * {"status": "error", "message": "unauthorized"}
 * Content-Type: application/json; charset=utf-8
 * 
 * Respuesta HTML (no AJAX):
 * Redirige a: LoginController.php?action=login
 * 
 * @return void - Continúa ejecución si sesión existe, sino detiene y redirige/retorna JSON
 */
function ensureAdmin() {
    // Iniciar sesión si no está activa
    if (session_status() === PHP_SESSION_NONE) session_start();
    
    // Verificar si existe la sesión de administrador
    if (!isset($_SESSION['admin'])) {
        // Detectar si es una petición AJAX por header X-Requested-With
        $isAjax = false;
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
            $isAjax = true;
        }
        
        // También considerar Accept header solicitando JSON
        if (!$isAjax && !empty($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false) {
            $isAjax = true;
        }

        // Responder según tipo de petición
        if ($isAjax) {
            // Para AJAX: retornar JSON 401
            header('Content-Type: application/json; charset=utf-8');
            http_response_code(401);
            echo json_encode(['status' => 'error', 'message' => 'unauthorized']);
            exit;
        }

        // Para HTML: redirigir a login
        header("Location: ../../app/controllers/LoginController.php?action=login");
        exit;
    }
}
?>