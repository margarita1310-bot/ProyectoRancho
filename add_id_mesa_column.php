<?php
// Ejecutar el script SQL para agregar id_mesa a reserva
require_once 'app/models/Conexion.php';

try {
    $db = Conexion::conectar();
    
    echo "Agregando columna id_mesa a tabla reserva...\n\n";
    
    // Verificar si la columna ya existe
    $stmt = $db->query("SHOW COLUMNS FROM reserva LIKE 'id_mesa'");
    if ($stmt->rowCount() > 0) {
        echo "✓ La columna id_mesa ya existe en la tabla reserva\n";
    } else {
        // Agregar la columna
        $sql = "ALTER TABLE `reserva` 
                ADD COLUMN `id_mesa` INT(11) NULL DEFAULT NULL AFTER `id_evento`";
        $db->exec($sql);
        echo "✓ Columna id_mesa agregada exitosamente\n";
        
        // Agregar foreign key
        try {
            $sql = "ALTER TABLE `reserva` 
                    ADD CONSTRAINT `fk_reserva_mesa` FOREIGN KEY (`id_mesa`) REFERENCES `mesa` (`id_mesa`) ON DELETE SET NULL";
            $db->exec($sql);
            echo "✓ Foreign key agregada exitosamente\n";
        } catch (Exception $e) {
            echo "⚠ Foreign key ya existe o no se pudo agregar: " . $e->getMessage() . "\n";
        }
    }
    
    echo "\n✓ Proceso completado\n\n";
    
    // Verificar estructura final
    echo "Estructura actualizada de tabla 'reserva':\n";
    $stmt = $db->query('DESCRIBE reserva');
    while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "  " . $row['Field'] . " - " . $row['Type'] . "\n";
    }
    
} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
}
?>
