# Implementación de DataTables Server-Side

## Resumen
Se implementó DataTables en modo server-side para las tablas de **usuarios** y **defunciones** para garantizar:
- Escalabilidad con grandes datasets (miles de registros)
- Búsqueda y ordenamiento rápidos procesados en el servidor
- Interfaz consistente entre ambas tablas
- Paginación, búsqueda y ordenamiento integrados con filtros existentes

## Cambios Realizados

### 1. Assets Centralizados
**Archivo:** `resources/views/layouts/principal.blade.php`
- Agregados enlaces CDN para jQuery, DataTables CSS y JS
- Ubicación: dentro del `<head>` antes del cierre

### 2. Controladores - Endpoints JSON

#### UserController
**Archivo:** `app/Http/Controllers/UserController.php`
- **Nuevo método:** `dataTable(Request $request)`
- **Ruta:** `POST /usuario/gestion-de-usuarios/datatable` → `user.datatable`
- Acepta parámetros DataTables: `draw`, `start`, `length`, `search`, `order`
- Aplica filtros existentes del formulario (role, position, jurisdiction, etc.)
- Retorna JSON con formato DataTables: `{ draw, recordsTotal, recordsFiltered, data }`

#### DeathController
**Archivo:** `app/Http/Controllers/DeathController.php`
- **Nuevo método:** `dataTable(Request $request)`
- **Ruta:** `POST /estadisticas/datos/datatable` → `statistic.datatable`
- Acepta parámetros DataTables y filtros de fecha, jurisdicción, municipio, edad, causa, etc.
- Retorna JSON con formato DataTables

### 3. Vistas Actualizadas

#### Tabla de Usuarios
**Archivo:** `resources/views/usuarios/gestion-de-usuarios.blade.php`
- Eliminado componente `<x-table-controls>` y renderizado PHP de filas
- Tabla ahora se llena vía AJAX desde `user.datatable`
- DataTables configurado con:
  - `serverSide: true`
  - `pageLength: 25` (default)
  - `lengthMenu: [10, 25, 50, 100]`
  - Búsqueda global integrada
  - Ordenamiento por columnas (excepto acciones)
  - Estilos Tailwind personalizados

#### Tabla de Defunciones
**Archivo:** `resources/views/estadisticas/datos.blade.php`
- Eliminado componente `<x-table-controls>` y renderizado PHP de filas
- Tabla ahora se llena vía AJAX desde `statistic.datatable`
- DataTables configurado con:
  - `serverSide: true`
  - `pageLength: 25` (default)
  - `lengthMenu: [25, 50, 100, 250]`
  - Búsqueda global integrada
  - Ordenamiento por fecha de defunción (descendente) por defecto

### 4. Partials para Acciones
**Archivos creados:**
- `resources/views/usuarios/partials/table-actions.blade.php`
- `resources/views/estadisticas/partials/table-actions.blade.php`

Contienen los botones de acción (editar, eliminar, etc.) reutilizados por el endpoint JSON.

### 5. Rutas
**Archivo:** `routes/web.php`
- `POST usuario/gestion-de-usuarios/datatable` → `UserController@dataTable`
- `POST estadisticas/datos/datatable` → `DeathController@dataTable`
- Ambas rutas protegidas por middleware `auth` y roles correspondientes

### 6. Seguridad
- Token CSRF configurado vía `$.ajaxSetup` en los scripts de inicialización
- Meta tag `csrf-token` ya existente en el layout
- Rutas protegidas por middleware de autenticación y roles

## Características Implementadas

### Usuarios
- ✅ Paginación server-side (25 por página por defecto)
- ✅ Búsqueda global (ID, username, nombre, email, teléfono)
- ✅ Ordenamiento por columnas (ID, username, nombre, fecha alta, última sesión)
- ✅ Integración con filtros existentes (rol, cargo, jurisdicción, activo/inactivo, fecha)
- ✅ Estilos Tailwind con colores del sitio (#611132 maroon)
- ✅ Badges de rol con colores (admin: rojo, usuario: verde, operador: azul, invitado: gris)
- ✅ Estado activo/inactivo con indicador visual

### Defunciones
- ✅ Paginación server-side (25 por página por defecto)
- ✅ Búsqueda global (folio, nombre, apellidos, edad)
- ✅ Ordenamiento por columnas (fecha defunción por defecto descendente)
- ✅ Integración con filtros complejos (fecha, jurisdicción, municipio, sexo, edad, causa)
- ✅ Estilos Tailwind con colores del sitio
- ✅ Importación de archivos Excel/CSV funciona y recarga la tabla vía AJAX

## Ventajas de la Implementación

1. **Escalabilidad:** Maneja miles de registros sin problemas de memoria/rendimiento
2. **Consistencia:** Misma UX entre usuarios y defunciones
3. **URLs Compartibles:** Los filtros permanecen en la URL y se aplican al cargar
4. **Búsqueda Instantánea:** DataTables busca en el servidor sin recargar la página
5. **Ordenamiento Rápido:** Click en columnas ordena server-side
6. **Integración Perfecta:** Los filtros del sidebar funcionan con DataTables
7. **Estilo del Sitio:** Botones, paginación y colores coinciden con el diseño existente

## Cómo Probar

1. Limpiar cache de vistas:
   ```bash
   php artisan view:clear
   ```

2. Iniciar servidor de desarrollo:
   ```bash
   php artisan serve
   ```

3. Navegar a:
   - Usuarios: `http://localhost:8000/usuario/gestion-de-usuarios`
   - Defunciones: `http://localhost:8000/estadisticas/datos`

4. Probar:
   - Búsqueda global (barra superior derecha)
   - Cambiar cantidad de filas por página (selector superior izquierda)
   - Ordenar por columnas (click en encabezados)
   - Aplicar filtros del sidebar y verificar que se mantengan
   - Paginación (botones inferior)

## Cómo Revertir a Paginación Laravel

Si prefieres volver a la paginación Laravel original:

### 1. Revertir Vistas

#### Usuarios
```bash
git checkout HEAD -- resources/views/usuarios/gestion-de-usuarios.blade.php
```

#### Defunciones
```bash
git checkout HEAD -- resources/views/estadisticas/datos.blade.php
```

### 2. Eliminar Rutas DataTables
En `routes/web.php`, eliminar:
```php
Route::post('usuario/gestion-de-usuarios/datatable', [UserController::class, 'dataTable'])->name('user.datatable');
Route::post('estadisticas/datos/datatable', [App\Http\Controllers\DeathController::class, 'dataTable'])->name('statistic.datatable');
```

### 3. (Opcional) Remover Assets del Layout
En `resources/views/layouts/principal.blade.php`, eliminar:
```html
<!-- DataTables Assets -->
<link rel="stylesheet" href="https://cdn.datatables.net/2.3.5/css/dataTables.dataTables.min.css">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/2.3.5/js/dataTables.min.js"></script>
```

### 4. (Opcional) Eliminar Métodos de Controladores
Puedes dejar los métodos `dataTable` en `UserController` y `DeathController` sin daño, o eliminarlos manualmente.

### 5. Limpiar Cache
```bash
php artisan view:clear
php artisan route:clear
```

## Notas de Despliegue

- **Producción:** Los assets de DataTables están en CDN (no requiere instalación local)
- **Cache:** Limpiar cache de vistas y rutas después de desplegar
- **Permisos:** Las rutas ya están protegidas por middleware existente
- **Performance:** Con miles de registros, DataTables server-side es más rápido que la paginación Laravel con filtros complejos

## Próximos Pasos Opcionales

1. **Exportar a Excel/PDF:** Agregar botones de exportación DataTables con plugins
2. **Búsqueda Avanzada por Columna:** Habilitar búsqueda individual en cada columna
3. **State Saving:** Guardar estado de tabla (página, orden, búsqueda) en localStorage
4. **Responsive:** Agregar plugin DataTables Responsive para móviles
5. **Botones Personalizados:** Agregar acciones masivas (seleccionar múltiples filas)

## Soporte y Documentación

- [DataTables Server-Side Docs](https://datatables.net/manual/server-side)
- [DataTables Laravel Package](https://github.com/yajra/laravel-datatables) (alternativa más robusta)
- [Tailwind + DataTables Styling](https://tailwindcss.com/docs/adding-custom-styles)

---

**Fecha de Implementación:** 18 de noviembre, 2025  
**Versión:** 1.0  
**Autor:** GitHub Copilot + Usuario
