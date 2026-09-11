<section class="users-filter-card imports-filter-toolbar" aria-label="Controles del historial de importaciones">
    <form id="filters-imports-form" class="users-filter-form" novalidate>
        <div class="users-filter-topbar">
            <div class="users-filter-popover-wrap">
                <x-filtros.boton id="importsFilterToggle" controls="importsFilterPanel" count-id="importsFilterCount" />
                <x-filtros.panel
                    id="importsFilterPanel"
                    clear-id="limpiarFiltrosImportaciones"
                    cancel-id="closeImportsFilters"
                    apply-id="aplicarFiltrosImportaciones"
                >
                        <x-filtros.seccion titulo="Fecha de carga" :abierto="true">
                            <div class="imports-filter-fields">
                                <div class="imports-filter-field"><label for="dateRangeImports">Periodo</label><x-filtros.select id="dateRangeImports" name="dateRangeImports" placeholder="Todas las fechas"><option value="all">Todas las fechas</option><option value="today">Hoy</option><option value="week">Últimos 7 días</option><option value="month">Últimos 30 días</option><option value="year">Último año</option><option value="custom">Rango personalizado</option></x-filtros.select></div>
                                <div id="customRangeSelectorImports" class="imports-filter-date-grid hidden"><label>Desde<input type="date" id="startDateImports" name="startDateImports"></label><label>Hasta<input type="date" id="endDateImports" name="endDateImports"></label></div>
                            </div>
                        </x-filtros.seccion>
                        <x-filtros.seccion titulo="Estado">
                            <div class="users-filter-options">
                                @foreach(['completed' => 'Completado', 'reversed' => 'Revertido', 'failed' => 'Fallido', 'processing' => 'Procesando'] as $value => $label)
                                    <label class="users-filter-option imports-filter-option"><input type="checkbox" name="statuses" value="{{ $value }}" class="status-checkbox sr-only"><span class="users-filter-check"><i class="fas fa-check" aria-hidden="true"></i></span><span>{{ $label }}</span></label>
                                @endforeach
                            </div>
                        </x-filtros.seccion>
                        <x-filtros.seccion titulo="Usuario">
                            <div class="imports-filter-field"><label for="usuarioImports">Cargado por</label><x-filtros.select id="usuarioImports" name="usuarioImports" placeholder="Todos los usuarios"><option value="">Todos los usuarios</option></x-filtros.select></div>
                        </x-filtros.seccion>
                        <x-filtros.seccion titulo="Resultado">
                            <label class="users-filter-option imports-filter-option"><input type="checkbox" id="conFallidos" name="conFallidos" class="sr-only"><span class="users-filter-check"><i class="fas fa-check" aria-hidden="true"></i></span><span>Con registros fallidos</span></label>
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
    const setPanel = open => { panel.classList.toggle('is-collapsed', !open); toggle.setAttribute('aria-expanded', String(open)); };
    toggle.addEventListener('click', () => setPanel(panel.classList.contains('is-collapsed')));
    document.getElementById('closeImportsFilters')?.addEventListener('click', () => setPanel(false));
    document.addEventListener('click', event => { if (!event.target.closest('.users-filter-popover-wrap')) setPanel(false); });
    document.addEventListener('keydown', event => { if (event.key === 'Escape') setPanel(false); });
    form.querySelectorAll('[data-filter-section]').forEach(section => section.querySelector('[data-filter-section-toggle]')?.addEventListener('click', function () { section.classList.toggle('is-open'); const open = section.classList.contains('is-open'); const icon = this.querySelector('i'); icon?.classList.toggle('fa-chevron-down', open); icon?.classList.toggle('fa-chevron-right', !open); }));
    const dateRange = document.getElementById('dateRangeImports');
    const customRange = document.getElementById('customRangeSelectorImports');
    const syncDateRange = () => customRange?.classList.toggle('hidden', dateRange?.value !== 'custom');
    dateRange?.addEventListener('change', syncDateRange);
    form.querySelectorAll('.imports-filter-option input').forEach(input => input.addEventListener('change', () => input.closest('.imports-filter-option')?.classList.toggle('is-active', input.checked)));

    function initImportsTomSelect() {
        if (!window.AppFilterSelect) return false;
        if (dateRange && !dateRange.tomselect) window.AppFilterSelect.init(dateRange, { searchable: false });
        const user = document.getElementById('usuarioImports');
        if (user && !user.tomselect) window.AppFilterSelect.init(user, {
            valueField: 'id', labelField: 'display_name', searchField: ['name','username'], maxItems: 1, create: false, preload: 'focus', placeholder: 'Todos los usuarios',
            load: function (query, callback) { fetch('/api/users/search?importers_only=1&q=' + encodeURIComponent(query)).then(response => response.ok ? response.json() : []).then(items => callback(Array.isArray(items) ? items : [])).catch(() => callback([])); },
            render: { option: function (data, escape) { const name = data.full_name || data.name || ''; const username = data.username ? '@' + data.username : ''; return '<div><strong>' + escape(name) + '</strong>' + (username ? '<small>' + escape(username) + '</small>' : '') + '</div>'; }, item: function (data, escape) { return '<div>' + escape(data.full_name || data.name || data.display_name || '') + '</div>'; } }
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
        const statuses = Array.from(form.querySelectorAll('.status-checkbox:checked')).map(input => input.closest('label')?.innerText.trim()).filter(Boolean); if (statuses.length) chips.push({ key: 'status', label: 'Estado: ' + statuses.join(', ') });
        const user = document.getElementById('usuarioImports'); if (user?.value) chips.push({ key: 'user', label: 'Usuario: ' + (user.tomselect?.getItem(user.value)?.textContent?.trim() || user.value) });
        if (document.getElementById('conFallidos')?.checked) chips.push({ key: 'result', label: 'Resultado: con fallidos' });
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
        } else if (button.dataset.clearImportFilter === 'status') {
            form.querySelectorAll('.status-checkbox').forEach(input => { input.checked = false; });
        } else if (button.dataset.clearImportFilter === 'user') {
            const user = document.getElementById('usuarioImports');
            if (user?.tomselect) user.tomselect.clear(true);
            else if (user) user.value = '';
        } else if (button.dataset.clearImportFilter === 'result') {
            const failed = document.getElementById('conFallidos');
            if (failed) failed.checked = false;
        }

        window.syncImportFilterUI();
        document.getElementById('aplicarFiltrosImportaciones')?.click();
    });
    document.getElementById('aplicarFiltrosImportaciones')?.addEventListener('click', () => { setPanel(false); window.setTimeout(window.syncImportFilterUI, 0); });
    document.getElementById('limpiarFiltrosImportaciones')?.addEventListener('click', () => window.setTimeout(window.syncImportFilterUI, 0));
    window.syncImportFilterUI();
});
</script>
@endonce
