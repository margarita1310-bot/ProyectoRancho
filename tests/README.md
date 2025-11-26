# Plan de Pruebas Unitarias - Rancho La Joya

## ✅ Configuración Completada

### 1. **PHPUnit Instalado**
- ✅ Composer configurado con `composer.json`
- ✅ PHPUnit 9.5 instalado como dependencia de desarrollo
- ✅ Autoloader PSR-4 configurado

### 2. **Estructura de Tests Creada**
```
tests/
├── bootstrap.php          # Bootstrap de PHPUnit
├── Unit/                  # Tests unitarios
│   ├── Models/           # Tests de modelos
│   │   └── EventoModelTest.php
│   └── Controllers/      # Tests de controladores
└── Integration/          # Tests de integración
```

### 3. **Archivo de Configuración**
- ✅ `phpunit.xml` configurado con:
  - Testsuites (Unit, Integration)
  - Coverage para models y controllers
  - Variables de entorno para BD de pruebas

### 4. **Primer Test Creado**
- ✅ `EventoModelTest.php` con 7 tests unitarios:
  1. `testGetAllRetornaArray()` - Verifica que getAll() retorna array
  2. `testCreateRetornaIdEvento()` - Verifica creación y retorno de ID
  3. `testGetByIdRetornaEventoCorrecto()` - Prueba obtener por ID
  4. `testUpdateActualizaEventoCorrectamente()` - Prueba actualización
  5. `testDeleteEliminaEvento()` - Prueba eliminación
  6. `testGetByIdConIdInexistenteRetornaFalse()` - Caso edge: ID inexistente

---

## 🚀 Cómo Ejecutar las Pruebas

### Opción 1: Ejecutar todos los tests
```powershell
php vendor\phpunit\phpunit\phpunit
```

### Opción 2: Ejecutar solo tests unitarios
```powershell
php vendor\phpunit\phpunit\phpunit --testsuite Unit
```

### Opción 3: Ejecutar un test específico
```powershell
php vendor\phpunit\phpunit\phpunit tests\Unit\Models\EventoModelTest.php
```

### Opción 4: Con reporte de cobertura (HTML)
```powershell
php vendor\phpunit\phpunit\phpunit --coverage-html coverage
```

---

## 📝 Próximos Pasos Recomendados

### 1. **Crear más tests de modelos**
- [ ] `PromocionModelTest.php` - Tests para PromocionModel
- [ ] `ReservaModelTest.php` - Tests para ReservaModel
- [ ] `ProductoModelTest.php` - Tests para ProductoModel

### 2. **Tests de controladores con mocks**
```php
// Ejemplo: EventoControllerTest.php
- Mockear requests HTTP
- Verificar respuestas JSON
- Validar códigos de estado HTTP
```

### 3. **Tests de integración**
```php
// Ejemplo: ReservaIntegrationTest.php
- Flujo completo: crear cliente + crear reserva
- Validar horarios por día
- Verificar disponibilidad de mesas
```

### 4. **Base de datos de pruebas**
Crear BD separada para tests:
```sql
CREATE DATABASE lajoya_gestion_test;
-- Copiar estructura de lajoya_gestion
```

### 5. **Automatización con GitHub Actions**
Crear `.github/workflows/tests.yml` para CI/CD

---

## 📚 Buenas Prácticas Implementadas

✅ **Patrón AAA**: Arrange-Act-Assert en cada test
✅ **Setup/Teardown**: Limpieza automática de datos de prueba
✅ **Nombres descriptivos**: Tests autodocumentados
✅ **Isolación**: Cada test es independiente
✅ **Coverage**: Configurado para medir cobertura de código

---

## 🎯 Métricas Objetivo

- **Cobertura de código**: > 80%
- **Tests por modelo**: Mínimo 5-7 tests
- **Tiempo de ejecución**: < 30 segundos
- **Tests pasando**: 100%
