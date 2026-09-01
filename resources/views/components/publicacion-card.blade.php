@props([
    'tipo' => '',
    'titulo' => '',
    'folio' => '',
    'fecha' => '',
    'fecha_full' => '',
    'actualizado' => '',
    'actualizado_full' => '',
    'usuario' => '',
    'usuario_full' => '',
    'descripcion' => '',
    'status' => 'pendiente',
    'approvedBy' => null,
    'rejectedBy' => null,
    'rejectionReason' => null,
    'archivos' => null,
    'archivosCount' => 0,
    'hasComments' => false,
    'hasUnread' => false,
    'commentsCount' => 0,
    'unreadCommentsCount' => 0,
])

@php
    $commentsCount = (int) $commentsCount;
    $unreadCommentsCount = max(0, (int) $unreadCommentsCount);
    $files = is_string($archivos) ? json_decode($archivos, true) : $archivos;
    $filesCount = is_array($files) ? count($files) : (int) $archivosCount;
    $fileTypeLabels = [
        'pdf' => 'PDF',
        'xls' => 'Excel', 'xlsx' => 'Excel', 'csv' => 'Excel',
        'doc' => 'Word', 'docx' => 'Word',
        'ppt' => 'PowerPoint', 'pptx' => 'PowerPoint',
        'jpg' => 'imágenes', 'jpeg' => 'imágenes', 'png' => 'imágenes', 'gif' => 'imágenes', 'webp' => 'imágenes',
        'zip' => 'comprimidos', 'rar' => 'comprimidos', '7z' => 'comprimidos',
    ];
    $fileTypeCounts = [];

    foreach (is_array($files) ? $files : [] as $file) {
        $fileName = is_array($file) ? ($file['name'] ?? '') : (string) $file;
        $extension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        $typeLabel = $fileTypeLabels[$extension] ?? 'otros';
        $fileTypeCounts[$typeLabel] = ($fileTypeCounts[$typeLabel] ?? 0) + 1;
    }

    arsort($fileTypeCounts);
    $fileTypeParts = [];
    $remainingTypesCount = 0;

    foreach ($fileTypeCounts as $typeLabel => $count) {
        if (count($fileTypeParts) < 3) {
            $fileTypeParts[] = "{$count} {$typeLabel}";
        } else {
            $remainingTypesCount += $count;
        }
    }

    if ($remainingTypesCount > 0) {
        $fileTypeParts[] = "{$remainingTypesCount} más";
    }

    $filesDetail = $filesCount === 0
        ? 'Sin archivos'
        : "{$filesCount} " . \Illuminate\Support\Str::plural('archivo', $filesCount)
            . (!empty($fileTypeParts) ? ': ' . implode(' · ', $fileTypeParts) : '');
    $statusLabel = match ($status) {
        'aprobado' => 'Aprobado',
        'rechazado' => 'Rechazado',
        default => 'Pendiente',
    };
    $statusDetail = match ($status) {
        'aprobado' => $approvedBy ? "Aprobado por {$approvedBy}" : 'Reporte aprobado',
        'rechazado' => $rejectedBy ? "Rechazado por {$rejectedBy}" : 'Reporte rechazado',
        default => $tipo === 'Alcoholimetría' ? 'Pendiente de revisión' : 'Pendiente de aprobación',
    };
    $commentsLabel = $commentsCount === 1 ? '1 comentario' : "{$commentsCount} comentarios";
    $commentsDetail = match (true) {
        $commentsCount === 0 => 'Sin comentarios',
        $unreadCommentsCount === 0 => "{$commentsLabel} · sin comentarios nuevos",
        default => "{$commentsLabel} · {$unreadCommentsCount} "
            . \Illuminate\Support\Str::plural('nuevo', $unreadCommentsCount),
    };
    $location = preg_replace('/^Distrito:\s*/u', '', $descripcion);
    $locationParts = preg_split('/\s+-\s+/u', $location, 2);
    $locationDisplay = count($locationParts) === 2
        ? $locationParts[0] . ' · ' . \Illuminate\Support\Str::title(\Illuminate\Support\Str::lower($locationParts[1]))
        : $location;
@endphp

<article {{ $attributes->class(['publication-card-wrapper', 'publication-card']) }}>
    <header class="reports-card-cover">
        <div class="reports-card-cover-identity">
            <span class="reports-type-badge">{{ $tipo }}</span>
        </div>

        <div class="reports-card-header-actions">
            <label class="reports-checkbox-hitbox">
                <input type="checkbox"
                       class="publication-check-btn"
                       data-publication-id="{{ $attributes['data-publication-id'] ?? '' }}"
                       aria-label="Seleccionar el reporte {{ $titulo }}">
            </label>
            <div class="reports-actions-cell">
                {{ $slot }}
            </div>
        </div>
    </header>

    <div class="reports-card-content">
        <div class="reports-card-identity">
            <button type="button" class="reports-card-main archivos-open" title="Ver detalles de {{ $titulo }}">
                <span class="reports-topic-title">{{ $titulo }}</span>
            </button>
        </div>

        <div class="reports-card-context">
            <div class="reports-card-author-row">
                <span class="reports-card-author"
                      title="Autor: {{ $usuario_full ?: $usuario }}"
                      aria-label="Autor: {{ $usuario_full ?: $usuario }}">
                    <span class="reports-card-author-label">Autor</span>
                    <span class="reports-card-author-name">{{ $usuario }}</span>
                </span>
                @if($descripcion)
                    <span class="reports-card-meta-separator" aria-hidden="true">&middot;</span>
                    <span class="reports-card-location"
                          title="Distrito: {{ $location }}"
                          aria-label="Distrito: {{ $location }}">{{ $locationDisplay }}</span>
                @endif
            </div>

            <div class="reports-card-date-row">
                <span class="reports-card-date"
                      title="Fecha del reporte: {{ $fecha_full ?: $fecha }}"
                      aria-label="Fecha del reporte: {{ $fecha_full ?: $fecha }}">{{ $fecha }}</span>
                @if($actualizado)
                    <span class="reports-card-meta-separator" aria-hidden="true">&middot;</span>
                    <span class="reports-card-updated"
                          title="Última actualización: {{ $actualizado_full ?: $actualizado }}"
                          aria-label="Última actualización: {{ $actualizado_full ?: $actualizado }}">mod. {{ $actualizado }}</span>
                @endif
            </div>
        </div>

        @if($folio)
            <p class="reports-card-folio" title="Folio del reporte: {{ $folio }}">{{ $folio }}</p>
        @endif
    </div>

    <footer class="reports-card-footer">
        <span class="reports-status reports-status--{{ $status }}" title="{{ $statusDetail }}">
            <span class="reports-status-dot" aria-hidden="true"></span>
            <span>{{ $statusLabel }}</span>
        </span>

        <div class="reports-card-resources">
            <button type="button" class="reports-resource-button reports-resource-button--files archivos-open" title="{{ $filesDetail }}" aria-label="Abrir {{ $filesDetail }} de {{ $titulo }}" @disabled($filesCount === 0)>
                <i class="fas fa-paperclip" aria-hidden="true"></i>
                <span aria-hidden="true">{{ $filesCount }}</span>
            </button>
            <button type="button" class="reports-resource-button reports-resource-button--comments open-comments" title="{{ $commentsDetail }}" aria-label="{{ $commentsDetail }} en {{ $titulo }}">
                <i class="far fa-comment-alt" aria-hidden="true"></i>
                <span aria-hidden="true">{{ $commentsCount }}</span>
                @if($hasUnread)
                    <span class="reports-resource-new">Nuevo</span>
                @endif
            </button>
        </div>
    </footer>
</article>
