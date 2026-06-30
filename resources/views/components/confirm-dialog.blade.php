<div id="report-confirm-dialog" class="hidden fixed inset-0 z-[1000000] items-center justify-center p-4" aria-hidden="true">
    <div class="absolute inset-0 bg-[#1f2328]/45 backdrop-blur-[2px]" data-confirm-cancel></div>
    <section class="relative w-full max-w-md rounded-xl border border-gray-200 bg-white shadow-2xl ring-1 ring-black/5 overflow-hidden confirm-dialog-card" role="dialog" aria-modal="true" aria-labelledby="report-confirm-title" aria-describedby="report-confirm-message">
        <div class="p-5">
            <div class="flex items-start gap-3">
                <div id="report-confirm-icon-wrap" class="mt-0.5 flex h-10 w-10 flex-none items-center justify-center rounded-lg bg-[#611132]/10 text-[#611132]">
                    <i id="report-confirm-icon" class="fas fa-question-circle text-base"></i>
                </div>
                <div class="min-w-0 flex-1">
                    <h2 id="report-confirm-title" class="font-lora text-lg font-bold text-[#2f3337] leading-tight">Confirmar accion</h2>
                    <p id="report-confirm-message" class="mt-1 font-lora text-sm leading-relaxed text-gray-600">Revisa la accion antes de continuar.</p>
                </div>
                <button type="button" id="report-confirm-close" class="flex h-8 w-8 flex-none items-center justify-center rounded-lg text-gray-400 transition-colors hover:bg-gray-100 hover:text-gray-700 focus:outline-none focus:ring-2 focus:ring-[#611132]/30" aria-label="Cerrar confirmacion">
                    <i class="fas fa-times text-sm"></i>
                </button>
            </div>

            <div class="mt-5 flex flex-col-reverse gap-2 sm:flex-row sm:justify-end">
                <button type="button" id="report-confirm-cancel" class="rounded-lg border border-gray-300 px-4 py-2 font-lora text-sm font-semibold text-[#404041] transition-all duration-200 hover:border-[#611132]/40 hover:bg-gray-50 active:scale-[0.99] focus:outline-none focus:ring-2 focus:ring-[#611132]/25">
                    Cancelar
                </button>
                <button type="button" id="report-confirm-accept" class="min-w-[92px] rounded-lg px-4 py-2 font-lora text-sm font-semibold text-white shadow-sm transition-all duration-200 active:scale-[0.99] focus:outline-none focus:ring-2">
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
                accentColor: '#AB1A1A',
                iconBg: '#FEF2F2',
                iconColor: '#AB1A1A',
                icon: 'fas fa-trash-alt text-base',
                buttonBg: '#AB1A1A',
                buttonHover: '#8F1616',
                focusColor: 'rgba(171, 26, 26, 0.28)'
            },
            warning: {
                accentColor: '#B38E1A',
                iconBg: '#FFF8E1',
                iconColor: '#9A7610',
                icon: 'fas fa-exclamation-triangle text-base',
                buttonBg: '#7A5B00',
                buttonHover: '#624900',
                focusColor: 'rgba(179, 142, 26, 0.32)'
            },
            success: {
                accentColor: '#237A3B',
                iconBg: '#ECFDF3',
                iconColor: '#237A3B',
                icon: 'fas fa-check-circle text-base',
                buttonBg: '#237A3B',
                buttonHover: '#1D6531',
                focusColor: 'rgba(35, 122, 59, 0.28)'
            },
            neutral: {
                accentColor: '#611132',
                iconBg: 'rgba(97, 17, 50, 0.10)',
                iconColor: '#611132',
                icon: 'fas fa-question-circle text-base',
                buttonBg: '#611132',
                buttonHover: '#4A0E26',
                focusColor: 'rgba(97, 17, 50, 0.30)'
            }
        };

        const state = { resolver: null, previousFocus: null };

        function getDialogParts() {
            const root = document.getElementById('report-confirm-dialog');
            if (!root) return null;

            return {
                root,
                title: document.getElementById('report-confirm-title'),
                message: document.getElementById('report-confirm-message'),
                iconWrap: document.getElementById('report-confirm-icon-wrap'),
                icon: document.getElementById('report-confirm-icon'),
                cancel: document.getElementById('report-confirm-cancel'),
                accept: document.getElementById('report-confirm-accept'),
                close: document.getElementById('report-confirm-close'),
                card: root.querySelector('.confirm-dialog-card')
            };
        }

        function resolveDialog(value) {
            const parts = getDialogParts();
            if (!parts) return;

            parts.root.classList.add('hidden');
            parts.root.classList.remove('flex');
            parts.root.setAttribute('aria-hidden', 'true');
            document.removeEventListener('keydown', handleKeydown);

            if (state.previousFocus && typeof state.previousFocus.focus === 'function') {
                state.previousFocus.focus();
            }

            if (state.resolver) {
                const resolver = state.resolver;
                state.resolver = null;
                resolver(value);
            }
        }

        function handleKeydown(event) {
            if (event.key === 'Escape') {
                event.preventDefault();
                resolveDialog(false);
            }
        }

        window.confirmDialog = function(options = {}) {
            const parts = getDialogParts();
            if (!parts) return Promise.resolve(false);

            const config = variants[options.variant || 'neutral'] || variants.neutral;
            state.previousFocus = document.activeElement;

            parts.title.textContent = options.title || 'Confirmar accion';
            parts.message.textContent = options.message || 'Revisa la accion antes de continuar.';
            parts.cancel.textContent = options.cancelText || 'Cancelar';
            parts.accept.textContent = options.confirmText || 'Confirmar';

            parts.iconWrap.style.backgroundColor = config.iconBg;
            parts.iconWrap.style.color = config.iconColor;
            parts.icon.className = config.icon;
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
            parts.root.setAttribute('aria-hidden', 'false');
            parts.card.style.animation = 'confirmEnter 0.2s ease-out forwards';

            document.addEventListener('keydown', handleKeydown);
            return new Promise(resolve => {
                state.resolver = resolve;
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
                message: 'Se quitaran los datos capturados y los archivos seleccionados en este formulario.',
                confirmText: 'Limpiar',
                cancelText: 'Cancelar',
                variant: 'warning'
            });
        };

        document.addEventListener('click', function(event) {
            if (event.target.closest('[data-confirm-cancel], #report-confirm-cancel, #report-confirm-close')) {
                resolveDialog(false);
            }

            if (event.target.closest('#report-confirm-accept')) {
                resolveDialog(true);
            }
        });

        if (!document.getElementById('report-confirm-styles')) {
            const style = document.createElement('style');
            style.id = 'report-confirm-styles';
            style.textContent = `
                @keyframes confirmEnter {
                    from {
                        opacity: 0;
                        transform: translate3d(0, 10px, 0) scale(0.98);
                    }
                    to {
                        opacity: 1;
                        transform: translate3d(0, 0, 0) scale(1);
                    }
                }
            `;
            document.head.appendChild(style);
        }
    })();
</script>
