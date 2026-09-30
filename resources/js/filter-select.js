function resolveElement(target) {
    if (target instanceof HTMLSelectElement) return target;
    if (typeof target === 'string') return document.querySelector(target);
    return null;
}

function init(target, options = {}) {
    const select = resolveElement(target);

    if (!select || typeof window.TomSelect === 'undefined') return null;
    if (select.tomselect) return select.tomselect;

    const multiple = select.multiple;
    const searchableAttribute = select.dataset.searchable;
    const searchableFromMarkup = searchableAttribute === undefined
        ? null
        : searchableAttribute !== 'false';
    const searchable = options.searchable ?? searchableFromMarkup ?? (multiple || select.options.length > 8);
    const allowEmptyOption = options.allowEmptyOption
        ?? select.dataset.allowEmptyOption === 'true';
    const customRender = options.render || {};
    const customPlugins = options.plugins;
    const customOnBlur = options.onBlur;
    const customOnChange = options.onChange;
    const customOnDropdownClose = options.onDropdownClose;
    const customOnInitialize = options.onInitialize;

    const syncMultiSelectSummary = (instance) => {
        if (!multiple || !instance?.control) return;

        const collapsedLimit = 3;
        const hiddenCount = Math.max(0, (instance.items?.length || 0) - collapsedLimit);
        let summary = instance.control.querySelector('.app-filter-select__overflow-summary');

        if (!summary) {
            summary = document.createElement('span');
            summary.className = 'app-filter-select__overflow-summary';
            summary.setAttribute('aria-hidden', 'true');
            instance.control.insertBefore(summary, instance.control_input || null);
        }

        summary.textContent = hiddenCount > 0 ? `+${hiddenCount} más` : '';
        instance.wrapper.classList.toggle('has-overflow-items', hiddenCount > 0);
    };

    const resetClosedMultiSelectScroll = (instance) => {
        if (!multiple || !instance?.control) return;
        instance.control.scrollTop = 0;
        window.requestAnimationFrame(() => {
            window.requestAnimationFrame(() => {
                instance.control.scrollTop = 0;
            });
        });
    };

    const config = {
        valueField: 'value',
        labelField: 'text',
        searchField: searchable ? ['text'] : [],
        maxOptions: 100,
        maxItems: multiple ? null : 1,
        create: false,
        allowEmptyOption,
        placeholder: select.dataset.placeholder || (multiple ? 'Selecciona opciones' : 'Seleccionar'),
        hideSelected: false,
        closeAfterSelect: !multiple,
        plugins: customPlugins ?? (multiple ? {
            remove_button: { title: 'Eliminar esta selección' },
        } : {}),
        ...options,
        onBlur: function () {
            resetClosedMultiSelectScroll(this);
            customOnBlur?.call(this);
        },
        onChange: function (value) {
            syncMultiSelectSummary(this);
            customOnChange?.call(this, value);
        },
        onDropdownClose: function () {
            resetClosedMultiSelectScroll(this);
            customOnDropdownClose?.call(this);
        },
        onInitialize: function () {
            syncMultiSelectSummary(this);
            customOnInitialize?.call(this);
        },
        render: {
            no_results: () => '<div class="no-results">Sin resultados</div>',
            ...customRender,
        },
    };

    delete config.searchable;

    const instance = new window.TomSelect(select, config);
    const variant = select.dataset.selectVariant?.trim();

    instance.wrapper.classList.add('app-filter-select');
    if (variant && /^[a-z0-9-]+$/i.test(variant)) {
        instance.wrapper.classList.add(`app-filter-select--${variant}`);
    }
    instance.wrapper.classList.toggle('is-searchable', searchable);

    if (multiple) {
        const syncSummary = () => syncMultiSelectSummary(instance);
        instance.on('item_add', syncSummary);
        instance.on('item_remove', syncSummary);
        instance.on('clear', syncSummary);
    }

    return instance;
}

function initAll(root = document, optionsFactory = null) {
    return Array.from(root.querySelectorAll('select[data-filter-select]'))
        .map((select) => init(select, optionsFactory?.(select) || {}))
        .filter(Boolean);
}

window.AppFilterSelect = Object.freeze({ init, initAll });
window.dispatchEvent(new CustomEvent('app-filter-select:ready'));

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => initAll(), { once: true });
} else {
    initAll();
}
