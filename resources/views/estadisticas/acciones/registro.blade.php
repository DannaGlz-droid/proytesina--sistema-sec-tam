@extends('layouts.principal')
@section('title', 'Registrar defunción')
@section('content')

    @include('components.header-admin')
    @include('components.nav-estadisticas')

    @php
        $selectedResidenceMunicipality = (string) old('residence_municipality_id', '');
        $selectedDistrict = (string) old('district_id', '');
        if ($selectedDistrict === '' && $selectedResidenceMunicipality !== '') {
            $selectedDistrict = (string) optional($municipalities->firstWhere('id', (int) $selectedResidenceMunicipality))->district_id;
        }
        $selectedDistrictName = $selectedDistrict !== ''
            ? optional($districts->firstWhere('id', (int) $selectedDistrict))->name
            : null;
    @endphp

    <main class="users-form-page statistics-death-form-page px-4 sm:px-6 lg:px-10 pt-6 lg:pt-8 pb-8 lg:pb-10">
        <x-ui.page-header
            title="Registrar defunción"
            description="Complete la información de la persona y los datos de la defunción."
            :back-href="route('statistic.data')"
            back-label="Volver a datos de defunciones"
            :prefer-history-back="true"
        />

        <div class="users-form-card">
            <form id="death-register-form" method="POST" action="{{ route('statistic.store') }}" novalidate>
                @csrf

                <x-ui.form.section title="Información de la persona" icon="far fa-user">
                    <div>
                        <label for="gov_folio" class="block">Folio <span class="text-red-600">*</span></label>
                        <input id="gov_folio" name="gov_folio" type="text" value="{{ old('gov_folio') }}" required minlength="9" maxlength="17"
                            pattern="([0-9]{9}|[0-9]{2}[A-Za-z][0-9]{5}[A-Za-z][0-9]{8})"
                            title="Capture 9 dígitos o el folio alfanumérico oficial"
                            placeholder="Ej: 230787888 o 28M19673E00000007"
                            aria-describedby="gov-folio-help @error('gov_folio') gov-folio-error @enderror"
                            @error('gov_folio') aria-invalid="true" @enderror>
                        <p id="gov-folio-help" class="statistics-field-help">Capture 9 dígitos o el folio alfanumérico oficial.</p>
                        @error('gov_folio') <p id="gov-folio-error" class="statistics-field-error" role="alert">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="name" class="block">Nombre(s) <span class="text-red-600">*</span></label>
                        <input id="name" name="name" type="text" value="{{ old('name') }}" required minlength="2" maxlength="191"
                            placeholder="Ej: Juan Diego" autocomplete="given-name"
                            @error('name') aria-invalid="true" aria-describedby="name-error" @enderror>
                        @error('name') <p id="name-error" class="statistics-field-error" role="alert">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="first_last_name" class="block">Apellido paterno <span class="text-red-600">*</span></label>
                        <input id="first_last_name" name="first_last_name" type="text" value="{{ old('first_last_name') }}" required minlength="2" maxlength="191"
                            placeholder="Ej: García" autocomplete="family-name"
                            @error('first_last_name') aria-invalid="true" aria-describedby="first-last-name-error" @enderror>
                        @error('first_last_name') <p id="first-last-name-error" class="statistics-field-error" role="alert">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="second_last_name" class="block">Apellido materno</label>
                        <input id="second_last_name" name="second_last_name" type="text" value="{{ old('second_last_name') }}" minlength="2" maxlength="191"
                            placeholder="Ej: López"
                            @error('second_last_name') aria-invalid="true" aria-describedby="second-last-name-error" @enderror>
                        @error('second_last_name') <p id="second-last-name-error" class="statistics-field-error" role="alert">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="sex" class="block">Sexo <span class="text-red-600">*</span></label>
                        <select id="sex" name="sex" class="tomselect-select" required @error('sex') aria-invalid="true" aria-describedby="sex-error" @enderror>
                            <option value="">Seleccione una opción</option>
                            <option value="masculino" @selected(old('sex') === 'masculino')>Masculino</option>
                            <option value="femenino" @selected(old('sex') === 'femenino')>Femenino</option>
                        </select>
                        <p id="sex-client-error" class="statistics-field-error hidden" role="alert"></p>
                        @error('sex') <p id="sex-error" class="statistics-field-error" role="alert">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="edad_valor" class="block">Edad <span class="text-red-600">*</span></label>
                        <div class="statistics-age-fields">
                            <input id="edad_valor" name="edad_valor" type="number" min="0" max="150" value="{{ old('edad_valor') }}" required inputmode="numeric"
                                placeholder="Ej: 34" aria-label="Valor de la edad"
                                @error('edad_valor') aria-invalid="true" aria-describedby="age-value-error" @enderror>
                            <select id="edad_unidad" name="edad_unidad" class="tomselect-select" required aria-label="Unidad de la edad"
                                @error('edad_unidad') aria-invalid="true" aria-describedby="age-unit-error" @enderror>
                                <option value="">Seleccione la unidad</option>
                                <option value="anos" @selected(old('edad_unidad') === 'anos')>Años</option>
                                <option value="meses" @selected(old('edad_unidad') === 'meses')>Meses</option>
                                <option value="dias" @selected(old('edad_unidad') === 'dias')>Días</option>
                            </select>
                        </div>
                        <p id="age-client-error" class="statistics-field-error hidden" role="alert"></p>
                        @error('edad_valor') <p id="age-value-error" class="statistics-field-error" role="alert">{{ $message }}</p> @enderror
                        @error('edad_unidad') <p id="age-unit-error" class="statistics-field-error" role="alert">{{ $message }}</p> @enderror
                    </div>
                </x-ui.form.section>

                <x-ui.form.section title="Ubicación" icon="fas fa-location-dot">
                    <div>
                        <label for="residence_municipality_select" class="block">Municipio de residencia <span class="text-red-600">*</span></label>
                        <select id="residence_municipality_select" name="residence_municipality_id" class="tomselect-select" required
                            @error('residence_municipality_id') aria-invalid="true" aria-describedby="residence-municipality-error" @enderror>
                            <option value="">Seleccione un municipio</option>
                            @foreach($municipalities as $municipality)
                                <option value="{{ $municipality->id }}" @selected((string) old('residence_municipality_id') === (string) $municipality->id)>{{ $municipality->name }}</option>
                            @endforeach
                        </select>
                        <p id="residence-municipality-client-error" class="statistics-field-error hidden" role="alert"></p>
                        @error('residence_municipality_id') <p id="residence-municipality-error" class="statistics-field-error" role="alert">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="jurisdiction_display" class="block">Distrito de residencia</label>
                        <input id="jurisdiction_input" name="district_id" type="hidden" value="{{ $selectedDistrict }}">
                        <input id="jurisdiction_display" type="text" class="ui-field--disabled" value="{{ $selectedDistrictName ?: 'Pendiente (seleccione municipio)' }}"
                            disabled aria-disabled="true" aria-describedby="jurisdiction-help">
                        <p id="jurisdiction-help" class="statistics-field-help">Se asigna automáticamente según el municipio de residencia.</p>
                        @error('district_id') <p class="statistics-field-error" role="alert">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="death_municipality_select" class="block">Municipio de defunción <span class="text-red-600">*</span></label>
                        <select id="death_municipality_select" name="death_municipality_id" class="tomselect-select" required
                            @error('death_municipality_id') aria-invalid="true" aria-describedby="death-municipality-error" @enderror>
                            <option value="">Seleccione un municipio</option>
                            @foreach($municipalities as $municipality)
                                <option value="{{ $municipality->id }}" @selected((string) old('death_municipality_id') === (string) $municipality->id)>{{ $municipality->name }}</option>
                            @endforeach
                        </select>
                        <p id="death-municipality-client-error" class="statistics-field-error hidden" role="alert"></p>
                        @error('death_municipality_id') <p id="death-municipality-error" class="statistics-field-error" role="alert">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="death_municipality_location" class="block">Lugar específico <span class="text-red-600">*</span></label>
                        <select id="death_municipality_location" name="death_location_id" class="tomselect-select" required
                            @error('death_location_id') aria-invalid="true" aria-describedby="death-location-error" @enderror>
                            <option value="">Seleccione un lugar</option>
                            @foreach($locations as $location)
                                <option value="{{ $location->id }}" @selected((string) old('death_location_id') === (string) $location->id)>{{ $location->name }}</option>
                            @endforeach
                        </select>
                        <p id="death-location-client-error" class="statistics-field-error hidden" role="alert"></p>
                        @error('death_location_id') <p id="death-location-error" class="statistics-field-error" role="alert">{{ $message }}</p> @enderror
                    </div>
                </x-ui.form.section>

                <x-ui.form.section title="Información de la defunción" icon="far fa-clipboard">
                    <div>
                        <label for="death_cause" class="block">Causa de la defunción <span class="text-red-600">*</span></label>
                        <select id="death_cause" name="death_cause_id" class="tomselect-select" required
                            @error('death_cause_id') aria-invalid="true" aria-describedby="death-cause-error" @enderror>
                            <option value="">Seleccione una causa</option>
                            @foreach($causes as $cause)
                                <option value="{{ $cause->id }}" @selected((string) old('death_cause_id') === (string) $cause->id)>{{ $cause->name }}</option>
                            @endforeach
                        </select>
                        <p id="death-cause-client-error" class="statistics-field-error hidden" role="alert"></p>
                        @error('death_cause_id') <p id="death-cause-error" class="statistics-field-error" role="alert">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="death_date" class="block">Fecha de defunción <span class="text-red-600">*</span></label>
                        <input id="death_date" name="death_date" type="date" value="{{ old('death_date') }}" required max="{{ now()->format('Y-m-d') }}"
                            @error('death_date') aria-invalid="true" aria-describedby="death-date-error" @enderror>
                        @error('death_date') <p id="death-date-error" class="statistics-field-error" role="alert">{{ $message }}</p> @enderror
                    </div>
                </x-ui.form.section>

                <x-form-buttons class="users-form-actions" primaryText="Guardar registro" secondaryText="Limpiar formulario"
                    primaryType="submit" secondaryType="button" secondaryOnclick="clearDeathForm()" />
            </form>
        </div>
    </main>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('death-register-form');
    if (!form || typeof TomSelect === 'undefined') return;

    const municipalityToDistrict = @json($municipalities->mapWithKeys(fn ($municipality) => [(string) $municipality->id => (string) $municipality->district_id]));
    const districtNames = @json($districts->mapWithKeys(fn ($district) => [(string) $district->id => $district->name]));
    const residenceMunicipality = document.getElementById('residence_municipality_select');
    const districtInput = document.getElementById('jurisdiction_input');
    const districtDisplay = document.getElementById('jurisdiction_display');
    const ageValue = document.getElementById('edad_valor');
    const ageUnit = document.getElementById('edad_unidad');
    const requiredSelects = [
        [document.getElementById('sex'), 'Seleccione el sexo.', 'sex-client-error'],
        [ageUnit, 'Seleccione la unidad de la edad.', 'age-client-error'],
        [residenceMunicipality, 'Seleccione el municipio de residencia.', 'residence-municipality-client-error'],
        [document.getElementById('death_municipality_select'), 'Seleccione el municipio de defunción.', 'death-municipality-client-error'],
        [document.getElementById('death_municipality_location'), 'Seleccione el lugar específico.', 'death-location-client-error'],
        [document.getElementById('death_cause'), 'Seleccione la causa de la defunción.', 'death-cause-client-error']
    ];

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
        new TomSelect(select, {
            valueField: 'value', labelField: 'text', searchField: searchable ? ['text'] : [],
            create: false, maxItems: 1, maxOptions: 100, allowEmptyOption: false
        });
        select.tomselect.on('change', () => clearSelectError(select));
        select.tomselect.on('blur', function () {
            const definition = requiredSelects.find(([field]) => field === select);
            if (definition) validateRequiredSelect(...definition);
        });
    }

    initializeSelect(document.getElementById('sex'), false);
    initializeSelect(ageUnit, false);
    initializeSelect(residenceMunicipality, true);
    initializeSelect(document.getElementById('death_municipality_select'), true);
    initializeSelect(document.getElementById('death_municipality_location'), true);
    initializeSelect(document.getElementById('death_cause'), true);

    function updateDistrict() {
        const districtId = municipalityToDistrict[String(residenceMunicipality?.value || '')] || '';
        if (districtInput) districtInput.value = districtId;
        if (districtDisplay) districtDisplay.value = districtNames[districtId] || 'Pendiente (seleccione municipio)';
    }
    residenceMunicipality?.tomselect?.on('change', updateDistrict);
    updateDistrict();

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
        ageValue.toggleAttribute('aria-invalid', Boolean(message));
        return !message;
    }

    function updateAgeLimit(shouldValidate = true) {
        const limits = { anos: 150, meses: 11, dias: 30 };
        ageValue.max = String(limits[ageUnit.value] ?? 150);
        if (shouldValidate) validateAge();
    }
    ageValue?.addEventListener('blur', validateAge);
    ageValue?.addEventListener('input', function () { if (ageValue.hasAttribute('aria-invalid')) validateAge(); });
    ageUnit?.tomselect?.on('change', updateAgeLimit);
    updateAgeLimit(false);

    form.addEventListener('submit', function (event) {
        let firstInvalid = validateAge() ? null : ageValue;
        requiredSelects.forEach(function (definition) {
            if (!validateRequiredSelect(...definition) && !firstInvalid) firstInvalid = definition[0];
        });
        if (!form.checkValidity() || firstInvalid) {
            event.preventDefault();
            const target = firstInvalid || form.querySelector(':invalid');
            if (target?.tomselect) {
                target.tomselect.focus();
                target.tomselect.control_input?.reportValidity();
            } else {
                target?.focus();
                target?.reportValidity?.();
            }
            return;
        }
        const submitButton = form.querySelector('.ui-button--primary');
        if (submitButton) { submitButton.disabled = true; submitButton.textContent = 'Guardando…'; }
    });

    window.clearDeathForm = async function () {
        const canClear = typeof window.confirmFormClear === 'function' ? await window.confirmFormClear(form) : true;
        if (!canClear) return;
        form.reset();
        requiredSelects.forEach(function ([select]) { select?.tomselect?.clear(true); clearSelectError(select); });
        ageValue.value = '';
        ageValue.setCustomValidity('');
        ageValue.removeAttribute('aria-invalid');
        document.getElementById('age-client-error')?.classList.add('hidden');
        updateDistrict();
        updateAgeLimit(false);
        window.showToast?.('Formulario limpiado.', 'info', 2400);
    };
});
</script>
@endpush
