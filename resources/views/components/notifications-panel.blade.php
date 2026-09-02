<div class="app-notifications-header">
    <div>
        <h3>Notificaciones</h3>
        <p x-text="unreadCount > 0
            ? `${unreadCount} ${unreadCount === 1 ? 'notificación nueva' : 'notificaciones nuevas'}`
            : 'Estás al día'"></p>
    </div>
    <button type="button"
            class="app-notifications-mark-all"
            x-show="unreadCount > 0"
            :disabled="markingAll"
            @click="markAllRead()">
        <span x-text="markingAll
            ? 'Marcando…'
            : (unreadCount === 1 ? 'Marcar como leída' : 'Marcar todas como leídas')"></span>
    </button>
</div>

<div class="app-notifications-list"
     x-ref="notificationsList"
     aria-live="polite"
     :aria-busy="loading.toString()">
    <template x-if="loading && notifications.length === 0">
        <div class="app-notifications-loading" aria-label="Cargando notificaciones">
            <span></span>
            <span></span>
            <span></span>
        </div>
    </template>

    <template x-if="loadError && notifications.length === 0">
        <div class="app-notifications-state">
            <i class="fa-solid fa-triangle-exclamation" aria-hidden="true"></i>
            <strong>No pudimos cargar las notificaciones</strong>
            <span>Comprueba tu conexión e inténtalo nuevamente.</span>
            <button type="button" @click="loadNotifications()">Reintentar</button>
        </div>
    </template>

    <template x-if="!loading && !loadError && notifications.length === 0">
        <div class="app-notifications-state">
            <i class="fa-regular fa-bell" aria-hidden="true"></i>
            <strong>No tienes notificaciones</strong>
            <span>Las novedades de tus reportes aparecerán aquí.</span>
        </div>
    </template>

    <template x-for="notif in notifications" :key="notif.id">
        <article class="app-notification-item"
                 :class="{
                    'is-unread': !notif.read,
                    'is-actionable': Boolean(notif.publication_id),
                    [`is-${notif.tone || 'neutral'}`]: true
                 }"
                 :role="notif.publication_id ? 'link' : null"
                 :tabindex="notif.publication_id ? 0 : null"
                 :aria-label="notif.publication_id ? `Abrir ${notif.event_label.toLowerCase()}` : null"
                 @click="if (notif.publication_id && !$event.target.closest('.app-notification-inline-action')) openReport(notif)"
                 @keydown.enter.self.prevent="if (notif.publication_id) openReport(notif)"
                 @keydown.space.self.prevent="if (notif.publication_id) openReport(notif)"
                 x-data="{
                    expanded: false,
                    detailExpandable: false,
                    subjectExpandable: false,
                    exceedsCollapsedLines(element, lines = 2) {
                        if (!element) return false;

                        const lineHeight = Number.parseFloat(window.getComputedStyle(element).lineHeight);
                        if (!Number.isFinite(lineHeight)) return false;

                        return element.scrollHeight > (lineHeight * lines) + 1;
                    },
                    measure() {
                        this.$nextTick(() => {
                            const detail = this.$refs.detail;
                            const subject = this.$refs.subject;
                            this.detailExpandable = this.exceedsCollapsedLines(detail);
                            this.subjectExpandable = this.exceedsCollapsedLines(subject);
                        });
                    }
                 }"
                 x-init="measure()"
                 x-effect="if (openNotifications) measure()"
                 @resize.window.debounce.150ms="measure()">
            <span x-show="!notif.read" class="app-notification-unread-dot" aria-hidden="true"></span>
            <span x-show="!notif.read" class="sr-only">Notificación no leída.</span>

            <span class="app-notification-type-icon"
                  :class="`is-${notif.tone || 'neutral'}`"
                  :title="notif.event_label"
                  aria-hidden="true">
                <i :class="notif.icon || 'fa-regular fa-bell'"></i>
            </span>

            <div class="app-notification-content">
                <div class="app-notification-summary-row">
                    <p class="app-notification-summary">
                        <strong x-show="notif.actor"
                                :title="notif.actor?.full_name"
                                x-text="notif.actor?.display_name"></strong>
                        <span x-text="notif.action_text"></span>
                    </p>
                    <time class="app-notification-time"
                          :datetime="notif.created_at"
                          :title="notif.created_at_label"
                          x-text="notif.time_ago"></time>
                </div>

                <p class="app-notification-subject"
                   x-show="notif.publication_title"
                   :title="notif.publication_title">
                    <span x-ref="subject"
                          :class="{ 'is-expanded': expanded }"
                          x-text="notif.publication_title"></span>
                </p>

                <div x-show="notif.detail"
                    class="app-notification-detail"
                     :class="{ 'is-comment': notif.type === 'comment' }">
                    <span x-show="notif.type !== 'rejection'"
                          class="sr-only"
                          x-text="notif.detail_label"></span>
                    <p x-ref="detail"
                       :class="{ 'is-expanded': expanded }">
                        <span x-show="notif.type === 'rejection'"
                              class="app-notification-detail-prefix">Motivo:</span>
                        <span class="app-notification-detail-text"
                              x-text="notif.detail"></span>
                    </p>
                </div>

                <div class="app-notification-actions"
                     x-show="expanded || subjectExpandable || (notif.detail && detailExpandable) || (!notif.read && !notif.publication_id)">
                    <button type="button"
                            class="app-notification-inline-action"
                            x-show="expanded || subjectExpandable || (notif.detail && detailExpandable)"
                            @click="expanded = !expanded"
                            :aria-expanded="expanded.toString()">
                        <span x-text="expanded ? 'Mostrar menos' : 'Mostrar más'"></span>
                    </button>
                    <button type="button"
                            class="app-notification-inline-action"
                            x-show="!notif.read && !notif.publication_id"
                            @click="markRead(notif)">
                        Marcar como leída
                    </button>
                </div>
            </div>
        </article>
    </template>
</div>

<div class="app-notifications-footer">
    <a href="/reportes/publicaciones">
        Ver todos los reportes <span aria-hidden="true">&rarr;</span>
    </a>
</div>
