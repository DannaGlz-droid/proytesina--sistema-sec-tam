/**
 * Shared behavior for the data-table design system.
 * Module-specific DataTables configuration remains in each screen.
 */
export function focusDataTableSearch(input) {
    if (!input) return;

    try {
        input.focus({ preventScroll: true });
    } catch (error) {
        input.focus();
    }

    const cursorPosition = input.value.length;
    input.setSelectionRange?.(cursorPosition, cursorPosition);
}

export function initDataTableSearch({
    input,
    clearButton,
    initialValue = input?.value ?? '',
    onSearch,
    beforeSearch,
    loadingDelay = 150,
} = {}) {
    if (!input || !clearButton || typeof onSearch !== 'function') return null;

    const control = input.closest('.ui-data-table__search, .users-filter-search');
    let appliedValue = String(initialValue ?? '').trim();
    let loadingTimer = null;
    let requestSequence = 0;

    const syncClearButton = () => {
        clearButton.classList.toggle('hidden', input.value.trim().length === 0);
    };

    const setLoading = (loading) => {
        window.clearTimeout(loadingTimer);
        loadingTimer = null;
        input.setAttribute('aria-busy', loading ? 'true' : 'false');

        if (!loading) {
            control?.classList.remove('is-searching');
            return;
        }

        loadingTimer = window.setTimeout(() => {
            control?.classList.add('is-searching');
        }, Math.max(0, Number(loadingDelay) || 0));
    };

    const search = async (nextValue = input.value, { force = false, focus = false } = {}) => {
        const normalizedValue = String(nextValue ?? '').trim();
        input.value = normalizedValue;
        syncClearButton();

        if (!force && normalizedValue === appliedValue) {
            if (focus) focusDataTableSearch(input);
            return false;
        }

        const maySearch = await beforeSearch?.(normalizedValue, appliedValue);
        if (maySearch === false) {
            input.value = appliedValue;
            syncClearButton();
            if (focus) focusDataTableSearch(input);
            return false;
        }

        const sequence = ++requestSequence;
        setLoading(true);

        try {
            await onSearch(normalizedValue, appliedValue);
            if (sequence === requestSequence) appliedValue = normalizedValue;
            return true;
        } finally {
            if (sequence === requestSequence) setLoading(false);
            syncClearButton();
            if (focus) focusDataTableSearch(input);
        }
    };

    const handleInput = () => syncClearButton();
    const handleKeydown = (event) => {
        if (event.key !== 'Enter') return;
        event.preventDefault();
        void search();
    };
    const handleClear = (event) => {
        event.preventDefault();
        void search('', { focus: true });
    };

    input.value = appliedValue;
    input.setAttribute('aria-busy', 'false');
    syncClearButton();
    input.addEventListener('input', handleInput);
    input.addEventListener('keydown', handleKeydown);
    clearButton.addEventListener('click', handleClear);

    return {
        search,
        sync: syncClearButton,
        setValue(nextValue, { focus = false } = {}) {
            appliedValue = String(nextValue ?? '').trim();
            input.value = appliedValue;
            syncClearButton();
            if (focus) focusDataTableSearch(input);
        },
        get value() {
            return appliedValue;
        },
        destroy() {
            window.clearTimeout(loadingTimer);
            input.removeEventListener('input', handleInput);
            input.removeEventListener('keydown', handleKeydown);
            clearButton.removeEventListener('click', handleClear);
            control?.classList.remove('is-searching');
            input.setAttribute('aria-busy', 'false');
        },
    };
}

export function initDataTablePageSize({
    select,
    button,
    label,
    menu,
    initialValue,
    onChange,
    onOpen,
    beforeChange,
}) {
    if (!select || !button || !label || !menu) return null;

    const options = Array.from(menu.querySelectorAll('[data-value]'));

    const update = (value) => {
        const normalizedValue = String(value);
        label.textContent = normalizedValue;
        select.value = normalizedValue;
        options.forEach((option) => {
            const isActive = option.dataset.value === normalizedValue;
            option.classList.toggle('is-active', isActive);
            option.setAttribute('aria-selected', isActive ? 'true' : 'false');
        });
    };

    const close = ({ restoreFocus = false } = {}) => {
        menu.classList.add('hidden');
        button.setAttribute('aria-expanded', 'false');
        if (restoreFocus) button.focus();
    };

    const handleDocumentClick = (event) => {
        if (!menu.contains(event.target) && !button.contains(event.target)) close();
    };

    const handleDocumentKeydown = (event) => {
        if (menu.classList.contains('hidden')) return;

        if (event.key === 'Escape') {
            close({ restoreFocus: true });
            return;
        }

        if (!['ArrowDown', 'ArrowUp', 'Home', 'End'].includes(event.key)) return;

        event.preventDefault();
        const currentIndex = Math.max(0, options.indexOf(document.activeElement));
        const nextIndex = event.key === 'Home'
            ? 0
            : event.key === 'End'
                ? options.length - 1
                : (currentIndex + (event.key === 'ArrowDown' ? 1 : -1) + options.length) % options.length;
        options[nextIndex]?.focus();
    };

    button.addEventListener('click', (event) => {
        event.stopPropagation();
        const isOpen = !menu.classList.contains('hidden');
        menu.classList.toggle('hidden', isOpen);
        button.setAttribute('aria-expanded', isOpen ? 'false' : 'true');
        if (!isOpen) {
            onOpen?.();
            const activeOption = menu.querySelector('[aria-selected="true"]') || options[0];
            window.requestAnimationFrame(() => activeOption?.focus());
        }
    });

    options.forEach((option) => {
        option.addEventListener('click', async (event) => {
            event.preventDefault();
            const mayChange = await beforeChange?.(option.dataset.value, select);
            if (mayChange === false) return;
            update(option.dataset.value);
            close();
            onChange?.(option.dataset.value, select);
        });
    });

    document.addEventListener('click', handleDocumentClick);
    document.addEventListener('keydown', handleDocumentKeydown);
    update(initialValue ?? select.value);

    return {
        close,
        update,
        destroy() {
            document.removeEventListener('click', handleDocumentClick);
            document.removeEventListener('keydown', handleDocumentKeydown);
        },
    };
}

/**
 * Shared, collision-aware row action menus for dynamic data tables.
 * The table footer is treated as a positioning boundary.
 */
export function initDataTableRowActions({
    table,
    panel = table?.closest('.ui-data-table'),
    triggerSelector = '.users-row-menu-button',
    actionsSelector = '.users-row-actions',
    menuSelector = '.users-row-menu',
    footerSelector = '.ui-data-table__footer',
    viewportMargin = 8,
    spacing = 6,
} = {}) {
    if (!table) return null;

    let openMenu = null;
    let openTrigger = null;
    let menuHost = null;

    const restore = () => {
        if (!openMenu) return;

        openMenu.classList.remove('is-viewport-positioned', 'opens-upward');
        ['top', 'right', 'bottom', 'left'].forEach((property) => openMenu.style.removeProperty(property));

        if (menuHost?.isConnected && openMenu.parentElement !== menuHost) {
            menuHost.appendChild(openMenu);
        } else if (!menuHost?.isConnected && openMenu.parentElement === document.body) {
            openMenu.remove();
        }
    };

    const close = ({ restoreFocus = false } = {}) => {
        if (!openMenu) return;

        const trigger = openTrigger;
        openMenu.classList.add('hidden');
        trigger?.setAttribute('aria-expanded', 'false');
        restore();

        openMenu = null;
        openTrigger = null;
        menuHost = null;

        if (restoreFocus) trigger?.focus();
    };

    const position = () => {
        if (!openMenu || !openTrigger) return;

        const triggerRect = openTrigger.getBoundingClientRect();
        document.body.appendChild(openMenu);
        openMenu.classList.add('is-viewport-positioned');
        openMenu.classList.remove('opens-upward');

        const menuWidth = openMenu.offsetWidth;
        const menuHeight = openMenu.offsetHeight;
        const footerRect = panel?.querySelector(footerSelector)?.getBoundingClientRect();
        const viewportBottom = window.innerHeight - viewportMargin;
        const tableBottom = footerRect && footerRect.top > triggerRect.bottom
            ? footerRect.top - spacing
            : viewportBottom;
        const lowerBoundary = Math.min(viewportBottom, tableBottom);
        const spaceBelow = Math.max(0, lowerBoundary - triggerRect.bottom - spacing);
        const spaceAbove = Math.max(0, triggerRect.top - viewportMargin - spacing);
        const opensUpward = spaceBelow < menuHeight && spaceAbove > spaceBelow;
        const left = Math.min(
            window.innerWidth - menuWidth - viewportMargin,
            Math.max(viewportMargin, triggerRect.right - menuWidth),
        );
        let top = opensUpward
            ? triggerRect.top - menuHeight - spacing
            : triggerRect.bottom + spacing;

        top = Math.min(
            Math.max(viewportMargin, top),
            Math.max(viewportMargin, viewportBottom - menuHeight),
        );

        openMenu.classList.toggle('opens-upward', opensUpward);
        openMenu.style.left = `${left}px`;
        openMenu.style.top = `${top}px`;
        openMenu.style.right = 'auto';
        openMenu.style.bottom = 'auto';
    };

    const handleTableClick = (event) => {
        const trigger = event.target.closest(triggerSelector);
        if (!trigger || !table.contains(trigger)) return;

        event.preventDefault();
        event.stopPropagation();

        const menu = trigger.closest(actionsSelector)?.querySelector(menuSelector);
        if (!menu) return;

        const wasOpen = menu === openMenu && !menu.classList.contains('hidden');
        close();
        if (wasOpen) return;

        openMenu = menu;
        openTrigger = trigger;
        menuHost = menu.parentElement;
        menu.classList.remove('hidden');
        trigger.setAttribute('aria-expanded', 'true');
        position();
    };

    const handleDocumentClick = (event) => {
        if (!openMenu) return;
        if (openMenu.contains(event.target)) {
            if (event.target.closest('.users-row-menu-item')) {
                window.requestAnimationFrame(() => close());
            }
            return;
        }
        if (!openTrigger?.contains(event.target)) close();
    };

    const handleDocumentKeydown = (event) => {
        if (event.key === 'Escape' && openMenu) close({ restoreFocus: true });
    };

    const handleViewportChange = () => close();

    // Capture the trigger before row-level handlers can stop bubbling. Data
    // tables commonly suppress button clicks to avoid opening responsive rows.
    table.addEventListener('click', handleTableClick, true);
    document.addEventListener('click', handleDocumentClick);
    document.addEventListener('keydown', handleDocumentKeydown);
    window.addEventListener('resize', handleViewportChange);
    window.addEventListener('scroll', handleViewportChange, true);

    return {
        close,
        destroy() {
            close();
            table.removeEventListener('click', handleTableClick, true);
            document.removeEventListener('click', handleDocumentClick);
            document.removeEventListener('keydown', handleDocumentKeydown);
            window.removeEventListener('resize', handleViewportChange);
            window.removeEventListener('scroll', handleViewportChange, true);
        },
    };
}
