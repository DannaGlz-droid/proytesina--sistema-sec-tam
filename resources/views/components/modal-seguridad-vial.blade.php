@php
    $config = [
        'tipo' => 'seguridad_vial',
        'titulo' => 'Reporte de Seguridad Vial', 
        'colorBadge' => 'bg-[#4C8CC4]',
        'colorBorder' => 'border-[#13264F]',
        'modalId' => 'modalSeguridadVial'
    ];
@endphp

<x-modal-reporte-base 
    :tipo="$config['tipo']" 
    :titulo="$config['titulo']" 
    :colorBadge="$config['colorBadge']" 
    :colorBorder="$config['colorBorder']"
    :modalId="$config['modalId']">
    
    <!-- SECCIÓN ESPECÍFICA DE SEGURIDAD VIAL -->
    
    <!-- DETALLES DE LA ACTIVIDAD -->
    <div class="mb-6">
        <h4 class="font-semibold text-[#404041] mb-4 text-lg font-lora">Detalles de la Actividad</h4>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
            <div class="bg-white rounded-lg p-4 border border-[#404041] text-center h-24 flex flex-col justify-center">
                <div class="flex items-center justify-center gap-2 mb-2">
                    <i class="fas fa-map-marker-alt text-[#404041] text-xl"></i>
                </div>
                <div class="text-lg font-bold text-[#404041] font-lora modal-lugar">Centro Comunitario Norte</div>
                <p class="text-xs text-gray-700 font-lora mt-1">Lugar</p>
            </div>
            
            <div class="bg-white rounded-lg p-4 border border-[#404041] text-center h-24 flex flex-col justify-center">
                <div class="flex items-center justify-center gap-2 mb-2">
                    <i class="fas fa-user-tie text-[#404041] text-xl"></i>
                </div>
                <div class="text-lg font-bold text-[#404041] font-lora modal-promotor">María González López</div>
                <p class="text-xs text-gray-700 font-lora mt-1">Promotor</p>
            </div>
            
            <div class="bg-white rounded-lg p-4 border border-[#404041] text-center h-24 flex flex-col justify-center">
                <div class="flex items-center justify-center gap-2 mb-2">
                    <i class="fas fa-users text-[#404041] text-xl"></i>
                </div>
                <div class="text-lg font-bold text-[#404041] font-lora modal-participantes">45</div>
                <p class="text-xs text-gray-700 font-lora mt-1">Participantes</p>
            </div>
        </div>
    </div>
</x-modal-reporte-base>