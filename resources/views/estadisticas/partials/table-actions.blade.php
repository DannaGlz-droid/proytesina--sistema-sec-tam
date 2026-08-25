<div class="users-row-actions">
    <button type="button" class="users-row-menu-button" title="Acciones" aria-label="Abrir acciones de la defunción {{ $death->id }}" aria-expanded="false">
        <i class="fas fa-ellipsis-v" aria-hidden="true"></i>
    </button>
    <div class="users-row-menu hidden" role="menu">
        <a href="{{ route('statistic.edit', $death->id) }}" class="users-row-menu-item" role="menuitem">
            <i class="fas fa-edit users-row-menu-icon" aria-hidden="true"></i>
            <span>Editar</span>
        </a>
        <form method="POST"
              action="{{ route('statistic.destroy', $death->id) }}"
              data-confirm-delete-form
              data-confirm-title="Eliminar registro"
              data-confirm-subject="el registro {{ $death->id }}"
              data-confirm-description="El registro dejará de estar disponible de forma permanente.">
            @csrf
            @method('DELETE')
            <button type="submit" class="users-row-menu-item users-row-menu-item-danger" role="menuitem">
                <i class="fas fa-trash users-row-menu-icon" aria-hidden="true"></i>
                <span>Eliminar</span>
            </button>
        </form>
    </div>
</div>
