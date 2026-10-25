@extends('layouts.principal')
@section('title', 'Editar defunción')
@section('content')

    @include('components.header-admin')
    @include('components.nav-estadisticas')

    @php
        $selectedResidenceMunicipality = (string) old('residence_municipality_id', $defuncion->residence_municipality_id ?? '');
        $selectedDeathMunicipality = (string) old('death_municipality_id', $defuncion->death_municipality_id ?? '');
        $selectedDeathLocation = (string) old('death_location_id', $defuncion->death_location_id ?? '');
        $selectedDeathCause = (string) old('death_cause_id', $defuncion->death_cause_id ?? '');
        $selectedDistrict = (string) old('district_id', $defuncion->district_id ?? '');
        if ($selectedDistrict === '' && $selectedResidenceMunicipality !== '') {
            $selectedDistrict = (string) optional($municipalities->firstWhere('id', (int) $selectedResidenceMunicipality))->district_id;
        }
        $selectedDistrictName = $selectedDistrict !== ''
            ? optional($districts->firstWhere('id', (int) $selectedDistrict))->display_name
            : null;
        $selectedDeathDistrict = (string) old('death_district_id', $defuncion->death_district_id ?? '');
        if ($selectedDeathDistrict === '' && $selectedDeathMunicipality !== '') {
            $selectedDeathDistrict = (string) optional($municipalities->firstWhere('id', (int) $selectedDeathMunicipality))->district_id;
        }
        $selectedDeathDistrictName = $selectedDeathDistrict !== ''
            ? optional($districts->firstWhere('id', (int) $selectedDeathDistrict))->display_name
            : null;

        $selectedSex = strtolower((string) old('sex', $defuncion->sex ?? ''));
        if (in_array($selectedSex, ['m', 'hombre'], true)) $selectedSex = 'masculino';
        if (in_array($selectedSex, ['f', 'mujer'], true)) $selectedSex = 'femenino';

        $selectedAgeValue = old('edad_valor');
        $selectedAgeUnit = old('edad_unidad');
        if ($selectedAgeValue === null) {
            if ($defuncion->age_days !== null) {
                $selectedAgeValue = $defuncion->age_days;
                $selectedAgeUnit = 'dias';
            } elseif ($defuncion->age_months !== null) {
                $selectedAgeValue = $defuncion->age_months;
                $selectedAgeUnit = 'meses';
            } elseif ($defuncion->age_years !== null) {
                $selectedAgeValue = $defuncion->age_years;
                $selectedAgeUnit = 'anos';
            } else {
                $selectedAgeValue = $defuncion->age ?? '';
                $selectedAgeUnit = 'anos';
            }
        }

        $selectedDeathDate = old(
            'death_date',
            $defuncion->death_date ? \Carbon\Carbon::parse($defuncion->death_date)->format('Y-m-d') : ''
        );

        $statisticsDataPath = parse_url(route('statistic.data'), PHP_URL_PATH);
        $requestedReturnTo = request('return_to');
        $statisticsReturnUrl = is_string($requestedReturnTo)
            && str_starts_with($requestedReturnTo, '/')
            && parse_url($requestedReturnTo, PHP_URL_PATH) === $statisticsDataPath
                ? $requestedReturnTo
                : route('statistic.data');
    @endphp

    <x-ui.form.page
        title="Editar defunción"
        description="Modifique los campos necesarios y guarde los cambios."
        :back-href="$statisticsReturnUrl"
        back-label="Volver a datos de defunciones"
        :prefer-history-back="true"
        history-back-scope="deaths"
        page-class="statistics-death-form-page"
    >
            <form id="death-update-form" method="POST" action="{{ route('statistic.update', $defuncion) }}" novalidate data-ui-inline-validation>
                @csrf
                @method('PUT')

                @include('estadisticas.acciones.partials.death-form-fields')

                <x-form-buttons class="users-form-actions" primaryText="Guardar cambios" secondaryText="Restablecer cambios"
                    primaryType="submit" secondaryType="button" secondaryOnclick="resetDeathEditForm(event)" />
            </form>
    </x-ui.form.page>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('death-update-form');
    if (!form || typeof TomSelect === 'undefined') return;

    const municipalityToDistrict = @json($municipalities->mapWithKeys(fn ($municipality) => [(string) $municipality->id => (string) $municipality->district_id]));
    const districtNames = @json($districts->mapWithKeys(fn ($district) => [(string) $district->id => $district->display_name]));
    const residenceMunicipality = document.getElementById('residence_municipality_select');
    const deathMunicipality = document.getElementById('death_municipality_select');
    const districtInput = document.getElementById('jurisdiction_input');
    const districtDisplay = document.getElementById('jurisdiction_display');
    const deathDistrictInput = document.getElementById('death_district_input');
    const deathDistrictDisplay = document.getElementById('death_district_display');
    const ageValue = document.getElementById('edad_valor');
    const ageUnit = document.getElementById('edad_unidad');
    const requiredSelects = [
        [document.getElementById('sex'), 'Seleccione el sexo.', 'sex-client-error'],
        [ageUnit, 'Seleccione la unidad de la edad.', 'age-client-error'],
        [residenceMunicipality, 'Seleccione el municipio de residencia.', 'residence-municipality-client-error'],
        [document.getElementById('death_municipality_select'), 'Seleccione el municipio de defunción.', 'death-municipality-client-error'],
        [document.getElementById('death_municipality_location'), 'Seleccione el lugar de defunción.', 'death-location-client-error'],
        [document.getElementById('death_cause'), 'Seleccione la causa de la defunción.', 'death-cause-client-error']
    ];
    const selects = requiredSelects.map(([select]) => select).filter(Boolean);
    let validationAttempted = false;

    function selectErrorElement(select) {
        return requiredSelects.find(([field]) => field === select)?.[2] || null;
    }

    function clearSelectError(select) {
        if (!select) return;
        select.removeAttribute('aria-invalid');
        select.tomselect?.control_input?.setCustomValidity('');
        const error = document.getElementById(selectErrorElement(select));
        if (error) { error.textContent = ''; error.classList.add('hidden'); }
    }

    function validateRequiredSelect(select, message, errorId) {
        if (select?.value) { clearSelectError(select); return true; }
        if (!select) return false;
        select.setAttribute('aria-invalid', 'true');
        select.tomselect?.control_input?.setCustomValidity(message);
        const error = document.getElementById(errorId);
        if (error) { error.textContent = message; error.classList.remove('hidden'); }
        return false;
    }

    function initializeSelect(select, searchable) {
        if (!select || select.tomselect) return;
        const initialValue = select.value;
        const options = {
            valueField: 'value', labelField: 'text', searchField: searchable ? ['text'] : [],
            searchable, create: false, maxItems: 1, maxOptions: 100, allowEmptyOption: false
        };
        const instance = window.AppFilterSelect?.init(select, options) ?? new TomSelect(select, options);
        instance.wrapper.classList.add('app-filter-select', 'app-filter-select--form');
        instance.wrapper.classList.toggle('is-searchable', searchable);
        select.dataset.initialValue = initialValue;
        instance.on('change', function () {
            const definition = requiredSelects.find(([field]) => field === select);
            if (validationAttempted && definition) {
                validateRequiredSelect(...definition);
                return;
            }
            clearSelectError(select);
        });
    }

    initializeSelect(document.getElementById('sex'), false);
    initializeSelect(ageUnit, false);
    initializeSelect(residenceMunicipality, true);
    initializeSelect(document.getElementById('death_municipality_select'), true);
    initializeSelect(document.getElementById('death_municipality_location'), true);
    initializeSelect(document.getElementById('death_cause'), true);

    function updateDistricts() {
        const districtId = municipalityToDistrict[String(residenceMunicipality?.value || '')] || '';
        if (districtInput) districtInput.value = districtId;
        if (districtDisplay) districtDisplay.value = districtNames[districtId] || 'Seleccione un municipio primero';

        const deathDistrictId = municipalityToDistrict[String(deathMunicipality?.value || '')] || '';
        if (deathDistrictInput) deathDistrictInput.value = deathDistrictId;
        if (deathDistrictDisplay) deathDistrictDisplay.value = districtNames[deathDistrictId] || 'Seleccione un municipio primero';
    }
    residenceMunicipality?.tomselect?.on('change', updateDistricts);
    deathMunicipality?.tomselect?.on('change', updateDistricts);
    updateDistricts();

    function validateAge() {
        if (!ageValue || !ageUnit) return true;
        const error = document.getElementById('age-client-error');
        const unit = ageUnit.value;
        const value = ageValue.value === '' ? null : Number(ageValue.value);
        let message = '';
        if (value === null || !Number.isInteger(value) || value < 0) message = 'Capture una edad válida.';
        else if (!unit) message = 'Seleccione la unidad de la edad.';
        else if (unit === 'anos' && value > 150) message = 'La edad en años no puede ser mayor a 150.';
        else if (unit === 'meses' && value > 11) message = 'Use años para edades de 12 meses o más.';
        else if (unit === 'dias' && value > 30) message = 'La edad en días no puede ser mayor a 30.';
        ageValue.setCustomValidity(message);
        if (error) { error.textContent = message; error.classList.toggle('hidden', !message); }
        if (message) ageValue.setAttribute('aria-invalid', 'true');
        else ageValue.removeAttribute('aria-invalid');
        return !message;
    }

    function updateAgeLimit(shouldValidate = validationAttempted) {
        const limits = { anos: 150, meses: 11, dias: 30 };
        ageValue.max = String(limits[ageUnit.value] ?? 150);
        if (shouldValidate) validateAge();
    }
    ageValue?.addEventListener('input', function () {
        if (validationAttempted || ageValue.hasAttribute('aria-invalid')) validateAge();
    });
    ageUnit?.tomselect?.on('change', updateAgeLimit);
    updateAgeLimit(false);

    const getFormSnapshot = () => JSON.stringify(
        Array.from(new FormData(form).entries())
            .filter(([name]) => !['_token', '_method'].includes(name))
            .map(([name, value]) => [name, String(value)])
    );
    const initialSnapshot = getFormSnapshot();
    const navigationState = { submitting: false, navigating: false, confirming: false };
    const hasUnsavedChanges = () => getFormSnapshot() !== initialSnapshot;

    async function confirmDiscardChanges(options) {
        if (!hasUnsavedChanges()) return true;
        if (navigationState.confirming) return false;
        navigationState.confirming = true;
        try {
            if (typeof window.confirmDialog !== 'function') return window.confirm(`${options.question}\n\n${options.description}`);
            return await window.confirmDialog({
                title: 'Descartar cambios', question: options.question, description: options.description,
                confirmText: options.confirmText, cancelText: 'Continuar editando', variant: 'warning'
            });
        } finally {
            navigationState.confirming = false;
        }
    }

    window.resetDeathEditForm = async function (event) {
        event?.preventDefault();
        const hadChanges = hasUnsavedChanges();
        const shouldReset = await confirmDiscardChanges({
            question: '¿Deseas restablecer los cambios?',
            description: 'Los campos volverán a los valores con los que abriste esta edición.',
            confirmText: 'Restablecer'
        });
        if (!shouldReset) return;
        validationAttempted = false;
        form.reset();
        selects.forEach(function (select) {
            select.tomselect?.setValue(select.dataset.initialValue || '', true);
            clearSelectError(select);
        });
        ageValue.setCustomValidity('');
        ageValue.removeAttribute('aria-invalid');
        document.getElementById('age-client-error')?.classList.add('hidden');
        form.querySelectorAll('[data-server-error]').forEach((error) => error.remove());
        updateDistricts();
        updateAgeLimit(false);
        if (hadChanges) {
            if (window.AppToast?.info) window.AppToast.info('Cambios restablecidos.');
            else window.showToast?.('Cambios restablecidos.', 'info', 4000);
        }
    };

    form.addEventListener('submit', function (event) {
        validationAttempted = true;
        let firstInvalid = validateAge() ? null : ageValue;
        requiredSelects.forEach(function (definition) {
            if (!validateRequiredSelect(...definition) && !firstInvalid) firstInvalid = definition[0];
        });
        if (!form.checkValidity() || firstInvalid) {
            event.preventDefault();
            const target = firstInvalid || form.querySelector(':invalid');
            if (target?.tomselect) {
                target.tomselect.focus();
            } else {
                target?.focus();
            }
            return;
        }
        navigationState.submitting = true;
    });

    document.addEventListener('click', async function (event) {
        const link = event.target.closest('a[href]');
        if (!link || event.defaultPrevented || event.button !== 0 || event.metaKey || event.ctrlKey || event.shiftKey || event.altKey
            || link.target === '_blank' || link.hasAttribute('download') || !hasUnsavedChanges()) return;
        const href = link.getAttribute('href');
        if (!href || href.startsWith('#') || href.startsWith('javascript:')) return;
        const destination = new URL(link.href, window.location.href);
        if (destination.origin !== window.location.origin) return;
        event.preventDefault();
        const shouldLeave = await confirmDiscardChanges({
            question: '¿Deseas salir de la edición?', description: 'Los cambios realizados no se guardarán.', confirmText: 'Salir sin guardar'
        });
        if (!shouldLeave) return;
        navigationState.navigating = true;
        if (typeof window.navigateBackOrVisit === 'function') window.navigateBackOrVisit(destination.href);
        else window.location.assign(destination.href);
    }, true);

    window.addEventListener('beforeunload', function (event) {
        if (navigationState.submitting || navigationState.navigating || !hasUnsavedChanges()) return;
        event.preventDefault();
        event.returnValue = '';
    });
});
</script>
@endpush
