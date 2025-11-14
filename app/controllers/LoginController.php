<?php
require_once '../../app/models/Usuario.php';
session_start();

class LoginController {
    //Mostrar formulario de inicio de sesión
    public function login() {
        include '../../app/views/login/login.php';
    }

    //Validación de credenciales
    public function autenticar() {
        $correo = $_POST['correo'] ?? '';
        $password = $_POST['password'] ?? '';

        $user = Usuario::verificar($correo, $password);

        if ($user) {
            $_SESSION['admin'] = $user;
            header("Location: ../../app/controllers/AdminController.php?action=dashboard");
            exit;
        } else {
            $error = "Usuario o contraseña incorrectos.";
            include '../../app/views/login/login.php';
        }
    }

    //Cerrar sesión
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
    echo "Acción no válida.";
}
?>