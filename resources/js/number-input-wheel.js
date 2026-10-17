function releaseFocusedNumberInput(event) {
    const input = event.target;

    if (!(input instanceof HTMLInputElement)
        || input.type !== 'number'
        || document.activeElement !== input
        || input.disabled
        || input.readOnly) {
        return;
    }

    input.blur();
}

document.addEventListener('wheel', releaseFocusedNumberInput, {
    capture: true,
    passive: true,
});
