<?php
require_once 'Conexion.php';

try {
    $db = Conexion::conectar();
    echo "✅ Conexión correcta";
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage();
}
?>
