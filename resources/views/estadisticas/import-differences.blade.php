@extends('layouts.principal')
@section('title', 'Revisión de diferencias')
@section('content')
    @include('components.header-admin')
    @include('components.nav-estadisticas')

    <main class="users-management-page statistics-failed-imports-page statistics-import-differences-page px-4 sm:px-6 lg:px-10 pt-5 lg:pt-7 pb-8 lg:pb-10">
        <div id="differences-announcer" class="sr-only" role="status" aria-live="polite" aria-atomic="true"></div>

        <x-ui.page-header
            class="users-management-header statistics-import-differences-header"
            title="Diferencias de importación"
            description="Compare el registro actual con el archivo importado y elija cuál conservar."
        >
            <x-slot:actions>
                <a href="{{ route('statistic.import-history-view') }}" class="users-page-create-btn">
                    <i class="fas fa-arrow-left" aria-hidden="true"></i>Volver al historial
                </a>
            </x-slot:actions>
        </x-ui.page-header>

        <x-ui.data-table.panel class="users-table-card failed-imports-card import-differences-card" label="Diferencias detectadas en la importación">
            <x-ui.data-table.toolbar class="failed-imports-controls import-differences-controls">
                <div class="failed-imports-file-context import-differences-file-context">
                    <span class="failed-imports-file-icon" aria-hidden="true"><i class="far fa-file-excel"></i></span>
                    <div>
                        @php
                            $fileExtensionPosition = strrpos($importFileName, '.');
                            $importFileBaseName = $fileExtensionPosition > 0
                                ? substr($importFileName, 0, $fileExtensionPosition)
                                : $importFileName;
                            $importFileExtension = $fileExtensionPosition > 0
                                ? substr($importFileName, $fileExtensionPosition)
                                : '';
                        @endphp
                        <strong
                            class="import-differences-file-name"
                            tabindex="0"
                            title="{{ $importFileName }}"
                            aria-label="Nombre completo del archivo: {{ $importFileName }}"
                        >
                            <span class="import-differences-file-name-base">{{ $importFileBaseName }}</span>
                            <span class="import-differences-file-name-extension">{{ $importFileExtension }}</span>
                        </strong>
                        @if ($isPreview ?? false)
                            <small class="import-differences-preview-note"><i class="fas fa-eye" aria-hidden="true"></i> Vista previa · decisiones deshabilitadas</small>
                        @endif
                    </div>
                </div>

                <x-ui.data-table.search
                    id="search-import-differences"
                    clear-id="clear-import-differences-search"
                    placeholder="Buscar por folio o persona..."
                    label="Buscar diferencias"
                />

                <x-ui.data-table.page-size
                    id="per-page-import-differences"
                    button-id="per-page-import-differences-button"
                    label-id="per-page-import-differences-label"
                    menu-id="per-page-import-differences-menu"
                    :options="[4, 6, 10]"
                    :selected="10"
                />
            </x-ui.data-table.toolbar>

            <nav class="import-differences-tabs" aria-label="Estado de las diferencias">
                <button type="button" class="import-differences-tab is-active" data-difference-status="pending" aria-pressed="true">
                    Pendientes <span id="difference-count-pending">0</span>
                </button>
                <button type="button" class="import-differences-tab" data-difference-status="resolved" aria-pressed="false">
                    Resueltas <span id="difference-count-resolved">0</span>
                </button>
            </nav>

            <section id="differences-loading" class="import-differences-state" role="status">
                <div class="import-differences-loading-grid" aria-hidden="true">
                    @for ($card = 0; $card < 2; $card++)
                        <article class="import-difference-skeleton">
                            <div class="users-table-skeleton"><div></div></div>
                            <div class="users-table-skeleton"><div></div><div></div></div>
                            <div class="users-table-skeleton"><div></div></div>
                        </article>
                    @endfor
                </div>
                <span class="sr-only">Cargando diferencias</span>
            </section>

            <section id="differences-list" class="import-differences-list hidden" aria-live="polite"></section>

            <x-ui.data-table.footer
                class="users-table-footer failed-imports-footer import-differences-footer"
                info-id="differences-info"
                pagination-id="differences-pagination"
                initial-text="Preparando diferencias"
            />
        </x-ui.data-table.panel>
    </main>
@endsection

@push('scripts')
<script>
(() => {
    const importId = @json($importId);
    const expectedDifferenceCount = @json($expectedDifferenceCount);
    const isPreview = @json($isPreview ?? false);
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
    const list = document.getElementById('differences-list');
    const loading = document.getElementById('differences-loading');
    const info = document.getElementById('differences-info');
    const pagination = document.getElementById('differences-pagination');
    const searchInput = document.getElementById('search-import-differences');
    const clearSearch = document.getElementById('clear-import-differences-search');
    const perPageSelect = document.getElementById('per-page-import-differences');
    let currentStatus = 'pending';
    let currentPage = 1;
    let searchTimer = null;
    let activeRequest = null;
    let shouldScrollToReview = false;

    const escapeHtml = value => String(value ?? '')
        .replaceAll('&', '&amp;')
        .replaceAll('<', '&lt;')
        .replaceAll('>', '&gt;')
        .replaceAll('"', '&quot;')
        .replaceAll("'", '&#039;');

    const announce = message => {
        const node = document.getElementById('differences-announcer');
        if (node) node.textContent = message;
    };

    const toast = (message, type = 'success', duration = 3500, options = {}) => {
        if (typeof window.showToast === 'function') window.showToast(message, type, duration, options);
    };

    const statusLabel = status => ({
        applied: 'Se usó el archivo',
        kept: 'Se conservó el registro actual',
        mixed: 'Se combinaron valores',
    }[status] || 'Resuelta');

    const resolvedDate = value => {
        if (!value) return '';
        return new Intl.DateTimeFormat('es-MX', {
            dateStyle: 'medium',
            timeStyle: 'short',
        }).format(new Date(value));
    };

    function renderPendingCard(item) {
        const fieldCount = item.field_count ?? item.changed_fields.length;
        const fieldSummary = `${fieldCount} ${fieldCount === 1 ? 'campo distinto' : 'campos distintos'}`;
        const fields = (item.changed_fields || []).map(field => `
            <div class="import-difference-field-row">
                <div class="import-difference-field-label">${escapeHtml(field.label)}</div>
                <div class="import-difference-plain-value import-difference-plain-value--current">
                    <span>Registro actual</span>
                    <strong>${escapeHtml(field.previous)}</strong>
                </div>
                <span class="import-difference-row-arrow" aria-hidden="true">&rarr;</span>
                <div class="import-difference-plain-value import-difference-plain-value--imported">
                    <span>Archivo importado</span>
                    <strong>${escapeHtml(field.proposed)}</strong>
                </div>
            </div>`).join('');

        const disabled = isPreview ? 'disabled title="Las decisiones están deshabilitadas en la vista previa"' : '';
        return `
            <article class="record-card import-difference-card is-pending" data-difference-id="${item.id}">
                <header class="import-difference-compact-header">
                    <span class="import-difference-status-icon" aria-hidden="true">
                        <i class="far fa-user"></i>
                    </span>
                    <div class="import-difference-compact-person">
                        <div>
                            <h2 title="${escapeHtml(item.person_name)}">${escapeHtml(item.person_name)}</h2>
                            <p>Folio ${escapeHtml(item.gov_folio || 'Sin folio')}</p>
                        </div>
                    </div>
                    <span class="import-difference-field-count">${fieldSummary}</span>
                </header>
                <section class="import-difference-comparison-section" aria-label="Campos con diferencias">
                    <div class="import-difference-card-column-head" aria-hidden="true">
                        <span class="import-difference-column-title"><i class="far fa-rectangle-list"></i> Campo</span>
                        <span>Registro actual</span>
                        <span></span>
                        <span>Archivo importado</span>
                    </div>
                    <div class="import-difference-comparison-body">${fields}</div>
                </section>
                <footer class="import-difference-compact-actions">
                    <span aria-hidden="true"></span>
                        <button type="button" class="ui-button ui-button--secondary" data-difference-decision="keep" ${disabled}>
                            Conservar actual
                        </button>
                    <span aria-hidden="true"></span>
                        <button type="button" class="ui-button ui-button--secondary" data-difference-decision="apply" ${disabled}>
                            Usar archivo
                        </button>
                </footer>
            </article>`;
    }

    function renderResolvedCard(item) {
        const meta = [item.resolved_by ? `Por ${item.resolved_by}` : '', resolvedDate(item.resolved_at)].filter(Boolean).join(' · ');
        return `
            <article class="record-card import-difference-card import-difference-card--resolved" data-difference-id="${item.id}">
                <div class="failed-record-error import-difference-alert is-corrected">
                    <span class="failed-record-error-icon"><i class="fas fa-circle-check" aria-hidden="true"></i></span>
                    <div>
                        <p class="failed-record-error-title">${escapeHtml(statusLabel(item.status))}</p>
                        ${meta ? `<ul class="error-messages"><li>${escapeHtml(meta)}</li></ul>` : ''}
                    </div>
                </div>
                <section class="failed-correction-section import-difference-identity-section">
                    <div class="failed-correction-section-heading">
                        <i class="far fa-user" aria-hidden="true"></i>
                        <h2>Información de la persona</h2>
                    </div>
                    <div class="import-difference-identity-grid">
                        <div class="import-difference-readonly-field">
                            <span>Nombre</span>
                            <strong title="${escapeHtml(item.person_name)}">${escapeHtml(item.person_name)}</strong>
                        </div>
                        <div class="import-difference-readonly-field">
                            <span>Folio</span>
                            <strong>${escapeHtml(item.gov_folio || 'Sin folio')}</strong>
                        </div>
                    </div>
                </section>
            </article>`;
    }

    function renderCard(item) {
        return item.status === 'pending' ? renderPendingCard(item) : renderResolvedCard(item);
    }

    function renderEmpty(total) {
        const legacy = total === 0 && expectedDifferenceCount > 0;
        list.innerHTML = `<div class="users-table-state users-table-state--empty import-differences-empty">
            <i class="fas ${legacy ? 'fa-clock-rotate-left' : 'fa-check'}" aria-hidden="true"></i>
            <strong>${legacy ? 'No hay detalle disponible' : (currentStatus === 'pending' ? 'No quedan diferencias pendientes' : 'Todavía no hay diferencias resueltas')}</strong>
            <span>${legacy ? 'Esta carga se procesó antes de habilitar la comparación.' : (currentStatus === 'pending' ? 'Todas las diferencias ya fueron atendidas.' : 'Las decisiones tomadas aparecerán aquí.')}</span>
        </div>`;
    }

    function renderPagination(meta) {
        pagination.innerHTML = '';
        if (!meta || meta.last_page < 1) return;

        const navigation = document.createElement('div');
        navigation.className = 'fb-pagination';
        navigation.setAttribute('role', 'navigation');
        navigation.setAttribute('aria-label', 'Paginación de diferencias');

        const previous = document.createElement('button');
        previous.type = 'button'; previous.className = 'fb-page-btn fb-page-first'; previous.textContent = 'Anterior'; previous.disabled = meta.current_page <= 1;
        previous.classList.toggle('fb-page-disabled', previous.disabled);
        previous.addEventListener('click', () => { currentPage--; shouldScrollToReview = true; load(); });

        const current = document.createElement('span');
        current.className = 'fb-page-btn fb-page-num fb-page-active';
        current.setAttribute('aria-current', 'page');
        current.textContent = meta.current_page;

        const next = document.createElement('button');
        next.type = 'button'; next.className = 'fb-page-btn fb-page-last'; next.textContent = 'Siguiente'; next.disabled = meta.current_page >= meta.last_page;
        next.classList.toggle('fb-page-disabled', next.disabled);
        next.addEventListener('click', () => { currentPage++; shouldScrollToReview = true; load(); });

        navigation.append(previous, current, next);
        pagination.append(navigation);
    }

    async function load() {
        activeRequest?.abort();
        activeRequest = new AbortController();
        loading.classList.remove('hidden');
        list.classList.add('hidden');
        const params = new URLSearchParams({
            page: currentPage,
            per_page: perPageSelect?.value || 4,
            status: currentStatus,
            search: searchInput?.value.trim() || '',
        });

        try {
            const response = await fetch(`/api/estadisticas/importaciones/${importId}/diferencias?${params}`, { signal: activeRequest.signal });
            const payload = await response.json();
            if (!response.ok || !payload.ok) throw new Error(payload.message || 'No se pudieron cargar las diferencias.');
            const page = payload.data;
            if (page.total > 0 && currentPage > page.last_page) {
                currentPage = page.last_page;
                setTimeout(load, 0);
                return;
            }
            const records = page.data || [];
            list.innerHTML = records.map(renderCard).join('');
            if (!records.length) renderEmpty(payload.counts.total);
            document.getElementById('difference-count-pending').textContent = payload.counts.pending;
            document.getElementById('difference-count-resolved').textContent = payload.counts.resolved;
            const first = page.total ? page.from : 0;
            const last = page.total ? page.to : 0;
            info.innerHTML = page.total
                ? `Mostrando <span class="font-semibold text-gray-900">${first}-${last}</span> de <span class="font-semibold text-gray-900">${page.total}</span>`
                : 'Sin resultados';
            renderPagination(page);
            announce(`${records.length} diferencias mostradas.`);
            if (shouldScrollToReview) {
                shouldScrollToReview = false;
                requestAnimationFrame(() => document.querySelector('.import-differences-tabs')?.scrollIntoView({ behavior: 'smooth', block: 'start' }));
            }
        } catch (error) {
            if (error.name === 'AbortError') return;
            list.innerHTML = `<div class="users-table-state import-differences-load-error"><i class="fas fa-triangle-exclamation" aria-hidden="true"></i><strong>No se pudieron cargar las diferencias</strong><span>${escapeHtml(error.message)}</span><button type="button" class="ui-button ui-button--secondary" data-retry-differences>Reintentar</button></div>`;
        } finally {
            loading.classList.add('hidden');
            list.classList.remove('hidden');
        }
    }

    async function undoDifference(differenceId) {
        try {
            const response = await fetch(`/api/estadisticas/diferencias/${differenceId}/deshacer`, {
                method: 'POST',
                headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken },
            });
            const payload = await response.json();
            if (!response.ok || !payload.ok) throw new Error(payload.message || 'No se pudo deshacer la decisión.');
            currentStatus = 'pending';
            currentPage = 1;
            document.querySelectorAll('[data-difference-status]').forEach(tab => {
                const active = tab.dataset.differenceStatus === 'pending';
                tab.classList.toggle('is-active', active);
                tab.setAttribute('aria-pressed', active ? 'true' : 'false');
            });
            await load();
            toast(payload.message);
            announce(payload.message);
        } catch (error) {
            toast(error.message, 'error');
        }
    }

    async function resolveDifference(card, decision) {
        const buttons = card.querySelectorAll('button');
        buttons.forEach(button => button.disabled = true);
        try {
            const response = await fetch(`/api/estadisticas/diferencias/${card.dataset.differenceId}/resolver`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                body: JSON.stringify({ decision }),
            });
            const payload = await response.json();
            if (!response.ok || !payload.ok) throw new Error(payload.message || 'No se pudo guardar la decisión.');
            const differenceId = card.dataset.differenceId;
            announce(payload.message);
            await load();
            toast(payload.message, 'success', 7000, {
                action: {
                    label: 'Deshacer',
                    onClick: () => undoDifference(differenceId),
                },
            });
        } catch (error) {
            buttons.forEach(button => button.disabled = false);
            toast(error.message, 'error');
        }
    }

    list.addEventListener('click', event => {
        const retry = event.target.closest('[data-retry-differences]');
        if (retry) return load();

        const decisionButton = event.target.closest('[data-difference-decision]');
        if (!decisionButton) return;
        resolveDifference(decisionButton.closest('[data-difference-id]'), decisionButton.dataset.differenceDecision);
    });

    document.querySelectorAll('[data-difference-status]').forEach(button => button.addEventListener('click', () => {
        currentStatus = button.dataset.differenceStatus;
        currentPage = 1;
        shouldScrollToReview = true;
        document.querySelectorAll('[data-difference-status]').forEach(tab => {
            const active = tab === button;
            tab.classList.toggle('is-active', active);
            tab.setAttribute('aria-pressed', active ? 'true' : 'false');
        });
        load();
    }));

    searchInput?.addEventListener('input', () => {
        clearTimeout(searchTimer);
        currentPage = 1;
        searchTimer = setTimeout(load, 280);
    });
    clearSearch?.addEventListener('click', () => { searchInput.value = ''; currentPage = 1; load(); });
    perPageSelect?.addEventListener('change', () => { currentPage = 1; load(); });
    load();
})();
</script>
@endpush
