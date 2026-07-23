<div id="user-password-dialog" class="user-password-dialog hidden fixed inset-0 items-center justify-center p-4" aria-hidden="true">
    <div class="user-password-dialog-overlay absolute inset-0" data-password-dialog-close></div>
    <section class="user-password-dialog-card relative w-full" role="dialog" aria-modal="true" aria-labelledby="user-password-dialog-title" aria-describedby="user-password-dialog-description">
        <form id="user-password-dialog-form" method="POST" novalidate>
            @csrf
            @method('PUT')

            <header class="user-password-dialog-header">
                <div class="min-w-0">
                    <h2 id="user-password-dialog-title">Cambiar contraseña</h2>
                    <p id="user-password-dialog-description">Actualice las credenciales de acceso de esta cuenta.</p>
                </div>
                <button type="button" class="user-password-dialog-close" data-password-dialog-close aria-label="Cerrar">
                    <i class="fas fa-times" aria-hidden="true"></i>
                </button>
            </header>

            <div class="user-password-dialog-body">
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
                    <div class="user-password-dialog-input-wrap">
                        <input id="user-password-new" name="password" type="password" required minlength="12" maxlength="255" autocomplete="new-password" placeholder="Ingrese la nueva contraseña">
                        <button type="button" class="app-password-visibility user-password-dialog-visibility" data-password-toggle="user-password-new" aria-label="Mostrar contraseña" title="Mostrar contraseña">
                            <i class="far fa-eye" aria-hidden="true"></i>
                        </button>
                    </div>
                    <p class="user-password-dialog-field-error hidden" data-error-for="password"></p>
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
                    <div class="user-password-dialog-input-wrap">
                        <input id="user-password-confirm" name="password_confirmation" type="password" required minlength="12" maxlength="255" autocomplete="new-password" placeholder="Confirme la nueva contraseña">
                        <button type="button" class="app-password-visibility user-password-dialog-visibility" data-password-toggle="user-password-confirm" aria-label="Mostrar contraseña" title="Mostrar contraseña">
                            <i class="far fa-eye" aria-hidden="true"></i>
                        </button>
                    </div>
                    <p class="user-password-dialog-field-error hidden" data-error-for="password_confirmation"></p>
                    <div class="user-password-dialog-confirm-tools">
                        <span id="user-password-dialog-match" data-status="empty" aria-live="polite"><i class="far fa-circle" aria-hidden="true"></i><span>Confirme la contraseña</span></span>
                        <button type="button" id="user-password-dialog-generate"><i class="fas fa-key" aria-hidden="true"></i>Generar contraseña</button>
                    </div>
                </div>
            </div>

            <footer class="user-password-dialog-actions">
                <button type="button" class="user-password-dialog-button user-password-dialog-cancel" data-password-dialog-close>Cancelar</button>
                <button type="submit" class="user-password-dialog-button user-password-dialog-save">Guardar contraseña</button>
            </footer>
        </form>
    </section>
</div>

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
        const state = { dirty: false, closing: false, awaitingDiscard: false, previousFocus: null, previousOverflow: '' };

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

            const hasConfirmation = confirmation.value.length > 0;
            const matches = hasConfirmation && confirmation.value === value;
            match.dataset.status = !hasConfirmation ? 'empty' : matches ? 'match' : 'mismatch';
            match.querySelector('i').className = !hasConfirmation ? 'far fa-circle' : matches ? 'fas fa-check-circle' : 'fas fa-exclamation-circle';
            match.querySelector('span').textContent = !hasConfirmation ? 'Confirme la contraseña' : matches ? 'Las contraseñas coinciden' : 'Las contraseñas no coinciden';
            confirmation.setCustomValidity(hasConfirmation && !matches ? 'Las contraseñas no coinciden.' : '');
        }

        function clearErrors() {
            errorBox.textContent = '';
            errorBox.classList.add('hidden');
            root.querySelectorAll('[data-error-for]').forEach(element => {
                element.textContent = '';
                element.classList.add('hidden');
            });
        }

        function openDialog(trigger) {
            state.previousFocus = trigger;
            state.previousOverflow = document.body.style.overflow;
            state.dirty = false;
            state.closing = false;
            form.reset();
            form.action = trigger.dataset.passwordUpdateUrl;
            const userName = trigger.dataset.userName || 'Usuario';
            document.getElementById('user-password-dialog-name').textContent = userName;
            document.getElementById('user-password-dialog-username').textContent = `@${trigger.dataset.username || ''}`;
            const photo = document.getElementById('user-password-dialog-photo');
            photo.src = trigger.dataset.userPhoto || '{{ asset('images/default_pfp.svg.png') }}';
            photo.alt = `Foto de ${userName}`;
            clearErrors();
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
            password.focus();
        }

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
                const visible = input.type === 'text';
                input.type = visible ? 'password' : 'text';
                const accessibleLabel = visible ? 'Mostrar contraseña' : 'Ocultar contraseña';
                toggle.setAttribute('aria-label', accessibleLabel);
                toggle.setAttribute('title', accessibleLabel);
                toggle.querySelector('i').className = visible ? 'far fa-eye' : 'far fa-eye-slash';
                return;
            }
            if (event.target.closest('#user-password-dialog-generate')) generatePassword();
            if (event.target.closest('[data-password-dialog-close]') && root.contains(event.target)) requestClose();
        }, true);

        [password, confirmation].forEach(input => input.addEventListener('input', function() {
            state.dirty = Boolean(password.value || confirmation.value);
            clearErrors();
            updatePasswordState();
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
            clearErrors();
            updatePasswordState();
            if (!form.checkValidity()) {
                form.reportValidity();
                return;
            }

            saveButton.disabled = true;
            saveButton.textContent = 'Guardando...';
            try {
                const response = await fetch(form.action, {
                    method: 'POST',
                    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                    body: new FormData(form)
                });
                const data = await response.json().catch(() => ({}));
                if (!response.ok) {
                    const errors = data.errors || {};
                    Object.entries(errors).forEach(([field, messages]) => {
                        const element = root.querySelector(`[data-error-for="${field}"]`);
                        if (!element) return;
                        element.textContent = messages[0];
                        element.classList.remove('hidden');
                    });
                    errorBox.textContent = data.message || 'Revisa los datos e intenta nuevamente.';
                    errorBox.classList.remove('hidden');
                    return;
                }
                state.dirty = false;
                closeDialog(true);
                window.showToast?.(data.message || 'La contraseña se actualizó correctamente.', 'success');
            } catch (error) {
                errorBox.textContent = 'No se pudo actualizar la contraseña. Intenta nuevamente.';
                errorBox.classList.remove('hidden');
            } finally {
                saveButton.disabled = false;
                saveButton.textContent = 'Guardar contraseña';
            }
        });
    })();
</script>
