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
            <p id="download-dialog-description" class="ui-dialog__description">Seleccione el formato en el que desea exportar las gráficas.</p>
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
    let tipoDescarga = 'todo';
    let chartIdIndividual = '';
    let previousFocus = null;
    let previousOverflow = '';
    let closing = false;

    function mostrarModal(tipo, chartId = '') {
        tipoDescarga = tipo;
        chartIdIndividual = chartId;
        previousFocus = document.activeElement;
        previousOverflow = document.body.style.overflow;
        closing = false;
        modal.classList.remove('hidden', 'is-closing');
        modal.setAttribute('aria-hidden', 'false');
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

    descargarPDF.addEventListener('click', () => cerrarModal(() => tipoDescarga === 'todo' ? descargarTodoPDF() : descargarIndividualPDF(chartIdIndividual)));
    descargarPNG.addEventListener('click', () => cerrarModal(() => tipoDescarga === 'todo' ? descargarTodoPNG() : descargarIndividualPNG(chartIdIndividual)));

    function descargarTodoPDF() {
        const link = document.createElement('a');
        link.href = '#';
        link.download = `estadisticas_completas_${new Date().toISOString().slice(0, 10)}.pdf`;
        link.click();
    }
    function descargarIndividualPDF(chartId) {
        const link = document.createElement('a');
        link.href = '#';
        link.download = `${chartId}_${new Date().toISOString().slice(0, 10)}.pdf`;
        link.click();
    }
    function descargarTodoPNG() {
        Object.keys(charts).forEach((chartId, index) => {
            setTimeout(() => {
                const chart = charts[chartId];
                if (!chart) return;
                const imageLink = document.createElement('a');
                imageLink.href = chart.toBase64Image();
                imageLink.download = `${chartId}_${new Date().toISOString().slice(0, 10)}.png`;
                imageLink.click();
            }, index * 300);
        });
    }
    function descargarIndividualPNG(chartId) {
        const chart = charts[chartId];
        if (!chart) return;
        const imageLink = document.createElement('a');
        imageLink.href = chart.toBase64Image();
        imageLink.download = `${chartId}_${new Date().toISOString().slice(0, 10)}.png`;
        imageLink.click();
    }
    window.mostrarModalDescargas = mostrarModal;
});
</script>
