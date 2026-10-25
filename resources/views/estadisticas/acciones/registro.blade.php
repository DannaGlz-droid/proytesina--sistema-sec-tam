@extends('layouts.principal')
@section('title', 'Registrar defunción')
@section('content')

    @include('components.header-admin')
    @include('components.nav-estadisticas')

    @php
        $selectedResidenceMunicipality = (string) old('residence_municipality_id', '');
        $selectedDeathMunicipality = (string) old('death_municipality_id', '');
        $selectedDistrict = (string) old('district_id', '');
        if ($selectedDistrict === '' && $selectedResidenceMunicipality !== '') {
            $selectedDistrict = (string) optional($municipalities->firstWhere('id', (int) $selectedResidenceMunicipality))->district_id;
        }
        $selectedDistrictName = $selectedDistrict !== ''
            ? optional($districts->firstWhere('id', (int) $selectedDistrict))->display_name
            : null;
        $selectedDeathDistrict = (string) old('death_district_id', '');
        if ($selectedDeathDistrict === '' && $selectedDeathMunicipality !== '') {
            $selectedDeathDistrict = (string) optional($municipalities->firstWhere('id', (int) $selectedDeathMunicipality))->district_id;
        }
        $selectedDeathDistrictName = $selectedDeathDistrict !== ''
            ? optional($districts->firstWhere('id', (int) $selectedDeathDistrict))->display_name
            : null;
        $selectedDeathLocation = (string) old('death_location_id', '');
        $selectedDeathCause = (string) old('death_cause_id', '');
        $selectedSex = (string) old('sex', '');
        $selectedAgeValue = old('edad_valor', '');
        $selectedAgeUnit = (string) old('edad_unidad', '');
        $selectedDeathDate = old('death_date', '');

        $statisticsDataPath = parse_url(route('statistic.data'), PHP_URL_PATH);
        $requestedReturnTo = request('return_to');
        $statisticsReturnUrl = is_string($requestedReturnTo)
            && str_starts_with($requestedReturnTo, '/')
            && parse_url($requestedReturnTo, PHP_URL_PATH) === $statisticsDataPath
                ? $requestedReturnTo
                : route('statistic.data');
    @endphp

    <x-ui.form.page
        title="Registrar defunción"
        description="Complete la información de la persona y los datos de la defunción."
        :back-href="$statisticsReturnUrl"
        back-label="Volver a datos de defunciones"
        :prefer-history-back="true"
        history-back-scope="deaths"
        page-class="statistics-death-form-page"
    >
            <form id="death-register-form" method="POST" action="{{ route('statistic.store') }}" novalidate data-ui-inline-validation>
                @csrf

                @include('estadisticas.acciones.partials.death-form-fields')

                <x-form-buttons class="users-form-actions" primaryText="Guardar registro" secondaryText="Limpiar formulario"
                    primaryType="submit" secondaryType="button" secondaryOnclick="clearDeathForm()" />
            </form>
    </x-ui.form.page>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('death-register-form');
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
        const options = {
            valueField: 'value', labelField: 'text', searchField: searchable ? ['text'] : [],
            searchable, create: false, maxItems: 1, maxOptions: 100, allowEmptyOption: false
        };
        const instance = window.AppFilterSelect?.init(select, options) ?? new TomSelect(select, options);
        instance.wrapper.classList.add('app-filter-select', 'app-filter-select--form');
        instance.wrapper.classList.toggle('is-searchable', searchable);
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
    });

    window.clearDeathForm = async function () {
        const canClear = typeof window.confirmFormClear === 'function' ? await window.confirmFormClear(form) : true;
        if (!canClear) return;
        validationAttempted = false;
        form.reset();
        requiredSelects.forEach(function ([select]) { select?.tomselect?.clear(true); clearSelectError(select); });
        ageValue.value = '';
        ageValue.setCustomValidity('');
        ageValue.removeAttribute('aria-invalid');
        document.getElementById('age-client-error')?.classList.add('hidden');
        updateDistricts();
        updateAgeLimit(false);
        window.showToast?.('Formulario limpiado.', 'info', 2400);
    };
});
</script>
@endpush
