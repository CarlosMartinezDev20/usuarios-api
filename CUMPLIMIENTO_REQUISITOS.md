# ✅ CUMPLIMIENTO DE REQUISITOS - API de Gestión de Usuarios

## 📋 Requisitos Solicitados

### ✅ 1. API para Gestionar Usuarios - Operaciones CRUD

**Requisito:** Desarrollar una API para gestionar usuarios, permitiendo operaciones CRUD (Crear, Leer, Actualizar y Eliminar).

**Implementación Completa:**

#### 📍 Endpoints CRUD Implementados

| Operación | Método HTTP | Endpoint | Descripción |
|-----------|-------------|----------|-------------|
| **CREATE** | POST | `/api/users` | Crea un nuevo usuario |
| **READ** | GET | `/api/users` | Lista todos los usuarios |
| **READ** | GET | `/api/users/{id}` | Obtiene un usuario por ID |
| **UPDATE** | PUT | `/api/users/{id}` | Actualiza un usuario |
| **DELETE** | DELETE | `/api/users/{id}` | Elimina un usuario |

#### 📄 Código de Referencia
```php
// app/Http/Controllers/UserController.php
- store()    → Crear
- index()    → Leer todos
- show()     → Leer uno
- update()   → Actualizar
- destroy()  → Eliminar
```

**Campos gestionados:** name, email, password, role, isActive

---

### ✅ 2. Autenticación con Token de Acceso

**Requisito:** Implementar autenticación utilizando un token de acceso. El token debe tener una expiración configurada para refrescarse cada cierto tiempo (por ejemplo, cada 5 minutos).

**Implementación Completa:**

#### 🔐 Sistema de Autenticación (AuthController.php)

| Endpoint | Método | Función | Token Expira |
|----------|--------|---------|--------------|
| `/api/auth/register` | POST | Registro de nuevos usuarios | ✅ 5 minutos |
| `/api/auth/login` | POST | Login y generación de token | ✅ 5 minutos |
| `/api/auth/refresh` | POST | Refrescar token antes de expirar | ✅ 5 minutos |
| `/api/auth/logout` | POST | Cerrar sesión (revoca token actual) | N/A |

#### ⏱️ Configuración de Expiración del Token

```php
// AuthController.php - login() y refresh()
$token = $user->createToken(
    'auth_token',
    ['*'],
    now()->addMinutes(5)  // ⏰ Expira en 5 minutos exactos
)->plainTextToken;

// Respuesta incluye tiempo de expiración
'expires_in' => 300  // 300 segundos = 5 minutos
```

#### 🔒 Protección de Rutas

```php
// routes/api.php
Route::middleware('auth:sanctum')->group(function () {
    // Todas las rutas CRUD y estadísticas están protegidas
    Route::apiResource('users', UserController::class);
    Route::prefix('statistics')->group(/* ... */);
});
```

#### 🛡️ Características de Seguridad

- ✅ Token JWT mediante Laravel Sanctum
- ✅ Expiración automática en 5 minutos
- ✅ Endpoint de refresh para renovar token
- ✅ Revocación de tokens al logout
- ✅ Verificación de usuario activo en login
- ✅ Hashing de contraseñas con bcrypt
- ✅ Validación de credenciales

---

### ✅ 3. Estadísticas de Usuarios por Día, Semana y Mes

**Requisito:** Generar estadísticas basadas en los usuarios registrados en la base de datos, reflejando la cantidad de usuarios registrados por día, semana y mes.

**Implementación Completa:**

#### 📊 Endpoints de Estadísticas (StatisticsController.php)

| Endpoint | Método | Descripción | Período |
|----------|--------|-------------|---------|
| `/api/statistics/daily` | GET | Registros agrupados por día | Últimos 30 días |
| `/api/statistics/weekly` | GET | Registros agrupados por semana | Últimas 12 semanas |
| `/api/statistics/monthly` | GET | Registros agrupados por mes | Últimos 12 meses |

#### 📈 Datos Proporcionados por Endpoint

##### 1. `/api/statistics/daily` (Por Día)
```json
[
  {
    "date": "2025-11-25",
    "total": 52
  }
]
```

##### 2. `/api/statistics/weekly` (Por Semana)
```json
[
  {
    "period": "Año 2025 - Semana 48",
    "year": 2025,
    "week": 48,
    "total": 52
  }
]
```

##### 3. `/api/statistics/monthly` (Por Mes)
```json
[
  {
    "period": "2025-11",
    "month_name": "noviembre",
    "year": 2025,
    "month": 11,
    "total": 52
  }
]
```

#### 🗄️ Consultas SQL Implementadas

```php
// Estadísticas diarias
User::select(
    DB::raw('DATE(created_at) as date'),
    DB::raw('count(*) as total')
)
->where('created_at', '>=', Carbon::now()->subDays(30))
->groupBy('date')
->orderBy('date', 'asc')
->get();

// Estadísticas semanales
User::select(
    DB::raw('YEAR(created_at) as year'),
    DB::raw('WEEK(created_at) as week'),
    DB::raw('count(*) as total')
)
->where('created_at', '>=', Carbon::now()->subWeeks(12))
->groupBy('year', 'week')
->get();

// Estadísticas mensuales
User::select(
    DB::raw('YEAR(created_at) as year'),
    DB::raw('MONTH(created_at) as month'),
    DB::raw('count(*) as total')
)
->where('created_at', '>=', Carbon::now()->subMonths(12))
->groupBy('year', 'month')
->get();
```

---

## 🎯 Resumen de Cumplimiento

| # | Requisito | Estado | Implementación |
|---|-----------|--------|----------------|
| 1 | **CRUD de Usuarios** | ✅ **100%** | 5 endpoints (Crear, Leer, Leer uno, Actualizar, Eliminar) |
| 2 | **Token con Expiración de 5 min** | ✅ **100%** | Register + Login + Refresh (todos con token de 5 minutos) |
| 3 | **Estadísticas por Día/Semana/Mes** | ✅ **100%** | 3 endpoints (daily, weekly, monthly) |

---

## 📁 Estructura de Archivos Clave

```
usuarios-api/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── AuthController.php      ← Autenticación y tokens
│   │   │   ├── UserController.php      ← CRUD de usuarios
│   │   │   └── StatisticsController.php ← Estadísticas
│   │   └── Requests/
│   │       ├── LoginRequest.php
│   │       ├── StoreUserRequest.php
│   │       └── UpdateUserRequest.php
│   └── Models/
│       └── User.php                     ← Modelo con SoftDeletes
├── routes/
│   └── api.php                          ← Definición de todas las rutas
├── config/
│   └── sanctum.php                      ← Configuración de tokens
└── database/
    └── migrations/
        └── create_users_table.php       ← Estructura de BD
```

---

## 🧪 Cómo Probar los Endpoints

### 1. Registrar Usuario y Obtener Token
```bash
POST http://127.0.0.1:8000/api/auth/register
Body: {
  "name": "Juan Pérez",
  "email": "juan@example.com",
  "password": "password123",
  "password_confirmation": "password123"
}

# Respuesta incluye token que expira en 5 minutos
```

### 2. Login (si ya tienes cuenta)
```bash
POST http://127.0.0.1:8000/api/auth/login
Body: {
  "email": "juan@example.com",
  "password": "password123"
}
```

### 3. Crear Usuario (con token)
```bash
POST http://127.0.0.1:8000/api/users
Headers: Authorization: Bearer {token}
Body: {
  "name": "Juan Pérez",
  "email": "juan@example.com",
  "password": "password123",
  "password_confirmation": "password123",
  "role": "user",
  "isActive": true
}
```

### 4. Ver Estadísticas
```bash
# Por día
GET http://127.0.0.1:8000/api/statistics/daily
Headers: Authorization: Bearer {token}

# Por semana
GET http://127.0.0.1:8000/api/statistics/weekly
Headers: Authorization: Bearer {token}

# Por mes
GET http://127.0.0.1:8000/api/statistics/monthly
Headers: Authorization: Bearer {token}
```

### 5. Refrescar Token (antes de expirar)
```bash
POST http://127.0.0.1:8000/api/auth/refresh
Headers: Authorization: Bearer {token}
```

---

## ✅ Conclusión

**Todos los requisitos han sido implementados al 100%:**

1. ✅ **CRUD de Usuarios:** 5 endpoints (POST, GET, GET/:id, PUT/:id, DELETE/:id)
2. ✅ **Autenticación con Token:** Register + Login + Refresh (expiración de 5 minutos)
3. ✅ **Estadísticas:** 3 endpoints (daily, weekly, monthly)

**Total de Endpoints Implementados: 12**
- 2 Autenticación (register, login)
- 2 Gestión de Token (refresh, logout)
- 5 CRUD Usuarios
- 3 Estadísticas

**Tecnologías utilizadas:**
- Laravel 12
- Laravel Sanctum (tokens API)
- MySQL
- PHP 8.2+

La API cumple exactamente con los requisitos especificados y está lista para uso.
