<div id="report-confirm-dialog" class="hidden fixed inset-0 z-[1000000] items-center justify-center p-4" aria-hidden="true">
    <div class="confirm-dialog-overlay absolute inset-0" data-confirm-cancel></div>
    <section class="confirm-dialog-card relative w-full border bg-white" role="dialog" aria-modal="true" aria-labelledby="report-confirm-title" aria-describedby="report-confirm-message">
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
    </section>
</div>

<script>
    (function() {
        if (window.confirmDialog) return;

        const variants = {
            danger: {
                iconColor: '#AB1A1A',
                icon: 'fas fa-exclamation-circle',
                buttonBg: '#AB1A1A',
                buttonHover: '#8F1616',
                focusColor: 'rgba(171, 26, 26, 0.28)'
            },
            warning: {
                iconColor: '#9A7610',
                icon: 'fas fa-exclamation-triangle',
                buttonBg: '#7A5B00',
                buttonHover: '#624900',
                focusColor: 'rgba(179, 142, 26, 0.32)'
            },
            success: {
                iconColor: '#237A3B',
                icon: 'fas fa-check-circle',
                buttonBg: '#237A3B',
                buttonHover: '#1D6531',
                focusColor: 'rgba(35, 122, 59, 0.28)'
            },
            neutral: {
                iconColor: 'var(--brand-primary)',
                icon: 'fas fa-question-circle',
                buttonBg: 'var(--brand-primary)',
                buttonHover: 'var(--brand-primary-hover)',
                focusColor: 'rgba(97, 17, 50, 0.30)'
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

            const config = variants[options.variant || 'neutral'] || variants.neutral;
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
            parts.icon.style.color = config.iconColor;
            parts.accept.style.backgroundColor = config.buttonBg;
            parts.accept.style.borderColor = config.buttonBg;
            parts.accept.style.color = '#FFFFFF';
            parts.accept.style.boxShadow = `0 0 0 0 ${config.focusColor}`;
            parts.accept.onmouseenter = () => {
                parts.accept.style.backgroundColor = config.buttonHover;
                parts.accept.style.borderColor = config.buttonHover;
            };
            parts.accept.onmouseleave = () => {
                parts.accept.style.backgroundColor = config.buttonBg;
                parts.accept.style.borderColor = config.buttonBg;
            };
            parts.accept.onfocus = () => {
                parts.accept.style.boxShadow = `0 0 0 3px ${config.focusColor}`;
            };
            parts.accept.onblur = () => {
                parts.accept.style.boxShadow = `0 0 0 0 ${config.focusColor}`;
            };

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
