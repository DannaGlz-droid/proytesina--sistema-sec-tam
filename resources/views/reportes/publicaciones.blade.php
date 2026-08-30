@extends('layouts.principal')
@section('title', 'Reportes')
@section('content')

    @include('components.header-admin')
    @include('components.nav-reportes')

    @php
        $activeFilterCount = collect(['status', 'district_id', 'date_filter'])->filter(fn ($key) => request()->filled($key))->count();
        $hasActiveCriteria = request()->filled('q') || $activeFilterCount > 0 || request('tipo', 'todos') !== 'todos';
        $districtLabel = optional($districts->firstWhere('id', request('district_id')))->name;
    @endphp

    <main class="reports-publications-page px-4 lg:pl-10 pt-5 lg:pt-7 pb-8 lg:pb-10">
        <x-ui.page-header
            class="reports-publications-header"
            title="Centro de control"
            description="Monitoree y administre los reportes registrados en el sistema."
        />

        <div id="reportes-publicaciones-panel" class="app-table-card reports-table-card">
            <nav class="reports-type-tabs" aria-label="Tipos de reporte">
                @foreach([
                    'todos' => 'Todos',
                    'seguridad_vial' => 'Seguridad vial',
                    'observatorio' => 'Observatorio',
                    'alcoholimetria' => 'Alcoholimetría',
                    'grupos-vulnerables' => 'Grupos vulnerables',
                ] as $typeValue => $typeLabel)
                    <button type="button"
                            data-tipo="{{ $typeValue }}"
                            class="tab-filter {{ request('tipo', 'todos') === $typeValue ? 'is-active' : '' }}"
                            aria-pressed="{{ request('tipo', 'todos') === $typeValue ? 'true' : 'false' }}">
                        {{ $typeLabel }}
                    </button>
                @endforeach
            </nav>

            <form id="reports-filter-form" method="GET" action="{{ route('reportes.index') }}" class="reports-filter-form">
                <input type="hidden" name="tipo" id="filter-tipo-input" value="{{ request('tipo', 'todos') }}">

                <div class="app-table-toolbar reports-toolbar">
                    <div class="reports-filter-popover-wrap">
                        <button type="button" id="reports-filter-toggle" class="reports-filter-toggle" aria-expanded="false" aria-controls="reports-filter-panel">
                            <i class="fas fa-sliders-h" aria-hidden="true"></i>
                            <span>Filtros</span>
                            @if($activeFilterCount > 0)
                                <span class="reports-filter-count">{{ $activeFilterCount }}</span>
                            @endif
                        </button>

                        <div id="reports-filter-panel" class="reports-filter-panel hidden" role="dialog" aria-modal="false" aria-labelledby="reports-filter-title">
                            <div class="reports-filter-panel-header">
                                <h2 id="reports-filter-title">Filtros</h2>
                                <a href="{{ route('reportes.index', array_filter(['tipo' => request('tipo', 'todos'), 'q' => request('q'), 'order_by' => request('order_by'), 'per_page' => request('per_page')])) }}">Limpiar</a>
                            </div>
                            <div class="reports-filter-native-controls" aria-hidden="true">
                                <select name="status" tabindex="-1">
                                    @foreach(['' => 'Todos', 'pendiente' => 'Pendiente', 'aprobado' => 'Aprobado', 'rechazado' => 'Rechazado'] as $value => $label)
                                        <option value="{{ $value }}" {{ request('status') === $value ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                                <select name="date_filter" tabindex="-1">
                                    @foreach(['' => 'Todas las fechas', 'hoy' => 'Hoy', 'semana' => 'Esta semana', 'mes' => 'Este mes', '3meses' => 'Últimos 3 meses', 'anio' => 'Este año'] as $value => $label)
                                        <option value="{{ $value }}" {{ request('date_filter') === $value ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                                <select name="order_by" tabindex="-1">
                                    @foreach(['updated_at:desc' => 'Última actualización', 'created_at:desc' => 'Creación, recientes', 'created_at:asc' => 'Creación, antiguos', 'titulo:asc' => 'Título, A-Z', 'titulo:desc' => 'Título, Z-A', 'usuario:asc' => 'Usuario, A-Z', 'usuario:desc' => 'Usuario, Z-A'] as $value => $label)
                                        <option value="{{ $value }}" {{ request('order_by', 'updated_at:desc') === $value ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="reports-filter-panel-body">
                                @foreach([
                                    'status' => ['label' => 'Estado', 'value' => request('status', ''), 'options' => ['' => 'Todos', 'pendiente' => 'Pendiente', 'aprobado' => 'Aprobado', 'rechazado' => 'Rechazado']],
                                    'date_filter' => ['label' => 'Periodo', 'value' => request('date_filter', ''), 'options' => ['' => 'Todas las fechas', 'hoy' => 'Hoy', 'semana' => 'Esta semana', 'mes' => 'Este mes', '3meses' => 'Últimos 3 meses', 'anio' => 'Este año']],
                                ] as $target => $filter)
                                    <div class="reports-filter-section {{ $filter['value'] !== '' ? 'is-open' : '' }}" data-reports-filter-section>
                                        <button type="button" class="reports-filter-section-toggle" data-reports-filter-section-toggle>
                                            <i class="fas {{ $filter['value'] !== '' ? 'fa-chevron-down' : 'fa-chevron-right' }}" aria-hidden="true"></i>
                                            <span>{{ $filter['label'] }}</span>
                                        </button>
                                        <div class="reports-filter-section-content">
                                            <div class="reports-filter-options">
                                                @foreach($filter['options'] as $value => $label)
                                                    <button type="button" class="reports-filter-option" data-reports-filter-target="{{ $target }}" data-reports-filter-value="{{ $value }}">
                                                        <span class="reports-filter-check"><i class="fas fa-check" aria-hidden="true"></i></span>
                                                        <span>{{ $label }}</span>
                                                    </button>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                @endforeach

                                <div class="reports-filter-section {{ request()->filled('district_id') ? 'is-open' : '' }}" data-reports-filter-section>
                                    <button type="button" class="reports-filter-section-toggle" data-reports-filter-section-toggle>
                                        <i class="fas {{ request()->filled('district_id') ? 'fa-chevron-down' : 'fa-chevron-right' }}" aria-hidden="true"></i>
                                        <span>Distrito</span>
                                    </button>
                                    <div class="reports-filter-section-content district-filter-field">
                                        <select id="district_id" name="district_id" class="reports-district-tomselect tomselect-select" data-placeholder="Todos">
                                            <option value="">Todos</option>
                                            @foreach($districts as $district)
                                                <option value="{{ $district->id }}" {{ (string) request('district_id') === (string) $district->id ? 'selected' : '' }}>{{ $district->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="reports-filter-section {{ request('order_by', 'updated_at:desc') !== 'updated_at:desc' ? 'is-open' : '' }}" data-reports-filter-section>
                                    <button type="button" class="reports-filter-section-toggle" data-reports-filter-section-toggle>
                                        <i class="fas {{ request('order_by', 'updated_at:desc') !== 'updated_at:desc' ? 'fa-chevron-down' : 'fa-chevron-right' }}" aria-hidden="true"></i>
                                        <span>Orden</span>
                                    </button>
                                    <div class="reports-filter-section-content">
                                        <div class="reports-filter-options">
                                            @foreach(['updated_at:desc' => 'Última actualización', 'created_at:desc' => 'Creación, recientes', 'created_at:asc' => 'Creación, antiguos', 'titulo:asc' => 'Título, A-Z', 'titulo:desc' => 'Título, Z-A', 'usuario:asc' => 'Usuario, A-Z', 'usuario:desc' => 'Usuario, Z-A'] as $value => $label)
                                                <button type="button" class="reports-filter-option" data-reports-filter-target="order_by" data-reports-filter-value="{{ $value }}">
                                                    <span class="reports-filter-check"><i class="fas fa-check" aria-hidden="true"></i></span>
                                                    <span>{{ $label }}</span>
                                                </button>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="reports-filter-panel-footer">
                                <button type="button" id="reports-filter-cancel" class="reports-button reports-button--secondary">Cancelar</button>
                                <button type="submit" class="reports-button reports-button--primary">Aplicar filtros</button>
                            </div>
                        </div>
                    </div>

                    <div class="reports-search">
                        <i class="fas fa-search" aria-hidden="true"></i>
                        <input type="search" name="q" id="search" value="{{ request('q') }}" placeholder="Buscar por título o autor..." aria-label="Buscar reportes"
                               autocomplete="off" autocorrect="off" autocapitalize="none" spellcheck="false" enterkeyhint="search">
                        <span class="reports-search-progress" aria-hidden="true"></span>
                        <button type="button" class="reports-search-clear {{ request()->filled('q') ? '' : 'hidden' }}" aria-label="Limpiar búsqueda" title="Limpiar búsqueda">
                            <i class="fas fa-times" aria-hidden="true"></i>
                        </button>
                    </div>

                    <div class="reports-page-size">
                        <select name="per_page" class="reports-native-page-size" aria-hidden="true" tabindex="-1">
                            @foreach([12, 24, 48] as $size)
                                <option value="{{ $size }}" {{ (int) request('per_page', 12) === $size ? 'selected' : '' }}>{{ $size }}</option>
                            @endforeach
                        </select>
                        <div class="reports-page-size-dropdown">
                            <button type="button" class="reports-page-size-button" aria-haspopup="listbox" aria-expanded="false">
                                <span>Mostrar</span>
                                <strong>{{ (int) request('per_page', 12) }}</strong>
                                <i class="fas fa-list-ul reports-page-size-icon" aria-hidden="true"></i>
                            </button>
                            <div class="reports-page-size-menu hidden" role="listbox" aria-label="Reportes por página">
                                @foreach([12, 24, 48] as $size)
                                    <button type="button" role="option" class="reports-page-size-option {{ (int) request('per_page', 12) === $size ? 'is-active' : '' }}"
                                            data-value="{{ $size }}" aria-selected="{{ (int) request('per_page', 12) === $size ? 'true' : 'false' }}">
                                        {{ $size }}
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                @if($activeFilterCount > 0)
                    <div class="reports-filter-chips" aria-label="Filtros activos">
                        @if(request()->filled('status'))
                            <a href="{{ route('reportes.index', request()->except(['status', 'page'])) }}" class="reports-filter-chip">Estado: {{ ucfirst(request('status')) }} <i class="fas fa-times" aria-hidden="true"></i></a>
                        @endif
                        @if(request()->filled('district_id'))
                            <a href="{{ route('reportes.index', request()->except(['district_id', 'page'])) }}" class="reports-filter-chip">Distrito: {{ $districtLabel ?? 'Seleccionado' }} <i class="fas fa-times" aria-hidden="true"></i></a>
                        @endif
                        @if(request()->filled('date_filter'))
                            <a href="{{ route('reportes.index', request()->except(['date_filter', 'page'])) }}" class="reports-filter-chip">Periodo: {{ ['hoy' => 'Hoy', 'semana' => 'Esta semana', 'mes' => 'Este mes', '3meses' => 'Últimos 3 meses', 'anio' => 'Este año'][request('date_filter')] ?? request('date_filter') }} <i class="fas fa-times" aria-hidden="true"></i></a>
                        @endif
                    </div>
                @endif
            </form>

            <div id="bulk-toolbar" class="reports-bulk-toolbar app-table-bulk-inline hidden items-center gap-3" role="status" aria-live="polite">
                <div class="reports-bulk-summary">
                    <span class="app-table-selection-marker" aria-hidden="true"></span>
                    <span id="selected-count" class="app-table-selection-count whitespace-nowrap"></span>
                </div>
                <span class="reports-bulk-context">En esta página</span>
                <button id="bulk-download-files" type="button" class="reports-bulk-action reports-bulk-action--download" title="Descargar archivos seleccionados">
                    <i class="fas fa-download" aria-hidden="true"></i><span>Descargar archivos</span>
                </button>
                <button id="clear-selection" type="button" class="reports-bulk-action reports-bulk-action--clear" title="Quitar selección">Quitar selección</button>
                <button id="bulk-delete-reports" type="button" class="app-table-bulk-danger reports-bulk-delete" title="Eliminar seleccionados">
                    <i class="fas fa-trash" aria-hidden="true"></i><span>Eliminar</span>
                </button>
            </div>

            <div id="reports-table-error" class="reports-inline-error hidden" role="alert">
                <i class="fas fa-exclamation-triangle" aria-hidden="true"></i>
                <span>No se pudieron actualizar los reportes. Los datos visibles pueden estar desactualizados.</span>
                <button type="button" data-retry-reports>Reintentar</button>
            </div>

            <div class="reports-table-progress" aria-hidden="true"></div>
            <span class="sr-only" role="status" aria-live="polite" aria-atomic="true" id="reports-table-status"></span>

            <section class="reports-card-grid {{ $publications->isEmpty() ? 'is-empty' : '' }}" aria-label="Reportes">
                    @forelse($publications as $pub)
                        @php
                            // Determinar tipo de reporte y sus datos específicos
                            $tipoDisplay = '';
                            $claseModal = '';
                            $dataAttributes = [];
                            $activityInfo = ''; // Para mostrar debajo del título
                            $editRoute = '#'; // Ruta de edición
                            
                            if ($pub->publication_type === 'seguridad_vial') {
                                $tipoDisplay = 'Seguridad Vial';
                                $claseModal = 'ver-detalle-seguridad';
                                $editRoute = route('reportes.seguridad-vial.edit', $pub) . '?redirect_tipo=' . (request('tipo') ?? 'todos');
                                $reporte = $pub->roadSafetyReports->first();
                                if ($reporte) {
                                    // Mostrar el Distrito en lugar de Actividad
                                    $activityInfo = 'Distrito: ' . ($reporte->district->name ?? 'No especificado');
                                    $dataAttributes = [
                                        'data-lugar' => $reporte->location ?? '',
                                        'data-promotor' => $reporte->promoter ?? '',
                                        'data-participantes' => $reporte->participants ?? '',
                                        'data-actividad' => $reporte->activityType->name ?? '',
                                        'data-municipio' => $reporte->municipality->name ?? '',
                                        'data-distrito' => $reporte->district->name ?? '',
                                    ];
                                }
                            } elseif ($pub->publication_type === 'observatorio') {
                                $tipoDisplay = 'Observatorio de lesiones';
                                $claseModal = 'ver-detalle-observatorio';
                                $editRoute = route('reportes.observatorio.edit', $pub) . '?redirect_tipo=' . (request('tipo') ?? 'todos');
                                $reporte = $pub->injuryObservatoryReports->first();
                                if ($reporte) {
                                    // Mostrar el Distrito para Observatorio
                                    $activityInfo = 'Distrito: ' . ($reporte->district->name ?? 'No especificado');
                                    $dataAttributes = [
                                        'data-municipio' => $reporte->municipality->name ?? '',
                                        'data-distrito' => $reporte->district->name ?? '',
                                    ];
                                }
                            } elseif ($pub->publication_type === 'alcoholimetria') {
                                $tipoDisplay = 'Alcoholimetría';
                                $claseModal = 'ver-detalle-alcohol';
                                $editRoute = route('reportes.alcoholimetria.edit', $pub) . '?redirect_tipo=' . (request('tipo') ?? 'todos');
                                $reporte = $pub->breathalyzerReports->first();
                                if ($reporte) {
                                    // Mostrar el Distrito para Alcoholimetría
                                    $activityInfo = 'Distrito: ' . ($reporte->district->name ?? 'No especificado');
                                    $dataAttributes = [
                                        'data-puntos-revision' => $reporte->checkpoints ?? '',
                                        'data-conductores-no-aptos' => $reporte->drivers_not_fit ?? '',
                                        'data-pruebas-realizadas' => $reporte->tests_performed ?? '',
                                        'data-mujeres-no-aptas' => $reporte->women ?? '',
                                        'data-hombres-no-aptos' => $reporte->men ?? '',
                                        'data-automoviles-no-aptos' => $reporte->cars_trucks ?? '',
                                        'data-motocicletas-no-aptas' => $reporte->motorcycles ?? '',
                                        'data-transporte-colectivo-no-apto' => $reporte->public_transport_collective ?? '',
                                        'data-transporte-individual-no-apto' => $reporte->public_transport_individual ?? '',
                                        'data-transporte-carga-no-apto' => $reporte->cargo_transport ?? '',
                                        'data-emergencia-no-apto' => $reporte->emergency_vehicles ?? '',
                                        'data-municipio' => $reporte->municipality->name ?? '',
                                        'data-distrito' => $reporte->district->name ?? '',
                                    ];
                                }
                            } elseif ($pub->publication_type === 'grupos-vulnerables') {
                                $tipoDisplay = 'Grupos Vulnerables';
                                $claseModal = 'ver-detalle-grupos-vulnerables';
                                $editRoute = route('reportes.grupos-vulnerables.edit', $pub) . '?redirect_tipo=' . (request('tipo') ?? 'todos');
                                $reporte = $pub->gruposVulnerablesReport;
                                if ($reporte) {
                                    $activityInfo = 'Distrito: ' . ($reporte->district->name ?? 'No especificado');
                                    $dataAttributes = [
                                        'data-lugar' => $reporte->location ?? '',
                                        'data-promotor' => $reporte->promoter ?? '',
                                        'data-participantes' => $reporte->participants ?? '',
                                        'data-actividad' => $reporte->activityType->name ?? '',
                                        'data-municipio' => $reporte->municipality->name ?? '',
                                        'data-distrito' => $reporte->district->name ?? '',
                                    ];
                                } else {
                                    $activityInfo = '';
                                    $dataAttributes = [];
                                }
                            }
                            
                            // Archivos JSON - incluir ID y nombre original
                            $archivosArray = $pub->files->map(function($file) {
                                return [
                                    'id' => $file->id,
                                    'name' => $file->original_name,
                                    'public_url' => '/storage/' . ltrim($file->file_path, '/'),
                                ];
                            })->toArray();
                            $archivosJson = json_encode($archivosArray);
                        @endphp

                        @php
                            $user = $pub->user ?? null;
                            $uGiven = $user->name ?? '';
                            $uFirst = $user->first_last_name ?? '';
                            $uSecond = $user->second_last_name ?? '';
                            $uFull = trim(implode(' ', array_filter([$uGiven, $uFirst, $uSecond])));
                            $uShort = trim($uGiven . ($uFirst ? ' ' . $uFirst : '')) ?: 'Usuario';
                        @endphp

                        @php
                            // Determine if there are any unread comments for current user
                            // Only count comments from OTHER users that current user hasn't read
                            $comentarios = $pub->comentarios_json ?? [];
                            $hasUnread = false;
                            $unreadCommentsCount = 0;
                            $currentUserId = auth()->id();
                            
                            // Convert to array if it's a Collection
                            if ($comentarios instanceof \Illuminate\Support\Collection) {
                                $comentarios = $comentarios->toArray();
                            }
                            
                            // DEBUG: Log para publicación #10
                            if ($pub->id == 10) {
                                \Log::info("Publication #10 debug for user #{$currentUserId}:", [
                                    'total_comments' => count($comentarios),
                                    'comments_detail' => array_map(function($c) use ($currentUserId) {
                                        return [
                                            'id' => $c['id'] ?? 'N/A',
                                            'author_id' => $c['user']['id'] ?? 'N/A',
                                            'seen_by_current' => $c['seen_by_current_user'] ?? false,
                                            'is_own' => ($c['user']['id'] ?? null) == $currentUserId
                                        ];
                                    }, $comentarios)
                                ]);
                            }
                            
                            if (!empty($comentarios) && is_array($comentarios)) {
                                foreach ($comentarios as $cc) {
                                    // Skip comments written by current user (they don't count as "unread")
                                    $commentAuthorId = $cc['user']['id'] ?? null;
                                    if ($commentAuthorId == $currentUserId) {
                                        continue;
                                    }
                                    // Check if this comment from another user is unread
                                    if (!($cc['seen_by_current_user'] ?? false)) {
                                        $hasUnread = true;
                                        $unreadCommentsCount++;
                                    }
                                }
                            }
                            
                            // DEBUG: Log result
                            if ($pub->id == 10) {
                                \Log::info("Publication #10 hasUnread result for user #{$currentUserId}: " . ($hasUnread ? 'TRUE' : 'FALSE'));
                            }

                            $wasUpdated = $pub->created_at && $pub->updated_at && $pub->updated_at->gt($pub->created_at->copy()->addMinute());
                    $updatedDisplay = $wasUpdated
                        ? str_replace('.', '', $pub->updated_at->locale('es')->translatedFormat('d M')) . ', ' . $pub->updated_at->format('H:i')
                        : '';
                    $updatedFull = $wasUpdated ? $pub->updated_at->format('d/m/Y H:i') : '';
                    $publicationDateDisplay = str_replace('.', '', $pub->publication_date->locale('es')->translatedFormat('d M Y'));
                    $publicationDateFull = $pub->publication_date->format('d/m/Y');
                        @endphp

                        <x-publicacion-card
                            data-publication-id="{{ $pub->id }}"
                            :tipo="$tipoDisplay"
                            :titulo="$pub->topic"
                            :folio="$pub->folio"
                            :fecha="$publicationDateDisplay"
                            :fecha_full="$publicationDateFull"
                            :actualizado="$updatedDisplay"
                            :actualizado_full="$updatedFull"
                            :usuario="$uShort"
                            :usuario_full="$uFull"
                            :descripcion="$activityInfo"
                            :archivos="$archivosArray"
                            :archivosCount="$pub->files->count()"
                            :has-comments="count($pub->comentarios_json ?? []) > 0"
                            :comments-count="count($pub->comentarios_json ?? [])"
                            :has-unread="$hasUnread"
                            :unread-comments-count="$unreadCommentsCount"
                            :status="$pub->status"
                            :approvedBy="optional($pub->approver)->full_name"
                            :rejectedBy="optional($pub->rejector)->full_name"
                            :rejectionReason="$pub->rejection_reason"
                            data-publication-tipo="{{ $pub->publication_type }}"
                            class="publication-card-wrapper">

                            <div class="reports-row-actions">
                                <button class="hidden {{ $claseModal }}" 
                                        title="Ver detalles"
                                        tabindex="-1"
                                        aria-hidden="true"
                                        data-tipo="{{ $pub->publication_type }}"
                                        data-titulo="{{ $pub->topic }}"
                                        data-folio="{{ $pub->folio }}"
                                        data-fecha="{{ $publicationDateDisplay }}"
                                        data-fecha-actividad="{{ $pub->activity_date->locale('es')->isoFormat('dddd, D [de] MMMM [de] YYYY') }}"
                                        data-actualizado="{{ $updatedDisplay }}"
                                        data-usuario="{{ $uFull ?: ($pub->user->name ?? 'Usuario') }}"
                                        data-position="{{ $pub->user->position->name ?? '' }}"
                                        data-descripcion="{{ $pub->description ?? '' }}"
                                        data-archivos='{{ $archivosJson }}'
                                        data-comentarios='@json($pub->comentarios_json)'
                                        data-publication-id="{{ $pub->id }}"
                                        data-is-owner="{{ auth()->id() === $pub->user_id ? 'true' : 'false' }}"
                                        data-status="{{ $pub->status }}"
                                        data-approved-by="{{ optional($pub->approver)->full_name }}"
                                        data-rejected-by="{{ optional($pub->rejector)->full_name }}"
                                        data-rejection-reason="{{ $pub->rejection_reason }}"
                                        @foreach($dataAttributes as $key => $value)
                                            {{ $key }}="{{ $value }}"
                                        @endforeach>
                                </button>
                                @php
                                    $canEdit = $pub->canBeEditedBy(auth()->id());
                                    $isApproved = $pub->status === 'aprobado';
                                    // Autor puede eliminar solo si NO está aprobado (sin importar su rol).
                                    // Si está aprobado, solo Admin puede eliminar.
                                    $canDelete = $isApproved
                                                 ? auth()->user()->isAdmin()
                                                 : ((auth()->id() === $pub->user_id) || auth()->user()->isAdmin());
                                @endphp
                                <button type="button" class="reports-row-menu-button" aria-label="Acciones para {{ $pub->topic }}" aria-haspopup="menu" aria-expanded="false">
                                    <i class="fas fa-ellipsis-v" aria-hidden="true"></i>
                                </button>
                                <div class="reports-row-menu hidden" role="menu">
                                    <button type="button" class="reports-row-menu-item report-menu-detail" role="menuitem">
                                        <i class="fas fa-eye" aria-hidden="true"></i><span>Ver detalles</span>
                                    </button>
                                    @if($canEdit)
                                        <a href="{{ $editRoute }}" class="reports-row-menu-item" role="menuitem">
                                            <i class="fas fa-edit" aria-hidden="true"></i><span>Editar</span>
                                        </a>
                                    @endif
                                    @if($canDelete)
                                        <button type="button"
                                                class="reports-row-menu-item reports-row-menu-item--danger eliminar-reporte"
                                                role="menuitem"
                                                data-publication-id="{{ $pub->id }}"
                                                data-publication-title="{{ $pub->topic }}"
                                                data-delete-url="{{ route('reportes.destroy', $pub) }}"
                                                data-redirect-tipo="{{ request('tipo', 'todos') }}">
                                            <i class="fas fa-trash" aria-hidden="true"></i><span>Eliminar</span>
                                        </button>
                                    @endif
                                </div>
                            </div>

                        </x-publicacion-card>
                    @empty
                        <div class="reports-table-state {{ $hasActiveCriteria ? 'reports-table-state--no-results' : 'reports-table-state--empty' }}" role="status">
                            <i class="fas {{ $hasActiveCriteria ? 'fa-search' : 'fa-file-alt' }}" aria-hidden="true"></i>
                            @if($hasActiveCriteria)
                                <strong>No encontramos resultados</strong>
                                <span>Prueba con otra búsqueda o elimina los filtros aplicados.</span>
                                <a class="reports-table-state-action" href="{{ route('reportes.index') }}">Limpiar búsqueda y filtros</a>
                            @else
                                <strong>No hay reportes registrados</strong>
                                <span>Los reportes aparecerán aquí cuando se registre el primero.</span>
                            @endif
                        </div>
                    @endforelse
            </section>

            <footer class="reports-table-footer">
                <p>
                    @if($publications->total() > 0)
                        Mostrando <strong>{{ $publications->firstItem() }}-{{ $publications->lastItem() }}</strong> de <strong>{{ $publications->total() }}</strong>
                        @if($publications->total() !== $totalPublications)
                            <span class="reports-table-total">({{ $totalPublications }} totales)</span>
                        @endif
                    @else
                        Mostrando <strong>0-0</strong> de <strong>0</strong>
                        @if($totalPublications > 0)
                            <span class="reports-table-total">({{ $totalPublications }} totales)</span>
                        @endif
                    @endif
                </p>
                <div>
                    @if($publications->total() > 0)
                        {{ $publications->onEachSide(2)->links('vendor.pagination.reports') }}
                    @else
                        <nav role="navigation" aria-label="Navegación de paginación" class="reports-pagination">
                            <span class="reports-page-item reports-page-edge is-disabled" aria-disabled="true">Anterior</span>
                            <span class="reports-page-item reports-page-edge is-disabled" aria-disabled="true">Siguiente</span>
                        </nav>
                    @endif
                </div>
            </footer>
        </div>
    </main>

    <!-- INCLUIR EL COMPONENTE DEL MODAL DE ALCOHOLIMETRÍA -->
<!-- AL FINAL DEL ARCHIVO hola.blade.php, DESPUÉS de incluir los modales -->

    <!-- INCLUIR TODOS LOS COMPONENTES DE MODALES -->
<!-- Modal de rechazo -->
<div id="reject-modal" class="reports-reject-modal hidden" role="dialog" aria-modal="true" aria-labelledby="reject-modal-heading">
    <div class="reports-reject-card">
        <div class="reports-reject-heading">
            <div>
                <i class="fas fa-exclamation-triangle" aria-hidden="true"></i>
                <h3 id="reject-modal-heading">Rechazar reporte</h3>
            </div>
            <button type="button" onclick="closeRejectModal()" class="modal-cerrar" aria-label="Cerrar modal" title="Cerrar">
                <i class="fas fa-times" aria-hidden="true"></i>
            </button>
        </div>
        
        <input type="hidden" id="reject-modal-publication-id">
        
        <div class="reports-reject-body">
            <p>¿Desea rechazar <strong id="reject-modal-title"></strong>?</p>
            <label for="rejection-reason">Motivo del rechazo <span aria-hidden="true">*</span></label>
            <textarea 
                id="rejection-reason"
                rows="5"
                aria-describedby="rejection-reason-help"
                placeholder="Explique brevemente por qué se rechaza este reporte"
                maxlength="500"
                required></textarea>
            <p id="rejection-reason-help" class="reports-reject-help">Máximo 500 caracteres</p>
        </div>
        
        <div class="reports-reject-actions">
            <button type="button" onclick="closeRejectModal()" class="reports-button reports-button--secondary">
                Cancelar
            </button>
            <button type="button" onclick="submitRejection()" class="reports-button reports-button--danger">
                Rechazar reporte
            </button>
        </div>
    </div>
</div>

  <!-- INCLUIR TODOS LOS COMPONENTES DE MODALES -->
@include('components.modal-alcoholimetria')
@include('components.modal-seguridad-vial') 
@include('components.modal-observatorio')
@include('components.modal-grupos-vulnerables')

<!-- Previsualizacion de archivos -->
<div id="archivo-preview-overlay" class="hidden fixed inset-0 bg-[#2f2f2f]/95 text-white" role="dialog" aria-modal="true" aria-hidden="true" aria-label="Vista previa de archivo">
    <div id="archivo-preview-header" class="h-16 px-3 sm:px-4 flex items-center justify-between gap-3 bg-[#2f2f2f] border-b border-white/10">
        <div class="flex items-center gap-3 min-w-0">
            <button type="button" id="archivo-preview-close" class="w-10 h-10 rounded-lg flex items-center justify-center text-white/75 hover:text-white hover:bg-white/10 transition-colors" title="Cerrar">
                <i class="fas fa-times text-xl"></i>
            </button>
            <div class="min-w-0">
                <p id="archivo-preview-file-name" class="truncate font-semibold text-sm sm:text-base leading-tight">Archivo</p>
                <p id="archivo-preview-file-type" class="text-xs text-white/55 leading-tight mt-0.5">Previsualizacion</p>
            </div>
        </div>
        <div class="flex items-center gap-2 flex-shrink-0">
            <a id="archivo-preview-open" class="w-10 h-10 rounded-lg hidden md:flex items-center justify-center text-white/75 hover:text-white hover:bg-white/10 transition-colors" target="_blank" rel="noopener" title="Abrir en otra pestana">
                <i class="fas fa-external-link-alt text-lg"></i>
            </a>
            <a id="archivo-preview-download" class="w-10 h-10 rounded-lg flex items-center justify-center text-white/75 hover:text-white hover:bg-white/10 transition-colors" title="Descargar">
                <i class="fas fa-download text-lg"></i>
            </a>
        </div>
    </div>

    <button type="button" id="archivo-preview-prev" class="hidden absolute left-2 sm:left-3 top-1/2 -translate-y-1/2 w-10 h-10 rounded-full bg-black/40 hover:bg-black/65 text-white transition-colors" title="Archivo anterior">
        <i class="fas fa-chevron-left text-base"></i>
    </button>

    <button type="button" id="archivo-preview-next" class="hidden absolute right-2 sm:right-3 top-1/2 -translate-y-1/2 w-10 h-10 rounded-full bg-black/40 hover:bg-black/65 text-white transition-colors" title="Archivo siguiente">
        <i class="fas fa-chevron-right text-base"></i>
    </button>

    <div id="archivo-preview-content" class="h-[calc(100dvh-4rem)] overflow-auto flex items-start justify-center p-3 md:p-5">
        <div class="text-center text-white/70 mt-20">
            <i class="fas fa-file-alt text-4xl mb-3"></i>
            <p>Selecciona un archivo para previsualizar.</p>
        </div>
    </div>
</div>

<!-- Navegación de Reportes (Flechas) -->
<div id="report-nav-container" class="hidden fixed inset-0 z-[9999999] pointer-events-none">
    <!-- Flecha izquierda (posicionada a la izquierda) -->
    <button id="report-nav-prev" 
            class="pointer-events-auto absolute left-4 top-1/2 -translate-y-1/2 w-12 h-12 rounded-full bg-[#404041] text-white hover:bg-[#2a2a2a] transition-all duration-200 flex items-center justify-center shadow-lg hidden hover:shadow-xl cursor-pointer"
            title="Reporte anterior (Flecha izquierda)">
        <i class="fas fa-chevron-left text-xl pointer-events-none"></i>
    </button>
    
    <!-- Flecha derecha (posicionada a la derecha) -->
    <button id="report-nav-next" 
            class="pointer-events-auto absolute right-4 top-1/2 -translate-y-1/2 w-12 h-12 rounded-full bg-[#404041] text-white hover:bg-[#2a2a2a] transition-all duration-200 flex items-center justify-center shadow-lg hidden hover:shadow-xl cursor-pointer"
            title="Siguiente reporte (Flecha derecha)">
        <i class="fas fa-chevron-right text-xl pointer-events-none"></i>
    </button>
</div>

    <!-- JAVASCRIPT SIMPLIFICADO Y FUNCIONAL -->
    <script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('=== INICIANDO SISTEMA DE MODALES ===');
    const CURRENT_USER_ID = {{ auth()->id() ?? 'null' }};
    
    // ===== SISTEMA DE NAVEGACIÓN DE REPORTES =====
    let allReportButtons = [];
    let currentReportIndex = -1;
    let currentPublicationId = null; // Agregar variable para rastrear el ID actual
    let currentModalType = null;
    window.isNavigating = false; // Flag para detectar si estamos navegando
    const reportesIndexPath = new URL('{{ route("reportes.index") }}', window.location.origin).pathname;
    let reportesPanelAbortController = null;
    const reportesPanelCache = new Map();
    let reportsMenuTrigger = null;

    function getReportesPanel() {
        return document.getElementById('reportes-publicaciones-panel');
    }

    function isReportesIndexUrl(url) {
        try {
            const parsed = new URL(url, window.location.origin);
            return parsed.origin === window.location.origin && parsed.pathname === reportesIndexPath;
        } catch (error) {
            return false;
        }
    }

    function buildReportesUrlFromForm(form) {
        const url = new URL(form.action || window.location.href, window.location.origin);
        const params = new URLSearchParams();
        new FormData(form).forEach((value, key) => {
            if (key === 'page') return;
            const stringValue = String(value ?? '').trim();
            if (stringValue !== '') {
                params.append(key, stringValue);
            }
        });
        url.search = params.toString();
        return url.toString();
    }

    function setReportesPanelLoading(isLoading, kind = 'refresh') {
        const panel = getReportesPanel();
        if (!panel) return;
        panel.setAttribute('aria-busy', isLoading ? 'true' : 'false');
        panel.classList.toggle('is-searching', isLoading && kind === 'search');
        const status = panel.querySelector('#reports-table-status');
        if (status) status.textContent = isLoading ? (kind === 'search' ? 'Buscando reportes' : 'Actualizando reportes') : '';
        if (isLoading) panel.querySelector('#reports-table-error')?.classList.add('hidden');
    }

    function replaceReportesPanelFromHtml(html, url, options = {}) {
        const parser = new DOMParser();
        const doc = parser.parseFromString(html, 'text/html');
        const nextPanel = doc.getElementById('reportes-publicaciones-panel');
        const currentPanel = getReportesPanel();

        if (!nextPanel || !currentPanel) {
            window.location.href = url;
            return false;
        }

        currentPanel.replaceWith(nextPanel);
        if (options.push !== false && window.location.href !== url) {
            history.pushState({ reportesAjax: true }, '', url);
        }
        initializeReportesDynamicArea({ scroll: options.scroll === true });
        return true;
    }

    function loadReportesPanel(url, options = {}) {
        if (!isReportesIndexUrl(url)) {
            window.location.href = url;
            return Promise.resolve(false);
        }

        const targetUrl = new URL(url, window.location.origin).toString();
        if (reportesPanelCache.has(targetUrl) && options.cache !== false) {
            return Promise.resolve(replaceReportesPanelFromHtml(reportesPanelCache.get(targetUrl), targetUrl, options));
        }

        if (reportesPanelAbortController) {
            reportesPanelAbortController.abort();
        }
        reportesPanelAbortController = new AbortController();
        setReportesPanelLoading(true, options.search ? 'search' : 'refresh');

        return fetch(targetUrl, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'text/html'
            },
            signal: reportesPanelAbortController.signal
        })
        .then(response => {
            if (!response.ok) throw new Error(`HTTP ${response.status}`);
            return response.text().then(html => ({ html, finalUrl: response.url || targetUrl }));
        })
        .then(({ html, finalUrl }) => {
            reportesPanelCache.set(finalUrl, html);
            reportesPanelCache.set(targetUrl, html);
            return replaceReportesPanelFromHtml(html, finalUrl, options);
        })
        .catch(error => {
            if (error.name === 'AbortError') return false;
            console.error('Error cargando reportes sin recargar:', error);
            getReportesPanel()?.querySelector('#reports-table-error')?.classList.remove('hidden');
            return false;
        })
        .finally(() => {
            setReportesPanelLoading(false);
            reportesPanelAbortController = null;
        });
    }

    function prefetchReportesPanel(url) {
        if (!isReportesIndexUrl(url)) return;
        const targetUrl = new URL(url, window.location.origin).toString();
        if (reportesPanelCache.has(targetUrl)) return;

        fetch(targetUrl, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'text/html'
            }
        })
        .then(response => {
            if (!response.ok) throw new Error(`HTTP ${response.status}`);
            return response.text().then(html => ({ html, finalUrl: response.url || targetUrl }));
        })
        .then(({ html, finalUrl }) => {
            reportesPanelCache.set(targetUrl, html);
            reportesPanelCache.set(finalUrl, html);
        })
        .catch(() => {});
    }

    function buildTabPrefetchUrl(tabButton) {
        const panel = getReportesPanel();
        const form = panel?.querySelector('form[action*="publicaciones"]');
        if (!form) return null;

        const clone = form.cloneNode(true);
        const tipoInput = clone.querySelector('#filter-tipo-input');
        if (tipoInput) tipoInput.value = tabButton.getAttribute('data-tipo') || 'todos';
        const search = clone.querySelector('#search');
        if (search) search.value = '';
        const status = clone.querySelector('select[name="status"]');
        if (status) status.value = '';
        const district = clone.querySelector('select[name="district_id"]');
        if (district) district.value = '';
        const dateFilter = clone.querySelector('select[name="date_filter"]');
        if (dateFilter) dateFilter.value = '';
        const orderBy = clone.querySelector('select[name="order_by"]');
        if (orderBy) orderBy.value = 'updated_at:desc';

        return buildReportesUrlFromForm(clone);
    }

    function scheduleReportesPrefetch() {
        const run = () => {
            const panel = getReportesPanel();
            if (!panel) return;

            panel.querySelectorAll('.tab-filter').forEach(tabButton => {
                const url = buildTabPrefetchUrl(tabButton);
                if (url) prefetchReportesPanel(url);
            });

            panel.querySelectorAll('a[href]').forEach(link => {
                if (isReportesIndexUrl(link.href)) {
                    prefetchReportesPanel(link.href);
                }
            });
        };

        if ('requestIdleCallback' in window) {
            window.requestIdleCallback(run, { timeout: 1200 });
        } else {
            window.setTimeout(run, 300);
        }
    }

    function refreshReportesPanel(options = {}) {
        reportesPanelCache.clear();
        return loadReportesPanel(window.location.href, {
            push: false,
            scroll: false,
            cache: false,
            ...options
        });
    }

    function updateActiveTabFromUrl(url = window.location.href) {
        const tabButtons = document.querySelectorAll('.tab-filter');
        const currentTipo = new URL(url, window.location.origin).searchParams.get('tipo') || 'todos';
        const activeButton = Array.from(tabButtons).find(btn => btn.getAttribute('data-tipo') === currentTipo) || tabButtons[0];
        if (activeButton) {
            updateActiveTab(activeButton);
        }
    }

    function initializeReportesDynamicArea(options = {}) {
        const currentPanel = getReportesPanel();
        if (currentPanel) {
            reportesPanelCache.set(new URL(window.location.href, window.location.origin).toString(), currentPanel.outerHTML);
        }
        updateActiveTabFromUrl();
        collectAllReportButtons();
        bindReportModalOpeners();
        bindArchivoPreviewControls();
        if (typeof window.initializeReportesTomSelect === 'function') {
            window.initializeReportesTomSelect();
        }
        if (typeof toggleBulkToolbar === 'function') {
            toggleBulkToolbar();
        }
        if (options.scroll) {
            getReportesPanel()?.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
        scheduleReportesPrefetch();
    }

    document.addEventListener('submit', function(e) {
        const form = e.target.closest('form[action*="publicaciones"]');
        if (!form || !getReportesPanel()?.contains(form)) return;

        e.preventDefault();
        loadReportesPanel(buildReportesUrlFromForm(form), {
            push: true,
            scroll: false,
            search: document.activeElement?.id === 'search'
        });
    });

    function closeReportsFilter({ reset = false } = {}) {
        const panel = document.getElementById('reports-filter-panel');
        const toggle = document.getElementById('reports-filter-toggle');
        const form = document.getElementById('reports-filter-form');
        if (!panel || !toggle) return;
        if (reset && form) {
            form.reset();
            const district = form.querySelector('#district_id');
            if (district?.tomselect) district.tomselect.setValue(district.value, true);
        }
        panel.classList.add('hidden');
        toggle.setAttribute('aria-expanded', 'false');
    }

    window.syncReportsFilterPanel = function() {
        const form = document.getElementById('reports-filter-form');
        if (!form) return;
        form.querySelectorAll('.reports-filter-option[data-reports-filter-target]').forEach(option => {
            const control = form.elements.namedItem(option.dataset.reportsFilterTarget);
            const active = control && String(control.value) === String(option.dataset.reportsFilterValue || '');
            option.classList.toggle('is-active', active);
            option.setAttribute('aria-pressed', active ? 'true' : 'false');
        });
    };

    function closeReportsRowMenus({ restoreFocus = false } = {}) {
        document.querySelectorAll('.reports-row-menu:not(.hidden)').forEach(menu => menu.classList.add('hidden'));
        document.querySelectorAll('.reports-row-menu-button[aria-expanded="true"]').forEach(button => button.setAttribute('aria-expanded', 'false'));
        if (restoreFocus && reportsMenuTrigger) reportsMenuTrigger.focus();
        reportsMenuTrigger = null;
    }

    function openReportsRowMenu(button) {
        const menu = button.closest('.reports-row-actions')?.querySelector('.reports-row-menu');
        if (!menu) return;
        closeReportsRowMenus();
        reportsMenuTrigger = button;
        menu.classList.remove('hidden');
        button.setAttribute('aria-expanded', 'true');
        const buttonRect = button.getBoundingClientRect();
        const menuRect = menu.getBoundingClientRect();
        const openAbove = window.innerHeight - buttonRect.bottom < menuRect.height + 12 && buttonRect.top > menuRect.height + 12;
        menu.style.top = `${openAbove ? buttonRect.top - menuRect.height - 6 : buttonRect.bottom + 6}px`;
        menu.style.left = `${Math.max(8, Math.min(window.innerWidth - menuRect.width - 8, buttonRect.right - menuRect.width))}px`;
        menu.querySelector('[role="menuitem"]')?.focus();
    }

    function closeReportsRowMenuOnViewportChange() {
        if (document.querySelector('.reports-row-menu:not(.hidden)')) {
            closeReportsRowMenus();
        }
    }

    document.addEventListener('scroll', closeReportsRowMenuOnViewportChange, { capture: true, passive: true });
    window.addEventListener('resize', closeReportsRowMenuOnViewportChange, { passive: true });
    window.addEventListener('orientationchange', closeReportsRowMenuOnViewportChange, { passive: true });

    document.addEventListener('change', function(e) {
        const pageSize = e.target.closest('.reports-page-size select');
        if (!pageSize || !getReportesPanel()?.contains(pageSize)) return;
        const form = pageSize.closest('form');
        if (form) loadReportesPanel(buildReportesUrlFromForm(form), { push: true, scroll: false, cache: false });
    });

    document.addEventListener('click', function(e) {
        const searchClear = e.target.closest('.reports-search-clear');
        if (searchClear && getReportesPanel()?.contains(searchClear)) {
            e.preventDefault();
            const search = searchClear.closest('.reports-search')?.querySelector('#search');
            const form = search?.closest('form');
            if (!search || !form) return;
            search.value = '';
            searchClear.classList.add('hidden');
            loadReportesPanel(buildReportesUrlFromForm(form), {
                push: true,
                scroll: false,
                search: true,
                cache: false
            }).then(() => document.getElementById('search')?.focus());
            return;
        }

        const pageSizeButton = e.target.closest('.reports-page-size-button');
        if (pageSizeButton && getReportesPanel()?.contains(pageSizeButton)) {
            e.preventDefault();
            e.stopPropagation();
            const dropdown = pageSizeButton.closest('.reports-page-size-dropdown');
            const menu = dropdown?.querySelector('.reports-page-size-menu');
            const willOpen = menu?.classList.contains('hidden');
            document.querySelectorAll('.reports-page-size-menu:not(.hidden)').forEach(openMenu => {
                if (openMenu !== menu) {
                    openMenu.classList.add('hidden');
                    openMenu.closest('.reports-page-size-dropdown')?.querySelector('.reports-page-size-button')?.setAttribute('aria-expanded', 'false');
                }
            });
            menu?.classList.toggle('hidden', !willOpen);
            pageSizeButton.setAttribute('aria-expanded', willOpen ? 'true' : 'false');
            if (willOpen) window.requestAnimationFrame(() => menu?.querySelector('[aria-selected="true"]')?.focus());
            return;
        }

        const pageSizeOption = e.target.closest('.reports-page-size-option');
        if (pageSizeOption && getReportesPanel()?.contains(pageSizeOption)) {
            e.preventDefault();
            const dropdown = pageSizeOption.closest('.reports-page-size-dropdown');
            const pageSize = dropdown?.closest('.reports-page-size')?.querySelector('select');
            const button = dropdown?.querySelector('.reports-page-size-button');
            if (!pageSize) return;
            pageSize.value = pageSizeOption.dataset.value || '12';
            button?.querySelector('strong')?.replaceChildren(document.createTextNode(pageSize.value));
            dropdown.querySelectorAll('.reports-page-size-option').forEach(option => {
                const active = option === pageSizeOption;
                option.classList.toggle('is-active', active);
                option.setAttribute('aria-selected', active ? 'true' : 'false');
            });
            dropdown.querySelector('.reports-page-size-menu')?.classList.add('hidden');
            button?.setAttribute('aria-expanded', 'false');
            pageSize.dispatchEvent(new Event('change', { bubbles: true }));
            return;
        }

        const filterSectionToggle = e.target.closest('[data-reports-filter-section-toggle]');
        if (filterSectionToggle && getReportesPanel()?.contains(filterSectionToggle)) {
            const section = filterSectionToggle.closest('[data-reports-filter-section]');
            const willOpen = !section?.classList.contains('is-open');
            section?.classList.toggle('is-open', willOpen);
            const icon = filterSectionToggle.querySelector('i');
            icon?.classList.toggle('fa-chevron-down', willOpen);
            icon?.classList.toggle('fa-chevron-right', !willOpen);
            return;
        }

        const filterOption = e.target.closest('.reports-filter-option[data-reports-filter-target]');
        if (filterOption && getReportesPanel()?.contains(filterOption)) {
            const form = filterOption.closest('form');
            const control = form?.elements.namedItem(filterOption.dataset.reportsFilterTarget);
            if (control) control.value = filterOption.dataset.reportsFilterValue || '';
            syncReportsFilterPanel();
            return;
        }

        const filterToggle = e.target.closest('#reports-filter-toggle');
        if (filterToggle && getReportesPanel()?.contains(filterToggle)) {
            const filterPanel = document.getElementById('reports-filter-panel');
            const willOpen = filterPanel?.classList.contains('hidden');
            filterPanel?.classList.toggle('hidden', !willOpen);
            filterToggle.setAttribute('aria-expanded', willOpen ? 'true' : 'false');
            if (willOpen) {
                syncReportsFilterPanel();
                filterPanel?.querySelector('button, select, input, a')?.focus();
            }
            return;
        }

        const filterCancel = e.target.closest('#reports-filter-cancel');
        if (filterCancel && getReportesPanel()?.contains(filterCancel)) {
            closeReportsFilter({ reset: true });
            document.getElementById('reports-filter-toggle')?.focus();
            return;
        }

        const rowMenuButton = e.target.closest('.reports-row-menu-button');
        if (rowMenuButton && getReportesPanel()?.contains(rowMenuButton)) {
            e.preventDefault();
            if (rowMenuButton.getAttribute('aria-expanded') === 'true') closeReportsRowMenus({ restoreFocus: true });
            else openReportsRowMenu(rowMenuButton);
            return;
        }

        const reportDetail = e.target.closest('.report-menu-detail');
        if (reportDetail && getReportesPanel()?.contains(reportDetail)) {
            const row = reportDetail.closest('.publication-card');
            closeReportsRowMenus();
            row?.querySelector('button[title="Ver detalles"]')?.click();
            return;
        }

        const retry = e.target.closest('[data-retry-reports]');
        if (retry && getReportesPanel()?.contains(retry)) {
            e.preventDefault();
            loadReportesPanel(window.location.href, { push: false, scroll: false, cache: false });
            return;
        }

        const tabButton = e.target.closest('.tab-filter');
        if (tabButton && getReportesPanel()?.contains(tabButton)) {
            updateActiveTab(tabButton);
            const url = buildTabPrefetchUrl(tabButton);
            if (url) loadReportesPanel(url, { push: true, scroll: false });
            return;
        }

        const link = e.target.closest('a[href]');
        if (!link || !getReportesPanel()?.contains(link)) return;
        const href = link.getAttribute('href');
        if (!href || href.startsWith('#') || link.target || link.hasAttribute('download')) return;
        if (!isReportesIndexUrl(link.href)) return;

        e.preventDefault();
        loadReportesPanel(link.href, { push: true, scroll: true });
    });

    document.addEventListener('click', function(e) {
        if (!e.target.closest('.reports-filter-popover-wrap, .ts-dropdown')) closeReportsFilter();
        if (!e.target.closest('.reports-page-size-dropdown')) {
            document.querySelectorAll('.reports-page-size-menu:not(.hidden)').forEach(menu => {
                menu.classList.add('hidden');
                menu.closest('.reports-page-size-dropdown')?.querySelector('.reports-page-size-button')?.setAttribute('aria-expanded', 'false');
            });
        }
        if (!e.target.closest('.reports-row-actions, .reports-row-menu')) closeReportsRowMenus();
    });

    document.addEventListener('keydown', function(e) {
        if (e.key !== 'Escape') return;
        if (document.querySelector('#reports-filter-panel:not(.hidden)')) {
            closeReportsFilter({ reset: true });
            document.getElementById('reports-filter-toggle')?.focus();
        } else if (document.querySelector('.reports-row-menu:not(.hidden)')) {
            closeReportsRowMenus({ restoreFocus: true });
        } else if (document.querySelector('.reports-page-size-menu:not(.hidden)')) {
            const menu = document.querySelector('.reports-page-size-menu:not(.hidden)');
            const button = menu?.closest('.reports-page-size-dropdown')?.querySelector('.reports-page-size-button');
            menu?.classList.add('hidden');
            button?.setAttribute('aria-expanded', 'false');
            button?.focus();
        }
    });

    document.addEventListener('mouseover', function(e) {
        const tabButton = e.target.closest('.tab-filter');
        if (tabButton && getReportesPanel()?.contains(tabButton)) {
            const url = buildTabPrefetchUrl(tabButton);
            if (url) prefetchReportesPanel(url);
            return;
        }

        const link = e.target.closest('a[href]');
        if (link && getReportesPanel()?.contains(link) && isReportesIndexUrl(link.href)) {
            prefetchReportesPanel(link.href);
        }
    });

    window.addEventListener('popstate', function() {
        if (isReportesIndexUrl(window.location.href)) {
            loadReportesPanel(window.location.href, { push: false, scroll: false });
        }
    });
    
    function collectAllReportButtons() {
        // Recolectar todos los botones de reporte en orden
        allReportButtons = [];
        
        // Recorrer el contenedor de publicaciones y recolectar todos los botones de "Ver detalles"
        document.querySelectorAll('.publication-card-wrapper').forEach((card, idx) => {
            const eyeBtn = card.querySelector('button[title="Ver detalles"]');
            if (eyeBtn) {
                allReportButtons.push(eyeBtn);
                console.log(`📌 Botón ${idx}: ID=${eyeBtn.dataset.publicationId}, Tipo=${eyeBtn.dataset.tipo}`);
            }
        });
        
        console.log(`✅ Recolectados ${allReportButtons.length} reportes`);
    }
    
    function updateNavigationArrows() {
        const prevBtn = document.getElementById('report-nav-prev');
        const nextBtn = document.getElementById('report-nav-next');
        const navContainer = document.getElementById('report-nav-container');
        
        if (!prevBtn || !nextBtn || !navContainer) return;
        
        // Mostrar/ocultar el contenedor solo si hay un modal abierto
        const openModal = document.querySelector('[id^="modal"]:not(.hidden)');
        
        if (!openModal || allReportButtons.length <= 1) {
            navContainer.classList.add('hidden');
            return;
        }
        
        navContainer.classList.remove('hidden');
        
        console.log(`📍 Estado de navegación: currentReportIndex=${currentReportIndex}, total=${allReportButtons.length}, currentPublicationId=${currentPublicationId}`);
        
        // Mostrar/ocultar flecha anterior
        if (currentReportIndex > 0) {
            prevBtn.classList.remove('hidden');
            console.log(`✅ Flecha anterior visible (index > 0: ${currentReportIndex} > 0)`);
        } else {
            prevBtn.classList.add('hidden');
            console.log(`❌ Flecha anterior oculta (index = 0: ${currentReportIndex})`);
        }
        
        // Mostrar/ocultar flecha siguiente
        if (currentReportIndex < allReportButtons.length - 1) {
            nextBtn.classList.remove('hidden');
            console.log(`✅ Flecha siguiente visible (index < max: ${currentReportIndex} < ${allReportButtons.length - 1})`);
        } else {
            nextBtn.classList.add('hidden');
            console.log('❌ Flecha siguiente oculta');
        }
    }
    
    function navigateToReport(index) {
        if (index < 0 || index >= allReportButtons.length) {
            console.warn(`❌ Índice inválido: ${index}`);
            return;
        }
        
        console.log(`🔄 Navegando de #${currentReportIndex + 1} a #${index + 1}`);
        
        // Actualizar índice
        currentReportIndex = index;
        const btn = allReportButtons[index];
        
        if (btn) {
            console.log(`👆 Simulando click en reporte #${index + 1}`);
            
            // Determinar cuál modal es actualmente visible
            const openModal = document.querySelector('[id^="modal"]:not(.hidden)');
            
            if (openModal) {
                // Ocultar el modal anterior SIN animación
                console.log('🔐 Cerrando modal anterior');
                openModal.classList.add('hidden');
                
                // Hacer click INMEDIATAMENTE al botón del nuevo reporte
                console.log('✅ Abriendo nuevo modal sin transición');
                // Usar un flag para indicar que es navegación (sin animación)
                window.isNavigating = true;
                btn.click();
                window.isNavigating = false;
                
                // Actualizar flechas de navegación
                updateNavigationArrows();
            } else {
                // Si no hay modal abierto, abrir normalmente
                btn.click();
                updateNavigationArrows();
            }
        }
    }
    
    window.navigateToPrevReport = function() {
        console.log('🔙 Navegar al anterior - función llamada');
        navigateToReport(currentReportIndex - 1);
    };
    
    window.navigateToNextReport = function() {
        console.log('🔜 Navegar al siguiente - función llamada');
        navigateToReport(currentReportIndex + 1);
    };
    
    // Usar delegación de eventos a nivel de documento para las flechas
    document.addEventListener('click', function(e) {
        // Verificar si el click es en alguna de las flechas
        const prevBtn = e.target.closest('#report-nav-prev');
        const nextBtn = e.target.closest('#report-nav-next');
        
        if (prevBtn) {
            console.log('✅ Click en flecha anterior - evento capturado');
            e.preventDefault();
            e.stopPropagation();
            window.navigateToPrevReport();
            return false;
        }
        
        if (nextBtn) {
            console.log('✅ Click en flecha siguiente - evento capturado');
            e.preventDefault();
            e.stopPropagation();
            window.navigateToNextReport();
            return false;
        }
    }, true); // Captura = true para ejecutarse primero
    
    // Teclado: flechas izquierda/derecha
    document.addEventListener('keydown', function(e) {
        const filePreviewOpen = document.getElementById('archivo-preview-overlay') && !document.getElementById('archivo-preview-overlay').classList.contains('hidden');
        if (filePreviewOpen) return;

        const openModal = document.querySelector('[id^="modal"]:not(.hidden)');
        if (!openModal) return;
        
        if (e.key === 'ArrowLeft') {
            e.preventDefault();
            navigateToPrevReport();
        } else if (e.key === 'ArrowRight') {
            e.preventDefault();
            navigateToNextReport();
        }
    });
    
    // Llamar inicialmente para recolectar botones
    collectAllReportButtons();
    
    // Función simple para mostrar modal - GLOBAL (original)
    window.showModalBase = function(modalId, skipAnimation = false) {
        console.log('Intentando mostrar modal:', modalId);
        const modal = document.getElementById(modalId);
        
        if (!modal) {
            console.error('❌ Modal no encontrado:', modalId);
            return false;
        }
        
        console.log('✅ Modal encontrado, mostrando...');
        
        // Desactivar scroll de la página principal
        modal._previouslyFocusedElement = document.activeElement;
        document.body.style.overflow = 'hidden';

        modal.classList.remove('hidden');
        modal.setAttribute('aria-hidden', 'false');

        requestAnimationFrame(() => {
            modal.querySelector('.modal-cerrar')?.focus({ preventScroll: true });
        });
        
        // Solo hacer animación si no es navegación
        if (!skipAnimation) {
            setTimeout(() => {
                const content = modal.querySelector('.publication-detail-modal');
                if (content) {
                    content.style.transform = 'translateY(0) scale(1)';
                    content.style.opacity = '1';
                }
            }, 50);
        } else {
            // Si es navegación, mostrar sin animación
            const content = modal.querySelector('.publication-detail-modal');
            if (content) {
                content.style.transform = 'translateY(0) scale(1)';
                content.style.opacity = '1';
            }
        }
        
        return true;
    }
    
    // Envoltura de showModal que incluye actualización de navegación
    window.showModal = function(modalId) {
        // Recolectar botones cuando se abre un modal
        collectAllReportButtons();
        
        // Re-establecer el índice correcto basado en el currentPublicationId
        if (currentPublicationId) {
            currentReportIndex = allReportButtons.findIndex(btn => btn.dataset.publicationId === currentPublicationId);
            console.log(`🔄 Índice actualizado: #${currentReportIndex + 1} de ${allReportButtons.length}`);
        }
        
        // Si estamos navegando, no hacer animación
        const skipAnimation = window.isNavigating || false;
        
        // Llamar a la función base
        const result = showModalBase(modalId, skipAnimation);
        
        // Actualizar flechas
        updateNavigationArrows();
        
        return result;
    }
    
    // Función para cerrar modal - GLOBAL
    window.closeModal = function(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            const content = modal.querySelector('.publication-detail-modal');
            if (content) {
                content.style.transform = 'translateY(12px) scale(0.985)';
                content.style.opacity = '0';
            }
            
            setTimeout(() => {
                modal.classList.add('hidden');
                modal.setAttribute('aria-hidden', 'true');
                // Ocultar flechas cuando se cierra el modal
                const navContainer = document.getElementById('report-nav-container');
                if (navContainer) {
                    navContainer.classList.add('hidden');
                }
                
                // Restaurar scroll de la página si no hay más modales abiertos
                const anyModalOpen = document.querySelector('[id^="modal"]:not(.hidden)') || 
                                     document.getElementById('reject-modal')?.classList.contains('hidden') === false;
                if (!anyModalOpen) {
                    document.body.style.overflow = 'auto';
                }

                if (modal._previouslyFocusedElement?.isConnected) {
                    modal._previouslyFocusedElement.focus({ preventScroll: true });
                }
            }, 300);
        }
    }
    
    // Configurar eventos de cierre para todos los modales
    document.querySelectorAll('[id^="modal"]').forEach(modal => {
        const closeBtn = modal.querySelector('.modal-cerrar');
        if (closeBtn) {
            closeBtn.addEventListener('click', () => {
                closeModal(modal.id);
            });
        }
        
        // Cerrar al hacer click fuera del contenido
        modal.addEventListener('click', (e) => {
            // No cerrar si el click es en el contenedor de navegación
            const navContainer = document.getElementById('report-nav-container');
            if (navContainer && (navContainer === e.target || navContainer.contains(e.target))) {
                console.log('❌ Click ignorado - pertenece al contenedor de navegación');
                e.stopPropagation();
                return false;
            }
            
            // Solo cerrar si el click es directamente en el modal (el fondo)
            if (e.target === modal) {
                console.log('✅ Cerrando modal - click en el fondo');
                closeModal(modal.id);
            }
        });
        
        // Cerrar con ESC
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && !modal.classList.contains('hidden')) {
                closeModal(modal.id);
            } else if (e.key === 'Tab' && !modal.classList.contains('hidden')) {
                const focusable = Array.from(modal.querySelectorAll(
                    'button:not([disabled]):not([hidden]), a[href], textarea:not([disabled]), input:not([disabled]), select:not([disabled]), [tabindex]:not([tabindex="-1"])'
                )).filter((element) => element.offsetParent !== null);

                if (!focusable.length) return;
                const first = focusable[0];
                const last = focusable[focusable.length - 1];

                if (e.shiftKey && document.activeElement === first) {
                    e.preventDefault();
                    last.focus();
                } else if (!e.shiftKey && document.activeElement === last) {
                    e.preventDefault();
                    first.focus();
                }
            }
        });
    });
    
    // Cerrar modal de rechazo con ESC
    document.addEventListener('keydown', (e) => {
        const rejectModal = document.getElementById('reject-modal');
        if (!rejectModal || rejectModal.classList.contains('hidden')) return;
        if (e.key === 'Escape') {
            e.preventDefault();
            closeRejectModal();
        } else if (e.key === 'Tab') {
            const focusable = Array.from(rejectModal.querySelectorAll('button:not([disabled]), textarea:not([disabled])'));
            if (focusable.length) {
                const first = focusable[0];
                const last = focusable[focusable.length - 1];
                if (e.shiftKey && document.activeElement === first) {
                    e.preventDefault();
                    last.focus();
                } else if (!e.shiftKey && document.activeElement === last) {
                    e.preventDefault();
                    first.focus();
                }
            }
        }
    });

    document.addEventListener('click', (e) => {
        const rejectModal = document.getElementById('reject-modal');
        if (e.target === rejectModal && !rejectModal.classList.contains('hidden')) closeRejectModal();
    });
    
    // === CONFIGURAR BOTONES DE APERTURA ===
    function bindReportModalOpeners() {
    
    // Alcoholimetría
    document.querySelectorAll('.ver-detalle-alcohol:not([data-report-modal-bound])').forEach(btn => {
        btn.dataset.reportModalBound = 'true';
        btn.addEventListener('click', function() {
            console.log('🎯 Click en botón Alcoholimetría');
            
            // Recolectar botones si no están recolectados
            if (allReportButtons.length === 0) {
                collectAllReportButtons();
            }
            
            // Guardar el ID del reporte actual
            currentPublicationId = this.dataset.publicationId;
            console.log(`🔍 Buscando publicationId: ${currentPublicationId}`);
            
            // Establecer el índice basado en data-publication-id (más robusto)
            currentReportIndex = allReportButtons.findIndex(btn => btn.dataset.publicationId === currentPublicationId);
            console.log(`📍 Encontrado en índice: ${currentReportIndex}, mostrado como #${currentReportIndex + 1} de ${allReportButtons.length}`);
            
            const modal = document.getElementById('modalAlcoholimetria');
            if (modal) {
                // Datos básicos
                fillBasicData(modal, this.dataset);
                
                // Datos específicos de alcoholimetría
                // Use kebab-case for class names and convert to camelCase to access dataset properties
                const specificFields = [
                    'puntos-revision', 'conductores-no-aptos', 'pruebas-realizadas',
                    'mujeres-no-aptas', 'hombres-no-aptos', 'automoviles-no-aptos',
                    'motocicletas-no-aptas', 'transporte-colectivo-no-apto',
                    'transporte-individual-no-apto', 'transporte-carga-no-apto', 'emergencia-no-apto'
                ];

                function kebabToCamel(s) {
                    return s.replace(/-([a-z])/g, function(_, c) { return c.toUpperCase(); });
                }

                specificFields.forEach(kebab => {
                    const element = modal.querySelector(`.modal-${kebab}`);
                    const key = kebabToCamel(kebab);
                    if (element && this.dataset[key] !== undefined && this.dataset[key] !== '') {
                        element.textContent = this.dataset[key];
                    }
                });

                // Municipio y Distrito
                const geoFields = ['municipio', 'distrito'];
                geoFields.forEach(field => {
                    const element = modal.querySelector(`.modal-${field}`);
                    if (element && this.dataset[field]) {
                        element.textContent = this.dataset[field];
                    }
                });
                
                // Llenar archivos y comentarios
                fillFilesAndComments(modal, this.dataset);
            }
            
            showModal('modalAlcoholimetria');
        });
    });
    
    // Seguridad Vial
    document.querySelectorAll('.ver-detalle-seguridad:not([data-report-modal-bound])').forEach(btn => {
        btn.dataset.reportModalBound = 'true';
        btn.addEventListener('click', function() {
            console.log('🎯 Click en botón Seguridad Vial');
            
            // Recolectar botones si no están recolectados
            if (allReportButtons.length === 0) {
                collectAllReportButtons();
            }
            
            // Guardar el ID del reporte actual
            currentPublicationId = this.dataset.publicationId;
            console.log(`🔍 Buscando publicationId: ${currentPublicationId}`);
            
            // Establecer el índice basado en data-publication-id (más robusto)
            currentReportIndex = allReportButtons.findIndex(btn => btn.dataset.publicationId === currentPublicationId);
            console.log(`📍 Encontrado en índice: ${currentReportIndex}, mostrado como #${currentReportIndex + 1} de ${allReportButtons.length}`);
            
            const modal = document.getElementById('modalSeguridadVial');
            if (modal) {
                // Datos básicos
                fillBasicData(modal, this.dataset);
                
                // Datos específicos de seguridad vial
                const specificFields = ['lugar', 'promotor', 'participantes', 'actividad', 'municipio', 'distrito'];
                specificFields.forEach(field => {
                    const element = modal.querySelector(`.modal-${field}`);
                    if (element && this.dataset[field]) {
                        element.textContent = this.dataset[field];
                    }
                });
                
                // Llenar archivos y comentarios
                fillFilesAndComments(modal, this.dataset);
            }
            
            showModal('modalSeguridadVial');
        });
    });
    
    // Observatorio
    document.querySelectorAll('.ver-detalle-observatorio:not([data-report-modal-bound])').forEach(btn => {
        btn.dataset.reportModalBound = 'true';
        btn.addEventListener('click', function() {
            console.log('🎯 Click en botón Observatorio');
            
            // Recolectar botones si no están recolectados
            if (allReportButtons.length === 0) {
                collectAllReportButtons();
            }
            
            // Guardar el ID del reporte actual
            currentPublicationId = this.dataset.publicationId;
            console.log(`🔍 Buscando publicationId: ${currentPublicationId}`);
            
            // Establecer el índice basado en data-publication-id (más robusto)
            currentReportIndex = allReportButtons.findIndex(btn => btn.dataset.publicationId === currentPublicationId);
            console.log(`📍 Encontrado en índice: ${currentReportIndex}, mostrado como #${currentReportIndex + 1} de ${allReportButtons.length}`);
            
            const modal = document.getElementById('modalObservatorio');
            if (modal) {
                // Datos básicos
                fillBasicData(modal, this.dataset);
                
                // Datos específicos del observatorio
                const specificFields = [
                    'municipio', 'distrito', 'totalLesiones',
                    'lesionesGraves', 'lesionesModeradas', 'lesionesLeves'
                ];
                
                specificFields.forEach(field => {
                    const element = modal.querySelector(`.modal-${field}`);
                    if (element && this.dataset[field]) {
                        element.textContent = this.dataset[field];
                    }
                });
                
                // Llenar archivos y comentarios
                fillFilesAndComments(modal, this.dataset);
            }
            
            showModal('modalObservatorio');
        });
    });
    
    // Grupos Vulnerables
    document.querySelectorAll('.ver-detalle-grupos-vulnerables:not([data-report-modal-bound])').forEach(btn => {
        btn.dataset.reportModalBound = 'true';
        btn.addEventListener('click', function() {
            console.log('🎯 Click en botón Grupos Vulnerables');
            
            // Recolectar botones si no están recolectados
            if (allReportButtons.length === 0) {
                collectAllReportButtons();
            }
            
            // Guardar el ID del reporte actual
            currentPublicationId = this.dataset.publicationId;
            console.log(`🔍 Buscando publicationId: ${currentPublicationId}`);
            
            // Establecer el índice basado en data-publication-id (más robusto)
            currentReportIndex = allReportButtons.findIndex(btn => btn.dataset.publicationId === currentPublicationId);
            console.log(`📍 Encontrado en índice: ${currentReportIndex}, mostrado como #${currentReportIndex + 1} de ${allReportButtons.length}`);
            
            const modal = document.getElementById('modalGruposVulnerables');
            if (modal) {
                // Datos básicos (título, usuario, fecha, descripción, etc)
                fillBasicData(modal, this.dataset);
                
                // Datos específicos de Grupos Vulnerables (desde data attributes)
                const specificFields = ['lugar', 'promotor', 'participantes', 'actividad', 'municipio', 'distrito'];
                specificFields.forEach(field => {
                    const element = modal.querySelector(`.modal-${field}`);
                    if (element && this.dataset[field]) {
                        element.textContent = this.dataset[field];
                    }
                });
                
                // Llenar archivos y comentarios
                fillFilesAndComments(modal, this.dataset);
            }
            
            showModal('modalGruposVulnerables');
        });
    });
    
    }

    bindReportModalOpeners();

    // Función para llenar datos básicos
    function fillBasicData(modal, dataset) {
        // Datos básicos comunes
        const basicFields = {
            'modal-titulo': dataset.titulo,
            'modal-folio': dataset.folio,
            // Mostrar la fecha de la actividad directamente bajo el título (sin prefijo)
            'modal-fecha-actividad': dataset.fechaActividad || dataset.fecha,
            // La fecha de publicación se muestra en la zona superior derecha (reemplaza 'Subido por')
            'modal-fecha-publicacion': dataset.fecha,
            'modal-usuario': dataset.usuario
        };
        
        Object.entries(basicFields).forEach(([className, value]) => {
            modal.querySelectorAll(`.${className}`).forEach((element) => {
                if (!value) return;
                element.textContent = value;

                if (className === 'modal-titulo') {
                    element.setAttribute('title', value);
                }
            });
        });

        const folioMeta = modal.querySelector('.report-folio-meta');
        if (folioMeta) {
            const hasFolio = Boolean(String(dataset.folio || '').trim());
            folioMeta.classList.toggle('hidden', !hasFolio);
            folioMeta.hidden = !hasFolio;
        }

        const rawUpdatedValue = String(dataset.actualizado ?? '').trim();
        const hiddenUpdatedValues = new Set([
            '', '-', '—', 'null', 'undefined',
            'sin modificaciones', 'sin modificación', 'sin actualizaciones'
        ]);
        const hasUpdatedValue = !hiddenUpdatedValues.has(rawUpdatedValue.toLocaleLowerCase('es-MX'));

        modal.querySelectorAll('.modal-updated-meta').forEach((element) => {
            element.classList.toggle('hidden', !hasUpdatedValue);
            element.hidden = !hasUpdatedValue;
            element.style.display = hasUpdatedValue ? '' : 'none';
            const valueElement = element.querySelector('.modal-actualizado');
            if (valueElement) {
                valueElement.textContent = hasUpdatedValue ? rawUpdatedValue : '';
            }
        });

        modal.querySelectorAll('.report-meta-grid').forEach((element) => {
            element.classList.toggle('has-update', hasUpdatedValue);
        });

        const descripcion = (dataset.descripcion || '').trim();
        const hasDescription = descripcion.length > 0 && descripcion !== 'Sin descripción adicional.';
        const descripcionSection = modal.querySelector('.descripcion-section');
        const descripcionSeparator = modal.querySelector('.descripcion-separator');

        if (descripcionSection) {
            descripcionSection.style.display = hasDescription ? 'block' : 'none';
        }

        if (descripcionSeparator) {
            descripcionSeparator.style.display = hasDescription ? 'block' : 'none';
        }

        if (hasDescription) {
            modal.querySelectorAll('.modal-descripcion').forEach((element) => {
                element.textContent = descripcion;
            });
        }

        // Nota: ya no mostramos el cargo del usuario en el modal (solo nombre)

        // Llenar informacion de estado del reporte
        const statusContainer = modal.querySelector('.modal-status-container');
        if (statusContainer) {
            const status = dataset.status || 'publicado';
            let statusHTML = '';
            
            if (status === 'aprobado') {
                const approvedBy = dataset.approvedBy || 'Administrador';
                statusHTML = `<div class="status-card status-approved"><span class="status-stamp">Aprobado</span><div><strong>Aprobado</strong><span>Validado por ${escapeHtml(approvedBy)}</span></div></div>`;
            } else if (status === 'rechazado') {
                const rejectedBy = dataset.rejectedBy || 'Administrador';
                const rejectionReason = dataset.rejectionReason || 'No se proporciono motivo';
                statusHTML = `<div class="status-card status-rejected"><span class="status-stamp">Rechazado</span><div><strong>Rechazado</strong><span>Revisado por ${escapeHtml(rejectedBy)}</span></div><div class="status-reason"><span class="status-reason-label">Motivo de rechazo</span><p class="status-reason-text">${escapeHtml(rejectionReason)}</p></div></div>`;
            } else {
                statusHTML = `<div class="status-card status-pending"><span class="status-stamp">Pendiente</span><div><strong>Pendiente de revisión</strong><span>Esperando validación</span></div></div>`;
            }
            
            statusContainer.innerHTML = statusHTML;
        }

        // Configurar botones de aprobación/rechazo
        const approvalContainer = modal.querySelector('.approval-buttons-container');
        const actionsFooter = modal.querySelector('.modal-actions-footer');
        const footerNote = modal.querySelector('.modal-footer-note');

        modal.classList.remove('has-actions-footer');
        if (actionsFooter) actionsFooter.style.display = 'flex';
        if (footerNote) {
            const currentStatus = dataset.status || 'publicado';
            footerNote.textContent = currentStatus === 'aprobado'
                ? 'Reporte aprobado'
                : currentStatus === 'rechazado'
                    ? 'El autor puede corregir y reenviar el reporte'
                    : 'Reporte pendiente de revisión';
        }

        if (approvalContainer) {
            const userIsAdminOrCoord = {{ auth()->user()->isAdmin() || auth()->user()->isCoordinator() ? 'true' : 'false' }};
            const status = dataset.status || 'publicado';
            const publicationId = dataset.publicationId;
            const isOwner = dataset.isOwner === 'true';
            
            // Limpiar contenido previo del contenedor
            approvalContainer.innerHTML = '';
            approvalContainer.style.display = 'none';
            
            // Si es rechazado y el usuario es el autor, mostrar botón de reenvío
            if (status === 'rechazado' && isOwner) {
                if (actionsFooter) {
                    actionsFooter.style.display = 'flex';
                }
                modal.classList.add('has-actions-footer');
                approvalContainer.style.display = 'flex';
                approvalContainer.innerHTML = `
                    <button onclick="resubmitReport(${publicationId})" class="reenviar-reporte">
                        Reenviar para revisión
                    </button>
                `;
            }
            // Si es pendiente o rechazado y el usuario es Admin/Coordinador, mostrar botones de aprobar/rechazar
            else if (userIsAdminOrCoord && status !== 'aprobado') {
                if (actionsFooter) {
                    actionsFooter.style.display = 'flex';
                }
                modal.classList.add('has-actions-footer');
                approvalContainer.style.display = 'flex';
                approvalContainer.innerHTML = `
                    <div class="approval-button-row">
                        <button class="rechazar-reporte">
                            Rechazar
                        </button>
                        <button class="aprobar-reporte">
                            Aprobar
                        </button>
                    </div>
                `;
                
                // Re-attach event handlers
                const newAprobarBtn = approvalContainer.querySelector('.aprobar-reporte');
                const newRechazarBtn = approvalContainer.querySelector('.rechazar-reporte');
                
                if (newAprobarBtn) {
                    newAprobarBtn.onclick = function() {
                        approveReport(publicationId);
                    };
                }
                
                if (newRechazarBtn) {
                    newRechazarBtn.onclick = function() {
                        const titulo = dataset.titulo || 'este reporte';
                        showRejectModal(publicationId, titulo);
                    };
                }
            } else {
                approvalContainer.style.display = 'none';
            }
        }
    }
    
    // Función para llenar archivos y comentarios
    function getCommentsCounterLabel(modal, count) {
        return modal?.dataset.reportType === 'alcoholimetria'
            ? `Comentarios · ${count}`
            : `Comentarios (${count})`;
    }

    function fillFilesAndComments(modal, dataset) {
        // Archivos adjuntos
        const archivosContainer = modal.querySelector('.modal-archivos');
        if (archivosContainer && dataset.archivos) {
            try {
                const archivos = JSON.parse(dataset.archivos);
                archivosContainer.innerHTML = '';
                const filesCount = modal.querySelector('.modal-archivos-count');
                if (filesCount) filesCount.textContent = archivos.length;
                const btnDescargarTodos = modal.querySelector('.descargar-todos-archivos');
                if (btnDescargarTodos) btnDescargarTodos.hidden = archivos.length === 0;

                if (archivos.length === 0) {
                    archivosContainer.innerHTML = '<p class="report-empty-state">Sin archivos adjuntos</p>';
                }
                
                // Guardar lista de archivos en el modal para el botón "Descargar Todos"
                modal.dataset.archivosJson = dataset.archivos;

                const previewFiles = archivos.map((archivo) => {
                    const fileName = archivo.name || archivo;
                    const fileId = archivo.id || null;
                    const extension = fileName.split('.').pop().toLowerCase();

                    return {
                        id: fileId,
                        name: fileName,
                        extension,
                        publicUrl: archivo.public_url || null,
                        canPreview: fileId && ['pdf', 'jpg', 'jpeg', 'png', 'xlsx', 'xls'].includes(extension),
                    };
                });
                
                previewFiles.forEach((archivo, index) => {
                    const fileName = archivo.name;
                    const fileId = archivo.id;
                    const extension = archivo.extension;
                    const { icono, color } = obtenerEstiloArchivo(extension);
                    const canPreview = archivo.canPreview;
                    const safeFileName = escapeHtml(fileName);
                    const fileSize = obtenerTamañoAleatorio();
                    const thumbHtml = getAttachmentThumbHtml(archivo, icono, color, safeFileName);
                    const previewTitle = canPreview ? 'Previsualizar archivo' : 'Previsualizacion no disponible';
                    
                    archivosContainer.innerHTML += `
                        <div class="archivo-preview-card ${canPreview ? 'cursor-pointer' : ''}" data-file-id="${fileId}" data-file-name="${safeFileName}" data-extension="${extension}" data-file-index="${index}" title="${canPreview ? 'Abrir previsualizacion' : 'Previsualizacion no disponible'}" onclick="if (!event.target.closest('.archivo-action')) window.openArchivoPreviewFromCard(this, event)">
                            <div class="archivo-thumb" data-thumbnail-index="${index}">
                                ${thumbHtml}
                            </div>
                            <div class="archivo-actions" aria-label="Acciones del archivo">
                                <button type="button" class="archivo-action archivo-view" title="${previewTitle}" aria-label="${previewTitle}" ${canPreview ? '' : 'disabled'}>
                                    <i class="fas fa-eye text-xs"></i>
                                </button>
                                <button type="button" class="archivo-action descargar-archivo" title="Descargar" aria-label="Descargar" data-file-id="${fileId}">
                                    <i class="fas fa-download text-xs"></i>
                                </button>
                                <button type="button" class="archivo-action archivo-info" title="Datos del archivo" aria-label="Datos del archivo">
                                    <i class="fas fa-info text-xs"></i>
                                </button>
                                <div class="archivo-info-popover font-lora">
                                    <div><strong>Nombre:</strong> ${safeFileName}</div>
                                    <div><strong>Tipo:</strong> ${extension.toUpperCase()}</div>
                                    <div><strong>Tamano:</strong> ${fileSize}</div>
                                    <div><strong>Vista previa:</strong> ${canPreview ? 'Disponible' : 'No disponible'}</div>
                                </div>
                            </div>
                            <div class="archivo-meta">
                                <p class="archivo-nombre" title="${safeFileName}">
                                    ${safeFileName}
                                </p>
                                <p class="archivo-detalle">
                                    ${extension.toUpperCase()} - ${fileSize}
                                </p>
                            </div>
                        </div>
                    `;
                });
                
                // Configurar botón "Descargar Todos" si existe
                archivosContainer.dataset.previewFiles = JSON.stringify(previewFiles);
                schedulePreviewPreload(previewFiles);

                archivosContainer.querySelectorAll('.archivo-preview-card').forEach((card) => {
                    const preloadFromCard = () => {
                        const fileIndex = Number(card.dataset.fileIndex || 0);
                        preloadArchivoPreview(previewFiles[fileIndex]);
                    };

                    card.addEventListener('mouseenter', preloadFromCard, { once: true });
                    card.addEventListener('focusin', preloadFromCard, { once: true });

                    card.addEventListener('click', function(event) {
                        if (event.target.closest('.archivo-action') || event.target.closest('.descargar-archivo')) {
                            return;
                        }

                        event.preventDefault();
                        window.openArchivoPreviewFromCard(this, event);
                    });
                });

                hydrateAttachmentThumbnails(archivosContainer, previewFiles);

                if (btnDescargarTodos) {
                    btnDescargarTodos.replaceWith(btnDescargarTodos.cloneNode(true));
                    const newBtn = modal.querySelector('.descargar-todos-archivos');
                    newBtn.addEventListener('click', function() {
                        window.location.href = `/reportes/${dataset.publicationId}/download-all`;
                    });
                }
            } catch (e) {
                console.error('Error parsing archivos:', e);
            }
        }
        
        // Comentarios
        const comentariosContainer = modal.querySelector('.modal-comentarios');
        const comentariosToggle = modal.querySelector('.comentarios-toggle');
        const comentariosContainerDiv = modal.querySelector('.comentarios-container');
        
        if (comentariosContainer && dataset.comentarios) {
            try {
                const comentarios = JSON.parse(dataset.comentarios);
                
                // Guardar comentarios originales en el modal para uso posterior
                modal.dataset.comentariosJson = dataset.comentarios;
                
                renderComments(comentariosContainer, comentarios);
                
                // Mostrar el botón de toggle y actualizar contador
                if (comentariosToggle) {
                    comentariosToggle.style.display = 'flex';
                    const contadorSpan = comentariosToggle.querySelector('.contador-comentarios');
                    const icono = comentariosToggle.querySelector('.icono-chevron');
                    const isAlcoholReport = modal.dataset.reportType === 'alcoholimetria';
                    const shouldExpandComments = isAlcoholReport || comentarios.length > 0;

                    if (contadorSpan) {
                        contadorSpan.textContent = getCommentsCounterLabel(modal, comentarios.length);
                    }

                    if (comentariosContainerDiv) {
                        comentariosContainerDiv.style.transition = 'none';
                        comentariosContainerDiv.classList.toggle('expanded', shouldExpandComments);
                        comentariosContainerDiv.offsetHeight;
                        requestAnimationFrame(() => {
                            comentariosContainerDiv.style.transition = '';
                        });
                    }

                    if (icono) {
                        icono.style.transform = shouldExpandComments ? 'rotate(180deg)' : 'rotate(0deg)';
                    }
                    comentariosToggle.setAttribute('aria-expanded', shouldExpandComments ? 'true' : 'false');
                    
                    // Agregar evento de click al toggle
                    comentariosToggle.onclick = function(e) {
                        e.preventDefault();
                        const isExpanded = comentariosContainerDiv.classList.contains('expanded');
                        
                        if (isExpanded) {
                            comentariosContainerDiv.classList.remove('expanded');
                        } else {
                            comentariosContainerDiv.classList.add('expanded');
                        }
                        comentariosToggle.setAttribute('aria-expanded', isExpanded ? 'false' : 'true');
                        
                        // Rotar el icono
                        const icono = comentariosToggle.querySelector('.icono-chevron');
                        if (icono) {
                            icono.style.transform = isExpanded ? 'rotate(0deg)' : 'rotate(180deg)';
                        }
                    };
                }

                // Marcar como vistos los comentarios (si aplica) y actualizar la UI
                fetch(`/reportes/${dataset.publicationId}/comentarios/mark-seen`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    }
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success && Array.isArray(data.updated_ids) && data.updated_ids.length > 0) {
                        // Marcar en la estructura local y re-renderizar (per-user flag)
                        comentarios.forEach(c => {
                            if (data.updated_ids.includes(c.id)) c.seen_by_current_user = true;
                        });
                        
                        // Actualizar también el dataset
                        modal.dataset.comentariosJson = JSON.stringify(comentarios);
                        
                        renderComments(comentariosContainer, comentarios);

                        // Also hide the unread dot on the publication card for this user
                        try {
                            const card = document.querySelector(`.publication-card[data-publication-id="${dataset.publicationId}"]`);
                            if (card) {
                                const dot = card.querySelector('.absolute.-top-0\.5.-right-0\.5');
                                if (dot) dot.remove();
                            }
                        } catch (e) {
                            // ignore
                        }
                    }
                })
                .catch(err => console.error('Error marcando comentarios como vistos:', err));
            } catch (e) {
                console.error('Error parsing comentarios:', e);
            }
        }

        // Guardar el publication ID en el modal para envío de comentarios
        modal.dataset.publicationId = dataset.publicationId;

        // Mostrar/ocultar formulario de comentarios según permisos
        const comentarioForm = modal.querySelector('.comentario-form-container');
        const comentarioNoPermisos = modal.querySelector('.comentario-no-permisos');
        const isOwner = dataset.isOwner === 'true';
        const userRole = '{{ auth()->user()->role->name ?? "" }}';
        
        // Admin/Coordinador siempre pueden comentar, Operador solo en sus propias publicaciones
        if (comentarioForm && comentarioNoPermisos) {
            if (userRole === 'Administrador' || userRole === 'Coordinador' || (userRole === 'Operador' && isOwner)) {
                comentarioForm.style.display = 'block';
                comentarioNoPermisos.style.display = 'none';
            } else {
                comentarioForm.style.display = 'none';
                comentarioNoPermisos.style.display = 'block';
            }
        }
    }

    // Función para renderizar comentarios
    function renderComments(container, comentarios) {
        container.innerHTML = '';
        
        if (comentarios.length > 0) {
                comentarios.forEach(comentario => {
                    const userName = comentario.user?.name || 'Usuario';
                    const userDistrict = comentario.user?.district || 'Sin distrito';
                    const userMeta = userDistrict;
                    const userPhotoUrl = comentario.user?.profile_photo_url || '{{ asset('images/default_pfp.svg.png') }}';
                    const avatarHtml = `<img src="${escapeHtml(userPhotoUrl)}" alt="Foto de ${escapeHtml(userName)}" class="w-8 h-8 rounded-full object-cover border border-slate-200 shadow-sm flex-shrink-0">`;

                    // Prefer an ISO timestamp from the server and format it in the user's local timezone in the browser.
                    let dateStr = comentario.date || '';
                    let timeStr = comentario.time || '';
                    if (comentario.created_at_iso) {
                        try {
                            const dt = new Date(comentario.created_at_iso);
                            if (!isNaN(dt.getTime())) {
                                const d = String(dt.getDate()).padStart(2, '0');
                                const m = String(dt.getMonth() + 1).padStart(2, '0');
                                const y = dt.getFullYear();
                                const hh = String(dt.getHours()).padStart(2, '0');
                                const mm = String(dt.getMinutes()).padStart(2, '0');
                                dateStr = `${d}/${m}/${y}`;
                                timeStr = `${hh}:${mm}`;
                            }
                        } catch (e) {
                            // fallback to server-provided date/time
                        }
                    }

                    const tickHtml = (comentario.user && comentario.user.id == CURRENT_USER_ID)
                        ? (comentario.seen_by_current_user ? '<i class="fas fa-check-double text-blue-500" title="Visto"></i>' : '<i class="fas fa-check text-gray-400" title="Enviado"></i>')
                        : '';

                    container.innerHTML += `
                        <div class="comentario-item relative border rounded-xl px-3 py-2 transition-all duration-200" data-comment-id="${comentario.id}" data-user-id="${comentario.user?.id}">
                            <div class="flex gap-2.5">
                                ${avatarHtml}
                                <div class="flex-1 min-w-0">
                                    <div class="flex justify-between items-start gap-2">
                                        <div class="min-w-0 flex items-baseline gap-2 leading-tight">
                                            <span class="font-semibold text-[#404041] font-lora text-sm truncate">
                                                ${escapeHtml(userName)}
                                            </span>
                                            <span class="text-xs text-gray-500 font-lora truncate">
                                                ${escapeHtml(userMeta)}
                                            </span>
                                        </div>
                                        <div class="text-[11px] text-gray-500 font-lora whitespace-nowrap text-right pt-0.5">
                                            ${escapeHtml(dateStr)} · ${escapeHtml(timeStr)}
                                        </div>
                                    </div>
                                    <p class="m-0 mt-2.5 pr-6 text-gray-800 text-sm leading-snug break-words whitespace-pre-line font-lora min-w-0">${escapeHtml(comentario.comment || '')}</p>
                                </div>
                            </div>
                            <span class="absolute right-3 bottom-2 text-xs leading-none">${tickHtml}</span>
                        </div>
                    `;
            });
        } else {
            const isAlcoholReport = container.closest('.publication-detail-modal')?.dataset.reportType === 'alcoholimetria';
            container.innerHTML = isAlcoholReport
                ? '<p class="report-comments-empty">Aún no hay comentarios en este expediente.</p>'
                : `
                    <div class="text-center py-8 text-gray-500 font-lora">
                        <i class="fas fa-comments text-3xl mb-3 text-gray-300"></i>
                        <p class="text-sm">No hay comentarios aún</p>
                    </div>
                `;
        }
    }

    const archivoPreviewState = {
        files: [],
        index: 0,
        renderToken: 0,
        previousFocus: null,
    };
    let pdfJsLoadingPromise = null;
    let archivoPreviewCloseTimer = null;
    const archivoPreviewCache = {
        pdf: new Map(),
        pdfLoading: new Map(),
        spreadsheetFrames: new Map(),
    };

    function openArchivoPreview(files, index) {
        const supportedFiles = files.filter((file) => file.canPreview);
        const selected = files[index];
        const selectedId = selected ? selected.id : null;
        const selectedIndex = supportedFiles.findIndex((file) => String(file.id) === String(selectedId));

        if (!selected || !selected.canPreview || selectedIndex < 0) {
            showToast('Este archivo no tiene previsualización disponible.', 'warning', 3000);
            return;
        }

        archivoPreviewState.files = supportedFiles;
        archivoPreviewState.index = selectedIndex;
        renderArchivoPreviewOverlay();

        const overlay = document.getElementById('archivo-preview-overlay');
        if (overlay) {
            if (archivoPreviewCloseTimer) {
                clearTimeout(archivoPreviewCloseTimer);
                archivoPreviewCloseTimer = null;
            }

            overlay.classList.remove('archivo-preview-closing');
            overlay.classList.remove('hidden');
            overlay.style.display = 'block';
            overlay.setAttribute('aria-hidden', 'false');
            archivoPreviewState.previousFocus = document.activeElement;
            document.body.style.overflow = 'hidden';
            requestAnimationFrame(() => document.getElementById('archivo-preview-close')?.focus());
        }

        document.getElementById('report-nav-container')?.classList.add('hidden');
    }

    window.openArchivoPreviewFromCard = function(card, event) {
        if (event && event.target && event.target.closest('.descargar-archivo')) {
            return;
        }

        const container = card.closest('.modal-archivos');
        const fileIndex = Number(card.dataset.fileIndex || 0);

        try {
            const files = JSON.parse((container && container.dataset.previewFiles) ? container.dataset.previewFiles : '[]');
            openArchivoPreview(files, fileIndex);
        } catch (error) {
            console.error('Error abriendo previsualizacion:', error);
            showToast('No se pudo abrir la vista previa.', 'error', 4000, {
                description: 'Actualiza la página e inténtalo nuevamente.'
            });
        }
    };

    function closeArchivoPreview() {
        const overlay = document.getElementById('archivo-preview-overlay');
        const content = document.getElementById('archivo-preview-content');

        archivoPreviewState.renderToken++;

        if (!overlay || overlay.classList.contains('hidden')) return;
        if (overlay.classList.contains('archivo-preview-closing')) return;

        overlay.classList.add('archivo-preview-closing');

        archivoPreviewCloseTimer = setTimeout(() => {
            if (content) {
                preserveActiveSpreadsheetFrame(content);
                content.innerHTML = '';
            }

            overlay.classList.add('hidden');
            overlay.style.display = 'none';
            overlay.setAttribute('aria-hidden', 'true');
            overlay.classList.remove('archivo-preview-closing');
            archivoPreviewCloseTimer = null;

            const anyModalOpen = document.querySelector('[id^="modal"]:not(.hidden)');
            if (!anyModalOpen) {
                document.body.style.overflow = 'auto';
            } else {
                updateNavigationArrows();
            }

            if (archivoPreviewState.previousFocus instanceof HTMLElement && document.contains(archivoPreviewState.previousFocus)) {
                archivoPreviewState.previousFocus.focus();
            }
            archivoPreviewState.previousFocus = null;
        }, window.matchMedia('(prefers-reduced-motion: reduce)').matches ? 0 : 280);
    }

    function navigateArchivoPreview(direction) {
        if (!archivoPreviewState.files.length) return;

        archivoPreviewState.index = (archivoPreviewState.index + direction + archivoPreviewState.files.length) % archivoPreviewState.files.length;
        renderArchivoPreviewOverlay();
    }

    function getPreviewUrl(file) {
        return `/reportes/file/${file.id}/preview`;
    }

    function getPdfUrl(file) {
        return file.publicUrl || getPreviewUrl(file);
    }

    function getFileCacheKey(file) {
        return `${file.extension}:${file.id}:${file.name}`;
    }

    function getAttachmentThumbHtml(file, icono, color, safeFileName) {
        if (file.canPreview && ['jpg', 'jpeg', 'png'].includes(file.extension)) {
            return `<img src="${getPreviewUrl(file)}" alt="Vista previa de ${safeFileName}" loading="lazy">`;
        }

        if (file.canPreview && file.extension === 'pdf') {
            return `
                <div class="archivo-thumb-fallback archivo-pdf-thumb" data-pdf-thumb="true">
                    <i class="${icono} text-red-600"></i>
                </div>
            `;
        }

        if (['xlsx', 'xls'].includes(file.extension)) {
            const cells = Array.from({ length: 25 }, () => '<span></span>').join('');
            return `
                <div class="archivo-sheet-thumb" aria-hidden="true">
                    <div class="archivo-sheet-bar"></div>
                    <div class="archivo-sheet-grid">${cells}</div>
                </div>
            `;
        }

        return `
            <div class="archivo-thumb-fallback ${color}">
                <i class="${icono} text-white"></i>
            </div>
        `;
    }

    function hydrateAttachmentThumbnails(container, files) {
        if (!container || !Array.isArray(files)) return;

        container.querySelectorAll('[data-thumbnail-index]').forEach((thumb) => {
            const index = Number(thumb.dataset.thumbnailIndex || 0);
            const file = files[index];

            if (!file || file.extension !== 'pdf' || !file.canPreview) return;
            renderPdfAttachmentThumbnail(file, thumb);
        });
    }

    async function renderPdfAttachmentThumbnail(file, thumb) {
        try {
            const pdfjsLib = await ensurePdfJsLoaded();
            const pdf = await pdfjsLib.getDocument({ url: encodeURI(getPdfUrl(file)), withCredentials: true }).promise;
            const page = await pdf.getPage(1);
            const viewport = page.getViewport({ scale: 0.42 });
            const canvas = document.createElement('canvas');
            const context = canvas.getContext('2d');

            canvas.width = Math.floor(viewport.width);
            canvas.height = Math.floor(viewport.height);
            await page.render({ canvasContext: context, viewport }).promise;

            thumb.innerHTML = '';
            thumb.appendChild(canvas);
        } catch (error) {
            console.warn('No se pudo generar miniatura PDF:', error);
        }
    }

    function preRenderPdfToCache(file) {
        if (!file || archivoPreviewCache.pdfLoading.has(getFileCacheKey(file))) return;

        const key = getFileCacheKey(file);
        const task = renderPdfAttachmentThumbnail(file, document.createElement('div')).catch(() => {});
        archivoPreviewCache.pdfLoading.set(key, task);
    }

    function getPreviewCacheHost() {
        let host = document.getElementById('archivo-preview-cache-host');
        if (!host) {
            host = document.createElement('div');
            host.id = 'archivo-preview-cache-host';
            host.setAttribute('aria-hidden', 'true');
            host.style.cssText = 'position:fixed;left:-10000px;top:-10000px;width:1px;height:1px;overflow:hidden;opacity:0;pointer-events:none;';
            document.body.appendChild(host);
        }
        return host;
    }

    function schedulePreviewPreload(files) {
        const supported = files.filter((file) => file.canPreview && ['pdf', 'xlsx', 'xls', 'jpg', 'jpeg', 'png'].includes(file.extension));
        if (!supported.length) return;

        window.setTimeout(() => {
            supported.slice(0, 2).forEach((file) => preloadArchivoPreview(file));
        }, 450);
    }

    function preloadArchivoPreview(file) {
        if (!file || !file.canPreview) return;

        if (['jpg', 'jpeg', 'png'].includes(file.extension)) {
            const image = new Image();
            image.src = getPreviewUrl(file);
            return;
        }

        if (file.extension === 'pdf') {
            preRenderPdfToCache(file);
            return;
        }

        if (['xlsx', 'xls'].includes(file.extension)) {
            ensureSpreadsheetFrame(file, true);
        }
    }

    function renderArchivoPreviewOverlay() {
        const file = archivoPreviewState.files[archivoPreviewState.index];
        if (!file) return;

        const content = document.getElementById('archivo-preview-content');
        const fileNameEl = document.getElementById('archivo-preview-file-name');
        const fileTypeEl = document.getElementById('archivo-preview-file-type');
        const openLink = document.getElementById('archivo-preview-open');
        const downloadLink = document.getElementById('archivo-preview-download');
        const prevButton = document.getElementById('archivo-preview-prev');
        const nextButton = document.getElementById('archivo-preview-next');

        if (!content) return;
        const renderToken = ++archivoPreviewState.renderToken;

        const extension = file.extension;
        const previewUrl = getPreviewUrl(file);
        const displayUrl = extension === 'pdf' && file.publicUrl ? file.publicUrl : previewUrl;
        const downloadUrl = `/reportes/file/${file.id}/download`;
        if (fileNameEl) fileNameEl.textContent = file.name;
        if (fileTypeEl) fileTypeEl.textContent = `${extension.toUpperCase()} · ${archivoPreviewState.index + 1} de ${archivoPreviewState.files.length}`;
        if (openLink) openLink.href = displayUrl;
        if (downloadLink) downloadLink.href = downloadUrl;

        const hasMultiple = archivoPreviewState.files.length > 1;
        if (prevButton) prevButton.classList.toggle('hidden', !hasMultiple);
        if (nextButton) nextButton.classList.toggle('hidden', !hasMultiple);

        const loadingHtml = `
            <div class="archivo-preview-loader hidden absolute inset-0 z-10 flex items-center justify-center bg-white">
                <div class="archivo-preview-spinner" aria-hidden="true"></div>
            </div>
        `;

        if (['jpg', 'jpeg', 'png'].includes(extension)) {
            content.className = 'h-[calc(100dvh-4rem)] overflow-auto flex items-center justify-center py-3 px-14 sm:px-16 md:px-20 lg:px-24';
            content.innerHTML = `
                <img src="${previewUrl}" alt="${escapeHtml(file.name)}" class="archivo-preview-surface max-w-full max-h-full object-contain shadow-2xl bg-white">
            `;
            return;
        }

        if (extension === 'pdf') {
            const pdfUrl = getPdfUrl(file);
            renderPdfPreview(file, pdfUrl, loadingHtml, renderToken);
            return;
        }

        if (['xlsx', 'xls'].includes(extension)) {
            content.className = 'h-[calc(100dvh-4rem)] overflow-auto flex items-start justify-center py-2 px-12 sm:px-14 md:px-16';
            content.innerHTML = `
                <div class="archivo-preview-surface relative w-full max-w-[86vw] h-[calc(100dvh-6rem)] bg-white shadow-2xl">
                    ${loadingHtml}
                </div>
            `;
            renderSpreadsheetPreview(file, content, renderToken);
            return;
        }

        content.className = 'h-[calc(100dvh-4rem)] overflow-auto flex items-center justify-center p-3 md:p-5';
            content.innerHTML = `
                <div class="archivo-preview-surface text-center text-white/70">
                    <i class="fas fa-file-alt text-4xl mb-3"></i>
                    <p>Este tipo de archivo no tiene previsualizacion disponible.</p>
                </div>
        `;
    }

    function ensurePdfJsLoaded() {
        if (window.pdfjsLib) {
            return Promise.resolve(window.pdfjsLib);
        }

        if (!pdfJsLoadingPromise) {
            pdfJsLoadingPromise = new Promise((resolve, reject) => {
                const script = document.createElement('script');
                script.src = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js';
                script.onload = () => {
                    if (!window.pdfjsLib) {
                        reject(new Error('PDF.js no esta disponible'));
                        return;
                    }

                    window.pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';
                    resolve(window.pdfjsLib);
                };
                script.onerror = () => reject(new Error('No se pudo cargar PDF.js'));
                document.head.appendChild(script);
            });
        }

        return pdfJsLoadingPromise;
    }

    function showPreviewLoaderIfStillLoading(container, renderToken, delay = 220) {
        window.setTimeout(() => {
            if (renderToken !== archivoPreviewState.renderToken) return;

            const loader = container.querySelector('.archivo-preview-loader');
            if (loader) {
                loader.classList.remove('hidden');
            }
        }, delay);
    }

    function ensureSpreadsheetFrame(file, preloadOnly = false) {
        const key = getFileCacheKey(file);
        let entry = archivoPreviewCache.spreadsheetFrames.get(key);

        if (!entry) {
            const iframe = document.createElement('iframe');
            iframe.src = `${getPreviewUrl(file)}?embed=1`;
            iframe.title = file.name;
            iframe.dataset.previewCacheKey = key;
            iframe.className = 'w-full h-full border-0 bg-white opacity-0 transition-opacity duration-200';

            entry = { iframe, loaded: false };
            iframe.addEventListener('load', () => {
                entry.loaded = true;
                iframe.classList.remove('opacity-0');
                const loader = iframe.parentElement?.querySelector('.archivo-preview-loader');
                if (loader) loader.remove();
            });

            archivoPreviewCache.spreadsheetFrames.set(key, entry);
            getPreviewCacheHost().appendChild(iframe);
        }

        if (preloadOnly && !entry.iframe.parentElement) {
            getPreviewCacheHost().appendChild(entry.iframe);
        }

        return entry;
    }

    function renderSpreadsheetPreview(file, content, renderToken) {
        const surface = content.querySelector('.archivo-preview-surface');
        if (!surface) return;

        const entry = ensureSpreadsheetFrame(file);
        if (renderToken !== archivoPreviewState.renderToken) return;

        entry.iframe.className = `w-full h-full border-0 bg-white transition-opacity duration-200 ${entry.loaded ? '' : 'opacity-0'}`;
        surface.appendChild(entry.iframe);

        if (entry.loaded) {
            const loader = surface.querySelector('.archivo-preview-loader');
            if (loader) loader.remove();
        } else {
            showPreviewLoaderIfStillLoading(content, renderToken);
        }
    }

    function preserveActiveSpreadsheetFrame(content) {
        const iframe = content.querySelector('iframe[data-preview-cache-key]');
        if (!iframe) return;

        getPreviewCacheHost().appendChild(iframe);
    }

    async function renderPdfPreview(file, pdfUrl, loadingHtml, renderToken) {
        const content = document.getElementById('archivo-preview-content');
        if (!content) return;

        const safeName = escapeHtml(file.name);
        const sourceUrl = encodeURI(pdfUrl);

        content.className = 'h-[calc(100vh-5rem)] overflow-auto flex items-start justify-center p-4 md:p-8';
        content.innerHTML = `
            <div id="pdf-preview-frame" class="relative w-full max-w-5xl min-h-[calc(100vh-9rem)]">
                ${loadingHtml}
                <div id="pdf-preview-pages" class="flex flex-col items-center gap-5" aria-label="Previsualizacion PDF: ${safeName}"></div>
            </div>
        `;
        showPreviewLoaderIfStillLoading(content, renderToken);

        try {
            const pdfjsLib = await ensurePdfJsLoaded();
            if (renderToken !== archivoPreviewState.renderToken) return;

            const loadingTask = pdfjsLib.getDocument({ url: sourceUrl, withCredentials: true });
            const pdf = await loadingTask.promise;
            if (renderToken !== archivoPreviewState.renderToken) return;

            const frame = document.getElementById('pdf-preview-frame');
            const pagesContainer = document.getElementById('pdf-preview-pages');
            if (!frame || !pagesContainer) return;

            const firstPage = await pdf.getPage(1);
            const baseViewport = firstPage.getViewport({ scale: 1 });
            const availableWidth = Math.max(320, Math.min(frame.clientWidth - 16, 930));
            const scale = Math.max(0.8, Math.min(1.65, availableWidth / baseViewport.width));
            const outputScale = Math.min(window.devicePixelRatio || 1, 2);

            for (let pageNumber = 1; pageNumber <= pdf.numPages; pageNumber++) {
                if (renderToken !== archivoPreviewState.renderToken) return;

                const page = pageNumber === 1 ? firstPage : await pdf.getPage(pageNumber);
                const viewport = page.getViewport({ scale });
                const canvas = document.createElement('canvas');
                const context = canvas.getContext('2d');
                const pageWrap = document.createElement('div');

                canvas.width = Math.floor(viewport.width * outputScale);
                canvas.height = Math.floor(viewport.height * outputScale);
                canvas.style.width = `${Math.floor(viewport.width)}px`;
                canvas.style.height = `${Math.floor(viewport.height)}px`;
                canvas.className = 'block bg-white shadow-2xl';

                pageWrap.className = 'archivo-preview-surface bg-white';
                pageWrap.appendChild(canvas);

                await page.render({
                    canvasContext: context,
                    viewport,
                    transform: outputScale !== 1 ? [outputScale, 0, 0, outputScale, 0, 0] : null,
                }).promise;

                if (renderToken !== archivoPreviewState.renderToken) return;

                pagesContainer.appendChild(pageWrap);

                if (pageNumber === 1) {
                    const loader = frame.querySelector('.absolute.inset-0');
                    if (loader) loader.remove();
                }
            }
        } catch (error) {
            if (renderToken !== archivoPreviewState.renderToken) return;

            content.innerHTML = `
                <div class="text-center text-white/80 font-lora max-w-md">
                    <i class="fas fa-file-pdf text-5xl mb-4"></i>
                    <p class="mb-4">No se pudo cargar la previsualizacion del PDF en el visor interno.</p>
                    <a href="${sourceUrl}" target="_blank" rel="noopener" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-white text-[#2f2f2f] font-semibold">
                        <i class="fas fa-up-right-from-square"></i>
                        Abrir en otra pestana
                    </a>
                </div>
            `;
        }
    }

    function escapeHtml(value) {
        return String(value ?? '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    function bindArchivoPreviewControls() {
        const overlay = document.getElementById('archivo-preview-overlay');
        if (!overlay || overlay.dataset.previewControlsBound === 'true') return;

        overlay.dataset.previewControlsBound = 'true';
        overlay.querySelector('#archivo-preview-close')?.addEventListener('click', closeArchivoPreview);
        overlay.querySelector('#archivo-preview-prev')?.addEventListener('click', () => navigateArchivoPreview(-1));
        overlay.querySelector('#archivo-preview-next')?.addEventListener('click', () => navigateArchivoPreview(1));

        overlay.addEventListener('click', function(e) {
            const isControlClick = e.target.closest(
                '#archivo-preview-header, #archivo-preview-prev, #archivo-preview-next, #archivo-preview-close, #archivo-preview-open, #archivo-preview-download'
            );
            const isFileSurfaceClick = e.target.closest('.archivo-preview-surface');
            const isPreviewContentClick = e.target.closest('#archivo-preview-content');

            if (!isControlClick && !isFileSurfaceClick && (e.target === this || isPreviewContentClick)) {
                closeArchivoPreview();
            }
        });
    }

    bindArchivoPreviewControls();

    document.addEventListener('keydown', function(e) {
        const overlay = document.getElementById('archivo-preview-overlay');
        const isPreviewOpen = overlay && !overlay.classList.contains('hidden');

        if (!isPreviewOpen) return;

        if (e.key === 'Escape') {
            e.preventDefault();
            e.stopImmediatePropagation();
            closeArchivoPreview();
        } else if (e.key === 'ArrowLeft') {
            e.preventDefault();
            e.stopImmediatePropagation();
            navigateArchivoPreview(-1);
        } else if (e.key === 'ArrowRight') {
            e.preventDefault();
            e.stopImmediatePropagation();
            navigateArchivoPreview(1);
        }
    }, true);

    window.addEventListener('message', function(event) {
        if (event.origin !== window.location.origin) return;
        if (event.data && event.data.type === 'close-file-preview') {
            closeArchivoPreview();
        }
    });
    
    // Funciones auxiliares
    function obtenerEstiloArchivo(extension) {
        const estilos = {
            'pdf': { icono: 'fas fa-file-pdf', color: 'bg-red-500' },
            'xlsx': { icono: 'fas fa-file-excel', color: 'bg-green-500' },
            'jpg': { icono: 'fas fa-file-image', color: 'bg-purple-500' },
            'jpeg': { icono: 'fas fa-file-image', color: 'bg-purple-500' },
            'png': { icono: 'fas fa-file-image', color: 'bg-purple-500' },
            'doc': { icono: 'fas fa-file-word', color: 'bg-blue-500' },
            'docx': { icono: 'fas fa-file-word', color: 'bg-blue-500' },
            'zip': { icono: 'fas fa-file-archive', color: 'bg-yellow-500' },
            'default': { icono: 'fas fa-file', color: 'bg-gray-500' }
        };
        
        return estilos[extension] || estilos.default;
    }
    
    function obtenerTamañoAleatorio() {
        const tamanios = ['2.1 MB', '1.5 MB', '3.2 MB', '856 KB', '4.7 MB'];
        return tamanios[Math.floor(Math.random() * tamanios.length)];
    }
    
    console.log('=== SISTEMA DE MODALES INICIALIZADO ===');
    
    // Función utilitaria para abrir un modal de publicación y navegar al comentario
    window.openPublicationFromNotification = function(publicationId, commentId) {
        try {
            const btn = document.querySelector(`button[data-publication-id="${publicationId}"]`);
            if (btn) {
                // Click the existing button to fill and show the modal
                btn.click();

                // Wait for modal to render comments and then scroll to the comment
                let tries = 0;
                const iv = setInterval(() => {
                    tries += 1;
                    // Find currently visible modal (not hidden)
                    const modal = Array.from(document.querySelectorAll('[id^="modal"]')).find(m => !m.classList.contains('hidden'));
                    if (modal) {
                        const commentEl = modal.querySelector(`[data-comment-id="${commentId}"]`);
                        if (commentEl) {
                            commentEl.scrollIntoView({ behavior: 'smooth', block: 'center' });
                            commentEl.style.transition = 'box-shadow 0.3s ease';
                            commentEl.style.boxShadow = '0 0 0 3px rgba(59,130,246,0.25)';
                            setTimeout(() => { commentEl.style.boxShadow = 'none'; }, 3500);
                            clearInterval(iv);
                        }
                    }
                    if (tries > 30) clearInterval(iv);
                }, 200);
                return;
            }
        } catch (e) {
            console.error('openPublicationFromNotification error:', e);
        }

        // Fallback: cargar la pagina de publicaciones sin refrescar todo el layout.
        const fallbackUrl = '/reportes/publicaciones?publication=' + publicationId + (commentId ? ('&comment=' + commentId) : '');
        if (isReportesIndexUrl(fallbackUrl)) {
            loadReportesPanel(fallbackUrl, { push: true, scroll: false }).then(() => {
                const refreshedBtn = document.querySelector(`button[data-publication-id="${publicationId}"]`);
                if (refreshedBtn) refreshedBtn.click();
            });
            return;
        }

        window.location.href = fallbackUrl;
    }
    
    // === SISTEMA DE FILTRADO POR PESTAÑAS (SERVER-SIDE) ===
    
    // Función para actualizar estilos de pestañas activas
    function updateActiveTab(activeButton) {
        const tabButtons = document.querySelectorAll('.tab-filter');
        tabButtons.forEach(btn => {
            const isActive = btn === activeButton;
            btn.classList.toggle('is-active', isActive);
            btn.setAttribute('aria-pressed', isActive ? 'true' : 'false');
        });
    }
    
    initializeReportesDynamicArea();
    
    console.log('=== SISTEMA DE FILTRADO SERVER-SIDE INICIALIZADO ===');

    // === SISTEMA DE COMENTARIOS ===
    document.addEventListener('click', function(e) {
        // Enviar comentario
        if (e.target.closest('.enviar-comentario')) {
            e.preventDefault();
            console.log('🔔 Click en enviar comentario');
            
            const button = e.target.closest('.enviar-comentario');
            const modal = button.closest('[id^="modal"]');
            
            if (!modal) {
                console.error('❌ No se encontró el modal');
                return;
            }
            
            const textarea = modal.querySelector('.nuevo-comentario');
            const publicationId = modal.dataset.publicationId;
            const comment = textarea.value.trim();

            console.log('📝 Datos:', { publicationId, comment: comment.substring(0, 50) });

            if (!comment) {
                showToast('Escribe un comentario antes de enviarlo.', 'warning', 2800);
                return;
            }
            
            if (!publicationId) {
                showToast('No se encontró la publicación seleccionada.', 'error', 4000, {
                    description: 'Actualiza la página e inténtalo nuevamente.'
                });
                return;
            }

            const idleButtonMarkup = button.innerHTML;

            // Deshabilitar botón mientras se envía
            button.disabled = true;
            button.innerHTML = '<i class="fas fa-spinner fa-spin text-sm"></i>';

            fetch(`/reportes/${publicationId}/comentarios`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ comment })
            })
            .then(response => {
                console.log('📡 Respuesta recibida:', response.status, response.statusText);
                // Si no es OK, leer el body como texto para más detalles y lanzar error
                if (!response.ok) {
                    return response.text().then(text => {
                        const short = text.length > 100 ? text.substring(0, 100) + '...' : text;
                        throw new Error(`HTTP ${response.status}: ${short}`);
                    });
                }

                // Intentar parsear JSON, pero si el content-type no es JSON, leer como texto y fallar con mensaje claro
                const contentType = response.headers.get('content-type') || '';
                if (contentType.indexOf('application/json') === -1) {
                    return response.text().then(text => {
                        try {
                            return JSON.parse(text);
                        } catch (e) {
                            throw new Error('Respuesta inválida del servidor: ' + (text || response.statusText));
                        }
                    });
                }

                return response.json();
            })
            .then(data => {
                console.log('✅ Datos recibidos:', data);
                
                if (data.success) {
                    // Limpiar textarea
                    textarea.value = '';
                    textarea.style.height = 'auto';
                    // Obtener el contenedor de comentarios
                    const comentariosContainer = modal.querySelector('.modal-comentarios');
                    const comentariosToggle = modal.querySelector('.comentarios-toggle');
                    const comentariosContainerDiv = modal.querySelector('.comentarios-container');
                    
                    // Obtener los comentarios actuales almacenados en el modal
                    // (los almacenamos cuando se abre el modal en fillFilesAndComments)
                    let comentariosActuales = [];
                    try {
                        const comentariosData = modal.dataset.comentariosJson;
                        if (comentariosData) {
                            comentariosActuales = JSON.parse(comentariosData);
                        }
                    } catch (e) {
                        console.warn('No se pudieron cargar comentarios previos del dataset, continuando...');
                        comentariosActuales = [];
                    }

                    // Agregar el nuevo comentario al final (nueva entrada = más reciente)
                    comentariosActuales.push(data.comment);
                    
                    // Actualizar el dataset con los nuevos comentarios
                    modal.dataset.comentariosJson = JSON.stringify(comentariosActuales);

                    // Re-renderizar todos los comentarios
                    renderComments(comentariosContainer, comentariosActuales);
                    
                    // Actualizar el contador de comentarios y expandir si estaba contraído
                    if (comentariosToggle) {
                        const contadorSpan = comentariosToggle.querySelector('.contador-comentarios');
                        if (contadorSpan) {
                            contadorSpan.textContent = getCommentsCounterLabel(modal, comentariosActuales.length);
                        }
                        
                        // Expandir la sección si estaba contraída
                        if (comentariosContainerDiv && !comentariosContainerDiv.classList.contains('expanded')) {
                            comentariosContainerDiv.classList.add('expanded');
                            const icono = comentariosToggle.querySelector('.icono-chevron');
                            if (icono) {
                                icono.style.transform = 'rotate(180deg)';
                            }
                        }
                    }
                    
                    // Remove unread dot for this publication (author has read their own comment)
                    try {
                        const card = document.querySelector(`.publication-card[data-publication-id="${publicationId}"]`);
                        if (card) {
                            const dot = card.querySelector('.absolute.-top-0\\.5.-right-0\\.5');
                            if (dot) dot.remove();
                        }
                    } catch (e) {
                        // ignore
                    }
                    
                    console.log('Comentario agregado correctamente.');
                    showToast('Comentario enviado.', 'success', 2600);
                } else {
                    showToast(data.message || 'No se pudo enviar el comentario.', 'error', 3200);
                }
            })
            .catch(error => {
                console.error('❌ Error enviando comentario:', error);
                // Mostrar mensaje más informativo al usuario cuando sea posible
                showToast('No se pudo enviar el comentario. Intenta nuevamente.', 'error', 3600);
            })
            .finally(() => {
                button.innerHTML = idleButtonMarkup;
                button.disabled = !textarea.value.trim();
            });
        }

        // Nota: la opción de eliminar comentarios está deshabilitada en la interfaz por ahora.
    });

    // Auto-resize textarea y habilitar/deshabilitar botón
    document.querySelectorAll('.nuevo-comentario').forEach(textarea => {
        textarea.addEventListener('input', function() {
            const modal = this.closest('.publication-detail-modal');
            const maxHeight = modal?.dataset.reportType === 'alcoholimetria' ? 96 : 120;
            this.style.height = 'auto';
            this.style.height = Math.min(this.scrollHeight, maxHeight) + 'px';
            
            const button = this.closest('.report-comment-form')?.querySelector('.enviar-comentario');
            if (button) button.disabled = !this.value.trim();
        });
    });

    // === SISTEMA DE DESCARGA DE ARCHIVOS ===
    document.addEventListener('click', function(e) {
        // Abrir previsualizacion al hacer click en la tarjeta del archivo
        if (e.target.closest('.archivo-preview-card') && !e.target.closest('.archivo-action') && !e.target.closest('.descargar-archivo')) {
            e.preventDefault();
            const card = e.target.closest('.archivo-preview-card');
            window.openArchivoPreviewFromCard(card, e);
        }

        // Abrir previsualizacion desde el boton flotante
        if (e.target.closest('.archivo-view')) {
            e.preventDefault();
            e.stopPropagation();
            const card = e.target.closest('.archivo-preview-card');
            if (card && !e.target.closest('.archivo-view').disabled) {
                window.openArchivoPreviewFromCard(card, e);
            }
            return;
        }

        // Descargar archivo individual
        if (e.target.closest('.descargar-archivo')) {
            e.preventDefault();
            e.stopPropagation();
            const button = e.target.closest('.descargar-archivo');
            const fileId = button.dataset.fileId;
            
            if (fileId && fileId !== 'null') {
                window.location.href = `/reportes/file/${fileId}/download`;
            } else {
                showToast('No se encontró el archivo seleccionado.', 'error', 4000, {
                    description: 'Actualiza la página e inténtalo nuevamente.'
                });
            }
        }
    });

    console.log('=== SISTEMA DE COMENTARIOS Y DESCARGAS INICIALIZADO ===');

    // === SISTEMA DE APROBACIÓN/RECHAZO DE REPORTES ===
    window.approveReport = async function(publicationId) {
        const confirmed = await window.confirmDialog({
            title: 'Aprobar reporte',
            question: '¿Deseas aprobar este reporte?',
            description: 'El reporte quedará marcado como aprobado y será visible con ese estado.',
            confirmText: 'Aprobar',
            cancelText: 'Cancelar',
            variant: 'success'
        });
        if (!confirmed) return;
        
        // Buscar el modal abierto para extraer datos del reporte
        const modal = Array.from(document.querySelectorAll('[id^="modal"]')).find(m => !m.classList.contains('hidden') && m.id !== 'reject-modal');
        if (!modal) return;

        // Obtener elemento del dataset
        const dataElement = modal.querySelector('[data-status]');
        const dataset = dataElement ? dataElement.dataset : {};

        fetch(`/reportes/${publicationId}/aprobar`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Mostrar toast de éxito
                showToast('Reporte aprobado.', 'success', 3000);
                refreshReportesPanel();
                
                // Actualizar estado en el modal sin recargar
                if (dataElement) {
                    dataElement.dataset.status = 'aprobado';
                    dataElement.dataset.approvedBy = data.approved_by || '';
                    dataElement.dataset.approvedAt = data.approved_at || '';
                }

                // Cerrar modal después de 1s para que se vea el toast
                setTimeout(() => {
                    if (modal && !modal.classList.contains('hidden')) {
                        closeModal(modal.id);
                    }
                }, 1000);
            } else {
                showToast(data.message || 'No se pudo aprobar el reporte.', 'error', 3000);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('No se pudo aprobar el reporte.', 'error', 3000);
        });
    };

    // Variable para guardar el modal anterior
    let previousModalId = null;

    window.showRejectModal = function(publicationId, publicationTitle) {
        // Guardar cuál modal de detalles estaba abierto
        document.querySelectorAll('[id^="modal"]').forEach(modal => {
            if (modal.id !== 'reject-modal' && !modal.classList.contains('hidden')) {
                previousModalId = modal.id;
                closeModal(modal.id);
            }
        });
        
        // Pequeño delay para que se cierre el modal anterior antes de abrir el de rechazo
        setTimeout(() => {
            document.getElementById('reject-modal-publication-id').value = publicationId;
            document.getElementById('reject-modal-title').textContent = publicationTitle;
            document.getElementById('reject-modal').classList.remove('hidden');
            // Desactivar scroll de la página
            document.body.style.overflow = 'hidden';
            document.getElementById('rejection-reason')?.focus();
        }, 350);
    };

    window.closeRejectModal = async function(returnToPrevious = true) {
        const reasonField = document.getElementById('rejection-reason');
        if (returnToPrevious && reasonField?.value.trim()) {
            const confirmed = await window.confirmDeleteDialog({
                title: 'Descartar motivo',
                subject: 'el motivo escrito',
                description: 'El texto no se guardará y regresará al detalle del reporte.',
                confirmLabel: 'Descartar'
            });
            if (!confirmed) {
                reasonField.focus();
                return;
            }
        }
        document.getElementById('reject-modal').classList.add('hidden');
        if (reasonField) reasonField.value = '';
        
        // Si se cancela, regresar al modal anterior
        if (returnToPrevious && previousModalId) {
            // showModal habilitará el overflow hidden nuevamente
            showModal(previousModalId);
            previousModalId = null;
        } else {
            // Si no regresamos a un modal anterior, restaurar el scroll
            document.body.style.overflow = 'auto';
            previousModalId = null;
        }
    };

    window.submitRejection = function() {
        const publicationId = document.getElementById('reject-modal-publication-id').value;
        const reason = document.getElementById('rejection-reason').value.trim();
        
        if (!reason) {
            showToast('Escribe la razón de rechazo.', 'warning', 3000);
            return;
        }
        
        fetch(`/reportes/${publicationId}/rechazar`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ rejection_reason: reason })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Mostrar toast de éxito
                showToast('Reporte rechazado.', 'success', 3000);
                refreshReportesPanel();
                
                // Actualizar estado en el modal sin recargar
                const modal = Array.from(document.querySelectorAll('[id^="modal"]')).find(m => !m.classList.contains('hidden') && m.id !== 'reject-modal');
                if (modal) {
                    const dataElement = modal.querySelector('[data-status]');
                    if (dataElement) {
                        dataElement.dataset.status = 'rechazado';
                        dataElement.dataset.rejectedBy = data.rejected_by || '';
                        dataElement.dataset.rejectedAt = data.rejected_at || '';
                    }
                }

                // Cerrar modal de rechazo
                closeRejectModal(false);
                
                // Cerrar modal anterior después de 1s
                setTimeout(() => {
                    const mainModal = Array.from(document.querySelectorAll('[id^="modal"]')).find(m => !m.classList.contains('hidden') && m.id !== 'reject-modal');
                    if (mainModal) {
                        closeModal(mainModal.id);
                    }
                }, 1000);
            } else {
                showToast(data.message || 'No se pudo rechazar el reporte.', 'error', 3000);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('No se pudo rechazar el reporte.', 'error', 3000);
        });
    };

    window.resubmitReport = async function(publicationId) {
        const confirmed = await window.confirmDialog({
            title: 'Reenviar reporte',
            question: '¿Deseas reenviar este reporte?',
            description: 'El reporte volverá a quedar pendiente de revisión.',
            confirmText: 'Reenviar',
            cancelText: 'Cancelar',
            variant: 'warning'
        });
        if (!confirmed) return;
        
        // Buscar el modal abierto
        const modal = Array.from(document.querySelectorAll('[id^="modal"]')).find(m => !m.classList.contains('hidden') && m.id !== 'reject-modal');
        if (!modal) return;

        const dataElement = modal.querySelector('[data-status]');
        const dataset = dataElement ? dataElement.dataset : {};

        fetch(`/reportes/${publicationId}/reenviar`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Mostrar toast de éxito
                showToast('Reporte reenviado para revisión.', 'success', 3000);
                refreshReportesPanel();
                
                // Actualizar estado en el modal
                if (dataElement) {
                    dataElement.dataset.status = 'pendiente';
                    dataElement.dataset.rejectedBy = '';
                    dataElement.dataset.rejectedAt = '';
                }

                // Cerrar modal después de 1s
                setTimeout(() => {
                    if (modal && !modal.classList.contains('hidden')) {
                        closeModal(modal.id);
                    }
                }, 1000);
            } else {
                showToast(data.message || 'No se pudo reenviar el reporte.', 'error', 3000);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('No se pudo reenviar el reporte.', 'error', 3000);
        });
    };

    // === SISTEMA DE ELIMINACION ===
    document.addEventListener('click', async function(e) {
        if (e.target.closest('.eliminar-reporte')) {
            e.preventDefault();
            const button = e.target.closest('.eliminar-reporte');
            const deleteUrl = button.dataset.deleteUrl;
            const redirectTipo = button.dataset.redirectTipo;

            const confirmed = await window.confirmDeleteDialog({
                title: 'Eliminar publicación',
                subject: 'esta publicación',
                description: 'La publicación y sus archivos dejarán de estar disponibles de forma permanente.'
            });
            if (!confirmed) {
                return;
            }

            const body = new URLSearchParams();
            body.set('_method', 'DELETE');
            body.set('redirect_tipo', redirectTipo || new URLSearchParams(window.location.search).get('tipo') || 'todos');

            fetch(deleteUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'text/html'
                },
                body: body.toString()
            })
            .then(response => response.text().then(html => ({ response, html })))
            .then(({ response, html }) => {
                const replaced = replaceReportesPanelFromHtml(html, response.url || window.location.href, {
                    push: false,
                    scroll: false
                });
                if (replaced) {
                    showToast('Publicación eliminada.', 'success', 3000);
                }
            })
            .catch(error => {
                console.error('Error eliminando reporte:', error);
                showToast('No se pudo eliminar la publicación.', 'error', 3000);
            });
        }
    });

    // === Abrir modal desde área de archivos o icono de comentarios ===
    document.addEventListener('click', function(e) {
        // Si se hace click en el bloque de archivos, abrir el modal de detalles
        const archivosEl = e.target.closest('.archivos-open');
        if (archivosEl) {
            e.preventDefault();
            const card = archivosEl.closest('.publication-card');
            if (card) {
                const eyeBtn = card.querySelector('button[title="Ver detalles"]');
                if (eyeBtn) eyeBtn.click();
            }
            return;
        }

        // Si se hace click en el icono de comentarios, abrir modal y llevar a la sección de comentarios
        const commentsBtn = e.target.closest('.open-comments');
        if (commentsBtn) {
            e.preventDefault();
            const card = commentsBtn.closest('.publication-card');
            if (card) {
                const eyeBtn = card.querySelector('button[title="Ver detalles"]');
                if (eyeBtn) {
                    eyeBtn.click();
                    // Después de abrir el modal, desplazar/focar la sección de comentarios
                    setTimeout(() => {
                        const visibleModal = document.querySelector('[id^="modal"]:not(.hidden)');
                        if (visibleModal) {
                            const comentariosContainer = visibleModal.querySelector('.modal-comentarios');
                            const textarea = visibleModal.querySelector('.nuevo-comentario');
                            if (comentariosContainer) comentariosContainer.scrollTop = comentariosContainer.scrollHeight;
                            if (textarea) {
                                textarea.focus();
                                textarea.style.height = 'auto';
                                textarea.style.height = Math.min(textarea.scrollHeight, 120) + 'px';
                            }
                        }
                    }, 450);
                }
            }
            return;
        }
    });

    // ========== MANEJO DE CHECKBOXES Y ELIMINACIÓN MASIVA DE REPORTES ==========
    // Función para actualizar la barra de herramientas y el contador
    function toggleBulkToolbar() {
        const toolbar = document.getElementById('bulk-toolbar');
        const counter = document.getElementById('selected-count');
        const checked = document.querySelectorAll('.publication-check-btn:checked');
        if (!toolbar || !counter) return;
        
        // Actualizar todas las tarjetas
        document.querySelectorAll('.publication-card-wrapper').forEach(card => {
            card.classList.remove('selected-card');
        });
        
        if (checked.length > 0) {
            toolbar.classList.remove('hidden');
            counter.textContent = `${checked.length} seleccionado${checked.length === 1 ? '' : 's'}`;
            // Resaltar SOLO las tarjetas seleccionadas
            checked.forEach(checkbox => {
                const card = checkbox.closest('.publication-card-wrapper');
                if (card) {
                    card.classList.add('selected-card');
                }
            });
        } else {
            toolbar.classList.add('hidden');
        }
    }
    document.addEventListener('change', function(e) {
        if (e.target.closest('.publication-check-btn') && getReportesPanel()?.contains(e.target)) {
            toggleBulkToolbar();
        }
    });

    document.addEventListener('click', async function(e) {
        const clearButton = e.target.closest('#clear-selection');
        if (clearButton && getReportesPanel()?.contains(clearButton)) {
            e.preventDefault();
            document.querySelectorAll('.publication-check-btn:checked').forEach(checkbox => {
                checkbox.checked = false;
            });
            toggleBulkToolbar();
            return;
        }

        const bulkDeleteButton = e.target.closest('#bulk-delete-reports');
        if (bulkDeleteButton && getReportesPanel()?.contains(bulkDeleteButton)) {
            e.preventDefault();
            const checked = document.querySelectorAll('.publication-check-btn:checked');
            if (checked.length === 0) {
                showToast('Selecciona al menos un reporte.', 'warning', 2800);
                return;
            }

            const ids = Array.from(checked).map(checkbox => checkbox.dataset.publicationId);
            const confirmed = await window.confirmDeleteDialog({
                title: 'Eliminar reportes',
                subject: `${ids.length} reporte${ids.length === 1 ? '' : 's'}`,
                description: 'Los reportes seleccionados dejarán de estar disponibles de forma permanente.'
            });
            if (!confirmed) {
                return;
            }

            fetch('{{ route("reportes.massDelete") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ ids: ids })
            })
            .then(response => response.json())
            .then(data => {
                if (data.ok) {
                    const deletedReports = Number(data.deleted || 0);
                    const skippedReports = Number(data.skipped || 0);
                    const deletionMessage = deletedReports === 1
                        ? 'Se eliminó 1 reporte.'
                        : `Se eliminaron ${deletedReports} reportes.`;

                    if (skippedReports > 0) {
                        showToast(deletionMessage, 'warning', 5000, {
                            description: skippedReports === 1
                                ? '1 reporte no se eliminó porque no tienes permiso.'
                                : `${skippedReports} reportes no se eliminaron porque no tienes permiso.`
                        });
                    } else {
                        showToast(deletionMessage, 'success', 3500);
                    }
                    refreshReportesPanel();
                } else {
                    showToast(data.error || 'No se pudieron eliminar los reportes.', 'error', 3500);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showToast('No se pudieron eliminar los reportes.', 'error', 3500);
            });
            return;
        }

        const bulkDownloadButton = e.target.closest('#bulk-download-files');
        if (bulkDownloadButton && getReportesPanel()?.contains(bulkDownloadButton)) {
            e.preventDefault();
            const checked = document.querySelectorAll('.publication-check-btn:checked');
            if (checked.length === 0) {
                showToast('Selecciona al menos un reporte.', 'warning', 2800);
                return;
            }

            const ids = Array.from(checked).map(checkbox => checkbox.dataset.publicationId);
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '{{ route("reportes.massDownloadFiles") }}';

            const csrfInput = document.createElement('input');
            csrfInput.type = 'hidden';
            csrfInput.name = '_token';
            csrfInput.value = document.querySelector('meta[name="csrf-token"]').content;
            form.appendChild(csrfInput);

            const idsInput = document.createElement('input');
            idsInput.type = 'hidden';
            idsInput.name = 'publication_ids';
            idsInput.value = JSON.stringify(ids);
            form.appendChild(idsInput);

            document.body.appendChild(form);
            form.submit();
            form.remove();
        }
    });

    // === ABRIR REPORTE DESDE PARÁMETROS DE URL (desde notificaciones) ===
    const urlParams = new URLSearchParams(window.location.search);
    const publicationIdFromUrl = urlParams.get('publication');
    const commentIdFromUrl = urlParams.get('comment');
    
    if (publicationIdFromUrl) {
        console.log('📌 Parámetros de URL detectados:', { publication: publicationIdFromUrl, comment: commentIdFromUrl });
        
        // Esperar a que los botones estén recolectados
        setTimeout(() => {
            const btn = document.querySelector(`button[data-publication-id="${publicationIdFromUrl}"]`);
            if (btn) {
                console.log('✅ Botón de reporte encontrado, abriendo...');
                btn.click();
                
                // Si hay un comentario específico, scroll a él
                if (commentIdFromUrl) {
                    let tries = 0;
                    const iv = setInterval(() => {
                        tries += 1;
                        const modal = Array.from(document.querySelectorAll('[id^="modal"]')).find(m => !m.classList.contains('hidden'));
                        if (modal) {
                            const commentEl = modal.querySelector(`[data-comment-id="${commentIdFromUrl}"]`);
                            if (commentEl) {
                                console.log('✅ Comentario encontrado, scroll...');
                                commentEl.scrollIntoView({ behavior: 'smooth', block: 'center' });
                                commentEl.style.transition = 'box-shadow 0.3s ease';
                                commentEl.style.boxShadow = '0 0 0 3px rgba(59,130,246,0.25)';
                                setTimeout(() => { commentEl.style.boxShadow = 'none'; }, 3500);
                                clearInterval(iv);
                            }
                        }
                        if (tries > 30) {
                            console.warn('❌ Comentario no encontrado después de varios intentos');
                            clearInterval(iv);
                        }
                    }, 200);
                }
            } else {
                console.warn('❌ Botón de reporte no encontrado:', publicationIdFromUrl);
            }
        }, 500);
    }
});
</script>


<script>
    // Initialize TomSelect for district filter
    window.initializeReportesTomSelect = function() {
        if (typeof TomSelect === 'undefined') return;
        const tomSelectElements = document.querySelectorAll('.tomselect-select');
        tomSelectElements.forEach(select => {
            const field = select.closest('.district-filter-field');
            field?.classList.remove('tomselect-ready');
            select.style.display = 'none';
            select.style.visibility = 'hidden';

            if (select.tomselect) {
                select.tomselect.destroy();
            }

            const staleWrapper = select.nextElementSibling;
            if (staleWrapper?.classList.contains('ts-wrapper')) {
                staleWrapper.remove();
            }

            const instance = new TomSelect(select, {
                create: false,
                allowEmptyOption: false,
                placeholder: select.dataset.placeholder || 'Todos',
                maxItems: 1,
                searchField: ['text', 'value'],
                closeAfterSelect: true,
                dropdownParent: 'body',
                render: {
                    no_results: function() {
                        return '<div class="no-results">Sin resultados</div>';
                    }
                },
                onChange: function() {
                    if (typeof syncReportsFilterPanel === 'function') syncReportsFilterPanel();
                }
            });

            // Posicionar el dropdown correctamente
            const control = instance.control;
            const dropdown = instance.dropdown;
            dropdown.classList.add('reports-ts-dropdown');

            const positionDropdown = () => {
                const rect = control.getBoundingClientRect();
                dropdown.style.top = `${rect.bottom}px`;
                dropdown.style.left = `${rect.left}px`;
                dropdown.style.width = `${rect.width}px`;
            };

            // Posicionar cuando se abre
            instance.on('dropdown_open', positionDropdown);
            instance.on('type', positionDropdown);
            instance.on('load', positionDropdown);
            
        });

        document.querySelectorAll('.district-filter-field').forEach(field => {
            field.classList.add('tomselect-ready');
        });
        if (typeof syncReportsFilterPanel === 'function') syncReportsFilterPanel();
    };

    document.addEventListener('DOMContentLoaded', window.initializeReportesTomSelect);
</script>
@endsection
