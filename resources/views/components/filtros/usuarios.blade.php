@props(['positions' => collect(), 'districts' => collect(), 'roles' => collect()])

@php
    $activeValue = request('is_active', '');
    $lastSessionValue = request('last_session', '');
    $dateRangeValue = request('date_range', 'all');
    $positionValue = request('position_id', '');
    $districtValue = request('district_id', '');
    $roleValue = request('role_id', '');
    $filteredRoles = collect($roles ?? [])->filter(function ($role) {
        return in_array(trim((string) ($role->name ?? '')), ['Administrador', 'Coordinador', 'Operador'], true);
    });
    $positionsList = isset($positions) && $positions->isNotEmpty()
        ? $positions->reject(fn($pos) => in_array(mb_strtolower(trim((string) $pos->name)), ['administrador', 'admin', 'no definido'], true))
        : collect();
    $districtsList = isset($districts) && $districts->isNotEmpty() ? $districts : collect();
@endphp

<section class="users-filter-card" aria-label="Filtros de usuarios">
    <form id="filtersForm" method="GET" action="{{ route('user.user-gestion') }}" class="users-filter-form">
        <div class="users-filter-topbar">
            <div class="users-filter-popover-wrap">
                <button type="button" id="usersFilterToggle" class="users-filter-toggle" aria-expanded="false" aria-controls="usersFilterPanel">
                    <i class="fas fa-sliders-h" aria-hidden="true"></i>
                    <span>Filtros</span>
                    <span id="usersFilterCount" class="users-filter-count hidden">0</span>
                </button>

                <div id="usersFilterPanel" class="users-filter-panel users-filter-menu is-collapsed">
                    <div class="users-filter-panel-header">
                        <h2>Filtros</h2>
                        <button type="button" id="clearUsersFilters" class="users-filter-clear">Limpiar</button>
                    </div>

                    <div class="users-filter-native-controls" aria-hidden="true">
                        <select name="date_range" id="dateRange" tabindex="-1">
                            <option value="all" {{ $dateRangeValue === 'all' ? 'selected' : '' }}>Todas las fechas</option>
                            <option value="7days" {{ $dateRangeValue === '7days' ? 'selected' : '' }}>Ultimos 7 dias</option>
                            <option value="30days" {{ $dateRangeValue === '30days' ? 'selected' : '' }}>Ultimos 30 dias</option>
                            <option value="90days" {{ $dateRangeValue === '90days' ? 'selected' : '' }}>Ultimos 90 dias</option>
                            <option value="custom" {{ $dateRangeValue === 'custom' ? 'selected' : '' }}>Personalizado</option>
                        </select>
                        <select name="is_active" id="estadoCuenta" tabindex="-1">
                            <option value="" {{ $activeValue === null || $activeValue === '' ? 'selected' : '' }}>Todos</option>
                            <option value="1" {{ $activeValue === '1' ? 'selected' : '' }}>Activo</option>
                            <option value="0" {{ $activeValue === '0' ? 'selected' : '' }}>Inactivo</option>
                        </select>
                        <select name="last_session" id="ultimaSesion" tabindex="-1">
                            <option value="" {{ $lastSessionValue === null || $lastSessionValue === '' ? 'selected' : '' }}>Cualquier momento</option>
                            <option value="today" {{ $lastSessionValue === 'today' ? 'selected' : '' }}>Hoy</option>
                            <option value="7" {{ $lastSessionValue === '7' ? 'selected' : '' }}>Ultimos 7 dias</option>
                            <option value="30" {{ $lastSessionValue === '30' ? 'selected' : '' }}>Ultimos 30 dias</option>
                            <option value="never" {{ $lastSessionValue === 'never' ? 'selected' : '' }}>Nunca</option>
                        </select>
                        <select name="position_id" id="cargo" tabindex="-1">
                            <option value="" {{ $positionValue === null || $positionValue === '' ? 'selected' : '' }}>Todos los cargos</option>
                            @if($positionsList->isNotEmpty())
                                @foreach($positionsList as $pos)
                                    <option value="{{ $pos->id }}" {{ (string) $positionValue === (string) $pos->id ? 'selected' : '' }}>{{ $pos->name }}</option>
                                @endforeach
                            @else
                                <option>Coordinador</option>
                                <option>Supervisor</option>
                                <option>Analista</option>
                                <option>Tecnico</option>
                                <option>Operador</option>
                            @endif
                        </select>
                        <select name="role_id" id="rol" tabindex="-1">
                            <option value="" {{ $roleValue === null || $roleValue === '' ? 'selected' : '' }}>Todos</option>
                            @if($filteredRoles->isNotEmpty())
                                @foreach($filteredRoles as $role)
                                    <option value="{{ $role->id }}" {{ (string) $roleValue === (string) $role->id ? 'selected' : '' }}>{{ $role->name }}</option>
                                @endforeach
                            @else
                                <option>Administrador</option>
                                <option>Coordinador</option>
                                <option>Operador</option>
                            @endif
                        </select>
                        <input type="date" name="date_from" id="startDate" value="{{ request('date_from') }}" max="{{ now()->toDateString() }}" tabindex="-1">
                        <input type="date" name="date_to" id="endDate" value="{{ request('date_to') }}" max="{{ now()->toDateString() }}" tabindex="-1">
                    </div>

                    <div class="users-filter-panel-body">
                        <div class="users-filter-section" data-filter-section>
                            <button type="button" class="users-filter-section-toggle" data-filter-section-toggle>
                                <i class="fas fa-chevron-right" aria-hidden="true"></i>
                                <span>Fecha alta</span>
                            </button>
                            <div class="users-filter-section-content">
                                <div class="users-filter-options">
                                    @foreach([
                                        'all' => 'Todas las fechas',
                                        '7days' => 'Ultimos 7 dias',
                                        '30days' => 'Ultimos 30 dias',
                                        '90days' => 'Ultimos 90 dias',
                                        'custom' => 'Personalizado',
                                    ] as $value => $label)
                                        <button type="button" class="users-filter-option" data-filter-target="date_range" data-filter-value="{{ $value }}">
                                            <span class="users-filter-check"><i class="fas fa-check" aria-hidden="true"></i></span>
                                            <span>{{ $label }}</span>
                                        </button>
                                    @endforeach
                                </div>
                                <div id="customRangeSelector" class="users-filter-custom-range {{ $dateRangeValue === 'custom' ? 'is-visible' : '' }}">
                                    <label>
                                        <span>Desde</span>
                                        <input type="date" data-filter-date-proxy="date_from" value="{{ request('date_from') }}" max="{{ now()->toDateString() }}">
                                    </label>
                                    <label>
                                        <span>Hasta</span>
                                        <input type="date" data-filter-date-proxy="date_to" value="{{ request('date_to') }}" max="{{ now()->toDateString() }}">
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="users-filter-section {{ $districtValue === null || $districtValue === '' ? 'is-open' : '' }}" data-filter-section>
                            <button type="button" class="users-filter-section-toggle" data-filter-section-toggle>
                                <i class="fas {{ $districtValue === null || $districtValue === '' ? 'fa-chevron-down' : 'fa-chevron-right' }}" aria-hidden="true"></i>
                                <span>Estado</span>
                            </button>
                            <div class="users-filter-section-content">
                                <div class="users-filter-options">
                                    @foreach(['' => 'Todos', '1' => 'Activo', '0' => 'Inactivo'] as $value => $label)
                                        <button type="button" class="users-filter-option" data-filter-target="is_active" data-filter-value="{{ $value }}">
                                            <span class="users-filter-check"><i class="fas fa-check" aria-hidden="true"></i></span>
                                            <span>{{ $label }}</span>
                                        </button>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <div class="users-filter-section" data-filter-section>
                            <button type="button" class="users-filter-section-toggle" data-filter-section-toggle>
                                <i class="fas fa-chevron-right" aria-hidden="true"></i>
                                <span>Última sesión</span>
                            </button>
                            <div class="users-filter-section-content">
                                <div class="users-filter-options">
                                    @foreach([
                                        '' => 'Cualquier momento',
                                        'today' => 'Hoy',
                                        '7' => 'Ultimos 7 dias',
                                        '30' => 'Ultimos 30 dias',
                                        'never' => 'Nunca',
                                    ] as $value => $label)
                                        <button type="button" class="users-filter-option" data-filter-target="last_session" data-filter-value="{{ $value }}">
                                            <span class="users-filter-check"><i class="fas fa-check" aria-hidden="true"></i></span>
                                            <span>{{ $label }}</span>
                                        </button>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <div class="users-filter-section" data-filter-section>
                            <button type="button" class="users-filter-section-toggle" data-filter-section-toggle>
                                <i class="fas fa-chevron-right" aria-hidden="true"></i>
                                <span>Cargo</span>
                            </button>
                            <div class="users-filter-section-content">
                                <div class="users-filter-options">
                                    <button type="button" class="users-filter-option" data-filter-target="position_id" data-filter-value="">
                                        <span class="users-filter-check"><i class="fas fa-check" aria-hidden="true"></i></span>
                                        <span>Todos los cargos</span>
                                    </button>
                                    @foreach($positionsList as $pos)
                                        <button type="button" class="users-filter-option" data-filter-target="position_id" data-filter-value="{{ $pos->id }}">
                                            <span class="users-filter-check"><i class="fas fa-check" aria-hidden="true"></i></span>
                                            <span>{{ $pos->name }}</span>
                                        </button>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <div class="users-filter-section {{ $districtValue !== null && $districtValue !== '' ? 'is-open' : '' }}" data-filter-section>
                            <button type="button" class="users-filter-section-toggle" data-filter-section-toggle>
                                <i class="fas {{ $districtValue !== null && $districtValue !== '' ? 'fa-chevron-down' : 'fa-chevron-right' }}" aria-hidden="true"></i>
                                <span>Distrito</span>
                            </button>
                            <div class="users-filter-section-content">
                                <select name="district_id" id="distrito" class="users-district-tomselect" data-placeholder="Todos">
                                    <option value="" {{ $districtValue === null || $districtValue === '' ? 'selected' : '' }}>Todos</option>
                                    @if($districtsList->isNotEmpty())
                                        @foreach($districtsList as $district)
                                            <option value="{{ $district->id }}" {{ (string) $districtValue === (string) $district->id ? 'selected' : '' }}>{{ $district->name }}</option>
                                        @endforeach
                                    @else
                                        <option>Distrito Sanitario I</option>
                                        <option>Distrito Sanitario II</option>
                                        <option>Distrito Sanitario III</option>
                                    @endif
                                </select>
                            </div>
                        </div>

                        <div class="users-filter-section" data-filter-section>
                            <button type="button" class="users-filter-section-toggle" data-filter-section-toggle>
                                <i class="fas fa-chevron-right" aria-hidden="true"></i>
                                <span>Rol</span>
                            </button>
                            <div class="users-filter-section-content">
                                <div class="users-filter-options">
                                    <button type="button" class="users-filter-option" data-filter-target="role_id" data-filter-value="">
                                        <span class="users-filter-check"><i class="fas fa-check" aria-hidden="true"></i></span>
                                        <span>Todos</span>
                                    </button>
                                    @foreach($filteredRoles as $role)
                                        <button type="button" class="users-filter-option" data-filter-target="role_id" data-filter-value="{{ $role->id }}">
                                            <span class="users-filter-check"><i class="fas fa-check" aria-hidden="true"></i></span>
                                            <span>{{ $role->name }}</span>
                                        </button>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="users-filter-panel-footer">
                        <button type="button" id="closeUsersFilters" class="users-filter-secondary">Cancelar</button>
                        <button type="submit" id="aplicarFiltros" class="users-filter-apply">Aplicar filtros</button>
                    </div>
                </div>
            </div>

            <div class="users-filter-search">
                <i class="fas fa-search" aria-hidden="true"></i>
                <input type="text" id="dt-search-users" name="usuarios_busqueda_no_autofill" placeholder="Buscar usuarios..." autocomplete="new-password" autocorrect="off" autocapitalize="none" spellcheck="false">
                <span class="users-search-progress" aria-hidden="true">
                    <span></span><span></span><span></span>
                </span>
                <button type="button" id="dt-clear-btn" class="hidden" title="Limpiar busqueda" aria-label="Limpiar busqueda">
                    <i class="fas fa-times" aria-hidden="true"></i>
                </button>
            </div>

            <div class="app-table-page-size users-filter-page-size">
                <select id="dt-per-page" class="users-native-page-size" aria-hidden="true" tabindex="-1">
                    <option value="10">10</option>
                    <option value="25" selected>25</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                </select>
                <div class="users-page-size-dropdown">
                    <button id="dt-per-page-button" type="button" class="users-page-size-button" aria-haspopup="listbox" aria-expanded="false">
                        <span>Mostrar</span>
                        <strong id="dt-per-page-label">25</strong>
                        <i class="fas fa-chevron-down" aria-hidden="true"></i>
                    </button>
                    <div id="dt-per-page-menu" class="users-page-size-menu hidden" role="listbox" aria-labelledby="dt-per-page-button">
                        <button type="button" role="option" class="users-page-size-option" data-value="10">10</button>
                        <button type="button" role="option" class="users-page-size-option is-active" data-value="25">25</button>
                        <button type="button" role="option" class="users-page-size-option" data-value="50">50</button>
                        <button type="button" role="option" class="users-page-size-option" data-value="100">100</button>
                    </div>
                </div>
            </div>
        </div>

        <div id="usersFilterChips" class="users-filter-chips" aria-live="polite"></div>
    </form>
</section>
