Fixtures de importación para `tests/fixtures/import/`

Archivos creados (CSV):
- `valid.csv` — filas válidas (incluye una fila con fecha serial Excel).
- `folio_invalido.csv` — fila con `gov_folio` no numérico (para `folio_invalido`).
- `folio_duplicado_archivo.csv` — dos filas con el mismo `gov_folio` en el mismo archivo.
- `folio_duplicado_db.csv` — fila con `gov_folio` que se presupone existe en la BD (preparar manualmente).
- `meses_invalidos.csv` — filas con `meses` = 12 y 13 (deben fallar).
- `municipio_fuera.csv` — municipio no existente para probar mapeo a `OTRO`.
- `sitio_causa_nuevo.csv` — filas que incluyen causa y sitio nuevos (deben crearse).
- `mixed_tests.csv` — archivo mixto con casos variados para pruebas rápidas.

Notas sobre pruebas de hojas (XLSX):
- CSV no soporta hojas. Para comprobar la lógica que infiere la `causa` desde el NOMBRE de la hoja (p. ej. hoja llamada "Ahogamiento"), crea un archivo XLSX con una hoja nombrada `Ahogamiento` y en esa hoja incluye las mismas columnas sin la columna `causa`.
- Para probar la regla "hoja por defecto" (Sheet1/Hoja1): crea un XLSX cuya hoja se llame `Sheet1` y omite la columna `causa`; el import debe rechazar todas las filas de esa hoja con el código `falta_causa`.

Cabecera recomendada para todos los archivos CSV/XLSX:
gov_folio,nombre,apellido_paterno,apellido_materno,fecha_defuncion,anos,meses,dias,municipio_defuncion,municipio_residencia,causa,sitio,sexo

Instrucciones rápidas (pruebas manuales):
1) Abrir `http://localhost:8000/estadisticas/datos` en tu entorno de desarrollo.
2) En el formulario de importación subir uno de los CSV de `tests/fixtures/import/`.
3) En la respuesta JSON revisar: `imported`, `failed`, `errors_file` y `details`.
4) Si `errors_file` contiene errores, se guardará en `storage/app/tmp_imports/`. Puedes descargarlo desde el backend o inspeccionarlo localmente.

Comprobaciones en BD:
- `deaths` para los `gov_folio` importados.
- `death_causes` y `death_locations` para entradas creadas.
- `municipalities` y `jurisdictions` para el municipio `OTRO`.
- `imports` para counters del job de importación.

Si quieres, procedo a crear también los tests automáticos (`tests/Feature/DeathImportTest.php`) que usen estos fixtures. También puedo generar pequeños XLSX (si confirmas que quieres archivos binarios, los crearé como archivos base64 válidos o te doy instrucciones para generarlos con PHP/Excel localmente).