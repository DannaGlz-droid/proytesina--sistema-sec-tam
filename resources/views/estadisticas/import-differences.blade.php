@extends('layouts.principal')
@section('title', 'Revisión de diferencias')
@section('content')
    @include('components.header-admin')
    @include('components.nav-estadisticas')

    @php
        $isCompactReview = request()->query('vista') === 'compacta';
    @endphp

    <main class="users-management-page statistics-failed-imports-page statistics-import-differences-page {{ $isCompactReview ? 'is-compact-review' : '' }} px-4 sm:px-6 lg:px-10 pt-5 lg:pt-7 pb-8 lg:pb-10">
        <div id="differences-announcer" class="sr-only" role="status" aria-live="polite" aria-atomic="true"></div>

        <x-ui.page-header
            class="users-management-header statistics-import-differences-header"
            title="Diferencias de importación"
            description="Compare el registro actual con el archivo importado y elija cuál conservar."
        >
            <x-slot:actions>
                <a
                    href="{{ route('statistic.import-differences-view', ['importId' => $importId] + ($isCompactReview ? [] : ['vista' => 'compacta'])) }}"
                    class="users-page-create-btn import-differences-view-preview"
                >
                    <i class="fas {{ $isCompactReview ? 'fa-table-cells-large' : 'fa-table-list' }}" aria-hidden="true"></i>
                    {{ $isCompactReview ? 'Ver diseño actual' : 'Probar diseño optimizado' }}
                </a>
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
    const isCompactReview = @json($isCompactReview);
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
    let undoCountdownTimer = null;
    let visibleRecords = [];
    let selectedDifferenceId = null;
    let pendingDifferenceCount = 0;
    let bulkDefaultDecision = null;
    let bulkDecisionOverrides = new Map();
    let bulkSaving = false;

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

    const optimizedStatusLabel = status => ({
        applied: 'Archivo importado',
        kept: 'Registro actual',
        mixed: 'Valores combinados',
    }[status] || 'Resuelta');

    const preparedDecisionFor = differenceId => {
        if (!bulkDefaultDecision) return null;
        return bulkDecisionOverrides.get(String(differenceId)) || bulkDefaultDecision;
    };

    const bulkDecisionCounts = () => {
        let applied = bulkDefaultDecision === 'apply' ? pendingDifferenceCount : 0;
        let kept = bulkDefaultDecision === 'keep' ? pendingDifferenceCount : 0;

        bulkDecisionOverrides.forEach(decision => {
            if (decision === bulkDefaultDecision) return;
            if (bulkDefaultDecision === 'apply') {
                applied--;
                kept++;
            } else {
                kept--;
                applied++;
            }
        });

        return { applied: Math.max(0, applied), kept: Math.max(0, kept) };
    };

    const resolvedDate = value => {
        if (!value) return '';
        const date = new Date(value);
        const isCurrentYear = date.getFullYear() === new Date().getFullYear();
        const day = new Intl.DateTimeFormat('es-MX', {
            day: 'numeric',
            month: 'short',
            ...(isCurrentYear ? {} : { year: 'numeric' }),
        }).format(date);
        const time = new Intl.DateTimeFormat('es-MX', {
            hour: 'numeric',
            minute: '2-digit',
        }).format(date);

        return `${day}, ${time}`;
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

    function renderOptimizedSinglePendingCard(item) {
        const field = (item.changed_fields || [])[0];

        if (!field) return renderPendingCard(item);

        const disabled = isPreview ? 'disabled title="Las decisiones est&aacute;n deshabilitadas en la vista previa"' : '';

        return `
            <article class="record-card import-difference-card import-difference-hybrid-card is-pending" data-difference-id="${item.id}">
                <header class="import-difference-hybrid-identity">
                    <span class="import-difference-status-icon" aria-hidden="true">
                        <i class="far fa-user"></i>
                    </span>
                    <div class="import-difference-compact-person">
                        <div>
                            <h2 title="${escapeHtml(item.person_name)}">${escapeHtml(item.person_name)}</h2>
                            <p>Folio ${escapeHtml(item.gov_folio || 'Sin folio')}</p>
                        </div>
                    </div>
                    <span class="import-difference-field-count">1 campo distinto</span>
                </header>

                <section class="import-difference-hybrid-comparison" aria-label="Comparaci&oacute;n de ${escapeHtml(field.label)}">
                    <div class="import-difference-hybrid-field">
                        <span>Campo</span>
                        <strong>${escapeHtml(field.label)}</strong>
                    </div>
                    <div class="import-difference-hybrid-value import-difference-hybrid-value--current">
                        <span>Registro actual</span>
                        <strong>${escapeHtml(field.previous)}</strong>
                    </div>
                    <span class="import-difference-row-arrow" aria-hidden="true">&rarr;</span>
                    <div class="import-difference-hybrid-value import-difference-hybrid-value--imported">
                        <span>Archivo importado</span>
                        <strong>${escapeHtml(field.proposed)}</strong>
                    </div>

                    <span class="import-difference-hybrid-action-spacer" aria-hidden="true"></span>
                    <button type="button" class="ui-button ui-button--secondary" data-difference-decision="keep" ${disabled}>
                        Conservar actual
                    </button>
                    <span class="import-difference-hybrid-action-divider" aria-hidden="true"></span>
                    <button type="button" class="ui-button ui-button--secondary" data-difference-decision="apply" ${disabled}>
                        Usar archivo
                    </button>
                </section>
            </article>`;
    }

    function renderResolvedCard(item) {
        const fieldCount = item.field_count ?? item.changed_fields.length;
        const fieldSummary = `${fieldCount} ${fieldCount === 1 ? 'campo distinto' : 'campos distintos'}`;
        const meta = [item.resolved_by || '', resolvedDate(item.resolved_at)].filter(Boolean).join(' · ');
        const canUndo = item.can_undo && item.undo_expires_at && new Date(item.undo_expires_at).getTime() > Date.now();
        const fields = (item.changed_fields || []).map(field => {
            const decision = field.decision || (item.status === 'applied' ? 'apply' : (item.status === 'kept' ? 'keep' : ''));
            const currentSelected = decision === 'keep';
            const importedSelected = decision === 'apply';
            const selectedIcon = '<i class="fas fa-circle-check import-difference-selected-indicator" aria-hidden="true"></i>';

            return `
                <div class="import-difference-field-row">
                    <div class="import-difference-field-label">${escapeHtml(field.label)}</div>
                    <div class="import-difference-plain-value import-difference-plain-value--current ${currentSelected ? 'is-selected' : ''}">
                        <span>Registro actual</span>
                        <div class="import-difference-value-content">
                            ${currentSelected ? '<span class="sr-only">Valor elegido. </span>' : ''}
                            <strong>${escapeHtml(field.previous)}</strong>
                            ${currentSelected ? selectedIcon : ''}
                        </div>
                    </div>
                    <span class="import-difference-row-arrow" aria-hidden="true">&rarr;</span>
                    <div class="import-difference-plain-value import-difference-plain-value--imported ${importedSelected ? 'is-selected' : ''}">
                        <span>Archivo importado</span>
                        <div class="import-difference-value-content">
                            ${importedSelected ? '<span class="sr-only">Valor elegido. </span>' : ''}
                            <strong>${escapeHtml(field.proposed)}</strong>
                            ${importedSelected ? selectedIcon : ''}
                        </div>
                    </div>
                </div>`;
        }).join('');

        return `
            <article class="record-card import-difference-card import-difference-card--resolved" data-difference-id="${item.id}">
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
                    ${isCompactReview ? `
                        <button type="button" class="import-difference-optimized-toggle" data-toggle-resolved aria-expanded="false">
                            <span>Ver cambios</span>
                            <i class="fas fa-chevron-down" aria-hidden="true"></i>
                        </button>` : ''}
                </header>
                <section class="import-difference-comparison-section ${isCompactReview ? 'hidden' : ''}" aria-label="Comparación resuelta">
                    <div class="import-difference-card-column-head" aria-hidden="true">
                        <span class="import-difference-column-title"><i class="far fa-rectangle-list"></i> Campo</span>
                        <span>Registro actual</span>
                        <span></span>
                        <span>Archivo importado</span>
                    </div>
                    <div class="import-difference-comparison-body">${fields}</div>
                </section>
                <footer class="import-difference-resolution-footer">
                    <div class="import-difference-resolution-summary">
                        <span class="import-difference-resolution-status">
                            <i class="fas fa-circle-check" aria-hidden="true"></i>
                            ${escapeHtml(statusLabel(item.status))}
                        </span>
                        ${meta ? `<span class="import-difference-resolution-meta">${escapeHtml(meta)}</span>` : ''}
                    </div>
                    ${canUndo ? `
                        <button
                            type="button"
                            class="ui-button ui-button--secondary import-difference-undo-button"
                            data-undo-difference="${item.id}"
                            data-undo-expires-at="${escapeHtml(item.undo_expires_at)}"
                        >
                            <i class="fas fa-rotate-left" aria-hidden="true"></i>
                            <span data-undo-label>Deshacer</span>
                        </button>` : ''}
                </footer>
            </article>`;
    }

    function renderCompactPending(item) {
        const fieldCount = item.field_count ?? item.changed_fields.length;
        const fieldSummary = `${fieldCount} ${fieldCount === 1 ? 'campo distinto' : 'campos distintos'}`;
        const fields = (item.changed_fields || []).map(field => `
            <div class="import-difference-compact-field-row">
                <div class="import-difference-compact-field-label">${escapeHtml(field.label)}</div>
                <div class="import-difference-compact-value import-difference-compact-value--current">
                    <span>Registro actual</span>
                    <strong>${escapeHtml(field.previous)}</strong>
                </div>
                <span class="import-difference-row-arrow" aria-hidden="true">&rarr;</span>
                <div class="import-difference-compact-value import-difference-compact-value--imported">
                    <span>Archivo importado</span>
                    <strong>${escapeHtml(field.proposed)}</strong>
                </div>
            </div>`).join('');
        const disabled = isPreview ? 'disabled title="Las decisiones están deshabilitadas en la vista previa"' : '';

        return `
            <article class="import-difference-compact-record" data-difference-id="${item.id}">
                <div class="import-difference-compact-identity">
                    <h2 title="${escapeHtml(item.person_name)}">${escapeHtml(item.person_name)}</h2>
                    <p>Folio ${escapeHtml(item.gov_folio || 'Sin folio')}</p>
                    <span>${fieldSummary}</span>
                </div>
                <div class="import-difference-compact-fields">${fields}</div>
                <div class="import-difference-compact-decision" aria-label="Decisión para ${escapeHtml(item.person_name)}">
                    ${fieldCount > 1 ? '<span>Decisión para todo el registro</span>' : ''}
                    <div>
                        <button type="button" class="ui-button ui-button--secondary" data-difference-decision="keep" ${disabled}>Conservar actual</button>
                        <button type="button" class="ui-button ui-button--secondary" data-difference-decision="apply" ${disabled}>Usar archivo</button>
                    </div>
                </div>
            </article>`;
    }

    function renderCompactResolved(item) {
        const fieldCount = item.field_count ?? item.changed_fields.length;
        const fieldSummary = `${fieldCount} ${fieldCount === 1 ? 'campo distinto' : 'campos distintos'}`;
        const meta = [item.resolved_by || '', resolvedDate(item.resolved_at)].filter(Boolean).join(' · ');
        const canUndo = item.can_undo && item.undo_expires_at && new Date(item.undo_expires_at).getTime() > Date.now();
        const fields = (item.changed_fields || []).map(field => {
            const decision = field.decision || (item.status === 'applied' ? 'apply' : (item.status === 'kept' ? 'keep' : ''));
            const currentSelected = decision === 'keep';
            const importedSelected = decision === 'apply';
            const selectedIcon = '<i class="fas fa-circle-check import-difference-selected-indicator" aria-hidden="true"></i>';

            return `
                <div class="import-difference-compact-field-row">
                    <div class="import-difference-compact-field-label">${escapeHtml(field.label)}</div>
                    <div class="import-difference-compact-value import-difference-compact-value--current ${currentSelected ? 'is-selected' : ''}">
                        <span>Registro actual</span>
                        <div class="import-difference-value-content"><strong>${escapeHtml(field.previous)}</strong>${currentSelected ? selectedIcon : ''}</div>
                    </div>
                    <span class="import-difference-row-arrow" aria-hidden="true">&rarr;</span>
                    <div class="import-difference-compact-value import-difference-compact-value--imported ${importedSelected ? 'is-selected' : ''}">
                        <span>Archivo importado</span>
                        <div class="import-difference-value-content"><strong>${escapeHtml(field.proposed)}</strong>${importedSelected ? selectedIcon : ''}</div>
                    </div>
                </div>`;
        }).join('');

        return `
            <article class="import-difference-compact-resolved" data-difference-id="${item.id}">
                <div class="import-difference-compact-resolved-summary">
                    <button type="button" class="import-difference-resolved-toggle" data-toggle-resolved aria-expanded="false">
                        <span class="import-difference-compact-resolved-person">
                            <strong>${escapeHtml(item.person_name)}</strong>
                            <small>Folio ${escapeHtml(item.gov_folio || 'Sin folio')} · ${fieldSummary}</small>
                        </span>
                        <span class="import-difference-resolution-status"><i class="fas fa-circle-check" aria-hidden="true"></i>${escapeHtml(statusLabel(item.status))}</span>
                        ${meta ? `<span class="import-difference-resolution-meta">${escapeHtml(meta)}</span>` : '<span></span>'}
                        <i class="fas fa-chevron-down import-difference-resolved-chevron" aria-hidden="true"></i>
                    </button>
                    ${canUndo ? `
                        <button type="button" class="ui-button ui-button--secondary import-difference-undo-button" data-undo-difference="${item.id}" data-undo-expires-at="${escapeHtml(item.undo_expires_at)}">
                            <i class="fas fa-rotate-left" aria-hidden="true"></i><span data-undo-label>Deshacer</span>
                        </button>` : ''}
                </div>
                <section class="import-difference-compact-resolved-detail hidden" aria-label="Comparación resuelta">
                    <div class="import-difference-compact-detail-head" aria-hidden="true">
                        <span>Campo</span>
                        <span>Registro actual</span>
                        <span></span>
                        <span>Archivo importado</span>
                    </div>
                    ${fields}
                </section>
            </article>`;
    }

    function compactListHeader() {
        return `<div class="import-difference-compact-list-head" aria-hidden="true">
            <span>Registro</span>
            <div>
                <span>Campo</span>
                <span>Registro actual</span>
                <span></span>
                <span>Archivo importado</span>
            </div>
            <span>Decisión</span>
        </div>`;
    }

    function compactResolvedListHeader() {
        return `<div class="import-difference-compact-list-head import-difference-compact-list-head--resolved" aria-hidden="true">
            <span>Registro</span>
            <span>Resultado</span>
            <span>Resuelta por</span>
        </div>`;
    }

    function updateUndoCountdowns() {
        const buttons = [...list.querySelectorAll('[data-undo-difference]')];
        let hasActiveCountdown = false;

        buttons.forEach(button => {
            const expiresAt = new Date(button.dataset.undoExpiresAt).getTime();
            const remainingSeconds = Math.max(0, Math.ceil((expiresAt - Date.now()) / 1000));
            if (!Number.isFinite(expiresAt) || remainingSeconds <= 0) {
                button.remove();
                return;
            }

            hasActiveCountdown = true;
            const minutes = Math.floor(remainingSeconds / 60);
            const seconds = String(remainingSeconds % 60).padStart(2, '0');
            const label = button.querySelector('[data-undo-label]');
            if (label) label.textContent = `Deshacer (${minutes}:${seconds})`;
            button.setAttribute('aria-label', `Deshacer decisión. Tiempo restante: ${minutes} minutos y ${seconds} segundos.`);
        });

        if (!hasActiveCountdown && undoCountdownTimer) {
            clearInterval(undoCountdownTimer);
            undoCountdownTimer = null;
        }
    }

    function startUndoCountdowns() {
        if (undoCountdownTimer) clearInterval(undoCountdownTimer);
        undoCountdownTimer = null;
        updateUndoCountdowns();
        if (list.querySelector('[data-undo-difference]')) {
            undoCountdownTimer = setInterval(updateUndoCountdowns, 1000);
        }
    }

    function renderCard(item) {
        const fieldCount = item.field_count ?? item.changed_fields?.length ?? 0;

        if (isCompactReview && item.status === 'pending' && fieldCount === 1) {
            return renderOptimizedSinglePendingCard(item);
        }

        return item.status === 'pending' ? renderPendingCard(item) : renderResolvedCard(item);
    }

    function renderOptimizedAccordionDetail(item) {
        const isResolved = item.status !== 'pending';
        const preparedDecision = isResolved ? null : preparedDecisionFor(item.id);
        const interactionDisabled = isPreview || bulkSaving;
        const disabledTitle = isPreview
            ? 'Las decisiones est&aacute;n deshabilitadas en la vista previa'
            : 'Se est&aacute;n confirmando las decisiones';
        const disabled = interactionDisabled ? `disabled title="${disabledTitle}"` : '';
        const meta = [item.resolved_by || '', resolvedDate(item.resolved_at)].filter(Boolean).join(' · ');
        const canUndo = isResolved && item.can_undo && item.undo_expires_at && new Date(item.undo_expires_at).getTime() > Date.now();
        const fields = (item.changed_fields || []).map(field => {
            const decision = field.decision || (item.status === 'applied' ? 'apply' : (item.status === 'kept' ? 'keep' : ''));
            const currentSelected = isResolved && decision === 'keep';
            const importedSelected = isResolved && decision === 'apply';
            const currentPrepared = preparedDecision === 'keep';
            const importedPrepared = preparedDecision === 'apply';
            const selectedIcon = '<i class="fas fa-circle-check import-difference-selected-indicator" aria-hidden="true"></i>';
            const preparedIcon = '<i class="far fa-circle-check import-difference-prepared-indicator" aria-hidden="true"></i>';

            return `
                <div class="import-difference-field-row">
                    <div class="import-difference-field-label">${escapeHtml(field.label)}</div>
                    <div class="import-difference-plain-value import-difference-plain-value--current ${currentSelected ? 'is-selected' : ''} ${currentPrepared ? 'is-prepared' : ''}">
                        <span>Registro actual</span>
                        <div class="import-difference-value-content">
                            <strong>${escapeHtml(field.previous)}</strong>
                            ${currentSelected ? selectedIcon : (currentPrepared ? preparedIcon : '')}
                        </div>
                    </div>
                    <span class="import-difference-row-arrow" aria-hidden="true">&rarr;</span>
                    <div class="import-difference-plain-value import-difference-plain-value--imported ${importedSelected ? 'is-selected' : ''} ${importedPrepared ? 'is-prepared' : ''}">
                        <span>Archivo importado</span>
                        <div class="import-difference-value-content">
                            <strong>${escapeHtml(field.proposed)}</strong>
                            ${importedSelected ? selectedIcon : (importedPrepared ? preparedIcon : '')}
                        </div>
                    </div>
                </div>`;
        }).join('');

        return `
            <section class="import-difference-comparison-section" aria-label="Campos con diferencias">
                <div class="import-difference-card-column-head" aria-hidden="true">
                    <span class="import-difference-column-title"><i class="far fa-rectangle-list"></i> Campo</span>
                    <span>Registro actual</span>
                    <span></span>
                    <span>Archivo importado</span>
                </div>
                <div class="import-difference-comparison-body">${fields}</div>
            </section>
            ${isResolved ? `
                <footer class="import-difference-resolution-footer">
                    <div class="import-difference-resolution-summary">
                        <span class="import-difference-resolution-status">
                            <i class="fas fa-circle-check" aria-hidden="true"></i>
                            ${escapeHtml(optimizedStatusLabel(item.status))}
                        </span>
                        ${meta ? `<span class="import-difference-resolution-meta">${escapeHtml(meta)}</span>` : ''}
                    </div>
                    ${canUndo ? `
                        <button type="button" class="ui-button ui-button--secondary import-difference-undo-button" data-undo-difference="${item.id}" data-undo-expires-at="${escapeHtml(item.undo_expires_at)}">
                            <i class="fas fa-rotate-left" aria-hidden="true"></i>
                            <span data-undo-label>Deshacer</span>
                        </button>` : ''}
                </footer>` : `
                <footer class="import-difference-compact-actions">
                    <span aria-hidden="true"></span>
                    <button type="button" class="ui-button ui-button--secondary ${preparedDecision === 'keep' ? 'is-prepared' : ''}" data-difference-decision="keep" aria-pressed="${preparedDecision === 'keep' ? 'true' : 'false'}" ${disabled}>Conservar actual</button>
                    <span aria-hidden="true"></span>
                    <button type="button" class="ui-button ui-button--secondary ${preparedDecision === 'apply' ? 'is-prepared' : ''}" data-difference-decision="apply" aria-pressed="${preparedDecision === 'apply' ? 'true' : 'false'}" ${disabled}>Usar archivo</button>
                </footer>`}`;
    }

    function renderBulkControls() {
        if (!isCompactReview || currentStatus !== 'pending' || pendingDifferenceCount < 1) return '';

        if (!bulkDefaultDecision) {
            return `
                <section class="import-difference-bulk-bar" aria-label="Decisi&oacute;n r&aacute;pida">
                    <div class="import-difference-bulk-copy">
                        <strong>Decisi&oacute;n r&aacute;pida</strong>
                        <span>Prepare los ${pendingDifferenceCount} pendientes con el archivo y cambie solo las excepciones.</span>
                    </div>
                    <button type="button" class="ui-button ui-button--secondary" data-start-bulk-decisions ${isPreview ? 'disabled title="Las decisiones est&aacute;n deshabilitadas en la vista previa"' : ''}>
                        <i class="far fa-circle-check" aria-hidden="true"></i>
                        Preparar ${pendingDifferenceCount} con archivo
                    </button>
                </section>`;
        }

        const counts = bulkDecisionCounts();
        return `
            <section class="import-difference-bulk-bar is-preparing" aria-label="Decisiones preparadas" role="status">
                <div class="import-difference-bulk-copy">
                    <strong>${pendingDifferenceCount} decisiones preparadas</strong>
                    <span>Archivo: ${counts.applied} &middot; Actual: ${counts.kept}. Revise las excepciones antes de confirmar.</span>
                </div>
                <div class="import-difference-bulk-actions">
                    <button type="button" class="ui-button ui-button--secondary" data-cancel-bulk-decisions ${bulkSaving ? 'disabled' : ''}>Cancelar</button>
                    <button type="button" class="ui-button ui-button--primary" data-confirm-bulk-decisions ${bulkSaving ? 'disabled' : ''}>
                        ${bulkSaving ? '<i class="fas fa-spinner fa-spin" aria-hidden="true"></i> Guardando&hellip;' : `Confirmar ${pendingDifferenceCount}`}
                    </button>
                </div>
            </section>`;
    }

    function renderOptimizedAccordion(records) {
        const allCollapsed = selectedDifferenceId === '';
        const selected = allCollapsed
            ? null
            : (records.find(item => String(item.id) === String(selectedDifferenceId)) || records[0]);
        if (selected) selectedDifferenceId = selected.id;

        const items = records.map(item => {
            const fieldCount = item.field_count ?? item.changed_fields?.length ?? 0;
            const fieldSummary = `${fieldCount} ${fieldCount === 1 ? 'campo distinto' : 'campos distintos'}`;
            const preparedDecision = item.status === 'pending' ? preparedDecisionFor(item.id) : null;
            const active = selected !== null && String(item.id) === String(selected.id);
            const detailId = `optimized-difference-detail-${item.id}`;

            return `
                <article class="import-difference-accordion-record ${active ? 'is-active' : ''} ${item.status !== 'pending' ? 'import-difference-card--resolved' : 'is-pending'}" data-difference-id="${item.id}">
                    <button
                        type="button"
                        class="import-difference-accordion-trigger"
                        data-select-difference="${item.id}"
                        aria-expanded="${active ? 'true' : 'false'}"
                        aria-controls="${detailId}"
                    >
                        <span class="import-difference-status-icon" aria-hidden="true">
                            <i class="far fa-user"></i>
                        </span>
                        <span class="import-difference-accordion-person">
                            <strong>${escapeHtml(item.person_name)}</strong>
                            <small>Folio ${escapeHtml(item.gov_folio || 'Sin folio')}</small>
                        </span>
                        <span class="import-difference-accordion-summary">
                            <span>${fieldSummary}</span>
                            ${item.status !== 'pending' ? `<small><i class="fas fa-circle-check" aria-hidden="true"></i>${escapeHtml(optimizedStatusLabel(item.status))}</small>` : ''}
                            ${preparedDecision ? `<small class="is-prepared"><i class="far fa-circle-check" aria-hidden="true"></i>${preparedDecision === 'apply' ? 'Archivo' : 'Actual'}</small>` : ''}
                        </span>
                        <i class="fas fa-chevron-down import-difference-accordion-chevron" aria-hidden="true"></i>
                    </button>
                    <div
                        id="${detailId}"
                        class="import-difference-accordion-detail"
                        aria-hidden="${active ? 'false' : 'true'}"
                        ${active ? '' : 'inert'}
                    >
                        <div class="import-difference-accordion-detail-inner">${renderOptimizedAccordionDetail(item)}</div>
                    </div>
                </article>`;
        }).join('');

        const bulkControls = renderBulkControls();
        const pendingPreparation = currentStatus === 'pending' && bulkDefaultDecision;

        return `${pendingPreparation ? '' : bulkControls}<div class="import-difference-accordion" aria-label="${currentStatus === 'pending' ? 'Registros pendientes' : 'Registros resueltos'}">${items}</div>${pendingPreparation ? bulkControls : ''}`;
    }

    function rerenderOptimizedList(focusSelector = null) {
        if (!isCompactReview) return;
        list.innerHTML = renderOptimizedAccordion(visibleRecords);
        startUndoCountdowns();
        if (focusSelector) {
            requestAnimationFrame(() => list.querySelector(focusSelector)?.focus({ preventScroll: true }));
        }
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
            visibleRecords = records;
            pendingDifferenceCount = Number(payload.counts.pending || 0);
            if (pendingDifferenceCount === 0) {
                bulkDefaultDecision = null;
                bulkDecisionOverrides.clear();
            }
            list.innerHTML = isCompactReview ? renderOptimizedAccordion(records) : records.map(renderCard).join('');
            startUndoCountdowns();
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

    async function undoDifference(differenceId, trigger = null) {
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
            if (trigger) trigger.disabled = false;
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

    async function confirmBulkDecisions() {
        if (!bulkDefaultDecision || bulkSaving) return;

        bulkSaving = true;
        rerenderOptimizedList('[data-confirm-bulk-decisions]');
        const exceptions = Array.from(bulkDecisionOverrides.entries())
            .filter(([, decision]) => decision !== bulkDefaultDecision)
            .map(([id, decision]) => ({ id: Number(id), decision }));

        try {
            const response = await fetch(`/api/estadisticas/importaciones/${importId}/diferencias/resolver-lote`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                body: JSON.stringify({ default_decision: bulkDefaultDecision, exceptions }),
            });
            const payload = await response.json();
            if (!response.ok || !payload.ok) throw new Error(payload.message || 'No se pudieron guardar las decisiones.');

            bulkDefaultDecision = null;
            bulkDecisionOverrides.clear();
            bulkSaving = false;
            selectedDifferenceId = null;
            currentPage = 1;
            await load();
            toast(payload.message);
            announce(payload.message);
        } catch (error) {
            bulkSaving = false;
            rerenderOptimizedList('[data-confirm-bulk-decisions]');
            toast(error.message, 'error');
            announce(`No se guardaron las decisiones. ${error.message}`);
        }
    }

    list.addEventListener('click', event => {
        const retry = event.target.closest('[data-retry-differences]');
        if (retry) return load();

        const startBulk = event.target.closest('[data-start-bulk-decisions]');
        if (startBulk) {
            bulkDefaultDecision = 'apply';
            bulkDecisionOverrides.clear();
            rerenderOptimizedList('[data-confirm-bulk-decisions]');
            announce(`Se prepararon ${pendingDifferenceCount} decisiones con el archivo importado. Puede cambiar las excepciones antes de confirmar.`);
            return;
        }

        const cancelBulk = event.target.closest('[data-cancel-bulk-decisions]');
        if (cancelBulk) {
            bulkDefaultDecision = null;
            bulkDecisionOverrides.clear();
            rerenderOptimizedList('[data-start-bulk-decisions]');
            announce('Se cancelaron las decisiones preparadas. No se guardaron cambios.');
            return;
        }

        const confirmBulk = event.target.closest('[data-confirm-bulk-decisions]');
        if (confirmBulk) return confirmBulkDecisions();

        const recordSelector = event.target.closest('[data-select-difference]');
        if (recordSelector) {
            const selectedId = recordSelector.dataset.selectDifference;
            const collapseSelected = recordSelector.getAttribute('aria-expanded') === 'true';
            selectedDifferenceId = collapseSelected ? '' : selectedId;
            list.querySelectorAll('.import-difference-accordion-record').forEach(record => {
                const active = !collapseSelected && String(record.dataset.differenceId) === String(selectedId);
                const trigger = record.querySelector('[data-select-difference]');
                const detail = record.querySelector('.import-difference-accordion-detail');

                record.classList.toggle('is-active', active);
                trigger?.setAttribute('aria-expanded', active ? 'true' : 'false');
                detail?.setAttribute('aria-hidden', active ? 'false' : 'true');
                detail?.toggleAttribute('inert', !active);
            });
            recordSelector.focus({ preventScroll: true });
            announce(collapseSelected ? 'Detalles cerrados.' : 'Detalles del registro abiertos.');
            return;
        }

        const resolvedToggle = event.target.closest('[data-toggle-resolved]');
        if (resolvedToggle) {
            const detail = resolvedToggle.closest('[data-difference-id]')?.querySelector('.import-difference-compact-resolved-detail, .import-difference-comparison-section');
            const expanded = resolvedToggle.getAttribute('aria-expanded') === 'true';
            resolvedToggle.setAttribute('aria-expanded', expanded ? 'false' : 'true');
            detail?.classList.toggle('hidden', expanded);
            const label = resolvedToggle.querySelector('span');
            if (label) label.textContent = expanded ? 'Ver cambios' : 'Ocultar cambios';
            return;
        }

        const undoButton = event.target.closest('[data-undo-difference]');
        if (undoButton) {
            undoButton.disabled = true;
            return undoDifference(undoButton.dataset.undoDifference, undoButton);
        }

        const decisionButton = event.target.closest('[data-difference-decision]');
        if (!decisionButton) return;
        if (bulkDefaultDecision && isCompactReview && currentStatus === 'pending') {
            const card = decisionButton.closest('[data-difference-id]');
            const id = String(card.dataset.differenceId);
            const decision = decisionButton.dataset.differenceDecision;
            if (decision === bulkDefaultDecision) bulkDecisionOverrides.delete(id);
            else bulkDecisionOverrides.set(id, decision);
            rerenderOptimizedList(`[data-difference-id="${id}"] [data-difference-decision="${decision}"]`);
            announce(`${decision === 'apply' ? 'Archivo importado' : 'Registro actual'} preparado para este registro.`);
            return;
        }
        resolveDifference(decisionButton.closest('[data-difference-id]'), decisionButton.dataset.differenceDecision);
    });

    document.querySelectorAll('[data-difference-status]').forEach(button => button.addEventListener('click', () => {
        currentStatus = button.dataset.differenceStatus;
        currentPage = 1;
        selectedDifferenceId = null;
        bulkDefaultDecision = null;
        bulkDecisionOverrides.clear();
        bulkSaving = false;
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
