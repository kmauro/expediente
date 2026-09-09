# Arquitectura y flujos

## Propósito funcional

El sistema centraliza expedientes administrativos. Un usuario autenticado puede consultar expedientes, crear o editar sus datos, registrar movimientos, importar expedientes desde libros Excel y generar un reporte de los expedientes cuya última fecha de movimiento coincide con una fecha elegida.

## Capas y organización

```text
routes/web.php
    -> Controllers
        -> Form Requests (validación)
        -> Services (reglas y orquestación)
            -> Repositories (consultas y persistencia)
                -> Eloquent Models / MySQL

Controllers -> Inertia::render(...) -> resources/js/Pages/*.vue
```

| Ruta | Responsabilidad |
| --- | --- |
| `app/Domains/Users` | Registro, autenticación, perfiles, contraseñas y roles. |
| `app/Domains/Records` | Expedientes, movimientos y catálogos de estado, tipo, contraparte y área. |
| `app/Domains/Files` | Importación temporal de Excel y exportación de reportes XLSX. |
| `app/Http/Middleware/HandleInertiaRequests.php` | Props compartidas: usuario, estados y mensajes flash. |
| `resources/js/Pages` | Páginas Vue asociadas a las respuestas Inertia. |
| `resources/js/Components/ui` | Componentes visuales reutilizables. |
| `routes/web.php` | Navegación web y middleware de autenticación. |

Laravel resuelve controladores, servicios y repositorios por el contenedor; no hay bindings personalizados en `AppServiceProvider`.

## Módulos

### Usuarios

`UserController` delega en `UserService` y `UserRepository`. El acceso se realiza con `username` y contraseña mediante el guard web de Laravel. Hay dos roles sembrados: `admin` (id 1) y `usuario` (id 2). El modelo `User` considera administrador a quien tenga `role_id === 1`.

La interfaz ofrece registro, login, logout, perfil propio, listado de usuarios, vista de otro usuario y cambio de contraseña. Consulta las limitaciones de autorización actuales en [HANDOVER.md](HANDOVER.md).

### Expedientes

`RecordController` coordina `RecordService` y los servicios de catálogos. `RecordService` construye expedientes y movimientos; `RecordRepository` concentra consultas, relaciones y persistencia.

Un expediente tiene:

- número único y título;
- estado, objeto/tipo y contraparte;
- fechas `created_at` y `updated_at` tratadas como fechas de negocio, no como timestamps automáticos;
- un puntero opcional a su último movimiento;
- una colección histórica de movimientos.

Cada movimiento guarda el área destino, descripción/documento asociado, fecha y usuario. Al añadirlo se actualizan `records.last_movement_id` y `records.updated_at`.

### Catálogos

Estados, tipos, contrapartes y áreas son tablas propias. Tipos, contrapartes y áreas guardan además `normalized_name`; `NormalizeService` permite buscar o crear valores equivalentes sin depender de mayúsculas ni acentos. Los estados incluyen un color usado por la interfaz.

### Archivos y reportes

La importación carga el archivo temporalmente en el disco `local`, lee todas sus hojas, omite la primera fila de cada hoja, normaliza valores, convierte filas válidas y persiste todo dentro de una transacción. El archivo se elimina en un bloque `finally`.

La reimportación usa el número de expediente como identidad. Los movimientos históricos se crean de forma idempotente según sus datos, y el movimiento actual puede actualizarse cuando coincide en área y descripción. Las reglas completas están en [DATA_AND_IMPORTS.md](DATA_AND_IMPORTS.md).

El reporte consulta expedientes por `records.updated_at`, carga relaciones para evitar N+1 y genera `reporte_expedientes.xlsx`. Los valores que empiezan por `=`, `+`, `-` o `@` se neutralizan para impedir inyección de fórmulas.

## Rutas funcionales

Todas las rutas salvo login y registro requieren sesión.

| Área | Métodos y rutas principales |
| --- | --- |
| Sesión | `GET/POST /login`, `GET/POST /signup`, `POST /logout` |
| Inicio | `GET /`, `GET /homepage` |
| Usuarios | `GET /profile`, `POST /profile`, `POST /profile/updatePassword`, `GET /users`, `GET /users/{id}` |
| Expedientes | `GET /records`, `GET /records/{id}`, `GET/POST /record/add`, edición, eliminación y movimientos por POST |
| Catálogos | POST a `/config/addArea`, `addType`, `addCounterparty` y `addState` |
| Importación | `GET/POST /file/upload` |
| Reportes | `GET /report/generate`, `GET /report/download?date=AAAA-MM-DD` |
| Salud | `GET /up` |

Comprueba el contrato exacto con `php artisan route:list --except-vendor`.

## Frontend

`resources/js/app.js` inicia Vue e Inertia y resuelve páginas de `resources/js/Pages`. `AppLayout.vue` compone cabecera, navegación lateral, contenido, pie y notificaciones; `AuthLayout.vue` se usa en login y registro.

Las páginas envían formularios con `useForm`. Los mensajes `success` y `error` de sesión se comparten desde `HandleInertiaRequests` y se muestran mediante `NotificationToast`.

Tailwind CSS 4 se integra como plugin de Vite. Los componentes UI siguen una estructura similar a shadcn/reka-ui y se configuran en `components.json`. El alias `@` apunta a `resources/js`.

## Decisiones que deben preservarse o cambiarse deliberadamente

- El número de expediente es la clave funcional de idempotencia en importaciones.
- `records.updated_at` representa la fecha del último movimiento, no la hora de actualización técnica.
- `last_movement_id` acelera listados y reportes, pero debe mantenerse consistente con `record_movements`.
- El estado importado se fija actualmente en id 1; presupone que el seeder conserva ese identificador.
- Las áreas derivadas de una importación se crean desde la descripción del último estado conocido, truncada a 255 caracteres.
- La aplicación usa rutas web con sesión e Inertia; introducir una API sería una decisión arquitectónica nueva.
