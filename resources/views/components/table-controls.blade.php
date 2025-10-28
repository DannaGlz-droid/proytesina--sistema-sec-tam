@props([
    'items',
    // URL or route where the GET filter form should submit. Defaults to the current URL.
    'action' => request()->url(),
    // Default sort value used when the 'sort' query param is missing
    'defaultSort' => 'registration_date_desc',
    'perPageOptions' => [10,20,50,100],
    'sortOptions' => null,
    'searchPlaceholder' => 'Buscar...'
])

@php
    // sensible defaults for sort options if not provided
    $sortOptions = $sortOptions ?? [
        'registration_date_desc' => 'Fecha alta: Recientes',
        'registration_date_asc' => 'Fecha alta: Antiguos',
        'username_asc' => 'Usuario: A–Z',
        'username_desc' => 'Usuario: Z–A',
        'name_asc' => 'Nombre: A–Z',
        'name_desc' => 'Nombre: Z–A',
    ];

    // guard: ensure we have a paginator-like object
    $isPaginator = isset($items) && method_exists($items, 'links');
    // Reorder sort options so the defaultSort (if present) appears first in the select list for better UX.
    $orderedSortOptions = [];
    if (is_array($sortOptions) && isset($sortOptions[$defaultSort])) {
        $orderedSortOptions[$defaultSort] = $sortOptions[$defaultSort];
        foreach ($sortOptions as $k => $v) {
            if ($k === $defaultSort) continue;
            $orderedSortOptions[$k] = $v;
        }
    } else {
        // fallback: preserve original order
        $orderedSortOptions = $sortOptions;
    }
@endphp

<div class="bg-white relative shadow-md sm:rounded-lg overflow-hidden border border-[#404041]">
    <div class="flex flex-col md:flex-row items-center justify-between space-y-3 md:space-y-0 p-4">
    <form method="GET" action="{{ $action }}" class="w-full flex items-center gap-3">
            <div class="flex-1 md:w-1/2 lg:w-2/3">
                <div class="relative w-full">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                        <i class="fas fa-search text-gray-400 text-sm"></i>
                    </div>
                    <input type="text" name="q" value="{{ request('q') }}" id="table-search-users"
                        aria-label="Buscar"
                        class="bg-gray-50 border border-[#404041] text-gray-900 text-sm rounded-lg focus:ring-[#611132] focus:border-[#611132] block w-full pl-10 pr-24 p-2.5 max-w-full"
                        placeholder="{{ $searchPlaceholder }}">

                    <div class="absolute inset-y-0 right-0 flex items-center pr-2 space-x-1">
                        <button type="submit" class="h-8 px-3 bg-[#611132] text-white rounded-lg text-xs font-semibold hover:bg-[#4a0e26] transition-all duration-150" title="Buscar">
                            <i class="fas fa-search text-xs"></i>
                        </button>

                        @if(request('q'))
                            <button type="button" onclick="clearSearch(this)" class="h-8 px-2 bg-white border border-[#404041] text-gray-700 rounded-lg text-xs hover:bg-gray-100" title="Limpiar búsqueda" aria-label="Limpiar búsqueda">
                                <i class="fas fa-times text-xs"></i>
                            </button>
                        @endif
                    </div>
                </div>
            </div>

            <div class="ml-auto flex items-center space-x-3">
                <div class="flex items-center space-x-2">
                    <span class="text-sm text-gray-700 font-lora">Mostrar</span>
                    <select name="per_page" onchange="this.form.submit()" class="bg-gray-50 border border-[#404041] text-gray-900 text-sm rounded-lg focus:ring-[#611132] focus:border-[#611132] block w-24 p-2">
                        @foreach($perPageOptions as $opt)
                            <option value="{{ $opt }}" {{ (int)request('per_page', 10) === (int)$opt ? 'selected' : '' }}>{{ $opt }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="flex items-center space-x-2">
                    <span class="text-sm text-gray-700 font-lora">Ordenar</span>
                    <select name="sort" onchange="this.form.submit()" class="bg-white border border-[#404041] rounded-lg text-sm p-2 min-w-[180px] focus:outline-none focus:ring-1 focus:ring-[#611132] focus:border-[#611132]">
                        @foreach($orderedSortOptions as $key => $label)
                            <option value="{{ $key }}" {{ request('sort', $defaultSort) === $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                
            </div>
        </form>
    </div>

    {{-- slot: expected to contain the table markup (thead + tbody) --}}
    <div class="overflow-x-auto">
        {{ $slot }}
    </div>

    {{-- pagination --}}
    @if($isPaginator)
        <nav class="flex flex-col md:flex-row justify-between items-start md:items-center space-y-3 md:space-y-0 p-4 border-t border-[#404041]">
            <span class="text-sm font-normal text-gray-500 font-lora">
                Mostrando
                <span class="font-semibold text-gray-900">{{ $items->firstItem() ?? 0 }}-{{ $items->lastItem() ?? 0 }}</span>
                de
                <span class="font-semibold text-gray-900">{{ $items->total() }}</span>
                entradas
            </span>

            <div>
                @php
                    $lastPage = $items->lastPage();
                    $current = $items->currentPage();
                @endphp

                <ul class="inline-flex items-stretch -space-x-px">
                    <li>
                        @if($items->onFirstPage())
                            <span aria-disabled="true" class="flex items-center justify-center h-full py-1.5 px-3 ml-0 text-gray-500 bg-white rounded-l-lg border border-gray-300 cursor-default">
                                <i class="fas fa-chevron-left text-xs" aria-hidden="true"></i>
                            </span>
                        @else
                            <a href="{{ $items->previousPageUrl() }}" aria-label="Página anterior" class="flex items-center justify-center h-full py-1.5 px-3 ml-0 text-gray-500 bg-white rounded-l-lg border border-gray-300 hover:bg-gray-100 hover:text-gray-700">
                                <i class="fas fa-chevron-left text-xs" aria-hidden="true"></i>
                            </a>
                        @endif
                    </li>

                    @php $maxButtons = 5; @endphp

                    @if($lastPage <= $maxButtons)
                        @for ($i = 1; $i <= $lastPage; $i++)
                            @php $isActive = $i === $current; @endphp
                            <li>
                                @if($isActive)
                                    <a href="{{ $items->url($i) }}" aria-current="page" aria-label="Página {{ $i }}" class="flex items-center justify-center text-sm py-2 px-3 leading-tight text-[#611132] bg-[#f8f1f4] border border-[#611132]">{{ $i }}</a>
                                @else
                                    <a href="{{ $items->url($i) }}" aria-label="Ir a la página {{ $i }}" class="flex items-center justify-center text-sm py-2 px-3 leading-tight text-gray-500 bg-white border border-gray-300 hover:bg-gray-100 hover:text-gray-700">{{ $i }}</a>
                                @endif
                            </li>
                        @endfor
                    @else
                        @if($current <= 3)
                            @for($i = 1; $i <= 5; $i++)
                                @php $isActive = $i === $current; @endphp
                                <li>
                                    @if($isActive)
                                        <a href="{{ $items->url($i) }}" class="flex items-center justify-center text-sm py-2 px-3 leading-tight text-[#611132] bg-[#f8f1f4] border border-[#611132]">{{ $i }}</a>
                                    @else
                                        <a href="{{ $items->url($i) }}" class="flex items-center justify-center text-sm py-2 px-3 leading-tight text-gray-500 bg-white border border-gray-300 hover:bg-gray-100 hover:text-gray-700">{{ $i }}</a>
                                    @endif
                                </li>
                            @endfor
                            <li><span class="flex items-center justify-center text-sm py-2 px-3 leading-tight text-gray-500 bg-white border border-gray-300">&hellip;</span></li>
                            <li><a href="{{ $items->url($lastPage) }}" class="flex items-center justify-center text-sm py-2 px-3 leading-tight text-gray-500 bg-white border border-gray-300 hover:bg-gray-100 hover:text-gray-700">{{ $lastPage }}</a></li>

                        @elseif($current >= $lastPage - 2)
                            <li><a href="{{ $items->url(1) }}" class="flex items-center justify-center text-sm py-2 px-3 leading-tight text-gray-500 bg-white border border-gray-300 hover:bg-gray-100 hover:text-gray-700">1</a></li>
                            <li><span class="flex items-center justify-center text-sm py-2 px-3 leading-tight text-gray-500 bg-white border border-gray-300">&hellip;</span></li>
                            @for($i = $lastPage - 4; $i <= $lastPage; $i++)
                                @php $isActive = $i === $current; @endphp
                                <li>
                                    @if($isActive)
                                        <a href="{{ $items->url($i) }}" class="flex items-center justify-center text-sm py-2 px-3 leading-tight text-[#611132] bg-[#f8f1f4] border border-[#611132]">{{ $i }}</a>
                                    @else
                                        <a href="{{ $items->url($i) }}" class="flex items-center justify-center text-sm py-2 px-3 leading-tight text-gray-500 bg-white border border-gray-300 hover:bg-gray-100 hover:text-gray-700">{{ $i }}</a>
                                    @endif
                                </li>
                            @endfor

                        @else
                            <li><a href="{{ $items->url(1) }}" class="flex items-center justify-center text-sm py-2 px-3 leading-tight text-gray-500 bg-white border border-gray-300 hover:bg-gray-100 hover:text-gray-700">1</a></li>
                            <li><span class="flex items-center justify-center text-sm py-2 px-3 leading-tight text-gray-500 bg-white border border-gray-300">&hellip;</span></li>

                            @for($i = $current - 2; $i <= $current + 2; $i++)
                                @php $isActive = $i === $current; @endphp
                                <li>
                                    @if($isActive)
                                        <a href="{{ $items->url($i) }}" class="flex items-center justify-center text-sm py-2 px-3 leading-tight text-[#611132] bg-[#f8f1f4] border border-[#611132]">{{ $i }}</a>
                                    @else
                                        <a href="{{ $items->url($i) }}" class="flex items-center justify-center text-sm py-2 px-3 leading-tight text-gray-500 bg-white border border-gray-300 hover:bg-gray-100 hover:text-gray-700">{{ $i }}</a>
                                    @endif
                                </li>
                            @endfor

                            <li><span class="flex items-center justify-center text-sm py-2 px-3 leading-tight text-gray-500 bg-white border border-gray-300">&hellip;</span></li>
                            <li><a href="{{ $items->url($lastPage) }}" class="flex items-center justify-center text-sm py-2 px-3 leading-tight text-gray-500 bg-white border border-gray-300 hover:bg-gray-100 hover:text-gray-700">{{ $lastPage }}</a></li>
                        @endif
                    @endif

                    <li>
                        @if($items->hasMorePages())
                            <a href="{{ $items->nextPageUrl() }}" aria-label="Página siguiente" class="flex items-center justify-center h-full py-1.5 px-3 leading-tight text-gray-500 bg-white rounded-r-lg border border-gray-300 hover:bg-gray-100 hover:text-gray-700">
                                <i class="fas fa-chevron-right text-xs" aria-hidden="true"></i>
                            </a>
                        @else
                            <span aria-disabled="true" class="flex items-center justify-center h-full py-1.5 px-3 leading-tight text-gray-500 bg-white rounded-r-lg border border-gray-300 cursor-default">
                                <i class="fas fa-chevron-right text-xs" aria-hidden="true"></i>
                            </span>
                        @endif
                    </li>
                </ul>
            </div>
        </nav>
    @endif
</div>

<script>
    // Helper used by the clear-search button in the table-controls component.
    // Clears the text input named "q" in the same form and submits the form.
    function clearSearch(button) {
        try {
            var form = button && button.closest ? button.closest('form') : null;
            if (!form) return;
            var q = form.querySelector('input[name="q"]');
            if (q) q.value = '';
            // Submit the form to refresh the listing with other params (per_page/sort) preserved
            form.submit();
        } catch (e) {
            console && console.error && console.error('clearSearch error', e);
        }
    }

    
</script>
