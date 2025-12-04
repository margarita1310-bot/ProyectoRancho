# Sistema de Gestión - Rancho La Joya

Sistema de gestión para administración de reservas, eventos y promociones en Rancho La Joya.

## Requisitos para Hosting

- **PHP**: 7.4 o superior
- **MySQL**: 5.7 o superior
- **Extensiones PHP necesarias**:
  - `pdo`
  - `pdo_mysql`
  - `json`
  - `session`

## Instalación en Infinity Free

### 1. Preparar el código localmente

```bash
# Clonar o descargar el repositorio
git clone <tu-repositorio> lajoya_gestion
cd lajoya_gestion

# Instalar dependencias (si tienes composer localmente)
composer install
```

### 2. Configurar variables de entorno

```bash
# Copiar el archivo de ejemplo
cp .env.example .env

# Editar .env con tus credenciales
# Los datos los obtendrás de Infinity Free en tu panel de control
```

### 3. Crear la estructura de carpetas en Infinity Free

Conectarse por SFTP al hosting:
```
lajoya_gestion/
├── app/
├── config/
├── public/
├── tests/
├── vendor/
├── .env
├── .htaccess
├── .gitignore
├── composer.json
├── index.php
└── phpunit.xml
```

### 4. Subir archivos al hosting

Usando SFTP o el Gestor de archivos de Infinity Free:

1. Crear la carpeta principal `lajoya_gestion` en el directorio raíz
2. Subir todos los archivos del proyecto
3. **Importante**: Asegurarse de subir los archivos ocultos (`.env`, `.htaccess`, `.gitignore`)

### 5. Configurar la base de datos

#### En el panel de Infinity Free:

1. Ir a **Bases de datos** → **Crear base de datos MySQL**
2. Crear un usuario MySQL con permisos total
3. Copiar los datos:
   - Nombre del servidor (host)
   - Nombre de la base de datos
   - Usuario de la BD
   - Contraseña

#### Actualizar archivo `.env`:

```env
DB_HOST=tu_servidor.com
DB_USER=tu_usuario_bd
DB_PASS=tu_contraseña_bd
DB_NAME=tu_base_datos
DEBUG_MODE=false
```

### 6. Importar la estructura de base de datos

1. Descargar los scripts SQL de tu base de datos local
2. En Infinity Free → **phpMyAdmin** → seleccionar la BD
3. Ir a **Importar** y seleccionar el archivo SQL
4. Ejecutar la importación

### 7. Configurar permisos

En el gestor de archivos de Infinity Free, asegurarse que:

- Las carpetas tengan permisos: `755`
- Los archivos tengan permisos: `644`
- El archivo `.env` tiene permisos: `600` (lectura solo por el servidor)

### 8. Verificar instalación

1. Acceder a: `https://tudominio.com/` 
2. Debería redirigir a la página de login
3. Probar con las credenciales configuradas

## Rutas de acceso

- **Admin**: `https://tudominio.com/app/controllers/LoginController.php?action=login`
- **Usuario**: `https://tudominio.com/app/controllers/UserViewController.php`

## Troubleshooting

### Error: "Error de conexión"
- Verificar que los datos en `.env` son correctos
- Verificar que el usuario de BD tiene permisos en la base de datos
- Probar la conexión en phpMyAdmin de Infinity Free

### Error 404 en las páginas
- Verificar que `.htaccess` está en la raíz del proyecto
- Asegurarse que `mod_rewrite` está habilitado en Infinity Free
- Si no funciona, contactar a soporte de Infinity Free

### Error: "Permission denied"
- Revisar permisos de archivo/carpeta (755/644)
- Asegurarse que el archivo `.env` existe y es legible

### Errores de caracteres especiales (acentos, ñ)
- Verificar que la BD usa charset `utf8mb4`
- Verificar que los archivos PHP están en UTF-8

## Desarrollo local

```bash
# Para desarrollo local con XAMPP
# Copiar el archivo a htdocs/
cp -r lajoya_gestion /ruta/a/xampp/htdocs/

# Crear base de datos en phpMyAdmin local
# Importar estructura SQL

# Acceder a: http://localhost/lajoya_gestion/
```

## Mantenimiento

### Actualizar desde local a hosting

```bash
# Hacer cambios localmente
git add .
git commit -m "Cambios en desarrollo"

# Subir cambios al hosting (vía SFTP o git)
git push

# En el hosting, si tienes Git:
git pull
```

### Respaldos recomendados

1. **Base de Datos**: Exportar regularmente desde phpMyAdmin
2. **Archivos**: Mantener copia en sistema de control de versiones (Git)
3. **Frecuencia**: Mínimo semanal, idealmente después de cambios importantes

## Soporte

Para problemas con Infinity Free:
- [Documentación oficial](https://wiki.infinityfree.net/)
- [Panel de control](https://app.infinityfree.net/)
- Email: support@infinityfree.net

## Licencia

Proyecto privado para Rancho La Joya.
