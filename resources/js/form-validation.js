function fieldLabel(field) {
    const label = field.id
        ? field.form?.querySelector(`label[for="${CSS.escape(field.id)}"]`)
        : null;

    const fallback = (field.name || 'este campo')
        .replace(/_id$/, '')
        .replaceAll('_', ' ');

    return (label?.textContent || fallback)
        .replace('*', '')
        .trim()
        .toLocaleLowerCase('es-MX');
}

function validationMessage(field) {
    const validity = field.validity;
    const label = fieldLabel(field);

    if (validity.valueMissing) {
        if (field.dataset.validationRequiredMessage) {
            return field.dataset.validationRequiredMessage;
        }

        return field instanceof HTMLSelectElement
            ? `Seleccione ${label}.`
            : `Capture ${label}.`;
    }

    if (validity.typeMismatch && field.type === 'email') {
        return 'Capture un correo electrónico válido.';
    }

    if (validity.tooShort) {
        return `${field.labels?.[0]?.textContent.replace('*', '').trim() || 'Este campo'} debe tener al menos ${field.minLength} caracteres.`;
    }

    if (validity.rangeUnderflow || validity.rangeOverflow || validity.stepMismatch || validity.badInput) {
        return field.dataset.validationNumberMessage || `Capture un valor válido para ${label}.`;
    }

    if (validity.patternMismatch) {
        return field.dataset.validationPatternMessage || field.title || `Revise el formato de ${label}.`;
    }

    if (validity.customError) {
        return field.validationMessage;
    }

    return `Revise ${label}.`;
}

function errorAnchor(field) {
    if (field.tomselect?.wrapper) return field.tomselect.wrapper;
    if (field.closest('.statistics-age-fields')) return field.closest('.statistics-age-fields');
    if (field.parentElement?.classList.contains('relative')) return field.parentElement;
    return field;
}

function errorElement(field) {
    const explicitId = field.dataset.validationErrorId;
    const explicitError = explicitId ? document.getElementById(explicitId) : null;
    if (explicitError) return explicitError;

    const id = `${field.id || field.name}-client-error`;
    let error = document.getElementById(id);
    if (error) return error;

    error = document.createElement('p');
    error.id = id;
    error.className = 'ui-field-error hidden';
    error.setAttribute('role', 'alert');
    error.dataset.uiClientError = '';
    errorAnchor(field).insertAdjacentElement('afterend', error);
    return error;
}

function clearServerError(field) {
    const describedBy = (field.getAttribute('aria-describedby') || '').split(/\s+/).filter(Boolean);
    const remainingDescriptions = [];

    describedBy.forEach((id) => {
        const error = document.getElementById(id);
        if (error?.matches('[data-server-error]')) {
            if (error.hasAttribute('data-ui-error-slot')) {
                error.removeAttribute('data-server-error');
                error.textContent = '';
                error.classList.add('hidden');
            } else {
                error.remove();
            }
            return;
        }

        remainingDescriptions.push(id);
    });

    if (remainingDescriptions.length) field.setAttribute('aria-describedby', remainingDescriptions.join(' '));
    else field.removeAttribute('aria-describedby');
}

function syncErrorDescription(field, error, invalid) {
    if (!error?.id) return;

    const describedBy = new Set(
        (field.getAttribute('aria-describedby') || '').split(/\s+/).filter(Boolean)
    );

    if (invalid) describedBy.add(error.id);
    else describedBy.delete(error.id);

    if (describedBy.size) field.setAttribute('aria-describedby', Array.from(describedBy).join(' '));
    else field.removeAttribute('aria-describedby');
}

function setAriaInvalid(field, invalid) {
    if (invalid) field.setAttribute('aria-invalid', 'true');
    else field.removeAttribute('aria-invalid');
}

function renderFieldValidity(field) {
    const error = errorElement(field);
    const invalid = !field.validity.valid;

    setAriaInvalid(field, invalid);
    error.textContent = invalid ? validationMessage(field) : '';
    error.classList.toggle('hidden', !invalid);
    syncErrorDescription(field, error, invalid);

    return !invalid;
}

function focusField(field) {
    if (field.tomselect) {
        field.tomselect.focus();
        return;
    }

    field.focus({ preventScroll: true });
}

function validateMatches(field) {
    const targetId = field.dataset.matchField;
    if (!targetId) return;

    const target = document.getElementById(targetId);
    const mismatch = field.value && target && field.value !== target.value;
    field.setCustomValidity(mismatch ? (field.dataset.matchMessage || 'Los valores no coinciden.') : '');
}

function validationFields(form) {
    return Array.from(form.elements).filter((field) =>
        field instanceof HTMLInputElement || field instanceof HTMLSelectElement || field instanceof HTMLTextAreaElement
    ).filter((field) =>
        !field.disabled
        && field.type !== 'hidden'
        && field.willValidate
        && !field.closest('.ts-wrapper')
    );
}

function setFormSubmitting(form, submitter) {
    if (form.dataset.uiSubmitting === 'true') return;

    const button = submitter instanceof HTMLButtonElement || submitter instanceof HTMLInputElement
        ? submitter
        : form.querySelector('[data-ui-submit-button], button[type="submit"], input[type="submit"]');

    form.dataset.uiSubmitting = 'true';
    form.setAttribute('aria-busy', 'true');

    if (!button) return;

    button.style.width = `${button.getBoundingClientRect().width}px`;
    button.disabled = true;
    button.setAttribute('aria-busy', 'true');

    const loadingText = button.dataset.loadingText || 'Guardando…';
    const label = button.querySelector?.('[data-ui-submit-label]');
    const icon = button.querySelector?.('[data-ui-submit-icon]');
    const spinner = button.querySelector?.('[data-ui-submit-spinner]');

    if (icon) icon.hidden = true;
    if (spinner) spinner.hidden = false;

    if (label) {
        label.textContent = loadingText;
    } else if (button instanceof HTMLInputElement) {
        button.value = loadingText;
    } else {
        button.textContent = loadingText;
    }
}

function initializeInlineValidation(form) {
    let validationAttempted = false;

    const validateField = (field) => {
        validateMatches(field);
        return renderFieldValidity(field);
    };

    form.addEventListener('submit', (event) => {
        validationAttempted = true;
        const fields = validationFields(form);
        const renderedErrorIds = new Set();
        let firstInvalid = null;

        fields.forEach((field) => {
            validateMatches(field);
            const invalid = !field.validity.valid;
            const error = errorElement(field);

            setAriaInvalid(field, invalid);
            syncErrorDescription(field, error, invalid);

            if (!invalid) {
                error.textContent = '';
                error.classList.add('hidden');
                return;
            }

            if (!renderedErrorIds.has(error.id)) {
                error.textContent = validationMessage(field);
                error.classList.remove('hidden');
                renderedErrorIds.add(error.id);
            }

            if (!firstInvalid) firstInvalid = field;
        });

        if (!firstInvalid) {
            if (!event.defaultPrevented) setFormSubmitting(form, event.submitter);
            return;
        }

        event.preventDefault();
        firstInvalid.scrollIntoView({ behavior: 'smooth', block: 'center' });
        window.requestAnimationFrame(() => focusField(firstInvalid));
    });

    ['input', 'change'].forEach((eventName) => {
        form.addEventListener(eventName, (event) => {
            const field = event.target;
            if (!(field instanceof HTMLInputElement || field instanceof HTMLSelectElement || field instanceof HTMLTextAreaElement)) return;

            clearServerError(field);
            if (!validationAttempted && field.getAttribute('aria-invalid') !== 'true') return;
            queueMicrotask(() => validateField(field));
        });
    });

    form.addEventListener('reset', () => {
        validationAttempted = false;
        window.requestAnimationFrame(() => {
            form.querySelectorAll('[data-ui-client-error]').forEach((error) => {
                error.textContent = '';
                error.classList.add('hidden');
            });
            validationFields(form).forEach((field) => {
                field.setCustomValidity('');
                field.removeAttribute('aria-invalid');
            });
        });
    });

    const serverInvalid = form.querySelector('[aria-invalid="true"]');
    if (serverInvalid) {
        window.requestAnimationFrame(() => focusField(serverInvalid));
    }
}

document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('form[data-ui-inline-validation]').forEach(initializeInlineValidation);
});
