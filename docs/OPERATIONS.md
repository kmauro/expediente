# Operación, configuración y despliegue

## Configuración de entorno

Parte de `.env.example`. Variables esenciales:

| Variable | Uso |
| --- | --- |
| `APP_NAME`, `APP_ENV`, `APP_KEY`, `APP_DEBUG`, `APP_URL` | Identidad, cifrado, modo de ejecución y URLs. |
| `DB_CONNECTION`, `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD` | Conexión MySQL. |
| `SESSION_DRIVER=database` | Sesiones persistidas en MySQL. |
| `CACHE_STORE=database` | Caché persistida en MySQL. |
| `QUEUE_CONNECTION=database` | Trabajos en cola persistidos en MySQL. |
| `FILESYSTEM_DISK=local` | Almacenamiento temporal de importaciones. |
| `LOG_CHANNEL`, `LOG_LEVEL` | Destino y nivel de logs. |
| `MAIL_*` | Correo; el entorno de ejemplo sólo escribe al log. |

En producción usa `APP_ENV=production`, `APP_DEBUG=false`, una `APP_KEY` estable y secretos gestionados fuera del repositorio. Ajusta `APP_URL` al dominio real.

## Instalación manual

Si no se usa `composer run setup`:

```bash
composer install
copy .env.example .env
php artisan key:generate
php artisan migrate --force
npm install --ignore-scripts
npm run build
```

En Linux o macOS sustituye `copy` por `cp`. Carga seeders sólo en una base nueva y después de revisar las credenciales sembradas.

## Desarrollo diario

```bash
composer run dev
```

Procesos iniciados:

- `php artisan serve` — aplicación web;
- `php artisan queue:listen --tries=1` — worker de colas;
- `npm run dev` — Vite y HMR.

Si Laragon sirve el proyecto directamente, puede ejecutarse sólo `npm run dev`; la configuración actual de HMR usa `expediente.local:5173`.

## Validaciones antes de entregar

```bash
vendor/bin/pint --format agent
composer test
npm run build
php artisan route:list --except-vendor
```

La suite usa SQLite en memoria según `phpunit.xml`, por lo que no valida diferencias específicas de MySQL. Mantén al menos una comprobación de migraciones sobre MySQL antes de desplegar cambios de esquema.

## Despliegue

El repositorio no incluye Docker, configuración de servidor web ni receta específica del proveedor. Existe un workflow de pruebas PHP, pero no compila el frontend ni despliega la aplicación. Un despliegue debe definir externamente:

1. PHP, extensiones, servidor web y permisos de `storage` y `bootstrap/cache`.
2. Base MySQL y secretos de entorno.
3. `composer install --no-dev --optimize-autoloader`.
4. `npm ci` y `npm run build`, o artefactos frontend previamente construidos.
5. `php artisan migrate --force` con copia de seguridad previa.
6. Cachés de producción después de configurar el entorno.
7. Worker de cola supervisado y reiniciado tras cada despliegue.
8. Cron del scheduler si se agregan tareas programadas.
9. Logs, alertas, backups y prueba de restauración.

La aplicación expone `/up` como endpoint de salud. Su respuesta no comprueba necesariamente todos los servicios dependientes; amplía el monitoreo según el entorno.

## Operación habitual

### Logs

```bash
php artisan pail
```

También pueden revisarse los archivos bajo `storage/logs`. No copies a tickets logs con información personal o secretos sin depurarlos.

### Colas

Aunque el código actual no define jobs de negocio, el driver es `database` y desarrollo inicia un worker. Si se introducen jobs, configura un supervisor en producción y revisa `failed_jobs`:

```bash
php artisan queue:failed
php artisan queue:retry all
```

No reintentes masivamente sin comprobar que el trabajo es idempotente.

### Modo mantenimiento y migraciones

```bash
php artisan down
php artisan migrate --force
php artisan up
```

Decide el modo mantenimiento según la compatibilidad de la migración. Conserva un dump antes de cambios destructivos y evita editar migraciones que ya llegaron a entornos compartidos.

### Limpieza de cachés durante diagnóstico

```bash
php artisan optimize:clear
```

Después del diagnóstico, reconstruye las cachés apropiadas para producción.

## Diagnóstico rápido

| Síntoma | Comprobación inicial |
| --- | --- |
| CSS/JS viejo o error de manifest | Ejecutar `npm run build` o mantener `npm run dev` activo. |
| Error 419 | Revisar dominio, cookies, duración y tabla de sesiones; la app muestra un mensaje específico. |
| Importación falla | Validar extensión, tamaño, encabezado, columnas, fecha y prefijo `EX-`; revisar logs. |
| Reporte vacío | Confirmar que la fecha coincide con `records.updated_at`, que representa el último movimiento. |
| Catálogos duplicados | Revisar normalización y no repetir seeders sobre una base poblada. |
| HMR no conecta | Resolver `expediente.local` o adaptar `vite.config.js` al host local. |

## Datos personales y seguridad operativa

Usuarios incluyen nombre, apellido, email, username y teléfono. Restringe dumps, logs y backups por mínimo acceso, cifra copias y define una política de retención. El repositorio no contiene una política formal de retención o borrado; debe acordarse con el responsable funcional y legal.
