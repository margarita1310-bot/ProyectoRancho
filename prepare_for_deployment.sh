#!/bin/bash
# Script para preparar el proyecto para deployment en Infinity Free
# Uso: bash prepare_for_deployment.sh

echo "================================"
echo "Preparando proyecto para Infinity Free"
echo "================================"
echo ""

# Verificar si estamos en el directorio correcto
if [ ! -f "composer.json" ]; then
    echo "❌ Error: composer.json no encontrado"
    echo "Por favor ejecuta este script desde la raíz del proyecto"
    exit 1
fi

echo "✓ Proyecto encontrado"
echo ""

# Crear .env si no existe
if [ ! -f ".env" ]; then
    echo "📝 Creando archivo .env..."
    cp .env.example .env
    echo "✓ Archivo .env creado"
    echo "  ⚠️  Recuerda editar .env con tus credenciales de Infinity Free"
else
    echo "✓ Archivo .env ya existe"
fi

echo ""

# Verificar estructura de directorios
echo "🔍 Verificando estructura de directorios..."
required_dirs=("app" "config" "public" "tests" "app/models" "app/controllers" "app/helpers" "app/views")

for dir in "${required_dirs[@]}"; do
    if [ -d "$dir" ]; then
        echo "  ✓ $dir"
    else
        echo "  ✗ $dir NO ENCONTRADO"
    fi
done

echo ""

# Verificar archivos críticos
echo "🔍 Verificando archivos críticos..."
required_files=(".htaccess" "index.php" "config/config.php" "composer.json")

for file in "${required_files[@]}"; do
    if [ -f "$file" ]; then
        echo "  ✓ $file"
    else
        echo "  ✗ $file NO ENCONTRADO"
    fi
done

echo ""
echo "================================"
echo "✓ Verificación completada"
echo "================================"
echo ""
echo "Próximos pasos:"
echo "1. Edita .env con tus credenciales de Infinity Free"
echo "2. Sube todos los archivos (incluidos los ocultos) al hosting"
echo "3. Importa la estructura de BD en phpMyAdmin"
echo "4. Accede a diagnostic.php para verificar la configuración"
echo ""
echo "Para más información, consulta DEPLOYMENT_INFINITYFREE.md"
