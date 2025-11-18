<?php
 /*
 * LoginController.php
 * 
 * Controlador de autenticación de administradores.
 * Gestiona el inicio de sesión, validación de credenciales y cierre de sesión.
 * 
 * Acciones:
 * - login: GET - Muestra formulario de login
 * - autenticar: POST - Valida credenciales (correo, password)
 * - logout: GET - Destruye sesión y redirige a login
 * 
 * Tabla de base de datos: administrador (id_admin, correo, password, ...)
 */

require_once '../../app/models/Usuario.php';
session_start();

class LoginController {
     /*
     * login()
     * Muestra el formulario de inicio de sesión.
     * @return void - Incluye vista login.php
     */
    public function login() {
        include '../../app/views/login/login.php';
    }

     /*
     * autenticar()
     * Valida las credenciales de administrador (correo y contraseña).
     * @return void - Redirige a dashboard o muestra formulario con error
     */
    public function autenticar() {
        $correo = $_POST['correo'] ?? '';
        $password = $_POST['password'] ?? '';

        // Verificar credenciales usando modelo Usuario
        $user = Usuario::verificar($correo, $password);

        if ($user) {
            // Credenciales válidas: crear sesión y redirigir
            $_SESSION['admin'] = $user;
            header("Location: ../../app/controllers/AdminController.php?action=dashboard");
            exit;
        } else {
            // Credenciales inválidas: mostrar formulario con error
            $error = "Usuario o contraseña incorrectos.";
            include '../../app/views/login/login.php';
        }
    }

     /*
     * logout()
     * Cierra la sesión activa del administrador.
     * @return void - Redirige a login
     */
    public function logout() {
        session_destroy();
        header("Location: ../../app/controllers/LoginController.php?action=login");
        exit;
    }
}

//Ejecución automática del controlador
$controller = new LoginController();
$action = $_GET['action'] ?? 'login';

if (method_exists($controller, $action)) {
    $controller->$action();
} else {
    $isAjax = false;
    if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') $isAjax = true;
    if (!$isAjax && !empty($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false) $isAjax = true;
    if ($isAjax) {
        header('Content-Type: application/json; charset=utf-8');
        http_response_code(404);
        echo json_encode(['status' => 'error', 'message' => 'action_not_found']);
    } else {
        echo "Acción no válida.";
    }
}
?>