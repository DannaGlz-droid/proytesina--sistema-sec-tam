@php
    $deathRecord = $defuncion ?? null;
@endphp

<x-ui.form.section title="Información de la persona" icon="far fa-user">
    <div>
        <div class="statistics-form-label-row">
            <label for="gov_folio" class="block">Folio <span class="text-red-600">*</span></label>
            <x-ui.form.help-tooltip id="gov-folio-help" label="Cómo capturar el folio">
                Capture 9 dígitos o el folio alfanumérico oficial.
            </x-ui.form.help-tooltip>
        </div>
        <input id="gov_folio" name="gov_folio" type="text" value="{{ old('gov_folio', data_get($deathRecord, 'gov_folio')) }}" required minlength="9" maxlength="17"
            pattern="([0-9]{9}|[0-9]{2}[A-Za-z][0-9]{5}[A-Za-z][0-9]{8})"
            title="Capture 9 dígitos o el folio alfanumérico oficial"
            placeholder="Ej: 230787888 o 28M19673E00000007"
            aria-describedby="gov-folio-help @error('gov_folio') gov-folio-error @enderror"
            @error('gov_folio') aria-invalid="true" @enderror>
        @error('gov_folio') <p id="gov-folio-error" class="statistics-field-error" data-server-error role="alert">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="name" class="block">Nombre(s) <span class="text-red-600">*</span></label>
        <input id="name" name="name" type="text" value="{{ old('name', data_get($deathRecord, 'name')) }}" required minlength="2" maxlength="191"
            placeholder="Ej: Juan Diego" autocomplete="given-name"
            @error('name') aria-invalid="true" aria-describedby="name-error" @enderror>
        @error('name') <p id="name-error" class="statistics-field-error" data-server-error role="alert">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="first_last_name" class="block">Apellido paterno <span class="text-red-600">*</span></label>
        <input id="first_last_name" name="first_last_name" type="text" value="{{ old('first_last_name', data_get($deathRecord, 'first_last_name')) }}" required minlength="2" maxlength="191"
            placeholder="Ej: García" autocomplete="family-name"
            @error('first_last_name') aria-invalid="true" aria-describedby="first-last-name-error" @enderror>
        @error('first_last_name') <p id="first-last-name-error" class="statistics-field-error" data-server-error role="alert">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="second_last_name" class="block">Apellido materno</label>
        <input id="second_last_name" name="second_last_name" type="text" value="{{ old('second_last_name', data_get($deathRecord, 'second_last_name')) }}" minlength="2" maxlength="191"
            placeholder="Ej: López"
            @error('second_last_name') aria-invalid="true" aria-describedby="second-last-name-error" @enderror>
        @error('second_last_name') <p id="second-last-name-error" class="statistics-field-error" data-server-error role="alert">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="sex" class="block">Sexo <span class="text-red-600">*</span></label>
        <select id="sex" name="sex" class="tomselect-select" data-placeholder="Seleccione el sexo" data-validation-error-id="sex-client-error" data-validation-required-message="Seleccione el sexo." required @error('sex') aria-invalid="true" aria-describedby="sex-error" @enderror>
            <option value="">Seleccione una opción</option>
            <option value="masculino" @selected($selectedSex === 'masculino')>Masculino</option>
            <option value="femenino" @selected($selectedSex === 'femenino')>Femenino</option>
        </select>
        <p id="sex-client-error" class="statistics-field-error hidden" role="alert"></p>
        @error('sex') <p id="sex-error" class="statistics-field-error" data-server-error role="alert">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="edad_valor" class="block">Edad <span class="text-red-600">*</span></label>
        <div class="statistics-age-fields">
            <input id="edad_valor" name="edad_valor" type="number" min="0" max="150" value="{{ $selectedAgeValue }}" required inputmode="numeric"
                data-validation-error-id="age-client-error" data-validation-required-message="Capture una edad válida." data-validation-number-message="Capture una edad válida."
                placeholder="Ej: 34" aria-label="Valor de la edad"
                @error('edad_valor') aria-invalid="true" aria-describedby="age-value-error" @enderror>
            <select id="edad_unidad" name="edad_unidad" class="tomselect-select" data-placeholder="Seleccione la unidad" data-validation-error-id="age-client-error" data-validation-required-message="Seleccione la unidad de la edad." required aria-label="Unidad de la edad"
                @error('edad_unidad') aria-invalid="true" aria-describedby="age-unit-error" @enderror>
                <option value="">Seleccione la unidad</option>
                <option value="anos" @selected($selectedAgeUnit === 'anos')>Años</option>
                <option value="meses" @selected($selectedAgeUnit === 'meses')>Meses</option>
                <option value="dias" @selected($selectedAgeUnit === 'dias')>Días</option>
            </select>
        </div>
        <p id="age-client-error" class="statistics-field-error hidden" role="alert"></p>
        @error('edad_valor') <p id="age-value-error" class="statistics-field-error" data-server-error role="alert">{{ $message }}</p> @enderror
        @error('edad_unidad') <p id="age-unit-error" class="statistics-field-error" data-server-error role="alert">{{ $message }}</p> @enderror
    </div>
</x-ui.form.section>

<x-ui.form.section title="Ubicación" icon="fas fa-location-dot">
    <div>
        <label for="residence_municipality_select" class="block">Municipio de residencia <span class="text-red-600">*</span></label>
        <select id="residence_municipality_select" name="residence_municipality_id" class="tomselect-select" data-placeholder="Seleccione un municipio" data-validation-error-id="residence-municipality-client-error" data-validation-required-message="Seleccione el municipio de residencia." required
            @error('residence_municipality_id') aria-invalid="true" aria-describedby="residence-municipality-error" @enderror>
            <option value="">Seleccione un municipio</option>
            @foreach($municipalities as $municipality)
                <option value="{{ $municipality->id }}" @selected($selectedResidenceMunicipality === (string) $municipality->id)>{{ $municipality->display_name }}</option>
            @endforeach
        </select>
        <p id="residence-municipality-client-error" class="statistics-field-error hidden" role="alert"></p>
        @error('residence_municipality_id') <p id="residence-municipality-error" class="statistics-field-error" data-server-error role="alert">{{ $message }}</p> @enderror
    </div>

    <div>
        <div class="statistics-form-label-row">
            <label for="jurisdiction_display" class="block">Distrito de residencia</label>
            <x-ui.form.help-tooltip id="jurisdiction-help" label="Cómo se asigna el distrito de residencia">
                Se asigna automáticamente según el municipio de residencia.
            </x-ui.form.help-tooltip>
        </div>
        <input id="jurisdiction_input" name="district_id" type="hidden" value="{{ $selectedDistrict }}">
        <input id="jurisdiction_display" type="text" class="ui-field--disabled" value="{{ $selectedDistrictName ?: 'Seleccione un municipio primero' }}"
            disabled aria-disabled="true" aria-describedby="jurisdiction-help">
        @error('district_id') <p class="statistics-field-error" data-server-error role="alert">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="death_municipality_select" class="block">Municipio de defunción <span class="text-red-600">*</span></label>
        <select id="death_municipality_select" name="death_municipality_id" class="tomselect-select" data-placeholder="Seleccione un municipio" data-validation-error-id="death-municipality-client-error" data-validation-required-message="Seleccione el municipio de defunción." required
            @error('death_municipality_id') aria-invalid="true" aria-describedby="death-municipality-error" @enderror>
            <option value="">Seleccione un municipio</option>
            @foreach($municipalities as $municipality)
                <option value="{{ $municipality->id }}" @selected($selectedDeathMunicipality === (string) $municipality->id)>{{ $municipality->display_name }}</option>
            @endforeach
        </select>
        <p id="death-municipality-client-error" class="statistics-field-error hidden" role="alert"></p>
        @error('death_municipality_id') <p id="death-municipality-error" class="statistics-field-error" data-server-error role="alert">{{ $message }}</p> @enderror
    </div>

    <div>
        <div class="statistics-form-label-row">
            <label for="death_district_display" class="block">Distrito de defunción</label>
            <x-ui.form.help-tooltip id="death-district-help" label="Cómo se asigna el distrito de defunción">
                Se asigna automáticamente según el municipio de defunción.
            </x-ui.form.help-tooltip>
        </div>
        <input id="death_district_input" name="death_district_id" type="hidden" value="{{ $selectedDeathDistrict }}">
        <input id="death_district_display" type="text" class="ui-field--disabled" value="{{ $selectedDeathDistrictName ?: 'Seleccione un municipio primero' }}"
            disabled aria-disabled="true" aria-describedby="death-district-help">
        @error('death_district_id') <p class="statistics-field-error" data-server-error role="alert">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="death_municipality_location" class="block">Lugar de defunción <span class="text-red-600">*</span></label>
        <select id="death_municipality_location" name="death_location_id" class="tomselect-select" data-placeholder="Seleccione un lugar" data-validation-error-id="death-location-client-error" data-validation-required-message="Seleccione el lugar de defunción." required
            @error('death_location_id') aria-invalid="true" aria-describedby="death-location-error" @enderror>
            <option value="">Seleccione un lugar</option>
            @foreach($locations as $location)
                <option value="{{ $location->id }}" @selected($selectedDeathLocation === (string) $location->id)>{{ $location->display_name }}</option>
            @endforeach
        </select>
        <p id="death-location-client-error" class="statistics-field-error hidden" role="alert"></p>
        @error('death_location_id') <p id="death-location-error" class="statistics-field-error" data-server-error role="alert">{{ $message }}</p> @enderror
    </div>
</x-ui.form.section>

<x-ui.form.section title="Información de la defunción" icon="far fa-clipboard">
    <div>
        <label for="death_cause" class="block">Causa de la defunción <span class="text-red-600">*</span></label>
        <select id="death_cause" name="death_cause_id" class="tomselect-select" data-placeholder="Seleccione una causa" data-validation-error-id="death-cause-client-error" data-validation-required-message="Seleccione la causa de la defunción." required
            @error('death_cause_id') aria-invalid="true" aria-describedby="death-cause-error" @enderror>
            <option value="">Seleccione una causa</option>
            @foreach($causes as $cause)
                <option value="{{ $cause->id }}" @selected($selectedDeathCause === (string) $cause->id)>{{ $cause->display_name }}</option>
            @endforeach
        </select>
        <p id="death-cause-client-error" class="statistics-field-error hidden" role="alert"></p>
        @error('death_cause_id') <p id="death-cause-error" class="statistics-field-error" data-server-error role="alert">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="death_date" class="block">Fecha de defunción <span class="text-red-600">*</span></label>
        <input id="death_date" name="death_date" type="date" value="{{ $selectedDeathDate }}" required max="{{ now()->format('Y-m-d') }}"
            @error('death_date') aria-invalid="true" aria-describedby="death-date-error" @enderror>
        @error('death_date') <p id="death-date-error" class="statistics-field-error" data-server-error role="alert">{{ $message }}</p> @enderror
    </div>
</x-ui.form.section>
