@php
    $profileNavActive = request()->routeIs('usuario.*');
    $usersNavActive = request()->routeIs('user.*');
@endphp

<nav class="app-subnav bg-nav text-white w-full font-sans" aria-label="Navegación de usuarios">
    <div class="app-subnav-inner">
        <a href="{{ route('usuario.miperfil') }}"
           @class(['app-subnav-link', 'is-active' => $profileNavActive])
           @if($profileNavActive) aria-current="page" @endif>
            <span>Mi perfil</span>
            <span class="app-subnav-indicator" aria-hidden="true"></span>
        </a>

        @if(auth()->user() && auth()->user()->isAdmin())
            <a href="{{ route('user.user-gestion') }}"
               @class(['app-subnav-link', 'is-active' => $usersNavActive])
               @if($usersNavActive) aria-current="page" @endif>
                <span>Gestión de usuarios</span>
                <span class="app-subnav-indicator" aria-hidden="true"></span>
            </a>
        @endif
    </div>
</nav>
