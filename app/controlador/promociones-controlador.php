<?php
require_once 'app/modelo/promociones.php';

class PromocionesControlador {
    public function index() {
        $promociones = Promocion::obtenerTodas();
        $titulo = "Gestión de Promociones";
        $vista = 'app/vista/admin/promociones.php';
        include 'app/vista/admin/layout.php';
    }
}?>