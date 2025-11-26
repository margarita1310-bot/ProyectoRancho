<?php
/**
 * EventoModelTest.php
 * Pruebas unitarias para EventoModel
 */

namespace Tests\Unit\Models;

use PHPUnit\Framework\TestCase;

// Incluir el modelo directamente (sin namespace en el código original)
require_once __DIR__ . '/../../../app/models/EventoModel.php';

class EventoModelTest extends TestCase
{
    private $eventoModel;
    private $testEventoId;

    /**
     * Se ejecuta antes de cada test
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->eventoModel = new \EventoModel();
    }

    /**
     * Se ejecuta después de cada test
     */
    protected function tearDown(): void
    {
        // Limpiar el evento de prueba si existe
        if ($this->testEventoId) {
            $this->eventoModel->delete($this->testEventoId);
            $this->testEventoId = null;
        }
        parent::tearDown();
    }

    /**
     * Test: Verificar que getAll() retorna un array
     */
    public function testGetAllRetornaArray()
    {
        $eventos = $this->eventoModel->getAll();
        
        $this->assertIsArray($eventos, 'getAll() debe retornar un array');
    }

    /**
     * Test: Crear un evento y verificar que retorna ID
     */
    public function testCreateRetornaIdEvento()
    {
        $nombre = 'Evento Test ' . time();
        $descripcion = 'Descripción de prueba';
        $fecha = date('Y-m-d', strtotime('+7 days'));
        $horaInicio = '18:00:00';
        $horaFin = '22:00:00';

        $resultado = $this->eventoModel->create(
            $nombre,
            $descripcion,
            $fecha,
            $horaInicio,
            $horaFin
        );

        // Guardar ID para limpieza
        $this->testEventoId = $resultado;

        $this->assertIsInt($resultado, 'create() debe retornar un ID entero');
        $this->assertGreaterThan(0, $resultado, 'El ID debe ser mayor a 0');
    }

    /**
     * Test: Crear y obtener un evento por ID
     */
    public function testGetByIdRetornaEventoCorrecto()
    {
        // Crear evento de prueba
        $nombre = 'Evento GetById Test ' . time();
        $fecha = date('Y-m-d', strtotime('+10 days'));
        
        $this->testEventoId = $this->eventoModel->create(
            $nombre,
            'Descripción test',
            $fecha,
            '19:00:00',
            '23:00:00'
        );

        // Obtener el evento
        $evento = $this->eventoModel->getById($this->testEventoId);

        $this->assertIsArray($evento, 'getById() debe retornar un array');
        $this->assertEquals($nombre, $evento['nombre'], 'El nombre debe coincidir');
        $this->assertEquals($fecha, $evento['fecha'], 'La fecha debe coincidir');
    }

    /**
     * Test: Actualizar un evento
     */
    public function testUpdateActualizaEventoCorrectamente()
    {
        // Crear evento
        $this->testEventoId = $this->eventoModel->create(
            'Evento Original',
            'Descripción original',
            date('Y-m-d', strtotime('+5 days')),
            '20:00:00',
            '23:00:00'
        );

        // Actualizar
        $nuevoNombre = 'Evento Actualizado ' . time();
        $resultado = $this->eventoModel->update(
            $this->testEventoId,
            $nuevoNombre,
            'Descripción actualizada',
            date('Y-m-d', strtotime('+6 days')),
            '21:00:00',
            '23:30:00'
        );

        $this->assertTrue($resultado, 'update() debe retornar true');

        // Verificar que se actualizó
        $evento = $this->eventoModel->getById($this->testEventoId);
        $this->assertEquals($nuevoNombre, $evento['nombre'], 'El nombre debe haberse actualizado');
    }

    /**
     * Test: Eliminar un evento
     */
    public function testDeleteEliminaEvento()
    {
        // Crear evento
        $id = $this->eventoModel->create(
            'Evento a Eliminar',
            'Se eliminará',
            date('Y-m-d', strtotime('+3 days')),
            '18:00:00',
            '22:00:00'
        );

        // Eliminar
        $resultado = $this->eventoModel->delete($id);
        $this->assertTrue($resultado, 'delete() debe retornar true');

        // Verificar que no existe
        $evento = $this->eventoModel->getById($id);
        $this->assertFalse($evento, 'El evento no debe existir después de eliminarse');

        // No necesita limpieza adicional
        $this->testEventoId = null;
    }

    /**
     * Test: getById con ID inexistente retorna false
     */
    public function testGetByIdConIdInexistenteRetornaFalse()
    {
        $idInexistente = 999999;
        $resultado = $this->eventoModel->getById($idInexistente);
        
        $this->assertFalse($resultado, 'getById() con ID inexistente debe retornar false');
    }
}
