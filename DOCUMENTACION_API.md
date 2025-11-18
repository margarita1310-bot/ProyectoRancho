# Documentación de Modelos y Controladores - La Joya Gestión

## 📋 Índice
1. [Controladores](#controladores)
2. [Modelos](#modelos)
3. [Flujo de Autenticación](#flujo-de-autenticación)
4. [Respuestas API](#respuestas-api)

---

## Controladores

### 1. **AdminController.php**
**Ruta:** `app/controllers/AdminController.php`

**Descripción:** Controlador principal del dashboard administrativo. Maneja acciones de administración general y carga datos de todos los módulos.

**Acciones:**

| Acción | Método | Parámetros | Descripción |
|--------|--------|-----------|-------------|
| `dashboard` | GET | - | Carga el dashboard principal con productos, promociones y eventos |
| `logout` | GET | - | Cierra la sesión del administrador y redirige a login |

**Comportamiento especial:**
- Requiere autenticación (`ensureAdmin()`)
- Si la acción no existe y es una petición AJAX, retorna JSON 404
- Si no es AJAX, muestra mensaje de texto

---

### 2. **Auth.php**
**Ruta:** `app/controllers/Auth.php`

**Descripción:** Módulo de autenticación y autorización. Verifica que el usuario tenga sesión de administrador activa.

**Funciones:**

| Función | Parámetros | Retorno | Descripción |
|---------|-----------|--------|-------------|
| `ensureAdmin()` | - | void | Verifica sesión de admin; si no existe, redirige a login o retorna JSON 401 para AJAX |

**Lógica de detección AJAX:**
- Verifica header `X-Requested-With: XMLHttpRequest`
- Verifica header `Accept: application/json`
- Si es AJAX sin sesión: retorna `{"status":"error","message":"unauthorized"}` con HTTP 401
- Si no es AJAX: redirige a `LoginController.php?action=login`

---

### 3. **LoginController.php**
**Ruta:** `app/controllers/LoginController.php`

**Descripción:** Gestiona el inicio y cierre de sesión. Valida credenciales contra la tabla `administrador`.

**Acciones:**

| Acción | Método | Parámetros | Descripción |
|--------|--------|-----------|-------------|
| `login` | GET | - | Muestra formulario de login (`app/views/login/login.php`) |
| `autenticar` | POST | `correo`, `password` | Valida credenciales y crea sesión si son correctas |
| `logout` | GET | - | Destruye sesión y redirige a login |

**Validación:**
- Usa `Usuario::verificar()` para autenticar
- Si credenciales correctas: crea `$_SESSION['admin']` y redirige a dashboard
- Si incorrectas: muestra formulario con mensaje de error

**Comportamiento especial:**
- Si acción no existe y es AJAX: retorna JSON 404
- Si no es AJAX: muestra mensaje de texto

---

### 4. **MenuController.php**
**Ruta:** `app/controllers/MenuController.php`

**Descripción:** CRUD de productos. Gestiona la creación, lectura, actualización y eliminación de productos del menú.

**Acciones:**

| Acción | Método | Parámetros | Descripción |
|--------|--------|-----------|-------------|
| `index` | GET | - | Muestra lista de productos (`app/views/admin/menu.php`) |
| `guardar` | POST | `nombre`, `precio`, `categoria` | Crea un nuevo producto |
| `obtener` | POST | `id` | Retorna JSON con datos de un producto por ID |
| `actualizar` | POST | `id`, `nombre`, `precio`, `categoria` | Actualiza un producto existente |
| `eliminar` | POST | `id` | Elimina un producto por ID |

**Validaciones:**
- `nombre`: obligatorio, no vacío
- `precio`: obligatorio, numérico, >= 0
- `categoria`: obligatorio, no vacío
- `id`: obligatorio, dígitos

**Respuestas CRUD:**
- **Éxito:** `{"status":"ok"}` con HTTP 200
- **Error de validación:** `{"status":"error","errors":["campo_required"]}` con HTTP 400
- **Error BD:** `{"status":"error","message":"no se pudo crear/actualizar/eliminar el producto"}` con HTTP 500

---

### 5. **PromocionesController.php**
**Ruta:** `app/controllers/PromocionesController.php`

**Descripción:** CRUD de promociones con gestión de imágenes. Crea, lee, actualiza y elimina promociones. Las imágenes se guardan en `public/images/promociones/`.

**Acciones:**

| Acción | Método | Parámetros | Descripción |
|--------|--------|-----------|-------------|
| `index` | GET | - | Muestra lista de promociones |
| `guardar` | POST | `nombre`, `descripcion`, `fecha_inicio`, `fecha_fin`, `estado`, `imagen` (file, OPCIONAL) | Crea nueva promoción con imagen opcional |
| `obtener` | POST | `id` | Retorna JSON con datos de una promoción |
| `actualizar` | POST | `id`, `nombre`, `descripcion`, `fecha_inicio`, `fecha_fin`, `estado`, `imagen` (file, OPCIONAL) | Actualiza promoción con imagen opcional |
| `eliminar` | POST | `id` | Elimina promoción y su imagen |

**Validaciones:**
- `nombre`: obligatorio, no vacío
- `descripcion`: obligatorio, no vacío
- `fecha_inicio` / `fecha_fin`: formato `YYYY-MM-DD` (si se proporcionan)
- `estado`: `'activo'` o `'inactivo'`
- `imagen`: **OPCIONAL** en create/update, máx 2MB, tipos: `image/jpeg`, `image/png`

**Gestión de imágenes:**
- Se generan nombres únicos: `{timestamp}_{random6bytes}.{ext}`
- Al actualizar, se elimina la imagen anterior del filesystem (solo si se sube una nueva)
- Al eliminar, se elimina la imagen del filesystem
- Si no se proporciona imagen: se guarda `null` en la BD y se mantiene/elimina según corresponda

**Respuestas:**
- **Éxito:** `{"status":"ok"}` con HTTP 200
- **Imagen muy grande:** `{"status":"error","errors":["imagen_too_large"]}` con HTTP 400
- **Tipo de imagen inválido:** `{"status":"error","errors":["imagen_invalid_type"]}` con HTTP 400
- **Error upload:** `{"status":"error","message":"upload_failed"}` con HTTP 500
- **Error validación:** `{"status":"error","errors":["campo_invalid"]}` con HTTP 400

---

### 6. **EventosController.php**
**Ruta:** `app/controllers/EventosController.php`

**Descripción:** CRUD de eventos con gestión de imágenes. Idéntico a PromocionesController pero para eventos. Las imágenes se guardan en `public/images/eventos/`.

**Acciones:**

| Acción | Método | Parámetros | Descripción |
|--------|--------|-----------|-------------|
| `index` | GET | - | Muestra lista de eventos |
| `guardar` | POST | `nombre`, `descripcion`, `fecha`, `hora_inicio`, `hora_fin`, `imagen` (file, OPCIONAL) | Crea nuevo evento con imagen opcional |
| `obtener` | POST | `id` | Retorna JSON con datos de un evento |
| `actualizar` | POST | `id`, `nombre`, `descripcion`, `fecha`, `hora_inicio`, `hora_fin`, `imagen` (file, OPCIONAL) | Actualiza evento con imagen opcional |
| `eliminar` | POST | `id` | Elimina evento y su imagen |

**Validaciones:**
- `nombre`: obligatorio
- `descripcion`: obligatorio
- `fecha`: formato `YYYY-MM-DD`, obligatorio
- `hora_inicio` / `hora_fin`: formato `HH:MM` (si se proporcionan), validación regex `^[0-2][0-9]:[0-5][0-9]$`
- `imagen`: **OPCIONAL**, máx 2MB, tipos: `image/jpeg`, `image/png`

**Gestión de imágenes:**
- Idéntica a PromocionesController (opcionalidad incluida)

**Respuestas:**
- Idénticas a PromocionesController

---

### 7. **MesasController.php**
**Ruta:** `app/controllers/MesasController.php`

**Descripción:** Gestiona la disponibilidad de mesas. Permite crear, consultar, actualizar y eliminar registros de disponibilidad por fecha.

**Acciones:**

| Acción | Método | Parámetros | Descripción |
|--------|--------|-----------|-------------|
| `listar` | GET | `fecha` (obligatorio) | Retorna JSON con disponibilidad para una fecha (ej: `?fecha=2025-11-16`) |
| `guardar` | POST | `fecha`, `cantidad` | Crea disponibilidad para una fecha o actualiza si ya existe |
| `actualizar` | POST | `id`, `cantidad` | Actualiza cantidad de mesas para un registro |
| `eliminar` | POST | `id` | Elimina un registro de disponibilidad |

**Validaciones:**
- `fecha`: obligatoria (GET o POST)
- `cantidad`: obligatoria, debe ser dígitos
- `id`: obligatorio para update/delete, debe ser dígitos

**Respuestas:**
- **Éxito:** `{"status":"ok"}` con HTTP 200
- **Falta parámetro:** `{"status":"error","message":"missing_fecha"}` o `{"status":"error","message":"invalid_input"}` con HTTP 400
- **Error BD:** `{"status":"error","message":"db_error"}` con HTTP 500

**Ejemplo de respuesta `listar`:**
```json
{
  "id": 1,
  "fecha": "2025-11-16",
  "cantidad": 15,
  "created_at": "2025-11-16 10:30:00"
}
```

---

### 8. **ReservasController.php**
**Ruta:** `app/controllers/ReservasController.php`

**Descripción:** Gestiona reservas: listar, filtrar por fecha, confirmar y declinar.

**Acciones:**

| Acción | Método | Parámetros | Descripción |
|--------|--------|-----------|-------------|
| `index` | GET | - | Muestra lista de reservas |
| `listar` | GET | `fecha` (opcional) | Retorna JSON con reservas; si `fecha` se filtra por esa fecha |
| `confirmar` | POST | `id`, `mesa` (opcional) | Marca reserva como confirmada; si `mesa` se asigna mesa |
| `declinar` | POST | `id` | Elimina (declina) una reserva |

**Validaciones:**
- `id`: obligatorio, debe ser dígitos
- `mesa`: opcional, si se proporciona debe ser dígitos

**Respuestas:**
- **Éxito:** `{"status":"ok"}` con HTTP 200
- **Falta ID:** `{"status":"error","message":"missing_id"}` con HTTP 400
- **Mesa inválida:** `{"status":"error","message":"mesa_invalid"}` con HTTP 400
- **Error BD:** `{"status":"error","message":"no se pudo confirmar/eliminar"}` con HTTP 500

**Flujo de confirmación:**
- Si `mesa` no se proporciona: solo cambia estado a 'confirmada'
- Si `mesa` se proporciona: cambia estado a 'confirmada' y asigna mesa (columna `mesa` en tabla `reserva`)

---

## Modelos

### 1. **Conexion.php**
**Ruta:** `app/models/Conexion.php`

**Descripción:** Conexión centralizada a la base de datos MySQL usando PDO.

**Métodos estáticos:**

| Método | Retorno | Descripción |
|--------|---------|-------------|
| `Conexion::conectar()` | PDO | Retorna instancia PDO conectada a la base de datos |

**Configuración:** Se asume que está configurada con las credenciales de XAMPP/MySQL (root, localhost, base de datos del proyecto).

---

### 2. **Usuario.php**
**Ruta:** `app/models/Usuario.php`

**Descripción:** Gestiona administradores. Busca, verifica credenciales y actualiza contraseñas (con soporte para migración de hashes).

**Métodos estáticos:**

| Método | Parámetros | Retorno | Descripción |
|--------|-----------|--------|-------------|
| `findByEmail($correo)` | `$correo` | array\|false | Busca administrador por correo; retorna registro o false |
| `verificar($correo, $password)` | `$correo`, `$password` | array\|false | Verifica credenciales; retorna registro o false |
| `updatePasswordHash($id, $hash)` | `$id`, `$hash` | bool | Actualiza hash de contraseña en BD |

**Lógica de `verificar()`:**
- Busca usuario por correo
- Si existe:
  - Si password es hash bcrypt/argon2: usa `password_verify()`
  - Si es texto plano: compara; si coincide, rehashea automáticamente y actualiza BD
  - Si coincide: retorna registro del usuario
  - Si no coincide: retorna false
- Si no existe: retorna false

---

### 3. **Producto.php**
**Ruta:** `app/models/Producto.php`

**Descripción:** Gestiona productos del menú (sin imágenes).

**Métodos:**

| Método | Parámetros | Retorno | Descripción |
|--------|-----------|--------|-------------|
| `getAll()` | - | array | Retorna todos los productos ordenados DESC por ID |
| `create($nombre, $precio, $categoria)` | nombre, precio, categoria | bool | Crea nuevo producto |
| `getById($id)` | $id | array\|false | Busca producto por ID |
| `update($id, $nombre, $precio, $categoria)` | id, nombre, precio, categoria | bool | Actualiza producto |
| `delete($id)` | $id | bool | Elimina producto |

**Tabla:** `producto` (id_producto, nombre, precio, categoria)

---

### 4. **Promocion.php**
**Ruta:** `app/models/Promocion.php`

**Descripción:** Gestiona promociones con soporte opcional para imágenes.

**Métodos:**

| Método | Parámetros | Retorno | Descripción |
|--------|-----------|--------|-------------|
| `getAll()` | - | array | Retorna todas las promociones DESC por ID |
| `create($nombre, $descripcion, $fecha_inicio, $fecha_fin, $estado[, $imagen])` | nombre, descripcion, fecha_inicio, fecha_fin, estado, (imagen opcional) | bool | Crea promoción; imagen es parámetro variádico (6to) |
| `getById($id)` | $id | array\|false | Busca promoción por ID |
| `update($id, $nombre, $descripcion, $fecha_inicio, $fecha_fin, $estado[, $imagen])` | id, nombre, descripcion, fecha_inicio, fecha_fin, estado, (imagen opcional) | bool | Actualiza promoción; si imagen se proporciona la actualiza |
| `delete($id)` | $id | bool | Elimina promoción |

**Tabla:** `promocion` (id_promocion, nombre, descripcion, fecha_inicio, fecha_fin, estado, imagen)

**Notas:**
- Imagen es parámetro variádico (func_get_arg)
- Si `$imagen` se proporciona y no es nula, se actualiza en BD
- Si no se proporciona, se ignora

---

### 5. **Evento.php**
**Ruta:** `app/models/Evento.php`

**Descripción:** Gestiona eventos con soporte opcional para imágenes. Idéntico en estructura a Promocion pero para eventos.

**Métodos:**

| Método | Parámetros | Retorno | Descripción |
|--------|-----------|--------|-------------|
| `getAll()` | - | array | Retorna todos los eventos DESC por ID |
| `create($nombre, $descripcion, $fecha, $hora_inicio, $hora_fin[, $imagen])` | nombre, descripcion, fecha, hora_inicio, hora_fin, (imagen opcional) | bool | Crea evento |
| `getById($id)` | $id | array\|false | Busca evento por ID |
| `update($id, $nombre, $descripcion, $fecha, $hora_inicio, $hora_fin[, $imagen])` | id, nombre, descripcion, fecha, hora_inicio, hora_fin, (imagen opcional) | bool | Actualiza evento |
| `delete($id)` | $id | bool | Elimina evento |

**Tabla:** `evento` (id_evento, nombre, descripcion, fecha, hora_inicio, hora_fin, imagen)

---

### 6. **Reserva.php**
**Ruta:** `app/models/Reserva.php`

**Descripción:** Gestiona reservas. Obtiene, confirma y elimina reservas.

**Métodos:**

| Método | Parámetros | Retorno | Descripción |
|--------|-----------|--------|-------------|
| `getAll()` | - | array | Retorna todas las reservas DESC por ID |
| `getPending()` | - | array | Retorna reservas con estado='pendiente' |
| `getByDate($fecha)` | $fecha | array | Retorna reservas de una fecha específica (YYYY-MM-DD) |
| `getById($id)` | $id | array\|false | Busca reserva por ID |
| `confirm($id[, $mesa])` | id, (mesa opcional) | bool | Marca reserva como confirmada; si mesa se asigna mesa |
| `delete($id)` | $id | bool | Elimina reserva |

**Tabla:** `reserva` (id_reserva, id_cliente, id_evento, fecha, hora, num_personas, folio, estado, codigo_conf, fecha_creacion, mesa)

**Notas:**
- `confirm()` usa parámetro variádico para mesa
- Si mesa se proporciona: `UPDATE ... SET estado='confirmada', mesa=? WHERE id_reserva=?`
- Si mesa no se proporciona: `UPDATE ... SET estado='confirmada' WHERE id_reserva=?`

---

### 7. **MesaDisponibilidad.php**
**Ruta:** `app/models/MesaDisponibilidad.php`

**Descripción:** Gestiona disponibilidad de mesas por fecha.

**Métodos:**

| Método | Parámetros | Retorno | Descripción |
|--------|-----------|--------|-------------|
| `getByDate($fecha)` | $fecha | array\|false | Busca disponibilidad de una fecha |
| `create($fecha, $cantidad)` | fecha, cantidad | bool | Crea disponibilidad o actualiza si existe |
| `update($id, $cantidad)` | id, cantidad | bool | Actualiza cantidad para un ID |
| `delete($id)` | $id | bool | Elimina un registro de disponibilidad |

**Tabla:** `mesas_disponibilidad` (id, fecha, cantidad, created_at)

**Lógica especial en `create()`:**
- Si ya existe disponibilidad para esa fecha: actualiza cantidad (UPSERT)
- Si no existe: inserta nuevo registro

---

### 8. **Mesa.php**
**Ruta:** `app/models/Mesa.php`

**Descripción:** Modelo para mesas individuales (tabla `mesa` si existe).

**Estado:** No se documentan métodos específicos porque en el código actual la tabla `mesas_disponibilidad` es la que gestiona disponibilidad. Mesa.php puede existir para ampliaciones futuras.

---

## Flujo de Autenticación

### Diagrama de flujo:
```
Usuario accede a AdminController.php
           ↓
    ensureAdmin() verifica $_SESSION['admin']
           ↓
    ¿Sesión existe?
           ├─ NO
           │  ├─ ¿Es AJAX? → JSON 401 (unauthorized)
           │  └─ No AJAX  → Redirige a LoginController
           │
           └─ SÍ → Carga dashboard con datos
```

### Login:
```
Usuario entra en LoginController.php?action=login
           ↓
Muestra formulario (login.php)
           ↓
Usuario ingresa correo/password y envía POST a LoginController?action=autenticar
           ↓
Usuario::verificar(correo, password)
           ├─ Válido  → $_SESSION['admin'] = user → Redirige a AdminController?action=dashboard
           └─ Inválido → Muestra formulario con error
```

### Logout:
```
Usuario accede a AdminController?action=logout o LoginController?action=logout
           ↓
session_destroy()
           ↓
Redirige a LoginController?action=login
```

---

## Respuestas API

### Formatos estándar:

**Éxito CRUD:**
```json
{"status":"ok"}
```
HTTP 200

**Error de validación:**
```json
{
  "status":"error",
  "errors":["campo_required", "otro_campo_invalid"]
}
```
HTTP 400

**Error de base de datos:**
```json
{
  "status":"error",
  "message":"descripción del error"
}
```
HTTP 500

**Entidad no encontrada:**
```json
{
  "status":"error",
  "message":"missing_id"
}
```
HTTP 400

**Acción no encontrada (AJAX):**
```json
{
  "status":"error",
  "message":"action_not_found"
}
```
HTTP 404

**No autorizado (AJAX):**
```json
{
  "status":"error",
  "message":"unauthorized"
}
```
HTTP 401

---

## Headers y Convenciones

### Detección de AJAX:
Los controladores detectan peticiones AJAX por:
1. Header `X-Requested-With: XMLHttpRequest` (estándar jQuery)
2. Header `Accept: application/json`

### Content-Type:
- Todos los endpoints CRUD y de datos retornan `Content-Type: application/json; charset=utf-8`
- Los archivos subidos se procesan con validación MIME via `finfo_open(FILEINFO_MIME_TYPE)`

### HTTP Codes:
| Código | Uso |
|--------|-----|
| 200 | Operación exitosa |
| 400 | Error de validación o parámetros inválidos |
| 401 | No autorizado (sin sesión en AJAX) |
| 404 | Acción no encontrada (en AJAX) |
| 500 | Error en base de datos o servidor |

---

## Notas Generales

- **Seguridad:** Todos los controladores requieren `ensureAdmin()` para verificar sesión
- **Parámetros variádicos:** Modelos de Evento, Promocion y Reserva usan `func_num_args()` y `func_get_arg()` para parámetros opcionales
- **Validación:** Se realiza en controladores; modelos asumen datos válidos
- **Imágenes:** Se guardan en `public/images/{promociones|eventos}/` con nombres únicos (timestamp + random)
- **Manejo de errores:** Controladores retornan HTTP codes apropiados para indicar tipo de error

---

**Última actualización:** 16 de noviembre de 2025
