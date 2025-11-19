<?php
// Simular llamada al controlador de Disponibilidad
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "=== Test DisponibilidadController ===\n\n";

$_GET['action'] = 'guardar';
$_POST['fecha'] = '2025-11-20';
$_POST['cantidad'] = '10';

// Simular headers AJAX
$_SERVER['HTTP_X_REQUESTED_WITH'] = 'XMLHttpRequest';

ob_start();
try {
    include 'app/controllers/DisponibilidadController.php';
    $output = ob_get_clean();
    echo "Output del controlador:\n";
    echo $output;
} catch (Exception $e) {
    ob_end_clean();
    echo "Error capturado: " . $e->getMessage() . "\n";
    echo "Trace:\n" . $e->getTraceAsString() . "\n";
}
?>
