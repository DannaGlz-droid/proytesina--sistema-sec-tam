<section class="users-filter-card imports-filter-toolbar" aria-label="Controles del historial de importaciones">
    <form id="filters-imports-form" class="users-filter-form" novalidate>
        <div class="users-filter-topbar">
            <div class="users-filter-popover-wrap">
                <x-filtros.boton id="importsFilterToggle" controls="importsFilterPanel" count-id="importsFilterCount" class="app-standard-filter-toggle" />
                <x-filtros.panel
                    id="importsFilterPanel"
                    clear-id="limpiarFiltrosImportaciones"
                    cancel-id="closeImportsFilters"
                    apply-id="aplicarFiltrosImportaciones"
                    class="app-standard-filter-panel"
                >
                        <x-filtros.seccion titulo="Fecha de carga" :abierto="true">
                            <div class="imports-filter-fields">
                                <div class="imports-filter-field"><label for="dateRangeImports">Periodo</label><x-filtros.select id="dateRangeImports" name="dateRangeImports" placeholder="Todas las fechas"><option value="all">Todas las fechas</option><option value="today">Hoy</option><option value="week">Últimos 7 días</option><option value="month">Últimos 30 días</option><option value="year">Último año</option><option value="custom">Rango personalizado</option></x-filtros.select></div>
                                <div id="customRangeSelectorImports" class="imports-filter-date-grid hidden"><label>Desde<input type="date" id="startDateImports" name="startDateImports"></label><label>Hasta<input type="date" id="endDateImports" name="endDateImports"></label></div>
                            </div>
                        </x-filtros.seccion>
                        <x-filtros.seccion titulo="Usuario">
                            <div class="imports-filter-field"><label for="usuarioImports">Cargado por</label><x-filtros.select id="usuarioImports" name="usuarioImports" placeholder="Todos los usuarios"><option value="">Todos los usuarios</option></x-filtros.select></div>
                        </x-filtros.seccion>
                        <x-filtros.seccion titulo="Resultado" data-import-filter-view="active">
                            <div class="users-filter-options">
                                <label class="users-filter-option imports-filter-option"><input type="checkbox" id="resultadoCorreccionImports" value="corrections" class="sr-only" data-import-context-filter data-filter-label="Resultado: requiere corrección"><span class="users-filter-check"><i class="fas fa-check" aria-hidden="true"></i></span><span>Requiere corrección</span></label>
                                <label class="users-filter-option imports-filter-option"><input type="checkbox" id="resultadoDiferenciasImports" value="differences" class="sr-only" data-import-context-filter data-filter-label="Resultado: con diferencias"><span class="users-filter-check"><i class="fas fa-check" aria-hidden="true"></i></span><span>Con diferencias</span></label>
                            </div>
                        </x-filtros.seccion>
                        <x-filtros.seccion titulo="Efecto de la reversión" data-import-filter-view="reversed" hidden>
                            <div class="users-filter-options">
                                <label class="users-filter-option imports-filter-option"><input type="checkbox" id="reversionDatosRetiradosImports" value="removed" class="sr-only" data-import-context-filter data-filter-label="Reversión: se retiraron datos"><span class="users-filter-check"><i class="fas fa-check" aria-hidden="true"></i></span><span>Se retiraron datos</span></label>
                                <label class="users-filter-option imports-filter-option"><input type="checkbox" id="reversionSinDatosImports" value="none" class="sr-only" data-import-context-filter data-filter-label="Reversión: no había datos aplicados"><span class="users-filter-check"><i class="fas fa-check" aria-hidden="true"></i></span><span>No había datos aplicados</span></label>
                            </div>
                        </x-filtros.seccion>
                        <x-filtros.seccion titulo="Motivo de falla" data-import-filter-view="failed" hidden>
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
                </x-filtros.panel>
            </div>
            <div class="users-filter-search"><i class="fas fa-search" aria-hidden="true"></i><input type="search" id="search-imports" placeholder="Buscar importaciones..." aria-label="Buscar importaciones" autocomplete="off" autocorrect="off" autocapitalize="none" spellcheck="false" enterkeyhint="search" aria-busy="false"><span class="users-search-progress" aria-hidden="true"></span><button type="button" id="clear-imports-btn" class="hidden" title="Limpiar búsqueda" aria-label="Limpiar búsqueda"><i class="fas fa-times" aria-hidden="true"></i></button></div>
            <div class="app-table-page-size users-filter-page-size"><select id="per-page-imports" class="users-native-page-size" aria-hidden="true" tabindex="-1">@foreach([10,25,50,100] as $size)<option value="{{ $size }}" @selected($size === 10)>{{ $size }}</option>@endforeach</select><div class="users-page-size-dropdown"><button id="per-page-imports-button" type="button" class="users-page-size-button" aria-haspopup="listbox" aria-expanded="false"><span>Mostrar</span><strong id="per-page-imports-label">10</strong><i class="fas fa-list-ul users-page-size-icon" aria-hidden="true"></i></button><div id="per-page-imports-menu" class="users-page-size-menu hidden" role="listbox" aria-labelledby="per-page-imports-button">@foreach([10,25,50,100] as $size)<button type="button" role="option" class="users-page-size-option {{ $size === 10 ? 'is-active' : '' }}" data-value="{{ $size }}">{{ $size }}</button>@endforeach</div></div></div>
        </div>
        <div id="importsFilterChips" class="users-filter-chips" aria-live="polite"></div>
    </form>
</section>

@once
<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('filters-imports-form');
    const panel = document.getElementById('importsFilterPanel');
    const toggle = document.getElementById('importsFilterToggle');
    if (!form || !panel || !toggle) return;
    const panelBody = panel.querySelector('.users-filter-panel-body');
    const resetPanelScroll = () => {
        if (panelBody) panelBody.scrollTop = 0;
    };
    const syncPanelAvailableHeight = () => {
        if (window.matchMedia('(max-width: 639px)').matches) {
            panel.style.removeProperty('max-height');
            return;
        }
        const availableHeight = Math.max(240, Math.floor(window.innerHeight - toggle.getBoundingClientRect().bottom - 16));
        panel.style.setProperty('max-height', availableHeight + 'px', 'important');
    };
    const setPanel = open => {
        if (open) syncPanelAvailableHeight();
        panel.classList.toggle('is-collapsed', !open);
        toggle.setAttribute('aria-expanded', String(open));
        if (open) window.requestAnimationFrame(resetPanelScroll);
    };
    toggle.addEventListener('click', () => setPanel(panel.classList.contains('is-collapsed')));
    document.getElementById('closeImportsFilters')?.addEventListener('click', () => setPanel(false));
    document.addEventListener('click', event => { if (!event.target.closest('.users-filter-popover-wrap')) setPanel(false); });
    document.addEventListener('keydown', event => { if (event.key === 'Escape') setPanel(false); });
    window.addEventListener('resize', () => { if (!panel.classList.contains('is-collapsed')) syncPanelAvailableHeight(); });
    form.querySelectorAll('[data-filter-section]').forEach(section => section.querySelector('[data-filter-section-toggle]')?.addEventListener('click', function () { section.classList.toggle('is-open'); const open = section.classList.contains('is-open'); const icon = this.querySelector('i'); icon?.classList.toggle('fa-chevron-down', open); icon?.classList.toggle('fa-chevron-right', !open); }));
    const dateRange = document.getElementById('dateRangeImports');
    const customRange = document.getElementById('customRangeSelectorImports');
    const contextSelects = Array.from(form.querySelectorAll('[data-import-context-select]'));
    const syncDateRange = () => customRange?.classList.toggle('hidden', dateRange?.value !== 'custom');
    dateRange?.addEventListener('change', syncDateRange);
    form.querySelectorAll('.imports-filter-option input').forEach(input => input.addEventListener('change', () => input.closest('.imports-filter-option')?.classList.toggle('is-active', input.checked)));

    function initImportsTomSelect() {
        if (!window.AppFilterSelect) return false;
        if (dateRange && !dateRange.tomselect) window.AppFilterSelect.init(dateRange, { searchable: false });
        const user = document.getElementById('usuarioImports');
        if (user && !user.tomselect) window.AppFilterSelect.init(user, {
            valueField: 'value', labelField: 'text', searchField: ['text','username'], maxItems: 1, create: false, allowEmptyOption: true, placeholder: 'Todos los usuarios',
            render: { option: function (data, escape) { const name = data.full_name || data.name || data.text || 'Todos los usuarios'; const username = data.username ? '@' + data.username : ''; return '<div><strong>' + escape(name) + '</strong>' + (username ? '<small>' + escape(username) + '</small>' : '') + '</div>'; } }
        });
        contextSelects.forEach(select => {
            if (!select.tomselect) window.AppFilterSelect.init(select, { searchable: false });
        });
        return true;
    }
    if (!initImportsTomSelect()) { let attempts = 0; const timer = window.setInterval(() => { if (initImportsTomSelect() || ++attempts > 40) window.clearInterval(timer); }, 100); }

    const pageSelect = document.getElementById('per-page-imports');
    const pageButton = document.getElementById('per-page-imports-button');
    const pageMenu = document.getElementById('per-page-imports-menu');
    pageButton?.addEventListener('click', () => { const open = pageMenu?.classList.contains('hidden'); pageMenu?.classList.toggle('hidden', !open); pageButton.setAttribute('aria-expanded', String(open)); });
    pageMenu?.querySelectorAll('[data-value]').forEach(option => option.addEventListener('click', () => { pageSelect.value = option.dataset.value; document.getElementById('per-page-imports-label').textContent = option.dataset.value; pageMenu.querySelectorAll('[data-value]').forEach(item => item.classList.toggle('is-active', item === option)); pageMenu.classList.add('hidden'); pageButton.setAttribute('aria-expanded', 'false'); pageSelect.dispatchEvent(new Event('change', { bubbles: true })); }));
    document.addEventListener('click', event => { if (!event.target.closest('.users-page-size-dropdown')) { pageMenu?.classList.add('hidden'); pageButton?.setAttribute('aria-expanded', 'false'); } });

    const escapeText = value => String(value ?? '').replace(/[&<>"']/g, char => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;'}[char]));
    window.syncImportFilterUI = function () {
        syncDateRange();
        form.querySelectorAll('.imports-filter-option input').forEach(input => input.closest('.imports-filter-option')?.classList.toggle('is-active', input.checked));
        const chips = []; const rangeLabels = { today:'Hoy', week:'Últimos 7 días', month:'Últimos 30 días', year:'Último año' };
        if (dateRange?.value === 'custom') { const start = document.getElementById('startDateImports')?.value; const end = document.getElementById('endDateImports')?.value; if (start || end) chips.push({ key: 'date', label: 'Fecha: ' + (start || 'inicio') + ' – ' + (end || 'hoy') }); }
        else if (dateRange?.value && dateRange.value !== 'all') chips.push({ key: 'date', label: 'Fecha: ' + rangeLabels[dateRange.value] });
        const user = document.getElementById('usuarioImports'); if (user?.value) chips.push({ key: 'user', label: 'Usuario: ' + (user.tomselect?.getItem(user.value)?.textContent?.trim() || user.value) });
        form.querySelectorAll('[data-import-context-filter]:checked').forEach(input => chips.push({ key: 'context:' + input.id, label: input.dataset.filterLabel || input.value }));
        contextSelects.forEach(select => Array.from(select.selectedOptions).filter(option => option.value !== 'all').forEach(option => chips.push({ key: 'context-select:' + select.id + ':' + option.value, label: option.dataset.filterLabel || option.textContent.trim() })));
        const chipsRoot = document.getElementById('importsFilterChips');
        if (chipsRoot) chipsRoot.innerHTML = chips.map(chip => '<span class="users-filter-chip"><span>' + escapeText(chip.label) + '</span><button type="button" data-clear-import-filter="' + chip.key + '" aria-label="Quitar ' + escapeText(chip.label) + '"><i class="fas fa-times" aria-hidden="true"></i></button></span>').join('');
        const count = document.getElementById('importsFilterCount'); if (count) { count.textContent = chips.length; count.classList.toggle('hidden', !chips.length); }
    };
    document.getElementById('importsFilterChips')?.addEventListener('click', function (event) {
        const button = event.target.closest('[data-clear-import-filter]');
        if (!button) return;

        if (button.dataset.clearImportFilter === 'date') {
            if (dateRange?.tomselect) dateRange.tomselect.setValue('all', true);
            else if (dateRange) dateRange.value = 'all';
            const start = document.getElementById('startDateImports');
            const end = document.getElementById('endDateImports');
            if (start) start.value = '';
            if (end) end.value = '';
        } else if (button.dataset.clearImportFilter === 'user') {
            const user = document.getElementById('usuarioImports');
            if (user?.tomselect) user.tomselect.clear(true);
            else if (user) user.value = '';
        } else if (button.dataset.clearImportFilter.startsWith('context:')) {
            const input = document.getElementById(button.dataset.clearImportFilter.slice(8));
            if (input) input.checked = false;
        } else if (button.dataset.clearImportFilter.startsWith('context-select:')) {
            const [, selectId, value] = button.dataset.clearImportFilter.split(':');
            const select = document.getElementById(selectId);
            if (select?.tomselect) select.tomselect.setValue('all', true);
            else if (select) select.value = 'all';
        }

        window.syncImportFilterUI();
        document.getElementById('aplicarFiltrosImportaciones')?.click();
    });
    document.getElementById('aplicarFiltrosImportaciones')?.addEventListener('click', () => { setPanel(false); window.setTimeout(window.syncImportFilterUI, 0); });
    document.getElementById('limpiarFiltrosImportaciones')?.addEventListener('click', () => window.setTimeout(window.syncImportFilterUI, 0));
    window.setImportFilterView = function (view, clearContext = false) {
        if (clearContext) {
            form.querySelectorAll('[data-import-context-filter]').forEach(input => { input.checked = false; });
            contextSelects.forEach(select => select.tomselect ? select.tomselect.setValue('all', true) : select.value = 'all');
        }
        form.querySelectorAll('[data-import-filter-view]').forEach(section => {
            section.hidden = section.dataset.importFilterView !== view;
        });
        contextSelects.forEach(select => {
            if (!select.tomselect) window.AppFilterSelect?.init(select, { searchable: false });
        });
        resetPanelScroll();
        window.syncImportFilterUI();
    };
    window.clearImportContextFilters = function () {
        form.querySelectorAll('[data-import-context-filter]').forEach(input => { input.checked = false; });
        contextSelects.forEach(select => select.tomselect ? select.tomselect.setValue('all', true) : select.value = 'all');
        window.syncImportFilterUI();
    };
    window.syncImportUserFilterOptions = function (imports) {
        const user = document.getElementById('usuarioImports');
        if (!user) return;

        const options = Array.from(new Map((Array.isArray(imports) ? imports : []).map(item => {
            const value = String(item.user_id ?? '');
            const fullName = [item.created_by, item.created_by_first_last_name, item.created_by_second_last_name].filter(Boolean).join(' ').trim() || 'Usuario sin nombre';
            const username = String(item.created_by_username || '').replace(/^@/, '');
            return [value, { value, text: username ? `${fullName} (@${username})` : fullName, full_name: fullName, username }];
        }).filter(([value]) => value)).values()).sort((a, b) => a.text.localeCompare(b.text, 'es'));
        const currentValue = String(user.value || '');

        if (user.tomselect) {
            const select = user.tomselect;
            select.clear(true);
            select.clearOptions();
            select.addOption({ value: '', text: 'Todos los usuarios' });
            select.addOptions(options);
            select.setValue(options.some(option => option.value === currentValue) ? currentValue : '', true);
            select.refreshOptions(false);
        } else {
            user.replaceChildren(new Option('Todos los usuarios', ''));
            options.forEach(option => user.add(new Option(option.text, option.value)));
            user.value = options.some(option => option.value === currentValue) ? currentValue : '';
        }
    };
    window.setImportFilterView('active');
    window.syncImportFilterUI();
});
</script>
@endonce
