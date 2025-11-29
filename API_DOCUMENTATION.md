# API de Gestión de Usuarios

API REST desarrollada con Laravel 12 y Laravel Sanctum para la gestión completa de usuarios con autenticación basada en tokens.

## 📋 Características

- ✅ **CRUD completo de usuarios**
- ✅ **Autenticación con Laravel Sanctum**
- ✅ **Tokens con expiración de 5 minutos**
- ✅ **Refresh token automático**
- ✅ **Soft Delete de usuarios**
- ✅ **Estadísticas por día, semana y mes**
- ✅ **Validación de datos con Form Requests**
- ✅ **Roles de usuario (admin/user)**
- ✅ **Control de usuarios activos/inactivos**

## 🚀 Instalación

### Requisitos Previos

- PHP >= 8.2
- Composer
- MySQL/PostgreSQL/SQLite
- Node.js y NPM (opcional)

### Pasos de Instalación

1. **Clonar el repositorio**
```bash
cd usuarios-api
```

2. **Instalar dependencias**
```bash
composer install
```

3. **Configurar archivo de entorno**
```bash
cp .env.example .env
```

4. **Configurar base de datos en el archivo `.env`**
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=usuarios_api
DB_USERNAME=root
DB_PASSWORD=
```

5. **Generar clave de aplicación**
```bash
php artisan key:generate
```

6. **Ejecutar migraciones**
```bash
php artisan migrate
```

7. **Ejecutar seeders (opcional - crea usuarios de prueba)**
```bash
php artisan db:seed
```

8. **Iniciar servidor de desarrollo**
```bash
php artisan serve
```

La API estará disponible en: `http://localhost:8000/api`

## 📚 Documentación de la API

### Base URL
```
http://localhost:8000/api
```

### Autenticación

Todas las rutas protegidas requieren el header:
```
Authorization: Bearer {token}
```

---

## 🔐 Endpoints de Autenticación

### 1. Login
Autentica un usuario y obtiene un token de acceso.

**Endpoint:** `POST /api/auth/login`

**Body:**
```json
{
    "email": "admin@example.com",
    "password": "password123"
}
```

**Respuesta exitosa (200):**
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
            "created_at": "2024-01-01T00:00:00.000000Z",
            "updated_at": "2024-01-01T00:00:00.000000Z"
        },
        "token": "1|abcdef123456...",
        "token_type": "Bearer",
        "expires_in": 300
    }
}
```

### 2. Logout
Cierra la sesión actual y revoca el token.

**Endpoint:** `POST /api/auth/logout`

**Headers:**
```
Authorization: Bearer {token}
```

**Respuesta exitosa (200):**
```json
{
    "success": true,
    "message": "Logout exitoso"
}
```

### 3. Refresh Token
Refresca el token de acceso antes de que expire.

**Endpoint:** `POST /api/auth/refresh`

**Headers:**
```
Authorization: Bearer {token}
```

**Respuesta exitosa (200):**
```json
{
    "success": true,
    "message": "Token refrescado exitosamente",
    "data": {
        "token": "2|newtoken123456...",
        "token_type": "Bearer",
        "expires_in": 300
    }
}
```

### 4. Usuario Autenticado
Obtiene información del usuario autenticado.

**Endpoint:** `GET /api/auth/me`

**Headers:**
```
Authorization: Bearer {token}
```

**Respuesta exitosa (200):**
```json
{
    "success": true,
    "message": "Usuario autenticado",
    "data": {
        "id": 1,
        "name": "Administrador",
        "email": "admin@example.com",
        "role": "admin",
        "isActive": true
    }
}
```

### 5. Logout de Todos los Dispositivos
Revoca todos los tokens del usuario.

**Endpoint:** `POST /api/auth/logout-all`

**Headers:**
```
Authorization: Bearer {token}
```

---

## 👥 Endpoints de Usuarios (CRUD)

### 1. Listar Usuarios
Obtiene todos los usuarios con paginación.

**Endpoint:** `GET /api/users`

**Headers:**
```
Authorization: Bearer {token}
```

**Query Parameters (opcionales):**
- `page`: Número de página (default: 1)
- `per_page`: Elementos por página (default: 15)

**Respuesta exitosa (200):**
```json
{
    "success": true,
    "message": "Usuarios obtenidos correctamente",
    "data": {
        "current_page": 1,
        "data": [
            {
                "id": 1,
                "name": "Usuario 1",
                "email": "user1@example.com",
                "role": "user",
                "isActive": true,
                "created_at": "2024-01-01T00:00:00.000000Z"
            }
        ],
        "total": 50,
        "per_page": 15
    }
}
```

### 2. Crear Usuario
Crea un nuevo usuario en el sistema.

**Endpoint:** `POST /api/users`

**Headers:**
```
Authorization: Bearer {token}
```

**Body:**
```json
{
    "name": "Nuevo Usuario",
    "email": "nuevo@example.com",
    "password": "password123",
    "role": "user",
    "isActive": true
}
```

**Campos:**
- `name` (requerido): Nombre del usuario
- `email` (requerido): Email único
- `password` (requerido): Mínimo 8 caracteres
- `role` (opcional): "admin" o "user" (default: "user")
- `isActive` (opcional): true o false (default: true)

**Respuesta exitosa (201):**
```json
{
    "success": true,
    "message": "Usuario creado exitosamente",
    "data": {
        "id": 51,
        "name": "Nuevo Usuario",
        "email": "nuevo@example.com",
        "role": "user",
        "isActive": true,
        "created_at": "2024-01-01T00:00:00.000000Z"
    }
}
```

### 3. Obtener Usuario Específico
Obtiene los detalles de un usuario.

**Endpoint:** `GET /api/users/{id}`

**Headers:**
```
Authorization: Bearer {token}
```

**Respuesta exitosa (200):**
```json
{
    "success": true,
    "message": "Usuario obtenido correctamente",
    "data": {
        "id": 1,
        "name": "Usuario",
        "email": "user@example.com",
        "role": "user",
        "isActive": true
    }
}
```

### 4. Actualizar Usuario
Actualiza la información de un usuario.

**Endpoint:** `PUT /api/users/{id}` o `PATCH /api/users/{id}`

**Headers:**
```
Authorization: Bearer {token}
```

**Body (todos los campos son opcionales):**
```json
{
    "name": "Nombre Actualizado",
    "email": "actualizado@example.com",
    "password": "newpassword123",
    "role": "admin",
    "isActive": false
}
```

**Respuesta exitosa (200):**
```json
{
    "success": true,
    "message": "Usuario actualizado exitosamente",
    "data": {
        "id": 1,
        "name": "Nombre Actualizado",
        "email": "actualizado@example.com",
        "role": "admin",
        "isActive": false
    }
}
```

### 5. Eliminar Usuario (Soft Delete)
Elimina un usuario de forma suave (puede restaurarse).

**Endpoint:** `DELETE /api/users/{id}`

**Headers:**
```
Authorization: Bearer {token}
```

**Respuesta exitosa (200):**
```json
{
    "success": true,
    "message": "Usuario eliminado exitosamente"
}
```

### 6. Listar Usuarios Eliminados
Obtiene todos los usuarios eliminados.

**Endpoint:** `GET /api/users/trashed/list`

**Headers:**
```
Authorization: Bearer {token}
```

### 7. Restaurar Usuario
Restaura un usuario eliminado.

**Endpoint:** `POST /api/users/{id}/restore`

**Headers:**
```
Authorization: Bearer {token}
```

---

## 📊 Endpoints de Estadísticas

### 1. Estadísticas Generales
Obtiene estadísticas completas de usuarios.

**Endpoint:** `GET /api/statistics`

**Headers:**
```
Authorization: Bearer {token}
```

**Respuesta exitosa (200):**
```json
{
    "success": true,
    "message": "Estadísticas obtenidas correctamente",
    "data": {
        "summary": {
            "total_users": 52,
            "active_users": 47,
            "inactive_users": 5,
            "deleted_users": 3
        },
        "by_role": {
            "admin": 10,
            "user": 42
        },
        "registrations": {
            "today": 5,
            "this_week": 23,
            "this_month": 45
        },
        "by_day": [
            {
                "date": "2024-01-01",
                "total": 5
            }
        ],
        "by_month": [
            {
                "period": "2024-01",
                "month_name": "enero",
                "total": 45
            }
        ]
    }
}
```

### 2. Estadísticas Diarias
Obtiene usuarios registrados por día (últimos 30 días).

**Endpoint:** `GET /api/statistics/daily`

**Headers:**
```
Authorization: Bearer {token}
```

### 3. Estadísticas Semanales
Obtiene usuarios registrados por semana (últimas 12 semanas).

**Endpoint:** `GET /api/statistics/weekly`

**Headers:**
```
Authorization: Bearer {token}
```

### 4. Estadísticas Mensuales
Obtiene usuarios registrados por mes (últimos 12 meses).

**Endpoint:** `GET /api/statistics/monthly`

**Headers:**
```
Authorization: Bearer {token}
```

---

## 🧪 Testing de la API

### Con cURL

**Login:**
```bash
curl -X POST http://localhost:8000/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "admin@example.com",
    "password": "password123"
  }'
```

**Crear Usuario:**
```bash
curl -X POST http://localhost:8000/api/users \
  -H "Authorization: Bearer {tu_token}" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Test User",
    "email": "test@example.com",
    "password": "password123"
  }'
```

### Con Postman

1. Importar la colección de endpoints
2. Configurar la variable `base_url` como `http://localhost:8000/api`
3. Usar el endpoint de login para obtener el token
4. Configurar el token en la pestaña Authorization de Postman

---

## 🔧 Comandos Útiles

```bash
# Ejecutar migraciones
php artisan migrate

# Revertir migraciones
php artisan migrate:rollback

# Refrescar base de datos y seeders
php artisan migrate:fresh --seed

# Limpiar caché
php artisan cache:clear
php artisan config:clear
php artisan route:clear

# Ver rutas
php artisan route:list

# Crear nuevo controlador
php artisan make:controller NombreController

# Crear nuevo modelo con migración
php artisan make:model Nombre -m
```

---

## 📝 Estructura de Campos del Usuario

| Campo | Tipo | Descripción | Requerido |
|-------|------|-------------|-----------|
| id | Integer | Identificador único | Auto |
| name | String | Nombre del usuario | Sí |
| email | String | Email único | Sí |
| password | String | Contraseña hasheada | Sí |
| role | Enum | admin/user | No (default: user) |
| isActive | Boolean | Estado activo/inactivo | No (default: true) |
| created_at | Timestamp | Fecha de creación | Auto |
| updated_at | Timestamp | Fecha de actualización | Auto |
| deleted_at | Timestamp | Fecha de eliminación | Auto (soft delete) |

---

## 🔒 Seguridad

- ✅ Contraseñas hasheadas con bcrypt
- ✅ Validación de datos en todas las peticiones
- ✅ Tokens con expiración de 5 minutos
- ✅ Protección CSRF habilitada
- ✅ Sanitización de inputs
- ✅ Rate limiting en rutas API

---

## 👨‍💻 Usuarios de Prueba

Después de ejecutar los seeders:

| Email | Password | Role |
|-------|----------|------|
| admin@example.com | password123 | admin |
| user@example.com | password123 | user |

---

## 📄 Licencia

Este proyecto es de código abierto bajo la licencia MIT.

---

## 🤝 Contribuciones

Las contribuciones son bienvenidas. Por favor, abre un issue o pull request.

---

## 📞 Soporte

Para soporte o preguntas, por favor abre un issue en el repositorio.
