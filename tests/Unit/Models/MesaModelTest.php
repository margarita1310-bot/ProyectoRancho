<?php
/**
 * MesaModelTest.php
 * Pruebas unitarias para MesaModel
 */

namespace Tests\Unit\Models;

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../../../app/models/MesaModel.php';
require_once __DIR__ . '/../../../app/models/Conexion.php';

class MesaModelTest extends TestCase
{
    private $mesaModel;

    /**
     * Se ejecuta antes de cada test
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->mesaModel = new \MesaModel();
    }

    /**
     * Test: Activar y desactivar mesas
     * Verifica que se pueden activar N mesas y luego desactivar todas
     */
    public function testActivarYDesactivarMesas()
    {
        $ok = $this->mesaModel->desactivarTodasMesas();
        $this->assertTrue($ok);

        $this->assertTrue($this->mesaModel->activarMesas(5));
        $mesas = $this->mesaModel->getMesasActivas();
        $this->assertNotEmpty($mesas);
        $this->assertGreaterThanOrEqual(5, count($mesas));
    }

    /**
     * Test: Actualizar estado de mesa y liberarla
     * Verifica que una mesa puede cambiar de 'Disponible' a 'Ocupada' y viceversa
     */
    public function testActualizarEstadoYLiberaMesa()
    {
        $this->mesaModel->activarMesas(2);
        $mesas = $this->mesaModel->getMesasActivas();
        $mesaId = (int)$mesas[0]['id_mesa'];
        // Crear cliente válido para asignar a la mesa (respeta FK)
        $db = \Conexion::conectar();
        $db->exec("INSERT INTO cliente (nombre, correo, telefono) VALUES ('Cliente Mesa', 'mesa@test.com', '7710000004')");
        $idCliente = (int)$db->lastInsertId();

        $ok = $this->mesaModel->actualizarEstado($mesaId, 'Ocupada', $idCliente);
        $this->assertTrue($ok);
        $m = $this->mesaModel->getMesaById($mesaId);
        $this->assertEquals('Ocupada', $m['estado']);

        $ok2 = $this->mesaModel->liberarMesa($mesaId);
        $this->assertTrue($ok2);
        $m2 = $this->mesaModel->getMesaById($mesaId);
        $this->assertEquals('Disponible', $m2['estado']);
    }

    /**
     * Test: Obtener mesa por ID retorna estructura correcta
     * Verifica que getMesaById() retorna un array con las columnas esperadas
     */
    public function testGetMesaByIdRetornaDatos()
    {
        $this->mesaModel->activarMesas(1);
        $mesas = $this->mesaModel->getMesasActivas();
        $mesaId = (int)$mesas[0]['id_mesa'];
        $m = $this->mesaModel->getMesaById($mesaId);
        $this->assertIsArray($m);
        $this->assertArrayHasKey('id_mesa', $m);
        $this->assertArrayHasKey('numero', $m);
        $this->assertArrayHasKey('estado', $m);
    }
}
