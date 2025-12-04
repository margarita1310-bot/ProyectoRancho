# Checklist de Verificación Previa a Despliegue

## ✓ Tareas Completadas de Adaptación

### Configuración Base
- [x] Crear `config/config.php` con constantes globales
- [x] Crear `.env.example` como plantilla
- [x] Actualizar `app/models/Conexion.php` para usar variables de entorno
- [x] Crear `.htaccess` para URL rewriting
- [x] Crear `index.php` principal
- [x] Actualizar puntos de entrada públicos

### Mejoras de Seguridad
- [x] Crear `.gitignore` con archivos sensitivos
- [x] Configurar headers de seguridad en `.htaccess`
- [x] Proteger archivos `.env`, `.env.example`, `config/config.php`

### Herramientas de Diagnóstico
- [x] Crear `app/helpers/ConfigValidator.php`
- [x] Crear `diagnostic.php` para validación
- [x] Crear `DEPLOYMENT_INFINITYFREE.md` con instrucciones

### Scripts Auxiliares
- [x] Crear `prepare_for_deployment.sh` (Linux/Mac)
- [x] Crear `prepare_for_deployment.ps1` (Windows)
- [x] Crear `database_structure.sql` como plantilla

### Documentación
- [x] Crear `ADAPTACION_INFINITYFREE.json` con resumen de cambios
- [x] Actualizar controladores principales

---

## 📋 Checklist Previo al Upload a Infinity Free

### Antes de subir archivos:
- [ ] Editar `.env` con credenciales reales de Infinity Free
- [ ] Verificar que `composer.json` está en la raíz
- [ ] Revisar que `.env` NO está en `.gitignore` (será necesario subirlo)
- [ ] Crear copia de seguridad local

### Preparación en Infinity Free:
- [ ] Crear cuenta en Infinity Free (si aún no tienes)
- [ ] Crear dominio/subdominio en panel de control
- [ ] Crear base de datos MySQL
- [ ] Crear usuario MySQL con permisos totales
- [ ] Anotar: servidor BD, usuario, contraseña, nombre BD

### Upload de archivos:
- [ ] Conectar por SFTP usando credenciales de Infinity Free
- [ ] Crear carpeta principal en el directorio raíz del hosting
- [ ] Subir TODOS los archivos incluyendo:
  - [ ] `.env` (con credenciales correctas)
  - [ ] `.htaccess` (puede ser oculto en algunos programas)
  - [ ] `.gitignore` (opcional pero recomendado)
  - [ ] Todos los directorios (app/, config/, public/, tests/, etc.)
  - [ ] vendor/ (carpeta de Composer)
  - [ ] composer.json y composer.lock
  - [ ] index.php

### Configuración de base de datos:
- [ ] Acceder a phpMyAdmin del hosting
- [ ] Seleccionar la base de datos creada
- [ ] Ir a "Importar"
- [ ] Subir archivo `database_structure.sql`
- [ ] Ejecutar importación
- [ ] Verificar que las tablas se crearon correctamente

### Permisos de archivo:
- [ ] Carpetas: 755
- [ ] Archivos PHP: 644
- [ ] `.env`: 600 (crítico)
- [ ] Directorios de carga (`public/images/`): 755

### Verificación inicial:
- [ ] Acceder a `https://tudominio.com/diagnostic.php`
- [ ] Verificar todas las validaciones pasan
- [ ] Revisar versión de PHP, extensiones, conexión a BD
- [ ] Eliminar `diagnostic.php` después de verificar

### Pruebas funcionales:
- [ ] Acceder a login: `https://tudominio.com/app/controllers/LoginController.php?action=login`
- [ ] Probar login con credenciales
- [ ] Verificar sesión se mantiene
- [ ] Acceder a dashboard de usuario: `https://tudominio.com/app/controllers/UserViewController.php`
- [ ] Probar funciones principales (crear reserva, ver promociones, etc.)
- [ ] Verificar que se pueden subir imágenes
- [ ] Probar logout

### Optimización:
- [ ] Eliminar `diagnostic.php`
- [ ] Eliminar `.env.example` (o mantener como referencia)
- [ ] Eliminar `DEPLOYMENT_INFINITYFREE.md` (opcional)
- [ ] Eliminar scripts de preparación (`.sh` y `.ps1`)
- [ ] Configurar DEBUG_MODE=false en `.env`

### Mantenimiento posterior:
- [ ] Configurar respaldos automáticos de BD
- [ ] Configurar respaldos automáticos de archivos
- [ ] Revisar logs de error regularmente
- [ ] Actualizar dependencias de Composer si es necesario
- [ ] Monitorear uso de recursos (base de datos, almacenamiento)

---

## 🚨 Troubleshooting Rápido

### No funciona el acceso a la página:
1. Verificar que `.htaccess` está presente
2. Contactar a Infinity Free para habilitar `mod_rewrite`
3. Revisar permisos de archivos
4. Revisar logs de error del servidor

### Error de conexión a BD:
1. Ejecutar `diagnostic.php`
2. Verificar credenciales en `.env`
3. Probar conexión en phpMyAdmin
4. Verificar que usuario BD tiene permisos en la BD

### Error: "Archivo no encontrado":
1. Verificar rutas en `config.php`
2. Revisar permisos 755 en carpetas
3. Verificar que archivos fueron subidos correctamente
4. Revisar `.htaccess` está intacto

### Acentos o caracteres especiales no se ven:
1. Verificar charset en `.htaccess`: `utf8mb4`
2. Verificar BD usa charset `utf8mb4`
3. Verificar que archivos PHP están en UTF-8
4. Verificar headers en respuestas HTTP

---

## 📞 Contactos de Soporte

- **Infinity Free Wiki**: https://wiki.infinityfree.net/
- **Infinity Free Panel**: https://app.infinityfree.net/
- **Email Soporte**: support@infinityfree.net

---

## ✨ Notas Importantes

1. **Seguridad**: El archivo `.env` contiene credenciales. Proteger con permisos 600.
2. **Performance**: Infinity Free puede tener limitaciones de CPU/RAM. Optimizar consultas.
3. **Persistencia**: Algunos datos en `/tmp/` pueden perderse. Usar BD para datos permanentes.
4. **Límites**: Revisar límites de Infinity Free (upload, ejecución, etc.)
5. **Respaldos**: Hacer respaldos regulares de BD y archivos.

---

**Última actualización**: 2025-12-03
**Estado**: Proyecto adaptado y listo para producción
