<div class="flex items-center justify-end space-x-1">
    <a href="{{ route('statistic.edit', $death->id) }}" class="w-7 h-7 flex items-center justify-center rounded border border-[#404041] text-[#404041] hover:bg-[#404041] hover:text-white transition-all duration-200" title="Editar" aria-label="Editar defunción {{ $death->id }}">
        <i class="fas fa-edit text-xs"></i>
    </a>
    <form method="POST" action="{{ route('statistic.destroy', $death->id) }}" onsubmit="return confirm('¿Eliminar registro? Esta acción no se puede deshacer.');">
        @csrf
        @method('DELETE')
        <button type="submit" class="w-7 h-7 flex items-center justify-center rounded border border-[#AB1A1A] text-[#AB1A1A] hover:bg-[#AB1A1A] hover:text-white transition-all duration-200" title="Eliminar" aria-label="Eliminar registro {{ $death->id }}">
            <i class="fas fa-trash text-xs"></i>
        </button>
    </form>
</div>
