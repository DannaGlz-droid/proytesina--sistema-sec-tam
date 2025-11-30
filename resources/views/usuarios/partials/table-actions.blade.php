<div class="flex items-center justify-end space-x-1">
    <a href="{{ route('user.edit', $user->id) }}" class="w-7 h-7 flex items-center justify-center rounded border border-[#404041] text-[#404041] hover:bg-[#404041] hover:text-white transition-all duration-200" title="Editar" aria-label="Editar usuario {{ $user->id }}">
        <i class="fas fa-edit text-xs"></i>
    </a>
    <a href="{{ route('user.update-password', $user->id) }}" class="w-7 h-7 flex items-center justify-center rounded border border-[#C08400] text-[#C08400] hover:bg-[#C08400] hover:text-white transition-all duration-200" title="Cambiar Contraseña" aria-label="Cambiar contraseña usuario {{ $user->id }}">
        <i class="fas fa-key text-xs"></i>
    </a>
    <form method="POST" action="{{ route('user.destroy', $user->id) }}" onsubmit="return confirm('¿Eliminar usuario? Esta acción no se puede deshacer.');">
        @csrf
        @method('DELETE')
        <button type="submit" class="w-7 h-7 flex items-center justify-center rounded border border-[#AB1A1A] text-[#AB1A1A] hover:bg-[#AB1A1A] hover:text-white transition-all duration-200" title="Eliminar" aria-label="Eliminar usuario {{ $user->id }}">
            <i class="fas fa-trash text-xs"></i>
        </button>
    </form>
</div>
