<div class="users-row-actions">
    <button type="button" class="users-row-menu-button" title="Acciones" aria-label="Abrir acciones del usuario {{ $user->id }}" aria-expanded="false">
        <i class="fas fa-ellipsis-v"></i>
    </button>
    <div class="users-row-menu hidden" role="menu">
        <a href="{{ route('user.edit', $user->id) }}" class="users-row-menu-item" role="menuitem">
            <span>Editar</span>
        </a>
        <a href="{{ route('user.update-password', $user->id) }}"
           class="users-row-menu-item js-change-password"
           role="menuitem"
           data-password-update-url="{{ route('user.update-password.update', $user->id) }}"
           data-user-name="{{ trim($user->name . ' ' . $user->first_last_name . ' ' . $user->second_last_name) ?: $user->username }}"
           data-username="{{ $user->username }}"
           data-user-photo="{{ $user->profile_photo_path ? asset('storage/' . $user->profile_photo_path) : asset('images/default_pfp.svg.png') }}">
            <span>Cambiar contraseña</span>
        </a>
        <form method="POST" action="{{ route('user.destroy', $user->id) }}" class="js-delete-user-form" data-user-name="{{ trim($user->name . ' ' . $user->first_last_name) ?: $user->username }}">
            @csrf
            @method('DELETE')
            <button type="submit" class="users-row-menu-item users-row-menu-item-muted" role="menuitem">
                <span>Eliminar</span>
            </button>
        </form>
    </div>
</div>
