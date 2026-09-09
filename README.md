# Expediente

Aplicación web interna para registrar, buscar y seguir expedientes, mantener sus movimientos entre áreas, importar datos desde Excel y generar reportes diarios en formato XLSX.

## Estado del proyecto

- Backend: PHP 8.3 o superior, Laravel 13 e Inertia Laravel 3.
- Frontend: Vue 3, Inertia Vue 3, Tailwind CSS 4 y Vite 8.
- Persistencia: MySQL. Sesiones, caché y colas también usan la base de datos.
- Excel: `maatwebsite/excel` 3.1 para importación y exportación.
- Pruebas: Pest 4.
- La aplicación no tiene una API pública; toda la interfaz se sirve mediante rutas web e Inertia.

El código está organizado por dominios dentro de `app/Domains`: `Users`, `Records` y `Files`. Antes de cambiar un flujo, consulta [Arquitectura](docs/ARCHITECTURE.md), [Datos e importaciones](docs/DATA_AND_IMPORTS.md) y [Operación](docs/OPERATIONS.md).

## Puesta en marcha

Requisitos locales:

- PHP con las extensiones requeridas por Laravel y PhpSpreadsheet.
- Composer.
- Node.js y npm compatibles con Vite 8.
- MySQL.

Desde la raíz del repositorio:

```bash
composer run setup
```

El script instala dependencias PHP y JavaScript, crea `.env` desde `.env.example`, genera `APP_KEY`, ejecuta migraciones y compila el frontend. Después configura la base de datos en `.env` y, únicamente sobre una base nueva de desarrollo, carga los catálogos iniciales:

```bash
php artisan db:seed
```

El seeder crea una cuenta administrativa de desarrollo con credenciales conocidas. No debe ejecutarse sin revisión en producción; consulta [Riesgos y trabajo pendiente](docs/HANDOVER.md).

Para trabajar localmente:

```bash
composer run dev
```

Este comando inicia el servidor Laravel, un worker de colas y Vite. La URL habitual del servidor Artisan es `http://localhost:8000`; si se usa Laragon, Vite espera el host `expediente.local` para HMR.

## Verificación

```bash
composer test
npm run build
vendor/bin/pint --format agent
```

`composer test` limpia la caché de configuración y ejecuta Pest. Pint sólo es obligatorio cuando se modifican archivos PHP.

## Documentación de continuidad

- [Arquitectura y flujos](docs/ARCHITECTURE.md)
- [Modelo de datos, importación y reportes](docs/DATA_AND_IMPORTS.md)
- [Operación, configuración y despliegue](docs/OPERATIONS.md)
- [Estado del traspaso, riesgos y próximos pasos](docs/HANDOVER.md)

## Reglas de contribución

- Mantener la organización por dominios y reutilizar servicios, repositorios y componentes existentes.
- Añadir o actualizar pruebas para cada cambio de comportamiento.
- No confirmar `.env`, archivos importados, exportaciones ni secretos.
- Usar migraciones nuevas para cambios de esquema; no reescribir migraciones ya aplicadas.
- Ejecutar la suite y la compilación antes de entregar un cambio.
