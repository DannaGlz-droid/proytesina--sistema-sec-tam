@props(['tipo', 'titulo', 'colorBadge' => null, 'colorBorder' => null, 'modalId'])

@php
    $tipoLabel = match ($tipo) {
        'seguridad_vial' => 'Seguridad Vial',
        'alcoholimetria' => 'Alcoholimetría',
        'observatorio de lesiones' => 'Observatorio de lesiones',
        'grupos_vulnerables' => 'Grupos Vulnerables',
        default => ucwords(str_replace('_', ' ', $tipo)),
    };
@endphp

<div id="{{ $modalId }}" class="report-modal-overlay fixed inset-0 z-[999999] hidden" aria-hidden="true">
    <article class="publication-detail-modal" role="dialog" aria-modal="true" aria-labelledby="{{ $modalId }}-title" data-report-type="{{ $tipo }}">
        <header class="report-header">
            <div class="report-category-line" aria-hidden="true"></div>
            <div class="report-heading-copy">
                <div class="report-context-line">
                    <span class="report-type-label" data-report-type="{{ $tipo }}">{{ $tipoLabel }}</span>
                    <span class="report-context-separator" aria-hidden="true">/</span>
                    <span class="report-kicker">Expediente de reporte</span>
                </div>
                <h2 id="{{ $modalId }}-title" class="modal-titulo" title="{{ $titulo }}">{{ $titulo }}</h2>
                <div class="report-meta-grid">
                    <span class="report-meta-item report-folio-meta hidden"><span class="modal-folio"></span></span>
                    <span class="report-meta-item">Autor <strong class="modal-usuario">Usuario</strong></span>
                    <span class="report-meta-item">Publicado <strong class="modal-fecha-publicacion">Fecha</strong></span>
                    <span class="report-meta-item modal-updated-meta hidden">Editado <strong class="modal-actualizado"></strong></span>
                </div>
            </div>
            <button type="button" class="modal-cerrar" aria-label="Cerrar expediente"><i class="fas fa-times" aria-hidden="true"></i></button>
        </header>

        <div class="report-modal-main">
            <section class="report-status-section" aria-label="Estado del reporte">
                <div class="modal-status-container">
                    <div class="status-card status-pending"><span class="status-stamp">Pendiente</span><div><strong>Pendiente de revisión</strong><span>Esperando validación</span></div></div>
                </div>
            </section>

            <div class="report-workspace">
                <main class="report-primary-pane">
                    {{ $slot }}
                    <section class="report-section descripcion-section">
                        <h3 class="report-section-title"><span>Descripción</span></h3>
                        <p class="modal-descripcion report-description">Descripción del reporte...</p>
                    </section>
                </main>
                <aside class="report-secondary-pane" aria-label="Archivos adjuntos">
                    <section class="report-section report-attachments-section">
                        <div class="report-section-title report-section-title--actions">
                            <span>Archivos adjuntos · <strong class="modal-archivos-count">0</strong></span>
                            <button type="button" class="descargar-todos-archivos"><i class="fas fa-download" aria-hidden="true"></i><span>Descargar todo</span></button>
                        </div>
                        <div class="modal-archivos"></div>
                    </section>
                </aside>
                <section class="report-comments-pane" aria-label="Comentarios">
                    <div class="comentarios-section comentarios-shell">
                        <button type="button" class="comentarios-toggle" style="display:none" aria-expanded="false">
                            <span class="contador-comentarios">Comentarios (0)</span><i class="fas fa-chevron-down icono-chevron" aria-hidden="true"></i>
                        </button>
                        <div class="comentarios-container">
                            <div class="modal-comentarios"></div>
                            <div class="comentario-form-container" style="display:none">
                                <div class="report-comment-form">
                                    <textarea class="nuevo-comentario" rows="1" placeholder="Añade un comentario..." aria-label="Añade un comentario"></textarea>
                                    <button type="button" class="enviar-comentario" disabled title="Enviar comentario" aria-label="Enviar comentario"><i class="fas fa-paper-plane" aria-hidden="true"></i><span>Enviar</span></button>
                                </div>
                            </div>
                            <div class="comentario-no-permisos" style="display:none"><p>Solo administradores y coordinadores pueden comentar.</p></div>
                        </div>
                    </div>
                </section>
            </div>
        </div>

        <footer class="modal-actions-footer">
            <p class="modal-footer-note" aria-live="polite"></p>
            <div class="approval-buttons-container" style="display:none"></div>
        </footer>
    </article>
</div>
