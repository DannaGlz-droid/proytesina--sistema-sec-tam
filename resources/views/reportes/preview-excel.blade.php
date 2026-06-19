<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $fileName }}</title>
    <style>
        :root {
            --chrome: #2f2f2f;
            --chrome-soft: #3a3a3a;
            --grid: #d9d9d9;
            --header: #f3f4f6;
            --text: #1f2937;
        }

        * {
            box-sizing: border-box;
        }

        html,
        body {
            height: 100%;
        }

        body {
            margin: 0;
            background: #111827;
            color: var(--text);
            font-family: Arial, Helvetica, sans-serif;
            font-size: 12px;
            overflow: hidden;
        }

        .excel-shell {
            height: 100vh;
            display: flex;
            flex-direction: column;
            background: #ffffff;
        }

        .topbar {
            height: 44px;
            flex: 0 0 auto;
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 0 14px;
            background: var(--chrome);
            color: #f9fafb;
            border-bottom: 1px solid #202020;
        }

        .topbar-icon {
            color: #22c55e;
            font-size: 16px;
        }

        .file-name {
            min-width: 0;
            margin: 0;
            font-size: 14px;
            font-weight: 700;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .file-meta {
            margin-left: auto;
            color: #d1d5db;
            white-space: nowrap;
        }

        .grid-wrap {
            flex: 1 1 auto;
            overflow: auto;
            background: #ffffff;
            scrollbar-width: thin;
            scrollbar-color: #6b7280 transparent;
        }

        .sheet-content {
            flex: 1 1 auto;
            min-height: 0;
            display: flex;
            flex-direction: column;
        }

        .grid-wrap::-webkit-scrollbar,
        .tabs-bar::-webkit-scrollbar {
            width: 10px;
            height: 10px;
        }

        .grid-wrap::-webkit-scrollbar-track,
        .tabs-bar::-webkit-scrollbar-track {
            background: transparent;
        }

        .grid-wrap::-webkit-scrollbar-thumb,
        .tabs-bar::-webkit-scrollbar-thumb {
            background: rgba(75, 85, 99, 0.72);
            border: 3px solid transparent;
            border-radius: 999px;
            background-clip: content-box;
        }

        .grid-wrap::-webkit-scrollbar-thumb:hover,
        .tabs-bar::-webkit-scrollbar-thumb:hover {
            background: rgba(55, 65, 81, 0.9);
            border: 2px solid transparent;
            background-clip: content-box;
        }

        .grid-wrap::-webkit-scrollbar-corner {
            background: transparent;
        }

        table {
            border-collapse: separate;
            border-spacing: 0;
            min-width: 100%;
            background: #ffffff;
        }

        th,
        td {
            height: 26px;
            border-right: 1px solid var(--grid);
            border-bottom: 1px solid var(--grid);
            padding: 4px 7px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            vertical-align: middle;
        }

        td {
            min-width: 112px;
            max-width: 320px;
        }

        .corner,
        .column-header,
        .row-header {
            background: var(--chrome-soft);
            color: #d1d5db;
            font-weight: 600;
            text-align: center;
            border-color: #555;
            user-select: none;
        }

        .corner {
            position: sticky;
            left: 0;
            top: 0;
            z-index: 4;
            width: 54px;
            min-width: 54px;
        }

        .column-header {
            position: sticky;
            top: 0;
            z-index: 3;
            min-width: 112px;
        }

        .row-header {
            position: sticky;
            left: 0;
            z-index: 2;
            width: 54px;
            min-width: 54px;
        }

        tbody tr:first-child td {
            font-weight: 700;
            background: #fafafa;
        }

        tbody tr:hover td {
            background: #eef6ff;
        }

        tbody tr:hover .row-header {
            background: #dbeafe;
            color: #1d4ed8;
        }

        td.cell-selected {
            position: relative;
            background: #ffffff !important;
            outline: 2px solid #1a73e8;
            outline-offset: -2px;
            box-shadow: inset 0 0 0 1px #ffffff;
        }

        td.cell-selected::after {
            content: '';
            position: absolute;
            right: -3px;
            bottom: -3px;
            width: 7px;
            height: 7px;
            background: #1a73e8;
            border: 1px solid #ffffff;
        }

        td.cell-row-highlight,
        td.cell-column-highlight {
            background: #e8f0fe;
        }

        .row-header.header-selected,
        .column-header.header-selected {
            background: #1a73e8;
            color: #ffffff;
            border-color: #1a73e8;
        }

        .tabs-bar {
            flex: 0 0 auto;
            min-height: 46px;
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 7px 10px;
            background: #111111;
            border-top: 1px solid #333;
            overflow-x: auto;
        }

        .sheet-tab {
            display: inline-flex;
            align-items: center;
            height: 30px;
            padding: 0 13px;
            border-radius: 999px;
            color: #e5e7eb;
            background: #2d2d2d;
            text-decoration: none;
            font-weight: 700;
            white-space: nowrap;
        }

        .sheet-tab.active {
            background: #ffffff;
            color: #2f2f2f;
        }

        .limit-note {
            flex: 0 0 auto;
            margin-left: auto;
            color: #9ca3af;
            white-space: nowrap;
        }

        .empty,
        .error {
            height: calc(100vh - 90px);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 28px;
            color: #6b7280;
            text-align: center;
            background: #ffffff;
        }

        .error {
            color: #991b1b;
        }
    </style>
</head>
<body>
    @php
        $embedded = request()->boolean('embed');
    @endphp

    <div class="excel-shell">
        @unless($embedded)
            <div class="topbar">
                <span class="topbar-icon">▣</span>
                <p class="file-name">{{ $fileName }}</p>
                @if(!empty($sheetName))
                    <span class="file-meta">{{ $sheetName }}</span>
                @endif
            </div>
        @endunless

        <div id="sheet-content" class="sheet-content">
            @if(!empty($error))
                <div class="error">{{ $error }}</div>
            @elseif(empty($rows))
                <div class="empty">El archivo no contiene datos visibles para previsualizar.</div>
            @else
                <div class="grid-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th class="corner"></th>
                                @foreach($columnLabels as $columnLabel)
                                    <th class="column-header">{{ $columnLabel }}</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($rows as $rowNumber => $row)
                                <tr>
                                    <th class="row-header">{{ $rowNumber + 1 }}</th>
                                    @foreach($row as $cell)
                                    <td title="{{ $cell }}" data-row="{{ $rowNumber + 1 }}" data-column="{{ $loop->iteration }}">{{ $cell }}</td>
                                    @endforeach
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        <div id="sheet-tabs" class="tabs-bar">
            @forelse($sheetNames as $index => $name)
                <a
                    href="{{ request()->fullUrlWithQuery(['sheet' => $index]) }}"
                    class="sheet-tab {{ $index === $activeSheetIndex ? 'active' : '' }}"
                    title="{{ $name }}"
                >
                    {{ $name }}
                </a>
            @empty
                <span class="sheet-tab active">Hoja 1</span>
            @endforelse

            @if(!empty($rowLimitReached) || !empty($columnLimitReached))
                <span class="limit-note">Vista parcial: 120 filas y 40 columnas.</span>
            @endif
        </div>
    </div>

    <script>
        (function() {
            const cache = new Map();
            let activeController = null;
            let latestRequestId = 0;

            function getPartsFromDocument(doc) {
                return {
                    content: doc.querySelector('#sheet-content')?.innerHTML || '',
                    tabs: doc.querySelector('#sheet-tabs')?.innerHTML || '',
                };
            }

            async function fetchSheetParts(url, signal) {
                const response = await fetch(url, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' },
                    credentials: 'same-origin',
                    signal,
                });

                if (!response.ok) {
                    throw new Error('No se pudo cargar la hoja seleccionada');
                }

                const html = await response.text();
                const doc = new DOMParser().parseFromString(html, 'text/html');
                return getPartsFromDocument(doc);
            }

            function abortableDelay(ms, signal) {
                return new Promise((resolve, reject) => {
                    const timeout = window.setTimeout(resolve, ms);

                    signal.addEventListener('abort', () => {
                        window.clearTimeout(timeout);
                        reject(new DOMException('Aborted', 'AbortError'));
                    }, { once: true });
                });
            }

            cache.set(window.location.href, {
                content: document.querySelector('#sheet-content')?.innerHTML || '',
                tabs: document.querySelector('#sheet-tabs')?.innerHTML || '',
            });

            window.setTimeout(async function preloadSheets() {
                const urls = Array.from(document.querySelectorAll('.sheet-tab[href]'))
                    .map((tab) => tab.href)
                    .filter((url) => url !== window.location.href && !cache.has(url));

                for (const url of urls) {
                    try {
                        if (!cache.has(url)) {
                            cache.set(url, await fetchSheetParts(url));
                        }
                    } catch (error) {
                        console.warn('No se pudo precargar hoja:', error);
                    }
                }
            }, 500);

            document.addEventListener('click', async function(event) {
                const tab = event.target.closest('.sheet-tab[href]');
                if (!tab) return;

                event.preventDefault();

                const url = tab.href;
                const content = document.querySelector('#sheet-content');
                const tabs = document.querySelector('#sheet-tabs');

                if (!content || !tabs) {
                    window.location.href = url;
                    return;
                }

                try {
                    const requestId = ++latestRequestId;
                    if (activeController) {
                        activeController.abort();
                    }

                    let parts = cache.get(url);

                    if (!parts) {
                        activeController = new AbortController();
                        await abortableDelay(120, activeController.signal);
                        parts = await fetchSheetParts(url, activeController.signal);
                        if (requestId !== latestRequestId) return;

                        cache.set(url, parts);
                    }

                    if (requestId !== latestRequestId) return;

                    content.innerHTML = parts.content;
                    tabs.innerHTML = parts.tabs;
                    window.history.replaceState(null, '', url);
                } catch (error) {
                    if (error.name === 'AbortError') return;

                    console.error('Error cargando hoja:', error);
                    window.location.href = url;
                } finally {
                    activeController = null;
                }
            });

            document.addEventListener('click', function(event) {
                const cell = event.target.closest('.grid-wrap td');
                if (!cell) return;

                const grid = cell.closest('.grid-wrap');
                const row = cell.dataset.row;
                const column = cell.dataset.column;

                grid.querySelectorAll('.cell-selected, .cell-row-highlight, .cell-column-highlight')
                    .forEach((item) => item.classList.remove('cell-selected', 'cell-row-highlight', 'cell-column-highlight'));

                grid.querySelectorAll('.header-selected')
                    .forEach((item) => item.classList.remove('header-selected'));

                grid.querySelectorAll(`td[data-row="${row}"]`)
                    .forEach((item) => item.classList.add('cell-row-highlight'));

                grid.querySelectorAll(`td[data-column="${column}"]`)
                    .forEach((item) => item.classList.add('cell-column-highlight'));

                cell.classList.add('cell-selected');

                const rowHeader = cell.parentElement.querySelector('.row-header');
                const columnHeader = grid.querySelector(`thead .column-header:nth-child(${Number(column) + 1})`);

                if (rowHeader) rowHeader.classList.add('header-selected');
                if (columnHeader) columnHeader.classList.add('header-selected');
            });

            document.addEventListener('keydown', function(event) {
                if (event.key !== 'Escape') return;

                if (window.parent && window.parent !== window) {
                    event.preventDefault();
                    window.parent.postMessage({ type: 'close-file-preview' }, window.location.origin);
                }
            });
        })();
    </script>
</body>
</html>
