<x-ui.dialog
    id="reject-modal"
    size="task"
    labelledby="reject-modal-heading"
    describedby="reject-modal-consequence"
    class="reports-reject-modal"
    panel-class="reports-reject-card"
    :overlay-attributes="['data-reject-dialog-overlay' => '']"
>
    <header class="ui-dialog__header reports-reject-heading">
        <div class="ui-dialog__heading reports-reject-heading-copy">
            <div class="ui-dialog__title-row reports-reject-title-row">
                <i class="fas fa-triangle-exclamation" aria-hidden="true"></i>
                <h2 id="reject-modal-heading" class="ui-dialog__title">Rechazar reporte</h2>
            </div>
            <div class="reports-reject-heading-context">
                <p id="reject-modal-title" class="reports-reject-report-title"></p>
                <p id="reject-modal-consequence" class="reports-reject-heading-consequence">El reporte volverá al autor para que pueda corregirlo y reenviarlo.</p>
            </div>
        </div>
        <button type="button" id="reject-modal-close" onclick="closeRejectModal()" class="ui-dialog__close modal-cerrar" aria-label="Cerrar rechazo" title="Cerrar">
            <i class="fas fa-times" aria-hidden="true"></i>
        </button>
    </header>

    <input type="hidden" id="reject-modal-publication-id">

    <div class="ui-dialog__body reports-reject-body">
        <div class="reports-reject-context">
            <p class="reports-reject-question reports-reject-question-default">¿Desea rechazar <strong id="reject-modal-title-default"></strong>?</p>
        </div>
        <label for="rejection-reason">Motivo del rechazo <span aria-hidden="true">*</span></label>
        <textarea
            id="rejection-reason"
            rows="4"
            aria-describedby="rejection-reason-help rejection-reason-error"
            aria-invalid="false"
            placeholder="Explique brevemente por qué se rechaza este reporte"
            maxlength="500"
            required></textarea>
        <div class="reports-reject-field-meta">
            <p id="rejection-reason-help" class="reports-reject-help">
                <span class="reports-reject-help-default">Máximo 500 caracteres</span>
                <span class="reports-reject-help-alcohol">Describa claramente qué necesita corregirse.</span>
            </p>
            <output id="rejection-reason-count" for="rejection-reason">0/500</output>
        </div>
        <p id="rejection-reason-error" class="reports-reject-error hidden" role="alert">Escriba el motivo del rechazo.</p>
    </div>

    <footer class="ui-dialog__actions reports-reject-actions">
        <button type="button" id="reject-modal-cancel" onclick="closeRejectModal()" class="reports-button reports-button--secondary">Cancelar</button>
        <button type="button" id="reject-modal-submit" onclick="submitRejection()" class="reports-button reports-button--danger">Rechazar</button>
    </footer>
</x-ui.dialog>
