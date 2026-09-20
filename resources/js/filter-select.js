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
    const customRender = options.render || {};
    const customPlugins = options.plugins;

    const config = {
        valueField: 'value',
        labelField: 'text',
        searchField: searchable ? ['text'] : [],
        maxOptions: 100,
        maxItems: multiple ? null : 1,
        create: false,
        allowEmptyOption: false,
        placeholder: select.dataset.placeholder || (multiple ? 'Selecciona opciones' : 'Seleccionar'),
        hideSelected: false,
        closeAfterSelect: !multiple,
        plugins: customPlugins ?? (multiple ? {
            remove_button: { title: 'Eliminar esta selección' },
        } : {}),
        ...options,
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
