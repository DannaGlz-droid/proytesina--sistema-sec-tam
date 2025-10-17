@php
    $config = [
        'tipo' => 'alcoholimetria',
        'titulo' => 'Reporte de Alcoholimetría', 
        'colorBadge' => 'bg-[#9D2449]',
        'colorBorder' => 'border-[#470202]',
        'modalId' => 'modalAlcoholimetria'
    ];
@endphp

<x-modal-reporte-base 
    :tipo="$config['tipo']" 
    :titulo="$config['titulo']" 
    :colorBadge="$config['colorBadge']" 
    :colorBorder="$config['colorBorder']"
    :modalId="$config['modalId']">
    
    <!-- SECCIÓN ESPECÍFICA DE ALCOHOLIMETRÍA -->
    
    <!-- RESULTADOS DEL OPERATIVO -->
    <div class="mb-6">
        <h4 class="font-semibold text-[#404041] mb-4 text-lg font-lora">Resultados del Operativo</h4>
        
        <!-- Estadísticas principales -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-3 mb-4">
            <div class="bg-white rounded-lg p-3 border border-[#404041] text-center h-20 flex flex-col justify-center">
                <div class="flex items-center justify-center gap-2 mb-1">
                    <i class="fas fa-map-marker-alt text-[#404041] text-lg"></i>
                    <div class="text-2xl font-bold text-[#404041] font-lora modal-puntos-revision">5</div>
                </div>
                <p class="text-xs text-gray-700 font-lora">Puntos de revisión instalados</p>
            </div>

            <div class="bg-white rounded-lg p-3 border border-[#404041] text-center h-20 flex flex-col justify-center">
                <div class="flex items-center justify-center gap-2 mb-1">
                    <i class="fas fa-user-times text-[#404041] text-lg"></i>
                    <div class="text-2xl font-bold text-[#404041] font-lora modal-conductores-no-aptos">12</div>
                </div>
                <p class="text-xs text-gray-700 font-lora">Conductores no aptos</p>
            </div>
            
            <div class="bg-white rounded-lg p-3 border border-[#404041] text-center h-20 flex flex-col justify-center">
                <div class="flex items-center justify-center gap-2 mb-1">
                    <i class="fas fa-vial text-[#404041] text-lg"></i>
                    <div class="text-2xl font-bold text-[#404041] font-lora modal-pruebas-realizadas">20</div>
                </div>
                <p class="text-xs text-gray-700 font-lora">Pruebas realizadas</p>
            </div>
        </div>
    </div>

    <!-- LÍNEA SEPARADORA -->
    <div class="h-px bg-gray-300 mb-6"></div>

    <!-- CONDUCTORES NO APTOS -->
    <div class="mb-6">
        <h4 class="font-semibold text-[#404041] mb-4 text-lg font-lora">Conductores no aptos</h4>
        
        <!-- Por género -->
        <div class="mb-6">
            <h5 class="font-semibold text-[#404041] mb-3 text-md font-lora">Por género</h5>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                <div class="bg-white rounded-lg p-3 border border-[#404041] h-20 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <i class="fas fa-female text-[#404041] text-lg"></i>
                        <span class="text-sm text-gray-700 font-semibold font-lora">Mujeres</span>
                    </div>
                    <div class="text-2xl font-bold text-[#404041] font-lora modal-mujeres-no-aptas">5</div>
                </div>
                
                <div class="bg-white rounded-lg p-3 border border-[#404041] h-20 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <i class="fas fa-male text-[#404041] text-lg"></i>
                        <span class="text-sm text-gray-700 font-semibold font-lora">Hombres</span>
                    </div>
                    <div class="text-2xl font-bold text-[#404041] font-lora modal-hombres-no-aptos">6</div>
                </div>
            </div>
        </div>

        <!-- Por tipo de vehículo -->
        <div>
            <h5 class="font-semibold text-[#404041] mb-3 text-md font-lora">Por tipo de vehículo</h5>
            
            <!-- Primera fila de 3 cuadros -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-3 mb-3">
                <div class="bg-white rounded-lg p-3 border border-[#404041] text-center h-20 flex flex-col justify-center">
                    <div class="flex items-center justify-center gap-2 mb-1">
                        <i class="fas fa-car text-[#404041] text-lg"></i>
                        <div class="text-xl font-bold text-[#404041] font-lora modal-automoviles-no-aptos">5</div>
                    </div>
                    <p class="text-xs text-gray-700 font-lora">Automóviles y camionetas</p>
                </div>
                
                <div class="bg-white rounded-lg p-3 border border-[#404041] text-center h-20 flex flex-col justify-center">
                    <div class="flex items-center justify-center gap-2 mb-1">
                        <i class="fas fa-motorcycle text-[#404041] text-lg"></i>
                        <div class="text-xl font-bold text-[#404041] font-lora modal-motocicletas-no-aptas">2</div>
                    </div>
                    <p class="text-xs text-gray-700 font-lora">Motocicletas</p>
                </div>
                
                <div class="bg-white rounded-lg p-3 border border-[#404041] text-center h-20 flex flex-col justify-center">
                    <div class="flex items-center justify-center gap-2 mb-1">
                        <i class="fas fa-bus text-[#404041] text-lg"></i>
                        <div class="text-xl font-bold text-[#404041] font-lora modal-transporte-colectivo-no-apto">0</div>
                    </div>
                    <p class="text-xs text-gray-700 font-lora">Transporte público colectivo</p>
                </div>
            </div>
            
            <!-- Segunda fila de 3 cuadros -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                <div class="bg-white rounded-lg p-3 border border-[#404041] text-center h-20 flex flex-col justify-center">
                    <div class="flex items-center justify-center gap-2 mb-1">
                        <i class="fas fa-taxi text-[#404041] text-lg"></i>
                        <div class="text-xl font-bold text-[#404041] font-lora modal-transporte-individual-no-apto">1</div>
                    </div>
                    <p class="text-xs text-gray-700 font-lora">Transporte público individual</p>
                </div>
                
                <div class="bg-white rounded-lg p-3 border border-[#404041] text-center h-20 flex flex-col justify-center">
                    <div class="flex items-center justify-center gap-2 mb-1">
                        <i class="fas fa-truck text-[#404041] text-lg"></i>
                        <div class="text-xl font-bold text-[#404041] font-lora modal-transporte-carga-no-apto">1</div>
                    </div>
                    <p class="text-xs text-gray-700 font-lora">Transporte de carga</p>
                </div>
                
                <div class="bg-white rounded-lg p-3 border border-[#404041] text-center h-20 flex flex-col justify-center">
                    <div class="flex items-center justify-center gap-2 mb-1">
                        <i class="fas fa-ambulance text-[#404041] text-lg"></i>
                        <div class="text-xl font-bold text-[#404041] font-lora modal-emergencia-no-apto">0</div>
                    </div>
                    <p class="text-xs text-gray-700 font-lora">Vehículos de emergencia</p>
                </div>
            </div>
        </div>
    </div>
</x-modal-reporte-base>