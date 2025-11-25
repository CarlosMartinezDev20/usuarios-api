# 🚀 INICIO RÁPIDO - API de Usuarios

## ⚡ Configuración en 5 Minutos

### Paso 1: Instalar Dependencias
```bash
composer install
```

### Paso 2: Configurar Entorno
```bash
# Copiar archivo de configuración
cp .env.example .env

# Generar clave de aplicación
php artisan key:generate
```

### Paso 3: Configurar Base de Datos
Editar el archivo `.env`:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=usuarios_api
DB_USERNAME=root
DB_PASSWORD=tu_password_aqui
```

### Paso 4: Crear Base de Datos
```sql
CREATE DATABASE usuarios_api;
```

### Paso 5: Ejecutar Migraciones
```bash
# Ejecutar migraciones
php artisan migrate

# Cargar datos de prueba (opcional pero recomendado)
php artisan db:seed
```

### Paso 6: Iniciar Servidor
```bash
php artisan serve
```

✅ **¡Listo!** Tu API está corriendo en: `http://localhost:8000/api`

---

## 🧪 Probar la API

### 1. Health Check
```bash
curl http://localhost:8000/api/health
```

### 2. Login (obtener token)
```bash
curl -X POST http://localhost:8000/api/auth/login \
  -H "Content-Type: application/json" \
  -d "{\"email\":\"admin@example.com\",\"password\":\"password123\"}"
```

**Guarda el token de la respuesta!**

### 3. Listar Usuarios
```bash
curl -X GET http://localhost:8000/api/users \
  -H "Authorization: Bearer TU_TOKEN_AQUI"
```

---

## 👥 Usuarios de Prueba

Después de ejecutar `php artisan db:seed`:

| Email | Password | Rol |
|-------|----------|-----|
| admin@example.com | password123 | admin |
| user@example.com | password123 | user |

---

## 📚 Documentación

- **Documentación completa**: Ver `API_DOCUMENTATION.md`
- **Guía de testing**: Ver `TESTING_GUIDE.md`
- **Colección de Postman**: Importar `postman_collection.json`
- **Resumen técnico**: Ver `IMPLEMENTATION_SUMMARY.md`

---

## 🔥 Endpoints Principales

### Autenticación
- `POST /api/auth/login` - Login y obtener token
- `POST /api/auth/logout` - Cerrar sesión
- `POST /api/auth/refresh` - Refrescar token
- `GET /api/auth/me` - Info del usuario autenticado

### Usuarios
- `GET /api/users` - Listar todos
- `POST /api/users` - Crear nuevo
- `GET /api/users/{id}` - Obtener uno
- `PUT /api/users/{id}` - Actualizar
- `DELETE /api/users/{id}` - Eliminar

### Estadísticas
- `GET /api/statistics` - Estadísticas completas
- `GET /api/statistics/daily` - Por día
- `GET /api/statistics/weekly` - Por semana
- `GET /api/statistics/monthly` - Por mes

---

## 🛠️ Comandos Útiles

```bash
# Ver todas las rutas
php artisan route:list

# Limpiar caché
php artisan cache:clear
php artisan config:clear

# Refrescar base de datos
php artisan migrate:fresh --seed

# Ejecutar tests
php artisan test
```

---

## ⚠️ Importante

1. **Token Expiration**: Los tokens expiran en 5 minutos. Usar refresh token.
2. **Base de datos**: Asegúrate de crear la base de datos antes de migrar.
3. **Composer**: Necesitas tener Composer instalado.
4. **PHP**: Requiere PHP 8.2 o superior.

---

## 🎯 Testing con Postman

1. Importar `postman_collection.json`
2. Configurar variable `base_url`: `http://localhost:8000/api`
3. Hacer login para obtener token
4. El token se guarda automáticamente
5. Probar los demás endpoints

---

## ❓ Problemas Comunes

### Error: "Could not find driver"
```bash
# Instalar extensión de MySQL para PHP
sudo apt-get install php8.2-mysql  # Linux
```

### Error: "Access denied for user"
```bash
# Verificar credenciales en .env
# Asegúrate de que DB_USERNAME y DB_PASSWORD sean correctos
```

### Error: "Base table or view not found"
```bash
# Ejecutar migraciones
php artisan migrate
```

---

## 📞 ¿Necesitas Ayuda?

1. Revisa `README.md` para documentación general
2. Consulta `API_DOCUMENTATION.md` para detalles de endpoints
3. Ve `TESTING_GUIDE.md` para casos de prueba
4. Lee `IMPLEMENTATION_SUMMARY.md` para detalles técnicos

---

<p align="center">
<strong>¡Todo listo para empezar! 🚀</strong>
</p>
