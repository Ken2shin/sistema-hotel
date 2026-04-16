# Hotel Management System

Sistema profesional de gestión hotelera construido con Laravel, Livewire, Alpine.js, Three.js y PostgreSQL.

## Características

- Backend + Frontend (Monolito profesional)
- API REST segura y escalable
- Visualización 3D de habitaciones con Three.js
- Sistema dinámico de roles y permisos (RBAC)
- Dashboard administrativo con Livewire
- Gestión de imágenes optimizadas
- Reportes avanzados
- Seguridad nivel empresa (Sanctum, Rate Limiting, Headers de seguridad)
- Auditoría completa de acciones
- Sistema de caché con Redis

## Requisitos

- PHP 8.2+
- PostgreSQL 12+
- Redis
- Composer

## Instalación

1. Clonar el repositorio
```bash
git clone <repository>
cd hotel-management-system
```

2. Instalar dependencias
```bash
composer install
```

3. Configurar variables de entorno
```bash
cp .env.example .env
```

4. Generar clave de aplicación
```bash
php artisan key:generate
```

5. Ejecutar migraciones
```bash
php artisan migrate
```

6. Ejecutar seeders
```bash
php artisan db:seed --class=RolePermissionSeeder
```

7. Iniciar servidor
```bash
php artisan serve
```

## Endpoints API

### Autenticación
- `POST /api/auth/register` - Registrar usuario
- `POST /api/auth/login` - Iniciar sesión
- `POST /api/auth/logout` - Cerrar sesión
- `GET /api/auth/me` - Obtener datos del usuario

### Clientes
- `POST /api/clientes` - Crear cliente
- `GET /api/clientes/{id}` - Obtener cliente
- `PUT /api/clientes/{id}` - Actualizar cliente

### Habitaciones
- `GET /api/habitaciones` - Listar habitaciones
- `GET /api/habitaciones/{id}` - Obtener detalles de habitación
- `GET /api/habitaciones/disponibles?inicio=YYYY-MM-DD&fin=YYYY-MM-DD` - Búsqueda de disponibilidad
- `GET /api/habitaciones/{id}/resenas` - Obtener reseñas
- `GET /api/habitaciones/{id}/rating` - Obtener calificación
- `GET /api/habitaciones/recomendadas?presupuesto=&inicio=&fin=&servicios=` - Habitaciones recomendadas

### Reservas
- `POST /api/reservas` - Crear reserva
- `GET /api/reservas/cliente/{id}` - Obtener reservas del cliente
- `PUT /api/reservas/{id}/estado` - Actualizar estado de reserva

### Pagos
- `POST /api/pagos` - Registrar pago

### Reseñas
- `POST /api/resenas` - Crear reseña

### Visualización 3D
- `GET /api/hotel/floor-plan?fecha=YYYY-MM-DD` - Obtener plano del hotel
- `GET /api/hotel/room/{id}/state?fecha=YYYY-MM-DD` - Obtener estado de habitación

### Imágenes
- `POST /api/habitaciones/{id}/imagenes` - Subir imagen
- `GET /api/habitaciones/{id}/imagenes` - Obtener imágenes
- `DELETE /api/imagenes/{id}` - Eliminar imagen
- `PUT /api/imagenes/{id}/principal` - Establecer imagen principal

### Reportes (requiere autenticación)
- `GET /api/reportes/ingresos?date_from=&date_to=` - Reporte de ingresos
- `GET /api/reportes/ocupacion?date=YYYY-MM-DD` - Reporte de ocupación
- `GET /api/reportes/clientes` - Reporte de clientes
- `GET /api/reportes/habitaciones` - Reporte de habitaciones
- `GET /api/reportes/metodos-pago?date_from=&date_to=` - Reporte de métodos de pago

## Roles y Permisos

### Roles predefinidos
- **Administrador**: Acceso total
- **Recepcionista**: Gestión de reservas y clientes
- **Supervisor**: Acceso a reportes y visualización

## Estructura de Directorio

```
app/
├── Models/              # Modelos Eloquent
├── Http/
│   ├── Controllers/     # Controladores API
│   ├── Requests/        # Form Requests para validación
│   └── Middleware/      # Middlewares personalizados
├── Livewire/           # Componentes Livewire
└── Services/           # Clases de servicio

database/
├── migrations/         # Migraciones de BD
└── seeders/           # Seeders

resources/
└── views/             # Vistas Blade y Livewire

routes/
├── api.php           # Rutas API
└── web.php           # Rutas web
```

## Seguridad

- Autenticación con Laravel Sanctum
- Tokens JWT seguros
- Rate limiting por usuario/IP
- Headers de seguridad (CSP, X-Frame-Options, etc.)
- Validación estricta con Form Requests
- Sanitización de datos
- Protección CSRF
- Auditoría de acciones

## Variables de Entorno

```env
APP_NAME="Hotel Management System"
APP_ENV=production
APP_DEBUG=false
APP_KEY=
APP_URL=http://localhost

DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=hotel_management
DB_USERNAME=postgres
DB_PASSWORD=

CACHE_DRIVER=redis
REDIS_HOST=127.0.0.1
REDIS_PORT=6379

SANCTUM_STATEFUL_DOMAINS=localhost,127.0.0.1
CORS_ALLOWED_ORIGINS=http://localhost:3000,http://localhost:8000
```

## Rendimiento

- Tiempo de carga: 2-3 segundos
- Cache con Redis
- Optimización de consultas
- Lazy loading
- Compresión de imágenes

## Documentación API

La API está completamente documentada en los controladores. Cada endpoint retorna respuestas JSON estructuradas:

```json
{
  "success": true,
  "data": {},
  "message": "Operación exitosa"
}
```

## Soporte

Para soporte técnico, contactar al equipo de desarrollo.
