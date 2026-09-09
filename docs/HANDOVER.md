# Estado del traspaso

Fecha de auditoría: 7 de septiembre de 2026.

## Estado verificable

- Laravel Boost informa PHP 8.5 en el entorno auditado y Laravel Framework 13.13.0; `composer.json` admite PHP `^8.3` y Laravel `^13.8`.
- Stack instalado: Inertia Laravel 3.1.0, Inertia Vue 3.3.0, Vue 3.5.35, Tailwind CSS 4.3.0, Pest 4.7.2 y Maatwebsite Excel 3.1.69.
- `php artisan test --compact`: 31 pruebas aprobadas y 114 aserciones.
- `npm run build`: compilación de producción completada.
- La conexión configurada es MySQL; en el entorno auditado no había tablas disponibles, por lo que no se validaron datos reales ni una migración completa sobre esa instancia.
- El árbol de trabajo ya contenía cambios sin confirmar en importación, reportes, expedientes, rutas, bootstrap, frontend y pruebas antes de crear esta documentación. Deben revisarse y agruparse conscientemente antes de cualquier commit o despliegue.

## Riesgos prioritarios

### Críticos

1. **Registro público con selección de rol.** `POST /signup` acepta `role_id` y la ruta es pública. Un cliente puede intentar registrarse como administrador. El rol debe asignarse en servidor o el registro debe restringirse.
2. **Actualización de perfiles sin política.** `UpdateProfileRequest::authorize()` devuelve `true`, `UserController::updateProfile()` usa un `id` recibido y las rutas sólo exigen autenticación. Debe verificarse que sólo el propietario o un administrador pueda editar y cambiar roles.
3. **Operaciones administrativas sin autorización por rol.** El listado de usuarios, perfiles ajenos, catálogos y CRUD de expedientes están detrás de `auth`, pero no de policies, gates o middleware administrativo.
4. **Credenciales conocidas en el seeder.** `DatabaseSeeder` contiene usuario y contraseña administrativos. Sustituir por creación segura condicionada al entorno y rotar cualquier cuenta creada con esas credenciales.

### Altos

1. **Login y registro sin rate limiting.** Añadir límites y pruebas para intentos repetidos.
2. **Suposición de `state_id = 1` en importación.** Vincular el estado por una clave estable o configuración, no por el orden del seeder.
3. **Migración reversible defectuosa.** El `down()` de `2026_05_21_003825_create_records_table.php` elimina tablas en un orden incompatible con claves foráneas y usa `suppliers` en vez de `counterparties`.
4. **Seeders no idempotentes.** Repetir `db:seed` puede chocar con valores únicos o duplicar estados.
5. **Carreras de concurrencia.** Los catálogos y movimientos usan consulta seguida de creación sin restricciones únicas suficientes; importaciones simultáneas pueden duplicar datos.
6. **Validación de fechas desigual.** Los reportes exigen `Y-m-d`, pero algunos movimientos aceptan una cadena genérica aunque la columna sea `date`.

### Medios

1. `records.created_at` y `records.updated_at` son fechas de negocio con nombres normalmente usados para timestamps técnicos.
2. El listado de expedientes usa `get()` sin paginación; crecerá el consumo de memoria y transferencia.
3. La tabla `files` y el modelo `Files\\Models\\File` no aparecen en el flujo activo; parece código residual.
4. `CHANGELOG.md` conserva notas del esqueleto de Laravel y no registra cambios del producto.
5. No hay receta de despliegue, automatización de backups ni monitoreo en el repositorio.
6. La suite usa SQLite en memoria; cambios de esquema requieren una comprobación adicional en MySQL.
7. El helper frontend `isAdmin.js` compara su argumento con `1`; sólo es correcto si recibe un `role_id`, no un id de usuario.
8. `/` y `/homepage` comparten el nombre de ruta `home`, lo que vuelve ambigua la generación por nombre.
9. La cobertura está concentrada en importación y reportes; faltan pruebas de autorización, usuarios, CRUD manual, catálogos y migraciones.

## Próximos pasos sugeridos

1. Asegurar registro, perfiles, usuarios, catálogos y expedientes con policies/gates y pruebas negativas.
2. Eliminar o rotar la credencial sembrada y hacer seeders idempotentes.
3. Levantar MySQL limpio, ejecutar todas las migraciones y probar una estrategia de rollback o forward-fix.
4. Revisar y confirmar por separado los cambios sin commit que ya existían al momento del traspaso.
5. Acordar el significado de estados, áreas y columnas Excel, especialmente la derivación automática del área.
6. Configurar CI con pruebas, Pint y build; documentar luego el despliegue y rollback reales.
7. Definir backups, retención, recuperación, monitoreo y tratamiento de datos personales.
8. Reemplazar el changelog del framework por un historial del producto desde la próxima entrega.

## Preguntas que requieren conocimiento del negocio

- ¿Quién puede registrar usuarios y asignar el rol administrador?
- ¿Qué acciones puede realizar `usuario` frente a `admin`?
- ¿El número `EX-...` es globalmente único para siempre?
- ¿La fecha de reporte representa último movimiento, actualización manual u otro hito?
- ¿La columna E del Excel debe reemplazar el movimiento actual o agregar siempre uno nuevo?
- ¿El texto del estado actual debe convertirse realmente en un área?
- ¿Qué archivos fuente deben conservarse y durante cuánto tiempo?
- ¿Dónde se despliega, quién custodia secretos y cuál es el objetivo de recuperación?

## Checklist para asumir mantenimiento

- [ ] Obtener acceso al repositorio, despliegue, base de datos, DNS, logs y backups.
- [ ] Recibir los `.env` mediante un canal seguro; nunca por Git.
- [ ] Restaurar una copia anonimizada en un entorno aislado.
- [ ] Ejecutar migraciones, pruebas y build desde cero.
- [ ] Confirmar login, alta, búsqueda, edición, movimiento, importación y reporte.
- [ ] Revisar los cambios locales no confirmados y su intención funcional.
- [ ] Resolver los cuatro riesgos críticos antes de exponer el sistema a usuarios no confiables.
- [ ] Registrar responsables técnicos y funcionales, ventanas de mantenimiento y procedimiento de incidentes.
