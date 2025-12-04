<?php

require_once '../../app/models/Usuario.php';

session_start();

/**
 * Controlador de Login
 * Gestiona la autenticación de administradores en el sistema
 */
class LoginController
{
    /**
     * Carga y renderiza la vista de formulario de login.
     * Muestra el formulario para que el administrador inicie sesión.
     * 
     * @return void Renderiza la vista de login
     */
    public function login()
    {
        include '../../app/views/login/login.php';
    }

    /**
     * Autentica un administrador validando correo y contraseña.
     * Si las credenciales son válidas, inicia sesión y redirige al dashboard.
     * Si no, muestra nuevamente el formulario con error.
     * 
     * @return void Redirige a dashboard o muestra vista con error
     */
    public function autenticar()
    {
        $correo = $_POST['correo'] ?? '';
        $password = $_POST['password'] ?? '';

        // Verificar credenciales del usuario
        $user = Usuario::verificar($correo, $password);

        if ($user) {
            // Credenciales válidas: crear sesión y redirigir
            $_SESSION['admin'] = $user;
            header("Location: ../../app/controllers/AdminController.php?action=dashboard");
            exit;
        } else {
            // Credenciales inválidas: mostrar error
            $error = "Usuario o contraseña incorrectos.";
            include '../../app/views/login/login.php';
        }
    }

    /**
     * Cierra la sesión del administrador y redirige al login.
     * Destruye todos los datos de sesión.
     * 
     * @return void Redirige y termina la ejecución
     */
    public function logout()
    {
        session_destroy();
        header("Location: ../../app/controllers/LoginController.php?action=login");
        exit;
    }
}

// Instanciar el controlador y ejecutar la acción solicitada
$controller = new LoginController();
$action = $_GET['action'] ?? 'login';

if (method_exists($controller, $action)) {
    $controller->$action();
} else {
    // Verificar si es una solicitud AJAX
    $isAjax = false;
    if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
        $isAjax = true;
    }
    if (!$isAjax && !empty($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false) {
        $isAjax = true;
    }

    // Retornar error apropiado según el tipo de solicitud
    if ($isAjax) {
        header('Content-Type: application/json; charset=utf-8');
        http_response_code(404);
        echo json_encode(['status' => 'error', 'message' => 'action_not_found']);
    } else {
        echo "Acción no válida.";
    }
}
?>