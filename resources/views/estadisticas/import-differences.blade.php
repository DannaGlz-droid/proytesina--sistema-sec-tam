@extends('layouts.principal')
@section('title', 'Revisión de diferencias')
@section('content')
    @include('components.header-admin')
    @include('components.nav-estadisticas')

    <main class="users-management-page statistics-failed-imports-page statistics-import-differences-page {{ ($focusMode ?? false) ? 'is-focus-view' : 'is-card-view' }} px-4 sm:px-6 lg:px-10 pt-5 lg:pt-7 pb-8 lg:pb-10">
        <div id="differences-announcer" class="sr-only" role="status" aria-live="polite" aria-atomic="true"></div>

        <x-ui.page-header
            class="users-management-header statistics-import-differences-header"
            title="Diferencias de importación"
            description="Compare la información registrada con los valores del archivo importado y decida qué conservar."
        >
            <x-slot:actions>
                <a
                    href="{{ route('statistic.import-differences-view', ['importId' => $importId] + (($focusMode ?? false) ? [] : ['vista' => 'enfocada'])) }}"
                    class="users-page-create-btn import-differences-view-switch"
                    aria-label="{{ ($focusMode ?? false) ? 'Cambiar a la vista de tarjetas' : 'Probar la vista enfocada' }}"
                >
                    <i class="fas {{ ($focusMode ?? false) ? 'fa-table-cells-large' : 'fa-window-maximize' }}" aria-hidden="true"></i>
                    {{ ($focusMode ?? false) ? 'Vista general' : 'Vista enfocada' }}
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
                        <strong title="{{ $importFileName }}">{{ $importFileName }}</strong>
                        <small id="differences-pending-count">Consultando diferencias pendientes…</small>
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

                @if (!($focusMode ?? false))
                    <x-ui.data-table.page-size
                        id="per-page-import-differences"
                        button-id="per-page-import-differences-button"
                        label-id="per-page-import-differences-label"
                        menu-id="per-page-import-differences-menu"
                        :options="[4, 6, 10]"
                        :selected="4"
                    />
                @endif
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
                <div class="failed-imports-loading-grid import-differences-loading-grid" aria-hidden="true">
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

            <section id="differences-list" class="failed-imports-list import-differences-list hidden" aria-live="polite"></section>

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
    const focusMode = @json($focusMode ?? false);
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

    const toast = (message, type = 'success') => {
        if (typeof window.showToast === 'function') window.showToast(message, type, 3500);
    };

    const statusLabel = status => ({
        applied: 'Se usaron los valores del archivo',
        kept: 'Se conservaron los valores actuales',
        mixed: 'Se combinaron valores actuales y del archivo',
    }[status] || 'Requiere revisión');

    function renderLegacyField(field) {
        return `
            <div class="import-difference-row">
                <div class="import-difference-field-label">${escapeHtml(field.label)}</div>
                <div class="import-difference-value-pair">
                    <div class="import-difference-value import-difference-value--current">
                        <span>Registro actual</span><strong>${escapeHtml(field.previous)}</strong>
                    </div>
                    <div class="import-difference-direction" aria-hidden="true"><i class="fas fa-arrow-right"></i></div>
                    <div class="import-difference-value import-difference-value--proposed">
                        <span>Archivo importado</span><strong>${escapeHtml(field.proposed)}</strong>
                    </div>
                </div>
            </div>`;
    }

    function renderFocusedField(item, field, group, pending, showDecision) {
        return `
            <div class="import-difference-row">
                <div class="import-difference-field-label">${escapeHtml(field.label)}</div>
                <div class="import-difference-value import-difference-value--current">
                    <span>Registro actual</span><strong>${escapeHtml(field.previous)}</strong>
                </div>
                <div class="import-difference-direction" aria-hidden="true"><i class="fas fa-arrow-right"></i></div>
                <div class="import-difference-value import-difference-value--proposed">
                    <span>Archivo importado</span><strong>${escapeHtml(field.proposed)}</strong>
                </div>
                ${showDecision ? renderDecisionChoice(item, group, pending) : ''}
            </div>`;
    }

    function decisionGroups(fields) {
        const groups = new Map();
        fields.forEach(field => {
            const key = field.decision_group || field.field;
            if (!groups.has(key)) {
                groups.set(key, {
                    key,
                    label: field.decision_group_label || field.label,
                    rawFields: field.decision_group_fields || field.raw_fields || [field.field],
                    decision: field.decision || null,
                    fields: [],
                });
            }
            groups.get(key).fields.push(field);
        });
        return [...groups.values()];
    }

    function renderDecisionChoice(item, group, pending) {
        if (!pending) {
            const decision = group.decision;
            return `<div class="import-difference-group-result ${decision === 'apply' ? 'is-applied' : 'is-kept'}">
                <i class="fas ${decision === 'apply' ? 'fa-file-import' : 'fa-database'}" aria-hidden="true"></i>
                ${decision === 'apply' ? 'Archivo' : 'Actual'}
            </div>`;
        }

        const name = `decision-${item.id}-${group.key}`;
        return `<fieldset class="import-difference-group-decision" data-decision-group="${escapeHtml(group.key)}" data-fields="${escapeHtml(group.rawFields.join(','))}">
            <legend class="sr-only">Decisión para ${escapeHtml(group.label)}</legend>
            <div class="import-difference-choice-set">
                <label class="import-difference-choice" title="Conservar el valor actual">
                    <input class="sr-only" type="radio" name="${escapeHtml(name)}" value="keep">
                    <span><i class="fas fa-database" aria-hidden="true"></i>Actual</span>
                </label>
                <label class="import-difference-choice" title="Usar el valor del archivo importado">
                    <input class="sr-only" type="radio" name="${escapeHtml(name)}" value="apply">
                    <span><i class="fas fa-file-import" aria-hidden="true"></i>Archivo</span>
                </label>
            </div>
        </fieldset>`;
    }

    function renderFocusedComparison(item, pending) {
        const groups = decisionGroups(item.changed_fields || []);
        const columnHeadings = `<div class="import-difference-column-headings" aria-hidden="true">
            <span>Campo</span><span>Registro actual</span><span></span><span>Archivo importado</span><span>Decisión</span>
        </div>`;

        return `${columnHeadings}${groups.map(group => `
            <section class="import-difference-group ${group.fields.length > 1 ? 'is-related' : ''}">
                ${group.fields.length > 1 ? `<div class="import-difference-group-heading"><div><i class="fas fa-link" aria-hidden="true"></i><strong>${escapeHtml(group.label)}</strong><span>${group.fields.length} campos relacionados</span></div>${renderDecisionChoice(item, group, pending)}</div>` : ''}
                <div class="import-difference-group-fields">${group.fields.map(field => renderFocusedField(item, field, group, pending, group.fields.length === 1)).join('')}</div>
            </section>`).join('')}`;
    }

    function renderCard(item) {
        const pending = item.status === 'pending';
        const fields = focusMode
            ? renderFocusedComparison(item, pending)
            : (item.changed_fields || []).map(renderLegacyField).join('');
        const fieldCount = item.field_count ?? item.changed_fields.length;
        const focusedGroupCount = focusMode ? decisionGroups(item.changed_fields || []).length : 0;
        const fieldSummary = `${fieldCount} ${fieldCount === 1 ? 'campo diferente' : 'campos diferentes'}`;
        const resolutionSummary = item.resolved_by ? `Resuelto por ${item.resolved_by}` : 'Diferencia resuelta';

        return `
            <article class="record-card import-difference-card ${pending ? 'is-pending' : 'is-resolved'}" data-difference-id="${item.id}">
                <div class="failed-record-error import-difference-alert ${pending ? '' : 'is-corrected'}">
                    <span class="failed-record-error-icon"><i class="fas ${pending ? 'fa-code-compare' : 'fa-circle-check'}" aria-hidden="true"></i></span>
                    <div>
                        <p class="failed-record-error-title">${pending ? `${fieldSummary} por revisar` : escapeHtml(resolutionSummary)}</p>
                    </div>
                </div>

                <section class="failed-correction-section import-difference-identity-section">
                    ${focusMode ? '' : `<div class="failed-correction-section-heading">
                        <i class="far fa-user" aria-hidden="true"></i>
                        <h2>Información de la persona</h2>
                    </div>`}
                    <div class="import-difference-identity">
                        <div><span>Nombre</span><strong>${escapeHtml(item.person_name)}</strong></div>
                        <div><span>Folio</span><strong>${escapeHtml(item.gov_folio || 'Sin folio')}</strong></div>
                    </div>
                </section>

                <section class="failed-correction-section import-difference-fields-section">
                    ${focusMode ? '' : `<div class="failed-correction-section-heading">
                        <i class="fas fa-code-compare" aria-hidden="true"></i>
                        <h2>Campos con diferencias</h2>
                    </div>`}
                    <div class="import-difference-comparison">${fields}</div>
                </section>

                ${pending && focusMode ? `<footer class="action-buttons-view import-difference-actions import-difference-focused-actions ${focusedGroupCount > 1 ? '' : 'is-single-decision'}">
                    ${focusedGroupCount > 1 ? `<div class="import-difference-shortcuts" aria-label="Aplicar una decisión a todos los grupos"><span>Aplicar a todos:</span>
                        <button type="button" class="ui-button ui-button--tertiary" data-set-all-decisions="keep">Actual</button>
                        <button type="button" class="ui-button ui-button--tertiary" data-set-all-decisions="apply">Archivo</button>
                    </div>` : ''}
                    <button type="button" class="ui-button ui-button--primary" data-save-decisions ${isPreview ? 'disabled title="Las decisiones están deshabilitadas en la vista previa"' : ''}><i class="fas fa-check" aria-hidden="true"></i>Guardar y continuar</button>
                </footer>` : (pending ? `<footer class="action-buttons-view import-difference-actions">
                    <button type="button" class="ui-button ui-button--secondary" data-difference-decision="keep" ${isPreview ? 'disabled title="Acción deshabilitada en la vista previa"' : ''}>Conservar actual</button>
                    <button type="button" class="ui-button ui-button--primary" data-difference-decision="apply" ${isPreview ? 'disabled title="Acción deshabilitada en la vista previa"' : ''}><i class="fas fa-check" aria-hidden="true"></i>Usar valores del archivo</button>
                </footer>` : '')}
            </article>`;
    }

    function renderEmpty(total) {
        const legacy = total === 0 && expectedDifferenceCount > 0;
        list.innerHTML = `<div class="users-table-state users-table-state--empty import-differences-empty">
            <i class="fas ${legacy ? 'fa-clock-rotate-left' : 'fa-check'}" aria-hidden="true"></i>
            <strong>${legacy ? 'Esta importación no guardó el detalle de comparación' : (currentStatus === 'pending' ? 'No quedan diferencias pendientes' : 'Todavía no hay diferencias resueltas')}</strong>
            <span>${legacy ? 'Fue procesada antes de habilitar esta revisión. Las cargas nuevas guardarán ambos valores automáticamente.' : (currentStatus === 'pending' ? 'Todas las diferencias de esta importación ya fueron atendidas.' : 'Las decisiones tomadas en esta importación aparecerán aquí.')}</span>
        </div>`;
    }

    function renderPagination(meta) {
        pagination.innerHTML = '';
        if (!meta || meta.last_page < 1) return;

        const navigation = document.createElement('div');
        navigation.className = 'fb-pagination';
        navigation.setAttribute('role', 'navigation');
        navigation.setAttribute('aria-label', focusMode ? 'Navegación entre registros con diferencias' : 'Paginación de diferencias');

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
            per_page: focusMode ? 1 : (perPageSelect?.value || 4),
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
            document.getElementById('differences-pending-count').textContent = payload.counts.pending === 1
                ? '1 diferencia pendiente de revisión'
                : `${payload.counts.pending} diferencias pendientes de revisión`;
            const first = page.total ? page.from : 0;
            const last = page.total ? page.to : 0;
            info.innerHTML = page.total
                ? (focusMode
                    ? `Registro <span class="font-semibold text-gray-900">${first}</span> de <span class="font-semibold text-gray-900">${page.total}</span>`
                    : `Mostrando <span class="font-semibold text-gray-900">${first}-${last}</span> de <span class="font-semibold text-gray-900">${page.total}</span>`)
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

    async function resolveDifference(card, requestBody) {
        const isGlobal = Boolean(requestBody.decision);
        const isApply = requestBody.decision === 'apply';
        const confirmed = typeof window.confirmDialog === 'function'
            ? await window.confirmDialog({
                title: isGlobal
                    ? (isApply ? '¿Usar los valores del archivo?' : '¿Conservar los valores actuales?')
                    : '¿Guardar las decisiones seleccionadas?',
                message: isGlobal
                    ? (isApply
                        ? 'Se actualizarán únicamente los campos mostrados como diferentes. La decisión quedará registrada.'
                        : 'No se modificará el registro. La diferencia quedará marcada como revisada.')
                    : 'Se aplicará la elección realizada para cada campo o grupo relacionado y quedará registrada en el historial.',
                confirmText: isGlobal ? (isApply ? 'Usar valores del archivo' : 'Conservar actuales') : 'Guardar decisiones',
                cancelText: 'Cancelar',
                tone: isApply || !isGlobal ? 'primary' : 'neutral',
            })
            : window.confirm(isGlobal
                ? (isApply ? '¿Usar los valores del archivo?' : '¿Conservar los valores actuales?')
                : '¿Guardar las decisiones seleccionadas?');
        if (!confirmed) return;

        const buttons = card.querySelectorAll('button');
        buttons.forEach(button => button.disabled = true);
        try {
            const response = await fetch(`/api/estadisticas/diferencias/${card.dataset.differenceId}/resolver`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                body: JSON.stringify(requestBody),
            });
            const payload = await response.json();
            if (!response.ok || !payload.ok) throw new Error(payload.message || 'No se pudo guardar la decisión.');
            toast(payload.message);
            announce(payload.message);
            shouldScrollToReview = true;
            await load();
        } catch (error) {
            buttons.forEach(button => button.disabled = false);
            toast(error.message, 'error');
        }
    }

    function collectDecisions(card) {
        const decisions = {};
        let complete = true;

        card.querySelectorAll('[data-decision-group]').forEach(group => {
            group.classList.remove('has-error');
            const selected = group.querySelector('input[type="radio"]:checked');
            if (!selected) {
                complete = false;
                group.classList.add('has-error');
                return;
            }
            String(group.dataset.fields || '').split(',').filter(Boolean).forEach(field => {
                decisions[field] = selected.value;
            });
        });

        if (!complete) {
            toast('Seleccione una opción en todos los campos antes de guardar.', 'error');
            card.querySelector('.import-difference-group-decision.has-error')?.scrollIntoView({ behavior: 'smooth', block: 'center' });
            return null;
        }
        return decisions;
    }

    list.addEventListener('click', event => {
        const retry = event.target.closest('[data-retry-differences]');
        if (retry) return load();

        const setAll = event.target.closest('[data-set-all-decisions]');
        if (setAll) {
            const card = setAll.closest('[data-difference-id]');
            card.querySelectorAll(`input[type="radio"][value="${setAll.dataset.setAllDecisions}"]`).forEach(input => {
                input.checked = true;
                input.closest('[data-decision-group]')?.classList.remove('has-error');
            });
            announce(setAll.dataset.setAllDecisions === 'apply' ? 'Se seleccionó usar el archivo en todos los campos.' : 'Se seleccionó conservar todos los valores actuales.');
            return;
        }

        const saveDecisions = event.target.closest('[data-save-decisions]');
        if (saveDecisions) {
            const card = saveDecisions.closest('[data-difference-id]');
            const decisions = collectDecisions(card);
            if (decisions) resolveDifference(card, { decisions });
            return;
        }

        const decisionButton = event.target.closest('[data-difference-decision]');
        if (!decisionButton) return;
        resolveDifference(decisionButton.closest('[data-difference-id]'), { decision: decisionButton.dataset.differenceDecision });
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
