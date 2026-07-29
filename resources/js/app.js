import './bootstrap';
import './notifications-handler';

import Alpine from 'alpinejs';

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

function usersTableRestoreIntentKey() {
    const scope = document.documentElement.dataset.authStorageScope || 'guest';
    return `sistema-sec-tam.users-table-restore-intent.v1.${scope}`;
}

function markUsersTableHistoryReturn(targetHref) {
    try {
        const targetUrl = new URL(targetHref, window.location.href);
        sessionStorage.setItem(usersTableRestoreIntentKey(), JSON.stringify({
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

    try {
        const restoreIntentKey = usersTableRestoreIntentKey();
        const restoreIntent = JSON.parse(sessionStorage.getItem(restoreIntentKey) || 'null');
        const currentTarget = `${window.location.pathname}${window.location.search}`;

        if (restoreIntent?.target === currentTarget) {
            sessionStorage.removeItem(restoreIntentKey);
        }
    } catch (error) {
        try { sessionStorage.removeItem(usersTableRestoreIntentKey()); } catch (storageError) {}
    }
});

window.navigateBackOrVisit = function navigateBackOrVisit(targetHref) {
    if (canReturnThroughHistory(targetHref)) {
        markUsersTableHistoryReturn(targetHref);
        window.history.back();
        return;
    }

    window.location.assign(targetHref);
};

document.addEventListener('click', (event) => {
    const preserveLink = event.target.closest('a[data-users-table-return]');
    if (preserveLink && isPlainPrimaryNavigation(event, preserveLink)) {
        markUsersTableHistoryReturn(preserveLink.dataset.usersTableReturn);
    }

    const link = event.target.closest('a[data-history-back="true"]');

    if (!link
        || !isPlainPrimaryNavigation(event, link)
        || !canReturnThroughHistory(link.href)) {
        return;
    }

    event.preventDefault();
    markUsersTableHistoryReturn(link.href);
    window.history.back();
});
