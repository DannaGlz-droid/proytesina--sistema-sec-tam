<style>
    .modal-content-scroll::-webkit-scrollbar {
        width: 6px;
    }
    .modal-content-scroll::-webkit-scrollbar-track {
        background: transparent;
    }
    .modal-content-scroll::-webkit-scrollbar-thumb {
        background: #ccc;
        border-radius: 3px;
    }
    .modal-content-scroll::-webkit-scrollbar-thumb:hover {
        background: #999;
    }
    .modal-content-scroll {
        scrollbar-width: thin;
        scrollbar-color: #ccc transparent;
    }
</style>

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
    
    <!-- INFORMACIÓN GEOGRÁFICA -->
    <div class="mb-6">
        <h4 class="font-semibold text-[#404041] mb-4 text-lg font-lora">Información Geográfica</h4>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
            <div class="bg-white rounded-lg p-4 border border-[#404041]">
                <div class="flex items-center gap-3 mb-3">
                    <i class="fas fa-city text-[#404041] text-xl"></i>
                    <h5 class="font-semibold text-[#404041] font-lora">Municipio</h5>
                </div>
                <div class="text-lg font-bold text-[#404041] font-lora modal-municipio">-</div>
                <p class="text-xs text-gray-600 font-lora mt-1">Área de cobertura del evento</p>
            </div>
            
            <div class="bg-white rounded-lg p-4 border border-[#404041]">
                <div class="flex items-center gap-3 mb-3">
                    <i class="fas fa-map text-[#404041] text-xl"></i>
                    <h5 class="font-semibold text-[#404041] font-lora">Jurisdicción</h5>
                </div>
                <div class="text-lg font-bold text-[#404041] font-lora modal-jurisdiccion">-</div>
                <p class="text-xs text-gray-600 font-lora mt-1">Zona administrativa</p>
            </div>
        </div>
    </div>
    
    <!-- DETALLES DE LA ACTIVIDAD -->
    <div class="mb-6">
        <h4 class="font-semibold text-[#404041] mb-4 text-lg font-lora">Detalles de la Actividad</h4>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
            <div class="bg-white rounded-lg p-4 border border-[#404041]">
                <div class="flex items-center gap-3 mb-3">
                    <i class="fas fa-calendar-alt text-[#404041] text-xl"></i>
                    <h5 class="font-semibold text-[#404041] font-lora">Actividad</h5>
                </div>
                <div class="text-lg font-bold text-[#404041] font-lora modal-actividad">-</div>
                <p class="text-xs text-gray-600 font-lora mt-1">Tipo de actividad realizada</p>
            </div>
            
            <div class="bg-white rounded-lg p-4 border border-[#404041]">
                <div class="flex items-center gap-3 mb-3">
                    <i class="fas fa-users text-[#404041] text-xl"></i>
                    <h5 class="font-semibold text-[#404041] font-lora">Participantes</h5>
                </div>
                <div class="text-lg font-bold text-[#404041] font-lora modal-participantes">45</div>
                <p class="text-xs text-gray-600 font-lora mt-1">Asistentes registrados</p>
            </div>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
            <div class="bg-white rounded-lg p-4 border border-[#404041]">
                <div class="flex items-center gap-3 mb-3">
                    <i class="fas fa-map-marker-alt text-[#404041] text-xl"></i>
                    <h5 class="font-semibold text-[#404041] font-lora">Lugar</h5>
                </div>
                <div class="text-lg font-bold text-[#404041] font-lora modal-lugar modal-content-scroll max-h-24 overflow-y-auto pr-2 break-words leading-tight">Centro Comunitario Norte</div>
                <p class="text-xs text-gray-600 font-lora mt-1">Ubicación del evento</p>
            </div>
            
            <div class="bg-white rounded-lg p-4 border border-[#404041]">
                <div class="flex items-center gap-3 mb-3">
                    <i class="fas fa-user-tie text-[#404041] text-xl"></i>
                    <h5 class="font-semibold text-[#404041] font-lora">Promotor</h5>
                </div>
                <div class="text-lg font-bold text-[#404041] font-lora modal-promotor modal-content-scroll max-h-24 overflow-y-auto pr-2 break-words leading-tight">María González López</div>
                <p class="text-xs text-gray-600 font-lora mt-1">Responsable de la actividad</p>
            </div>
        </div>
    </div>
</x-modal-reporte-base>