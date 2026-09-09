# Datos, importación y reportes

## Modelo relacional

```text
roles 1 --- N users 1 --- N record_movements N --- 1 areas
                                      |
                                      N
                                      |
                                      1
                                   records N --- 1 states
                                      |  \
                                      |   +------ 1 types
                                      +---------- 1 counterparties

records.last_movement_id ----> record_movements.id (nullable)
```

### Tablas de negocio

| Tabla | Campos relevantes | Observaciones |
| --- | --- | --- |
| `roles` | `role_name` | Nombre único. Seed: `admin`, `usuario`. |
| `users` | nombre, apellido, email, username, teléfono, password, `role_id` | Email y username únicos. |
| `states` | nombre, color | No tiene timestamps. |
| `types` | nombre, `normalized_name` | Catálogo de objeto del expediente. |
| `counterparties` | nombre, `normalized_name` | Proveedor o programa. |
| `areas` | nombre, `normalized_name` | Destino de movimientos. |
| `records` | número, título, catálogos, `last_movement_id`, fechas | Número único. Fechas de negocio tipo `date`; Eloquent tiene `$timestamps = false`. Índice en `updated_at, id`. |
| `record_movements` | expediente, área, descripción, fecha, usuario | Se elimina en cascada con el expediente. |

Las tablas de infraestructura (`sessions`, `cache`, `jobs`, `failed_jobs`, etc.) provienen de las migraciones base y soportan los drivers configurados.

## Valores iniciales

`DatabaseSeeder` carga roles `admin` y `usuario`, cinco estados y una cuenta administrativa de desarrollo.

El seeder actual no es idempotente porque inserta catálogos directamente. Debe ejecutarse sobre una base nueva, no repetidamente sobre datos existentes.

## Contrato del archivo de importación

Formatos aceptados: `.xlsx` y `.xls`, hasta 10 MB. Se procesan todas las hojas y se considera que cada hoja tiene una fila de encabezado que se descarta.

| Columna Excel | Índice | Contenido | Regla |
| --- | ---: | --- | --- |
| A | 0 | Fecha inicial | Obligatoria. Acepta fecha nativa Excel, `Y-m-d`, `d/m/Y`, `d-m-Y` y variantes sin cero inicial. |
| B | 1 | Número de expediente | Obligatorio y debe empezar por `EX-`, sin distinguir mayúsculas. |
| C | 2 | Título | Si falta: `Sin título informado`. |
| D | 3 | Historial | Opcional; puede contener varias fechas y descripciones. |
| E | 4 | Estado/movimiento actual | Opcional; si falta se usa el último elemento del historial. |
| F | 5 | Objeto/tipo | Opcional; si falta: `Sin objeto informado`. |
| G | 6 | Contraparte | Opcional; si falta: `Sin contraparte informada`. |
| H | 7 | Sin uso actual | Se lee durante la normalización pero no se mapea. |

Una fila se omite si no tiene fecha válida o si el número no empieza por `EX-`. Las filas completamente vacías también se eliminan. El resultado mostrado distingue expedientes detectados y filas omitidas.

### Interpretación del historial

`RecordHistoryParser` reconoce fechas `día/mes`, `día-mes` y las mismas formas con año 20xx. Si una fecha no trae año, intenta inferirlo desde un identificador documental o desde la fecha anterior. Los fragmentos sin fecha usan la fecha de respaldo.

Si historial y estado actual están vacíos, se crea un movimiento `Sin estado informado` con la fecha inicial. El área importada se deriva de la descripción del último estado conocido.

### Idempotencia

- El expediente se recupera o crea por `records.number`.
- Los movimientos históricos se recuperan o crean por expediente, área, descripción y fecha; el usuario no forma parte de la identidad.
- Si un expediente existente sólo tenía valores predeterminados de objeto, contraparte o título, una reimportación puede completar esos datos.
- El puntero al último movimiento sólo cambia si la fecha importada es igual o posterior a `records.updated_at`.

Estas reglas están cubiertas principalmente por `RecordImportMapperTest`, `RecordHistoryParserTest` y `RecordFileImportTest`.

## Reportes

La pantalla `/report/generate` no consulta datos hasta recibir `date=AAAA-MM-DD`. La búsqueda selecciona expedientes cuyo `records.updated_at` coincide exactamente con esa fecha.

El archivo descargado se llama `reporte_expedientes.xlsx` e incluye número, título, objeto, proveedor/programa, último movimiento e historial completo.

La descarga vuelve a consultar datos confiables en el servidor; no utiliza registros enviados por el navegador. La exportación neutraliza celdas que podrían interpretarse como fórmulas.

## Copias de seguridad y restauración

Los datos persistentes están en MySQL. Los archivos de importación son temporales y se eliminan al terminar, de modo que una copia de `storage` no reconstruye la fuente original. La operación debe conservar por separado:

- dump consistente de MySQL;
- `.env` o secretos equivalentes fuera del repositorio;
- versión o commit desplegado;
- archivos Excel originales, si la organización exige trazabilidad documental.

Prueba siempre una restauración en un entorno aislado y ejecuta migraciones pendientes después de restaurar el dump correspondiente a la versión desplegada.
