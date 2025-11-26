<?php
/**
 * ReservaModelTest.php
 * Pruebas unitarias para ReservaModel
 */

namespace Tests\Unit\Models;

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../../../app/models/ReservaModel.php';
require_once __DIR__ . '/../../../app/models/MesaModel.php';
require_once __DIR__ . '/../../../app/models/Conexion.php';

class ReservaModelTest extends TestCase
{
    private $reservaModel;
    private $mesaModel;
    private $testReservaId;
    private $testMesaId;

    /**
     * Se ejecuta antes de cada test
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->reservaModel = new \ReservaModel();
        $this->mesaModel = new \MesaModel();
        $this->testReservaId = null;
        $this->testMesaId = null;
    }

    /**
     * Se ejecuta después de cada test
     */
    protected function tearDown(): void
    {
        // Limpiar reserva y liberar mesa si aplica
        if ($this->testReservaId) {
            $this->reservaModel->delete($this->testReservaId);
            $this->testReservaId = null;
        }
        if ($this->testMesaId) {
            $this->mesaModel->liberarMesa($this->testMesaId);
            $this->testMesaId = null;
        }
        parent::tearDown();
    }

    /**
     * Test: Verificar que getAll() retorna un array
     */
    public function testGetAllRetornaArray()
    {
        $reservas = $this->reservaModel->getAll();
        $this->assertIsArray($reservas);
    }

    /**
     * Test: Crear una reserva y obtenerla por ID
     */
    public function testCrearYObtenerReserva()
    {
        // Crear cliente y datos mínimos vía SQL directo
        $db = \Conexion::conectar();
        $db->exec("INSERT INTO cliente (nombre, correo, telefono) VALUES ('Cliente Test', 'cliente@test.com', '7710000000')");
        $idCliente = (int)$db->lastInsertId();

        $fecha = date('Y-m-d', strtotime('+2 days'));
        $hora = '18:30:00';
        $folio = 'RES-' . strtoupper(substr(uniqid(), -8));

        $stmt = $db->prepare("INSERT INTO reserva (folio, id_cliente, id_evento, fecha, hora, num_personas, id_mesa, estado) VALUES (?, ?, NULL, ?, ?, ?, NULL, 'pendiente')");
        $stmt->execute([$folio, $idCliente, $fecha, $hora, 2]);
        $this->testReservaId = (int)$db->lastInsertId();

        $res = $this->reservaModel->getById($this->testReservaId);
        $this->assertIsArray($res);
        $this->assertEquals('pendiente', $res['estado']);
        $this->assertEquals($fecha, $res['fecha']);
        $this->assertEquals($hora, $res['hora']);
    }

    /**
     * Test: Filtrar reservas por fecha específica
     */
    public function testFiltrarPorFecha()
    {
        $fecha = date('Y-m-d', strtotime('+3 days'));
        $db = \Conexion::conectar();
        $db->exec("INSERT INTO cliente (nombre, correo, telefono) VALUES ('Cliente Filtro', 'filtro@test.com', '7710000001')");
        $idCliente = (int)$db->lastInsertId();
        $folio = 'RES-' . strtoupper(substr(uniqid(), -8));
        $stmt = $db->prepare("INSERT INTO reserva (folio, id_cliente, id_evento, fecha, hora, num_personas, id_mesa, estado) VALUES (?, ?, NULL, ?, '19:00:00', 3, NULL, 'pendiente')");
        $stmt->execute([$folio, $idCliente, $fecha]);
        $this->testReservaId = (int)$db->lastInsertId();

        $lista = $this->reservaModel->getByDate($fecha);
        $this->assertIsArray($lista);
        $this->assertNotEmpty($lista);
        $this->assertEquals($fecha, $lista[0]['fecha']);
    }

    /**
     * Test: Confirmar reserva con asignación de mesa
     * Verifica que el estado cambia a 'confirmada' y la mesa queda 'Ocupada'
     */
    public function testConfirmarReservaConAsignacionDeMesa()
    {
        // Crear cliente y reserva pendiente
        $db = \Conexion::conectar();
        $db->exec("INSERT INTO cliente (nombre, correo, telefono) VALUES ('Cliente Confirm', 'confirm@test.com', '7710000002')");
        $idCliente = (int)$db->lastInsertId();
        $fecha = date('Y-m-d', strtotime('+4 days'));
        $folio = 'RES-' . strtoupper(substr(uniqid(), -8));
        $stmt = $db->prepare("INSERT INTO reserva (folio, id_cliente, id_evento, fecha, hora, num_personas, id_mesa, estado) VALUES (?, ?, NULL, ?, '20:00:00', 4, NULL, 'pendiente')");
        $stmt->execute([$folio, $idCliente, $fecha]);
        $this->testReservaId = (int)$db->lastInsertId();

        // Activar mesas y tomar primera activa
        $this->mesaModel->activarMesas(5);
        $mesas = $this->mesaModel->getMesasActivas();
        $this->assertNotEmpty($mesas);
        $this->testMesaId = (int)$mesas[0]['id_mesa'];

        // Confirmar reserva con mesa
        $ok = $this->reservaModel->confirm($this->testReservaId, $this->testMesaId);
        $this->assertTrue($ok);

        // Verificar estados
        $res = $this->reservaModel->getById($this->testReservaId);
        $this->assertEquals('confirmada', $res['estado']);
        $this->assertEquals($this->testMesaId, (int)$res['id_mesa']);
        $mesa = $this->mesaModel->getMesaById($this->testMesaId);
        $this->assertEquals('Ocupada', $mesa['estado']);
    }

    /**
     * Test: Eliminar reserva libera la mesa asignada
     * Verifica que al eliminar una reserva confirmada, la mesa vuelve a estado 'Disponible'
     */
    public function testEliminarReservaLiberaMesa()
    {
        // Crear cliente y reserva confirmada con mesa
        $db = \Conexion::conectar();
        $db->exec("INSERT INTO cliente (nombre, correo, telefono) VALUES ('Cliente Delete', 'delete@test.com', '7710000003')");
        $idCliente = (int)$db->lastInsertId();
        $fecha = date('Y-m-d', strtotime('+5 days'));
        $folio = 'RES-' . strtoupper(substr(uniqid(), -8));
        $stmt = $db->prepare("INSERT INTO reserva (folio, id_cliente, id_evento, fecha, hora, num_personas, id_mesa, estado) VALUES (?, ?, NULL, ?, '21:00:00', 2, NULL, 'pendiente')");
        $stmt->execute([$folio, $idCliente, $fecha]);
        $this->testReservaId = (int)$db->lastInsertId();
        $this->mesaModel->activarMesas(3);
        $mesas = $this->mesaModel->getMesasActivas();
        $this->testMesaId = (int)$mesas[0]['id_mesa'];
        $this->reservaModel->confirm($this->testReservaId, $this->testMesaId);

        // Eliminar reserva y verificar liberación de mesa
        $ok = $this->reservaModel->delete($this->testReservaId);
        $this->assertTrue($ok);
        $mesa = $this->mesaModel->getMesaById($this->testMesaId);
        $this->assertEquals('Disponible', $mesa['estado']);

        // Marcar como limpiado
        $this->testReservaId = null;
    }

    /**
     * Test: getById con ID inexistente retorna false
     */
    public function testGetByIdConIdInexistenteRetornaFalse()
    {
        $this->assertFalse($this->reservaModel->getById(999999));
    }
}
