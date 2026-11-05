@props(['id', 'buttonId', 'labelId', 'menuId', 'options' => [10, 25, 50, 100], 'selected' => 25])

<div {{ $attributes->class(['ui-data-table__page-size', 'app-table-page-size', 'users-filter-page-size']) }}>
    <select id="{{ $id }}" class="users-native-page-size" aria-hidden="true" tabindex="-1">
        @foreach($options as $option)
            <option value="{{ $option }}" @selected((string) $selected === (string) $option)>{{ $option }}</option>
        @endforeach
    </select>
    <div class="users-page-size-dropdown">
        <button id="{{ $buttonId }}" type="button" class="users-page-size-button" aria-haspopup="listbox" aria-expanded="false">
            <span>Mostrar</span>
            <strong id="{{ $labelId }}">{{ $selected }}</strong>
            <i class="fas fa-list-ul users-page-size-icon" aria-hidden="true"></i>
        </button>
        <div id="{{ $menuId }}" class="users-page-size-menu hidden" role="listbox" aria-labelledby="{{ $buttonId }}">
            @foreach($options as $option)
                <button type="button" role="option"
                    class="users-page-size-option {{ (string) $selected === (string) $option ? 'is-active' : '' }}"
                    data-value="{{ $option }}"
                    aria-selected="{{ (string) $selected === (string) $option ? 'true' : 'false' }}">{{ $option }}</button>
            @endforeach
        </div>
    </div>
</div>
