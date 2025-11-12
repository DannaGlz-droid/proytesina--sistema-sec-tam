@props([
    'tipo' => '',
    'titulo' => '',
    'fecha' => '',
    'usuario' => '',
    'usuario_full' => '',
    'descripcion' => '',
    // archivos: can be an array or JSON string; archivosCount is a simple count fallback
    'archivos' => null,
    'archivosCount' => 0,
    // badge classes (bg and left border) to allow different colors per tipo
    'badgeClass' => 'bg-[#4C8CC4] text-white',
    'badgeBorderClass' => 'border-[#13264F]',
])

<div {{ $attributes->merge(['class' => 'border border-[#404041] rounded-lg p-5 bg-white transition-all duration-300 hover:-translate-y-1 hover:shadow-lg group flex flex-col h-full relative publication-card']) }}>
    <div class="flex-grow">
        <div class="flex justify-between items-start mb-4">
            <div class="text-gray-600 text-sm font-medium font-lora">{{ $fecha }}</div>
            <div class="relative">
                <div class="relative w-5 h-5 flex items-center justify-center text-gray-500">
                    <i class="fas fa-comment-alt text-sm"></i>
                    <div class="absolute -top-0.5 -right-0.5 w-2 h-2 bg-red-500 rounded-full border border-white"></div>
                </div>
            </div>
        </div>

        <div class="inline-block {{ $badgeClass }} px-3 py-1 rounded-lg text-xs font-semibold font-lora mb-3 border-l-4 {{ $badgeBorderClass }}">
            {{ $tipo }}
        </div>

        <h3 class="text-lg font-semibold text-[#404041] mb-3 leading-tight font-lora">{{ $titulo }}</h3>

        <div class="min-h-[3rem]">
            @if(!empty($descripcion))
                <div class="flex items-center gap-2 text-gray-600 text-sm mb-2 font-lora">
                    <i class="fas fa-tasks text-[#404041] w-4"></i>
                    <span>{{ $descripcion }}</span>
                </div>
            @endif
            <div class="flex items-center gap-2 text-gray-600 text-sm font-lora">
                <i class="fas fa-user text-[#404041] w-4"></i>
                <div class="min-w-0">
                    <span class="block truncate" title="{{ $usuario_full ?: $usuario }}">Subido por: <span class="font-semibold">{{ $usuario }}</span></span>
                    {{-- cargo removed from card; shown in modal only --}}
                </div>
            </div>
        </div>
    </div>

    <div class="h-[1px] bg-gray-300 my-3"></div>

    {{-- Archivos adjuntos (opcional) --}}
    @php
        $files = null;
        if ($archivos) {
            if (is_string($archivos)) {
                // try decode
                $decoded = json_decode($archivos, true);
                $files = is_array($decoded) ? $decoded : null;
            } elseif (is_array($archivos)) {
                $files = $archivos;
            }
        }
        $count = $files ? count($files) : (int) $archivosCount;
    @endphp

    @if($count > 0)
        <div class="flex-none">
            <div class="bg-gray-50 p-4 rounded-lg border border-[#404041] cursor-pointer transition-all duration-300 hover:bg-gray-100 hover:translate-x-1">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 flex items-center justify-center rounded-lg bg-[#BC955C] text-white">
                        <i class="fas fa-copy text-sm"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="font-semibold text-[#404041] text-sm font-lora">Archivos adjuntos</div>
                        <div class="text-gray-500 text-xs font-lora mt-1">{{ $count }} {{ \Illuminate\Support\Str::plural('archivo', $count) }} adjunto{{ $count>1 ? 's' : '' }}</div>
                    </div>
                    <div class="text-gray-500 transition-all duration-300 group-hover:translate-x-1 group-hover:text-[#404041]">
                        <i class="fas fa-chevron-right text-sm"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="h-[1px] bg-gray-300 my-3"></div>
    @endif

    <div class="flex-none">
        {{-- Slot para botones/acciones específicas (ver, editar, eliminar) --}}
        {{ $slot }}
    </div>
</div>
