@props(['districts' => null, 'municipalities' => null, 'causes' => null])

@php
    $dateRangeValue = request('dateRange', 'all');
    $sexValue = strtoupper((string) request('sexo', ''));
    $selectedMonths = (array) request('selectedMonths', []);
    $months = ['01'=>'Ene','02'=>'Feb','03'=>'Mar','04'=>'Abr','05'=>'May','06'=>'Jun','07'=>'Jul','08'=>'Ago','09'=>'Sep','10'=>'Oct','11'=>'Nov','12'=>'Dic'];
@endphp

<section class="users-filter-card statistics-filter-toolbar" aria-label="Controles de datos de defunciones">
    <form id="filters-form" method="GET" action="{{ route('statistic.data') }}" class="users-filter-form">
        <div class="users-filter-topbar">
                <div class="users-filter-popover-wrap">
                    <x-filtros.boton id="deathsFilterToggle" controls="deathsFilterPanel" count-id="deathsFilterCount" />

                    <x-filtros.panel id="deathsFilterPanel" clear-id="limpiarFiltros" cancel-id="closeDeathsFilters" apply-type="submit">
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
                            <div class="users-filter-section is-open" data-filter-section>
                                <button type="button" class="users-filter-section-toggle" data-filter-section-toggle>
                                    <i class="fas fa-chevron-down" aria-hidden="true"></i><span>Fecha de defunción</span>
                                </button>
                                <div class="users-filter-section-content">
                                    <div class="statistics-date-picker">
                                        <div class="statistics-filter-field">
                                            <label for="dateRange">Periodo</label>
                                            <x-filtros.select name="dateRange" id="dateRange" placeholder="Todas las fechas" aria-describedby="statisticsDateModeHelp">
                                                <option value="all" @selected($dateRangeValue === 'all')>Todas las fechas</option>
                                                <option value="years" @selected(in_array($dateRangeValue, ['year','years'], true))>Por año</option>
                                                <option value="months" @selected(in_array($dateRangeValue, ['month','months','multiple-months'], true))>Por meses</option>
                                                <option value="quarter" @selected($dateRangeValue === 'quarter')>Por trimestre</option>
                                                <option value="custom" @selected($dateRangeValue === 'custom')>Rango personalizado</option>
                                            </x-filtros.select>
                                            <p id="statisticsDateModeHelp" class="statistics-date-mode-help">Muestra todos los registros disponibles.</p>
                                        </div>
                                    </div>
                                    <div id="statisticsDateContext" class="statistics-date-context">
                                        <div class="statistics-filter-detail" data-date-detail="year">
                                            <label id="statisticsYearLabel" for="year">Año o periodo</label>
                                            <input type="text" id="year" name="year" value="{{ request('year') }}" placeholder="Ej. {{ now()->year }} o 2024-2026" inputmode="numeric" aria-describedby="statisticsYearHelp">
                                            <small id="statisticsYearHelp" class="statistics-filter-help">Sin año, este periodo no se aplicará.</small>
                                        </div>
                                        <div class="statistics-filter-detail" data-date-detail="months">
                                            <input type="hidden" id="monthHidden" name="month" value="{{ request('month') }}">
                                            <span class="statistics-filter-label">Meses</span>
                                            <div class="statistics-month-options">
                                                @foreach($months as $value => $label)
                                                    <label><input type="checkbox" name="selectedMonths[]" value="{{ $value }}" @checked(in_array($value, $selectedMonths))><span>{{ $label }}</span></label>
                                                @endforeach
                                            </div>
                                        </div>
                                        <div class="statistics-filter-detail" data-date-detail="quarter">
                                            <label for="quarter">Trimestre</label>
                                            <x-filtros.select id="quarter" name="quarter" placeholder="Seleccionar trimestre">
                                                <option value="">Seleccionar trimestre</option>
                                                <option value="1" @selected(request('quarter') === '1')>Q1 (Ene–Mar)</option>
                                                <option value="2" @selected(request('quarter') === '2')>Q2 (Abr–Jun)</option>
                                                <option value="3" @selected(request('quarter') === '3')>Q3 (Jul–Sep)</option>
                                                <option value="4" @selected(request('quarter') === '4')>Q4 (Oct–Dic)</option>
                                            </x-filtros.select>
                                        </div>
                                        <div class="statistics-filter-detail statistics-filter-date-grid" data-date-detail="custom">
                                            <label>Desde<input type="date" id="startDate" name="startDate" value="{{ request('startDate') }}"></label>
                                            <label>Hasta<input type="date" id="endDate" name="endDate" value="{{ request('endDate') }}"></label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="users-filter-section {{ request('distrito') || request('municipio') || request('municipioDefuncion') ? 'is-open' : '' }}" data-filter-section>
                                <button type="button" class="users-filter-section-toggle" data-filter-section-toggle>
                                    <i class="fas {{ request('distrito') || request('municipio') || request('municipioDefuncion') ? 'fa-chevron-down' : 'fa-chevron-right' }}" aria-hidden="true"></i><span>Ubicación</span>
                                </button>
                                <div class="users-filter-section-content">
                                    <div class="statistics-filter-fields">
                                        <div class="statistics-filter-field">
                                            <label for="distrito">Distrito de residencia</label>
                                            <x-filtros.select id="distrito" name="distrito" placeholder="Todos"><option value="">Todos</option>@foreach($districts ?? [] as $district)<option value="{{ $district->name }}" @selected(request('distrito') === $district->name)>{{ $district->name }}</option>@endforeach</x-filtros.select>
                                        </div>
                                        <div class="statistics-filter-field">
                                            <label for="municipio">Municipio de residencia</label>
                                            <x-filtros.select id="municipio" name="municipio" placeholder="Todos"><option value="">Todos</option>@foreach($municipalities ?? [] as $municipality)<option value="{{ $municipality->name }}" @selected(request('municipio') === $municipality->name)>{{ $municipality->name }}</option>@endforeach</x-filtros.select>
                                        </div>
                                        <div class="statistics-filter-field">
                                            <label for="municipioDefuncion">Municipio de defunción</label>
                                            <x-filtros.select id="municipioDefuncion" name="municipioDefuncion" placeholder="Todos"><option value="">Todos</option>@foreach($municipalities ?? [] as $municipality)<option value="{{ $municipality->name }}" @selected(request('municipioDefuncion') === $municipality->name)>{{ $municipality->name }}</option>@endforeach</x-filtros.select>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="users-filter-section {{ request('sexo') || request('edad') ? 'is-open' : '' }}" data-filter-section>
                                <button type="button" class="users-filter-section-toggle" data-filter-section-toggle>
                                    <i class="fas {{ request('sexo') || request('edad') ? 'fa-chevron-down' : 'fa-chevron-right' }}" aria-hidden="true"></i><span>Datos demográficos</span>
                                </button>
                                <div class="users-filter-section-content">
                                    <div class="users-filter-options">
                                        @foreach([''=>'Todos','F'=>'Femenino','M'=>'Masculino'] as $value => $label)
                                            <button type="button" class="users-filter-option" data-filter-target="sexo" data-filter-value="{{ $value }}"><span class="users-filter-check"><i class="fas fa-check" aria-hidden="true"></i></span><span>{{ $label }}</span></button>
                                        @endforeach
                                    </div>
                                    <div class="statistics-filter-detail is-visible"><label for="edad">Edad</label><input type="text" id="edad" name="edad" value="{{ request('edad') }}" placeholder="Ej. 25, 20-30 o 5,10,15"></div>
                                </div>
                            </div>

                            <div class="users-filter-section {{ request('causa') ? 'is-open' : '' }}" data-filter-section>
                                <button type="button" class="users-filter-section-toggle" data-filter-section-toggle>
                                    <i class="fas {{ request('causa') ? 'fa-chevron-down' : 'fa-chevron-right' }}" aria-hidden="true"></i><span>Causa de defunción</span>
                                </button>
                                <div class="users-filter-section-content">
                                    <x-filtros.select id="causa" name="causa" placeholder="Todas" aria-label="Causa de defunción"><option value="">Todas</option>@foreach($causes ?? [] as $cause)<option value="{{ $cause->id }}" @selected(request('causa') == $cause->id)>{{ $cause->name }}</option>@endforeach</x-filtros.select>
                                </div>
                            </div>
                    </x-filtros.panel>
                </div>

            <div class="users-filter-search">
                <i class="fas fa-search" aria-hidden="true"></i>
                <input type="search" id="dt-search-deaths" placeholder="Buscar defunciones..." aria-label="Buscar defunciones" autocomplete="off" autocorrect="off" autocapitalize="none" spellcheck="false" enterkeyhint="search" aria-busy="false">
                <span class="users-search-progress" aria-hidden="true"></span>
                <button type="button" id="dt-clear-deaths-btn" class="hidden" title="Limpiar búsqueda" aria-label="Limpiar búsqueda"><i class="fas fa-times" aria-hidden="true"></i></button>
            </div>

            <div class="app-table-page-size users-filter-page-size">
                <select id="dt-per-page-deaths" class="users-native-page-size" aria-hidden="true" tabindex="-1">
                    <option value="10" selected>10</option><option value="25">25</option><option value="50">50</option><option value="100">100</option>
                </select>
                <div class="users-page-size-dropdown">
                    <button id="dt-per-page-deaths-button" type="button" class="users-page-size-button" aria-haspopup="listbox" aria-expanded="false"><span>Mostrar</span><strong id="dt-per-page-deaths-label">10</strong><i class="fas fa-list-ul users-page-size-icon" aria-hidden="true"></i></button>
                    <div id="dt-per-page-deaths-menu" class="users-page-size-menu hidden" role="listbox" aria-labelledby="dt-per-page-deaths-button">
                        @foreach([10,25,50,100] as $size)<button type="button" role="option" class="users-page-size-option {{ $size === 10 ? 'is-active' : '' }}" data-value="{{ $size }}">{{ $size }}</button>@endforeach
                    </div>
                </div>
            </div>
        </div>

        <div id="deathsFilterChips" class="users-filter-chips" aria-live="polite"></div>
    </form>
</section>

@once
<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('filters-form');
    const panel = document.getElementById('deathsFilterPanel');
    const toggle = document.getElementById('deathsFilterToggle');
    if (!form || !panel || !toggle) return;

    const getControl = (name) => form.querySelector(`[name="${name}"]`);
    const selectedText = (control) => control?.options?.[control.selectedIndex]?.text?.trim() || '';
    const escapeText = (value) => String(value ?? '').replace(/[&<>"']/g, char => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;'}[char]));
    const monthNames = { '01':'Ene', '02':'Feb', '03':'Mar', '04':'Abr', '05':'May', '06':'Jun', '07':'Jul', '08':'Ago', '09':'Sep', '10':'Oct', '11':'Nov', '12':'Dic' };

    function setPanel(open) {
        panel.classList.toggle('is-collapsed', !open);
        toggle.setAttribute('aria-expanded', String(open));
    }

    toggle.addEventListener('click', () => setPanel(panel.classList.contains('is-collapsed')));
    document.getElementById('closeDeathsFilters')?.addEventListener('click', () => setPanel(false));
    document.addEventListener('click', event => {
        if (!event.target.closest('.users-filter-popover-wrap')) setPanel(false);
    });
    document.addEventListener('keydown', event => { if (event.key === 'Escape') setPanel(false); });

    form.querySelectorAll('[data-filter-section]').forEach(section => {
        section.querySelector('[data-filter-section-toggle]')?.addEventListener('click', function () {
            section.classList.toggle('is-open');
            const icon = this.querySelector('i');
            icon?.classList.toggle('fa-chevron-down', section.classList.contains('is-open'));
            icon?.classList.toggle('fa-chevron-right', !section.classList.contains('is-open'));
        });
    });

    function setFilterValue(name, value) {
        const control = getControl(name);
        if (!control) return;
        if (control.tomselect) control.tomselect.setValue(value, true);
        else control.value = value;
    }

    function updateDateDetails() {
        const value = getControl('dateRange')?.value || 'all';
        const help = document.getElementById('statisticsDateModeHelp');
        const context = document.getElementById('statisticsDateContext');
        const descriptions = {
            all: 'Muestra todos los registros disponibles.',
            years: 'Puede consultar un año, varios años o un periodo continuo.',
            months: 'Seleccione uno o varios meses dentro del año indicado.',
            quarter: 'Seleccione el año y el trimestre que desea consultar.',
            custom: 'Defina una fecha inicial, una fecha final o ambas.'
        };

        if (help) help.textContent = descriptions[value] || descriptions.all;
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
        const chips = [];
        const addSelect = (name, prefix, defaults = ['']) => {
            const control = getControl(name);
            if (control && !defaults.includes(String(control.value))) chips.push({ label: `${prefix}: ${selectedText(control)}`, fields: [name] });
        };
        const dateMode = getControl('dateRange')?.value || 'all';
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
                label: dateLabel,
                fields: ['dateRange','year','month','selectedMonths[]','quarter','startDate','endDate']
            });
        }
        addSelect('distrito', 'Distrito');
        addSelect('municipio', 'Municipio res.');
        addSelect('municipioDefuncion', 'Municipio def.');
        addSelect('sexo', 'Sexo');
        addSelect('causa', 'Causa');
        if (getControl('edad')?.value) chips.push({ label: `Edad: ${getControl('edad').value}`, fields: ['edad'] });

        const container = document.getElementById('deathsFilterChips');
        const count = document.getElementById('deathsFilterCount');
        container.innerHTML = chips.map(chip => `<span class="users-filter-chip">${escapeText(chip.label)}<button type="button" data-clear-filter="${chip.fields.join(',')}" aria-label="Quitar ${escapeText(chip.label)}"><i class="fas fa-times" aria-hidden="true"></i></button></span>`).join('');
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
        form.reset();
        form.querySelectorAll('select[data-filter-select]').forEach(select => select.tomselect?.clear(true));
        setFilterValue('dateRange', 'all');
        updateChips();
        form.dispatchEvent(new Event('submit', { bubbles: true, cancelable: true }));
        setPanel(false);
    });

    document.getElementById('deathsFilterChips')?.addEventListener('click', function (event) {
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

    const pageButton = document.getElementById('dt-per-page-deaths-button');
    const pageMenu = document.getElementById('dt-per-page-deaths-menu');
    pageButton?.addEventListener('click', event => {
        event.stopPropagation();
        const open = pageMenu.classList.toggle('hidden') === false;
        pageButton.setAttribute('aria-expanded', String(open));
    });
    pageMenu?.addEventListener('click', event => {
        const option = event.target.closest('[data-value]');
        if (!option) return;
        const value = option.dataset.value;
        document.getElementById('dt-per-page-deaths').value = value;
        document.getElementById('dt-per-page-deaths-label').textContent = value;
        pageMenu.querySelectorAll('.users-page-size-option').forEach(item => item.classList.toggle('is-active', item === option));
        document.getElementById('dt-per-page-deaths').dispatchEvent(new Event('change', { bubbles: true }));
        pageMenu.classList.add('hidden');
        pageButton.setAttribute('aria-expanded', 'false');
    });
    document.addEventListener('click', event => {
        if (!event.target.closest('.users-page-size-dropdown')) {
            pageMenu?.classList.add('hidden');
            pageButton?.setAttribute('aria-expanded', 'false');
        }
    });

    form.addEventListener('change', updateChips);
    form.addEventListener('submit', () => setPanel(false));
    updateChips();
});
</script>
@endonce
