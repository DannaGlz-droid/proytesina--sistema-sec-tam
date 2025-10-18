@props(['titulo', 'icono', 'gridCols' => 'grid-cols-1 gap-6'])

<div class="chart-category mb-8">
    <div class="chart-category-header mb-4 pb-2 border-b border-gray-300">
        <h2 class="chart-category-title text-xl font-semibold text-[#404041] font-lora flex items-center gap-2">
            <i class="fas fa-{{ $icono }}"></i> {{ $titulo }}
        </h2>
    </div>
    <div class="charts-container grid {{ $gridCols }}">
        {{ $slot }}
    </div>
</div>