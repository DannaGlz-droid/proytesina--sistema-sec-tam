@props(['districts' => null, 'municipalities' => null, 'causes' => null, 'deathLocations' => null, 'inheritedFilterChips' => []])

@php
    $dateRangeValue = request('dateRange', 'all');
    $sexValue = strtoupper((string) request('sexo', ''));
    $selectedMonths = (array) request('selectedMonths', []);
    $months = ['01'=>'Ene','02'=>'Feb','03'=>'Mar','04'=>'Abr','05'=>'May','06'=>'Jun','07'=>'Jul','08'=>'Ago','09'=>'Sep','10'=>'Oct','11'=>'Nov','12'=>'Dic'];
    $residenceDistricts = collect($districts ?? [])
        ->reject(fn ($district) => strtoupper(trim((string) $district->name)) === \App\Models\District::OTHER_NAME)
        ->values();
    $residenceMunicipalities = collect($municipalities ?? [])
        ->reject(fn ($municipality) => strtoupper(trim((string) $municipality->name)) === 'OTRO')
        ->values();
@endphp

<section class="users-filter-card statistics-filter-toolbar" aria-label="Controles de datos de defunciones">
    <form id="filters-form" method="GET" action="{{ route('statistic.data') }}" class="users-filter-form">
        <div class="users-filter-topbar">
                <div class="users-filter-popover-wrap">
                    <x-filtros.boton id="deathsFilterToggle" controls="deathsFilterPanel" count-id="deathsFilterCount" class="app-standard-filter-toggle" />

                    <x-filtros.panel id="deathsFilterPanel" clear-id="limpiarFiltros" cancel-id="closeDeathsFilters" apply-type="submit" class="app-standard-filter-panel">
                        <x-slot:beforeBody>
                        <div class="users-filter-native-controls" aria-hidden="true">
                            <select name="sexo" id="sexo" tabindex="-1">
                                <option value="" @selected($sexValue === '')>Todos</option>
                                <option value="F" @selected($sexValue === 'F')>Femenino</option>
                                <option value="M" @selected($sexValue === 'M')>Masculino</option>
                            </select>
                        </div>

                        <p id="statistics-filter-error" class="statistics-inline-error hidden" role="alert"></p>
                        </x-slot:beforeBody>
                            <div class="users-filter-section is-open" data-filter-section="date">
                                <button type="button" class="users-filter-section-toggle" data-filter-section-toggle>
                                    <i class="fas fa-chevron-down" aria-hidden="true"></i><span>Fecha de defunción</span>
                                </button>
                                <div class="users-filter-section-content">
                                    <div class="statistics-date-picker">
                                        <div class="statistics-filter-field">
                                            <label for="dateRange">Periodo</label>
                                            <x-filtros.select name="dateRange" id="dateRange" placeholder="Todas las fechas">
                                                <option value="all" @selected($dateRangeValue === 'all')>Todas las fechas</option>
                                                <option value="years" @selected(in_array($dateRangeValue, ['year','years'], true))>Por año</option>
                                                <option value="months" @selected(in_array($dateRangeValue, ['month','months','multiple-months'], true))>Por meses</option>
                                                <option value="quarter" @selected($dateRangeValue === 'quarter')>Por trimestre</option>
                                                <option value="custom" @selected($dateRangeValue === 'custom')>Rango personalizado</option>
                                            </x-filtros.select>
                                        </div>
                                    </div>
                                    <div id="statisticsDateContext" class="statistics-date-context">
                                        <div class="statistics-filter-detail" data-date-detail="year">
                                            <div class="statistics-filter-label-row">
                                                <label id="statisticsYearLabel" for="year">Año o periodo</label>
                                                <x-ui.form.help-tooltip id="statisticsYearFormatHelp" label="Formatos admitidos para el año">
                                                    Escriba un año ({{ now()->year }}), varios separados por comas (2024, 2026) o un periodo continuo (2024-2026).
                                                </x-ui.form.help-tooltip>
                                            </div>
                                            <input type="text" id="year" name="year" value="{{ request('year') }}" placeholder="Ej. {{ now()->year }} o 2024-2026" inputmode="numeric" aria-describedby="statisticsYearFormatHelp statisticsYearScopeHelp">
                                            <small id="statisticsYearScopeHelp" class="statistics-filter-help hidden"></small>
                                        </div>
                                        <div class="statistics-filter-detail" data-date-detail="months">
                                            <input type="hidden" id="monthHidden" name="month" value="{{ request('month') }}">
                                            <span class="statistics-filter-label">Meses</span>
                                            <div class="statistics-month-options" data-filter-date-proxy="selectedMonths[]" tabindex="-1">
                                                @foreach($months as $value => $label)
                                                    <label><input type="checkbox" name="selectedMonths[]" value="{{ $value }}" aria-describedby="statistics-filter-error" @checked(in_array($value, $selectedMonths))><span>{{ $label }}</span></label>
                                                @endforeach
                                            </div>
                                        </div>
                                        <div class="statistics-filter-detail" data-date-detail="quarter">
                                            <label for="quarter">Trimestre</label>
                                            <x-filtros.select id="quarter" name="quarter" placeholder="Seleccionar trimestre" :allow-empty-option="true" :searchable="false" aria-describedby="statistics-filter-error">
                                                <option value="">Seleccionar trimestre</option>
                                                <option value="1" @selected(request('quarter') === '1')>Q1 (Ene–Mar)</option>
                                                <option value="2" @selected(request('quarter') === '2')>Q2 (Abr–Jun)</option>
                                                <option value="3" @selected(request('quarter') === '3')>Q3 (Jul–Sep)</option>
                                                <option value="4" @selected(request('quarter') === '4')>Q4 (Oct–Dic)</option>
                                            </x-filtros.select>
                                        </div>
                                        <div class="statistics-filter-detail statistics-filter-date-grid" data-date-detail="custom">
                                            <label>Desde<input type="date" id="startDate" name="startDate" value="{{ request('startDate') }}" max="{{ now()->toDateString() }}" aria-describedby="statistics-filter-error"></label>
                                            <label>Hasta<input type="date" id="endDate" name="endDate" value="{{ request('endDate') }}" max="{{ now()->toDateString() }}" aria-describedby="statistics-filter-error"></label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="users-filter-section {{ request('distrito') || request('distritoDefuncion') || request('municipio') || request('municipioDefuncion') || request('lugar') ? 'is-open' : '' }}" data-filter-section="location">
                                <button type="button" class="users-filter-section-toggle" data-filter-section-toggle>
                                    <i class="fas {{ request('distrito') || request('distritoDefuncion') || request('municipio') || request('municipioDefuncion') || request('lugar') ? 'fa-chevron-down' : 'fa-chevron-right' }}" aria-hidden="true"></i><span>Ubicación</span>
                                </button>
                                <div class="users-filter-section-content">
                                    <div class="statistics-location-groups">
                                        <div class="statistics-location-group" role="group" aria-labelledby="statistics-location-residence-title">
                                            <h4 class="statistics-location-group-title" id="statistics-location-residence-title">Residencia</h4>
                                            <div class="statistics-filter-fields">
                                                <div class="statistics-filter-field">
                                                    <label for="distrito">Distrito</label>
                                                    <x-filtros.select id="distrito" name="distrito" placeholder="Todos"><option value="">Todos</option>@foreach($residenceDistricts as $district)<option value="{{ $district->name }}" @selected(request('distrito') === $district->name)>{{ $district->display_name }}</option>@endforeach</x-filtros.select>
                                                </div>
                                                <div class="statistics-filter-field">
                                                    <label for="municipio">Municipio</label>
                                                    <x-filtros.select id="municipio" name="municipio" placeholder="Todos"><option value="">Todos</option>@foreach($residenceMunicipalities as $municipality)<option value="{{ $municipality->name }}" @selected(request('municipio') === $municipality->name)>{{ $municipality->display_name }}</option>@endforeach</x-filtros.select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="statistics-location-group" role="group" aria-labelledby="statistics-location-death-title">
                                            <h4 class="statistics-location-group-title" id="statistics-location-death-title">Defunción</h4>
                                            <div class="statistics-filter-fields">
                                                <div class="statistics-filter-field">
                                                    <label for="distritoDefuncion">Distrito</label>
                                                    <x-filtros.select id="distritoDefuncion" name="distritoDefuncion" placeholder="Todos"><option value="">Todos</option>@foreach($districts ?? [] as $district)<option value="{{ $district->name }}" @selected(request('distritoDefuncion') === $district->name)>{{ $district->display_name }}</option>@endforeach</x-filtros.select>
                                                </div>
                                                <div class="statistics-filter-field">
                                                    <label for="municipioDefuncion">Municipio</label>
                                                    <x-filtros.select id="municipioDefuncion" name="municipioDefuncion" placeholder="Todos"><option value="">Todos</option>@foreach($municipalities ?? [] as $municipality)<option value="{{ $municipality->name }}" @selected(request('municipioDefuncion') === $municipality->name)>{{ $municipality->display_name }}</option>@endforeach</x-filtros.select>
                                                </div>
                                                <div class="statistics-filter-field">
                                                    <label for="lugar">Lugar</label>
                                                    <x-filtros.select id="lugar" name="lugar" placeholder="Todos"><option value="">Todos</option>@foreach($deathLocations ?? [] as $location)<option value="{{ $location->id }}" @selected((string) request('lugar') === (string) $location->id)>{{ $location->display_name }}</option>@endforeach</x-filtros.select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="users-filter-section {{ request('sexo') || request('edad') ? 'is-open' : '' }}" data-filter-section="demographics">
                                <button type="button" class="users-filter-section-toggle" data-filter-section-toggle>
                                    <i class="fas {{ request('sexo') || request('edad') ? 'fa-chevron-down' : 'fa-chevron-right' }}" aria-hidden="true"></i><span>Datos demográficos</span>
                                </button>
                                <div class="users-filter-section-content">
                                    <div class="users-filter-options">
                                        @foreach([''=>'Todos','F'=>'Femenino','M'=>'Masculino'] as $value => $label)
                                            <button type="button" class="users-filter-option {{ $value === '' ? 'users-filter-option--all' : '' }}" data-filter-target="sexo" data-filter-value="{{ $value }}"><span class="users-filter-check"><i class="fas fa-check" aria-hidden="true"></i></span><span>{{ $label }}</span></button>
                                        @endforeach
                                    </div>
                                    <div class="statistics-filter-detail is-visible">
                                        <div class="statistics-filter-label-row">
                                            <label for="edad">Edad</label>
                                            <x-ui.form.help-tooltip id="statisticsAgeFormatHelp" label="Formatos admitidos para la edad">
                                                Use una edad (25), un rango (20-30), varias separadas por comas (5, 10, 15) o una edad mínima (65+).
                                            </x-ui.form.help-tooltip>
                                        </div>
                                        <input type="text" id="edad" name="edad" value="{{ request('edad') }}" placeholder="Ej. 25 o 20-30" maxlength="50" aria-describedby="statisticsAgeFormatHelp">
                                    </div>
                                </div>
                            </div>

                            <div class="users-filter-section {{ request('causa') ? 'is-open' : '' }}" data-filter-section="cause">
                                <button type="button" class="users-filter-section-toggle" data-filter-section-toggle>
                                    <i class="fas {{ request('causa') ? 'fa-chevron-down' : 'fa-chevron-right' }}" aria-hidden="true"></i><span>Causa de defunción</span>
                                </button>
                                <div class="users-filter-section-content">
                                    <x-filtros.select id="causa" name="causa" placeholder="Todas" :allow-empty-option="true" aria-label="Causa de defunción"><option value="" @selected(!request()->filled('causa'))>Todas</option>@foreach($causes ?? [] as $cause)<option value="{{ $cause->id }}" @selected((string) request('causa', '') === (string) $cause->id)>{{ $cause->display_name }}</option>@endforeach</x-filtros.select>
                                </div>
                            </div>
                    </x-filtros.panel>
                </div>

            {{ $slot }}

            <x-ui.data-table.search
                id="dt-search-deaths"
                clear-id="dt-clear-deaths-btn"
                placeholder="Buscar defunciones..."
                label="Buscar defunciones"
            />

            <x-ui.data-table.page-size
                id="dt-per-page-deaths"
                button-id="dt-per-page-deaths-button"
                label-id="dt-per-page-deaths-label"
                menu-id="dt-per-page-deaths-menu"
                :options="[10, 25, 50, 100]"
                :selected="10"
            />
        </div>

        <div id="deathsFilterChips" class="ui-data-table__filter-chips users-filter-chips" aria-live="polite"></div>
    </form>
</section>

@once
<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('filters-form');
    const panel = document.getElementById('deathsFilterPanel');
    const toggle = document.getElementById('deathsFilterToggle');
    const pageSelect = document.getElementById('dt-per-page-deaths');
    const pageButton = document.getElementById('dt-per-page-deaths-button');
    const pageLabel = document.getElementById('dt-per-page-deaths-label');
    const pageMenu = document.getElementById('dt-per-page-deaths-menu');
    let deathsPageSizeController = null;
    let deathsFilterDraftSnapshot = null;
    if (!form || !panel || !toggle) return;

    const getControl = (name) => form.querySelector(`[name="${name}"]`);
    const selectedText = (control) => control?.options?.[control.selectedIndex]?.text?.trim() || '';
    const escapeText = (value) => String(value ?? '').replace(/[&<>"']/g, char => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;'}[char]));
    const monthNames = { '01':'Ene', '02':'Feb', '03':'Mar', '04':'Abr', '05':'May', '06':'Jun', '07':'Jul', '08':'Ago', '09':'Sep', '10':'Oct', '11':'Nov', '12':'Dic' };
    const inheritedFilterChips = @json(array_values($inheritedFilterChips));

    function captureDeathsFilterState() {
        return window.AppFilterPanel?.capture(form) || null;
    }

    function restoreDeathsFilterState(snapshot) {
        if (!snapshot) return;
        window.AppFilterPanel?.restore(snapshot);
        updateChips();
    }

    function setPanel(open, { discardDraft = false } = {}) {
        if (open) {
            deathsFilterDraftSnapshot = captureDeathsFilterState();
        } else if (discardDraft && deathsFilterDraftSnapshot) {
            restoreDeathsFilterState(deathsFilterDraftSnapshot);
            deathsFilterDraftSnapshot = null;
        }

        panel.classList.toggle('is-collapsed', !open);
        toggle.setAttribute('aria-expanded', String(open));
    }

    function closePageMenu() {
        if (deathsPageSizeController) {
            deathsPageSizeController.close();
            return;
        }
        pageMenu?.classList.add('hidden');
        pageButton?.setAttribute('aria-expanded', 'false');
    }

    function announceToolbarMenu(source) {
        document.dispatchEvent(new CustomEvent('statistics-toolbar-menu-open', {
            detail: { source },
        }));
    }

    const initializeDeathsPageSize = () => {
        if (deathsPageSizeController) return deathsPageSizeController;

        deathsPageSizeController = window.AppDataTable?.initPageSize({
            select: pageSelect,
            button: pageButton,
            label: pageLabel,
            menu: pageMenu,
            initialValue: pageSelect?.value,
            onOpen: () => announceToolbarMenu('page-size'),
            onChange: () => pageSelect?.dispatchEvent(new Event('change', { bubbles: true })),
        }) || null;

        return deathsPageSizeController;
    };

    if (!initializeDeathsPageSize()) {
        window.addEventListener('app:data-table-ready', initializeDeathsPageSize, { once: true });
    }

    document.addEventListener('statistics-toolbar-menu-open', event => {
        if (event.detail?.source !== 'filters') setPanel(false, { discardDraft: true });
        if (event.detail?.source !== 'page-size') closePageMenu();
    });

    toggle.addEventListener('click', () => {
        const open = panel.classList.contains('is-collapsed');
        if (open) announceToolbarMenu('filters');
        setPanel(open, { discardDraft: !open });
    });
    document.getElementById('closeDeathsFilters')?.addEventListener('click', () => {
        setPanel(false, { discardDraft: true });
    });
    document.addEventListener('click', event => {
        if (!event.target.closest('.users-filter-popover-wrap')) {
            setPanel(false, { discardDraft: true });
        }
    });
    document.addEventListener('keydown', event => {
        if (event.key !== 'Escape') return;
        setPanel(false, { discardDraft: true });
        closePageMenu();
    });

    form.querySelectorAll('[data-filter-section]').forEach(section => {
        section.querySelector('[data-filter-section-toggle]')?.addEventListener('click', function () {
            const open = !section.classList.contains('is-open');
            window.setFilterSectionExpanded?.(section, open);
            const icon = this.querySelector('i');
            icon?.classList.toggle('fa-chevron-down', open);
            icon?.classList.toggle('fa-chevron-right', !open);
        });
    });

    function setFilterValue(name, value) {
        window.AppFilterPanel?.setValue(form, name, value);
    }

    function updateDateDetails() {
        const value = getControl('dateRange')?.value || 'all';
        const scopeHelp = document.getElementById('statisticsYearScopeHelp');
        const context = document.getElementById('statisticsDateContext');

        if (scopeHelp) {
            const showScopeHelp = ['months', 'quarter'].includes(value);
            scopeHelp.textContent = showScopeHelp
                ? 'Si omite el año, se considerarán todos los años.'
                : '';
            scopeHelp.classList.toggle('hidden', !showScopeHelp);
        }
        if (context) {
            context.classList.toggle('has-detail', value !== 'all');
        }

        form.querySelectorAll('[data-date-detail]').forEach(detail => {
            const kind = detail.dataset.dateDetail;
            const visible = kind === 'year'
                ? ['years','months','quarter'].includes(value)
                : kind === value;
            detail.classList.toggle('is-visible', visible);
            detail.querySelectorAll('input, select').forEach(control => {
                control.disabled = !visible;
                if (control.tomselect) {
                    if (visible) control.tomselect.enable();
                    else control.tomselect.disable();
                }
            });
        });

        const yearLabel = document.getElementById('statisticsYearLabel');
        if (yearLabel) yearLabel.textContent = value === 'years' ? 'Año o periodo' : 'Año';
    }

    function syncOptions() {
        form.querySelectorAll('.users-filter-option[data-filter-target]').forEach(option => {
            const active = String(getControl(option.dataset.filterTarget)?.value ?? '') === String(option.dataset.filterValue ?? '');
            option.classList.toggle('is-active', active);
            option.setAttribute('aria-pressed', String(active));
        });
        updateDateDetails();
    }

    function updateChips() {
        const dateMode = getControl('dateRange')?.value || 'all';
        const customDateFocus = getControl('startDate')?.value
            ? 'startDate'
            : (getControl('endDate')?.value ? 'endDate' : 'startDate');
        const dateFocusTarget = {
            years: 'year',
            months: 'selectedMonths[]',
            quarter: 'quarter',
            custom: customDateFocus,
        }[dateMode] || 'dateRange';
        const sectionByKey = {
            date: 'date',
            residence_district: 'location',
            residence_municipality: 'location',
            death_district: 'location',
            death_municipality: 'location',
            death_location: 'location',
            sex: 'demographics',
            age: 'demographics',
            cause: 'cause',
        };
        const focusByKey = {
            date: dateFocusTarget,
            residence_district: 'distrito',
            residence_municipality: 'municipio',
            death_district: 'distritoDefuncion',
            death_municipality: 'municipioDefuncion',
            death_location: 'lugar',
            sex: 'sexo',
            age: 'edad',
            cause: 'causa',
        };
        const chips = inheritedFilterChips
            .map((chip, inheritedIndex) => ({
                ...chip,
                inheritedIndex,
                section: sectionByKey[chip.key] || chip.section || '',
                focusTarget: focusByKey[chip.key] || chip.focusTarget || '',
            }))
            .filter(chip => chip.active !== false);
        const inheritedKeys = new Set(chips.map(chip => chip.key));
        const addSelect = (name, prefix, key, defaults = ['']) => {
            if (inheritedKeys.has(key)) return;
            const control = getControl(name);
            if (control && !defaults.includes(String(control.value))) chips.push({ key, section: sectionByKey[key], focusTarget: name, label: `${prefix}: ${selectedText(control)}`, fields: [name] });
        };
        const yearValue = getControl('year')?.value?.trim() || '';
        let dateLabel = '';

        if (dateMode === 'years' && yearValue) {
            dateLabel = `Fecha: ${yearValue.replace(/-/g, '–')}`;
        } else if (dateMode === 'months') {
            const selected = Array.from(form.querySelectorAll('input[name="selectedMonths[]"]:checked'))
                .map(input => monthNames[input.value] || input.value);
            dateLabel = selected.length && yearValue
                ? `Fecha: ${selected.join(', ')} de ${yearValue.replace(/-/g, '–')}`
                : '';
        } else if (dateMode === 'quarter') {
            const quarterControl = getControl('quarter');
            const quarter = quarterControl?.value ? selectedText(quarterControl) : '';
            dateLabel = quarter && yearValue ? `Fecha: ${quarter} de ${yearValue.replace(/-/g, '–')}` : '';
        } else if (dateMode === 'custom') {
            const start = getControl('startDate')?.value || '';
            const end = getControl('endDate')?.value || '';
            const formatDate = value => value ? value.split('-').reverse().join('/') : '';
            if (start && end) dateLabel = `Fecha: ${formatDate(start)}–${formatDate(end)}`;
            else if (start) dateLabel = `Fecha: desde ${formatDate(start)}`;
            else if (end) dateLabel = `Fecha: hasta ${formatDate(end)}`;
            else dateLabel = 'Fecha: rango sin definir';
        }

        if (dateLabel) {
            chips.push({
                key: 'date',
                section: 'date',
                focusTarget: dateFocusTarget,
                label: dateLabel,
                fields: ['dateRange','year','month','selectedMonths[]','quarter','startDate','endDate']
            });
        }
        addSelect('distrito', 'Distrito de residencia', 'residence_district');
        addSelect('municipio', 'Municipio de residencia', 'residence_municipality');
        addSelect('distritoDefuncion', 'Distrito de defunción', 'death_district');
        addSelect('municipioDefuncion', 'Municipio de defunción', 'death_municipality');
        addSelect('lugar', 'Lugar de defunción', 'death_location');
        addSelect('sexo', 'Sexo', 'sex');
        addSelect('causa', 'Causa', 'cause');
        if (!inheritedKeys.has('age') && getControl('edad')?.value) chips.push({ key: 'age', section: 'demographics', focusTarget: 'edad', label: `Edad: ${getControl('edad').value}`, fields: ['edad'] });

        const container = document.getElementById('deathsFilterChips');
        const count = document.getElementById('deathsFilterCount');
        container.innerHTML = chips.map(chip => {
            const clearAttribute = Number.isInteger(chip.inheritedIndex)
                ? `data-clear-inherited-filter="${chip.inheritedIndex}"`
                : `data-clear-filter="${chip.fields.join(',')}"`;
            const label = chip.section
                ? `<button type="button" class="users-filter-chip-label" data-open-filter-section="${escapeText(chip.section)}" data-focus-filter="${escapeText(chip.focusTarget || '')}" aria-label="Editar ${escapeText(chip.label)}">${escapeText(chip.label)}</button>`
                : `<span>${escapeText(chip.label)}</span>`;
            return `<span class="users-filter-chip">${label}<button type="button" class="users-filter-chip-remove" ${clearAttribute} aria-label="Quitar ${escapeText(chip.label)}"><i class="fas fa-times" aria-hidden="true"></i></button></span>`;
        }).join('');
        count.textContent = chips.length;
        count.classList.toggle('hidden', chips.length === 0);
        syncOptions();
    }

    form.querySelectorAll('.users-filter-option[data-filter-target]').forEach(option => {
        option.addEventListener('click', function () {
            setFilterValue(this.dataset.filterTarget, this.dataset.filterValue || '');
            updateChips();
        });
    });

    form.querySelectorAll('select[data-filter-select]').forEach(select => {
        window.AppFilterSelect?.init(select, { onChange: updateChips });
    });

    document.getElementById('limpiarFiltros')?.addEventListener('click', function () {
        window.AppFilterPanel?.clear(form, [
            'dateRange',
            'year',
            'month',
            'selectedMonths[]',
            'quarter',
            'startDate',
            'endDate',
            'distrito',
            'municipio',
            'distritoDefuncion',
            'municipioDefuncion',
            'lugar',
            'sexo',
            'edad',
            'causa',
        ], { dateRange: 'all' });

        const filterError = document.getElementById('statistics-filter-error');
        if (filterError) {
            filterError.textContent = '';
            filterError.classList.add('hidden');
            delete filterError.dataset.errorSource;
        }
        document.getElementById('edad')?.removeAttribute('aria-invalid');

        const inheritedFields = inheritedFilterChips.flatMap(chip => chip.active === false ? [] : (chip.fields || []));
        const inheritedReturnKeys = inheritedFilterChips.flatMap(chip => chip.active === false ? [] : (chip.return_keys || []));
        inheritedFilterChips.forEach(chip => { chip.active = false; });
        if (inheritedFields.length) {
            document.dispatchEvent(new CustomEvent('statistics-analysis-filter-clear', {
                detail: { fields: inheritedFields, return_keys: inheritedReturnKeys, reload: false },
            }));
        }
        updateChips();
        form.dispatchEvent(new Event('submit', { bubbles: true, cancelable: true }));
        setPanel(false);
    });

    document.getElementById('deathsFilterChips')?.addEventListener('click', function (event) {
        const openButton = event.target.closest('[data-open-filter-section]');
        if (openButton) {
            event.stopPropagation();
            window.AppFilterPanel?.openSection({
                panel,
                sectionKey: openButton.dataset.openFilterSection,
                focusName: openButton.dataset.focusFilter,
                form,
                openPanel: () => setPanel(true),
            });
            return;
        }

        const inheritedButton = event.target.closest('[data-clear-inherited-filter]');
        if (inheritedButton) {
            const chip = inheritedFilterChips[Number(inheritedButton.dataset.clearInheritedFilter)];
            if (!chip || chip.active === false) return;
            chip.active = false;
            updateChips();
            document.dispatchEvent(new CustomEvent('statistics-analysis-filter-clear', {
                detail: { fields: chip.fields || [], return_keys: chip.return_keys || [], reload: true },
            }));
            return;
        }

        const button = event.target.closest('[data-clear-filter]');
        if (!button) return;
        button.dataset.clearFilter.split(',').forEach(name => {
            if (name === 'selectedMonths[]') {
                form.querySelectorAll('input[name="selectedMonths[]"]').forEach(input => { input.checked = false; });
                return;
            }
            setFilterValue(name, name === 'dateRange' ? 'all' : '');
        });
        updateChips();
        form.dispatchEvent(new Event('submit', { bubbles: true, cancelable: true }));
    });

    const ageInput = document.getElementById('edad');
    const filterError = document.getElementById('statistics-filter-error');
    const startDateInput = document.getElementById('startDate');
    const endDateInput = document.getElementById('endDate');
    const ageErrorMessage = 'Formato de edad no válido. Use 25, 20-30, 5,10,15 o 65+ (de 0 a 150).';

    function isValidAgeFilter(value) {
        const normalized = value.trim();
        if (!normalized) return true;

        const valuesAreInRange = values => values.every(item => Number(item) >= 0 && Number(item) <= 150);

        if (/^\d+$/.test(normalized)) return valuesAreInRange([normalized]);

        const range = normalized.match(/^(\d+)\s*-\s*(\d+)$/);
        if (range) return valuesAreInRange([range[1], range[2]]);

        const minimum = normalized.match(/^(\d+)\s*\+$/);
        if (minimum) return valuesAreInRange([minimum[1]]);

        if (/^\d+(?:\s*,\s*\d+)+$/.test(normalized)) {
            return valuesAreInRange(normalized.split(',').map(item => item.trim()));
        }

        return false;
    }

    ageInput?.addEventListener('input', function () {
        this.removeAttribute('aria-invalid');

        if (filterError?.dataset.errorSource === 'age') {
            filterError.textContent = '';
            filterError.classList.add('hidden');
            delete filterError.dataset.errorSource;
        }
    });

    [startDateInput, endDateInput].forEach(input => {
        input?.addEventListener('input', function () {
            window.AppFilterDateRange?.clear({
                start: startDateInput,
                end: endDateInput,
                error: filterError,
            });
        });
    });

    form.addEventListener('change', updateChips);
    form.addEventListener('submit', event => {
        const customRangeSelected = document.getElementById('dateRange')?.value === 'custom';
        const dateValidation = customRangeSelected
            ? window.AppFilterDateRange?.validate({
                start: startDateInput,
                end: endDateInput,
                error: filterError,
            })
            : { valid: true };

        if (!customRangeSelected) {
            window.AppFilterDateRange?.clear({
                start: startDateInput,
                end: endDateInput,
                error: filterError,
            });
        }

        if (dateValidation && !dateValidation.valid) {
            event.preventDefault();
            event.stopImmediatePropagation();
            window.AppToast?.warning(dateValidation.message);
            return;
        }

        if (ageInput && !isValidAgeFilter(ageInput.value)) {
            event.preventDefault();
            event.stopImmediatePropagation();
            ageInput.setAttribute('aria-invalid', 'true');
            if (filterError) {
                filterError.textContent = ageErrorMessage;
                filterError.dataset.errorSource = 'age';
                filterError.classList.remove('hidden');
            }
            ageInput.focus();
        }
    }, true);
    form.addEventListener('statistics:filters-applied', () => {
        deathsFilterDraftSnapshot = null;
        setPanel(false);
    });
    updateChips();
});
</script>
@endonce
