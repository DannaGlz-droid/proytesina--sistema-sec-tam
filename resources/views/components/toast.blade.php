<section id="toast-container" class="app-toast-container" data-position="bottom-right" data-expanded="false"
    aria-live="polite" aria-relevant="additions removals" aria-label="Notificaciones" role="region"></section>

<script>
    (() => {
        const defaults = {
            position: 'bottom-right',
            duration: 4000,
            visibleToasts: 3,
            maxToasts: 5,
            gap: 10,
            closeButton: true,
            richColors: true,
            expand: false,
        };

        const validTypes = new Set(['default', 'success', 'info', 'warning', 'error', 'loading']);
        const validPositions = new Set([
            'top-left', 'top-center', 'top-right',
            'bottom-left', 'bottom-center', 'bottom-right',
        ]);
        const iconClasses = {
            default: 'fa-regular fa-bell',
            success: 'fa-solid fa-circle-check',
            info: 'fa-solid fa-circle-info',
            warning: 'fa-solid fa-triangle-exclamation',
            error: 'fa-solid fa-circle-exclamation',
            loading: 'fa-solid fa-spinner',
        };

        let config = { ...defaults };
        let sequence = 0;
        let isInteracting = false;
        const stateByToast = new WeakMap();
        const recentMessages = new Map();

        const getContainer = () => document.getElementById('toast-container');
        const getToasts = () => Array.from(getContainer()?.querySelectorAll('.app-toast:not(.is-leaving)') || []);

        function normalizeOptions(options = {}) {
            const type = validTypes.has(options.type) ? options.type : 'default';
            return {
                ...options,
                type,
                duration: Number.isFinite(Number(options.duration)) ? Number(options.duration) : config.duration,
                closeButton: options.closeButton ?? config.closeButton,
                dismissible: options.dismissible !== false,
                richColors: options.richColors ?? config.richColors,
            };
        }

        function setText(element, value) {
            element.textContent = value == null ? '' : String(value);
        }

        function createIcon(type) {
            const iconWrap = document.createElement('span');
            iconWrap.className = 'app-toast-icon';
            iconWrap.setAttribute('aria-hidden', 'true');

            const icon = document.createElement('i');
            icon.className = iconClasses[type] || iconClasses.default;
            if (type === 'loading') icon.classList.add('app-toast-spinner');
            iconWrap.appendChild(icon);
            return iconWrap;
        }

        function createCloseButton(toast) {
            const button = document.createElement('button');
            button.type = 'button';
            button.className = 'app-toast-close';
            button.setAttribute('aria-label', 'Cerrar notificación');
            button.innerHTML = '<i class="fa-solid fa-xmark" aria-hidden="true"></i>';
            button.addEventListener('click', () => dismiss(toast));
            return button;
        }

        function renderToast(toast, options) {
            toast.replaceChildren();
            toast.dataset.type = options.type;
            toast.dataset.richColors = options.richColors ? 'true' : 'false';
            toast.setAttribute('role', options.type === 'error' ? 'alert' : 'status');
            toast.setAttribute('aria-atomic', 'true');

            if (options.closeButton && options.dismissible) {
                toast.appendChild(createCloseButton(toast));
            }

            toast.appendChild(createIcon(options.type));

            const content = document.createElement('div');
            content.className = 'app-toast-content';

            const title = document.createElement('div');
            title.className = 'app-toast-title';
            setText(title, options.message);
            content.appendChild(title);

            if (options.description) {
                const description = document.createElement('div');
                description.className = 'app-toast-description';
                setText(description, options.description);
                content.appendChild(description);
            }

            toast.appendChild(content);

            if (options.action?.label && typeof options.action.onClick === 'function') {
                const action = document.createElement('button');
                action.type = 'button';
                action.className = 'app-toast-action';
                setText(action, options.action.label);
                action.addEventListener('click', async (event) => {
                    await options.action.onClick(event, toast.dataset.toastId);
                    if (options.action.dismiss !== false) dismiss(toast);
                });
                toast.appendChild(action);
            }
        }

        function layoutToasts() {
            const container = getContainer();
            if (!container) return;

            const toasts = getToasts();
            const expanded = config.expand || container.dataset.expanded === 'true';
            const isTop = container.dataset.position.startsWith('top-');
            const ordered = [...toasts].reverse();
            let offset = 0;

            ordered.forEach((toast, index) => {
                const visible = expanded || index < config.visibleToasts;
                const currentOffset = expanded ? offset : Math.min(index, config.visibleToasts - 1) * 8;
                const scale = expanded ? 1 : Math.max(0.92, 1 - index * 0.035);
                const y = isTop ? currentOffset : -currentOffset;

                toast.dataset.offset = String(y);
                toast.dataset.scale = String(scale);
                toast.style.zIndex = String(100 - index);
                toast.style.opacity = visible ? '1' : '0';
                toast.style.pointerEvents = expanded || index === 0 ? 'auto' : 'none';
                toast.style.transform = `translate3d(0, ${y}px, 0) scale(${scale})`;

                if (expanded) offset += toast.offsetHeight + config.gap;
            });

            const frontHeight = ordered[0]?.offsetHeight || 0;
            const collapsedLift = Math.max(0, Math.min(ordered.length, config.visibleToasts) - 1) * 8;
            const height = expanded ? Math.max(0, offset - config.gap) : frontHeight + collapsedLift;
            container.style.height = `${height}px`;
            container.classList.toggle('is-expanded', expanded);
            container.classList.toggle('has-stack', ordered.length > 1);
        }

        function clearTimer(toast) {
            const state = stateByToast.get(toast);
            if (!state?.timer) return;
            window.clearTimeout(state.timer);
            state.timer = null;
            state.remaining = Math.max(0, state.remaining - (Date.now() - state.startedAt));
        }

        function startTimer(toast) {
            const state = stateByToast.get(toast);
            if (!state || state.duration <= 0 || state.timer || isInteracting || document.hidden) return;
            state.startedAt = Date.now();
            state.timer = window.setTimeout(() => dismiss(toast), state.remaining);
        }

        function pauseAll() {
            getToasts().forEach(clearTimer);
        }

        function resumeAll() {
            getToasts().forEach(startTimer);
        }

        function clearAllToasts() {
            const container = getContainer();
            const toasts = Array.from(container?.querySelectorAll('.app-toast') || []);

            toasts.forEach((toast) => {
                clearTimer(toast);
                toast.remove();
            });

            recentMessages.clear();
            isInteracting = false;

            if (container) {
                container.dataset.expanded = 'false';
                container.style.height = '0px';
                container.classList.remove('is-expanded', 'has-stack');
            }
        }

        function dismiss(target, immediate = false) {
            const toast = typeof target === 'string'
                ? getContainer()?.querySelector(`[data-toast-id="${CSS.escape(target)}"]`)
                : target;
            if (!toast || toast.classList.contains('is-leaving')) return;

            clearTimer(toast);
            toast.classList.add('is-leaving');
            toast.style.pointerEvents = 'none';

            const remove = () => {
                toast.remove();
                layoutToasts();
            };

            if (immediate || window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
                remove();
                return;
            }

            const position = getContainer()?.dataset.position || config.position;
            const directionX = position.endsWith('left') ? -1 : position.endsWith('right') ? 1 : 0;
            const directionY = position.startsWith('top-') ? -1 : 1;
            const offset = Number(toast.dataset.offset || 0);
            toast.style.opacity = '0';
            toast.style.transform = `translate3d(${directionX * 28}px, ${offset + directionY * 10}px, 0) scale(0.98)`;
            window.setTimeout(remove, 220);
        }

        function attachSwipe(toast) {
            let startX = 0;
            let deltaX = 0;
            let dragging = false;

            toast.addEventListener('pointerdown', (event) => {
                if (event.button !== 0 || event.target.closest('button')) return;
                startX = event.clientX;
                deltaX = 0;
                dragging = true;
                toast.classList.add('is-dragging');
                toast.setPointerCapture(event.pointerId);
                clearTimer(toast);
            });

            toast.addEventListener('pointermove', (event) => {
                if (!dragging) return;
                deltaX = event.clientX - startX;
                const offset = Number(toast.dataset.offset || 0);
                const scale = Number(toast.dataset.scale || 1);
                toast.style.transform = `translate3d(${deltaX}px, ${offset}px, 0) scale(${scale})`;
                toast.style.opacity = String(Math.max(0.35, 1 - Math.abs(deltaX) / 260));
            });

            const finish = () => {
                if (!dragging) return;
                dragging = false;
                toast.classList.remove('is-dragging');
                if (Math.abs(deltaX) >= 80) {
                    dismiss(toast);
                } else {
                    layoutToasts();
                    startTimer(toast);
                }
            };

            toast.addEventListener('pointerup', finish);
            toast.addEventListener('pointercancel', finish);
        }

        function show(message, options = {}) {
            const container = getContainer();
            if (!container || message == null || String(message).trim() === '') return null;

            const normalized = normalizeOptions({ ...options, message: String(message) });
            const duplicateKey = `${normalized.type}:${normalized.message}:${normalized.description || ''}`;
            const duplicateAt = recentMessages.get(duplicateKey);
            if (!normalized.id && duplicateAt && Date.now() - duplicateAt < 600) return null;
            recentMessages.set(duplicateKey, Date.now());
            window.setTimeout(() => recentMessages.delete(duplicateKey), 700);

            const requestedId = normalized.id == null ? null : String(normalized.id);
            const existing = requestedId
                ? container.querySelector(`[data-toast-id="${CSS.escape(requestedId)}"]`)
                : null;
            const toast = existing || document.createElement('article');
            const id = requestedId || `toast-${Date.now()}-${++sequence}`;

            toast.className = 'app-toast';
            toast.dataset.toastId = id;
            renderToast(toast, normalized);

            const state = stateByToast.get(toast) || {};
            clearTimer(toast);
            Object.assign(state, {
                duration: normalized.duration,
                remaining: normalized.duration,
                timer: null,
                startedAt: 0,
            });
            stateByToast.set(toast, state);

            if (!existing) {
                const isTop = container.dataset.position.startsWith('top-');
                toast.style.opacity = '0';
                toast.style.transform = `translate3d(0, ${isTop ? -18 : 18}px, 0) scale(0.98)`;
                toast.classList.add('is-entering');
                container.appendChild(toast);
                attachSwipe(toast);
            }

            while (getToasts().length > config.maxToasts) {
                dismiss(getToasts()[0], true);
            }

            if (existing) {
                requestAnimationFrame(() => {
                    layoutToasts();
                    startTimer(toast);
                });
            } else {
                // Dos fotogramas garantizan que el navegador pinte el estado inicial
                // antes de animar hacia la posicion final.
                requestAnimationFrame(() => {
                    requestAnimationFrame(() => {
                        toast.classList.remove('is-entering');
                        layoutToasts();
                        startTimer(toast);
                    });
                });
            }
            return id;
        }

        function update(id, message, options = {}) {
            return show(message, { ...options, id });
        }

        function promise(promiseOrFactory, options = {}) {
            const task = typeof promiseOrFactory === 'function' ? promiseOrFactory() : promiseOrFactory;
            const toastId = options.id || `toast-promise-${Date.now()}-${++sequence}`;
            const id = show(options.loading || 'Procesando…', {
                ...options,
                id: toastId,
                type: 'loading',
                duration: 0,
                description: options.loadingDescription,
            });

            return Promise.resolve(task)
                .then((result) => {
                    const message = typeof options.success === 'function' ? options.success(result) : options.success;
                    update(id, message || 'Proceso completado.', {
                        ...options,
                        type: 'success',
                        duration: options.duration ?? config.duration,
                        description: options.successDescription,
                    });
                    return result;
                })
                .catch((error) => {
                    const message = typeof options.error === 'function' ? options.error(error) : options.error;
                    update(id, message || 'No se pudo completar el proceso.', {
                        ...options,
                        type: 'error',
                        duration: options.errorDuration ?? 6000,
                        description: options.errorDescription,
                    });
                    throw error;
                });
        }

        function configure(options = {}) {
            config = {
                ...config,
                ...options,
                position: validPositions.has(options.position) ? options.position : config.position,
            };
            const container = getContainer();
            if (container) container.dataset.position = config.position;
            layoutToasts();
        }

        function initialize() {
            const container = getContainer();
            if (!container || container.dataset.initialized === 'true') return;
            container.dataset.initialized = 'true';
            container.dataset.position = config.position;

            container.addEventListener('pointerenter', () => {
                isInteracting = true;
                container.dataset.expanded = 'true';
                pauseAll();
                layoutToasts();
            });
            container.addEventListener('pointerleave', () => {
                if (container.contains(document.activeElement)) return;
                isInteracting = false;
                container.dataset.expanded = 'false';
                layoutToasts();
                resumeAll();
            });
            container.addEventListener('focusin', () => {
                isInteracting = true;
                container.dataset.expanded = 'true';
                pauseAll();
                layoutToasts();
            });
            container.addEventListener('focusout', () => {
                requestAnimationFrame(() => {
                    if (container.contains(document.activeElement) || container.matches(':hover')) return;
                    isInteracting = false;
                    container.dataset.expanded = 'false';
                    layoutToasts();
                    resumeAll();
                });
            });

            document.addEventListener('visibilitychange', () => document.hidden ? pauseAll() : resumeAll());
            // Las páginas restauradas desde el historial conservan su DOM y sus
            // temporizadores. Un toast es transitorio: no debe reaparecer al volver
            // desde crear o editar si en esa visita no hubo ninguna mutación.
            window.addEventListener('pagehide', clearAllToasts);
            window.addEventListener('pageshow', (event) => {
                if (event.persisted) clearAllToasts();
            });
            document.addEventListener('keydown', (event) => {
                if (event.altKey && event.code === 'KeyT') {
                    event.preventDefault();
                    const latestButton = getToasts().at(-1)?.querySelector('button');
                    latestButton?.focus();
                }
            });
            window.addEventListener('resize', layoutToasts, { passive: true });
        }

        window.AppToast = {
            show,
            success: (message, options = {}) => show(message, { ...options, type: 'success' }),
            info: (message, options = {}) => show(message, { ...options, type: 'info' }),
            warning: (message, options = {}) => show(message, { ...options, type: 'warning' }),
            error: (message, options = {}) => show(message, { ...options, type: 'error' }),
            loading: (message, options = {}) => show(message, { ...options, type: 'loading', duration: 0 }),
            update,
            promise,
            dismiss: (id) => id ? dismiss(String(id)) : getToasts().forEach((toast) => dismiss(toast)),
            configure,
        };

        window.showToast = function(message, type = 'success', duration = 3000, options = {}) {
            if (typeof type === 'object' && type !== null) return show(message, type);
            return show(message, { ...options, type, duration });
        };

        initialize();

        document.addEventListener('DOMContentLoaded', function() {
            initialize();
            @if(session('success'))
                window.AppToast.success(@json(session('success')));
            @endif
            @if(session('error'))
                window.AppToast.error(@json(session('error')), { duration: 6000 });
            @endif
            @if(session('warning'))
                window.AppToast.warning(@json(session('warning')), { duration: 5000 });
            @endif
            @if(session('info'))
                window.AppToast.info(@json(session('info')));
            @endif
        });
    })();
</script>
