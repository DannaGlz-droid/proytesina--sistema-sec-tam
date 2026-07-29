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
        <span x-text="markingAll ? 'Marcando…' : 'Marcar todas como leídas'"></span>
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
                 :class="{ 'is-unread': !notif.read }"
                 x-data="{
                    expanded: false,
                    detailExpandable: false,
                    subjectExpandable: false,
                    measure() {
                        this.$nextTick(() => {
                            const detail = this.$refs.detail;
                            const subject = this.$refs.subject;
                            this.detailExpandable = Boolean(detail && detail.scrollHeight > detail.clientHeight + 1);
                            this.subjectExpandable = Boolean(subject && subject.scrollHeight > subject.clientHeight + 1);
                        });
                    }
                 }"
                 x-init="measure()"
                 x-effect="if (openNotifications) measure()"
                 @resize.window.debounce.150ms="measure()">
            <div class="app-notification-heading">
                <div class="app-notification-title-wrap">
                    <span class="app-notification-type-icon"
                          :class="`is-${notif.tone || 'neutral'}`"
                          aria-hidden="true">
                        <i :class="notif.icon || 'fa-regular fa-bell'"></i>
                    </span>
                    <h4 class="app-notification-title" x-text="notif.event_label"></h4>
                </div>
                <div class="app-notification-meta">
                    <time class="app-notification-time"
                          :datetime="notif.created_at"
                          :title="notif.created_at_label"
                          x-text="notif.time_ago"></time>
                </div>
            </div>

            <span x-show="!notif.read" class="sr-only">Notificación no leída.</span>

            <p class="app-notification-action">
                <strong x-show="notif.actor"
                        :title="notif.actor?.full_name"
                        x-text="notif.actor?.display_name"></strong>
                <span x-text="notif.action_text"></span>
            </p>

            <div class="app-notification-subject"
                 x-show="notif.publication_title"
                 :title="notif.publication_title">
                <span>Reporte</span>
                <strong x-ref="subject"
                        :class="{ 'is-expanded': expanded }"
                        x-text="notif.publication_title"></strong>
            </div>

            <div x-show="notif.detail" class="app-notification-detail">
                <span x-text="notif.detail_label"></span>
                <p x-ref="detail"
                   :class="{ 'is-expanded': expanded }"
                   x-text="notif.detail"></p>
            </div>

            <div class="app-notification-actions">
                <button type="button"
                        x-show="subjectExpandable || (notif.detail && detailExpandable)"
                        @click="expanded = !expanded"
                        :aria-expanded="expanded.toString()">
                    <span x-text="expanded ? 'Ver menos' : 'Ver más'"></span>
                </button>
                <button type="button"
                        x-show="notif.publication_id"
                        @click="openReport(notif)">
                    Ir al reporte
                </button>
                <button type="button"
                        x-show="!notif.read && !notif.publication_id"
                        @click="markRead(notif)">
                    Marcar como leída
                </button>
            </div>
        </article>
    </template>
</div>

<div class="app-notifications-footer">
    <a href="/reportes/publicaciones">
        Ver todos los reportes <span aria-hidden="true">&rarr;</span>
    </a>
</div>
