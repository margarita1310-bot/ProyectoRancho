<?php
// Test directo del endpoint de guardar disponibilidad
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Simular sesión de admin
session_start();
$_SESSION['user_role'] = 'admin';
$_SESSION['user_id'] = 1;

// Simular request POST
$_SERVER['REQUEST_METHOD'] = 'POST';
$_GET['action'] = 'guardar';
$_POST['fecha'] = '2025-11-20';
$_POST['cantidad'] = '10';
$_SERVER['HTTP_X_REQUESTED_WITH'] = 'XMLHttpRequest';

echo "=== Simulando llamada a DisponibilidadController ===\n\n";
echo "Parámetros:\n";
echo "  fecha: " . $_POST['fecha'] . "\n";
echo "  cantidad: " . $_POST['cantidad'] . "\n\n";

try {
    // Capturar el output
    ob_start();
    include 'app/controllers/DisponibilidadController.php';
    $output = ob_get_clean();
    
    echo "Respuesta del controlador:\n";
    echo $output . "\n\n";
    
    // Verificar si es JSON válido
    $json = json_decode($output, true);
    if ($json) {
        echo "✓ JSON válido\n";
        echo "Status: " . ($json['status'] ?? 'N/A') . "\n";
    } else {
        echo "✗ JSON inválido\n";
        echo "Error: " . json_last_error_msg() . "\n";
    }
    
} catch (Exception $e) {
    echo "✗ Excepción capturada:\n";
    echo "Mensaje: " . $e->getMessage() . "\n";
    echo "Archivo: " . $e->getFile() . ":" . $e->getLine() . "\n";
    echo "\nTrace:\n" . $e->getTraceAsString() . "\n";
}
?>
