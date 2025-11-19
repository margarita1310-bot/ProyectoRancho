<?php
require_once 'app/models/Conexion.php';
$db = Conexion::conectar();
$stmt = $db->query('DESCRIBE reserva');
echo "Estructura de tabla 'reserva':\n\n";
while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    echo $row['Field'] . " - " . $row['Type'] . " - " . ($row['Null'] == 'YES' ? 'NULL' : 'NOT NULL') . " - " . $row['Default'] . "\n";
}
?>
