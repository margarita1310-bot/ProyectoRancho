<?php
/**
 * PromocionModelTest.php
 * Pruebas unitarias para PromocionModel
 */

namespace Tests\Unit\Models;

use PHPUnit\Framework\TestCase;

// Incluir el modelo directamente (sin namespace en el código original)
require_once __DIR__ . '/../../../app/models/PromocionModel.php';

class PromocionModelTest extends TestCase
{
    private $promocionModel;
    private $testPromocionId;

    /**
     * Se ejecuta antes de cada test
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->promocionModel = new \PromocionModel();
    }

    /**
     * Se ejecuta después de cada test
     */
    protected function tearDown(): void
    {
        // Limpiar la promoción de prueba si existe
        if ($this->testPromocionId) {
            $this->promocionModel->delete($this->testPromocionId);
            $this->testPromocionId = null;
        }
        parent::tearDown();
    }

    /**
     * Test: Verificar que getAll() retorna un array
     */
    public function testGetAllRetornaArray()
    {
        $promociones = $this->promocionModel->getAll();
        
        $this->assertIsArray($promociones, 'getAll() debe retornar un array');
    }

    /**
     * Test: Crear una promoción y verificar que retorna ID
     */
    public function testCreateRetornaIdPromocion()
    {
        $nombre = 'Promoción Test ' . time();
        $descripcion = 'Descripción de prueba para promoción';
        $fechaInicio = date('Y-m-d', strtotime('+1 day'));
        $fechaFin = date('Y-m-d', strtotime('+7 days'));
        $estado = 'Disponible';

        $resultado = $this->promocionModel->create(
            $nombre,
            $descripcion,
            $fechaInicio,
            $fechaFin,
            $estado
        );

        // Guardar ID para limpieza
        $this->testPromocionId = $resultado;

        $this->assertIsInt($resultado, 'create() debe retornar un ID entero');
        $this->assertGreaterThan(0, $resultado, 'El ID debe ser mayor a 0');
    }

    /**
     * Test: Crear promoción con estado "No disponible"
     */
    public function testCreateConEstadoNoDisponible()
    {
        $nombre = 'Promoción Inactiva ' . time();
        $estado = 'No disponible';
        
        $this->testPromocionId = $this->promocionModel->create(
            $nombre,
            'Descripción inactiva',
            date('Y-m-d'),
            date('Y-m-d', strtotime('+3 days')),
            $estado
        );

        $this->assertIsInt($this->testPromocionId, 'Debe crear promoción inactiva');
        
        $promocion = $this->promocionModel->getById($this->testPromocionId);
        $this->assertEquals($estado, $promocion['estado'], 'El estado debe ser No disponible');
    }

    /**
     * Test: Crear y obtener una promoción por ID
     */
    public function testGetByIdRetornaPromocionCorrecta()
    {
        // Crear promoción de prueba
        $nombre = 'Promoción GetById Test ' . time();
        $fechaInicio = date('Y-m-d', strtotime('+2 days'));
        $fechaFin = date('Y-m-d', strtotime('+10 days'));
        
        $this->testPromocionId = $this->promocionModel->create(
            $nombre,
            'Descripción test getById',
            $fechaInicio,
            $fechaFin,
            'Disponible'
        );

        // Obtener la promoción
        $promocion = $this->promocionModel->getById($this->testPromocionId);

        $this->assertIsArray($promocion, 'getById() debe retornar un array');
        $this->assertEquals($nombre, $promocion['nombre'], 'El nombre debe coincidir');
        $this->assertEquals($fechaInicio, $promocion['fecha_inicio'], 'La fecha inicio debe coincidir');
        $this->assertEquals($fechaFin, $promocion['fecha_fin'], 'La fecha fin debe coincidir');
        $this->assertEquals('Disponible', $promocion['estado'], 'El estado debe coincidir');
    }

    /**
     * Test: Actualizar una promoción
     */
    public function testUpdateActualizaPromocionCorrectamente()
    {
        // Crear promoción
        $this->testPromocionId = $this->promocionModel->create(
            'Promoción Original',
            'Descripción original',
            date('Y-m-d', strtotime('+1 day')),
            date('Y-m-d', strtotime('+5 days')),
            'Disponible'
        );

        // Actualizar
        $nuevoNombre = 'Promoción Actualizada ' . time();
        $nuevoEstado = 'No disponible';
        $resultado = $this->promocionModel->update(
            $this->testPromocionId,
            $nuevoNombre,
            'Descripción actualizada',
            date('Y-m-d', strtotime('+2 days')),
            date('Y-m-d', strtotime('+8 days')),
            $nuevoEstado
        );

        $this->assertTrue($resultado, 'update() debe retornar true');

        // Verificar que se actualizó
        $promocion = $this->promocionModel->getById($this->testPromocionId);
        $this->assertEquals($nuevoNombre, $promocion['nombre'], 'El nombre debe haberse actualizado');
        $this->assertEquals($nuevoEstado, $promocion['estado'], 'El estado debe haberse actualizado');
    }

    /**
     * Test: Cambiar estado de promoción de activa a inactiva
     */
    public function testCambiarEstadoDeActivaAInactiva()
    {
        // Crear promoción activa
        $this->testPromocionId = $this->promocionModel->create(
            'Promoción Activa',
            'Se desactivará',
            date('Y-m-d'),
            date('Y-m-d', strtotime('+5 days')),
            'Disponible'
        );

        // Cambiar a inactiva
        $resultado = $this->promocionModel->update(
            $this->testPromocionId,
            'Promoción Desactivada',
            'Ahora inactiva',
            date('Y-m-d'),
            date('Y-m-d', strtotime('+5 days')),
            'No disponible'
        );

        $this->assertTrue($resultado);
        
        $promocion = $this->promocionModel->getById($this->testPromocionId);
        $this->assertEquals('No disponible', $promocion['estado']);
    }

    /**
     * Test: Eliminar una promoción
     */
    public function testDeleteEliminaPromocion()
    {
        // Crear promoción
        $id = $this->promocionModel->create(
            'Promoción a Eliminar',
            'Se eliminará',
            date('Y-m-d'),
            date('Y-m-d', strtotime('+3 days')),
            'Disponible'
        );

        // Eliminar
        $resultado = $this->promocionModel->delete($id);
        $this->assertTrue($resultado, 'delete() debe retornar true');

        // Verificar que no existe
        $promocion = $this->promocionModel->getById($id);
        $this->assertFalse($promocion, 'La promoción no debe existir después de eliminarse');

        // No necesita limpieza adicional
        $this->testPromocionId = null;
    }

    /**
     * Test: getById con ID inexistente retorna false
     */
    public function testGetByIdConIdInexistenteRetornaFalse()
    {
        $idInexistente = 999999;
        $resultado = $this->promocionModel->getById($idInexistente);
        
        $this->assertFalse($resultado, 'getById() con ID inexistente debe retornar false');
    }

    /**
     * Test: Crear promoción con fechas válidas (fin después de inicio)
     */
    public function testCreateConFechasValidas()
    {
        $fechaInicio = date('Y-m-d', strtotime('+1 day'));
        $fechaFin = date('Y-m-d', strtotime('+10 days'));
        
        $this->testPromocionId = $this->promocionModel->create(
            'Promoción Fechas Válidas',
            'Fechas correctas',
            $fechaInicio,
            $fechaFin,
            'Disponible'
        );

        $this->assertIsInt($this->testPromocionId);
        
        $promocion = $this->promocionModel->getById($this->testPromocionId);
        $this->assertLessThanOrEqual($promocion['fecha_fin'], $promocion['fecha_inicio'], 
            'La fecha fin debe ser igual o posterior a la fecha inicio');
    }

    /**
     * Test: Verificar estructura de datos retornada por getById
     */
    public function testGetByIdRetornaEstructuraCorrecta()
    {
        $this->testPromocionId = $this->promocionModel->create(
            'Promoción Estructura',
            'Verificar estructura',
            date('Y-m-d'),
            date('Y-m-d', strtotime('+5 days')),
            'Disponible'
        );

        $promocion = $this->promocionModel->getById($this->testPromocionId);

        // Verificar que tiene las columnas esperadas
        $this->assertArrayHasKey('id_promocion', $promocion, 'Debe tener id_promocion');
        $this->assertArrayHasKey('nombre', $promocion, 'Debe tener nombre');
        $this->assertArrayHasKey('descripcion', $promocion, 'Debe tener descripcion');
        $this->assertArrayHasKey('fecha_inicio', $promocion, 'Debe tener fecha_inicio');
        $this->assertArrayHasKey('fecha_fin', $promocion, 'Debe tener fecha_fin');
        $this->assertArrayHasKey('estado', $promocion, 'Debe tener estado');
    }
}
