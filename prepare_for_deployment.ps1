# Script para preparar el proyecto para deployment en Infinity Free (Windows)
# Uso: powershell -ExecutionPolicy Bypass -File prepare_for_deployment.ps1

Write-Host "================================" -ForegroundColor Cyan
Write-Host "Preparando proyecto para Infinity Free" -ForegroundColor Cyan
Write-Host "================================" -ForegroundColor Cyan
Write-Host ""

# Verificar si estamos en el directorio correcto
if (-not (Test-Path "composer.json")) {
    Write-Host "Error: composer.json no encontrado" -ForegroundColor Red
    Write-Host "Por favor ejecuta este script desde la raíz del proyecto" -ForegroundColor Red
    exit 1
}

Write-Host "Proyecto encontrado" -ForegroundColor Green
Write-Host ""

# Crear .env si no existe
if (-not (Test-Path ".env")) {
    Write-Host "Creando archivo .env..." -ForegroundColor Yellow
    Copy-Item ".env.example" ".env"
    Write-Host "Archivo .env creado" -ForegroundColor Green
    Write-Host "AVISO: Recuerda editar .env con tus credenciales de Infinity Free" -ForegroundColor Yellow
}
else {
    Write-Host "Archivo .env ya existe" -ForegroundColor Green
}

Write-Host ""

# Verificar estructura de directorios
Write-Host "Verificando estructura de directorios..." -ForegroundColor Cyan
$requiredDirs = @("app", "config", "public", "tests", "app/models", "app/controllers", "app/helpers", "app/views")

foreach ($dir in $requiredDirs) {
    if (Test-Path $dir -PathType Container) {
        Write-Host "  ✓ $dir" -ForegroundColor Green
    }
    else {
        Write-Host "  ✗ $dir NO ENCONTRADO" -ForegroundColor Red
    }
}

Write-Host ""

# Verificar archivos críticos
Write-Host "Verificando archivos críticos..." -ForegroundColor Cyan
$requiredFiles = @(".htaccess", "index.php", "config/config.php", "composer.json")

foreach ($file in $requiredFiles) {
    if (Test-Path $file -PathType Leaf) {
        Write-Host "  ✓ $file" -ForegroundColor Green
    }
    else {
        Write-Host "  ✗ $file NO ENCONTRADO" -ForegroundColor Red
    }
}

Write-Host ""
Write-Host "================================" -ForegroundColor Cyan
Write-Host "Verificación completada" -ForegroundColor Green
Write-Host "================================" -ForegroundColor Cyan
Write-Host ""
Write-Host "Próximos pasos:" -ForegroundColor Yellow
Write-Host "1. Edita .env con tus credenciales de Infinity Free"
Write-Host "2. Sube todos los archivos (incluidos los ocultos) al hosting"
Write-Host "3. Importa la estructura de BD en phpMyAdmin"
Write-Host "4. Accede a diagnostic.php para verificar la configuración"
Write-Host ""
Write-Host "Para más información, consulta DEPLOYMENT_INFINITYFREE.md" -ForegroundColor Cyan
