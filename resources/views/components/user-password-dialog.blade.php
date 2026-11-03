<x-ui.dialog
    id="user-password-dialog"
    size="task"
    labelledby="user-password-dialog-title"
    describedby="user-password-dialog-description"
    class="user-password-dialog"
    panel-class="user-password-dialog-card"
    :overlay-attributes="['class' => 'user-password-dialog-overlay', 'data-password-dialog-close' => '']"
>
        <form id="user-password-dialog-form" method="POST" novalidate>
            @csrf
            @method('PUT')

            <header class="ui-dialog__header user-password-dialog-header">
                <div class="ui-dialog__heading min-w-0">
                    <div class="ui-dialog__title-row">
                        <i class="fas fa-key" aria-hidden="true"></i>
                        <h2 id="user-password-dialog-title" class="ui-dialog__title">Cambiar contraseña</h2>
                    </div>
                    <p id="user-password-dialog-description" class="ui-dialog__description">Actualice las credenciales de acceso de esta cuenta.</p>
                </div>
                <button type="button" class="ui-dialog__close user-password-dialog-close" data-password-dialog-close aria-label="Cerrar">
                    <i class="fas fa-times" aria-hidden="true"></i>
                </button>
            </header>

            <div class="ui-dialog__body user-password-dialog-body">
                <div class="user-password-dialog-account">
                    <img id="user-password-dialog-photo" src="{{ asset('images/default_pfp.svg.png') }}" alt="">
                    <div class="min-w-0">
                        <strong id="user-password-dialog-name"></strong>
                        <small id="user-password-dialog-username"></small>
                    </div>
                </div>

                <div id="user-password-dialog-error" class="user-password-dialog-error hidden" role="alert"></div>

                <div class="user-password-dialog-field">
                    <div class="user-password-dialog-label-row">
                        <label for="user-password-new">Nueva contraseña <span>*</span></label>
                        <span id="user-password-dialog-strength" data-level="empty" aria-live="polite">Sin evaluar</span>
                    </div>
                    <div class="user-password-dialog-input-wrap app-password-input-wrap">
                        <input id="user-password-new" name="password" type="password" required minlength="12" maxlength="255" autocomplete="new-password" autocapitalize="none" autocorrect="off" spellcheck="false" placeholder="Ingrese la nueva contraseña" aria-describedby="user-password-new-error">
                        <button type="button" class="app-password-visibility user-password-dialog-visibility" data-password-toggle="user-password-new" aria-label="Mostrar contraseña" title="Mostrar contraseña">
                            <i class="far fa-eye" aria-hidden="true"></i>
                        </button>
                    </div>
                    <p id="user-password-new-error" class="user-password-dialog-field-error hidden" data-error-for="password" aria-live="polite"></p>
                    <div id="user-password-dialog-meter" class="user-password-dialog-meter" role="progressbar" aria-label="Fortaleza de la contraseña" aria-valuemin="0" aria-valuemax="5" aria-valuenow="0">
                        <span data-level="empty"></span>
                    </div>
                    <ul class="user-password-dialog-rules" aria-label="Requisitos de seguridad">
                        <li data-password-modal-rule="length"><i class="far fa-circle" aria-hidden="true"></i><span>12 caracteres</span></li>
                        <li data-password-modal-rule="case"><i class="far fa-circle" aria-hidden="true"></i><span>Mayúscula y minúscula</span></li>
                        <li data-password-modal-rule="number"><i class="far fa-circle" aria-hidden="true"></i><span>Un número</span></li>
                        <li data-password-modal-rule="special"><i class="far fa-circle" aria-hidden="true"></i><span>Un símbolo</span></li>
                    </ul>
                </div>

                <div class="user-password-dialog-field">
                    <label for="user-password-confirm">Confirmar contraseña <span>*</span></label>
                    <div class="user-password-dialog-input-wrap app-password-input-wrap">
                        <input id="user-password-confirm" name="password_confirmation" type="password" required minlength="12" maxlength="255" autocomplete="new-password" autocapitalize="none" autocorrect="off" spellcheck="false" placeholder="Confirme la nueva contraseña" aria-describedby="user-password-confirm-error">
                        <button type="button" class="app-password-visibility user-password-dialog-visibility" data-password-toggle="user-password-confirm" aria-label="Mostrar contraseña" title="Mostrar contraseña">
                            <i class="far fa-eye" aria-hidden="true"></i>
                        </button>
                    </div>
                    <p id="user-password-confirm-error" class="user-password-dialog-field-error hidden" data-error-for="password_confirmation" aria-live="polite"></p>
                    <div class="user-password-dialog-confirm-tools">
                        <span id="user-password-dialog-match" data-status="empty" aria-live="polite"><i class="far fa-circle" aria-hidden="true"></i><span>Confirme la contraseña</span></span>
                        <button type="button" id="user-password-dialog-generate"><i class="fas fa-key" aria-hidden="true"></i>Generar contraseña</button>
                    </div>
                </div>
            </div>

            <footer class="ui-dialog__actions user-password-dialog-actions">
                <button type="button" class="ui-button ui-button--secondary user-password-dialog-button user-password-dialog-cancel" data-password-dialog-close>Cancelar</button>
                <button type="submit" class="ui-button ui-button--primary user-password-dialog-button user-password-dialog-save">
                    <i class="fas fa-spinner fa-spin" data-password-save-spinner aria-hidden="true" hidden></i>
                    <span data-password-save-label>Guardar contraseña</span>
                </button>
            </footer>
        </form>
</x-ui.dialog>

<script>
    (function() {
        const root = document.getElementById('user-password-dialog');
        if (!root || root.dataset.initialized === 'true') return;
        root.dataset.initialized = 'true';

        const form = document.getElementById('user-password-dialog-form');
        const password = document.getElementById('user-password-new');
        const confirmation = document.getElementById('user-password-confirm');
        const strength = document.getElementById('user-password-dialog-strength');
        const meter = document.getElementById('user-password-dialog-meter');
        const meterBar = meter.querySelector('span');
        const match = document.getElementById('user-password-dialog-match');
        const errorBox = document.getElementById('user-password-dialog-error');
        const saveButton = root.querySelector('.user-password-dialog-save');
        const saveSpinner = saveButton.querySelector('[data-password-save-spinner]');
        const saveLabel = saveButton.querySelector('[data-password-save-label]');
        const visibilityPointerState = new WeakMap();
        const state = {
            dirty: false,
            closing: false,
            awaitingDiscard: false,
            previousFocus: null,
            previousOverflow: '',
            submitting: false,
            validationAttempted: false
        };

        function passwordChecks(value) {
            return {
                length: value.length >= 12,
                case: /[a-z]/.test(value) && /[A-Z]/.test(value),
                number: /[0-9]/.test(value),
                special: /[^A-Za-z0-9]/.test(value)
            };
        }

        function updatePasswordState() {
            const value = password.value;
            const checks = passwordChecks(value);
            let score = 0;
            if (value.length >= 12) score++;
            if (/[a-z]/.test(value)) score++;
            if (/[A-Z]/.test(value)) score++;
            if (/[0-9]/.test(value)) score++;
            if (/[^A-Za-z0-9]/.test(value)) score++;

            const levels = score === 0 ? ['empty', 'Sin evaluar'] : score <= 2 ? ['weak', 'Débil'] : score === 3 ? ['medium', 'Media'] : score === 4 ? ['strong', 'Fuerte'] : ['very-strong', 'Muy fuerte'];
            strength.dataset.level = levels[0];
            strength.textContent = levels[1];
            meterBar.dataset.level = levels[0];
            meterBar.style.width = `${score * 20}%`;
            meter.setAttribute('aria-valuenow', score);
            meter.setAttribute('aria-valuetext', levels[1]);

            Object.entries(checks).forEach(([rule, complete]) => {
                const item = root.querySelector(`[data-password-modal-rule="${rule}"]`);
                if (!item) return;
                item.dataset.complete = complete ? 'true' : 'false';
                const icon = item.querySelector('i');
                icon.className = complete ? 'fas fa-check-circle' : 'far fa-circle';
            });

            updatePasswordMatchState();
        }

        function updatePasswordMatchState() {
            const hasConfirmation = confirmation.value.length > 0;
            const matches = hasConfirmation && confirmation.value === password.value;
            const confirmationInvalid = confirmation.getAttribute('aria-invalid') === 'true';
            match.hidden = confirmationInvalid || (hasConfirmation && !matches);
            match.dataset.status = !hasConfirmation ? 'empty' : matches ? 'match' : 'mismatch';
            match.querySelector('i').className = !hasConfirmation ? 'far fa-circle' : matches ? 'fas fa-check-circle' : 'fas fa-exclamation-circle';
            match.querySelector('span').textContent = matches ? 'Las contraseñas coinciden' : 'Confirme la contraseña';
        }

        function fieldError(field) {
            return root.querySelector(`[data-error-for="${field.name}"]`);
        }

        function setFieldError(field, message = '') {
            const error = fieldError(field);
            if (!error) return;

            const invalid = Boolean(message);
            error.textContent = message;
            error.classList.toggle('hidden', !invalid);
            if (invalid) field.setAttribute('aria-invalid', 'true');
            else field.removeAttribute('aria-invalid');
            if (field === confirmation) updatePasswordMatchState();
        }

        function passwordMessage() {
            const value = password.value;
            const checks = passwordChecks(value);

            if (!value) return 'Capture la nueva contraseña.';
            if (!checks.length) return 'La nueva contraseña debe tener al menos 12 caracteres.';
            if (!checks.case) return 'Incluya al menos una letra mayúscula y una minúscula.';
            if (!checks.number) return 'Incluya al menos un número.';
            if (!checks.special) return 'Incluya al menos un símbolo.';
            return '';
        }

        function confirmationMessage() {
            if (!confirmation.value) return 'Confirme la nueva contraseña.';
            if (confirmation.value !== password.value) return 'Las contraseñas no coinciden.';
            return '';
        }

        function validateField(field) {
            const message = field === password ? passwordMessage() : confirmationMessage();
            setFieldError(field, message);
            return !message;
        }

        function focusInvalidField(field) {
            field.scrollIntoView({ behavior: 'smooth', block: 'center' });
            window.requestAnimationFrame(() => field.focus({ preventScroll: true }));
        }

        function validateForm({ focus = false } = {}) {
            updatePasswordState();
            const passwordValid = validateField(password);
            const confirmationValid = validateField(confirmation);
            const firstInvalid = !passwordValid ? password : !confirmationValid ? confirmation : null;
            if (focus && firstInvalid) focusInvalidField(firstInvalid);
            return !firstInvalid;
        }

        function clearErrors({ general = true } = {}) {
            if (general) {
                errorBox.textContent = '';
                errorBox.classList.add('hidden');
            }
            setFieldError(password);
            setFieldError(confirmation);
        }

        function setSubmitting(submitting) {
            state.submitting = submitting;
            form.dataset.uiSubmitting = submitting ? 'true' : 'false';
            form.setAttribute('aria-busy', String(submitting));
            saveButton.disabled = submitting;
            saveButton.setAttribute('aria-busy', String(submitting));
            saveSpinner.hidden = !submitting;
            saveLabel.textContent = submitting ? 'Guardando…' : 'Guardar contraseña';
        }

        function openDialog(trigger) {
            state.previousFocus = trigger;
            state.previousOverflow = document.body.style.overflow;
            state.dirty = false;
            state.closing = false;
            state.validationAttempted = false;
            form.reset();
            setSubmitting(false);
            form.action = trigger.dataset.passwordUpdateUrl;
            const userName = trigger.dataset.userName || 'Usuario';
            document.getElementById('user-password-dialog-name').textContent = userName;
            document.getElementById('user-password-dialog-username').textContent = `@${trigger.dataset.username || ''}`;
            const photo = document.getElementById('user-password-dialog-photo');
            photo.src = trigger.dataset.userPhoto || '{{ asset('images/default_pfp.svg.png') }}';
            photo.alt = `Foto de ${userName}`;
            clearErrors();
            root.querySelectorAll('[data-password-toggle]').forEach(toggle => {
                const input = document.getElementById(toggle.dataset.passwordToggle);
                input.type = 'password';
                toggle.setAttribute('aria-label', 'Mostrar contraseña');
                toggle.setAttribute('title', 'Mostrar contraseña');
                toggle.querySelector('i').className = 'far fa-eye';
            });
            updatePasswordState();
            root.classList.remove('hidden', 'is-closing');
            root.classList.add('flex');
            root.setAttribute('aria-hidden', 'false');
            document.body.style.overflow = 'hidden';
            requestAnimationFrame(() => {
                root.classList.add('is-open');
                password.focus();
            });
        }

        function finishClose() {
            root.classList.remove('flex', 'is-open', 'is-closing');
            root.classList.add('hidden');
            root.setAttribute('aria-hidden', 'true');
            document.body.style.overflow = state.previousOverflow;
            state.closing = false;
            state.previousFocus?.focus();
        }

        function closeDialog(force = false) {
            if (state.closing) return;
            state.closing = true;
            root.classList.remove('is-open');
            root.classList.add('is-closing');
            const reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
            window.setTimeout(finishClose, force || reduce ? 0 : 130);
        }

        async function requestClose() {
            if (state.submitting) return;
            if (state.awaitingDiscard) return;
            if (!state.dirty) {
                closeDialog();
                return;
            }
            state.awaitingDiscard = true;
            const discard = await window.confirmDialog({
                    title: 'Descartar cambios',
                    question: '¿Deseas cerrar el cambio de contraseña?',
                    description: 'La contraseña escrita no se guardará.',
                    confirmText: 'Descartar',
                    cancelText: 'Continuar editando',
                    variant: 'warning'
                })
                .finally(() => { state.awaitingDiscard = false; });
            if (discard) closeDialog();
        }

        function secureIndex(length) {
            const values = new Uint32Array(1);
            crypto.getRandomValues(values);
            return values[0] % length;
        }

        function generatePassword() {
            const groups = ['abcdefghijklmnopqrstuvwxyz', 'ABCDEFGHIJKLMNOPQRSTUVWXYZ', '0123456789', '!@#$%^&*'];
            const all = groups.join('');
            const chars = groups.map(group => group[secureIndex(group.length)]);
            while (chars.length < 12) chars.push(all[secureIndex(all.length)]);
            for (let index = chars.length - 1; index > 0; index--) {
                const swap = secureIndex(index + 1);
                [chars[index], chars[swap]] = [chars[swap], chars[index]];
            }
            password.value = chars.join('');
            confirmation.value = password.value;
            state.dirty = true;
            clearErrors();
            updatePasswordState();
            if (state.validationAttempted) validateForm();
            password.focus();
        }

        root.addEventListener('pointerdown', function(event) {
            const toggle = event.target.closest('[data-password-toggle]');
            if (!toggle || !root.contains(toggle)) return;

            const input = document.getElementById(toggle.dataset.passwordToggle);
            if (!input || document.activeElement !== input) return;

            visibilityPointerState.set(toggle, {
                input,
                start: input.selectionStart,
                end: input.selectionEnd,
                direction: input.selectionDirection
            });

            // Evita que el botón tome el foco y cierre el teclado virtual.
            event.preventDefault();
        });

        root.addEventListener('pointercancel', function(event) {
            const toggle = event.target.closest('[data-password-toggle]');
            if (toggle) visibilityPointerState.delete(toggle);
        });

        root.addEventListener('keydown', function(event) {
            const toggle = event.target.closest('[data-password-toggle]');
            if (toggle) visibilityPointerState.delete(toggle);
        });

        document.addEventListener('click', function(event) {
            const trigger = event.target.closest('.js-change-password');
            if (trigger) {
                event.preventDefault();
                document.querySelectorAll('.users-row-menu').forEach(menu => menu.classList.add('hidden'));
                document.querySelectorAll('.users-row-menu-button').forEach(button => button.setAttribute('aria-expanded', 'false'));
                openDialog(trigger);
                return;
            }
            const toggle = event.target.closest('[data-password-toggle]');
            if (toggle && root.contains(toggle)) {
                const input = document.getElementById(toggle.dataset.passwordToggle);
                const pointerState = visibilityPointerState.get(toggle);
                const visible = input.type === 'text';
                input.type = visible ? 'password' : 'text';
                const accessibleLabel = visible ? 'Mostrar contraseña' : 'Ocultar contraseña';
                toggle.setAttribute('aria-label', accessibleLabel);
                toggle.setAttribute('title', accessibleLabel);
                toggle.querySelector('i').className = visible ? 'far fa-eye' : 'far fa-eye-slash';

                if (pointerState?.input === input) {
                    input.focus({ preventScroll: true });
                    if (pointerState.start !== null && pointerState.end !== null) {
                        input.setSelectionRange(pointerState.start, pointerState.end, pointerState.direction || 'none');
                    }
                    visibilityPointerState.delete(toggle);
                }
                return;
            }
            if (event.target.closest('#user-password-dialog-generate')) generatePassword();
            if (event.target.closest('[data-password-dialog-close]') && root.contains(event.target)) requestClose();
        }, true);

        [password, confirmation].forEach(input => input.addEventListener('input', function() {
            state.dirty = Boolean(password.value || confirmation.value);
            errorBox.textContent = '';
            errorBox.classList.add('hidden');
            updatePasswordState();
            if (state.validationAttempted || input.getAttribute('aria-invalid') === 'true') {
                validateField(input);
                if (input === password && confirmation.value) validateField(confirmation);
            } else {
                setFieldError(input);
                if (confirmation.value) validateField(confirmation);
            }
        }));

        document.addEventListener('keydown', function(event) {
            if (root.classList.contains('hidden')) return;
            const confirmDialog = document.getElementById('report-confirm-dialog');
            if (confirmDialog && !confirmDialog.classList.contains('hidden')) return;
            if (event.key === 'Escape') {
                event.preventDefault();
                requestClose();
                return;
            }
            if (event.key !== 'Tab') return;
            const focusable = Array.from(root.querySelectorAll('button:not([disabled]), input:not([disabled])'));
            const first = focusable[0];
            const last = focusable[focusable.length - 1];
            if (event.shiftKey && document.activeElement === first) { event.preventDefault(); last.focus(); }
            if (!event.shiftKey && document.activeElement === last) { event.preventDefault(); first.focus(); }
        });

        form.addEventListener('submit', async function(event) {
            event.preventDefault();
            if (state.submitting) return;

            state.validationAttempted = true;
            clearErrors();
            if (!validateForm({ focus: true })) return;

            setSubmitting(true);
            try {
                const response = await fetch(form.action, {
                    method: 'POST',
                    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                    body: new FormData(form)
                });
                const data = await response.json().catch(() => ({}));
                if (!response.ok) {
                    const errors = data.errors || {};
                    let firstInvalid = null;
                    Object.entries(errors).forEach(([field, messages]) => {
                        const element = root.querySelector(`[data-error-for="${field}"]`);
                        if (!element) return;
                        element.textContent = messages[0];
                        element.classList.remove('hidden');
                        const input = form.elements.namedItem(field);
                        if (input instanceof HTMLInputElement) {
                            input.setAttribute('aria-invalid', 'true');
                            if (!firstInvalid) firstInvalid = input;
                        }
                    });
                    if (firstInvalid) {
                        focusInvalidField(firstInvalid);
                    } else {
                        errorBox.textContent = data.message || 'Revisa los datos e intenta nuevamente.';
                        errorBox.classList.remove('hidden');
                    }
                    return;
                }
                state.dirty = false;
                closeDialog(true);
                window.showToast?.(data.message || 'La contraseña se actualizó correctamente.', 'success');
            } catch (error) {
                errorBox.textContent = 'No se pudo actualizar la contraseña. Intenta nuevamente.';
                errorBox.classList.remove('hidden');
            } finally {
                setSubmitting(false);
            }
        });
    })();
</script>
