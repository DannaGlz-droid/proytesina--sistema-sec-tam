@props(['tipo', 'titulo', 'colorBadge', 'colorBorder', 'modalId'])

@php
    $tipoLabel = match ($tipo) {
        'seguridad_vial' => 'Seguridad Vial',
        'alcoholimetria' => 'Alcoholimetría',
        'observatorio de lesiones' => 'Observatorio de lesiones',
        'grupos_vulnerables' => 'Grupos Vulnerables',
        default => ucwords(str_replace('_', ' ', $tipo)),
    };
@endphp

@once
<style>
    :root {
        --report-ink: #10233f;
        --report-muted: #526278;
        --report-line: #d8dee8;
        --report-paper: #ffffff;
        --report-panel: #f6f8fa;
        --report-focus: #475569;
        --report-confirm: var(--brand-primary, #611132);
        --report-confirm-hover: var(--brand-primary-hover, #4a0e26);
    }

    .modal-titulo {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        line-height: 1.18;
        max-height: 2.36em;
    }

    .comentarios-container {
        max-height: 0;
        opacity: 0;
        overflow: hidden;
        transition: max-height 0.28s ease, opacity 0.24s ease;
        border-radius: 0.5rem;
    }

    .comentarios-container.expanded {
        max-height: none;
        opacity: 1;
    }

    .description-scroll::-webkit-scrollbar,
    .modal-content-scroll::-webkit-scrollbar {
        width: 6px;
    }

    .description-scroll::-webkit-scrollbar-track,
    .modal-content-scroll::-webkit-scrollbar-track {
        background: transparent;
    }

    .description-scroll::-webkit-scrollbar-thumb,
    .modal-content-scroll::-webkit-scrollbar-thumb {
        background: #b8c0ca;
        border-radius: 999px;
    }

    .description-scroll,
    .modal-content-scroll {
        scrollbar-width: thin;
        scrollbar-color: #b8c0ca transparent;
    }

    .report-modal-overlay,
    .government-confirm-modal {
        background: rgba(15, 23, 42, 0.34);
        backdrop-filter: none;
    }

    .publication-detail-modal {
        color: var(--report-ink);
        border-radius: 7px !important;
        box-shadow: 0 10px 24px rgba(15, 23, 42, 0.14), 0 1px 3px rgba(15, 23, 42, 0.06);
        font-family: "Open Sans", system-ui, sans-serif;
        font-variant-numeric: tabular-nums;
    }

    .publication-detail-modal::before {
        content: "";
        position: absolute;
        inset: 0;
        pointer-events: none;
        border-radius: inherit;
        box-shadow: inset 0 1px 0 rgba(255,255,255,0.92);
    }

    .publication-detail-modal .report-header {
        background: var(--report-paper);
        border-bottom: 0;
    }

    .publication-detail-modal .report-heading-meta {
        display: flex;
        align-items: center;
        gap: 0.55rem;
        margin-bottom: 0.45rem;
    }

    .publication-detail-modal .report-kicker {
        color: var(--report-muted);
        font-size: 0.72rem;
        font-weight: 500;
        letter-spacing: 0;
        text-transform: none;
    }

    .publication-detail-modal .report-type-label {
        display: inline-flex;
        align-items: center;
        min-height: 1.55rem;
        padding: 0.18rem 0.52rem;
        border: 1px solid #d8dee8;
        border-radius: 6px;
        background: #f8fafc;
        color: #334155;
        font-size: 0.68rem;
        font-weight: 600;
        line-height: 1.1;
    }

    .publication-detail-modal .report-type-label[data-report-type="seguridad_vial"] {
        border-color: #bfdbf4;
        background: #f0f8ff;
        color: #377fbf;
    }

    .publication-detail-modal .report-type-label[data-report-type="alcoholimetria"] {
        border-color: #efb8c8;
        background: #fff2f6;
        color: #b51646;
    }

    .publication-detail-modal .report-type-label[data-report-type="observatorio de lesiones"] {
        border-color: #c8ddb8;
        background: #f3f9ee;
        color: #4f862e;
    }

    .publication-detail-modal .report-type-label[data-report-type="grupos_vulnerables"] {
        border-color: #dac9e7;
        background: #faf5fd;
        color: #74439a;
    }

    .publication-detail-modal .report-meta-bar {
        background: var(--report-paper);
        border-bottom: 1px solid var(--report-line);
    }

    .publication-detail-modal .report-meta-grid {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 0.35rem 0;
    }

    .publication-detail-modal .report-meta-item {
        display: inline-flex;
        align-items: baseline;
        min-width: 0;
    }

    .publication-detail-modal .report-meta-item + .report-meta-item::before {
        content: "\00b7";
        margin: 0 0.6rem;
        color: #a0aaba;
    }

    .publication-detail-modal .report-meta-label {
        color: var(--report-muted);
        font-size: 0.74rem;
        font-weight: 400;
        letter-spacing: 0;
        text-transform: none;
    }

    .publication-detail-modal .report-meta-label::after {
        content: ":";
        margin-right: 0.25rem;
    }

    .publication-detail-modal .report-meta-value {
        color: var(--report-muted);
        font-size: 0.74rem;
        font-weight: 500;
    }

    .publication-detail-modal .report-body {
        background: var(--report-paper);
    }

    .publication-detail-modal .h-px {
        background: var(--report-line) !important;
        margin-bottom: 1.35rem !important;
    }

    .publication-detail-modal h4 {
        display: flex;
        align-items: center;
        gap: 0.65rem;
        margin-bottom: 0.85rem !important;
        color: var(--report-ink) !important;
        font-size: 0.86rem !important;
        font-weight: 800 !important;
        letter-spacing: 0;
    }

    .publication-detail-modal h4::before {
        display: none;
    }

    .publication-detail-modal h5 {
        color: var(--report-muted) !important;
        font-size: 0.68rem !important;
        font-weight: 800 !important;
        letter-spacing: 0.04em;
        text-transform: uppercase;
    }

    .publication-detail-modal i.text-\[\#404041\] {
        color: var(--report-muted) !important;
    }

    .publication-detail-modal div.border-\[\#404041\] {
        background: linear-gradient(180deg, #ffffff 0%, #fbfcfd 100%) !important;
        border-color: var(--report-line) !important;
        border-radius: 0.5rem !important;
        box-shadow: none !important;
    }

    .publication-detail-modal div.border-\[\#404041\]:hover {
        border-color: #bfc7d0 !important;
        box-shadow: none !important;
    }

    .publication-detail-modal .font-lora {
        font-family: "Open Sans", system-ui, sans-serif !important;
    }

    .publication-detail-modal .datos-generales-grid {
        gap: 0.8rem !important;
    }

    .publication-detail-modal .datos-generales-grid > div {
        min-height: 116px;
        display: flex;
        flex-direction: column;
    }

    .publication-detail-modal .datos-generales-grid .dato-general-value {
        min-height: 2.35rem;
        display: flex;
        align-items: flex-start;
        color: var(--report-ink) !important;
        font-size: 0.95rem !important;
        line-height: 1.22;
        word-break: break-word;
    }

    .publication-detail-modal .modal-descripcion {
        color: #38404a !important;
        font-size: 0.94rem;
        line-height: 1.65;
    }

    .publication-detail-modal .modal-status-container > div,
    .publication-detail-modal .comentario-item {
        background: #ffffff !important;
        border-color: var(--report-line) !important;
        border-radius: 0.5rem !important;
        box-shadow: none !important;
    }

    .publication-detail-modal .status-card {
        padding: 0.75rem 0.85rem !important;
        border-left: 3px solid #8b949e !important;
        background: #f8fafc !important;
    }

    .publication-detail-modal .status-main {
        display: flex;
        align-items: center;
        gap: 0.55rem;
        min-width: 0;
    }

    .publication-detail-modal .status-card.status-approved {
        border-left-color: #2f7d32 !important;
    }

    .publication-detail-modal .status-card.status-rejected {
        border-left-color: #ab1a1a !important;
    }

    .publication-detail-modal .status-card.status-pending {
        border-left-color: #8a6d18 !important;
    }

    .publication-detail-modal .status-icon {
        width: 1.25rem;
        height: 1.25rem;
        border-radius: 999px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex: 0 0 auto;
        background: transparent !important;
    }

    .publication-detail-modal .status-reason {
        margin: 0.65rem 0 0 1.8rem;
        padding-top: 0.6rem;
        border-top: 1px solid #e2e8f0;
    }

    .publication-detail-modal .status-reason-label {
        display: inline;
        margin-right: 0.3rem;
        color: var(--report-muted);
        font-size: 0.72rem;
        font-weight: 600;
    }

    .publication-detail-modal .status-reason-text {
        display: inline;
        color: #334155;
        font-size: 0.78rem;
        line-height: 1.45;
    }

    .publication-detail-modal .modal-archivos {
        grid-template-columns: repeat(auto-fill, minmax(180px, 1fr)) !important;
    }

    .publication-detail-modal .archivo-preview-card {
        position: relative;
        display: flex;
        flex-direction: column;
        align-items: stretch;
        gap: 0;
        border-radius: 0.5rem !important;
        border-color: var(--report-line) !important;
        box-shadow: none !important;
        min-height: 134px;
        background: #ffffff;
        isolation: isolate;
        overflow: visible !important;
    }

    .publication-detail-modal .archivo-thumb {
        position: relative;
        height: 92px;
        overflow: hidden;
        background:
            linear-gradient(#f8fafc, #eef2f6),
            repeating-linear-gradient(90deg, rgba(32,36,40,0.05) 0 1px, transparent 1px 22px);
        border-bottom: 1px solid var(--report-line);
        border-radius: 0.5rem 0.5rem 0 0;
    }

    .publication-detail-modal .archivo-thumb img,
    .publication-detail-modal .archivo-thumb canvas {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
    }

    .publication-detail-modal .archivo-thumb-fallback {
        height: 100%;
        display: grid;
        place-items: center;
        color: #68717b;
    }

    .publication-detail-modal .archivo-thumb-fallback i {
        font-size: 1.65rem;
    }

    .publication-detail-modal .archivo-sheet-thumb {
        height: 100%;
        padding: 0.55rem;
        background: #f8fafc;
    }

    .publication-detail-modal .archivo-sheet-bar {
        height: 0.52rem;
        width: 46%;
        margin-bottom: 0.45rem;
        border-radius: 0.15rem;
        background: #22a567;
    }

    .publication-detail-modal .archivo-sheet-grid {
        display: grid;
        grid-template-columns: repeat(5, 1fr);
        gap: 2px;
    }

    .publication-detail-modal .archivo-sheet-grid span {
        height: 0.48rem;
        background: #dfe5eb;
        border-radius: 1px;
    }

    .publication-detail-modal .archivo-sheet-grid span:nth-child(5n),
    .publication-detail-modal .archivo-sheet-grid span:nth-child(7),
    .publication-detail-modal .archivo-sheet-grid span:nth-child(13) {
        background: #b9c4cf;
    }

    .publication-detail-modal .archivo-actions {
        position: absolute;
        inset: 0 0 auto 0;
        height: 92px;
        display: flex;
        align-items: flex-end;
        justify-content: center;
        gap: 0.4rem;
        padding: 0.55rem;
        background: linear-gradient(180deg, rgba(32,36,40,0.08), rgba(32,36,40,0.68));
        opacity: 0;
        pointer-events: none;
        transition: opacity 0.18s ease;
        z-index: 2;
    }

    .publication-detail-modal .archivo-preview-card:hover .archivo-actions,
    .publication-detail-modal .archivo-preview-card:focus-within .archivo-actions {
        opacity: 1;
        pointer-events: auto;
    }

    .publication-detail-modal .archivo-action {
        width: 2rem;
        height: 2rem;
        border-radius: 0.35rem;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: rgba(255,255,255,0.94);
        color: #2f3439;
        border: 1px solid rgba(255,255,255,0.76);
        box-shadow: 0 8px 16px rgba(0,0,0,0.18);
        transition: transform 0.16s ease, background 0.16s ease;
    }

    .publication-detail-modal .archivo-action:hover {
        background: #ffffff;
        transform: translateY(-1px);
    }

    .publication-detail-modal .archivo-action:disabled {
        opacity: 0.55;
        cursor: not-allowed;
        transform: none;
    }

    .publication-detail-modal .archivo-info-popover {
        position: absolute;
        right: 0.5rem;
        top: 2.75rem;
        width: min(13rem, calc(100vw - 3rem));
        padding: 0.65rem 0.75rem;
        background: #202428;
        color: #ffffff;
        border-radius: 0.45rem;
        font-size: 0.72rem;
        line-height: 1.45;
        opacity: 0;
        transform: translateY(-4px);
        pointer-events: none;
        transition: opacity 0.16s ease, transform 0.16s ease;
        z-index: 4;
    }

    .publication-detail-modal .archivo-info:hover + .archivo-info-popover,
    .publication-detail-modal .archivo-info:focus + .archivo-info-popover {
        opacity: 1;
        transform: translateY(0);
    }

    .publication-detail-modal .archivo-meta {
        min-width: 0;
        padding: 0.65rem 0.7rem 0.72rem;
        background: #ffffff;
    }

    .publication-detail-modal .archivo-preview-card:hover {
        border-color: #aeb7c1 !important;
        background: #fbfcfd !important;
        z-index: 5;
    }

    .publication-detail-modal .archivo-preview-card:focus-within {
        z-index: 5;
    }

    .publication-detail-modal .comentarios-shell {
        background: #f4f6f8;
        border: 1px solid var(--report-line);
        border-radius: 0.5rem !important;
        box-shadow: none;
    }

    .publication-detail-modal .comentarios-toggle {
        background: transparent;
        border-radius: 0.4rem;
    }

    .publication-detail-modal .comentarios-toggle:hover {
        background: #e9edf1;
    }

    .publication-detail-modal .modal-comentarios {
        padding-right: 0;
        overflow: visible;
    }

    .publication-detail-modal .comentario-item:hover {
        border-color: #bfc7d0 !important;
    }

    .publication-detail-modal .descargar-todos-archivos,
    .publication-detail-modal .enviar-comentario {
        min-height: 38px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.45rem;
        line-height: 1;
        border-radius: 0.45rem !important;
    }

    .publication-detail-modal .descargar-todos-archivos {
        padding: 0.5rem 0.85rem;
        box-shadow: none;
        background: #ffffff;
        color: #334155;
        border: 1px solid #cbd5e1;
    }

    .publication-detail-modal .descargar-todos-archivos:hover {
        background: #f8fafc;
        color: #10233f;
        transform: translateY(1px);
    }

    .publication-detail-modal .enviar-comentario {
        width: 118px;
        padding: 0 0.9rem;
        background: var(--report-confirm) !important;
    }

    .publication-detail-modal .enviar-comentario:hover {
        background: var(--report-confirm-hover) !important;
    }

    .publication-detail-modal .nuevo-comentario:focus {
        border-color: var(--report-focus) !important;
        box-shadow: 0 0 0 2px rgba(71, 85, 105, 0.18) !important;
    }

    .publication-detail-modal .modal-cerrar,
    .government-confirm-card .modal-cerrar {
        border: 1px solid transparent;
        border-radius: 0.45rem !important;
    }

    .publication-detail-modal .modal-cerrar:hover,
    .government-confirm-card .modal-cerrar:hover {
        border-color: var(--report-line);
        background: #eef1f4;
    }

    .publication-detail-modal .approval-buttons-container button {
        border-radius: 0.45rem !important;
        box-shadow: none !important;
        min-height: 38px;
    }

    .publication-detail-modal .aprobar-reporte,
    .publication-detail-modal .reenviar-reporte {
        border-color: var(--report-confirm) !important;
        background: var(--report-confirm) !important;
        color: #ffffff !important;
    }

    .publication-detail-modal .aprobar-reporte:hover,
    .publication-detail-modal .reenviar-reporte:hover {
        background: var(--report-confirm-hover) !important;
    }

    .publication-detail-modal .rechazar-reporte {
        background: #ffffff !important;
    }

    .publication-detail-modal .rechazar-reporte:hover {
        background: #fff5f5 !important;
    }

    .government-confirm-card {
        border-radius: 0.5rem;
        box-shadow: 0 24px 64px rgba(15, 23, 42, 0.24), 0 2px 8px rgba(15, 23, 42, 0.12);
    }

    /* Detail modal aligned with the users pilot. */
    .publication-detail-modal .report-header {
        min-height: 0 !important;
        padding: 1.125rem 1.25rem 0.9rem !important;
    }

    .publication-detail-modal .modal-titulo {
        color: var(--report-ink) !important;
        font-size: 1.125rem !important;
        font-weight: 700 !important;
        line-height: 1.3;
    }

    .publication-detail-modal .report-meta-bar {
        padding: 0 1.25rem 1rem !important;
    }

    .publication-detail-modal .report-meta-grid {
        gap: 0.35rem 0 !important;
    }

    .publication-detail-modal .report-body {
        padding: 1.15rem 1.25rem 1.35rem !important;
        scroll-padding-bottom: 1.5rem;
    }

    .publication-detail-modal.has-actions-footer .report-body {
        padding-bottom: 4rem !important;
        scroll-padding-bottom: 4rem;
    }

    .publication-detail-modal .report-workspace {
        display: grid;
        grid-template-columns: minmax(0, 1.08fr) minmax(20rem, .92fr);
        column-gap: 1.25rem;
        align-items: start;
    }

    .publication-detail-modal .report-primary-pane,
    .publication-detail-modal .report-secondary-pane,
    .publication-detail-modal .report-comments-pane {
        min-width: 0;
    }

    .publication-detail-modal .report-secondary-pane {
        padding-left: 1.25rem;
        border-left: 1px solid #dbe3ec;
    }

    .publication-detail-modal .report-pane-section + .report-pane-section {
        margin-top: 1.25rem;
        padding-top: 1.25rem;
        border-top: 1px solid #dbe3ec;
    }

        .publication-detail-modal .report-comments-pane {
            grid-column: 1 / -1;
            margin-top: 1.25rem;
            padding-top: 1.25rem;
            border-top: 1px solid #dbe3ec;
        }

        .publication-detail-modal .report-primary-pane > .h-px:last-child,
        .publication-detail-modal .report-pane-section > .h-px:last-child {
            display: none;
        }

        .publication-detail-modal .report-pane-section > .mb-6 {
            margin-bottom: 0;
        }

    .publication-detail-modal .report-primary-pane .datos-generales-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
    }

    .publication-detail-modal .report-secondary-pane .modal-archivos {
        grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
    }

    .publication-detail-modal.has-actions-footer .comentario-form-container {
        margin-bottom: 0.75rem;
        scroll-margin-bottom: 5rem;
    }

    .publication-detail-modal h4 {
        gap: 0.5rem;
        margin-bottom: 0.7rem !important;
        color: var(--report-ink) !important;
        font-size: 0.875rem !important;
        font-weight: 700 !important;
    }

    .publication-detail-modal h5 {
        color: #526278 !important;
        font-size: 0.7rem !important;
        font-weight: 600 !important;
        letter-spacing: 0;
        text-transform: none;
    }

    .publication-detail-modal div.border-\[\#404041\] {
        border-color: #e2e8f0 !important;
        border-radius: 7px !important;
        background: #f8fafc !important;
    }

    .publication-detail-modal div.border-\[\#404041\]:hover {
        border-color: #d8dee8 !important;
        background: #f8fafc !important;
    }

    .publication-detail-modal .datos-generales-grid > div {
        min-height: 104px;
    }

    .publication-detail-modal .datos-generales-grid .dato-general-value,
    .publication-detail-modal div.border-\[\#404041\] .text-lg,
    .publication-detail-modal div.border-\[\#404041\] .text-xl,
    .publication-detail-modal div.border-\[\#404041\] .text-2xl {
        color: var(--report-ink) !important;
        font-size: 0.875rem !important;
        font-weight: 600 !important;
        line-height: 1.35;
    }

    .publication-detail-modal div.border-\[\#404041\] i {
        color: #64748b !important;
        font-size: 0.85rem !important;
    }

    .publication-detail-modal div.border-\[\#404041\] p.text-xs {
        color: #64748b !important;
        font-size: 0.7rem !important;
        line-height: 1.4;
    }

    .publication-detail-modal .modal-actions-footer {
        min-height: 56px !important;
        padding: 0.65rem 1.25rem !important;
        background: #ffffff !important;
        border-color: #e5e7eb !important;
    }

    .publication-detail-modal .approval-buttons-container {
        gap: 0.5rem !important;
    }

    .publication-detail-modal .approval-buttons-container button {
        min-width: 6.25rem !important;
        min-height: 34px !important;
        height: 34px;
        padding: 0 0.82rem !important;
        border-radius: 7px !important;
        font-size: 0.78rem !important;
        font-weight: 600 !important;
    }

    .publication-detail-modal .modal-cerrar {
        width: 2rem !important;
        height: 2rem !important;
        border-radius: 6px !important;
        color: #718096 !important;
    }

    .publication-detail-modal .modal-cerrar:hover {
        border-color: transparent !important;
        background: #f3f6f9 !important;
        color: var(--report-ink) !important;
    }

    .publication-detail-modal .modal-cerrar:focus-visible,
    .publication-detail-modal button:focus-visible {
        outline: 2px solid var(--report-focus);
        outline-offset: 2px;
    }

    @media (max-width: 767px) {
        .report-modal-overlay {
            padding: 0.75rem !important;
        }

        .publication-detail-modal {
            max-height: calc(100dvh - 1.5rem) !important;
        }

        .publication-detail-modal .report-heading-meta {
            align-items: flex-start;
            flex-direction: column;
            gap: 0.25rem;
        }

        .publication-detail-modal .report-meta-grid {
            align-items: flex-start;
            flex-direction: column;
        }

        .publication-detail-modal .report-meta-item + .report-meta-item::before {
            display: none;
        }

        .publication-detail-modal .report-primary-pane .datos-generales-grid {
            grid-template-columns: minmax(0, 1fr) !important;
        }

        .publication-detail-modal .report-secondary-pane .modal-archivos {
            grid-template-columns: repeat(auto-fill, minmax(150px, 1fr)) !important;
        }
    }

    @media (max-width: 1023px) {
        .publication-detail-modal .report-workspace {
            grid-template-columns: minmax(0, 1fr);
        }

        .publication-detail-modal .report-secondary-pane {
            margin-top: 1.25rem;
            padding-top: 1.25rem;
            padding-left: 0;
            border-top: 1px solid #dbe3ec;
            border-left: 0;
        }

        .publication-detail-modal .report-comments-pane {
            grid-column: auto;
        }
    }

    @media (prefers-reduced-motion: reduce) {
        .report-modal-overlay,
        .publication-detail-modal,
        .comentarios-container {
            transition-duration: 1ms !important;
        }
    }

</style>
@endonce

<div id="{{ $modalId }}" class="report-modal-overlay fixed inset-0 flex items-center justify-center z-[999999] hidden transition-opacity duration-200 p-3 md:p-5">
    <div class="publication-detail-modal relative bg-white max-w-5xl w-full max-h-[94vh] overflow-hidden transform transition-all duration-300 translate-y-3 scale-[0.985] opacity-0 border border-gray-200 ring-1 ring-black/5 flex flex-col">

        <!-- HEADER -->
        <div class="report-header px-5 md:px-7 py-5 min-h-[88px] flex-shrink-0 flex items-center">
            <div class="flex justify-between items-center gap-4 w-full">
                <div class="flex-1 overflow-hidden">
                    <div class="report-heading-meta">
                        <span class="report-type-label" data-report-type="{{ $tipo }}">{{ $tipoLabel }}</span>
                        <span class="report-kicker font-lora">Expediente de reporte</span>
                    </div>
                    <h2 class="text-xl md:text-2xl font-lora font-bold text-[#202428] modal-titulo" title="{{ $titulo }}">{{ $titulo }}</h2>

                </div>
                <button class="modal-cerrar flex-shrink-0 text-gray-500 hover:text-gray-700 transition-colors duration-200 w-9 h-9 flex items-center justify-center" aria-label="Cerrar modal">
                    <i class="fas fa-times text-base"></i>
                </button>
            </div>
        </div>

        <!-- INFORMACIÓN ADICIONAL -->
        <div class="report-meta-bar px-5 md:px-7 py-3 flex-shrink-0">
            <div class="report-meta-grid">
                <div class="report-meta-item text-left">
                    <span class="report-meta-label font-lora">Autor</span>
                    <span class="report-meta-value font-lora modal-usuario">Usuario</span>
                </div>
                <div class="report-meta-item modal-publication-meta">
                    <span class="report-meta-label font-lora">Publicado</span>
                    <span class="report-meta-value font-lora modal-fecha-publicacion">Fecha</span>
                </div>
                <div class="report-meta-item modal-updated-meta hidden">
                    <span class="report-meta-label font-lora">Actualizado</span>
                    <span class="report-meta-value font-lora modal-actualizado"></span>
                </div>
            </div>
        </div>

        <!-- CONTENIDO -->
        <div class="flex flex-col min-h-0 flex-1">
            <div class="report-body p-5 md:p-7 overflow-y-auto flex-1">
                <div class="report-workspace">
                    <section class="report-primary-pane">
                <div class="hidden">
                    <h4 class="font-semibold text-[#404041] mb-3 text-lg font-lora">Datos del reporte</h4>
                    <div class="bg-white rounded-lg p-4 border border-[#404041]">
                        <div class="flex items-center gap-3">
                            <i class="fas fa-calendar-alt text-[#404041] text-xl"></i>
                            <div>
                                <h5 class="font-semibold text-[#404041] font-lora">Fecha de la actividad</h5>
                            <p class="text-sm font-semibold text-gray-700 font-lora modal-fecha-actividad">sábado, 14 de octubre de 2023</p>
                        </div>
                        </div>
                    </div>
                </div>
                
                <!-- SECCIÓN ESPECÍFICA DEL TIPO DE REPORTE -->
                {{ $slot }}
                
                <!-- LÍNEA SEPARADORA -->
                <div class="h-px bg-gray-200 mb-6 descripcion-separator"></div>

                <!-- DESCRIPCIÓN -->
                <div class="mb-6 descripcion-section">
                    <h4 class="font-semibold text-[#404041] mb-3 text-lg font-lora">Descripción</h4>
                    <div class="bg-white rounded-lg border border-[#404041] p-4">
                        <p class="text-gray-700 leading-relaxed font-lora w-full text-left modal-descripcion break-words whitespace-normal">
                            Descripción del reporte...
                        </p>
                    </div>
                </div>

                <!-- LÍNEA SEPARADORA -->
                <div class="h-px bg-gray-200 mb-6"></div>

                <!-- ESTADO DEL REPORTE (visible para todos) -->
                    </section>
                    <aside class="report-secondary-pane">
                        <section class="report-pane-section">
                <div class="mb-6">
                    <h4 class="font-semibold text-[#404041] mb-3 text-lg font-lora">Estado del reporte</h4>
                    <div class="modal-status-container">
                        <!-- Se llenara dinamicamente con JavaScript -->
                        <div class="status-card status-pending rounded-lg border border-[#404041] p-3 bg-white">
                            <div class="status-main">
                                <span class="status-icon bg-yellow-50 text-yellow-700">
                                    <i class="fas fa-clock text-sm"></i>
                                </span>
                                <div>
                                    <p class="text-sm font-semibold text-[#404041] font-lora leading-tight">Pendiente de revision</p>
                                    <p class="text-xs text-gray-500 font-lora leading-tight">Esperando validacion</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- LÍNEA SEPARADORA -->
                <div class="h-px bg-gray-200 mb-6"></div>

                <!-- ARCHIVOS ADJUNTOS -->
                        </section>
                        <section class="report-pane-section">
                <div class="mb-6">
                    <div class="flex justify-between items-center mb-4">
                        <h4 class="font-semibold text-[#404041] text-lg font-lora">Archivos Adjuntos</h4>
                        <button class="descargar-todos-archivos rounded-lg text-xs font-semibold transition-all duration-200 font-lora whitespace-nowrap">
                            <i class="fas fa-download text-xs"></i>
                            Descargar todo
                        </button>
                    </div>
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-3 modal-archivos">
                        <!-- Los archivos se insertarán dinámicamente aquí -->
                    </div>
                </div>

                <!-- LÍNEA SEPARADORA -->
                <div class="h-px bg-gray-200 mb-6"></div>

                <!-- COMENTARIOS - COLAPSABLE CON DISEÑO CLASSROOM -->
                        </section>
                    </aside>
                    <section class="report-comments-pane">
                <div class="comentarios-section comentarios-shell rounded-lg p-3">
                    
                    <!-- Botón para expandir/contraer comentarios -->
                    <button type="button" class="comentarios-toggle w-full text-left px-1 pb-3 transition-all duration-200 flex items-center justify-between" style="display: none;">  
                        <p class="font-semibold text-[#404041] font-lora text-sm leading-tight contador-comentarios">Comentarios (0)</p>
                        <i class="fas fa-chevron-down text-gray-500 transition-transform duration-300 icono-chevron text-xs" style="transform: rotate(0deg);"></i>
                    </button>
                    
                    <!-- Contenedor colapsable de comentarios -->
                    <div class="comentarios-container">
                        
                        <!-- Lista de comentarios -->
                        <div class="space-y-2 mb-3 modal-comentarios">
                            <!-- Los comentarios se insertarán dinámicamente aquí -->
                        </div>

                        <!-- Separador -->
                        <div class="border-t border-gray-200 pt-3"></div>
                        
                        <!-- FORMULARIO PARA NUEVO COMENTARIO -->
                        <div class="comentario-form-container" style="display: none;">
                            <div class="flex gap-2 items-end w-full">
                                <textarea 
                                    class="nuevo-comentario min-w-0 flex-1 border border-gray-300 bg-white rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#611132]/25 focus:border-[#611132] resize-none min-h-[40px] max-h-[100px] font-lora placeholder-gray-400"
                                    rows="1"
                                    placeholder="Añade un comentario..."
                                ></textarea>
                                <button 
                                    class="enviar-comentario flex-shrink-0 bg-[#611132] text-white text-sm font-semibold rounded-lg hover:bg-[#4a0e26] transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed"
                                    disabled
                                    title="Enviar comentario"
                                >
                                    <i class="fas fa-paper-plane text-sm"></i>
                                    <span>Enviar</span>
                                </button>
                            </div>
                        </div>

                        <!-- Mensaje si no hay permisos -->
                        <div class="comentario-no-permisos" style="display: none;">
                            <p class="text-xs text-gray-600 text-center font-lora italic">Solo administradores y coordinadores pueden comentar.</p>
                        </div>
                    </div>
                </div>
                    </section>
                </div>
            </div>

            <!-- FOOTER -->
            <div class="modal-actions-footer bg-[#eef1f4] px-5 md:px-7 py-3 border-t border-gray-200 flex items-center min-h-[64px] flex-shrink-0">
                <!-- Botones de aprobación/rechazo (solo Admin y Coordinador) -->
                <div class="approval-buttons-container flex flex-wrap justify-end gap-3 w-full" style="display: none;">
                    <button class="rechazar-reporte min-w-[118px] justify-center border border-[#AB1A1A] text-[#AB1A1A] px-4 py-2 rounded-lg text-xs lg:text-sm font-semibold hover:bg-red-50 transition-all duration-200 font-lora whitespace-nowrap inline-flex items-center gap-2">
                        Rechazar
                    </button>
                    <button class="aprobar-reporte min-w-[118px] justify-center text-white px-4 py-2 rounded-lg text-xs lg:text-sm font-semibold transition-all duration-200 font-lora whitespace-nowrap inline-flex items-center gap-2">
                        Aprobar
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    // El titulo se limita por CSS a dos lineas para mantener estable el modal.
</script>
