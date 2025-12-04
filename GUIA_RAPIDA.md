# GUÍA RÁPIDA - Desplegar en Infinity Free

## 📝 FASE 1: PREPARACIÓN LOCAL (5 min)

### 1. Copiar .env
```bash
cp .env.example .env
```

### 2. Editar .env (valores temporales, se cambiarán en Infinity Free)
```env
DB_HOST=localhost
DB_USER=root
DB_PASS=rancho
DB_NAME=lajoya_gestion
DEBUG_MODE=false
```

### 3. Verificar estructura (opcional)
```bash
# PowerShell
powershell -ExecutionPolicy Bypass -File prepare_for_deployment.ps1

# Bash
bash prepare_for_deployment.sh
```

✅ **Fase 1 completada**

---

## 🌐 FASE 2: CREAR HOSTING (10 min)

### 1. Ir a Infinity Free
- URL: https://www.infinityfree.net/
- Crear cuenta (si no tienes)

### 2. En el panel de control
- Crear nuevo dominio/subdominio
- Anotar el **nombre de usuario FTP** y **contraseña**

### 3. Crear Base de Datos
- Ir a **Bases de Datos** → **Crear Base de Datos MySQL**
- Nombre base de datos: `lajoya_gestion` (o tu preferencia)
- Crear usuario MySQL
- Anotar credenciales:
  - Servidor: `___.mysql.infinityfree.com`
  - Usuario: `_____________`
  - Contraseña: `_____________`
  - Base de datos: `_____________`

✅ **Fase 2 completada**

---

## 📤 FASE 3: SUBIR ARCHIVOS (15-30 min)

### Opción A: SFTP (FileZilla)
1. Descargar FileZilla: https://filezilla-project.org/
2. Conectar:
   - Host: `ftpupload.net` o tu dominio
   - Usuario: Credenciales FTP de Infinity Free
   - Puerto: 21
   - Contraseña: Tu contraseña FTP

3. Navegar a carpeta del dominio (root)

4. Subir carpetas:
   ```
   ├── app/
   ├── config/
   ├── public/
   ├── tests/
   ├── vendor/
   ├── .env (⭐ ACTUALIZAR CON CREDENCIALES)
   ├── .htaccess
   ├── .gitignore
   ├── composer.json
   ├── composer.lock
   ├── index.php
   └── ...otros archivos
   ```

### Opción B: File Manager (Web)
1. Panel de Infinity Free → **File Manager**
2. Navegar a public_html o raíz del dominio
3. Subir archivos uno por uno (más lento)

### ⚠️ IMPORTANTE: Archivos Ocultos
Algunos programas no muestran archivos que comienzan con punto (`.`)

Asegúrate de subir:
- ✅ `.env` (CON CREDENCIALES ACTUALIZADAS)
- ✅ `.htaccess`
- ✅ `.gitignore`

✅ **Fase 3 completada**

---

## 🗄️ FASE 4: CONFIGURAR BASE DE DATOS (10 min)

### 1. Acceder a phpMyAdmin
- Panel de Infinity Free → **Bases de Datos** → **phpMyAdmin**
- O ir a: `https://www.phpmyadmin.co/` (según Infinity Free)

### 2. Seleccionar tu base de datos

### 3. Ir a pestaña **Importar**

### 4. Subir archivo `database_structure.sql`
- Localizarlo en tu proyecto
- Seleccionar y ejecutar

### 5. Verificar
- Deberían aparecer las tablas (usuarios, reservas, eventos, etc.)

✅ **Fase 4 completada**

---

## 🔧 FASE 5: ACTUALIZAR .env EN INFINITY FREE

### ⚠️ CRÍTICO: Editar con datos correctos

En el File Manager o SFTP, editar `.env`:

```env
DB_HOST=tuservidor.mysql.infinityfree.com
DB_USER=tuusuario_bd
DB_PASS=tucontraseña_bd
DB_NAME=tu_base_datos
DEBUG_MODE=false
```

**Verificar que cada valor es correcto**

✅ **Fase 5 completada**

---

## ✅ FASE 6: VERIFICACIÓN (5 min)

### 1. Acceder a diagnostic.php
```
https://tudominio.com/diagnostic.php
```

### 2. Verificar que todos los items están ✓
- PHP Version ✓
- Extensiones PHP ✓
- Archivo .env ✓
- Conexión a BD ✓
- Directorios escribibles ✓

### 3. Si algo falla:
- Revisar `.env`
- Revisar permisos de carpeta (755)
- Revisar en phpMyAdmin que la BD existe

### 4. Si todo está bien:
- Eliminar `diagnostic.php`

✅ **Fase 6 completada**

---

## 🧪 FASE 7: PRUEBAS FUNCIONALES (10 min)

### 1. Acceder a Login
```
https://tudominio.com/app/controllers/LoginController.php?action=login
```
- ¿Carga la página? ✓

### 2. Acceder a Dashboard Usuario
```
https://tudominio.com/app/controllers/UserViewController.php
```
- ¿Carga la página? ✓

### 3. Probar funcionalidades
- Crear reserva ✓
- Ver promociones ✓
- Ver eventos ✓
- Subir imágenes ✓

### 4. Si hay errores:
- Ejecutar `diagnostic.php` de nuevo
- Revisar permisos de `public/images/`
- Contactar Infinity Free si persisten problemas

✅ **Fase 7 completada**

---

## 🧹 FASE 8: LIMPIEZA FINAL (2 min)

### Eliminar archivos no necesarios en producción:
```
❌ diagnostic.php         (Herramienta de debug)
❌ DEPLOYMENT_*.md        (Documentación)
❌ CHECKLIST_*.md         (Lista de verificación)
❌ .env.example           (Plantilla, no es necesaria)
❌ prepare_for_*.ps1/.sh  (Scripts de preparación)
❌ database_structure.sql (Ya fue importado)
```

### Mantener:
```
✅ .env                   (con credenciales)
✅ .htaccess              (configuración importante)
✅ .gitignore
✅ Todos los archivos de la aplicación
```

✅ **Fase 8 completada**

---

## ✨ ¡LISTO!

Tu aplicación está en producción en Infinity Free.

### URLs de acceso:
- **Admin Login**: `https://tudominio.com/app/controllers/LoginController.php?action=login`
- **Dashboard Usuario**: `https://tudominio.com/app/controllers/UserViewController.php`

### Mantenimiento regular:
- ✓ Respaldar BD semanalmente
- ✓ Respaldar archivos a Git
- ✓ Revisar logs si hay errores
- ✓ Monitorear almacenamiento

---

## 🆘 PROBLEMAS RÁPIDOS

### El sitio muestra 404
```
→ Verificar .htaccess está presente
→ Contactar Infinity Free para mod_rewrite
→ Revisar permisos (755)
```

### No conecta a BD
```
→ Verificar .env tiene credenciales correctas
→ Probar en phpMyAdmin
→ Revisar usuario BD tiene permisos
```

### Acentos no se ven correctamente
```
→ Revisar .env
→ Revisar .htaccess charset utf8mb4
```

### Otros problemas
```
→ Ejecutar diagnostic.php
→ Revisar logs del servidor
→ Contactar: support@infinityfree.net
```

---

## 📚 Más información
- Guía completa: `DEPLOYMENT_INFINITYFREE.md`
- Checklist: `CHECKLIST_DEPLOYMENT.md`
- Wiki Infinity Free: https://wiki.infinityfree.net/

**¡Listo para producción!** 🚀
