# Pruebas visuales de Estadísticas

La suite usa una base SQLite temporal y respuestas de gráfica controladas. No modifica ni depende de los datos reales del sistema.

## Ejecución

```bash
npx playwright install chromium
npm run test:e2e
```

Para aceptar deliberadamente un cambio visual:

```bash
npm run test:e2e:update
```

Revise las diferencias antes de actualizar las referencias. El reporte de la última ejecución se abre con:

```bash
npm run test:e2e:report
```

La cobertura incluye límites Top 5/10/15 y “Todos”, etiquetas largas, comparativa, tabla de edades, vista móvil, persistencia del contexto, exportaciones PNG/PDF y paginación de barras horizontales extensas.

Las respuestas del API y los recursos externos necesarios para las capturas se sirven localmente, por lo que la suite no depende de Internet ni de los datos reales del sistema.
