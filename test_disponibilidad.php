<?php
// Script de prueba para verificar errores
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "=== Test de Disponibilidad ===\n\n";

// Test 1: Verificar Conexión
echo "1. Verificando conexión a BD...\n";
try {
    require_once 'app/models/Conexion.php';
    $db = Conexion::conectar();
    echo "✓ Conexión exitosa\n\n";
} catch (Exception $e) {
    echo "✗ Error de conexión: " . $e->getMessage() . "\n\n";
    die();
}

// Test 2: Verificar tabla mesa
echo "2. Verificando tabla 'mesa'...\n";
try {
    $stmt = $db->query("SHOW TABLES LIKE 'mesa'");
    if ($stmt->rowCount() > 0) {
        echo "✓ Tabla 'mesa' existe\n";
        
        // Ver estructura
        $stmt = $db->query("DESCRIBE mesa");
        $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo "Columnas: ";
        foreach ($columns as $col) {
            echo $col['Field'] . " (" . $col['Type'] . "), ";
        }
        echo "\n\n";
    } else {
        echo "✗ Tabla 'mesa' NO existe\n\n";
    }
} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n\n";
}

// Test 3: Verificar tabla mesas_disponibilidad
echo "3. Verificando tabla 'mesas_disponibilidad'...\n";
try {
    $stmt = $db->query("SHOW TABLES LIKE 'mesas_disponibilidad'");
    if ($stmt->rowCount() > 0) {
        echo "✓ Tabla 'mesas_disponibilidad' existe\n";
        
        // Ver estructura
        $stmt = $db->query("DESCRIBE mesas_disponibilidad");
        $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo "Columnas: ";
        foreach ($columns as $col) {
            echo $col['Field'] . " (" . $col['Type'] . "), ";
        }
        echo "\n\n";
    } else {
        echo "✗ Tabla 'mesas_disponibilidad' NO existe\n\n";
    }
} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n\n";
}

// Test 4: Verificar tabla reserva
echo "4. Verificando tabla 'reserva'...\n";
try {
    $stmt = $db->query("SHOW TABLES LIKE 'reserva'");
    if ($stmt->rowCount() > 0) {
        echo "✓ Tabla 'reserva' existe\n";
        
        // Ver estructura
        $stmt = $db->query("DESCRIBE reserva");
        $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo "Columnas: ";
        foreach ($columns as $col) {
            echo $col['Field'] . " (" . $col['Type'] . "), ";
        }
        echo "\n\n";
    } else {
        echo "✗ Tabla 'reserva' NO existe\n\n";
    }
} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n\n";
}

// Test 5: Verificar tabla cliente
echo "5. Verificando tabla 'cliente'...\n";
try {
    $stmt = $db->query("SHOW TABLES LIKE 'cliente'");
    if ($stmt->rowCount() > 0) {
        echo "✓ Tabla 'cliente' existe\n\n";
    } else {
        echo "✗ Tabla 'cliente' NO existe\n\n";
    }
} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n\n";
}

// Test 6: Probar MesaModel
echo "6. Probando MesaModel...\n";
try {
    require_once 'app/models/MesaModel.php';
    $mesaModel = new MesaModel();
    $mesas = $mesaModel->getMesasActivas();
    echo "✓ MesaModel funciona correctamente\n";
    echo "Mesas activas encontradas: " . count($mesas) . "\n\n";
} catch (Exception $e) {
    echo "✗ Error en MesaModel: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n\n";
}

// Test 7: Probar DisponibilidadModel
echo "7. Probando DisponibilidadModel...\n";
try {
    require_once 'app/models/DisponibilidadModel.php';
    $dispModel = new DisponibilidadModel();
    $disponibilidad = $dispModel->getAll();
    echo "✓ DisponibilidadModel funciona correctamente\n";
    echo "Registros de disponibilidad: " . count($disponibilidad) . "\n\n";
} catch (Exception $e) {
    echo "✗ Error en DisponibilidadModel: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n\n";
}

echo "\n=== Fin del Test ===\n";
?>
