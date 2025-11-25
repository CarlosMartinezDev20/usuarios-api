# 🚀 API de Gestión de Usuarios - Laravel

API REST completa para la gestión de usuarios desarrollada con **Laravel 12** y **Laravel Sanctum**. Incluye autenticación con tokens, operaciones CRUD, estadísticas y soft delete.

## 📋 Características Principales

✅ **CRUD Completo de Usuarios**
- Crear, leer, actualizar y eliminar usuarios
- Soft delete con posibilidad de restauración
- Paginación automática

✅ **Autenticación Segura**
- Laravel Sanctum para tokens API
- Tokens con expiración de 5 minutos
- Refresh token automático
- Logout individual y múltiple

✅ **Gestión de Usuarios**
- Roles: Admin y User
- Control de usuarios activos/inactivos
- Contraseñas hasheadas con bcrypt
- Validación completa de datos

✅ **Estadísticas**
- Usuarios registrados por día
- Usuarios registrados por semana
- Usuarios registrados por mes
- Estadísticas generales del sistema

✅ **Documentación**
- Colección de Postman incluida
- Guía de testing completa
- Documentación de API detallada
- Comentarios en código

## 🛠️ Tecnologías Utilizadas

- **Laravel 12** - Framework PHP
- **Laravel Sanctum** - Autenticación API
- **MySQL/PostgreSQL** - Base de datos
- **PHP 8.2+** - Lenguaje de programación

## 📦 Instalación Rápida

### 1. Requisitos Previos

- PHP >= 8.2
- Composer
- MySQL/PostgreSQL/SQLite
- Servidor web (Apache/Nginx) o PHP built-in server

### 2. Instalación

```bash
# Clonar el repositorio
cd usuarios-api

# Instalar dependencias
composer install

# Copiar archivo de entorno
cp .env.example .env

# Generar clave de aplicación
php artisan key:generate

# Configurar base de datos en .env
# Editar: DB_DATABASE, DB_USERNAME, DB_PASSWORD

# Ejecutar migraciones
php artisan migrate

# Ejecutar seeders (opcional - crea usuarios de prueba)
php artisan db:seed

# Iniciar servidor
php artisan serve
```

La API estará disponible en: `http://localhost:8000/api`

## 🔑 Usuarios de Prueba

Después de ejecutar los seeders:

| Email | Password | Rol |
|-------|----------|-----|
| admin@example.com | password123 | admin |
| user@example.com | password123 | user |

## 📚 Documentación

### Documentación Completa
Ver archivo [API_DOCUMENTATION.md](./API_DOCUMENTATION.md) para documentación detallada de todos los endpoints.

### Guía de Testing
Ver archivo [TESTING_GUIDE.md](./TESTING_GUIDE.md) para casos de prueba completos.

### Colección de Postman
Importar el archivo [postman_collection.json](./postman_collection.json) en Postman o Insomnia.

## 🚀 Uso Rápido

### 1. Login
```bash
POST http://localhost:8000/api/auth/login
Content-Type: application/json

{
    "email": "admin@example.com",
    "password": "password123"
}
```

### 2. Crear Usuario
```bash
POST http://localhost:8000/api/users
Authorization: Bearer {token}
Content-Type: application/json

{
    "name": "Nuevo Usuario",
    "email": "nuevo@example.com",
    "password": "password123",
    "password_confirmation": "password123",
    "role": "user"
}
```

### 3. Obtener Estadísticas
```bash
GET http://localhost:8000/api/statistics
Authorization: Bearer {token}
```

## 📁 Estructura del Proyecto

```
usuarios-api/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── AuthController.php       # Autenticación
│   │   │   ├── UserController.php       # CRUD de usuarios
│   │   │   └── StatisticsController.php # Estadísticas
│   │   └── Requests/
│   │       ├── LoginRequest.php         # Validación login
│   │       ├── StoreUserRequest.php     # Validación crear usuario
│   │       └── UpdateUserRequest.php    # Validación actualizar
│   └── Models/
│       └── User.php                     # Modelo de usuario
├── database/
│   ├── migrations/                      # Migraciones de BD
│   ├── seeders/                         # Seeders de prueba
│   └── factories/                       # Factories
├── routes/
│   └── api.php                          # Rutas de la API
├── API_DOCUMENTATION.md                 # Documentación completa
├── TESTING_GUIDE.md                     # Guía de testing
└── postman_collection.json              # Colección de Postman
```

## 🔐 Endpoints Principales

### Autenticación
- `POST /api/auth/login` - Login
- `POST /api/auth/logout` - Logout
- `POST /api/auth/refresh` - Refresh token
- `GET /api/auth/me` - Usuario autenticado

### Usuarios (CRUD)
- `GET /api/users` - Listar usuarios
- `POST /api/users` - Crear usuario
- `GET /api/users/{id}` - Obtener usuario
- `PUT /api/users/{id}` - Actualizar usuario
- `DELETE /api/users/{id}` - Eliminar usuario
- `GET /api/users/trashed/list` - Usuarios eliminados
- `POST /api/users/{id}/restore` - Restaurar usuario

### Estadísticas
- `GET /api/statistics` - Estadísticas generales
- `GET /api/statistics/daily` - Por día
- `GET /api/statistics/weekly` - Por semana
- `GET /api/statistics/monthly` - Por mes

## 🧪 Testing

```bash
# Ejecutar tests
php artisan test

# Ver rutas
php artisan route:list

# Limpiar caché
php artisan cache:clear
php artisan config:clear
```

## 📊 Campos del Usuario

| Campo | Tipo | Descripción | Requerido |
|-------|------|-------------|-----------|
| name | String | Nombre completo | Sí |
| email | String | Email único | Sí |
| password | String | Contraseña (min 8 chars) | Sí |
| role | Enum | admin/user | No |
| isActive | Boolean | Activo/Inactivo | No |
| created_at | Timestamp | Fecha creación | Auto |
| updated_at | Timestamp | Fecha actualización | Auto |
| deleted_at | Timestamp | Fecha eliminación | Auto |

## 🔒 Seguridad

- ✅ Contraseñas hasheadas con bcrypt
- ✅ Tokens con expiración (5 minutos)
- ✅ Validación de datos en todas las peticiones
- ✅ Protección CSRF
- ✅ Soft delete para recuperación de datos
- ✅ Sanitización de inputs

## 🛠️ Comandos Artisan Útiles

```bash
# Crear nuevo controlador con recursos
php artisan make:controller NombreController --resource

# Crear modelo con migración
php artisan make:model Nombre -m

# Crear Request de validación
php artisan make:request NombreRequest

# Crear Seeder
php artisan make:seeder NombreSeeder

# Refrescar base de datos
php artisan migrate:fresh --seed
```

## 📝 Notas Importantes

1. **Expiración de Tokens**: Los tokens expiran en 5 minutos. Usar el endpoint de refresh antes de que expire.
2. **Paginación**: Los listados retornan 15 elementos por página por defecto.
3. **Soft Delete**: Los usuarios eliminados pueden ser restaurados.
4. **Validación**: Todos los endpoints validan los datos de entrada.
5. **Roles**: Solo dos roles disponibles: `admin` y `user`.

## 📄 Licencia

Este proyecto es de código abierto bajo la licencia MIT.

## 🤝 Contribuciones

Las contribuciones son bienvenidas. Por favor, abre un issue o pull request.

## 📞 Soporte

Para preguntas o soporte, por favor abre un issue en el repositorio.

---

<p align="center">Desarrollado con ❤️ usando Laravel</p>
