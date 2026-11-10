import './bootstrap';
import './notifications-handler';
import './filter-select';
import './form-validation';
import './number-input-wheel';
import { initDataTablePageSize, initDataTableRowActions, initDataTableSearch } from './data-table';

import Alpine from 'alpinejs';

window.AppDataTable = Object.freeze({
    ...(window.AppDataTable || {}),
    initSearch: initDataTableSearch,
    initPageSize: initDataTablePageSize,
    initRowActions: initDataTableRowActions,
});
window.dispatchEvent(new CustomEvent('app:data-table-ready'));

const logoutHistoryMarker = 'sistema-sec-tam.logout-completed';
const isAuthenticatedDocument = document.documentElement.hasAttribute('data-auth-storage-scope');

function readLogoutHistoryMarker() {
    try {
        return sessionStorage.getItem(logoutHistoryMarker) === 'true';
    } catch (error) {
        return false;
    }
}

function writeLogoutHistoryMarker(value) {
    try {
        if (value) sessionStorage.setItem(logoutHistoryMarker, 'true');
        else sessionStorage.removeItem(logoutHistoryMarker);
    } catch (error) {
        // The server-side no-store policy remains the primary protection.
    }
}

if (isAuthenticatedDocument) {
    // A protected document restored after logout must never reveal its cached
    // contents, regardless of how many history entries the user traverses.
    if (readLogoutHistoryMarker()) {
        window.location.replace('/login');
    }

    window.addEventListener('pageshow', () => {
        if (readLogoutHistoryMarker()) {
            window.location.replace('/login');
        }
    });
}

document.addEventListener('submit', (event) => {
    const form = event.target;
    if (!(form instanceof HTMLFormElement)) return;

    try {
        const action = new URL(form.action, window.location.href);
        if (action.origin === window.location.origin && action.pathname === '/logout') {
            writeLogoutHistoryMarker(true);
        } else if (action.origin === window.location.origin && action.pathname === '/login') {
            writeLogoutHistoryMarker(false);
        }
    } catch (error) {
        // An invalid action will be handled by the browser as usual.
    }
});

window.Alpine = Alpine;

Alpine.data('notificationCenter', () => ({
    openNotifications: false,
    notifications: [],
    unreadCount: 0,
    loading: true,
    loadError: false,
    markingAll: false,
    poller: null,

    init() {
        this.loadNotifications();
        this.poller = window.setInterval(() => this.loadNotifications(true), 30000);
    },

    destroy() {
        if (this.poller) {
            window.clearInterval(this.poller);
        }
    },

    toggleNotifications() {
        if (this.openNotifications) {
            this.closeNotifications();
            return;
        }

        this.openNotifications = true;

        this.$nextTick(() => {
            const list = this.$refs.notificationsList;

            if (list) {
                list.scrollTop = 0;
            }
        });
    },

    closeNotifications(restoreFocus = false) {
        if (!this.openNotifications) {
            return;
        }

        this.openNotifications = false;

        if (restoreFocus) {
            this.$nextTick(() => this.$refs.notificationsTrigger?.focus());
        }
    },

    async loadNotifications(silent = false) {
        if (!silent || this.notifications.length === 0) {
            this.loading = true;
        }

        try {
            const response = await fetch('/notificaciones', {
                headers: {
                    Accept: 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
            });

            if (!response.ok) {
                throw new Error('No se pudieron cargar las notificaciones.');
            }

            const data = await response.json();
            this.notifications = Array.isArray(data.notifications) ? data.notifications : [];
            this.unreadCount = Number(data.unread_count || 0);
            this.loadError = false;
        } catch (error) {
            if (this.notifications.length === 0) {
                this.loadError = true;
            }
        } finally {
            this.loading = false;
        }
    },

    async markRead(notification) {
        if (!notification || notification.read) {
            return;
        }

        try {
            const response = await fetch(`/notificaciones/${notification.id}/marcar-leida`, {
                method: 'POST',
                headers: {
                    Accept: 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                },
            });

            if (!response.ok) {
                return;
            }

            notification.read = true;
            this.unreadCount = Math.max(0, this.unreadCount - 1);
        } catch (error) {
            // Reading the notification remains possible even if this request fails.
        }
    },

    async markAllRead() {
        if (this.markingAll || this.unreadCount === 0) {
            return;
        }

        this.markingAll = true;

        try {
            const response = await fetch('/notificaciones/marcar-todas-leidas', {
                method: 'POST',
                headers: {
                    Accept: 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                },
            });

            if (!response.ok) {
                return;
            }

            this.notifications.forEach((notification) => {
                notification.read = true;
            });
            this.unreadCount = 0;
        } finally {
            this.markingAll = false;
        }
    },

    openReport(notification) {
        this.markRead(notification);

        if (typeof window.openPublicationFromNotification === 'function') {
            window.openPublicationFromNotification(notification.publication_id, notification.comment_id);
            return;
        }

        const comment = notification.comment_id ? `&comment=${notification.comment_id}` : '';
        window.location.assign(`/reportes/publicaciones?publication=${notification.publication_id}${comment}`);
    },
}));

Alpine.data('accountMenu', () => ({
    openProfile: false,

    toggleProfile() {
        this.openProfile = !this.openProfile;
    },

    closeProfile(restoreFocus = false) {
        if (!this.openProfile) {
            return;
        }

        this.openProfile = false;

        if (restoreFocus) {
            this.$nextTick(() => this.$refs.profileTrigger?.focus());
        }
    },
}));

Alpine.start();

function canReturnThroughHistory(targetHref) {
    if (!document.referrer || window.history.length <= 1) {
        return false;
    }

    try {
        const previousUrl = new URL(document.referrer);
        const targetUrl = new URL(targetHref, window.location.href);

        return previousUrl.origin === window.location.origin
            && previousUrl.pathname === targetUrl.pathname
            && previousUrl.search === targetUrl.search;
    } catch (error) {
        return false;
    }
}

function tableRestoreIntentKey(tableScope = 'users') {
    const scope = document.documentElement.dataset.authStorageScope || 'guest';
    return `sistema-sec-tam.${tableScope}-table-restore-intent.v1.${scope}`;
}

function markTableHistoryReturn(targetHref, tableScope = 'users') {
    try {
        const targetUrl = new URL(targetHref, window.location.href);
        sessionStorage.setItem(tableRestoreIntentKey(tableScope), JSON.stringify({
            target: `${targetUrl.pathname}${targetUrl.search}`,
            createdAt: Date.now(),
        }));
    } catch (error) {
        // Navigation still works when session storage is unavailable.
    }
}

function isPlainPrimaryNavigation(event, link) {
    return !event.defaultPrevented
        && event.button === 0
        && !event.metaKey
        && !event.ctrlKey
        && !event.shiftKey
        && !event.altKey
        && link.target !== '_blank'
        && !link.hasAttribute('download');
}

window.addEventListener('pageshow', (event) => {
    if (!event.persisted) return;

    ['users', 'deaths'].forEach((tableScope) => {
        try {
            const restoreIntentKey = tableRestoreIntentKey(tableScope);
            const restoreIntent = JSON.parse(sessionStorage.getItem(restoreIntentKey) || 'null');
            const currentTarget = `${window.location.pathname}${window.location.search}`;

            if (restoreIntent?.target === currentTarget) {
                sessionStorage.removeItem(restoreIntentKey);
            }
        } catch (error) {
            try { sessionStorage.removeItem(tableRestoreIntentKey(tableScope)); } catch (storageError) {}
        }
    });
});

window.navigateBackOrVisit = function navigateBackOrVisit(targetHref) {
    if (canReturnThroughHistory(targetHref)) {
        markTableHistoryReturn(targetHref);
        window.history.back();
        return;
    }

    window.location.assign(targetHref);
};

document.addEventListener('click', (event) => {
    const preserveLink = event.target.closest('a[data-users-table-return]');
    if (preserveLink && isPlainPrimaryNavigation(event, preserveLink)) {
        markTableHistoryReturn(preserveLink.dataset.usersTableReturn);
    }

    const preserveDeathsLink = event.target.closest('a[data-deaths-table-return]');
    if (preserveDeathsLink && isPlainPrimaryNavigation(event, preserveDeathsLink)) {
        const returnUrl = new URL(preserveDeathsLink.dataset.deathsTableReturn, window.location.href);
        const destinationUrl = new URL(preserveDeathsLink.href, window.location.href);
        const currentReturnTarget = `${window.location.pathname}${window.location.search}`;

        destinationUrl.searchParams.set('return_to', currentReturnTarget);
        preserveDeathsLink.href = destinationUrl.toString();
        markTableHistoryReturn(`${returnUrl.pathname}${window.location.search}`, 'deaths');
    }

    const link = event.target.closest('a[data-history-back="true"]');

    if (!link
        || !isPlainPrimaryNavigation(event, link)
        || !canReturnThroughHistory(link.href)) {
        return;
    }

    event.preventDefault();
    const historyScope = link.dataset.historyBackScope || 'users';
    markTableHistoryReturn(link.href, historyScope);
    window.history.back();
});

function uiTomSelectSearchInput(target) {
    if (!(target instanceof HTMLInputElement)) return null;
    return target.matches('.ui-form-fields .ts-control > input') ? target : null;
}

function localizeUiTomSelectEmptyState(wrapper) {
    window.requestAnimationFrame(() => {
        wrapper.querySelectorAll('.ts-dropdown .no-results').forEach((emptyState) => {
            emptyState.lang = 'es';
            emptyState.textContent = 'No se encontraron resultados';
        });
    });
}

function syncUiTomSelectSearchState(input) {
    const wrapper = input.closest('.ts-wrapper');
    if (!wrapper) return;

    wrapper.classList.toggle('ui-has-search-query', input.value.trim().length > 0);
    localizeUiTomSelectEmptyState(wrapper);
}

/*
 * Single-select searches keep their committed value, but only show the query
 * while the user is typing. This avoids presenting the value and query as if
 * they were two selected items. Event delegation also covers dynamic cards.
 */
document.addEventListener('input', (event) => {
    const input = uiTomSelectSearchInput(event.target);
    if (input) syncUiTomSelectSearchState(input);
});

document.addEventListener('change', (event) => {
    const select = event.target;
    if (!(select instanceof HTMLSelectElement)
        || !select.matches('.ui-form-fields select.tomselect-select')) return;

    select.nextElementSibling?.classList.remove('ui-has-search-query');
});

document.addEventListener('focusout', (event) => {
    const input = uiTomSelectSearchInput(event.target);
    if (!input) return;

    const wrapper = input.closest('.ts-wrapper');
    window.setTimeout(() => {
        if (!wrapper || wrapper.contains(document.activeElement)) return;

        const select = wrapper.previousElementSibling;
        if (select instanceof HTMLSelectElement && select.tomselect?.control_input === input) {
            select.tomselect.setTextboxValue('');
            select.tomselect.refreshOptions(false);
        } else {
            input.value = '';
        }

        wrapper.classList.remove('ui-has-search-query');
    }, 0);
});

/**
 * Smoothly expands or collapses a section inside the standard filter panel.
 */
window.setFilterSectionExpanded = (section, open) => {
    if (!(section instanceof HTMLElement)) return;

    const toggle = section.querySelector('[data-filter-section-toggle]');
    const content = section.querySelector(':scope > .users-filter-section-content');
    toggle?.setAttribute('aria-expanded', String(open));

    if (!(content instanceof HTMLElement)
        || window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
        section.classList.toggle('is-open', open);
        return;
    }

    const runningAnimation = content.getAnimations().find(animation => animation.playState === 'running');
    if (runningAnimation) {
        try {
            runningAnimation.commitStyles();
        } catch (_) {
            // Continue from the current rendered size when commitStyles is unavailable.
        }
        runningAnimation.cancel();
    }

    const startHeight = content.getBoundingClientRect().height;
    if (open) section.classList.add('is-open');
    const endHeight = open ? content.scrollHeight : 0;
    const animation = content.animate([
        { height: `${startHeight}px`, opacity: open ? 0 : 1 },
        { height: `${endHeight}px`, opacity: open ? 1 : 0 },
    ], {
        duration: 180,
        easing: 'cubic-bezier(0.2, 0, 0, 1)',
    });

    content.style.overflow = 'hidden';
    animation.addEventListener('finish', () => {
        section.classList.toggle('is-open', open);
        content.style.removeProperty('height');
        content.style.removeProperty('opacity');
        content.style.removeProperty('overflow');
    }, { once: true });
};

function currentLocalDateValue() {
    const now = new Date();
    const year = now.getFullYear();
    const month = String(now.getMonth() + 1).padStart(2, '0');
    const day = String(now.getDate()).padStart(2, '0');

    return `${year}-${month}-${day}`;
}

window.AppFilterDateRange = Object.freeze({
    clear({ start, end, error, source = 'date-range' }) {
        [start, end].forEach(control => control?.removeAttribute('aria-invalid'));

        if (error && (!error.dataset.errorSource || error.dataset.errorSource === source)) {
            error.textContent = '';
            error.classList.add('hidden');
            delete error.dataset.errorSource;
        }
    },

    validate({ start, end, error, source = 'date-range', focus = true }) {
        this.clear({ start, end, error, source });

        const startValue = start?.value || '';
        const endValue = end?.value || '';
        const maximum = start?.max || end?.max || currentLocalDateValue();
        let invalidControl = null;
        let message = '';

        if (startValue && startValue > maximum) {
            invalidControl = start;
            message = 'La fecha inicial no puede ser posterior a hoy.';
        } else if (endValue && endValue > maximum) {
            invalidControl = end;
            message = 'La fecha final no puede ser posterior a hoy.';
        } else if (startValue && endValue && startValue > endValue) {
            invalidControl = end;
            message = 'La fecha final debe ser posterior o igual a la fecha inicial.';
        }

        if (!message) return { valid: true, message: '', invalidControl: null };

        invalidControl?.setAttribute('aria-invalid', 'true');
        if (error) {
            error.textContent = message;
            error.dataset.errorSource = source;
            error.classList.remove('hidden');
        }
        if (focus) invalidControl?.focus();

        return { valid: false, message, invalidControl };
    },
});

window.AppFilterPanel = {
    highlightTimers: new WeakMap(),
    panelTimers: new WeakMap(),
    sectionTimers: new WeakMap(),

    getControl(form, name) {
        return form?.querySelector(`[name="${name}"]`) || null;
    },

    setValue(form, name, value) {
        const control = this.getControl(form, name);
        if (!control) return;

        if (control.tomselect) {
            if (value === null || value === '') control.tomselect.clear(true);
            else control.tomselect.setValue(value, true);
            return;
        }

        if (control instanceof HTMLInputElement && ['checkbox', 'radio'].includes(control.type)) {
            control.checked = Boolean(value);
            return;
        }

        control.value = value ?? '';
    },

    clear(form, fields, defaults = {}) {
        fields.forEach(name => {
            if (name.endsWith('[]')) {
                form?.querySelectorAll(`[name="${name}"]`).forEach(control => { control.checked = false; });
                return;
            }
            this.setValue(form, name, Object.prototype.hasOwnProperty.call(defaults, name) ? defaults[name] : '');
        });
    },

    capture(form) {
        if (!form) return null;
        return Array.from(form.elements)
            .filter(control => control instanceof HTMLInputElement || control instanceof HTMLSelectElement)
            .map(control => ({
                control,
                value: control instanceof HTMLSelectElement && control.multiple
                    ? Array.from(control.selectedOptions, option => option.value)
                    : control.value,
                checked: control instanceof HTMLInputElement ? control.checked : null,
            }));
    },

    restore(snapshot) {
        if (!snapshot) return;
        snapshot.forEach(({ control, value, checked }) => {
            if (!control?.isConnected) return;
            if (control instanceof HTMLInputElement && ['checkbox', 'radio'].includes(control.type)) {
                control.checked = checked;
                return;
            }
            if (control.tomselect) {
                if (Array.isArray(value)) control.tomselect.setValue(value, true);
                else if (value === '') control.tomselect.clear(true);
                else control.tomselect.setValue(value, true);
                return;
            }
            control.value = value;
        });
    },

    highlightTarget(target) {
        if (!(target instanceof HTMLElement)
            || window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;

        const previousTimer = this.highlightTimers.get(target);
        if (previousTimer) window.clearTimeout(previousTimer);
        target.classList.remove('app-filter-target-highlight');
        void target.offsetWidth;
        target.classList.add('app-filter-target-highlight');

        const timer = window.setTimeout(() => {
            target.classList.remove('app-filter-target-highlight');
            this.highlightTimers.delete(target);
        }, 800);
        this.highlightTimers.set(target, timer);
    },

    animateEntry(target, className, timers, duration) {
        if (!(target instanceof HTMLElement)
            || window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;

        const previousTimer = timers.get(target);
        if (previousTimer) window.clearTimeout(previousTimer);
        target.classList.remove(className);
        void target.offsetWidth;
        target.classList.add(className);

        const timer = window.setTimeout(() => {
            target.classList.remove(className);
            timers.delete(target);
        }, duration);
        timers.set(target, timer);
    },

    placeCaretAtEnd(control) {
        if (!(control instanceof HTMLInputElement || control instanceof HTMLTextAreaElement)) return;
        if (control instanceof HTMLInputElement
            && !['text', 'search', 'tel', 'url', 'email', 'number'].includes(control.type)) return;

        const end = control.value.length;
        try {
            control.setSelectionRange(end, end);
        } catch {
            // Algunos tipos de input no exponen una selección de texto.
        }
    },

    openSection({ panel, sectionKey, focusName = '', form, openPanel }) {
        if (!(panel instanceof HTMLElement)) return false;
        const section = Array.from(panel.querySelectorAll('[data-filter-section]'))
            .find(item => item.dataset.filterSection === sectionKey);
        if (!section) return false;

        const panelWasClosed = panel.classList.contains('is-collapsed');
        openPanel?.();
        if (panelWasClosed) this.animateEntry(panel, 'app-filter-panel-enter', this.panelTimers, 220);
        window.setFilterSectionExpanded?.(section, true);
        this.animateEntry(section, 'app-filter-section-reveal', this.sectionTimers, 300);
        const icon = section.querySelector('[data-filter-section-toggle] i');
        icon?.classList.add('fa-chevron-down');
        icon?.classList.remove('fa-chevron-right');

        window.setTimeout(() => {
            const body = panel.querySelector('.users-filter-panel-body');
            let shouldWaitForScroll = false;
            if (body instanceof HTMLElement) {
                const bodyRect = body.getBoundingClientRect();
                const sectionRect = section.getBoundingClientRect();
                const targetTop = Math.max(0, body.scrollTop + sectionRect.top - bodyRect.top - 8);
                shouldWaitForScroll = Math.abs(body.scrollTop - targetTop) > 8;
                body.scrollTo({
                    top: targetTop,
                    behavior: window.matchMedia('(prefers-reduced-motion: reduce)').matches ? 'auto' : 'smooth',
                });
            }

            window.setTimeout(() => {
                const control = focusName ? this.getControl(form, focusName) : null;
                const dateProxy = focusName ? section.querySelector(`[data-filter-date-proxy="${focusName}"]`) : null;
                const selectedOption = focusName
                    ? Array.from(section.querySelectorAll('.users-filter-option[data-filter-target]'))
                        .find(option => option.dataset.filterTarget === focusName
                            && String(option.dataset.filterValue || '') === String(control?.value || ''))
                    : null;

                let focusTarget = null;
                if (dateProxy) {
                    dateProxy.focus({ preventScroll: true });
                    focusTarget = dateProxy;
                } else if (selectedOption) {
                    selectedOption.focus({ preventScroll: true });
                    focusTarget = selectedOption;
                } else if (control?.tomselect) {
                    control.tomselect.focus();
                    focusTarget = control.tomselect.wrapper || control.tomselect.control;
                } else if (control && !control.hidden && control.tabIndex !== -1) {
                    control.focus({ preventScroll: true });
                    this.placeCaretAtEnd(control);
                    focusTarget = control;
                } else {
                    focusTarget = section.querySelector('[data-filter-section-toggle]');
                    focusTarget?.focus({ preventScroll: true });
                }
                this.highlightTarget(focusTarget);
            }, shouldWaitForScroll ? 240 : 0);
        }, 200);

        return true;
    },
};
