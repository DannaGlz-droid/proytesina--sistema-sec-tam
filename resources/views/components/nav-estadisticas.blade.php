@php
    $statisticsDataActive = request()->routeIs(
        'statistic.data',
        'statistic.edit',
        'statistic.import-history-view',
        'statistic.failed-imports-view'
    );
    $statisticsChartsActive = request()->routeIs('estadisticas.graficas');
    $statisticsCreateActive = request()->routeIs('statistic.create');
@endphp

<nav class="app-subnav bg-nav text-white w-full font-sans" aria-label="Navegación de estadísticas">
    <div class="app-subnav-inner">
        <a href="{{ route('statistic.data') }}"
           @class(['app-subnav-link', 'is-active' => $statisticsDataActive])
           @if($statisticsDataActive) aria-current="page" @endif>
            <span>Datos</span>
            <span class="app-subnav-indicator" aria-hidden="true"></span>
        </a>

        <a href="{{ route('estadisticas.graficas') }}"
           @class(['app-subnav-link', 'is-active' => $statisticsChartsActive])
           @if($statisticsChartsActive) aria-current="page" @endif>
            <span>Estadísticas</span>
            <span class="app-subnav-indicator" aria-hidden="true"></span>
        </a>

        <a href="{{ route('statistic.create') }}"
           @class(['app-subnav-link', 'is-active' => $statisticsCreateActive])
           @if($statisticsCreateActive) aria-current="page" @endif>
            <span>Nuevo registro</span>
            <span class="app-subnav-indicator" aria-hidden="true"></span>
        </a>
    </div>
</nav>
