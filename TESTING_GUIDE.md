# Guía de Testing para la API de Usuarios

## 🧪 Configuración Inicial

### 1. Preparar el Entorno de Testing

```bash
# Copiar archivo de entorno
cp .env.example .env

# Generar key
php artisan key:generate

# Configurar base de datos en .env
DB_DATABASE=usuarios_api

# Ejecutar migraciones
php artisan migrate

# Ejecutar seeders para datos de prueba
php artisan db:seed
```

### 2. Iniciar el Servidor

```bash
php artisan serve
```

La API estará disponible en: `http://localhost:8000/api`

---

## 📝 Casos de Prueba

### Test 1: Health Check
Verificar que la API está funcionando.

**Endpoint:** `GET http://localhost:8000/api/health`

**Resultado Esperado:**
```json
{
    "success": true,
    "message": "API funcionando correctamente",
    "timestamp": "2024-01-01 12:00:00"
}
```

---

### Test 2: Login Exitoso

**Endpoint:** `POST http://localhost:8000/api/auth/login`

**Body:**
```json
{
    "email": "admin@example.com",
    "password": "password123"
}
```

**Resultado Esperado:**
- Status: 200
- Respuesta contiene: `token`, `user`, `expires_in`
- El token debe ser guardado para pruebas posteriores

**Comando cURL:**
```bash
curl -X POST http://localhost:8000/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{"email":"admin@example.com","password":"password123"}'
```

---

### Test 3: Login con Credenciales Incorrectas

**Endpoint:** `POST http://localhost:8000/api/auth/login`

**Body:**
```json
{
    "email": "admin@example.com",
    "password": "wrongpassword"
}
```

**Resultado Esperado:**
- Status: 401
- Mensaje: "Credenciales incorrectas"

---

### Test 4: Obtener Usuario Autenticado

**Endpoint:** `GET http://localhost:8000/api/auth/me`

**Headers:**
```
Authorization: Bearer {token}
```

**Resultado Esperado:**
- Status: 200
- Respuesta contiene información del usuario autenticado

**Comando cURL:**
```bash
curl -X GET http://localhost:8000/api/auth/me \
  -H "Authorization: Bearer {TU_TOKEN_AQUI}"
```

---

### Test 5: Crear Usuario

**Endpoint:** `POST http://localhost:8000/api/users`

**Headers:**
```
Authorization: Bearer {token}
Content-Type: application/json
```

**Body:**
```json
{
    "name": "Usuario de Prueba",
    "email": "test@prueba.com",
    "password": "password123",
    "role": "user",
    "isActive": true
}
```

**Resultado Esperado:**
- Status: 201
- Usuario creado correctamente
- Respuesta contiene datos del usuario creado

**Comando cURL:**
```bash
curl -X POST http://localhost:8000/api/users \
  -H "Authorization: Bearer {TU_TOKEN}" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Usuario de Prueba",
    "email": "test@prueba.com",
    "password": "password123",
    "role": "user"
  }'
```

---

### Test 6: Validación - Crear Usuario sin Datos Requeridos

**Endpoint:** `POST http://localhost:8000/api/users`

**Body:**
```json
{
    "name": "Test"
}
```

**Resultado Esperado:**
- Status: 422 (Validation Error)
- Mensaje de errores de validación para email y password

---

### Test 7: Validación - Email Duplicado

**Endpoint:** `POST http://localhost:8000/api/users`

**Body:**
```json
{
    "name": "Usuario",
    "email": "admin@example.com",
    "password": "password123"
}
```

**Resultado Esperado:**
- Status: 422
- Error: "Este email ya está registrado"

---

### Test 8: Listar Todos los Usuarios

**Endpoint:** `GET http://localhost:8000/api/users`

**Headers:**
```
Authorization: Bearer {token}
```

**Resultado Esperado:**
- Status: 200
- Respuesta paginada con lista de usuarios
- Incluye metadata de paginación

**Comando cURL:**
```bash
curl -X GET http://localhost:8000/api/users \
  -H "Authorization: Bearer {TU_TOKEN}"
```

---

### Test 9: Obtener Usuario Específico

**Endpoint:** `GET http://localhost:8000/api/users/1`

**Headers:**
```
Authorization: Bearer {token}
```

**Resultado Esperado:**
- Status: 200
- Información del usuario con ID 1

---

### Test 10: Actualizar Usuario

**Endpoint:** `PUT http://localhost:8000/api/users/1`

**Headers:**
```
Authorization: Bearer {token}
Content-Type: application/json
```

**Body:**
```json
{
    "name": "Nombre Actualizado",
    "isActive": false
}
```

**Resultado Esperado:**
- Status: 200
- Usuario actualizado correctamente

**Comando cURL:**
```bash
curl -X PUT http://localhost:8000/api/users/1 \
  -H "Authorization: Bearer {TU_TOKEN}" \
  -H "Content-Type: application/json" \
  -d '{"name":"Nombre Actualizado"}'
```

---

### Test 11: Eliminar Usuario (Soft Delete)

**Endpoint:** `DELETE http://localhost:8000/api/users/1`

**Headers:**
```
Authorization: Bearer {token}
```

**Resultado Esperado:**
- Status: 200
- Usuario eliminado exitosamente

**Comando cURL:**
```bash
curl -X DELETE http://localhost:8000/api/users/1 \
  -H "Authorization: Bearer {TU_TOKEN}"
```

---

### Test 12: Listar Usuarios Eliminados

**Endpoint:** `GET http://localhost:8000/api/users/trashed/list`

**Headers:**
```
Authorization: Bearer {token}
```

**Resultado Esperado:**
- Status: 200
- Lista de usuarios eliminados con soft delete

---

### Test 13: Restaurar Usuario Eliminado

**Endpoint:** `POST http://localhost:8000/api/users/1/restore`

**Headers:**
```
Authorization: Bearer {token}
```

**Resultado Esperado:**
- Status: 200
- Usuario restaurado exitosamente

---

### Test 14: Obtener Estadísticas Generales

**Endpoint:** `GET http://localhost:8000/api/statistics`

**Headers:**
```
Authorization: Bearer {token}
```

**Resultado Esperado:**
- Status: 200
- Estadísticas completas:
  - Total de usuarios
  - Usuarios activos/inactivos
  - Usuarios por rol
  - Registros por día/semana/mes

**Comando cURL:**
```bash
curl -X GET http://localhost:8000/api/statistics \
  -H "Authorization: Bearer {TU_TOKEN}"
```

---

### Test 15: Estadísticas Diarias

**Endpoint:** `GET http://localhost:8000/api/statistics/daily`

**Resultado Esperado:**
- Status: 200
- Usuarios registrados por día (últimos 30 días)

---

### Test 16: Estadísticas Semanales

**Endpoint:** `GET http://localhost:8000/api/statistics/weekly`

**Resultado Esperado:**
- Status: 200
- Usuarios registrados por semana (últimas 12 semanas)

---

### Test 17: Estadísticas Mensuales

**Endpoint:** `GET http://localhost:8000/api/statistics/monthly`

**Resultado Esperado:**
- Status: 200
- Usuarios registrados por mes (últimos 12 meses)

---

### Test 18: Refresh Token

**Endpoint:** `POST http://localhost:8000/api/auth/refresh`

**Headers:**
```
Authorization: Bearer {token}
```

**Resultado Esperado:**
- Status: 200
- Nuevo token generado
- Token anterior revocado

**Comando cURL:**
```bash
curl -X POST http://localhost:8000/api/auth/refresh \
  -H "Authorization: Bearer {TU_TOKEN}"
```

---

### Test 19: Logout

**Endpoint:** `POST http://localhost:8000/api/auth/logout`

**Headers:**
```
Authorization: Bearer {token}
```

**Resultado Esperado:**
- Status: 200
- Token revocado
- Intentar usar el token debe fallar

**Comando cURL:**
```bash
curl -X POST http://localhost:8000/api/auth/logout \
  -H "Authorization: Bearer {TU_TOKEN}"
```

---

### Test 20: Acceso sin Token (Debe Fallar)

**Endpoint:** `GET http://localhost:8000/api/users`

**Sin Headers de Autorización**

**Resultado Esperado:**
- Status: 401 (Unauthorized)
- Mensaje: "Unauthenticated"

---

### Test 21: Token Expirado (Después de 5 minutos)

**Endpoint:** `GET http://localhost:8000/api/users`

**Headers:**
```
Authorization: Bearer {token_expirado}
```

**Resultado Esperado:**
- Status: 401
- Mensaje de token expirado

**Nota:** Esperar 5 minutos después de obtener el token o usar un token antiguo

---

### Test 22: Logout de Todos los Dispositivos

**Endpoint:** `POST http://localhost:8000/api/auth/logout-all`

**Headers:**
```
Authorization: Bearer {token}
```

**Resultado Esperado:**
- Status: 200
- Todos los tokens del usuario revocados

---

## 📊 Checklist de Testing

- [ ] Health check funciona
- [ ] Login exitoso
- [ ] Login con credenciales incorrectas falla
- [ ] Crear usuario con datos válidos
- [ ] Validación de campos requeridos
- [ ] Validación de email único
- [ ] Listar usuarios con paginación
- [ ] Obtener usuario específico
- [ ] Actualizar usuario
- [ ] Eliminar usuario (soft delete)
- [ ] Listar usuarios eliminados
- [ ] Restaurar usuario
- [ ] Estadísticas generales
- [ ] Estadísticas por día
- [ ] Estadísticas por semana
- [ ] Estadísticas por mes
- [ ] Refresh token funciona
- [ ] Logout revoca token
- [ ] Acceso sin token falla
- [ ] Token expira después de 5 minutos
- [ ] Logout de todos los dispositivos

---

## 🔍 Verificación de Seguridad

### Test de Seguridad 1: Password Hash
Verificar que las contraseñas se guardan hasheadas en la base de datos.

```sql
SELECT id, name, email, password FROM users LIMIT 1;
```

**Resultado Esperado:** La contraseña debe estar hasheada (no en texto plano)

### Test de Seguridad 2: Soft Delete
Verificar que los usuarios eliminados tienen `deleted_at` no nulo.

```sql
SELECT id, name, email, deleted_at FROM users WHERE deleted_at IS NOT NULL;
```

---

## 📝 Notas Importantes

1. **Token Expiration:** Los tokens expiran en 5 minutos. Usar refresh token antes de que expire.
2. **Pagination:** Por defecto, los listados retornan 15 elementos por página.
3. **Soft Delete:** Los usuarios eliminados pueden ser restaurados.
4. **Validación:** Todos los endpoints validan los datos de entrada.
5. **Roles:** Los roles disponibles son: `admin` y `user`.

---

## 🛠️ Herramientas Recomendadas

- **Postman:** Para testing manual de APIs
- **Insomnia:** Alternativa a Postman
- **cURL:** Para testing desde la línea de comandos
- **Thunder Client:** Extensión de VS Code

---

## 📄 Importar Colección

Importa el archivo `postman_collection.json` en Postman o Insomnia para tener todos los endpoints listos.

**Variables a configurar:**
- `base_url`: http://localhost:8000/api
- `token`: Se actualiza automáticamente después del login
