<?php
/**
 * ProductoModelTest.php
 * Pruebas unitarias para ProductoModel
 */

namespace Tests\Unit\Models;

use PHPUnit\Framework\TestCase;

// Incluir el modelo directamente (sin namespace en el código original)
require_once __DIR__ . '/../../../app/models/ProductoModel.php';

class ProductoModelTest extends TestCase
{
    private $productoModel;
    private $testProductoId;

    /**
     * Se ejecuta antes de cada test
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->productoModel = new \ProductoModel();
    }

    /**
     * Se ejecuta después de cada test
     */
    protected function tearDown(): void
    {
        // Limpiar el producto de prueba si existe
        if ($this->testProductoId) {
            $this->productoModel->delete($this->testProductoId);
            $this->testProductoId = null;
        }
        parent::tearDown();
    }

    /**
     * Test: Verificar que getAll() retorna un array
     */
    public function testGetAllRetornaArray()
    {
        $productos = $this->productoModel->getAll();
        
        $this->assertIsArray($productos, 'getAll() debe retornar un array');
    }

    /**
     * Test: Crear un producto y verificar que retorna true
     */
    public function testCreateRetornaTrue()
    {
        $nombre = 'Producto Test ' . time();
        $precio = 99.50;
        $categoria = 'Bebidas';

        $resultado = $this->productoModel->create($nombre, $precio, $categoria);

        $this->assertTrue($resultado, 'create() debe retornar true');

        // Buscar el producto recién creado para limpieza
        $productos = $this->productoModel->getAll();
        foreach ($productos as $p) {
            if ($p['nombre'] === $nombre) {
                $this->testProductoId = $p['id_producto'];
                break;
            }
        }
    }

    /**
     * Test: Crear producto con precio cero
     */
    public function testCreateConPrecioCero()
    {
        $nombre = 'Producto Gratis ' . time();
        $resultado = $this->productoModel->create($nombre, 0.00, 'Promociones');

        $this->assertTrue($resultado, 'Debe poder crear producto con precio cero');

        // Buscar para limpieza
        $productos = $this->productoModel->getAll();
        foreach ($productos as $p) {
            if ($p['nombre'] === $nombre) {
                $this->testProductoId = $p['id_producto'];
                break;
            }
        }
    }

    /**
     * Test: Crear producto con diferentes categorías
     */
    public function testCreateConDiferentesCategorias()
    {
        $categorias = ['Bebidas', 'Alimentos', 'Postres', 'Entradas', 'Platillos Fuertes'];
        $ids = [];

        foreach ($categorias as $cat) {
            $nombre = 'Producto ' . $cat . ' ' . time();
            $resultado = $this->productoModel->create($nombre, 50.00, $cat);
            $this->assertTrue($resultado);

            // Buscar ID
            $productos = $this->productoModel->getAll();
            foreach ($productos as $p) {
                if ($p['nombre'] === $nombre) {
                    $ids[] = $p['id_producto'];
                    break;
                }
            }
        }

        // Limpiar todos
        foreach ($ids as $id) {
            $this->productoModel->delete($id);
        }

        $this->assertCount(count($categorias), $ids, 'Debe crear productos para todas las categorías');
    }

    /**
     * Test: Obtener un producto por ID
     */
    public function testGetByIdRetornaProductoCorrecto()
    {
        // Crear producto de prueba
        $nombre = 'Producto GetById Test ' . time();
        $precio = 125.75;
        $categoria = 'Bebidas';
        
        $this->productoModel->create($nombre, $precio, $categoria);

        // Buscar el producto recién creado
        $productos = $this->productoModel->getAll();
        $productoCreado = null;
        foreach ($productos as $p) {
            if ($p['nombre'] === $nombre) {
                $productoCreado = $p;
                $this->testProductoId = $p['id_producto'];
                break;
            }
        }

        // Obtener por ID
        $producto = $this->productoModel->getById($this->testProductoId);

        $this->assertIsArray($producto, 'getById() debe retornar un array');
        $this->assertEquals($nombre, $producto['nombre'], 'El nombre debe coincidir');
        $this->assertEquals($precio, (float)$producto['precio'], 'El precio debe coincidir');
        $this->assertEquals($categoria, $producto['categoria'], 'La categoría debe coincidir');
    }

    /**
     * Test: Actualizar un producto
     */
    public function testUpdateActualizaProductoCorrectamente()
    {
        // Crear producto
        $this->productoModel->create('Producto Original', 50.00, 'Bebidas');

        // Buscar ID
        $productos = $this->productoModel->getAll();
        foreach ($productos as $p) {
            if ($p['nombre'] === 'Producto Original') {
                $this->testProductoId = $p['id_producto'];
                break;
            }
        }

        // Actualizar
        $nuevoNombre = 'Producto Actualizado ' . time();
        $nuevoPrecio = 75.50;
        $nuevaCategoria = 'Alimentos';
        
        $resultado = $this->productoModel->update(
            $this->testProductoId,
            $nuevoNombre,
            $nuevoPrecio,
            $nuevaCategoria
        );

        $this->assertTrue($resultado, 'update() debe retornar true');

        // Verificar actualización
        $producto = $this->productoModel->getById($this->testProductoId);
        $this->assertEquals($nuevoNombre, $producto['nombre'], 'El nombre debe haberse actualizado');
        $this->assertEquals($nuevoPrecio, (float)$producto['precio'], 'El precio debe haberse actualizado');
        $this->assertEquals($nuevaCategoria, $producto['categoria'], 'La categoría debe haberse actualizado');
    }

    /**
     * Test: Actualizar solo el precio de un producto
     */
    public function testUpdateSoloPrecio()
    {
        // Crear producto
        $nombreOriginal = 'Producto Precio ' . time();
        $this->productoModel->create($nombreOriginal, 100.00, 'Bebidas');

        // Buscar ID
        $productos = $this->productoModel->getAll();
        foreach ($productos as $p) {
            if ($p['nombre'] === $nombreOriginal) {
                $this->testProductoId = $p['id_producto'];
                break;
            }
        }

        // Actualizar solo precio
        $nuevoPrecio = 150.00;
        $resultado = $this->productoModel->update(
            $this->testProductoId,
            $nombreOriginal,
            $nuevoPrecio,
            'Bebidas'
        );

        $this->assertTrue($resultado);

        $producto = $this->productoModel->getById($this->testProductoId);
        $this->assertEquals($nuevoPrecio, (float)$producto['precio'], 'Solo el precio debe cambiar');
        $this->assertEquals($nombreOriginal, $producto['nombre'], 'El nombre debe mantenerse');
    }

    /**
     * Test: Eliminar un producto
     */
    public function testDeleteEliminaProducto()
    {
        // Crear producto
        $nombre = 'Producto a Eliminar ' . time();
        $this->productoModel->create($nombre, 45.00, 'Postres');

        // Buscar ID
        $productos = $this->productoModel->getAll();
        $id = null;
        foreach ($productos as $p) {
            if ($p['nombre'] === $nombre) {
                $id = $p['id_producto'];
                break;
            }
        }

        // Eliminar
        $resultado = $this->productoModel->delete($id);
        $this->assertTrue($resultado, 'delete() debe retornar true');

        // Verificar que no existe
        $producto = $this->productoModel->getById($id);
        $this->assertFalse($producto, 'El producto no debe existir después de eliminarse');

        $this->testProductoId = null; // No necesita limpieza adicional
    }

    /**
     * Test: getById con ID inexistente retorna false
     */
    public function testGetByIdConIdInexistenteRetornaFalse()
    {
        $idInexistente = 999999;
        $resultado = $this->productoModel->getById($idInexistente);
        
        $this->assertFalse($resultado, 'getById() con ID inexistente debe retornar false');
    }

    /**
     * Test: Verificar estructura de datos retornada por getById
     */
    public function testGetByIdRetornaEstructuraCorrecta()
    {
        // Crear producto
        $this->productoModel->create('Producto Estructura', 25.50, 'Entradas');

        // Buscar ID
        $productos = $this->productoModel->getAll();
        foreach ($productos as $p) {
            if ($p['nombre'] === 'Producto Estructura') {
                $this->testProductoId = $p['id_producto'];
                break;
            }
        }

        $producto = $this->productoModel->getById($this->testProductoId);

        // Verificar columnas esperadas
        $this->assertArrayHasKey('id_producto', $producto, 'Debe tener id_producto');
        $this->assertArrayHasKey('nombre', $producto, 'Debe tener nombre');
        $this->assertArrayHasKey('precio', $producto, 'Debe tener precio');
        $this->assertArrayHasKey('categoria', $producto, 'Debe tener categoria');
    }

    /**
     * Test: Crear producto con precio decimal
     */
    public function testCreateConPrecioDecimal()
    {
        $nombre = 'Producto Decimal ' . time();
        $precio = 123.45;
        $resultado = $this->productoModel->create($nombre, $precio, 'Alimentos');

        $this->assertTrue($resultado);

        // Buscar y verificar
        $productos = $this->productoModel->getAll();
        foreach ($productos as $p) {
            if ($p['nombre'] === $nombre) {
                $this->testProductoId = $p['id_producto'];
                $this->assertEquals($precio, (float)$p['precio'], 'El precio decimal debe guardarse correctamente');
                break;
            }
        }
    }

    /**
     * Test: Verificar que getAll() ordena por ID descendente
     */
    public function testGetAllOrdenaDescendente()
    {
        $productos = $this->productoModel->getAll();

        if (count($productos) >= 2) {
            $primerProducto = $productos[0];
            $segundoProducto = $productos[1];

            $this->assertGreaterThanOrEqual(
                $segundoProducto['id_producto'],
                $primerProducto['id_producto'],
                'Los productos deben estar ordenados por ID descendente'
            );
        } else {
            $this->assertTrue(true, 'No hay suficientes productos para verificar ordenamiento');
        }
    }
}
