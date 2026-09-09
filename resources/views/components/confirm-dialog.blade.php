<x-ui.dialog
    id="report-confirm-dialog"
    size="sm"
    labelledby="report-confirm-title"
    describedby="report-confirm-message"
    class="confirm-dialog"
    panel-class="confirm-dialog-card"
    :overlay-attributes="['class' => 'confirm-dialog-overlay', 'data-confirm-cancel' => '']"
>
        <div class="confirm-dialog-content">
            <div class="confirm-dialog-heading flex items-start">
                <i id="report-confirm-icon" class="confirm-dialog-icon fas fa-question-circle" aria-hidden="true"></i>
                <h2 id="report-confirm-title" class="confirm-dialog-title min-w-0">Confirmar acción</h2>
            </div>
            <div id="report-confirm-message" class="confirm-dialog-body">
                <p id="report-confirm-question" class="confirm-dialog-question hidden"></p>
                <p id="report-confirm-description" class="confirm-dialog-description">Revisa la acción antes de continuar.</p>
            </div>
            <div class="confirm-dialog-actions flex flex-col-reverse sm:flex-row sm:justify-end">
                <button type="button" id="report-confirm-cancel" class="confirm-dialog-button confirm-dialog-button-secondary">
                    Cancelar
                </button>
                <button type="button" id="report-confirm-accept" class="confirm-dialog-button confirm-dialog-button-primary">
                    Confirmar
                </button>
            </div>
        </div>
</x-ui.dialog>

<script>
    (function() {
        if (window.confirmDialog) return;

        const variants = {
            danger: {
                icon: 'fas fa-triangle-exclamation'
            },
            warning: {
                icon: 'fas fa-triangle-exclamation'
            },
            success: {
                icon: 'fas fa-circle-check'
            },
            neutral: {
                icon: 'fas fa-circle-question'
            }
        };

        const state = {
            resolver: null,
            previousFocus: null,
            previousBodyOverflow: '',
            isClosing: false,
            closeTimer: null
        };

        function getDialogParts() {
            const root = document.getElementById('report-confirm-dialog');
            if (!root) return null;

            return {
                root,
                title: document.getElementById('report-confirm-title'),
                question: document.getElementById('report-confirm-question'),
                description: document.getElementById('report-confirm-description'),
                icon: document.getElementById('report-confirm-icon'),
                cancel: document.getElementById('report-confirm-cancel'),
                accept: document.getElementById('report-confirm-accept')
            };
        }

        function finishResolve(value) {
            const parts = getDialogParts();
            if (!parts) return;

            parts.root.classList.remove('is-open', 'is-closing');
            parts.root.classList.add('hidden');
            parts.root.classList.remove('flex');
            parts.root.setAttribute('aria-hidden', 'true');
            document.body.style.overflow = state.previousBodyOverflow;
            state.isClosing = false;
            state.closeTimer = null;

            if (state.previousFocus && typeof state.previousFocus.focus === 'function') {
                state.previousFocus.focus();
            }

            if (state.resolver) {
                const resolver = state.resolver;
                state.resolver = null;
                resolver(value);
            }
        }

        function resolveDialog(value) {
            const parts = getDialogParts();
            if (!parts || state.isClosing || parts.root.classList.contains('hidden')) return;

            state.isClosing = true;
            parts.root.classList.remove('is-open');
            parts.root.classList.add('is-closing');
            document.removeEventListener('keydown', handleKeydown);

            const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
            state.closeTimer = window.setTimeout(() => finishResolve(value), reducedMotion ? 0 : 130);
        }

        function handleKeydown(event) {
            if (event.key === 'Escape') {
                event.preventDefault();
                resolveDialog(false);
                return;
            }

            if (event.key === 'Tab') {
                const parts = getDialogParts();
                if (!parts) return;

                const focusable = [parts.cancel, parts.accept].filter(element => element && !element.disabled);
                const first = focusable[0];
                const last = focusable[focusable.length - 1];

                if (event.shiftKey && document.activeElement === first) {
                    event.preventDefault();
                    last.focus();
                } else if (!event.shiftKey && document.activeElement === last) {
                    event.preventDefault();
                    first.focus();
                }
            }
        }

        window.confirmDialog = function(options = {}) {
            const parts = getDialogParts();
            if (!parts) return Promise.resolve(false);

            const variant = variants[options.variant || 'neutral'] ? (options.variant || 'neutral') : 'neutral';
            const config = variants[variant];
            if (state.closeTimer) window.clearTimeout(state.closeTimer);
            state.isClosing = false;
            state.previousFocus = document.activeElement;
            state.previousBodyOverflow = document.body.style.overflow;

            parts.title.textContent = options.title || 'Confirmar acción';
            if (options.subject) {
                const subject = document.createElement('strong');
                subject.className = 'confirm-dialog-subject';
                subject.textContent = String(options.subject);

                parts.question.replaceChildren(
                    document.createTextNode(options.messagePrefix || '¿Deseas eliminar '),
                    subject,
                    document.createTextNode(options.questionSuffix || '?')
                );
                parts.question.classList.remove('hidden');
                parts.description.textContent = options.description || options.messageSuffix || 'Esta acción es permanente y no se puede deshacer.';
            } else if (options.question) {
                parts.question.textContent = options.question;
                parts.question.classList.remove('hidden');
                parts.description.textContent = options.description || options.messageSuffix || 'Esta acción es permanente y no se puede deshacer.';
            } else {
                parts.question.replaceChildren();
                parts.question.classList.add('hidden');
                parts.description.textContent = options.message || 'Revisa la acción antes de continuar.';
            }
            parts.cancel.textContent = options.cancelText || 'Cancelar';
            parts.accept.textContent = options.confirmText || 'Confirmar';

            parts.icon.className = config.icon;
            parts.icon.classList.add('confirm-dialog-icon');
            parts.root.dataset.variant = variant;

            parts.root.classList.remove('hidden');
            parts.root.classList.add('flex');
            parts.root.classList.remove('is-closing');
            parts.root.setAttribute('aria-hidden', 'false');
            document.body.style.overflow = 'hidden';

            document.addEventListener('keydown', handleKeydown);
            requestAnimationFrame(() => {
                parts.root.classList.add('is-open');
                parts.cancel.focus();
            });
            return new Promise(resolve => {
                state.resolver = resolve;
            });
        };

        window.confirmDeleteDialog = function(options = {}) {
            return window.confirmDialog({
                ...options,
                title: options.title || 'Eliminar elemento',
                description: options.description || 'Esta acción es permanente y no se puede deshacer.',
                confirmText: options.confirmText || 'Eliminar',
                cancelText: options.cancelText || 'Cancelar',
                variant: 'danger'
            });
        };

        window.confirmFormClear = function(form, selectedFilesCount = 0) {
            if (!form) return Promise.resolve(true);

            const hasSelectedFiles = Number(selectedFilesCount || 0) > 0;
            const hasValues = Array.from(form.elements || []).some(element => {
                if (!element.name || element.type === 'hidden' || element.type === 'submit' || element.type === 'button') return false;
                if (element.type === 'checkbox' || element.type === 'radio') return element.checked;
                if (element.type === 'file') return element.files && element.files.length > 0;
                return String(element.value || '').trim() !== '';
            });

            if (!hasSelectedFiles && !hasValues) return Promise.resolve(true);

            return window.confirmDialog({
                title: 'Limpiar formulario',
                question: '¿Deseas limpiar el formulario?',
                description: 'Se quitarán los datos capturados y los archivos seleccionados.',
                confirmText: 'Limpiar',
                cancelText: 'Cancelar',
                variant: 'warning'
            });
        };

        document.addEventListener('submit', async function(event) {
            const form = event.target.closest('form[data-confirm-delete-form]');
            if (!form || form.dataset.confirmDeleteApproved === 'true') return;

            event.preventDefault();
            const confirmed = await window.confirmDeleteDialog({
                title: form.dataset.confirmTitle || 'Eliminar registro',
                subject: form.dataset.confirmSubject || 'este registro',
                messagePrefix: form.dataset.confirmMessagePrefix || undefined,
                description: form.dataset.confirmDescription || undefined,
                confirmText: form.dataset.confirmText || 'Eliminar'
            });

            if (!confirmed) return;
            form.dataset.confirmDeleteApproved = 'true';
            form.requestSubmit();
        });

        document.addEventListener('click', function(event) {
            if (event.target.closest('[data-confirm-cancel], #report-confirm-cancel')) {
                resolveDialog(false);
            }

            if (event.target.closest('#report-confirm-accept')) {
                resolveDialog(true);
            }
        });

    })();
</script>
