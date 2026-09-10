<x-ui.dialog
    id="modalDescargas"
    size="task"
    labelledby="download-dialog-title"
    describedby="download-dialog-description"
    class="download-dialog"
    panel-class="download-dialog__panel"
    :overlay-attributes="['data-download-dialog-close' => '']"
>
    <header class="ui-dialog__header">
        <div class="ui-dialog__heading">
            <div class="ui-dialog__title-row">
                <i class="fas fa-download" aria-hidden="true"></i>
                <h2 id="download-dialog-title" class="ui-dialog__title">Opciones de descarga</h2>
            </div>
            <p id="download-dialog-description" class="ui-dialog__description">Seleccione el formato para exportar la gráfica actual.</p>
        </div>
        <button type="button" class="ui-dialog__close" data-download-dialog-close aria-label="Cerrar opciones de descarga" title="Cerrar">
            <i class="fas fa-times" aria-hidden="true"></i>
        </button>
    </header>

    <div class="ui-dialog__body download-dialog__body">
        <div class="download-dialog__options" role="group" aria-label="Formatos disponibles">
            <button type="button" class="download-dialog__option" id="descargarPDF">
                <i class="far fa-file-pdf" aria-hidden="true"></i>
                <span><strong>Documento PDF</strong><small>Preparado para reportes e impresión.</small></span>
                <i class="fas fa-chevron-right download-dialog__chevron" aria-hidden="true"></i>
            </button>
            <button type="button" class="download-dialog__option" id="descargarPNG">
                <i class="far fa-image" aria-hidden="true"></i>
                <span><strong>Imagen PNG</strong><small>Útil para compartir o insertar.</small></span>
                <i class="fas fa-chevron-right download-dialog__chevron" aria-hidden="true"></i>
            </button>
        </div>
        <p id="download-dialog-error" class="download-dialog__error hidden" role="alert">
            <i class="fas fa-circle-exclamation" aria-hidden="true"></i>
            <span>No se pudo generar el archivo. Inténtelo nuevamente.</span>
        </p>
    </div>

    <footer class="ui-dialog__actions">
        <button type="button" class="ui-button ui-button--secondary" data-download-dialog-close>Cancelar</button>
    </footer>
</x-ui.dialog>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('modalDescargas');
    if (!modal || modal.dataset.initialized === 'true') return;
    modal.dataset.initialized = 'true';
    const closeControls = modal.querySelectorAll('[data-download-dialog-close]');
    const descargarPDF = document.getElementById('descargarPDF');
    const descargarPNG = document.getElementById('descargarPNG');
    let previousFocus = null;
    let previousOverflow = '';
    let closing = false;

    function mostrarModal(tipo, chartId = '') {
        previousFocus = document.activeElement;
        previousOverflow = document.body.style.overflow;
        closing = false;
        modal.classList.remove('hidden', 'is-closing');
        modal.setAttribute('aria-hidden', 'false');
        document.getElementById('download-dialog-error')?.classList.add('hidden');
        document.body.style.overflow = 'hidden';
        requestAnimationFrame(() => {
            modal.classList.add('is-open');
            descargarPDF.focus();
        });
    }

    function cerrarModal(callback) {
        if (closing || modal.classList.contains('hidden')) return;
        closing = true;
        modal.classList.remove('is-open');
        modal.classList.add('is-closing');
        const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
        window.setTimeout(() => {
            modal.classList.add('hidden');
            modal.classList.remove('is-closing');
            modal.setAttribute('aria-hidden', 'true');
            document.body.style.overflow = previousOverflow;
            closing = false;
            previousFocus?.focus();
            if (typeof callback === 'function') callback();
        }, reducedMotion ? 0 : 130);
    }

    closeControls.forEach(control => control.addEventListener('click', () => cerrarModal()));
    document.addEventListener('keydown', event => {
        if (modal.classList.contains('hidden')) return;
        if (event.key === 'Escape') {
            event.preventDefault();
            cerrarModal();
            return;
        }
        if (event.key !== 'Tab') return;
        const focusable = Array.from(modal.querySelectorAll('button:not([disabled])')).filter(element => element.offsetParent !== null);
        if (!focusable.length) return;
        const first = focusable[0];
        const last = focusable[focusable.length - 1];
        if (event.shiftKey && document.activeElement === first) { event.preventDefault(); last.focus(); }
        if (!event.shiftKey && document.activeElement === last) { event.preventDefault(); first.focus(); }
    });

    async function exportFromDialog(exportType) {
        const error = document.getElementById('download-dialog-error');
        error?.classList.add('hidden');
        [descargarPDF, descargarPNG].forEach(button => {
            button.disabled = true;
            button.setAttribute('aria-busy', 'true');
        });

        try {
            const exported = typeof window.exportStatisticsChart === 'function'
                ? await window.exportStatisticsChart(exportType)
                : false;
            if (!exported) {
                error?.classList.remove('hidden');
                return;
            }
            cerrarModal();
        } catch (exportError) {
            console.error('No se pudo exportar la gráfica:', exportError);
            error?.classList.remove('hidden');
        } finally {
            [descargarPDF, descargarPNG].forEach(button => {
                button.disabled = false;
                button.removeAttribute('aria-busy');
            });
        }
    }

    descargarPDF.addEventListener('click', () => exportFromDialog('pdf'));
    descargarPNG.addEventListener('click', () => exportFromDialog('png-white'));
    window.mostrarModalDescargas = mostrarModal;
});
</script>
