# 📐 ESTRUCTURA Y ARQUITECTURA DEL PROYECTO

## 🏗️ Arquitectura General

```
┌─────────────────────────────────────────────────────────┐
│                      CLIENT/POSTMAN                      │
└────────────────────┬────────────────────────────────────┘
                     │ HTTP/JSON
                     ▼
┌─────────────────────────────────────────────────────────┐
│                   LARAVEL API ROUTER                     │
│                    (routes/api.php)                      │
└────────────────────┬────────────────────────────────────┘
                     │
         ┌───────────┴───────────┐
         ▼                       ▼
┌──────────────────┐    ┌──────────────────┐
│  Public Routes   │    │ Protected Routes │
│                  │    │  (auth:sanctum)  │
│  - Login         │    │  - CRUD Users    │
│  - Health        │    │  - Statistics    │
└──────────────────┘    │  - Logout        │
                        │  - Refresh       │
                        └────────┬─────────┘
                                 │
                    ┌────────────┴────────────┐
                    ▼                         ▼
         ┌──────────────────┐      ┌──────────────────┐
         │  Form Requests   │      │   Controllers    │
         │                  │      │                  │
         │  - LoginRequest  │      │  - AuthCtrl      │
         │  - StoreUser     │◄─────┤  - UserCtrl      │
         │  - UpdateUser    │      │  - StatisticsCtrl│
         └──────────────────┘      └────────┬─────────┘
                                            │
                                            ▼
                                   ┌──────────────────┐
                                   │     Models       │
                                   │                  │
                                   │  - User Model    │
                                   │    • HasApiTokens│
                                   │    • SoftDeletes │
                                   └────────┬─────────┘
                                            │
                                            ▼
                                   ┌──────────────────┐
                                   │    Database      │
                                   │                  │
                                   │  - users table   │
                                   │  - tokens table  │
                                   └──────────────────┘
```

---

## 📁 Estructura de Archivos

```
usuarios-api/
│
├── 📂 app/
│   ├── 📂 Http/
│   │   ├── 📂 Controllers/
│   │   │   ├── 📄 AuthController.php          # Login, Logout, Refresh, Me
│   │   │   ├── 📄 UserController.php          # CRUD + Restore + Trashed
│   │   │   └── 📄 StatisticsController.php    # Stats por día/semana/mes
│   │   │
│   │   └── 📂 Requests/
│   │       ├── 📄 LoginRequest.php            # Validación de login
│   │       ├── 📄 StoreUserRequest.php        # Validación crear usuario
│   │       └── 📄 UpdateUserRequest.php       # Validación actualizar
│   │
│   └── 📂 Models/
│       └── 📄 User.php                        # Modelo con Sanctum + SoftDelete
│
├── 📂 database/
│   ├── 📂 factories/
│   │   └── 📄 UserFactory.php                 # Factory para testing
│   │
│   ├── 📂 migrations/
│   │   ├── 📄 *_create_users_table.php        # Tabla usuarios
│   │   └── 📄 *_personal_access_tokens.php    # Tabla tokens (Sanctum)
│   │
│   └── 📂 seeders/
│       ├── 📄 DatabaseSeeder.php              # Seeder principal
│       └── 📄 AdminUserSeeder.php             # Usuarios de prueba
│
├── 📂 routes/
│   ├── 📄 api.php                             # ⭐ Rutas de la API
│   ├── 📄 web.php                             # Rutas web
│   └── 📄 console.php                         # Comandos artisan
│
├── 📂 config/
│   ├── 📄 sanctum.php                         # Configuración de Sanctum
│   ├── 📄 database.php                        # Configuración de BD
│   └── 📄 app.php                             # Configuración general
│
├── 📂 bootstrap/
│   └── 📄 app.php                             # Bootstrap (rutas API)
│
├── 📄 .env.example                            # Ejemplo de configuración
├── 📄 composer.json                           # Dependencias PHP
│
└── 📂 Documentación/
    ├── 📄 README.md                           # Documentación principal
    ├── 📄 API_DOCUMENTATION.md                # Docs de endpoints
    ├── 📄 TESTING_GUIDE.md                    # Guía de testing
    ├── 📄 IMPLEMENTATION_SUMMARY.md           # Resumen técnico
    ├── 📄 QUICK_START.md                      # Inicio rápido
    └── 📄 postman_collection.json             # Colección Postman
```

---

## 🔄 Flujo de Autenticación

```
1. LOGIN
   ┌─────────┐
   │ Cliente │
   └────┬────┘
        │ POST /api/auth/login
        │ {email, password}
        ▼
   ┌─────────────┐
   │AuthController│
   └──────┬──────┘
          │ Valida credenciales
          │ Verifica isActive
          ▼
   ┌──────────┐
   │User Model│
   └────┬─────┘
        │ createToken(expires: 5min)
        ▼
   ┌─────────────────┐
   │ Token generado  │
   │ Respuesta JSON  │
   └─────────────────┘

2. REQUESTS AUTENTICADOS
   ┌─────────┐
   │ Cliente │
   └────┬────┘
        │ GET /api/users
        │ Header: Authorization Bearer {token}
        ▼
   ┌──────────────────┐
   │ Middleware       │
   │ auth:sanctum     │
   └────┬─────────────┘
        │ Valida token
        │ Verifica expiración
        ▼
   ┌──────────────────┐
   │ UserController   │
   │ Ejecuta acción   │
   └──────────────────┘

3. REFRESH TOKEN
   ┌─────────┐
   │ Cliente │
   └────┬────┘
        │ POST /api/auth/refresh
        │ Bearer {token_actual}
        ▼
   ┌─────────────┐
   │AuthController│
   └──────┬──────┘
          │ Revoca token actual
          │ Crea nuevo token (5min)
          ▼
   ┌─────────────────┐
   │ Nuevo Token     │
   └─────────────────┘
```

---

## 🗄️ Esquema de Base de Datos

```sql
┌──────────────────────────────────────────────────────┐
│                    TABLA: users                       │
├──────────────────┬──────────────┬────────────────────┤
│ Campo            │ Tipo         │ Descripción        │
├──────────────────┼──────────────┼────────────────────┤
│ id               │ BIGINT       │ PK, AUTO_INCREMENT │
│ name             │ VARCHAR(255) │ Nombre completo    │
│ email            │ VARCHAR(255) │ UNIQUE, NOT NULL   │
│ password         │ VARCHAR(255) │ Hash bcrypt        │
│ role             │ ENUM         │ admin/user         │
│ isActive         │ BOOLEAN      │ true/false         │
│ email_verified_at│ TIMESTAMP    │ Nullable           │
│ remember_token   │ VARCHAR(100) │ Nullable           │
│ created_at       │ TIMESTAMP    │ Auto               │
│ updated_at       │ TIMESTAMP    │ Auto               │
│ deleted_at       │ TIMESTAMP    │ Nullable (soft)    │
└──────────────────┴──────────────┴────────────────────┘

┌──────────────────────────────────────────────────────┐
│         TABLA: personal_access_tokens (Sanctum)       │
├──────────────────┬──────────────┬────────────────────┤
│ Campo            │ Tipo         │ Descripción        │
├──────────────────┼──────────────┼────────────────────┤
│ id               │ BIGINT       │ PK, AUTO_INCREMENT │
│ tokenable_type   │ VARCHAR(255) │ Tipo (User)        │
│ tokenable_id     │ BIGINT       │ User ID            │
│ name             │ VARCHAR(255) │ Nombre token       │
│ token            │ VARCHAR(64)  │ Hash token         │
│ abilities        │ TEXT         │ Permisos           │
│ expires_at       │ TIMESTAMP    │ Expiración (5min)  │
│ created_at       │ TIMESTAMP    │ Auto               │
│ updated_at       │ TIMESTAMP    │ Auto               │
└──────────────────┴──────────────┴────────────────────┘

RELACIONES:
users (1) ──────< (N) personal_access_tokens
```

---

## 🛣️ Mapa de Rutas

```
API BASE: /api

PUBLIC ROUTES:
│
├─ /health                     [GET]    Health check
└─ /auth/login                 [POST]   Login

PROTECTED ROUTES (auth:sanctum):
│
├─ /auth/
│  ├─ logout                   [POST]   Logout actual
│  ├─ logout-all               [POST]   Logout todos
│  ├─ refresh                  [POST]   Refresh token
│  └─ me                       [GET]    Info usuario
│
├─ /users/
│  ├─ /                        [GET]    Listar (paginado)
│  ├─ /                        [POST]   Crear
│  ├─ /{id}                    [GET]    Obtener uno
│  ├─ /{id}                    [PUT]    Actualizar
│  ├─ /{id}                    [DELETE] Eliminar (soft)
│  ├─ /trashed/list            [GET]    Listar eliminados
│  └─ /{id}/restore            [POST]   Restaurar
│
└─ /statistics/
   ├─ /                        [GET]    Todas las stats
   ├─ /daily                   [GET]    Por día
   ├─ /weekly                  [GET]    Por semana
   └─ /monthly                 [GET]    Por mes
```

---

## 🔐 Flujo de Seguridad

```
┌────────────────────────────────────────────────────┐
│              CAPAS DE SEGURIDAD                     │
└────────────────────────────────────────────────────┘

1. VALIDACIÓN DE DATOS
   └─> Form Requests (LoginRequest, StoreUserRequest, etc.)
       └─> Reglas de validación
       └─> Mensajes personalizados

2. AUTENTICACIÓN
   └─> Laravel Sanctum Middleware (auth:sanctum)
       └─> Verifica token válido
       └─> Verifica expiración (5 minutos)
       └─> Carga usuario autenticado

3. AUTORIZACIÓN
   └─> Verifica isActive en login
       └─> Solo usuarios activos pueden autenticarse

4. PROTECCIÓN DE DATOS
   └─> Passwords hasheados (bcrypt)
   └─> Hidden fields (password, remember_token)
   └─> Tokens encriptados

5. SOFT DELETE
   └─> Datos no se eliminan permanentemente
       └─> Posibilidad de restauración
       └─> Auditoría completa

6. CSRF PROTECTION
   └─> Habilitado por defecto
       └─> Para requests stateful
```

---

## 📊 Diagrama de Clases Principales

```
┌─────────────────────────────────────────┐
│            User Model                    │
├─────────────────────────────────────────┤
│ Traits:                                  │
│  - HasFactory                            │
│  - Notifiable                            │
│  - HasApiTokens         (Sanctum)        │
│  - SoftDeletes                           │
├─────────────────────────────────────────┤
│ Fillable:                                │
│  - name, email, password                 │
│  - role, isActive                        │
├─────────────────────────────────────────┤
│ Hidden:                                  │
│  - password, remember_token              │
├─────────────────────────────────────────┤
│ Casts:                                   │
│  - password: hashed                      │
│  - isActive: boolean                     │
│  - *_at: datetime                        │
└─────────────────────────────────────────┘

┌─────────────────────────────────────────┐
│         AuthController                   │
├─────────────────────────────────────────┤
│ Methods:                                 │
│  + login(LoginRequest)                   │
│  + logout(Request)                       │
│  + refresh(Request)                      │
│  + me(Request)                           │
│  + logoutAll(Request)                    │
└─────────────────────────────────────────┘

┌─────────────────────────────────────────┐
│         UserController                   │
├─────────────────────────────────────────┤
│ Methods:                                 │
│  + index(): JsonResponse                 │
│  + store(StoreUserRequest)               │
│  + show(string $id)                      │
│  + update(UpdateUserRequest, $id)        │
│  + destroy(string $id)                   │
│  + restore(string $id)                   │
│  + trashed()                             │
└─────────────────────────────────────────┘

┌─────────────────────────────────────────┐
│      StatisticsController                │
├─────────────────────────────────────────┤
│ Methods:                                 │
│  + index(): JsonResponse                 │
│  + daily(): JsonResponse                 │
│  + weekly(): JsonResponse                │
│  + monthly(): JsonResponse               │
└─────────────────────────────────────────┘
```

---

## 🔄 Ciclo de Vida de un Request

```
1. Cliente envía request
   ↓
2. Laravel recibe request (public/index.php)
   ↓
3. Router busca ruta (routes/api.php)
   ↓
4. Middleware auth:sanctum (si es ruta protegida)
   ├─ Valida token
   └─ Carga usuario
   ↓
5. Form Request (validación)
   ├─ Valida campos
   └─ Retorna errores si falla
   ↓
6. Controller ejecuta método
   ├─ Lógica de negocio
   └─ Interactúa con Model
   ↓
7. Model accede a DB (Eloquent ORM)
   ↓
8. Controller retorna JsonResponse
   ↓
9. Laravel serializa respuesta
   ↓
10. Cliente recibe JSON
```

---

## 📈 Características Implementadas

```
✅ CRUD COMPLETO
   ├─ Create: POST /users
   ├─ Read: GET /users, GET /users/{id}
   ├─ Update: PUT /users/{id}
   └─ Delete: DELETE /users/{id}

✅ AUTENTICACIÓN
   ├─ Laravel Sanctum
   ├─ Token con expiración (5 min)
   ├─ Refresh token
   └─ Logout múltiple

✅ ESTADÍSTICAS
   ├─ Por día (últimos 30 días)
   ├─ Por semana (últimas 12 semanas)
   └─ Por mes (últimos 12 meses)

✅ SOFT DELETE
   ├─ Usuarios no se borran permanentemente
   ├─ Listado de eliminados
   └─ Restauración

✅ VALIDACIÓN
   ├─ Form Requests
   ├─ Mensajes personalizados
   └─ Validación de campos únicos

✅ SEGURIDAD
   ├─ Passwords hasheados
   ├─ Tokens encriptados
   ├─ CSRF protection
   └─ Middleware de autenticación
```

---

## 🎯 Puntos de Extensión Futura

```
POSIBLES MEJORAS:

1. Roles y Permisos Avanzados
   └─> Implementar Spatie Permission
   └─> Múltiples roles por usuario

2. Rate Limiting
   └─> Limitar requests por minuto
   └─> Protección contra ataques

3. API Versioning
   └─> /api/v1/, /api/v2/
   └─> Versionado de endpoints

4. Logging Avanzado
   └─> Log de todas las acciones
   └─> Auditoría completa

5. Testing Automatizado
   └─> Unit Tests
   └─> Feature Tests
   └─> Integration Tests

6. Cache
   └─> Redis para cache
   └─> Mejorar performance

7. Queue Jobs
   └─> Envío de emails
   └─> Procesos en background

8. API Documentation
   └─> Swagger/OpenAPI
   └─> Documentación interactiva
```

---

<p align="center">
<strong>Arquitectura implementada con Laravel Best Practices</strong>
</p>
