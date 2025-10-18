@props(['titulo', 'tipos' => ['bar', 'pie', 'doughnut'], 'tipoInicial' => 'bar', 'descargable' => true, 'graficoId' => ''])

<div class="chart-card border border-[#404041] rounded-lg p-5 bg-white">
    <div class="chart-header flex justify-between items-center mb-4">
        <h3 class="chart-title font-semibold text-[#404041] text-lg font-lora">{{ $titulo }}</h3>
        
        <div class="chart-controls flex gap-2">
            <!-- Selector de tipo de gráfico -->
            @if(count($tipos) > 1)
                <div class="chart-type-selector flex bg-gray-100 rounded-lg p-1">
                    @if(in_array('bar', $tipos))
                        <button class="chart-type-btn {{ $tipoInicial == 'bar' ? 'active bg-[#611132] text-white' : 'bg-transparent text-gray-600' }} px-3 py-1 rounded text-xs flex items-center gap-1" 
                                data-chart-type="bar" title="Gráfico de Barras">
                            <i class="fas fa-chart-bar"></i>
                        </button>
                    @endif
                    
                    @if(in_array('line', $tipos))
                        <button class="chart-type-btn {{ $tipoInicial == 'line' ? 'active bg-[#611132] text-white' : 'bg-transparent text-gray-600' }} px-3 py-1 rounded text-xs flex items-center gap-1" 
                                data-chart-type="line" title="Gráfico de Líneas">
                            <i class="fas fa-chart-line"></i>
                        </button>
                    @endif
                    
                    @if(in_array('pie', $tipos))
                        <button class="chart-type-btn {{ $tipoInicial == 'pie' ? 'active bg-[#611132] text-white' : 'bg-transparent text-gray-600' }} px-3 py-1 rounded text-xs flex items-center gap-1" 
                                data-chart-type="pie" title="Gráfico Circular">
                            <i class="fas fa-chart-pie"></i>
                        </button>
                    @endif
                    
                    @if(in_array('doughnut', $tipos))
                        <button class="chart-type-btn {{ $tipoInicial == 'doughnut' ? 'active bg-[#611132] text-white' : 'bg-transparent text-gray-600' }} px-3 py-1 rounded text-xs flex items-center gap-1" 
                                data-chart-type="doughnut" title="Gráfico de Donas">
                            <i class="fas fa-circle"></i>
                        </button>
                    @endif
                </div>
            @endif
            
            <!-- Botón descargar -->
            <!-- En tu componente x-graficos.tarjeta -->
            @if($descargable)
                <button class="chart-download-btn font-lora bg-[#611132] text-white px-3 py-1 rounded-lg text-xs flex items-center gap-1">
                    <i class="fas fa-download"></i> Descargar
                </button>
            @endif
        </div>
    </div>
    
    <div class="chart-wrapper h-80">
        {{ $slot }}
    </div>
</div>