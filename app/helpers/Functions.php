<?php

/**
 * Funciones auxiliares del sistema
 * Contiene funciones de utilidad para el procesamiento de datos
 */

/**
 * Normaliza el formato de hora a HH:MM.
 * Convierte horas con formato variable a formato estándar de 24 horas.
 * Ejemplo: "9:5" → "09:05", "14:30" → "14:30"
 * 
 * @param string $hora Hora en formato variable (H:M o HH:MM)
 * @return string Hora normalizada en formato HH:MM
 */
function normalizarHora($hora)
{
    if (empty($hora)) {
        return '';
    }

    $partes = explode(':', $hora);

    if (count($partes) >= 2) {
        // Rellenar horas y minutos con ceros a la izquierda
        $horas = str_pad($partes[0], 2, '0', STR_PAD_LEFT);
        $minutos = str_pad($partes[1], 2, '0', STR_PAD_LEFT);
        return "$horas:$minutos";
    }

    return $hora;
}
?>