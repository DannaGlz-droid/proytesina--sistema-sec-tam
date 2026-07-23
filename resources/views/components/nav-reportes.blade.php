@php
    $reportsIndexActive = request()->routeIs('reportes.index');
    $reportsRecordActive = request()->routeIs(
        'reportes.seguridad-vial',
        'reportes.observatorio-de-lesiones',
        'reportes.alcoholimetria',
        'reportes.grupos-vulnerables',
        'reportes.seguridad-vial.edit',
        'reportes.observatorio.edit',
        'reportes.alcoholimetria.edit',
        'reportes.grupos-vulnerables.edit'
    );
@endphp

<nav class="app-subnav bg-nav text-white w-full font-sans" aria-label="Navegación de reportes">
    <div class="app-subnav-inner">
        <a href="{{ route('reportes.index') }}"
           @class(['app-subnav-link', 'is-active' => $reportsIndexActive])
           @if($reportsIndexActive) aria-current="page" @endif>
            <span>Centro de control</span>
            <span class="app-subnav-indicator" aria-hidden="true"></span>
        </a>

        <div class="app-subnav-menu" x-data="{ open: false }" @mouseenter="open = true" @mouseleave="open = false" @keydown.escape.window="open = false">
            <button type="button"
                    @click="open = !open"
                    @class(['app-subnav-link', 'is-active' => $reportsRecordActive])
                    :aria-expanded="open.toString()"
                    aria-haspopup="true">
                <span>Nuevo registro</span>
                <i class="fa-solid fa-chevron-down app-subnav-chevron" :class="{ 'is-open': open }" aria-hidden="true"></i>
                <span class="app-subnav-indicator" aria-hidden="true"></span>
            </button>

            <div x-show="open"
                 x-transition:enter="transition ease-out duration-150"
                 x-transition:enter-start="opacity-0 -translate-y-1"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100 translate-y-0"
                 x-transition:leave-end="opacity-0 -translate-y-1"
                 @click.outside="open = false"
                 class="app-subnav-dropdown"
                 role="menu"
                 style="display: none;">
                <a href="{{ route('reportes.seguridad-vial') }}" role="menuitem">
                    <i class="fa-solid fa-car-side" aria-hidden="true"></i>
                    <span>Seguridad vial</span>
                </a>
                <a href="{{ route('reportes.observatorio-de-lesiones') }}" role="menuitem">
                    <i class="fa-solid fa-chart-column" aria-hidden="true"></i>
                    <span>Observatorio</span>
                </a>
                <a href="{{ route('reportes.alcoholimetria') }}" role="menuitem">
                    <i class="fa-solid fa-vial" aria-hidden="true"></i>
                    <span>Alcoholimetría</span>
                </a>
                <a href="{{ route('reportes.grupos-vulnerables') }}" role="menuitem">
                    <i class="fa-solid fa-users" aria-hidden="true"></i>
                    <span>Grupos vulnerables</span>
                </a>
            </div>
        </div>
    </div>
</nav>
