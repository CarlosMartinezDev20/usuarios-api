# 📖 Guía Completa de Endpoints - API Gestión de Usuarios

## 🌐 Información General

**Base URL:** `http://127.0.0.1:8000/api`

**Autenticación:** Bearer Token (en headers para rutas protegidas)

**Token Expiración:** 5 minutos

---

## 📑 Índice de Endpoints

1. [Autenticación](#autenticación) (4 endpoints)
2. [CRUD Usuarios](#crud-usuarios) (5 endpoints)
3. [Estadísticas](#estadísticas) (3 endpoints)

**Total: 12 endpoints**

---

# 🔐 AUTENTICACIÓN

## 1. Registrar Usuario

**Endpoint:** `POST /api/auth/register`

**Autenticación:** No requerida (público)

**Descripción:** Registra un nuevo usuario y devuelve un token de acceso con expiración de 5 minutos.

### Request Body
```json
{
    "name": "Juan Pérez",
    "email": "juan@example.com",
    "password": "password123"
}
```

### Campos del Body
| Campo | Tipo | Requerido | Descripción |
|-------|------|-----------|-------------|
| name | string | ✅ Sí | Nombre completo del usuario |
| email | string | ✅ Sí | Email único (debe ser válido) |
| password | string | ✅ Sí | Contraseña (mínimo 8 caracteres) |

### Respuesta Exitosa (201)
```json
{
    "success": true,
    "message": "Registro exitoso",
    "data": {
        "user": {
            "id": 1,
            "name": "Juan Pérez",
            "email": "juan@example.com",
            "role": "user",
            "isActive": true,
            "created_at": "2025-11-28T08:00:00.000000Z",
            "updated_at": "2025-11-28T08:00:00.000000Z"
        },
        "token": "1|abc123def456...",
        "token_type": "Bearer",
        "expires_in": 300
    }
}
```

### Respuesta de Error (422)
```json
{
    "success": false,
    "message": "Datos inválidos",
    "errors": {
        "email": ["El email ya está registrado"],
        "password": ["La contraseña debe tener al menos 8 caracteres"]
    }
}
```

---

## 2. Login

**Endpoint:** `POST /api/auth/login`

**Autenticación:** No requerida (público)

**Descripción:** Autentica un usuario existente y devuelve un token de acceso con expiración de 5 minutos.

### Request Body
```json
{
    "email": "admin@example.com",
    "password": "password123"
}
```

### Campos del Body
| Campo | Tipo | Requerido | Descripción |
|-------|------|-----------|-------------|
| email | string | ✅ Sí | Email del usuario registrado |
| password | string | ✅ Sí | Contraseña del usuario |

### Respuesta Exitosa (200)
```json
{
    "success": true,
    "message": "Login exitoso",
    "data": {
        "user": {
            "id": 1,
            "name": "Administrador",
            "email": "admin@example.com",
            "role": "admin",
            "isActive": true,
            "created_at": "2025-11-25T00:00:00.000000Z",
            "updated_at": "2025-11-25T00:00:00.000000Z"
        },
        "token": "2|xyz789abc012...",
        "token_type": "Bearer",
        "expires_in": 300
    }
}
```

### Respuesta de Error (401)
```json
{
    "success": false,
    "message": "Credenciales incorrectas"
}
```

### Respuesta de Error - Usuario Inactivo (403)
```json
{
    "success": false,
    "message": "Usuario inactivo. Contacte al administrador."
}
```

---

## 3. Refrescar Token

**Endpoint:** `POST /api/auth/refresh`

**Autenticación:** ✅ Requerida (Bearer Token)

**Descripción:** Refresca el token actual antes de que expire. Revoca el token anterior y genera uno nuevo con 5 minutos de vigencia.

### Headers
```
Authorization: Bearer {token_actual}
```

### Request Body
No requiere body

### Respuesta Exitosa (200)
```json
{
    "success": true,
    "message": "Token refrescado exitosamente",
    "data": {
        "token": "3|new123token456...",
        "token_type": "Bearer",
        "expires_in": 300
    }
}
```

### Respuesta de Error (401)
```json
{
    "message": "Unauthenticated."
}
```

---

## 4. Logout

**Endpoint:** `POST /api/auth/logout`

**Autenticación:** ✅ Requerida (Bearer Token)

**Descripción:** Cierra la sesión del usuario y revoca el token actual.

### Headers
```
Authorization: Bearer {token}
```

### Request Body
No requiere body

### Respuesta Exitosa (200)
```json
{
    "success": true,
    "message": "Logout exitoso"
}
```

---

# 👥 CRUD USUARIOS

## 5. Crear Usuario

**Endpoint:** `POST /api/users`

**Autenticación:** ✅ Requerida (Bearer Token)

**Descripción:** Crea un nuevo usuario en el sistema con todos sus datos.

### Headers
```
Authorization: Bearer {token}
Content-Type: application/json
```

### Request Body
```json
{
    "name": "María García",
    "email": "maria@example.com",
    "password": "password123",
    "role": "user",
    "isActive": true
}
```

### Campos del Body
| Campo | Tipo | Requerido | Valores | Descripción |
|-------|------|-----------|---------|-------------|
| name | string | ✅ Sí | - | Nombre completo (max 255 caracteres) |
| email | string | ✅ Sí | - | Email único y válido |
| password | string | ✅ Sí | - | Contraseña (mínimo 8 caracteres) |
| role | string | ❌ No | user, admin | Rol del usuario (default: user) |
| isActive | boolean | ❌ No | true, false | Estado activo (default: true) |

### Respuesta Exitosa (201)
```json
{
    "success": true,
    "message": "Usuario creado exitosamente",
    "data": {
        "id": 53,
        "name": "María García",
        "email": "maria@example.com",
        "role": "user",
        "isActive": true,
        "created_at": "2025-11-28T10:30:00.000000Z",
        "updated_at": "2025-11-28T10:30:00.000000Z"
    }
}
```

### Respuesta de Error (422)
```json
{
    "success": false,
    "message": "Datos inválidos",
    "errors": {
        "email": ["El email ya ha sido registrado"],
        "password": ["Las contraseñas no coinciden"]
    }
}
```

---

## 6. Listar Usuarios

**Endpoint:** `GET /api/users`

**Autenticación:** ✅ Requerida (Bearer Token)

**Descripción:** Obtiene la lista de todos los usuarios activos del sistema con paginación.

### Headers
```
Authorization: Bearer {token}
```

### Query Parameters (Opcionales)
```
/api/users?page=1
```

| Parámetro | Tipo | Default | Descripción |
|-----------|------|---------|-------------|
| page | integer | 1 | Número de página |

### Request Body
No requiere body

### Respuesta Exitosa (200)
```json
{
    "success": true,
    "message": "Usuarios obtenidos correctamente",
    "data": {
        "current_page": 1,
        "data": [
            {
                "id": 1,
                "name": "Administrador",
                "email": "admin@example.com",
                "role": "admin",
                "isActive": true,
                "created_at": "2025-11-25T00:00:00.000000Z",
                "updated_at": "2025-11-25T00:00:00.000000Z"
            },
            {
                "id": 2,
                "name": "Usuario Test",
                "email": "user@example.com",
                "role": "user",
                "isActive": true,
                "created_at": "2025-11-25T00:00:00.000000Z",
                "updated_at": "2025-11-25T00:00:00.000000Z"
            }
        ],
        "first_page_url": "http://127.0.0.1:8000/api/users?page=1",
        "from": 1,
        "last_page": 4,
        "last_page_url": "http://127.0.0.1:8000/api/users?page=4",
        "next_page_url": "http://127.0.0.1:8000/api/users?page=2",
        "path": "http://127.0.0.1:8000/api/users",
        "per_page": 15,
        "prev_page_url": null,
        "to": 15,
        "total": 52
    }
}
```

---

## 7. Ver Usuario

**Endpoint:** `GET /api/users/{id}`

**Autenticación:** ✅ Requerida (Bearer Token)

**Descripción:** Obtiene la información detallada de un usuario específico por su ID.

### Headers
```
Authorization: Bearer {token}
```

### URL Parameters
| Parámetro | Tipo | Descripción |
|-----------|------|-------------|
| id | integer | ID del usuario a consultar |

### Ejemplo
```
GET /api/users/1
```

### Request Body
No requiere body

### Respuesta Exitosa (200)
```json
{
    "success": true,
    "message": "Usuario obtenido correctamente",
    "data": {
        "id": 1,
        "name": "Administrador",
        "email": "admin@example.com",
        "role": "admin",
        "isActive": true,
        "created_at": "2025-11-25T00:00:00.000000Z",
        "updated_at": "2025-11-25T00:00:00.000000Z"
    }
}
```

### Respuesta de Error (404)
```json
{
    "success": false,
    "message": "Usuario no encontrado",
    "error": "No query results for model [App\\Models\\User] 999"
}
```

---

## 8. Actualizar Usuario

**Endpoint:** `PUT /api/users/{id}`

**Autenticación:** ✅ Requerida (Bearer Token)

**Descripción:** Actualiza la información de un usuario existente. Todos los campos son opcionales.

### Headers
```
Authorization: Bearer {token}
Content-Type: application/json
```

### URL Parameters
| Parámetro | Tipo | Descripción |
|-----------|------|-------------|
| id | integer | ID del usuario a actualizar |

### Ejemplo
```
PUT /api/users/53
```

### Request Body (Todos Opcionales)
```json
{
    "name": "María García López",
    "email": "maria.nueva@example.com",
    "password": "newpassword123",
    "role": "admin",
    "isActive": false
}
```

### Campos del Body
| Campo | Tipo | Requerido | Valores | Descripción |
|-------|------|-----------|---------|-------------|
| name | string | ❌ No | - | Nuevo nombre |
| email | string | ❌ No | - | Nuevo email (debe ser único) |
| password | string | ❌ No | - | Nueva contraseña (mínimo 8 caracteres) |
| role | string | ❌ No | user, admin | Nuevo rol |
| isActive | boolean | ❌ No | true, false | Nuevo estado |

### Respuesta Exitosa (200)
```json
{
    "success": true,
    "message": "Usuario actualizado exitosamente",
    "data": {
        "id": 53,
        "name": "María García López",
        "email": "maria.nueva@example.com",
        "role": "admin",
        "isActive": false,
        "created_at": "2025-11-28T10:30:00.000000Z",
        "updated_at": "2025-11-28T11:45:00.000000Z"
    }
}
```

### Respuesta de Error (422)
```json
{
    "success": false,
    "message": "Datos inválidos",
    "errors": {
        "email": ["El email ya está en uso por otro usuario"]
    }
}
```

---

## 9. Eliminar Usuario

**Endpoint:** `DELETE /api/users/{id}`

**Autenticación:** ✅ Requerida (Bearer Token)

**Descripción:** Elimina un usuario del sistema (soft delete). El usuario puede ser restaurado posteriormente.

### Headers
```
Authorization: Bearer {token}
```

### URL Parameters
| Parámetro | Tipo | Descripción |
|-----------|------|-------------|
| id | integer | ID del usuario a eliminar |

### Ejemplo
```
DELETE /api/users/53
```

### Request Body
No requiere body

### Respuesta Exitosa (200)
```json
{
    "success": true,
    "message": "Usuario eliminado exitosamente"
}
```

### Respuesta de Error (404)
```json
{
    "success": false,
    "message": "Error al eliminar el usuario",
    "error": "No query results for model [App\\Models\\User] 999"
}
```

---

# 📊 ESTADÍSTICAS

## 10. Estadísticas Diarias

**Endpoint:** `GET /api/statistics/daily`

**Autenticación:** ✅ Requerida (Bearer Token)

**Descripción:** Obtiene la cantidad de usuarios registrados por día durante los últimos 30 días.

### Headers
```
Authorization: Bearer {token}
```

### Request Body
No requiere body

### Respuesta Exitosa (200)
```json
{
    "success": true,
    "message": "Estadísticas diarias obtenidas correctamente",
    "data": [
        {
            "date": "2025-11-25",
            "total": 52
        },
        {
            "date": "2025-11-26",
            "total": 3
        },
        {
            "date": "2025-11-27",
            "total": 5
        },
        {
            "date": "2025-11-28",
            "total": 8
        }
    ]
}
```

### Estructura de Respuesta
| Campo | Tipo | Descripción |
|-------|------|-------------|
| date | string | Fecha en formato YYYY-MM-DD |
| total | integer | Cantidad de usuarios registrados ese día |

---

## 11. Estadísticas Semanales

**Endpoint:** `GET /api/statistics/weekly`

**Autenticación:** ✅ Requerida (Bearer Token)

**Descripción:** Obtiene la cantidad de usuarios registrados por semana durante las últimas 12 semanas.

### Headers
```
Authorization: Bearer {token}
```

### Request Body
No requiere body

### Respuesta Exitosa (200)
```json
{
    "success": true,
    "message": "Estadísticas semanales obtenidas correctamente",
    "data": [
        {
            "period": "Año 2025 - Semana 47",
            "year": 2025,
            "week": 47,
            "total": 52
        },
        {
            "period": "Año 2025 - Semana 48",
            "year": 2025,
            "week": 48,
            "total": 16
        }
    ]
}
```

### Estructura de Respuesta
| Campo | Tipo | Descripción |
|-------|------|-------------|
| period | string | Descripción legible del período |
| year | integer | Año |
| week | integer | Número de semana (1-52) |
| total | integer | Cantidad de usuarios registrados esa semana |

---

## 12. Estadísticas Mensuales

**Endpoint:** `GET /api/statistics/monthly`

**Autenticación:** ✅ Requerida (Bearer Token)

**Descripción:** Obtiene la cantidad de usuarios registrados por mes durante los últimos 12 meses.

### Headers
```
Authorization: Bearer {token}
```

### Request Body
No requiere body

### Respuesta Exitosa (200)
```json
{
    "success": true,
    "message": "Estadísticas mensuales obtenidas correctamente",
    "data": [
        {
            "period": "2025-01",
            "month_name": "enero",
            "year": 2025,
            "month": 1,
            "total": 15
        },
        {
            "period": "2025-02",
            "month_name": "febrero",
            "year": 2025,
            "month": 2,
            "total": 23
        },
        {
            "period": "2025-11",
            "month_name": "noviembre",
            "year": 2025,
            "month": 11,
            "total": 68
        }
    ]
}
```

### Estructura de Respuesta
| Campo | Tipo | Descripción |
|-------|------|-------------|
| period | string | Período en formato YYYY-MM |
| month_name | string | Nombre del mes en español |
| year | integer | Año |
| month | integer | Número de mes (1-12) |
| total | integer | Cantidad de usuarios registrados ese mes |

---

# 🔧 Códigos de Estado HTTP

| Código | Significado | Cuándo se usa |
|--------|-------------|---------------|
| 200 | OK | Operación exitosa (GET, PUT, DELETE) |
| 201 | Created | Recurso creado exitosamente (POST) |
| 400 | Bad Request | Error en la solicitud |
| 401 | Unauthorized | Token inválido o expirado |
| 403 | Forbidden | Usuario sin permisos o inactivo |
| 404 | Not Found | Recurso no encontrado |
| 422 | Unprocessable Entity | Errores de validación |
| 500 | Internal Server Error | Error del servidor |

---

# 📝 Ejemplos de Uso con cURL

## Registro
```bash
curl -X POST http://127.0.0.1:8000/api/auth/register \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Test User",
    "email": "test@example.com",
    "password": "password123"
  }'
```

## Login
```bash
curl -X POST http://127.0.0.1:8000/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "admin@example.com",
    "password": "password123"
  }'
```

## Crear Usuario (con token)
```bash
curl -X POST http://127.0.0.1:8000/api/users \
  -H "Authorization: Bearer 1|abc123..." \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Nuevo Usuario",
    "email": "nuevo@example.com",
    "password": "password123",
    "role": "user"
  }'
```

## Listar Usuarios (con token)
```bash
curl -X GET http://127.0.0.1:8000/api/users \
  -H "Authorization: Bearer 1|abc123..."
```

## Ver Estadísticas Diarias (con token)
```bash
curl -X GET http://127.0.0.1:8000/api/statistics/daily \
  -H "Authorization: Bearer 1|abc123..."
```

---

# 📝 Ejemplos de Uso con PowerShell

## Registro
```powershell
$body = @{
    name = "Test User"
    email = "test@example.com"
    password = "password123"
} | ConvertTo-Json

Invoke-RestMethod -Method Post -Uri "http://127.0.0.1:8000/api/auth/register" `
  -Body $body -ContentType "application/json"
```

## Login y Guardar Token
```powershell
$loginBody = @{
    email = "admin@example.com"
    password = "password123"
} | ConvertTo-Json

$response = Invoke-RestMethod -Method Post -Uri "http://127.0.0.1:8000/api/auth/login" `
  -Body $loginBody -ContentType "application/json"

$token = $response.data.token
Write-Host "Token guardado: $token"
```

## Crear Usuario con Token
```powershell
$userBody = @{
    name = "Nuevo Usuario"
    email = "nuevo@example.com"
    password = "password123"
    role = "user"
} | ConvertTo-Json

Invoke-RestMethod -Method Post -Uri "http://127.0.0.1:8000/api/users" `
  -Headers @{ Authorization = "Bearer $token" } `
  -Body $userBody -ContentType "application/json"
```

## Listar Usuarios
```powershell
Invoke-RestMethod -Method Get -Uri "http://127.0.0.1:8000/api/users" `
  -Headers @{ Authorization = "Bearer $token" }
```

## Ver Estadísticas
```powershell
# Diarias
Invoke-RestMethod -Method Get -Uri "http://127.0.0.1:8000/api/statistics/daily" `
  -Headers @{ Authorization = "Bearer $token" }

# Semanales
Invoke-RestMethod -Method Get -Uri "http://127.0.0.1:8000/api/statistics/weekly" `
  -Headers @{ Authorization = "Bearer $token" }

# Mensuales
Invoke-RestMethod -Method Get -Uri "http://127.0.0.1:8000/api/statistics/monthly" `
  -Headers @{ Authorization = "Bearer $token" }
```

---

# 🎯 Flujo Típico de Uso

```
1. REGISTRAR USUARIO o LOGIN
   POST /api/auth/register o POST /api/auth/login
   └─> Guardar token recibido

2. USAR TOKEN EN TODAS LAS PETICIONES
   - Agregar header: Authorization: Bearer {token}
   
3. OPERACIONES CRUD
   - Crear usuarios: POST /api/users
   - Listar: GET /api/users
   - Ver uno: GET /api/users/{id}
   - Actualizar: PUT /api/users/{id}
   - Eliminar: DELETE /api/users/{id}

4. VER ESTADÍSTICAS
   - Diarias: GET /api/statistics/daily
   - Semanales: GET /api/statistics/weekly
   - Mensuales: GET /api/statistics/monthly

5. ANTES DE 5 MINUTOS
   POST /api/auth/refresh
   └─> Actualizar token

6. AL TERMINAR
   POST /api/auth/logout
```

---

# ⚠️ Notas Importantes

1. **Token Expiration:** El token expira en exactamente 5 minutos (300 segundos)
2. **Refresh Token:** Debe refrescarse antes de que expire para mantener la sesión
3. **Soft Delete:** Los usuarios eliminados no se borran físicamente de la base de datos
4. **Paginación:** Los listados devuelven 15 elementos por página
5. **Roles:** Solo existen dos roles: `admin` y `user`
6. **Email Único:** Cada email solo puede registrarse una vez
7. **Password Hash:** Las contraseñas se almacenan hasheadas con bcrypt

---

# 📞 Información de Contacto

- **Repositorio:** CarlosMartinezDev20/usuarios-api
- **Documentación adicional:** Ver archivos README.md, API_DOCUMENTATION.md, CUMPLIMIENTO_REQUISITOS.md

---

**Última actualización:** 28 de noviembre de 2025
