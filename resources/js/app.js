import './bootstrap';
import './notifications-handler';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

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
