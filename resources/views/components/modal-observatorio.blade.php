@php
    $config = [
        'tipo' => 'observatorio de lesiones',
        'titulo' => 'Reporte del Observatorio', 
        'colorBadge' => 'bg-[#75A84E]',
        'colorBorder' => 'border-[#184823]',
        'modalId' => 'modalObservatorio'
    ];
@endphp

<x-modal-reporte-base 
    :tipo="$config['tipo']" 
    :titulo="$config['titulo']" 
    :colorBadge="$config['colorBadge']" 
    :colorBorder="$config['colorBorder']"
    :modalId="$config['modalId']">
    
    <!-- SECCIÓN ESPECÍFICA DEL OBSERVATORIO -->
    
    <!-- INFORMACIÓN GEOGRÁFICA -->
    <div class="mb-6">
        <h4 class="font-semibold text-[#404041] mb-4 text-lg font-lora">Información Geográfica</h4>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
            <div class="bg-white rounded-lg p-4 border border-[#404041]">
                <div class="flex items-center gap-3 mb-3">
                    <i class="fas fa-city text-[#404041] text-xl"></i>
                    <h5 class="font-semibold text-[#404041] font-lora">Municipio</h5>
                </div>
                <div class="text-lg font-bold text-[#404041] font-lora modal-municipio">Municipio Centro</div>
                <p class="text-xs text-gray-600 font-lora mt-1">Área de cobertura del estudio</p>
            </div>
            
            <div class="bg-white rounded-lg p-4 border border-[#404041]">
                <div class="flex items-center gap-3 mb-3">
                    <i class="fas fa-map text-[#404041] text-xl"></i>
                    <h5 class="font-semibold text-[#404041] font-lora">Jurisdicción</h5>
                </div>
                <div class="text-lg font-bold text-[#404041] font-lora modal-jurisdiccion">Jurisdicción Sanitaria III</div>
                <p class="text-xs text-gray-600 font-lora mt-1">Zona administrativa</p>
            </div>
        </div>
    </div>
</x-modal-reporte-base>