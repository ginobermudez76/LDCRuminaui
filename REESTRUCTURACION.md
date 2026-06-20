Backend: API REST en Laravel con autenticación JWT y lógica de triggers/procedimientos migrada a PHP.
Frontend: SPA en Angular 20 con PrimeNG (utilizando el nuevo sistema de presets Lara y componentes adaptados) y enrutamiento con guards.
Cambios Realizados
Legacy (Respaldo)
Se movieron todos los archivos originales del proyecto MPA a la carpeta 
legacy/
 para mantenerlos como referencia histórica y de datos.
Backend (Laravel API REST)
Inicialización: Se creó un nuevo proyecto de Laravel en 
backend/
.
Dependencias: Se instaló y configuró tymon/jwt-auth para la autenticación sin estado (stateless) mediante JWT tokens.
Migraciones:
Se adaptó la migración inicial de usuarios para crear las tablas roles y usuarios (reemplazando users) respetando las columnas originales.
Se creó la migración 
2026_06_20_000001_create_solicitud_tables.php
 para las tablas solicitud, solicitud_estado, solicitud_tipo y external.
Se creó la migración 
2026_06_20_000002_create_sports_and_events_tables.php
 para deportes, eventos, logros, noticias, galeria_imagenes y carta_condolencias.
Modelos y Lógica de Negocio (Migración de BD a PHP):
Se implementó Usuario.php heredando de Authenticatable e implementando JWTSubject.
Se implementó Evento.php con un evento de ciclo de vida booted() -> saving() que reemplaza los triggers SQL (tr_antesdeinsertar y tr_before_update_evento) calculando dinámicamente las fechas de eliminación (fecha_fin + 3 días) y el estado del evento (Finalizado, En curso, Proximamente) en base a la fecha actual.
Se crearon el resto de modelos (Solicitud, Rol, Deporte, etc.) con sus correspondientes relaciones Eloquent.
Controladores:
AuthController.php: Registro, Login (traduce la credencial contrasena al mecanismo interno de Laravel), Perfil y Logout de JWT.
SolicitudController.php: CRUD de solicitudes. La creación (store()) reemplaza el procedimiento almacenado actualizar_departamento_encargado_proc realizando la asignación automática al agente del departamento que tenga la menor carga de trabajo (menor conteo de solicitudes pendientes).
DeporteController.php, EventoController.php, LogroController.php, NoticiaController.php.
Rutas: Se habilitó el mapeo en bootstrap/app.php y se definieron las rutas REST en 
routes/api.php
.
Frontend (Angular SPA)
Inicialización: Se creó el proyecto de Angular en 
frontend/
 con pnpm.
Dependencias de UI: Se instalaron primeng (v20) y primeicons para el diseño visual, y @primeuix/themes para el soporte de presets (usando el preset Lara).
Core Services & Guards:
AuthService: Maneja señales reactivas (Signals) para almacenar el usuario activo y controlar la autenticación.
JwtInterceptor: Añade el token Authorization: Bearer <token> a cada petición HTTP saliente.
AuthGuard: Protege las vistas del dashboard redirigiendo al login a usuarios no autenticados.
SolicitudService: Conexiones cliente HTTP para el CRUD de solicitudes en Laravel.
Estructura de Componentes:
LoginComponent: Tarjeta de ingreso premium con validación reactiva de campos y alertas de error en diseño de vidrio templado.
DashboardComponent: Shell de navegación superior y lateral responsivo que detecta los privilegios del usuario (ej. mostrar menú extendido a administradores).
SolicitudesListComponent: Tabla avanzada con p-table de PrimeNG con paginación, tags de estados (Aprobado/Pendiente/Rechazado) y modal interactivo para visualizar el detalle completo de una solicitud.
SolicitudFormComponent: Formulario reactivo para la creación de solicitudes usando <p-select> (reemplazo de <p-dropdown> en PrimeNG 20) y controles numéricos de moneda.
Rutas y Configuración: Rutas declaradas en app.routes.ts y provistas en app.config.ts junto con provideHttpClient() y providePrimeNG(). Se incrementó el límite de presupuesto (budgets) en angular.json para permitir bundles de PrimeNG en producción.
Verificación de Compilación
Hemos ejecutado la tarea de compilación en el frontend (pnpm build) y se ha completado exitosamente:

bash

Initial chunk files   | Names         |  Raw size | Estimated transfer size
main-Z4YMCH4J.js      | main          | 941.85 kB |               167.50 kB
chunk-ZUL7N3WV.js     | -             | 167.22 kB |                48.66 kB
polyfills-7R4CRVNH.js | polyfills     |  34.59 kB |                11.33 kB
styles-GCYJPWJF.css   | styles        |  13.39 kB |                 2.50 kB
                      | Initial total |   1.16 MB |               230.00 kB
Application bundle generation complete. [9.791 seconds]
Output location: C:\\xampp\\htdocs\\Ayudantias-1\\frontend\\dist\\frontend
No existen errores de tipos de TypeScript ni de resolución de dependencias de PrimeNG.

Como arrancar el entorno de desarrollo local

powershell

Start-Service MySQL80
Una vez que el servidor MySQL esté corriendo:

1. Iniciar el Backend (Laravel)
En una terminal en la carpeta /backend:

bash

# Ejecutar las migraciones para crear la estructura de tablas limpia
php artisan migrate
# (Opcional) Si quieres importar la data existente, puedes hacerlo importando el archivo sql:
# mysql -u root -p liga < ../legacy/estructura\ de\ la\ base\ de\ datos/Dump20240312.sql
# Iniciar el servidor local de desarrollo de Laravel
php artisan serve
El backend quedará disponible en http://127.0.0.1:8000.

2. Iniciar el Frontend (Angular)
En otra terminal en la carpeta /frontend:

bash

# Iniciar el servidor local de Angular
pnpm start --open
El frontend se abrirá automáticamente en tu navegador en http://localhost:4200.