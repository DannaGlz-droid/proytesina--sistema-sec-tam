@extends('layouts.principal')
@section('title', 'Gestión de usuarios')
@section('content')

    @include('components.header-admin')
    @include('components.nav-usuario')

    <div class="users-management-page px-4 lg:pl-10 pt-5 lg:pt-7 pb-8 lg:pb-10">
        <x-ui.page-header
            class="users-management-header"
            title="Gestión de usuarios"
            description="Administre y gestione todos los usuarios del sistema con permisos y roles específicos."
        >
            <x-slot:actions>
                <a href="{{ route('user.create') }}" class="users-page-create-btn" title="Crear usuario">
                    <i class="fas fa-plus text-xs" aria-hidden="true"></i>
                    Crear usuario
                </a>
            </x-slot:actions>
        </x-ui.page-header>

                <div class="space-y-4">
                    <div class="min-w-0">
                        <!-- Custom controls wrapper styled like the site -->
                        <div class="app-table-card users-table-card">
                            <!-- Toolbar: create button LEFT · search + per-page RIGHT (matching reference layout) -->
                            <div class="app-table-toolbar flex flex-row flex-wrap items-center justify-between gap-3 p-4">
                                <x-filtros.usuarios :positions="$positions" :districts="$districts" :roles="$roles" />

                                <!-- Bulk selection bar (JS moves this after the toolbar) -->
                                <div id="bulk-selection-bar" class="app-table-bulk-inline hidden items-center gap-3">
                                    <div class="flex items-center gap-2">
                                        <span class="app-table-selection-marker"></span>
                                        <span id="bulk-selected-count" class="app-table-selection-count text-xs whitespace-nowrap"></span>
                                    </div>
                                    <span class="hidden xl:inline text-xs text-gray-500">En esta página</span>
                                    <button id="clear-selected-users" type="button" class="hidden text-xs font-semibold text-slate-600 hover:underline whitespace-nowrap" title="Quitar selección">
                                        Quitar selección
                                    </button>
                                    <button id="bulk-delete-users" type="button" class="app-table-bulk-danger hidden items-center gap-2" title="Eliminar seleccionados">
                                        <i class="fas fa-trash text-xs"></i>
                                        <span>Eliminar</span>
                                    </button>
                                </div>
                            </div>

                            <!-- Table wrapper -->
                            <div class="app-table-shell overflow-x-hidden min-w-0">
                        <table id="users-table" class="app-data-table min-w-full w-full text-sm text-left text-gray-500">
                            <thead class="text-xs">
                                <tr>
                                    <th scope="col" class="px-4 py-2 whitespace-nowrap text-xs" data-orderable="false"></th>
                                    <th scope="col" class="dt-checkbox-cell px-3 py-2 whitespace-nowrap text-xs text-center"><input id="select-all-users" type="checkbox" /></th>
                                    <th scope="col" class="px-3 py-2 whitespace-nowrap text-xs">ID</th>
                                    <th scope="col" class="px-3 py-2 whitespace-nowrap text-xs">Usuario</th>
                                    <th scope="col" class="px-3 py-2 whitespace-nowrap text-xs">Nombre(s)</th>
                                    <th scope="col" class="px-3 py-2 whitespace-nowrap text-xs">Apellido paterno</th>
                                    <th scope="col" class="px-3 py-2 whitespace-nowrap text-xs">Apellido materno</th>
                                    <th scope="col" class="px-3 py-2 whitespace-nowrap text-xs">Correo</th>
                                    <th scope="col" class="px-3 py-2 whitespace-nowrap text-xs">Teléfono</th>
                                    <th scope="col" class="px-3 py-2 whitespace-nowrap text-xs">Cargo</th>
                                    <th scope="col" class="px-3 py-2 whitespace-nowrap text-xs">Distrito</th>
                                    <th scope="col" class="px-3 py-3 whitespace-nowrap text-xs">Fecha alta</th>
                                    <th scope="col" class="px-3 py-2 whitespace-nowrap text-xs">Rol</th>
                                    <th scope="col" class="px-3 py-2 whitespace-nowrap text-xs">Estado</th>
                                    <th scope="col" class="px-3 py-2 whitespace-nowrap text-xs">Últ. sesión</th>
                                    <th scope="col" class="px-3 py-2 whitespace-nowrap text-xs text-center" data-orderable="false">
                                        <span class="sr-only">Acciones</span>
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- DataTables will populate this via AJAX -->
                            </tbody>
                        </table>
                            </div>

                            <!-- Custom pagination styled like the site -->
                            <nav class="users-table-footer flex flex-row flex-wrap items-center justify-between gap-3 p-4 border-t border-gray-100">
                                <span class="text-sm font-normal text-gray-500 flex-1 min-w-0 is-loading" id="dt-info">
                                    Preparando tabla
                                </span>
                                <div id="dt-pagination" class="flex-none"></div>
                            </nav>
                        </div>
                        <script>
                            (function restoreUsersTableSnapshot() {
                                const snapshotKey = 'sistema-sec-tam.users-table-snapshot.v1.{{ auth()->id() }}';
                                const dataCacheKey = 'sistema-sec-tam.users-table-data.v1.{{ auth()->id() }}';
                                const restoreIntentKey = 'sistema-sec-tam.users-table-restore-intent.v1';

                                try {
                                    window.shouldRestoreUsersTableState = false;
                                    const restoreIntent = JSON.parse(sessionStorage.getItem(restoreIntentKey) || 'null');
                                    sessionStorage.removeItem(restoreIntentKey);

                                    @if(session('invalidate_users_table_cache'))
                                        sessionStorage.removeItem(snapshotKey);
                                        sessionStorage.removeItem(dataCacheKey);
                                        return;
                                    @endif

                                    const currentTarget = `${window.location.pathname}${window.location.search}`;
                                    const shouldRestore = restoreIntent
                                        && Number.isFinite(restoreIntent.createdAt)
                                        && Date.now() - restoreIntent.createdAt <= 30 * 1000
                                        && restoreIntent.target === currentTarget;

                                    window.shouldRestoreUsersTableState = Boolean(shouldRestore);
                                    if (!shouldRestore) return;

                                    const snapshot = JSON.parse(sessionStorage.getItem(snapshotKey) || 'null');
                                    const card = document.querySelector('.users-table-card');
                                    const isValid = snapshot
                                        && Number.isFinite(snapshot.savedAt)
                                        && Date.now() - snapshot.savedAt <= 5 * 60 * 1000
                                        && typeof snapshot.html === 'string'
                                        && snapshot.html.length > 0
                                        && card;

                                    if (!isValid) {
                                        sessionStorage.removeItem(snapshotKey);
                                        return;
                                    }

                                    const template = document.createElement('template');
                                    template.innerHTML = snapshot.html;
                                    template.content.querySelectorAll('[id]').forEach((element) => element.removeAttribute('id'));
                                    template.content.querySelectorAll('a, button, input, select, textarea').forEach((element) => {
                                        element.setAttribute('tabindex', '-1');
                                        element.setAttribute('aria-hidden', 'true');
                                    });

                                    const overlay = document.createElement('div');
                                    overlay.id = 'users-table-restore-snapshot';
                                    overlay.className = 'users-table-restore-snapshot';
                                    overlay.setAttribute('aria-hidden', 'true');
                                    overlay.appendChild(template.content);
                                    card.style.minHeight = `${Math.max(Number(snapshot.height) || 0, card.offsetHeight)}px`;
                                    card.appendChild(overlay);
                                    window.usersTableSnapshotFallback = window.setTimeout(() => {
                                        overlay.remove();
                                        card.style.removeProperty('min-height');
                                    }, 8000);
                                } catch (error) {
                                    window.shouldRestoreUsersTableState = false;
                                    try { sessionStorage.removeItem(snapshotKey); } catch (storageError) {}
                                }
                            })();
                        </script>
                </div>
            </div>
        </div>
    </div>

    @include('components.user-password-dialog')

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            if (!window.jQuery || !$.fn.DataTable) {
                console.error('jQuery or DataTables not loaded');
                return;
            }

            const bulkSelectionBar = document.getElementById('bulk-selection-bar');
            const tableToolbar = bulkSelectionBar?.closest('.app-table-toolbar');

            if (bulkSelectionBar && tableToolbar) {
                tableToolbar.insertAdjacentElement('afterend', bulkSelectionBar);
            }

            function notifyUser(message, type = 'success', duration = 3000) {
                if (typeof window.showToast === 'function') {
                    window.showToast(message, type, duration);
                } else {
                    console[type === 'error' ? 'error' : 'log'](message);
                }
            }

            // We will read current filter form values on each AJAX request
            const filtersForm = document.getElementById('filtersForm');

            function getFilterControl(name) {
                return filtersForm ? filtersForm.querySelector(`[name="${name}"]`) : null;
            }

            function selectedText(control) {
                if (!control) return '';
                if (control.tagName === 'SELECT') {
                    return control.options[control.selectedIndex]?.text?.trim() || '';
                }
                return control.value || '';
            }

            function setFilterValue(name, value) {
                const control = getFilterControl(name);
                if (!control) return;
                if (control.tomselect) {
                    if (value === null || value === '') {
                        control.tomselect.clear(true);
                    } else {
                        control.tomselect.setValue(value, true);
                    }
                } else {
                    control.value = value;
                }
            }

            function initUsersDistrictTomSelect() {
                const districtSelect = document.getElementById('distrito');
                if (!districtSelect || districtSelect.tomselect || typeof TomSelect === 'undefined') return false;

                new TomSelect(districtSelect, {
                    create: false,
                    allowEmptyOption: false,
                    maxItems: 1,
                    placeholder: districtSelect.dataset.placeholder || 'Todos',
                    render: {
                        no_results: function() {
                            return '<div class="no-results">Sin resultados</div>';
                        }
                    },
                    onChange: function() {
                        updateUsersFilterChips();
                    }
                });

                return true;
            }

            function ensureUsersDistrictTomSelect() {
                if (initUsersDistrictTomSelect()) return;
                let attempts = 0;
                const checker = setInterval(function() {
                    attempts += 1;
                    if (initUsersDistrictTomSelect() || attempts > 20) {
                        clearInterval(checker);
                    }
                }, 100);
            }

            function syncUsersFilterPanel() {
                document.querySelectorAll('.users-filter-option[data-filter-target]').forEach(function(option) {
                    const control = getFilterControl(option.dataset.filterTarget);
                    const isActive = control && String(control.value) === String(option.dataset.filterValue || '');
                    option.classList.toggle('is-active', !!isActive);
                    option.setAttribute('aria-pressed', isActive ? 'true' : 'false');
                });

                const dateRange = getFilterControl('date_range');
                const customSelector = document.getElementById('customRangeSelector');
                if (customSelector && dateRange) {
                    customSelector.classList.toggle('is-visible', dateRange.value === 'custom');
                }

                const dateFromProxy = document.querySelector('[data-filter-date-proxy="date_from"]');
                const dateToProxy = document.querySelector('[data-filter-date-proxy="date_to"]');
                const dateFrom = getFilterControl('date_from');
                const dateTo = getFilterControl('date_to');
                if (dateFromProxy && dateFrom) dateFromProxy.value = dateFrom.value || '';
                if (dateToProxy && dateTo) dateToProxy.value = dateTo.value || '';
            }

            function updateUsersFilterChips() {
                if (!filtersForm) return;
                const chipsContainer = document.getElementById('usersFilterChips');
                const countBadge = document.getElementById('usersFilterCount');
                if (!chipsContainer || !countBadge) return;

                const chips = [];
                const activeStatus = getFilterControl('is_active');
                const lastSession = getFilterControl('last_session');
                const dateRange = getFilterControl('date_range');
                const position = getFilterControl('position_id');
                const district = getFilterControl('district_id');
                const role = getFilterControl('role_id');
                const dateFrom = getFilterControl('date_from');
                const dateTo = getFilterControl('date_to');

                if (activeStatus && activeStatus.value !== '') {
                    chips.push({ label: `Estado: ${selectedText(activeStatus)}`, fields: ['is_active'] });
                }
                if (lastSession && lastSession.value !== '') {
                    chips.push({ label: `Ult. sesión: ${selectedText(lastSession)}`, fields: ['last_session'] });
                }
                if (dateRange && dateRange.value && dateRange.value !== 'all') {
                    let label = `Fecha alta: ${selectedText(dateRange)}`;
                    if (dateRange.value === 'custom') {
                        const from = dateFrom?.value || 'inicio';
                        const to = dateTo?.value || 'hoy';
                        label = `Fecha alta: ${from} a ${to}`;
                    }
                    chips.push({ label, fields: ['date_range', 'date_from', 'date_to'] });
                }
                if (position && position.value !== '') {
                    chips.push({ label: `Cargo: ${selectedText(position)}`, fields: ['position_id'] });
                }
                if (district && district.value !== '') {
                    chips.push({ label: `Distrito: ${selectedText(district)}`, fields: ['district_id'] });
                }
                if (role && role.value !== '') {
                    chips.push({ label: `Rol: ${selectedText(role)}`, fields: ['role_id'] });
                }

                chipsContainer.innerHTML = chips.map((chip) => `
                    <span class="users-filter-chip">
                        ${escapeHtml(chip.label)}
                        <button type="button" data-clear-filter="${chip.fields.join(',')}" aria-label="Quitar ${escapeHtml(chip.label)}">
                            <i class="fas fa-times" aria-hidden="true"></i>
                        </button>
                    </span>
                `).join('');

                countBadge.textContent = chips.length;
                countBadge.classList.toggle('hidden', chips.length === 0);
                syncUsersFilterPanel();
            }

            // Setup CSRF token for AJAX requests
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            function usersAjaxData(d) {
                const liveFilters = {};
                if (filtersForm) {
                    const arr = $(filtersForm).serializeArray();
                    arr.forEach(function(item) {
                        if (liveFilters.hasOwnProperty(item.name)) {
                            if (Array.isArray(liveFilters[item.name])) {
                                liveFilters[item.name].push(item.value);
                            } else {
                                liveFilters[item.name] = [liveFilters[item.name], item.value];
                            }
                        } else {
                            liveFilters[item.name] = item.value;
                        }
                    });
                }

                return $.extend({}, d, liveFilters);
            }

            $.fn.dataTable.Api.register('clearPipeline()', function() {
                return this.iterator('table', function(settings) {
                    settings.clearCache = true;
                });
            });

            const usersTableDataCacheKey = 'sistema-sec-tam.users-table-data.v1.{{ auth()->id() }}';
            const usersTableSnapshotKey = 'sistema-sec-tam.users-table-snapshot.v1.{{ auth()->id() }}';
            const usersTableDataCacheTtl = 5 * 60 * 1000;

            @if(session('invalidate_users_table_cache'))
                try { sessionStorage.removeItem(usersTableDataCacheKey); } catch (e) {}
            @endif

            function readUsersTableDataCache() {
                try {
                    const cached = JSON.parse(sessionStorage.getItem(usersTableDataCacheKey) || 'null');
                    const isValid = cached
                        && Number.isFinite(cached.savedAt)
                        && Date.now() - cached.savedAt <= usersTableDataCacheTtl
                        && Number.isInteger(cached.cacheLower)
                        && Number.isInteger(cached.cacheUpper)
                        && cached.cacheLastRequest
                        && cached.cacheLastJson
                        && Array.isArray(cached.cacheLastJson.data);

                    if (isValid) return cached;
                    sessionStorage.removeItem(usersTableDataCacheKey);
                } catch (e) {
                    try { sessionStorage.removeItem(usersTableDataCacheKey); } catch (storageError) {}
                }

                return null;
            }

            function saveUsersTableDataCache(cacheLower, cacheUpper, cacheLastRequest, cacheLastJson) {
                try {
                    sessionStorage.setItem(usersTableDataCacheKey, JSON.stringify({
                        savedAt: Date.now(),
                        cacheLower,
                        cacheUpper,
                        cacheLastRequest,
                        cacheLastJson
                    }));
                } catch (e) {
                    // The in-memory pipeline still works if session storage is unavailable or full.
                }
            }

            function removeUsersTableRestoreSnapshot() {
                window.clearTimeout(window.usersTableSnapshotFallback);
                document.getElementById('users-table-restore-snapshot')?.remove();
                document.querySelector('.users-table-card')?.style.removeProperty('min-height');
            }

            function saveUsersTableRenderSnapshot() {
                const card = document.querySelector('.users-table-card');
                if (!card || document.getElementById('users-table-restore-snapshot')) return;

                try {
                    const clone = card.cloneNode(true);
                    const sourceControls = card.querySelectorAll('input, select, textarea');
                    const clonedControls = clone.querySelectorAll('input, select, textarea');

                    sourceControls.forEach((source, index) => {
                        const target = clonedControls[index];
                        if (!target) return;

                        if (source.matches('input[type="checkbox"], input[type="radio"]')) {
                            target.toggleAttribute('checked', source.checked);
                            return;
                        }

                        if (source.tagName === 'SELECT') {
                            Array.from(target.options).forEach((option, optionIndex) => {
                                option.toggleAttribute('selected', source.options[optionIndex]?.selected === true);
                            });
                            return;
                        }

                        target.setAttribute('value', source.value);
                    });

                    sessionStorage.setItem(usersTableSnapshotKey, JSON.stringify({
                        savedAt: Date.now(),
                        height: card.offsetHeight,
                        html: clone.outerHTML
                    }));
                } catch (e) {
                    // The data cache still avoids network waiting if the visual snapshot cannot be stored.
                }
            }

            function usersPipeline(opts) {
                const conf = $.extend({
                    pages: 4,
                    url: '',
                    method: 'POST',
                    data: null
                }, opts);

                const persistedCache = window.shouldRestoreUsersTableState
                    ? readUsersTableDataCache()
                    : null;
                let cacheLower = persistedCache?.cacheLower ?? -1;
                let cacheUpper = persistedCache?.cacheUpper ?? null;
                let cacheLastRequest = persistedCache?.cacheLastRequest ?? null;
                let cacheLastJson = persistedCache?.cacheLastJson ?? null;
                let revalidatePersistedCache = Boolean(persistedCache);

                return function(request, drawCallback, settings) {
                    let ajax = false;
                    let requestStart = request.start;
                    const drawStart = request.start;
                    const requestLength = request.length;
                    const requestEnd = requestStart + requestLength;
                    const requestWithFilters = typeof conf.data === 'function'
                        ? conf.data($.extend(true, {}, request))
                        : $.extend(true, {}, request, conf.data || {});
                    const requestSignature = $.extend(true, {}, requestWithFilters);
                    delete requestSignature.draw;
                    delete requestSignature.start;
                    delete requestSignature.length;

                    if (settings.clearCache) {
                        ajax = true;
                        settings.clearCache = false;
                    } else if (
                        cacheLower < 0 ||
                        requestStart < cacheLower ||
                        requestEnd > cacheUpper
                    ) {
                        ajax = true;
                    } else if (
                        JSON.stringify(requestSignature) !== JSON.stringify(cacheLastRequest)
                    ) {
                        ajax = true;
                    }

                    cacheLastRequest = $.extend(true, {}, requestSignature);

                    if (ajax) {
                        revalidatePersistedCache = false;

                        if (requestStart < cacheLower) {
                            requestStart = requestStart - (requestLength * (conf.pages - 1));
                            if (requestStart < 0) requestStart = 0;
                        }

                        cacheLower = requestStart;
                        cacheUpper = requestStart + (requestLength * conf.pages);

                        const ajaxRequest = $.extend(true, {}, requestWithFilters, {
                            start: requestStart,
                            length: requestLength * conf.pages
                        });

                        return $.ajax({
                            type: conf.method,
                            url: conf.url,
                            data: ajaxRequest,
                            dataType: 'json',
                            success: function(json) {
                                cacheLastJson = $.extend(true, {}, json);
                                saveUsersTableDataCache(cacheLower, cacheUpper, cacheLastRequest, cacheLastJson);

                                if (cacheLower !== drawStart) {
                                    json.data.splice(0, drawStart - cacheLower);
                                }
                                if (requestLength >= -1) {
                                    json.data.splice(requestLength, json.data.length);
                                }

                                drawCallback(json);
                            },
                            error: function(xhr, error, thrown) {
                                console.error('DataTables AJAX error:', error, thrown);
                                renderUsersTableState('error');
                                usersTableCard.removeClass('is-refreshing');
                            }
                        });
                    }

                    const json = $.extend(true, {}, cacheLastJson);
                    json.draw = request.draw;
                    json.data.splice(0, requestStart - cacheLower);
                    json.data.splice(requestLength, json.data.length);
                    drawCallback(json);

                    if (revalidatePersistedCache) {
                        revalidatePersistedCache = false;
                        const backgroundLower = cacheLower;
                        const backgroundUpper = cacheUpper;
                        const backgroundSignature = $.extend(true, {}, cacheLastRequest);
                        const backgroundRequest = $.extend(true, {}, requestWithFilters, {
                            start: backgroundLower,
                            length: backgroundUpper - backgroundLower
                        });

                        $.ajax({
                            type: conf.method,
                            url: conf.url,
                            data: backgroundRequest,
                            dataType: 'json',
                            success: function(freshJson) {
                                if (cacheLower !== backgroundLower
                                    || cacheUpper !== backgroundUpper
                                    || JSON.stringify(cacheLastRequest) !== JSON.stringify(backgroundSignature)) {
                                    return;
                                }

                                cacheLastJson = $.extend(true, {}, freshJson);
                                saveUsersTableDataCache(backgroundLower, backgroundUpper, backgroundSignature, cacheLastJson);
                            }
                        });
                    }
                };
            }

            function escapeHtml(value) {
                return $('<div>').text(value == null || value === '' ? '—' : value).html();
            }

            function userDetailItem(label, value, isHtml = false) {
                const content = isHtml ? (value || '—') : escapeHtml(value);
                return `<div><dt>${escapeHtml(label)}</dt><dd>${content}</dd></div>`;
            }

            function userIdentityCell(row) {
                const isEmpty = value => !value || value === '—' || value === 'â€”';
                const fullName = [row.name, row.first_last_name, row.second_last_name]
                    .filter(value => !isEmpty(value))
                    .join(' ')
                    .trim() || row.username || 'Usuario';
                const email = row.email || '';
                const username = row.username ? (String(row.username).startsWith('@') ? row.username : `@${row.username}`) : '';
                const initials = fullName
                    .split(/\s+/)
                    .filter(Boolean)
                    .slice(0, 2)
                    .map(part => part.charAt(0))
                    .join('')
                    .toUpperCase() || 'U';
                const tones = ['tone-1', 'tone-2', 'tone-3', 'tone-4'];
                const tone = tones[Math.abs(Number(row.id || 0)) % tones.length];

                return `
                    <div class="users-identity">
                        ${row.profile_photo_url
                            ? `<img class="users-avatar users-avatar-img" src="${escapeHtml(row.profile_photo_url)}" alt="Foto de ${escapeHtml(fullName)}" loading="lazy">`
                            : `<span class="users-avatar ${tone}">${escapeHtml(initials)}</span>`
                        }
                        <span class="users-identity-copy">
                            <span class="users-name-row">
                                <span class="users-name">${escapeHtml(fullName)}</span>
                                ${username ? `<span class="users-username-badge">${escapeHtml(username)}</span>` : ''}
                            </span>
                            <span class="users-meta">${escapeHtml(email || 'Sin correo')}</span>
                        </span>
                    </div>
                `;
            }

            function userTitleCell(row) {
                const title = row.position && row.position !== '—' && row.position !== 'â€”'
                    ? row.position
                    : 'Sin cargo asignado';
                const district = row.district && row.district !== '—' && row.district !== 'â€”'
                    ? row.district
                    : 'Sin distrito';

                return `
                    <div class="users-title-cell">
                        <span>${escapeHtml(title)}</span>
                        <small>${escapeHtml(district)}</small>
                    </div>
                `;
            }

            function formatUserDetails(row) {
                return '';
            }

            let usersSearchPending = false;
            const usersTableCard = $('.users-table-card').first();
            $('#users-table').on('preXhr.dt', function() {
                if (!usersSearchPending) {
                    usersTableCard.addClass('is-refreshing');
                }
            });
            $('#users-table').on('xhr.dt draw.dt error.dt', function() {
                usersTableCard.removeClass('is-refreshing');
            });

            const usersTablePrefsKey = 'sistema-sec-tam.users-table-prefs';
            const usersTableContextKey = 'sistema-sec-tam.users-table-context';

            function readUsersTablePrefs() {
                try {
                    return JSON.parse(localStorage.getItem(usersTablePrefsKey) || '{}');
                } catch (e) {
                    return {};
                }
            }

            function saveUsersTablePrefs(nextPrefs) {
                try {
                    const currentPrefs = readUsersTablePrefs();
                    localStorage.setItem(usersTablePrefsKey, JSON.stringify({
                        ...currentPrefs,
                        ...nextPrefs
                    }));
                } catch (e) {
                    // Ignore storage failures; table behavior still works for this session.
                }
            }

            function readUsersTableContext() {
                try {
                    return JSON.parse(sessionStorage.getItem(usersTableContextKey) || '{}');
                } catch (e) {
                    return {};
                }
            }

            function currentUsersFilters() {
                const values = {};
                if (!filtersForm) return values;

                $(filtersForm).serializeArray().forEach(function(item) {
                    values[item.name] = item.value;
                });

                return values;
            }

            function restoreUsersFilters(values) {
                if (!values || typeof values !== 'object') return;
                Object.entries(values).forEach(function([name, value]) {
                    setFilterValue(name, value);
                });
            }

            function saveUsersTableContext(api) {
                try {
                    const info = api.page.info();
                    sessionStorage.setItem(usersTableContextKey, JSON.stringify({
                        page: info.page,
                        pageLength: info.length,
                        search: api.search(),
                        order: api.order(),
                        filters: currentUsersFilters()
                    }));
                } catch (e) {
                    // Session storage is an enhancement; navigation still works without it.
                }
            }

            const usersTablePrefs = readUsersTablePrefs();
            const usersTableContext = readUsersTableContext();
            restoreUsersFilters(usersTableContext.filters);

            const initialPageLength = [10, 25, 50, 100].includes(Number(usersTableContext.pageLength))
                ? Number(usersTableContext.pageLength)
                : [10, 25, 50, 100].includes(Number(usersTablePrefs.pageLength))
                    ? Number(usersTablePrefs.pageLength)
                : 25;
            const savedOrder = Array.isArray(usersTableContext.order) && usersTableContext.order.length
                ? usersTableContext.order
                : usersTablePrefs.order;
            const initialOrder = Array.isArray(savedOrder)
                && savedOrder.length
                && Number.isInteger(Number(savedOrder[0]?.[0]))
                && ['asc', 'desc'].includes(String(savedOrder[0]?.[1]).toLowerCase())
                    ? [[Number(savedOrder[0][0]), String(savedOrder[0][1]).toLowerCase()]]
                    : [[11, 'desc']];
            const initialPage = Number.isInteger(Number(usersTableContext.page)) && Number(usersTableContext.page) >= 0
                ? Number(usersTableContext.page)
                : 0;
            const initialSearch = typeof usersTableContext.search === 'string'
                ? usersTableContext.search
                : '';

            $('#users-table').one('init.dt', function() {
                removeUsersTableRestoreSnapshot();
                window.requestAnimationFrame(saveUsersTableRenderSnapshot);
            });

            // Initialize DataTables with server-side processing
            const table = $('#users-table').DataTable({
                serverSide: true,
                processing: false,
                scrollX: false,
                autoWidth: false,
                deferredRender: true,
                searching: true,  // Enable DataTables search
                lengthChange: false, // Disable DataTables length (use custom)
                dom: 't', // Only show table
                responsive: false,
                displayStart: initialPage * initialPageLength,
                search: { search: initialSearch },
                ajax: usersPipeline({
                    url: '{{ route('user.datatable') }}',
                    method: 'POST',
                    pages: 4,
                    data: usersAjaxData
                }),
                columns: [
                    { data: null, name: 'details', orderable: false, searchable: false, defaultContent: '<button type="button" class="dt-details-toggle" title="Ver detalles" aria-label="Ver detalles"><i class="fas fa-chevron-right"></i></button>' },
                    { data: 'id', name: 'id', orderable: false, searchable: false, render: function(data, type, row) { return '<input class="row-check-user" data-id="'+data+'" type="checkbox" />'; } },
                    { data: 'id', name: 'id' },
                    { data: 'username', name: 'username', render: function(data, type, row) {
                        if (type !== 'display') return data;
                        return userIdentityCell(row);
                    } },
                    { data: 'name', name: 'name' },
                    { data: 'first_last_name', name: 'first_last_name' },
                    { data: 'second_last_name', name: 'second_last_name' },
                    { data: 'email', name: 'email' },
                    { data: 'phone', name: 'phone', orderable: false },
                    { data: 'position', name: 'position_id', orderable: false, render: function(data, type, row) {
                        if (type !== 'display') return data;
                        return userTitleCell(row);
                    } },
                    { data: 'district', name: 'district_name' },
                    { data: 'registration_date', name: 'registration_date' },
                    { data: 'role', name: 'role_id', orderable: false },
                    { data: 'status', name: 'is_active', orderable: false },
                    { data: 'last_session', name: 'last_session', render: function(data) { return data || '—'; } },
                    { data: 'actions', name: 'actions', orderable: false, searchable: false }
                ],
                // Hide the ID column visually (we still include it in data for checkbox rendering)
                columnDefs: [
                    { targets: 0, visible: false, searchable: false, orderable: false },
                    { targets: 1, width: '2.5rem', className: 'dt-checkbox-cell text-center' },
                    { targets: 2, visible: false, searchable: false },
                    { targets: 3, width: '28%', className: 'dt-username-cell app-cell-wrap app-cell-strong' },
                    { targets: [4, 5, 6, 7, 10], visible: false },
                    { targets: 8, width: '11%', className: 'dt-phone-cell app-cell-nowrap' },
                    { targets: 9, width: '16%', className: 'dt-title-cell app-cell-wrap' },
                    { targets: 11, width: '10%', className: 'dt-date-cell app-cell-nowrap' },
                    { targets: 12, width: '11%', className: 'dt-role-cell app-cell-nowrap' },
                    { targets: 13, width: '9%', className: 'dt-status-cell app-cell-nowrap' },
                    { targets: 14, width: '10%', className: 'dt-last-session-cell app-cell-nowrap' },
                    { targets: 15, width: '3.5rem', className: 'text-right' }
                ],
                pageLength: initialPageLength,
                // Default: registration_date desc so newest users appear first.
                // registration_date is column index 11 (details + checkbox + hidden id included).
                order: initialOrder,
                language: {
                    emptyTable: 'No hay usuarios registrados',
                    loadingRecords: '<div class="users-table-skeleton" role="status" aria-label="Cargando usuarios"><span class="sr-only">Cargando usuarios</span><div></div><div></div><div></div><div></div><div></div></div>',
                    processing: 'Procesando...',
                    zeroRecords: 'No hay resultados para los criterios seleccionados'
                },
                drawCallback: function(settings) {
                    updateEmptyState(this.api());
                    updateCustomInfo(this.api());
                    updateCustomPagination(this.api());
                    saveUsersTableContext(this.api());
                    window.requestAnimationFrame(saveUsersTableRenderSnapshot);
                    try {
                        clearVisibleUserSelection();
                    } catch (e) {
                        console.error('clearVisibleUserSelection error', e);
                    }
                }
            });

            function applyUsersResponsiveColumns() {
                const width = window.innerWidth || document.documentElement.clientWidth;
                table.column(8).visible(width >= 980, false);  // Teléfono
                table.column(14).visible(width >= 820, false); // Últ. sesión
                table.column(11).visible(width >= 900, false); // Fecha alta
                table.column(9).visible(true, false);          // Cargo
                table.columns.adjust();
            }

            applyUsersResponsiveColumns();
            let usersResponsiveTimer = null;
            window.addEventListener('resize', function() {
                clearTimeout(usersResponsiveTimer);
                usersResponsiveTimer = setTimeout(applyUsersResponsiveColumns, 140);
            });

            // Ensure button state after init
            try { toggleBulkDeleteUsersButton(); } catch (e) {}

            // Custom search functionality
            const usersSearchWrap = $('.users-filter-search');
            $('#dt-search-users').val(initialSearch);
            $('#dt-clear-btn').toggleClass('hidden', !initialSearch);

            $('#dt-search-users').on('keyup', function(e) {
                const val = $(this).val();
                if (e.key === 'Enter') {
                    usersSearchPending = true;
                    usersSearchWrap.addClass('is-searching');
                    table.search(val).draw();
                    $('#dt-clear-btn').toggleClass('hidden', !val);
                }
            });

            $('#dt-clear-btn').on('click', function() {
                $('#dt-search-users').val('');
                usersSearchPending = true;
                usersSearchWrap.addClass('is-searching');
                table.search('').draw();
                $(this).addClass('hidden');
            });

            $('#users-table').on('draw.dt xhr.dt error.dt', function() {
                if (!usersSearchPending) return;
                usersSearchPending = false;
                usersSearchWrap.removeClass('is-searching');
            });

            // Custom per-page change
            $('#dt-per-page').on('change', function() {
                const pageLength = parseInt(this.value);
                saveUsersTablePrefs({ pageLength: pageLength });
                table.clearPipeline().page.len(pageLength).draw();
            });

            table.on('order.dt', function() {
                saveUsersTablePrefs({ order: table.order() });
            });

            const perPageSelect = document.getElementById('dt-per-page');
            const perPageButton = document.getElementById('dt-per-page-button');
            const perPageLabel = document.getElementById('dt-per-page-label');
            const perPageMenu = document.getElementById('dt-per-page-menu');
            const perPageOptions = document.querySelectorAll('.users-page-size-option');

            function closePerPageMenu() {
                if (!perPageMenu || !perPageButton) return;
                perPageMenu.classList.add('hidden');
                perPageButton.setAttribute('aria-expanded', 'false');
            }

            function updatePerPageDropdown(value) {
                if (perPageLabel) perPageLabel.textContent = value;
                perPageOptions.forEach(option => {
                    const isActive = option.dataset.value === String(value);
                    option.classList.toggle('is-active', isActive);
                    option.setAttribute('aria-selected', isActive ? 'true' : 'false');
                });
            }

            if (perPageButton && perPageMenu && perPageSelect) {
                perPageSelect.value = String(table.page.len());
                updatePerPageDropdown(perPageSelect.value);

                perPageButton.addEventListener('click', function(e) {
                    e.stopPropagation();
                    const isOpen = !perPageMenu.classList.contains('hidden');
                    perPageMenu.classList.toggle('hidden', isOpen);
                    perPageButton.setAttribute('aria-expanded', isOpen ? 'false' : 'true');
                    if (!isOpen) {
                        const activeOption = perPageMenu.querySelector('[aria-selected="true"]') || perPageOptions[0];
                        window.requestAnimationFrame(() => activeOption?.focus());
                    }
                });

                perPageOptions.forEach(option => {
                    option.addEventListener('click', function(e) {
                        e.preventDefault();
                        const value = option.dataset.value;
                        perPageSelect.value = value;
                        updatePerPageDropdown(value);
                        closePerPageMenu();
                        $('#dt-per-page').trigger('change');
                    });
                });

                document.addEventListener('click', function(e) {
                    if (!perPageMenu.contains(e.target) && !perPageButton.contains(e.target)) {
                        closePerPageMenu();
                    }
                });

                document.addEventListener('keydown', function(e) {
                    if (e.key === 'Escape' && !perPageMenu.classList.contains('hidden')) {
                        closePerPageMenu();
                        perPageButton.focus();
                        return;
                    }

                    if (!perPageMenu.classList.contains('hidden') && ['ArrowDown', 'ArrowUp', 'Home', 'End'].includes(e.key)) {
                        e.preventDefault();
                        const options = Array.from(perPageOptions);
                        const currentIndex = Math.max(0, options.indexOf(document.activeElement));
                        const nextIndex = e.key === 'Home'
                            ? 0
                            : e.key === 'End'
                                ? options.length - 1
                                : (currentIndex + (e.key === 'ArrowDown' ? 1 : -1) + options.length) % options.length;
                        options[nextIndex]?.focus();
                    }
                });
            }

            // Checkbox selection for users table
            $('#select-all-users').on('change', function() {
                const visibleChecks = $('#users-table tbody .row-check-user');
                const checkedCount = visibleChecks.filter(':checked').length;
                const shouldCheck = checkedCount !== visibleChecks.length;
                visibleChecks.prop('checked', shouldCheck);
                updateUserSelectionState();
            });

            function closeUserActionMenus(exceptMenu) {
                document.querySelectorAll('.users-row-menu').forEach(menu => {
                    if (menu !== exceptMenu) {
                        menu.classList.add('hidden');
                        menu.closest('.users-row-actions')?.querySelector('.users-row-menu-button')?.setAttribute('aria-expanded', 'false');
                    }
                });
            }

            $('#users-table tbody').on('click', '.users-row-menu-button', function(e) {
                e.preventDefault();
                e.stopPropagation();

                const menu = this.closest('.users-row-actions')?.querySelector('.users-row-menu');
                if (!menu) return;

                const isOpen = !menu.classList.contains('hidden');
                closeUserActionMenus(menu);
                menu.classList.toggle('hidden', isOpen);
                this.setAttribute('aria-expanded', isOpen ? 'false' : 'true');
            });

            document.addEventListener('click', function(e) {
                if (!e.target.closest('.users-row-actions')) {
                    closeUserActionMenus();
                }
            });

            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    const openMenu = document.querySelector('.users-row-menu:not(.hidden)');
                    const trigger = openMenu?.closest('.users-row-actions')?.querySelector('.users-row-menu-button');
                    closeUserActionMenus();
                    trigger?.focus();
                }
            });

            // Prevent row-expansion clicks when interacting with checkboxes or action buttons
            $('#users-table tbody').on('click', 'input.row-check-user, button:not(.dt-details-toggle), a', function(e) {
                e.stopPropagation();
            });

            $('#users-table tbody').on('click', 'button.dt-details-toggle', function(e) {
                e.preventDefault();
                e.stopPropagation();

                const tr = $(this).closest('tr');
                const row = table.row(tr);

                if (row.child.isShown()) {
                    row.child.hide();
                    tr.removeClass('dt-details-open');
                } else {
                    row.child(formatUserDetails(row.data()), 'child-row').show();
                    tr.addClass('dt-details-open');
                }
            });

            $('#users-table tbody').on('change', '.row-check-user', function() {
                updateUserSelectionState();
            });

            $('#clear-selected-users').on('click', function() {
                clearVisibleUserSelection();
            });

            function toggleBulkDeleteUsersButton() {
                updateUserSelectionState();
            }

            function updateUserSelectionState() {
                const visibleChecks = $('#users-table tbody .row-check-user');
                const checkedCount = visibleChecks.filter(':checked').length;
                const totalVisible = visibleChecks.length;
                const hasSelection = checkedCount > 0;
                const allSelected = totalVisible > 0 && checkedCount === totalVisible;
                const selectAll = $('#select-all-users');

                selectAll.prop('checked', allSelected);
                selectAll.prop('indeterminate', hasSelection && !allSelected);

                if (hasSelection) {
                    $('#bulk-selection-bar').removeClass('hidden').addClass('flex');
                    $('#bulk-delete-users').removeClass('hidden').addClass('flex');
                    $('#clear-selected-users').removeClass('hidden');
                    $('#bulk-selected-count')
                        .text(checkedCount + ' seleccionado' + (checkedCount === 1 ? '' : 's'));
                } else {
                    $('#bulk-selection-bar').addClass('hidden').removeClass('flex');
                    $('#bulk-delete-users').addClass('hidden').removeClass('flex');
                    $('#clear-selected-users').addClass('hidden');
                    $('#bulk-selected-count').text('');
                }
            }

            function clearVisibleUserSelection() {
                $('#users-table tbody .row-check-user').prop('checked', false);
                updateUserSelectionState();
            }

            function updateEmptyState(api) {
                const info = api.page.info();
                if (info.recordsDisplay === 0) {
                    renderUsersTableState(info.recordsTotal === 0 ? 'empty' : 'no-results');
                }
            }

            function renderUsersTableState(kind) {
                const states = {
                    empty: {
                        icon: 'far fa-user',
                        title: 'Aún no hay usuarios',
                        message: 'Crea el primer usuario para comenzar a gestionar accesos y permisos.',
                        action: '<a class="users-table-state-action" href="{{ route('user.create') }}">Crear usuario</a>'
                    },
                    'no-results': {
                        icon: 'fas fa-search',
                        title: 'No encontramos resultados',
                        message: 'Prueba con otra búsqueda o elimina los filtros aplicados.',
                        action: '<button class="users-table-state-action" type="button" data-clear-users-state>Limpiar búsqueda y filtros</button>'
                    },
                    error: {
                        icon: 'fas fa-exclamation-circle',
                        title: 'No pudimos cargar los usuarios',
                        message: 'Revisa tu conexión e inténtalo nuevamente.',
                        action: '<button class="users-table-state-action" type="button" data-retry-users-table>Reintentar</button>'
                    }
                };
                const state = states[kind] || states.error;
                $('#users-table tbody').html(`
                    <tr class="users-table-state-row">
                        <td colspan="16">
                            <div class="users-table-state users-table-state--${kind}" role="${kind === 'error' ? 'alert' : 'status'}">
                                <i class="${state.icon}" aria-hidden="true"></i>
                                <strong>${state.title}</strong>
                                <span>${state.message}</span>
                                ${state.action}
                            </div>
                        </td>
                    </tr>
                `);
                $('#dt-info').removeClass('is-loading').text(kind === 'error' ? 'Carga interrumpida' : '0 usuarios');
                $('#dt-pagination').empty();
            }

            $('#users-table tbody').on('click', '[data-retry-users-table]', function() {
                table.clearPipeline().ajax.reload();
            });

            $('#users-table tbody').on('click', '[data-clear-users-state]', function() {
                $('#dt-search-users').val('');
                table.search('');
                $('#clearUsersFilters').trigger('click');
            });

            $('#bulk-delete-users').on('click', async function() {
                const ids = [];
                $('#users-table tbody .row-check-user:checked').each(function() {
                    const id = $(this).data('id');
                    if (id) ids.push(id);
                });
                if (!ids.length) {
                    notifyUser('Selecciona al menos un usuario.', 'warning');
                    return;
                }

                const confirmed = await window.confirmDeleteDialog({
                    title: 'Eliminar usuarios',
                    subject: ids.length + ' usuario' + (ids.length === 1 ? '' : 's'),
                    description: 'Los usuarios seleccionados y su acceso al sistema se eliminarán de forma permanente.'
                });

                if (!confirmed) return;

                $.ajax({
                    url: '{{ route('user.massDelete') }}',
                    method: 'POST',
                    data: { ids: ids },
                    success: function(res) {
                        if (res && res.ok) {
                            notifyUser('Se eliminaron ' + (res.deleted || 0) + ' usuarios.', 'success');
                            table.clearPipeline().ajax.reload(null, false);
                            clearVisibleUserSelection();
                        } else {
                            notifyUser('No se pudieron eliminar los usuarios. Intenta nuevamente.', 'error');
                            console.error(res);
                        }
                    },
                    error: function(xhr) {
                        console.error(xhr);
                        notifyUser('No se pudieron eliminar los usuarios. Intenta nuevamente.', 'error');
                    }
                });
            });

            $('#users-table tbody').on('submit', '.js-delete-user-form', async function(e) {
                e.preventDefault();
                e.stopPropagation();

                const form = this;
                const userName = form.dataset.userName || 'este usuario';
                const confirmed = await window.confirmDeleteDialog({
                    title: 'Eliminar usuario',
                    subject: userName,
                    messagePrefix: '¿Deseas eliminar a '
                });

                if (!confirmed) return;

                $.ajax({
                    url: form.action,
                    method: 'POST',
                    data: $(form).serialize(),
                    success: function(res) {
                        notifyUser((res && res.message) ? res.message : 'Usuario eliminado.', 'success');
                        table.clearPipeline().ajax.reload(null, false);
                        clearVisibleUserSelection();
                    },
                    error: function(xhr) {
                        console.error(xhr);
                        notifyUser('No se pudo eliminar el usuario.', 'error');
                    }
                });
            });

            // Function to update custom info text
            function updateCustomInfo(api) {
                const info = api.page.info();
                const start = info.recordsDisplay === 0 ? 0 : info.start + 1;
                const end = info.end;
                const filteredTotal = info.recordsDisplay;
                const totalAll = info.recordsTotal;
                let text = `Mostrando <span class="font-semibold text-gray-900">${start}-${end}</span> de <span class="font-semibold text-gray-900">${filteredTotal}</span>`;
                if (filteredTotal !== totalAll) {
                    text += ` <span class="text-sm text-gray-400">(${totalAll} totales)</span>`;
                }
                $('#dt-info').removeClass('is-loading').html(text);
            }

            // Function to build custom pagination (Flowbite joined style)
            function updateCustomPagination(api) {
                const info = api.page.info();
                const current = info.page + 1;
                const pages = info.pages;

                function pageBtn(i) {
                    if (i === current) {
                        return `<span class="fb-page-btn fb-page-num fb-page-active" aria-current="page">${i}</span>`;
                    }
                    return `<a href="#" data-page="${i - 1}" class="fb-page-btn fb-page-num">${i}</a>`;
                }

                function ellipsis() {
                    return `<span class="fb-page-btn fb-page-num fb-page-ellipsis">...</span>`;
                }

                let html = '<div class="fb-pagination">';

                // Previous
                if (current === 1 || pages === 0) {
                    html += `<span class="fb-page-btn fb-page-first fb-page-disabled">Anterior</span>`;
                } else {
                    html += `<a href="#" data-page="${current - 2}" class="fb-page-btn fb-page-first">Anterior</a>`;
                }

                // Page numbers with ellipsis
                if (pages <= 5) {
                    for (let i = 1; i <= pages; i++) html += pageBtn(i);
                } else if (current <= 3) {
                    for (let i = 1; i <= 5; i++) html += pageBtn(i);
                    html += ellipsis();
                    html += pageBtn(pages);
                } else if (current >= pages - 2) {
                    html += pageBtn(1);
                    html += ellipsis();
                    for (let i = pages - 4; i <= pages; i++) html += pageBtn(i);
                } else {
                    html += pageBtn(1);
                    html += ellipsis();
                    for (let i = current - 1; i <= current + 1; i++) html += pageBtn(i);
                    html += ellipsis();
                    html += pageBtn(pages);
                }

                // Next
                if (current === pages || pages === 0) {
                    html += `<span class="fb-page-btn fb-page-last fb-page-disabled">Siguiente</span>`;
                } else {
                    html += `<a href="#" data-page="${current}" class="fb-page-btn fb-page-last">Siguiente</a>`;
                }

                html += '</div>';
                $('#dt-pagination').html(html);

                // Attach click handlers using targeted selection (no dt-page-link dependency)
                $('#dt-pagination').find('a.fb-page-btn').on('click', function(e) {
                    e.preventDefault();
                    table.page(parseInt($(this).data('page'))).draw('page');
                });
            }

            // When the filters form is submitted, prevent a full page reload and
            // reload the DataTable via AJAX so current filter values are sent.
            $('#filtersForm').on('submit', function(e) {
                e.preventDefault();
                try {
                    table.clearPipeline().ajax.reload();
                    updateUsersFilterChips();
                    $('#usersFilterPanel').addClass('is-collapsed');
                    $('#usersFilterToggle').attr('aria-expanded', 'false');
                } catch (err) {
                    // if table isn't available, fallback to full submit
                    this.submit();
                }
            });

            $('#clearUsersFilters').on('click', function() {
                setFilterValue('is_active', '');
                setFilterValue('last_session', '');
                setFilterValue('date_range', 'all');
                setFilterValue('date_from', '');
                setFilterValue('date_to', '');
                setFilterValue('position_id', '');
                setFilterValue('district_id', '');
                setFilterValue('role_id', '');
                $('#customRangeSelector').removeClass('is-visible');
                syncUsersFilterPanel();
                updateUsersFilterChips();
                table.clearPipeline().ajax.reload();
            });

            $('#usersFilterChips').on('click', '[data-clear-filter]', function() {
                const fields = String($(this).data('clear-filter') || '').split(',');
                fields.forEach(function(field) {
                    if (field === 'date_range') {
                        setFilterValue(field, 'all');
                    } else {
                        setFilterValue(field, '');
                    }
                });
                $('#dateRange').trigger('change');
                syncUsersFilterPanel();
                updateUsersFilterChips();
                table.clearPipeline().ajax.reload();
            });

            $('#usersFilterPanel').on('click', '[data-filter-section-toggle]', function() {
                const section = $(this).closest('[data-filter-section]');
                const isOpen = section.hasClass('is-open');
                section.toggleClass('is-open', !isOpen);
                $(this).find('i')
                    .toggleClass('fa-chevron-down', !isOpen)
                    .toggleClass('fa-chevron-right', isOpen);
            });

            $('#usersFilterPanel').on('click', '.users-filter-option[data-filter-target]', function() {
                const target = this.dataset.filterTarget;
                const value = this.dataset.filterValue || '';
                setFilterValue(target, value);
                if (target === 'date_range') {
                    const customSelector = $('#customRangeSelector');
                    if (value === 'custom') {
                        customSelector.addClass('is-visible');
                    } else {
                        customSelector.removeClass('is-visible');
                        setFilterValue('date_from', '');
                        setFilterValue('date_to', '');
                    }
                }
                syncUsersFilterPanel();
                updateUsersFilterChips();
            });

            $('#usersFilterPanel').on('change', '[data-filter-date-proxy]', function() {
                const target = this.dataset.filterDateProxy;
                setFilterValue(target, this.value || '');
                syncUsersFilterPanel();
                updateUsersFilterChips();
            });

            $('#usersFilterToggle').on('click', function() {
                const panel = $('#usersFilterPanel');
                const collapsed = !panel.hasClass('is-collapsed');
                panel.toggleClass('is-collapsed', collapsed);
                $(this).attr('aria-expanded', collapsed ? 'false' : 'true');
            });

            $('#closeUsersFilters').on('click', function() {
                $('#usersFilterPanel').addClass('is-collapsed');
                $('#usersFilterToggle').attr('aria-expanded', 'false');
            });

            $(document).on('click', function(e) {
                if (!$(e.target).closest('.users-filter-popover-wrap').length) {
                    $('#usersFilterPanel').addClass('is-collapsed');
                    $('#usersFilterToggle').attr('aria-expanded', 'false');
                }
            });

            $(document).on('keydown', function(e) {
                if (e.key === 'Escape' && !$('#usersFilterPanel').hasClass('is-collapsed')) {
                    $('#usersFilterPanel').addClass('is-collapsed');
                    $('#usersFilterToggle').attr('aria-expanded', 'false');
                    $('#usersFilterToggle').trigger('focus');
                }
            });

            // Handle date range selector visibility
            $('#dateRange').on('change', function() {
                const val = $(this).val();
                const customSelector = $('#customRangeSelector');
                if (val === 'custom') {
                    customSelector.addClass('is-visible');
                    $('#usersFilterPanel').removeClass('is-collapsed');
                    $('#usersFilterToggle').attr('aria-expanded', 'true');
                } else {
                    customSelector.removeClass('is-visible');
                    table.clearPipeline().ajax.reload();
                }
                syncUsersFilterPanel();
                updateUsersFilterChips();
            });

            ensureUsersDistrictTomSelect();
            $('#filtersForm').on('change', 'select, input[type="date"]', updateUsersFilterChips);
            updateUsersFilterChips();
            syncUsersFilterPanel();
        });
    </script>
@endsection
