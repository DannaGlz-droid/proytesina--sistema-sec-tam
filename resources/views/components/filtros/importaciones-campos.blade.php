@props(['inline' => false])

<x-filtros.seccion titulo="Fecha de carga" :abierto="true">
    <div class="imports-filter-fields">
        <div class="imports-filter-field">
            <label for="dateRangeImports">Periodo</label>
            <x-filtros.select id="dateRangeImports" name="dateRangeImports" placeholder="Todas las fechas">
                <option value="all">Todas las fechas</option>
                <option value="today">Hoy</option>
                <option value="week">Últimos 7 días</option>
                <option value="month">Últimos 30 días</option>
                <option value="year">Último año</option>
                <option value="custom">Rango personalizado</option>
            </x-filtros.select>
        </div>
        <div id="customRangeSelectorImports" class="imports-filter-date-grid hidden">
            <label>Desde<input type="date" id="startDateImports" name="startDateImports"></label>
            <label>Hasta<input type="date" id="endDateImports" name="endDateImports"></label>
        </div>
    </div>
</x-filtros.seccion>

<x-filtros.seccion titulo="Usuario" :abierto="$inline">
    <div class="imports-filter-field">
        <label for="usuarioImports">Cargado por</label>
        <x-filtros.select id="usuarioImports" name="usuarioImports" placeholder="Todos los usuarios">
            <option value="">Todos los usuarios</option>
        </x-filtros.select>
    </div>
</x-filtros.seccion>

<x-filtros.seccion titulo="Resultado" :abierto="$inline" data-import-filter-view="active">
    <div class="users-filter-options">
        <label class="users-filter-option imports-filter-option"><input type="checkbox" id="resultadoCorreccionImports" value="corrections" class="sr-only" data-import-context-filter data-filter-label="Resultado: por corregir"><span class="users-filter-check"><i class="fas fa-check" aria-hidden="true"></i></span><span>Por corregir</span></label>
        <label class="users-filter-option imports-filter-option"><input type="checkbox" id="resultadoDiferenciasImports" value="differences" class="sr-only" data-import-context-filter data-filter-label="Resultado: por revisar"><span class="users-filter-check"><i class="fas fa-check" aria-hidden="true"></i></span><span>Por revisar</span></label>
    </div>
</x-filtros.seccion>

<x-filtros.seccion titulo="Efecto de la reversión" :abierto="$inline" data-import-filter-view="reversed" hidden>
    <div class="users-filter-options">
        <label class="users-filter-option imports-filter-option"><input type="checkbox" id="reversionDatosRetiradosImports" value="removed" class="sr-only" data-import-context-filter data-filter-label="Reversión: datos retirados"><span class="users-filter-check"><i class="fas fa-check" aria-hidden="true"></i></span><span>Datos retirados</span></label>
        <label class="users-filter-option imports-filter-option"><input type="checkbox" id="reversionSinDatosImports" value="none" class="sr-only" data-import-context-filter data-filter-label="Reversión: sin datos aplicados"><span class="users-filter-check"><i class="fas fa-check" aria-hidden="true"></i></span><span>Sin datos aplicados</span></label>
    </div>
</x-filtros.seccion>

<x-filtros.seccion titulo="Motivo de falla" :abierto="$inline" data-import-filter-view="failed" hidden>
    <div class="imports-filter-field">
        <label for="motivoFallaImports">Motivos</label>
        <x-filtros.select id="motivoFallaImports" name="motivoFallaImports" placeholder="Todos los motivos" :searchable="false" data-import-context-select>
            <option value="all">Todos los motivos</option>
            @foreach([
                'missing_columns' => 'Columnas requeridas faltantes',
                'invalid_file' => 'Archivo dañado o incompatible',
                'unavailable_file' => 'Archivo no disponible',
                'internal_error' => 'Error de procesamiento',
                'unknown' => 'Sin motivo registrado',
            ] as $value => $label)
                <option value="{{ $value }}" data-filter-label="Motivo: {{ $label }}">{{ $label }}</option>
            @endforeach
        </x-filtros.select>
    </div>
</x-filtros.seccion>
