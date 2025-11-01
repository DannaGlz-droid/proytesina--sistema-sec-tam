@extends('layouts.principal')
@section('title', 'Estadísticas')
@section('content')

    @include('components.header-admin')
    @include('components.nav-estadisticas')

    <div class="px-4 lg:pl-10 pt-6 lg:pt-10 pb-8 lg:pb-12">
        <!-- HEADER CON TÍTULO Y BOTÓN EXTERNO -->
        <div class="flex flex-col lg:flex-row lg:justify-between lg:items-center gap-4 mb-6">
            <div>
                <h1 class="text-2xl lg:text-3xl font-lora font-bold text-[#404041] mb-2">Estadísticas</h1>
                <p class="text-sm lg:text-base text-[#404041] font-lora">
                    Visualice datos a través de gráficos interactivos. Utilice los filtros para personalizar su análisis.
                </p>
            </div>
            
            <!-- BOTÓN CENTRADO VERTICALMENTE -->
            <button class="bg-[#611132] text-white px-4 py-2.5 rounded-lg text-xs font-semibold hover:bg-[#4a0e26] transition-all duration-300 font-lora flex items-center gap-2 whitespace-nowrap shadow-sm self-start lg:self-auto" id="descargarTodo">
                <i class="fas fa-download text-xs"></i>
                Descargar Todo
            </button>
        </div>

        <!-- Contenedor principal -->
        <div class="border border-[#404041] rounded-lg lg:rounded-xl bg-white bg-opacity-95 max-w-full shadow-md overflow-hidden">
            
            <div class="p-4 lg:p-6 pt-8 lg:pt-12">
                <!-- Layout principal: Filtros + Gráficos -->
                <div class="flex flex-col lg:flex-row gap-6">
                    
                    <!-- Columna Izquierda - Filtros (ANCHO FIJO) -->
                    <div id="estadisticas-filtros" class="lg:w-80">
                        {{-- Pasar las listas necesarias al componente de filtros para que use los datos reales de BD --}}
                        <x-filtros.estadisticas :municipalities="$municipalities ?? collect()" :causes="$causes ?? collect()" :jurisdictions="$jurisdictions ?? collect()" :sexes="$sexes ?? collect()" />
                    </div>

                    <!-- Columna Derecha - Gráficos (ESPACIO RESTANTE) -->
                    <div class="lg:flex-1">
                        <!-- Controles de presentación: etiquetas + límite (Top N) + paleta colocados inline -->
                        <div class="flex items-center gap-4 mb-4 flex-wrap">
                            <label for="datalabelMode" class="text-sm text-gray-600 font-lora whitespace-nowrap">Etiquetas (valor / %)</label>
                            <select id="datalabelMode" class="text-sm border border-gray-200 rounded px-3 py-1.5 pr-8 w-40 sm:w-52 font-lora bg-white">
                                <option value="auto">Auto (predeterminado)</option>
                                <option value="value">Sólo valores</option>
                                <option value="percent">Sólo porcentaje</option>
                                <option value="both">Valores y %</option>
                                <option value="none">Ocultar</option>
                            </select>

                            <!-- Top / Límite de resultados (inline, compact) -->
                            <label for="chartLimit" class="text-sm text-gray-600 font-lora whitespace-nowrap">Top</label>
                            <select id="chartLimit" class="text-sm border border-gray-200 rounded px-2 py-1 pr-6 w-28 sm:w-32 font-lora bg-white" title="Mostrar sólo los N elementos principales">
                                <option value="all">Todos</option>
                                <option value="5">Top 5</option>
                                <option value="10">Top 10</option>
                                <option value="15">Top 15</option>
                            </select>

                            <!-- Selector de Paleta de Colores -->
                            <label for="colorPalette" class="text-sm text-gray-600 font-lora whitespace-nowrap">Paleta</label>
                            <select id="colorPalette" class="text-sm border border-gray-200 rounded px-3 py-1.5 pr-8 w-40 sm:w-52 font-lora bg-white" title="Seleccionar esquema de colores">
                                <option value="principal">Principal Institucional</option>
                                <option value="modern">Moderna Variada</option>
                                <option value="nature">Natural Equilibrada</option>
                                <option value="warm">Cálida</option>
                                <option value="cool">Fría</option>
                                <option value="sunset">Sunset</option>
                                <option value="ocean">Ocean</option>
                                <option value="vibrant">Vibrante</option>
                            </select>
                        </div>
                        
                        <!-- Categoría: Distribución Geográfica -->
                        <x-graficos.categoria 
                            titulo="Distribución Geográfica" 
                            icono="map-marked-alt"
                        >
                            <x-graficos.tarjeta 
                                titulo="Distribución por Municipios" 
                                :tipos="['bar', 'pie', 'doughnut']" 
                                tipoInicial="bar"
                                graficoId="municipioChart"
                            >
                                <!-- label moved to header controls to keep layout compact -->
                                    <div class="chart-wrapper" style="height:300px; overflow:hidden; position:relative;">
                                    <canvas id="municipioChart" class="chart-canvas" role="img" aria-label="Distribución por Municipios: número de defunciones por municipio" tabindex="0" style="height:100% !important; width:100% !important; display:block;"></canvas>
                                </div>
                            </x-graficos.tarjeta>
                            
                            <x-graficos.tarjeta 
                                titulo="Distribución por Jurisdicción" 
                                :tipos="['bar', 'doughnut']" 
                                tipoInicial="bar"
                                graficoId="jurisdiccionChart"
                            >
                                <div class="chart-wrapper" style="height:320px; overflow:hidden; position:relative;">
                                        <canvas id="jurisdiccionChart" class="chart-canvas" role="img" aria-label="Distribución por Jurisdicción: número de defunciones por jurisdicción" tabindex="0" style="height:100% !important; width:100% !important; display:block;"></canvas>
                                </div>
                            </x-graficos.tarjeta>

                            <x-graficos.tarjeta 
                                titulo="Comparativa: Residencia vs Defunción" 
                                :tipos="['bar']" 
                                tipoInicial="bar"
                                graficoId="compareChart"
                            >
                                <div class="chart-wrapper" style="height:320px; overflow:hidden; position:relative;">
                                    <canvas id="compareChart" class="chart-canvas" role="img" aria-label="Comparativa: número de defunciones por municipio comparando residencia y lugar de defunción" tabindex="0" style="height:100% !important; width:100% !important; display:block;"></canvas>
                                </div>
                            </x-graficos.tarjeta>
                        </x-graficos.categoria>

                        <!-- Categoría: Tendencia Temporal -->
                        <x-graficos.categoria 
                            titulo="Tendencia Temporal" 
                            icono="calendar-alt"
                        >
                            <x-graficos.tarjeta 
                                    titulo="Tendencia" 
                                    :tipos="['line', 'area']" 
                                    tipoInicial="line"
                                    graficoId="tendenciaChart"
                                >
                                    <!-- Subtítulo dinámico: se actualizará según la granularidad seleccionada -->
                                    <div id="tendenciaSubtitle" class="text-sm text-gray-600 mb-2">Mensual</div>
                                    <div class="chart-wrapper" style="height:260px; overflow:hidden; position:relative;">
                                        <canvas id="tendenciaChart" class="chart-canvas" role="img" aria-label="Tendencia temporal de defunciones en el periodo seleccionado" tabindex="0" style="height:100% !important; width:100% !important; display:block;"></canvas>
                                    </div>
                                </x-graficos.tarjeta>
                        </x-graficos.categoria>

                        <!-- Categoría: Distribución Demográfica -->
                        <x-graficos.categoria 
                            titulo="Distribución Demográfica" 
                            icono="users"
                            grid-cols="grid-cols-1 lg:grid-cols-2 gap-6"
                        >
                            <x-graficos.tarjeta 
                                titulo="Distribución por Edades" 
                                :tipos="['bar', 'pie']" 
                                tipoInicial="bar"
                                graficoId="edadChart"
                            >
                                <div class="chart-wrapper" style="height:260px; overflow:hidden; position:relative;">
                                    <canvas id="edadChart" class="chart-canvas" role="img" aria-label="Distribución por edades: conteo de defunciones por rango etario" tabindex="0" style="height:100% !important; width:100% !important; display:block;"></canvas>
                                </div>
                            </x-graficos.tarjeta>
                            
                            <x-graficos.tarjeta 
                                titulo="Distribución por Género" 
                                :tipos="['doughnut', 'pie', 'bar']" 
                                tipoInicial="doughnut"
                                graficoId="generoChart"
                            >
                                <div class="chart-wrapper" style="height:320px; overflow:hidden; position:relative;">
                                    <canvas id="generoChart" class="chart-canvas" role="img" aria-label="Distribución por género: proporción y conteo de defunciones por sexo" tabindex="0" style="height:100% !important; width:100% !important; display:block;"></canvas>
                                </div>
                            </x-graficos.tarjeta>
                        </x-graficos.categoria>

                        <!-- Categoría: Causas -->
                        <x-graficos.categoria 
                            titulo="Causas" 
                            icono="heartbeat"
                        >
                            <x-graficos.tarjeta 
                                titulo="Causas de Defunción" 
                                :tipos="['doughnut', 'pie', 'bar']" 
                                tipoInicial="doughnut"
                                graficoId="causaChart"
                            >
                                <div class="chart-wrapper" style="height:340px; overflow:hidden; position:relative;">
                                    <canvas id="causaChart" class="chart-canvas" role="img" aria-label="Causas de defunción: distribución por causa" tabindex="0" style="height:100% !important; width:100% !important; display:block;"></canvas>
                                </div>
                            </x-graficos.tarjeta>
                        </x-graficos.categoria>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Incluir Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- Data labels plugin para mostrar cifras directamente en los gráficos -->
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2"></script>

    <!-- INCLUIR EL MODAL DE DESCARGAS -->
    @include('components.modal-descargas')

    <!-- Script para gráficos (ACTUALIZADO) -->
    <script>
    // Mapa de gráficos global para acceso desde el modal
    let charts = {};
    // Municipio kind selected: 'death' (defunción) o 'residence' (residencia)
    let selectedMunKind = 'residence';
    // Modo de datalabels: 'auto'|'value'|'percent'|'both'|'none'
    let dataLabelMode = 'auto';

        document.addEventListener('DOMContentLoaded', function() {
            // Inicializar gráficos
            const municipioCtx = document.getElementById('municipioChart').getContext('2d');
            const tendenciaCtx = document.getElementById('tendenciaChart').getContext('2d');
            const edadCtx = document.getElementById('edadChart').getContext('2d');
            const generoCtx = document.getElementById('generoChart').getContext('2d');
            const causaCtx = document.getElementById('causaChart').getContext('2d');
            const jurisdiccionCtx = document.getElementById('jurisdiccionChart').getContext('2d');
            const compareCtx = document.getElementById('compareChart').getContext('2d');
            
            // Inicialmente no hay datos (se cargarán vía fetch según filtros)
            const sampleData = {
                municipios: [], municipioCounts: [],
                meses: [], mesCounts: [],
                edades: [], edadCounts: [],
                generos: [], generoCounts: [],
                causas: [], causaCounts: []
            };
            
            // Registrar el plugin de datalabels (muestra los números dentro/debajo de los elementos)
            try {
                if (typeof ChartDataLabels !== 'undefined') Chart.register(ChartDataLabels);
            } catch (e) {
                console.warn('chartjs-plugin-datalabels no disponible:', e);
            }

            // Variable global para almacenar la paleta seleccionada
            let selectedPalette = 'principal';

            // Definir múltiples paletas de colores profesionales con matices variados
            const colorPalettes = {
                // Armoniosa: inspirada en tu color institucional #611132 (vino)
                principal: [
                    '#611132', // vino institucional (principal)
                    '#8B6F47', // dorado cálido (complementario cálido)
                    '#2C5F5D', // verde azulado (triádico)
                    '#9B4D6F', // rosa viejo (análogo)
                    '#4A7C7E', // teal suave (complementario frío)
                    '#A67C52', // caramelo (complementario split)
                    '#3D4F5C', // azul pizarra (neutro frío)
                    '#7A5060'  // malva (análogo suave)
                ],
                // Moderna: matices completamente diferentes (azul, verde, naranja, magenta, cyan, amarillo)
                modern: [
                    '#4A90A4', // azul turquesa
                    '#7BA05B', // verde oliva
                    '#D88559', // naranja terracota
                    '#B565A7', // magenta
                    '#5DADA5', // cyan
                    '#D4A657', // mostaza
                    '#8D6A9F', // púrpura
                    '#6B8BA3'  // azul acero
                ],
                // Natural: tierra, bosque, cielo, mar (matices de naturaleza)
                nature: [
                    '#8B6F47', // tierra/café
                    '#5A7D5F', // verde bosque
                    '#6B9AC4', // azul cielo
                    '#7E8B92', // gris piedra
                    '#5D8C8A', // verde agua
                    '#B88B62', // arena
                    '#4A6B5F', // verde musgo
                    '#8FA3AD'  // azul grisáceo
                ],
                // Cálida: rojos, naranjas, amarillos, marrones (solo tonos cálidos)
                warm: [
                    '#C85D5D', // rojo ladrillo
                    '#D89155', // naranja calabaza
                    '#E3B567', // dorado
                    '#B67C5D', // marrón cálido
                    '#D47B6A', // coral
                    '#CBA052', // mostaza oscura
                    '#A86F5F', // terracota
                    '#8B7355'  // café con leche
                ],
                // Fría: azules, verdes, púrpuras (solo tonos fríos)
                cool: [
                    '#5B8BA6', // azul acero
                    '#5A9B8E', // verde jade
                    '#7E88B5', // azul lavanda
                    '#6A8E90', // teal
                    '#8895B9', // azul periwinkle
                    '#5F9F93', // verde agua
                    '#6B7FA8', // azul slate
                    '#7B9C95'  // verde grisáceo
                ],
                // Sunset: paleta cálida inspirada en atardeceres
                sunset: [
                    '#D65D5D', // rojo atardecer
                    '#E88C4F', // naranja intenso
                    '#F5B95F', // ámbar dorado
                    '#E6A06A', // melocotón
                    '#C97575', // rosa coral
                    '#D9955C', // naranja suave
                    '#B88470', // marrón rosado
                    '#9A7B6F'  // taupe cálido
                ],
                // Ocean: azules y verdes de océano profundo a costa
                ocean: [
                    '#2F5F7F', // azul marino profundo
                    '#4A7A8C', // azul océano
                    '#5D9B9B', // teal
                    '#6BAEB4', // aqua
                    '#5A8F83', // verde mar
                    '#7BA8AB', // azul claro
                    '#4E7B84', // azul petróleo
                    '#83A5A3'  // verde agua pálido
                ],
                // Vibrante: colores vivos pero equilibrados con matices diferentes
                vibrant: [
                    '#E85D75', // rosa coral vibrante
                    '#5AA4D4', // azul brillante
                    '#7BC67E', // verde lima
                    '#E89C4F', // naranja dorado
                    '#9B7BC4', // púrpura
                    '#5FC5C5', // cyan vibrante
                    '#E6B65C', // amarillo mostaza
                    '#8B8FA3'  // gris azulado
                ]
            };

            function hexToRgba(hex, alpha) {
                const h = hex.replace('#','');
                const bigint = parseInt(h, 16);
                const r = (bigint >> 16) & 255;
                const g = (bigint >> 8) & 255;
                const b = bigint & 255;
                return `rgba(${r}, ${g}, ${b}, ${alpha})`;
            }

            // Genera un gradiente de colores usando un solo color base con diferentes intensidades
            function getMonochromaticPalette(count, baseColor, alpha = 0.75) {
                const bg = [];
                const br = [];

                // Convertir hex a HSL (más robusto y legible)
                const hr = parseInt(baseColor.slice(1, 3), 16);
                const hg = parseInt(baseColor.slice(3, 5), 16);
                const hb = parseInt(baseColor.slice(5, 7), 16);

                // Utilidad: RGB -> HSL
                function rgbToHsl(r, g, b) {
                    r /= 255; g /= 255; b /= 255;
                    const max = Math.max(r, g, b), min = Math.min(r, g, b);
                    let h, s, l = (max + min) / 2;
                    if (max === min) {
                        h = s = 0; // achromatic
                    } else {
                        const d = max - min;
                        s = l > 0.5 ? d / (2 - max - min) : d / (max + min);
                        switch (max) {
                            case r: h = (g - b) / d + (g < b ? 6 : 0); break;
                            case g: h = (b - r) / d + 2; break;
                            case b: h = (r - g) / d + 4; break;
                        }
                        h /= 6;
                    }
                    return [Math.round(h * 360), Math.round(s * 100), Math.round(l * 100)];
                }

                const [baseH, baseS, baseL] = rgbToHsl(hr, hg, hb);

                // Rango de variación de luminosidad (en porcentaje). Ajustable.
                // Queremos mantener la similitud con el color base, así que variamos
                // ±spread/2 alrededor de baseL, evitando valores extremos.
                const spread = Math.min(40, Math.max(18, Math.floor(30 - Math.log10(Math.max(1, count)) * 4)));
                const half = spread / 2;

                // Si solo hay 1 elemento, devolver el color base directamente
                if (count === 1) {
                    bg.push(`hsla(${baseH}, ${Math.max(30, baseS)}%, ${Math.max(18, baseL)}%, ${alpha})`);
                    br.push(`hsla(${baseH}, ${Math.max(30, baseS)}%, ${Math.max(8, baseL - 10)}%, 1)`);
                    return { background: bg, border: br };
                }

                const step = spread / (count - 1);
                for (let i = 0; i < count; i++) {
                    // Distribuir luz desde baseL + half -> baseL - half
                    const lightness = Math.round(Math.max(12, Math.min(88, baseL + half - (step * i))));
                    const saturation = Math.round(Math.max(30, Math.min(90, baseS)));

                    bg.push(`hsla(${baseH}, ${saturation}%, ${lightness}%, ${alpha})`);
                    br.push(`hsla(${baseH}, ${saturation}%, ${Math.max(8, lightness - 10)}%, 1)`);
                }

                return { background: bg, border: br };
            }

            // devuelve array de colores RGBA para background y los borders según la paleta seleccionada
            function getPalette(count, alpha = 0.75, forceMonochromatic = false) {
                const currentPalette = colorPalettes[selectedPalette] || colorPalettes.principal;
                
                // Para gráficos que se ven mejor con un solo color (como municipios/jurisdicciones)
                if (forceMonochromatic) {
                    const baseColor = currentPalette[0]; // usar el primer color de la paleta
                    return getMonochromaticPalette(count, baseColor, alpha);
                }
                
                const bg = [];
                const br = [];

                // Si el número de categorías es mayor a la longitud de la paleta
                // o hay muchas categorías (>=12), generamos colores adicionales
                // pero usando los colores de la paleta seleccionada como base
                const useGenerated = (count > currentPalette.length) || (count >= 12);

                if (useGenerated) {
                    // Primero usar todos los colores de la paleta seleccionada
                    for (let i = 0; i < Math.min(count, currentPalette.length); i++) {
                        const hex = currentPalette[i];
                        bg.push(hexToRgba(hex, alpha));
                        br.push(hexToRgba(hex, 1));
                    }
                    
                    // Si necesitamos más colores, generar variaciones basadas en la paleta
                    if (count > currentPalette.length) {
                        // Extraer los tonos base de la paleta seleccionada
                        const baseHues = currentPalette.map(hex => {
                            const r = parseInt(hex.slice(1, 3), 16);
                            const g = parseInt(hex.slice(3, 5), 16);
                            const b = parseInt(hex.slice(5, 7), 16);
                            const max = Math.max(r, g, b) / 255;
                            const min = Math.min(r, g, b) / 255;
                            const delta = max - min;
                            let h = 0;
                            if (delta !== 0) {
                                if (max === r/255) h = ((g/255 - b/255) / delta) % 6;
                                else if (max === g/255) h = (b/255 - r/255) / delta + 2;
                                else h = (r/255 - g/255) / delta + 4;
                            }
                            return Math.round(h * 60);
                        });
                        
                        // Generar colores adicionales interpolando entre los tonos de la paleta
                        for (let i = currentPalette.length; i < count; i++) {
                            const baseIndex = i % baseHues.length;
                            const hueVariation = Math.floor(i / baseHues.length) * 30; // incremento de 30°
                            const hue = (baseHues[baseIndex] + hueVariation) % 360;
                            const saturation = 58 - (Math.floor(i / baseHues.length) * 8); // reducir saturación gradualmente
                            const light = 48 + (i % 2 === 0 ? -4 : 4); // alternar claridad
                            bg.push(`hsla(${hue}, ${Math.max(30, saturation)}%, ${light}%, ${alpha})`);
                            br.push(`hsla(${hue}, ${Math.max(30, saturation)}%, ${light}%, 1)`);
                        }
                    }
                    return { background: bg, border: br };
                }

                // fallback: usar la paleta definida (cíclica)
                for (let i = 0; i < count; i++) {
                    const hex = currentPalette[i % currentPalette.length];
                    bg.push(hexToRgba(hex, alpha));
                    br.push(hexToRgba(hex, 1));
                }

                return { background: bg, border: br };
            }

            // Crear gráficos (inicialmente vacíos)
            charts.municipioChart = new Chart(municipioCtx, {
                type: 'bar',
                data: {
                    labels: sampleData.municipios,
                    datasets: [{
                        label: 'Defunciones',
                        data: sampleData.municipioCounts,
                        backgroundColor: [
                            'rgba(97, 17, 50, 0.7)',
                            'rgba(64, 64, 65, 0.7)',
                            'rgba(139, 69, 19, 0.7)',
                            'rgba(101, 67, 33, 0.7)',
                            'rgba(119, 119, 119, 0.7)',
                            'rgba(150, 150, 150, 0.7)',
                            'rgba(180, 180, 180, 0.7)'
                        ],
                        borderColor: [
                            'rgba(97, 17, 50, 1)',
                            'rgba(64, 64, 65, 1)',
                            'rgba(139, 69, 19, 1)',
                            'rgba(101, 67, 33, 1)',
                            'rgba(119, 119, 119, 1)',
                            'rgba(150, 150, 150, 1)',
                            'rgba(180, 180, 180, 1)'
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    // Top padding reduced so the legend doesn't create excessive empty space above the bars
                    layout: { padding: { top: 12, right: 8, left: 8, bottom: 8 } },
                    plugins: {
                        datalabels: {
                            color: '#fff',
                            anchor: 'center',
                            align: 'center',
                            clamp: true,
                            formatter: function(value) { return value; },
                            font: { weight: '600', size: 13 }
                        },
                        legend: {
                            position: 'top',
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
            
            charts.tendenciaChart = new Chart(tendenciaCtx, {
                type: 'line',
                data: {
                    labels: sampleData.meses,
                    datasets: [{
                        label: 'Defunciones',
                        data: sampleData.mesCounts,
                        backgroundColor: 'rgba(97, 17, 50, 0.2)',
                        borderColor: 'rgba(97, 17, 50, 1)',
                        borderWidth: 2,
                        pointBackgroundColor: 'rgba(97, 17, 50, 1)',
                        pointBorderColor: '#fff',
                        pointHoverBackgroundColor: '#fff',
                        pointHoverBorderColor: 'rgba(97, 17, 50, 1)',
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        datalabels: {
                            display: false // normalmente no mostramos etiquetas en líneas para no saturar
                        },
                        legend: {
                            position: 'top',
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
            
            charts.edadChart = new Chart(edadCtx, {
                type: 'bar',
                data: {
                    labels: sampleData.edades,
                    datasets: [{
                        label: 'Personas',
                        data: sampleData.edadCounts,
                        backgroundColor: 'rgba(64, 64, 65, 0.7)',
                        borderColor: 'rgba(64, 64, 65, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        datalabels: {
                            // center the labels inside the bars for better visibility
                            color: '#fff',
                            anchor: 'center',
                            align: 'center',
                            formatter: function(value) { return value; },
                            font: { weight: '700', size: 13 }
                        },
                        legend: {
                            position: 'top',
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
            
            charts.generoChart = new Chart(generoCtx, {
                type: 'doughnut',
                data: {
                    labels: sampleData.generos,
                    datasets: [{
                        data: sampleData.generoCounts,
                        backgroundColor: [
                            'rgba(97, 17, 50, 0.7)',
                            'rgba(64, 64, 65, 0.7)'
                        ],
                        borderColor: [
                            'rgba(97, 17, 50, 1)',
                            'rgba(64, 64, 65, 1)'
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'right',
                        },
                        datalabels: {
                            color: '#fff',
                            formatter: function(value, ctx) {
                                // Mostrar valor y porcentaje
                                const data = ctx.chart.data.datasets[0].data;
                                const sum = data.reduce((a, b) => a + b, 0) || 0;
                                const pct = sum ? Math.round((value / sum) * 100) : 0;
                                return value + ' (' + pct + '%)';
                            },
                            font: { weight: '700', size: 13 }
                        }
                    }
                }
            });
            
            charts.causaChart = new Chart(causaCtx, {
                type: 'doughnut',
                data: {
                    labels: sampleData.causas,
                    datasets: [{
                        data: sampleData.causaCounts,
                        backgroundColor: [
                            'rgba(97, 17, 50, 0.7)',
                            'rgba(64, 64, 65, 0.7)',
                            'rgba(139, 69, 19, 0.7)',
                            'rgba(101, 67, 33, 0.7)',
                            'rgba(119, 119, 119, 0.7)',
                            'rgba(150, 150, 150, 0.7)'
                        ],
                        borderColor: [
                            'rgba(97, 17, 50, 1)',
                            'rgba(64, 64, 65, 1)',
                            'rgba(139, 69, 19, 1)',
                            'rgba(101, 67, 33, 1)',
                            'rgba(119, 119, 119, 1)',
                            'rgba(150, 150, 150, 1)'
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'right',
                        },
                        datalabels: {
                            color: '#fff',
                            formatter: function(value, ctx) {
                                const data = ctx.chart.data.datasets[0].data;
                                const sum = data.reduce((a, b) => a + b, 0) || 0;
                                const pct = sum ? Math.round((value / sum) * 100) : 0;
                                return value + ' (' + pct + '%)';
                            },
                            font: { weight: '700', size: 13 }
                        }
                    }
                }
            });

            // Jurisdicción chart
            charts.jurisdiccionChart = new Chart(jurisdiccionCtx, {
                type: 'bar',
                data: {
                    labels: [],
                    datasets: [{
                        label: 'Defunciones',
                        data: [],
                        backgroundColor: 'rgba(97, 17, 50, 0.7)',
                        borderColor: 'rgba(97, 17, 50, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        datalabels: { color: '#222', anchor: 'end', align: 'end', formatter: v=>v, font: { weight: '600', size: 13 } },
                        legend: { position: 'top' }
                    },
                    scales: { y: { beginAtZero: true } }
                }
            });

            // Compare Residence vs Death chart (stacked)
            charts.compareChart = new Chart(compareCtx, {
                type: 'bar',
                data: {
                    labels: [],
                    datasets: [
                        { label: 'Residencia', data: [], backgroundColor: 'rgba(64, 64, 65, 0.7)' },
                        { label: 'Defunción', data: [], backgroundColor: 'rgba(97, 17, 50, 0.7)' }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { datalabels: { color: '#222', font:{weight:'600', size: 13} }, legend:{position:'top'} },
                    scales: { x: { stacked: false }, y: { beginAtZero: true, stacked: false } }
                }
            });

            // Municipio kind toggle (Defunción / Residencia)
            const muniKindToggle = document.getElementById('municipioKindToggle');
            if (muniKindToggle) {
                muniKindToggle.querySelectorAll('.mun-kind-btn').forEach(btn => {
                    btn.addEventListener('click', function() {
                        // reset visual state
                        muniKindToggle.querySelectorAll('.mun-kind-btn').forEach(b => {
                            // clear all possible background/text classes so we can set the desired inactive style
                            b.classList.remove('bg-[#611132]', 'text-white', 'bg-transparent', 'text-gray-600', 'bg-white');
                            // set inactive appearance
                            b.classList.add('bg-white', 'text-gray-600');
                        });

                                // set active appearance on clicked button
                                this.classList.remove('bg-white', 'text-gray-600');
                                this.classList.add('bg-[#611132]', 'text-white');

                        // save selected kind and reload charts
                        selectedMunKind = this.getAttribute('data-value') || 'death';

                        // Update legend label immediately so the UI reflects the choice
                        try {
                            if (charts && charts.municipioChart && charts.municipioChart.data && charts.municipioChart.data.datasets && charts.municipioChart.data.datasets[0]) {
                                charts.municipioChart.data.datasets[0].label = (selectedMunKind === 'residence') ? 'Defunciones (residencia)' : 'Defunciones (defunción)';
                                charts.municipioChart.update();
                            }
                        } catch (e) {
                            console.warn('No se pudo actualizar la etiqueta del chart inmediatamente:', e);
                        }

                        loadChartsDebounced();
                    });
                });

                // Initialize default active button (residence)
                const initBtn = muniKindToggle.querySelector('.mun-kind-btn[data-value="residence"]');
                if (initBtn) {
                    // ensure any conflicting classes removed
                    initBtn.classList.remove('bg-white','bg-transparent','text-gray-600');
                    initBtn.classList.add('bg-[#611132]', 'text-white');
                    selectedMunKind = 'residence';
                }
            }

            // Cambiar tipo de gráfico
            document.querySelectorAll('.chart-type-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const chartType = this.getAttribute('data-chart-type');
                    const chartId = this.closest('.chart-card').querySelector('canvas').id;
                    const chart = charts[chartId];

                    if (chart) {
                        // Remover clase active de todos los botones del mismo grupo
                        this.parentElement.querySelectorAll('.chart-type-btn').forEach(b => {
                            b.classList.remove('active', 'bg-[#611132]', 'text-white');
                            b.classList.add('bg-transparent', 'text-gray-600');
                        });

                        // Agregar clase active al botón clickeado
                        this.classList.add('active', 'bg-[#611132]', 'text-white');
                        this.classList.remove('bg-transparent', 'text-gray-600');

                        // Recreate chart with appropriate options for the new type to avoid leftover gridlines
                        recreateChart(chartId, chartType);
                    }
                });
            });

            // Datalabels formatter generator depending on chart type and selected mode
            function getDatalabelsFormatter(type, mode) {
                // returns a function (value, ctx) => string | ''
                mode = mode || 'auto';
                if (mode === 'none') return function() { return ''; };

                // helper to compute percent
                const percentFor = function(value, ctx) {
                    const data = ctx.chart.data.datasets[0].data;
                    const sum = data.reduce((a,b)=>a+(Number(b)||0),0) || 0;
                    const pct = sum ? Math.round((Number(value) / sum) * 100) : 0;
                    return pct;
                };

                return function(value, ctx) {
                    const isPie = (type === 'pie' || type === 'doughnut');
                    // auto: pie/doughnut -> value + pct ; bars -> value only
                    const actualMode = (mode === 'auto') ? (isPie ? 'both' : 'value') : mode;

                    if (actualMode === 'value') return Number(value).toLocaleString();
                    if (actualMode === 'percent') return percentFor(value, ctx) + '%';
                    if (actualMode === 'both') return Number(value).toLocaleString() + ' (' + percentFor(value, ctx) + '%)';
                    return '';
                };
            }

            // Apply the current datalabels mode to a given chart instance
            function updateDatalabelsForChartInstance(chart, type) {
                try {
                    if (!chart || !chart.options || !chart.options.plugins) return;
                    // ensure plugin section exists
                    chart.options.plugins.datalabels = chart.options.plugins.datalabels || {};
                    const fmt = getDatalabelsFormatter(type, dataLabelMode);
                    // decide whether to display at all
                    const display = (dataLabelMode === 'none') ? false : true;
                    chart.options.plugins.datalabels.display = display;
                    chart.options.plugins.datalabels.formatter = fmt;
                    // color choices: pies white, bars dark
                    if (type === 'pie' || type === 'doughnut') chart.options.plugins.datalabels.color = '#fff';
                    else chart.options.plugins.datalabels.color = '#222';
                    // enforce font weight/size defaults
                    chart.options.plugins.datalabels.font = chart.options.plugins.datalabels.font || { weight: '600', size: 13 };
                    chart.update();
                } catch (e) {
                    console.warn('updateDatalabelsForChartInstance error', e);
                }
            }

            // Helper: return sensible default options per chart type to avoid leaving scale/grid artifacts
            function getDefaultOptionsForType(type) {
                const base = {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'top' },
                        datalabels: { display: true, color: '#fff', font: { weight: '600', size: 13 } }
                    }
                };

                if (type === 'pie' || type === 'doughnut') {
                    base.plugins.legend.position = 'right';
                    base.plugins.datalabels.formatter = getDatalabelsFormatter(type, dataLabelMode);
                    // pie/doughnut do not use scales
                } else {
                    // bar/line/scatter: include scales and integer ticks
                    base.plugins.datalabels.formatter = getDatalabelsFormatter(type, dataLabelMode);
                    base.scales = {
                        x: { ticks: { autoSkip: true }, grid: { display: false } },
                        y: { 
                            beginAtZero: true, 
                            ticks: { 
                                callback: function(value, index, ticks) {
                                    // Asegurarse de que value es un número primitivo
                                    if (typeof value === 'object' && value !== null && value.value !== undefined) {
                                        value = value.value;
                                    }
                                    const v = Number(value);
                                    if (!Number.isFinite(v)) return '';
                                    return Math.round(v).toLocaleString();
                                }
                            }
                        }
                    };
                }

                return base;
            }

            // Recreate a chart instance with the chosen type; this avoids leftover gridlines when switching
            function recreateChart(chartId, newType) {
                try {
                    const old = charts[chartId];
                    if (!old) return;
                    const canvas = document.getElementById(chartId);
                    const ctx = canvas.getContext('2d');

                    // Deep clone data (labels + datasets) but not functions
                    const data = JSON.parse(JSON.stringify(old.data || { labels: [], datasets: [] }));

                    // Destroy old chart instance
                    try { old.destroy(); } catch (e) { /* ignore */ }

                    // Manejar tipo 'area' (que en Chart.js es 'line' con fill: true)
                    let actualType = newType;
                    if (newType === 'area') {
                        actualType = 'line';
                        // Configurar fill para todos los datasets
                        data.datasets.forEach(dataset => {
                            dataset.fill = true;
                            dataset.backgroundColor = dataset.backgroundColor || 'rgba(97, 17, 50, 0.2)';
                        });
                    } else if (newType === 'line') {
                        // Para línea pura, asegurar que no hay fill
                        data.datasets.forEach(dataset => {
                            dataset.fill = false;
                        });
                    }

                    // Build default options for the new type
                    const options = getDefaultOptionsForType(actualType);

                    // Register datalabels plugin if available
                    try { if (typeof ChartDataLabels !== 'undefined') Chart.register(ChartDataLabels); } catch (e) {}

                    // Create new chart
                    const cfg = { type: actualType, data: data, options: options };
                    charts[chartId] = new Chart(ctx, cfg);

                    // Apply nice scaling for cartesian charts
                    if (actualType === 'bar' || actualType === 'line') {
                        try {
                            const max = Math.max(...(data.datasets[0].data.map(n => Number(n) || 0)));
                            applyNiceScaling(charts[chartId], max);
                        } catch (e) {}
                    }
                } catch (e) {
                    console.error('Error recreating chart', e);
                }
            }

            // Descargar gráficas individuales - AHORA ABRE EL MODAL
            document.querySelectorAll('.chart-download-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const chartId = this.closest('.chart-card').querySelector('canvas').id;
                    window.mostrarModalDescargas('individual', chartId);
                });
            });

            // Descargar todo - AHORA ABRE EL MODAL
            document.getElementById('descargarTodo').addEventListener('click', function() {
                window.mostrarModalDescargas('todo');
            });

            // --- FETCH Y ACTUALIZACIÓN DINÁMICA SEGÚN FILTROS ---
            const chartsEndpoint = '{{ route('estadisticas.charts-data') }}';

            function mapSexLabel(s) {
                if (!s) return s;
                if (s === 'M' || s.toLowerCase() === 'm') return 'Hombre';
                if (s === 'F' || s.toLowerCase() === 'f') return 'Mujer';
                return s;
            }

            function normalizeDataset(labels, counts) {
                if (!Array.isArray(labels) || labels.length === 0) {
                    return { labels: ['Sin datos'], counts: [0] };
                }
                return { labels: labels, counts: counts };
            }

            // Agrupar Top N — mostrar SOLO los top N (no agregar "Otros").
            // limitValue puede ser 'all' o un número (string). Si es 'all' devuelve todas las etiquetas.
            function groupTopN(labels, counts, limitValue) {
                if (!Array.isArray(labels) || !Array.isArray(counts) || labels.length === 0) return { labels: ['Sin datos'], counts: [0] };
                const topN = (limitValue && limitValue !== 'all') ? Number(limitValue) : labels.length;
                if (!Number.isFinite(topN) || topN <= 0) return { labels: ['Sin datos'], counts: [0] };
                if (labels.length <= topN) return { labels: labels.slice(), counts: counts.slice().map(n => Number(n) || 0) };

                const arr = labels.map((lab, i) => ({ label: lab, count: Number(counts[i]) || 0 }));
                // ordenar desc por conteo
                arr.sort((a, b) => b.count - a.count);

                const top = arr.slice(0, topN);
                const outLabels = top.map(x => x.label);
                const outCounts = top.map(x => x.count);

                return { labels: outLabels, counts: outCounts };
            }

            // Similar a groupTopN pero para dos series (ej. residencia vs defunción)
            // Devuelve SOLO los top N ordenados por la suma de ambas series.
            function groupTopNCompare(labels, aCounts, bCounts, limitValue) {
                if (!Array.isArray(labels) || labels.length === 0) return { labels: ['Sin datos'], aCounts: [0], bCounts: [0] };
                const topN = (limitValue && limitValue !== 'all') ? Number(limitValue) : labels.length;
                if (!Number.isFinite(topN) || topN <= 0) return { labels: ['Sin datos'], aCounts: [0], bCounts: [0] };
                if (labels.length <= topN) return { labels: labels.slice(), aCounts: aCounts.slice().map(n=>Number(n)||0), bCounts: bCounts.slice().map(n=>Number(n)||0) };

                const arr = labels.map((lab, i) => ({ label: lab, a: Number(aCounts[i]) || 0, b: Number(bCounts[i]) || 0, sum: (Number(aCounts[i]) || 0) + (Number(bCounts[i]) || 0) }));
                // ordenar por suma (desc)
                arr.sort((x, y) => y.sum - x.sum);

                const top = arr.slice(0, topN);
                const outLabels = top.map(x => x.label);
                const outA = top.map(x => x.a);
                const outB = top.map(x => x.b);

                return { labels: outLabels, aCounts: outA, bCounts: outB };
            }

            // Calcular un stepSize "agradable" para el eje Y (1,2,5 * 10^n) para ~5-8 ticks
            function niceStep(maxValue, targetTicks = 6) {
                if (!isFinite(maxValue) || maxValue <= 0) return 1;
                const raw = maxValue / targetTicks;
                const pow = Math.pow(10, Math.floor(Math.log10(raw)));
                const mant = raw / pow;
                let niceMant;
                if (mant <= 1) niceMant = 1;
                else if (mant <= 2) niceMant = 2;
                else if (mant <= 5) niceMant = 5;
                else niceMant = 10;
                return niceMant * pow;
            }

            // Aplicar escala agradable a un Chart.js chart instance (asegura ticks enteros, formateados)
            function applyNiceScaling(chart, suggestedMax) {
                try {
                    if (!chart || !chart.options || !chart.options.scales || !chart.options.scales.y) return;
                    const y = chart.options.scales.y;
                    y.beginAtZero = true;
                    // compute step from suggestedMax
                    const step = niceStep(suggestedMax || (y.suggestedMax || 0));
                    
                    // Recrear el objeto ticks completamente para evitar recursión
                    y.ticks = {
                        stepSize: step,
                        callback: function(value, index, ticks) {
                            // Asegurarse de que value es un número primitivo
                            if (typeof value === 'object' && value !== null && value.value !== undefined) {
                                value = value.value;
                            }
                            const v = Number(value);
                            if (!Number.isFinite(v)) return '';
                            // Show integer tick; avoid decimals
                            return Math.round(v).toLocaleString();
                        }
                    };
                    
                    // Optionally set suggestedMax to a multiple of step so top tick is round
                    if (!y.suggestedMax || y.suggestedMax < suggestedMax) {
                        const top = Math.ceil((suggestedMax || 0) / step) * step;
                        y.suggestedMax = top;
                    }
                } catch (e) {
                    console.warn('applyNiceScaling error', e);
                }
            }

            // Helpers para formateo de fechas y periodos legibles
            function monthShortNameESP(m) {
                const names = ['Ene','Feb','Mar','Abr','May','Jun','Jul','Ago','Sep','Oct','Nov','Dic'];
                return names[m-1] || '';
            }

            function formatDateDDMMYYYY(dstr) {
                if (!dstr) return '';
                const d = new Date(dstr);
                if (isNaN(d)) return dstr;
                const dd = String(d.getDate()).padStart(2,'0');
                const mm = String(d.getMonth()+1).padStart(2,'0');
                const yy = d.getFullYear();
                return `${dd}/${mm}/${yy}`;
            }

            function formatMonthYear(dstr) {
                // dstr may be '2025-03-01' or '2025-03' or '03' if only month number passed
                try {
                    const d = new Date(dstr);
                    if (!isNaN(d)) return `${monthShortNameESP(d.getMonth()+1)} ${d.getFullYear()}`;
                } catch(e) {}
                // fallback: try split
                if (/^\d{4}-\d{2}/.test(dstr)) {
                    const parts = dstr.split('-');
                    return `${monthShortNameESP(Number(parts[1]))} ${parts[0]}`;
                }
                return dstr;
            }

            function getReadablePeriodLabel(params) {
                // Inspect the UI controls to present a friendly label (handles quarter, month, year, multiple-months, custom)
                const dateRange = document.getElementById('dateRange')?.value;
                const year = document.getElementById('year')?.value;
                const month = document.getElementById('month')?.value;
                const selectedMonths = Array.from(document.querySelectorAll('.month-checkbox:checked')).map(i => Number(i.value));
                const quarter = document.getElementById('quarter')?.value;
                const startDate = document.getElementById('startDate')?.value;
                const endDate = document.getElementById('endDate')?.value;

                if (dateRange === 'quarter' && year && quarter) {
                    const q = Number(quarter);
                    const startMonth = (q - 1) * 3 + 1;
                    const endMonth = startMonth + 2;
                    const startLabel = `${monthShortNameESP(startMonth)} ${year}`;
                    const endLabel = `${monthShortNameESP(endMonth)} ${year}`;
                    return `Q${q} ${year} (${startLabel}–${endLabel})`;
                }

                if (dateRange === 'month' && year && month) {
                    // specific month
                    const mNum = Number(month);
                    return `${monthShortNameESP(mNum)} ${year}`;
                }

                if (dateRange === 'year' && year) {
                    return `${year}`;
                }

                if (dateRange === 'multiple-months' && year && selectedMonths.length) {
                    selectedMonths.sort((a,b)=>a-b);
                    const min = selectedMonths[0];
                    const max = selectedMonths[selectedMonths.length-1];
                    if (min === max) return `${monthShortNameESP(min)} ${year}`;
                    return `${monthShortNameESP(min)}–${monthShortNameESP(max)} ${year}`;
                }

                if (dateRange === 'custom' && startDate && endDate) {
                    // Decide how to present based on group_by (day/month/year)
                    const gb = params.group_by || 'month';
                    if (gb === 'day') return `${formatDateDDMMYYYY(startDate)} – ${formatDateDDMMYYYY(endDate)}`;
                    if (gb === 'month') {
                        // present like 'Ene 2024 – Jun 2024' using month-year
                        const s = new Date(startDate); const e = new Date(endDate);
                        return `${monthShortNameESP(s.getMonth()+1)} ${s.getFullYear()} – ${monthShortNameESP(e.getMonth()+1)} ${e.getFullYear()}`;
                    }
                    return `${new Date(startDate).getFullYear()} – ${new Date(endDate).getFullYear()}`;
                }

                // Fallback: if we have explicit start/end in params
                if (params.start_date && params.end_date) {
                    const s = new Date(params.start_date); const e = new Date(params.end_date);
                    // if same month/year, show month year
                    if (s.getFullYear() === e.getFullYear() && s.getMonth() === e.getMonth()) return `${monthShortNameESP(s.getMonth()+1)} ${s.getFullYear()}`;
                    // if span <= 365 show month-year range
                    const spanDays = Math.ceil((e - s) / (1000*60*60*24));
                    if (spanDays <= 365) return `${monthShortNameESP(s.getMonth()+1)} ${s.getFullYear()} – ${monthShortNameESP(e.getMonth()+1)} ${e.getFullYear()}`;
                    return `${s.getFullYear()} – ${e.getFullYear()}`;
                }

                return 'Todos los datos';
            }

            function updateChartsFromResponse(resp) {
                console.log('updateChartsFromResponse received:', resp);
                // Municipios
                const m = normalizeDataset(resp.municipios.labels, resp.municipios.counts);

                // Ajustar altura del wrapper dinámicamente según número de municipios (más etiquetas = más altura)
                try {
                    // Municipio: escala suave y con límite máximo para evitar ocupar toda la pantalla
                    const muniWrapper = document.getElementById('municipioChart')?.closest('.chart-wrapper');
                    if (muniWrapper) {
                        const labelCount = Array.isArray(m.labels) ? m.labels.length : 0;
                        // base más compacto pero que puede crecer ligeramente según etiquetas
                        const base = 300; // altura base más comedida
                        const extraPer = 12; // px extra por etiqueta adicional (más suave)
                        const threshold = 12; // no aumentamos hasta 12 etiquetas
                        const extra = labelCount > threshold ? (labelCount - threshold) * extraPer : 0;
                        const maxH = 520; // cap máximo razonable
                        const newH = Math.min(Math.max(base + extra, base), maxH);
                        muniWrapper.style.height = newH + 'px';
                    }

                    // Género: normalmente dos segmentos — aseguramos un tamaño confortable pero no excesivo
                    const generoWrapper = document.getElementById('generoChart')?.closest('.chart-wrapper');
                    if (generoWrapper) {
                        const gw = Math.max(280, Math.min(360, Math.floor(window.innerWidth * 0.32)));
                        generoWrapper.style.height = gw + 'px';
                    }

                    // Causas: usar la misma heurística / tamaño que Género para mantener consistencia
                    const causaWrapper = document.getElementById('causaChart')?.closest('.chart-wrapper');
                    if (causaWrapper) {
                        // reuse gw computed for genero (if available), fallback to a comfortable size
                        const reuse = Math.max(280, Math.min(360, Math.floor(window.innerWidth * 0.32)));
                        causaWrapper.style.height = reuse + 'px';
                    }

                    // Edad: adaptar a un tamaño cómodo similar a genero
                    const edadWrapper = document.getElementById('edadChart')?.closest('.chart-wrapper');
                    if (edadWrapper) {
                        const ew = Math.max(300, Math.min(360, Math.floor(window.innerWidth * 0.32)));
                        edadWrapper.style.height = ew + 'px';
                    }
                } catch (e) {
                    console.warn('No se pudo ajustar altura dinámica de municipios/género/causas:', e);
                }

                // Agrupar Top N según el selector chartLimit (si está presente)
                const limitVal = document.getElementById('chartLimit')?.value || 'all';
                const muniGrouped = groupTopN(m.labels, m.counts, limitVal, 'Otros');
                charts.municipioChart.data.labels = muniGrouped.labels;
                charts.municipioChart.data.datasets[0].data = muniGrouped.counts;
                // Aplicar paleta monocromática para mejor apariencia con muchas categorías
                const muniPalette = getPalette(muniGrouped.labels.length, 0.7, true);
                charts.municipioChart.data.datasets[0].backgroundColor = muniPalette.background;
                charts.municipioChart.data.datasets[0].borderColor = muniPalette.border;
                // Update dataset label to reflect whether we're grouping by residence or death
                charts.municipioChart.data.datasets[0].label = (selectedMunKind === 'residence') ? 'Defunciones (residencia)' : 'Defunciones (defunción)';
                // aplicar escala agradable según el máximo del dataset
                try { const maxM = Math.max(...(m.counts.map(c=>Number(c)||0))); applyNiceScaling(charts.municipioChart, maxM); } catch(e){}
                charts.municipioChart.update();

                // Tendencia
                const t = normalizeDataset(resp.meses.labels, resp.meses.counts);
                charts.tendenciaChart.data.labels = t.labels;
                charts.tendenciaChart.data.datasets[0].data = t.counts;
                // Aplicar color de la paleta para la línea
                const trendPalette = getPalette(1, 0.2);
                const trendBorder = getPalette(1, 1.0);
                charts.tendenciaChart.data.datasets[0].backgroundColor = trendPalette.background[0];
                charts.tendenciaChart.data.datasets[0].borderColor = trendBorder.border[0];
                charts.tendenciaChart.data.datasets[0].pointBackgroundColor = trendBorder.border[0];
                charts.tendenciaChart.data.datasets[0].pointBorderColor = '#fff';
                charts.tendenciaChart.data.datasets[0].pointHoverBackgroundColor = '#fff';
                charts.tendenciaChart.data.datasets[0].pointHoverBorderColor = trendBorder.border[0];
                try { const maxT = Math.max(...(t.counts.map(c=>Number(c)||0))); applyNiceScaling(charts.tendenciaChart, maxT); } catch(e){}
                charts.tendenciaChart.update();

                // Añadir total al subtítulo (suma de la serie de tendencia si existe)
                try {
                    const sub = document.getElementById('tendenciaSubtitle');
                    if (sub) {
                        let total = 0;
                        if (resp.meses && Array.isArray(resp.meses.counts)) total = resp.meses.counts.reduce((a,b)=>a+(Number(b)||0),0);
                        // fallback: sum municipios
                        if (!total && resp.municipios && Array.isArray(resp.municipios.counts)) total = resp.municipios.counts.reduce((a,b)=>a+(Number(b)||0),0);
                        if (total) sub.textContent = sub.textContent + ` · Total: ${total}`;
                    }
                } catch(e){ console.warn('No se pudo añadir total al subtítulo:', e); }

                // Edades
                const e = normalizeDataset(resp.edades.labels, resp.edades.counts);
                charts.edadChart.data.labels = e.labels;
                charts.edadChart.data.datasets[0].data = e.counts;
                // Aplicar paleta de colores
                const edadPalette = getPalette(e.labels.length, 0.7);
                charts.edadChart.data.datasets[0].backgroundColor = edadPalette.background;
                charts.edadChart.data.datasets[0].borderColor = edadPalette.border;
                try { const maxE = Math.max(...(e.counts.map(c=>Number(c)||0))); applyNiceScaling(charts.edadChart, maxE); } catch(e){}
                charts.edadChart.update();

                // Genero (mapear etiquetas)
                let gLabels = (resp.generos.labels || []).map(mapSexLabel);
                const g = normalizeDataset(gLabels, resp.generos.counts);
                charts.generoChart.data.labels = g.labels;
                charts.generoChart.data.datasets[0].data = g.counts;
                // Aplicar paleta de colores
                const generoPalette = getPalette(g.labels.length, 0.7);
                charts.generoChart.data.datasets[0].backgroundColor = generoPalette.background;
                charts.generoChart.data.datasets[0].borderColor = generoPalette.border;
                charts.generoChart.update();

                // Causas (aplicar Top N + Otros)
                const c = normalizeDataset(resp.causas.labels, resp.causas.counts);
                const causaGrouped = groupTopN(c.labels, c.counts, limitVal, 'Otros');
                charts.causaChart.data.labels = causaGrouped.labels;
                charts.causaChart.data.datasets[0].data = causaGrouped.counts;
                // Aplicar paleta de colores
                const causaPalette = getPalette(causaGrouped.labels.length, 0.7);
                charts.causaChart.data.datasets[0].backgroundColor = causaPalette.background;
                charts.causaChart.data.datasets[0].borderColor = causaPalette.border;
                charts.causaChart.update();

                // Jurisdicciones (si vienen) - aplicar Top N
                if (resp.jurisdictions) {
                    const j = normalizeDataset(resp.jurisdictions.labels, resp.jurisdictions.counts);
                    const jurGrouped = groupTopN(j.labels, j.counts, limitVal, 'Otros');
                    charts.jurisdiccionChart.data.labels = jurGrouped.labels;
                    charts.jurisdiccionChart.data.datasets[0].data = jurGrouped.counts;
                    // Aplicar paleta monocromática para mejor apariencia con muchas categorías
                    const jurPalette = getPalette(jurGrouped.labels.length, 0.7, true);
                    charts.jurisdiccionChart.data.datasets[0].backgroundColor = jurPalette.background;
                    charts.jurisdiccionChart.data.datasets[0].borderColor = jurPalette.border;
                    try { const maxJ = Math.max(...(jurGrouped.counts.map(c=>Number(c)||0))); applyNiceScaling(charts.jurisdiccionChart, maxJ); } catch(e){}
                    charts.jurisdiccionChart.update();
                }

                // Comparativa residencia vs defuncion (aplicar Top N sobre la suma de ambas series)
                if (resp.municipios_compare) {
                    const labels = resp.municipios_compare.labels || [];
                    const resCounts = resp.municipios_compare.residence_counts || [];
                    const deathCounts = resp.municipios_compare.death_counts || [];
                    const cmpGrouped = groupTopNCompare(labels, resCounts, deathCounts, limitVal, 'Otros');
                    charts.compareChart.data.labels = cmpGrouped.labels;
                    charts.compareChart.data.datasets[0].data = cmpGrouped.aCounts;
                    charts.compareChart.data.datasets[1].data = cmpGrouped.bCounts;
                    // Usar solo 2 colores fijos: uno para Residencia y otro para Defunción
                    // Residencia: gris/azul neutro, Defunción: primer color de la paleta seleccionada
                    const cmpPalette = getPalette(2, 0.7);
                    charts.compareChart.data.datasets[0].backgroundColor = 'rgba(100, 116, 139, 0.7)'; // Residencia en gris neutro
                    charts.compareChart.data.datasets[0].borderColor = 'rgba(100, 116, 139, 1)';
                    charts.compareChart.data.datasets[1].backgroundColor = cmpPalette.background[0]; // Defunción con primer color de paleta
                    charts.compareChart.data.datasets[1].borderColor = cmpPalette.border[0];
                    try { const both = cmpGrouped.aCounts.concat(cmpGrouped.bCounts).map(c=>Number(c)||0); const maxC = both.length ? Math.max(...both) : 0; applyNiceScaling(charts.compareChart, maxC); } catch(e){}
                    charts.compareChart.update();
                }

                // Update badges / totals for each chart (show quick total)
                try {
                    function setBadge(chartId, text) {
                        const el = document.querySelector(`.chart-total-badge[data-chart-id="${chartId}"]`);
                        if (!el) return;
                        el.textContent = text;
                    }

                    // municipio totals: show number of municipios included + total defunciones
                    const muniLabelsCount = (resp.municipios && Array.isArray(resp.municipios.labels)) ? resp.municipios.labels.length : 0;
                    const totalMunicipios = (resp.municipios && Array.isArray(resp.municipios.counts)) ? resp.municipios.counts.reduce((a,b)=>a+(Number(b)||0),0) : 0;
                    if (muniLabelsCount) setBadge('municipioChart', `Municipios: ${muniLabelsCount} · Total: ${totalMunicipios.toLocaleString()}`);
                    else setBadge('municipioChart', totalMunicipios ? `Total: ${totalMunicipios.toLocaleString()}` : 'Sin datos');

                    // tendencia total (already calculated earlier as sum of meses)
                    const totalT = (resp.meses && Array.isArray(resp.meses.counts)) ? resp.meses.counts.reduce((a,b)=>a+(Number(b)||0),0) : 0;
                    setBadge('tendenciaChart', totalT ? `Total: ${totalT.toLocaleString()}` : 'Sin datos');

                    // edades
                    const totalE = (resp.edades && Array.isArray(resp.edades.counts)) ? resp.edades.counts.reduce((a,b)=>a+(Number(b)||0),0) : 0;
                    setBadge('edadChart', totalE ? `Total: ${totalE.toLocaleString()}` : 'Sin datos');

                    // genero
                    const totalG = (resp.generos && Array.isArray(resp.generos.counts)) ? resp.generos.counts.reduce((a,b)=>a+(Number(b)||0),0) : 0;
                    setBadge('generoChart', totalG ? `Total: ${totalG.toLocaleString()}` : 'Sin datos');

                    // causas
                    const totalC = (resp.causas && Array.isArray(resp.causas.counts)) ? resp.causas.counts.reduce((a,b)=>a+(Number(b)||0),0) : 0;
                    setBadge('causaChart', totalC ? `Total: ${totalC.toLocaleString()}` : 'Sin datos');

                    // jurisdiccion
                    const totalJ = (resp.jurisdictions && Array.isArray(resp.jurisdictions.counts)) ? resp.jurisdictions.counts.reduce((a,b)=>a+(Number(b)||0),0) : 0;
                    setBadge('jurisdiccionChart', totalJ ? `Total: ${totalJ.toLocaleString()}` : 'Sin datos');
                } catch (e) { console.warn('No se pudo actualizar badges:', e); }
            }

            // Build request params from the filters UI (maps ids to the endpoint query params)
            function buildParamsFromFilterControls() {
                const params = {};

                const dateRange = document.getElementById('dateRange')?.value;
                const year = document.getElementById('year')?.value;
                const month = document.getElementById('month')?.value;
                const selectedMonths = Array.from(document.querySelectorAll('.month-checkbox:checked')).map(i => i.value);
                const quarter = document.getElementById('quarter')?.value;
                const startDate = document.getElementById('startDate')?.value;
                const endDate = document.getElementById('endDate')?.value;

                // compute start_date/end_date according to the selected mode
                if (dateRange === 'custom' && startDate && endDate) {
                    params.start_date = startDate;
                    params.end_date = endDate;
                    // compute span in days to decide grouping granularity
                    try {
                        const d1 = new Date(startDate);
                        const d2 = new Date(endDate);
                        const spanDays = Math.max(1, Math.ceil((d2 - d1) / (1000*60*60*24)));
                        if (spanDays <= 31) params.group_by = 'day';
                        else if (spanDays <= 365) params.group_by = 'month';
                        else params.group_by = 'year';
                    } catch (e) {
                        // ignore
                    }
                } else if (dateRange === 'month' && year && month) {
                    const d1 = new Date(Number(year), Number(month) - 1, 1);
                    const d2 = new Date(Number(year), Number(month), 0);
                    params.start_date = d1.toISOString().slice(0,10);
                    params.end_date = d2.toISOString().slice(0,10);
                    // specific month -> show daily trend
                    params.group_by = 'day';
                } else if (dateRange === 'year' && year) {
                    params.start_date = `${year}-01-01`;
                    params.end_date = `${year}-12-31`;
                    // whole year -> monthly trend
                    params.group_by = 'month';
                } else if (dateRange === 'multiple-months' && year && selectedMonths.length) {
                    const months = selectedMonths.map(m => Number(m));
                    const min = Math.min(...months);
                    const max = Math.max(...months);
                    const d1 = new Date(Number(year), min - 1, 1);
                    const d2 = new Date(Number(year), max, 0);
                    params.start_date = d1.toISOString().slice(0,10);
                    params.end_date = d2.toISOString().slice(0,10);
                    // multiple months -> monthly trend
                    params.group_by = 'month';
                } else if (dateRange === 'quarter' && year && quarter) {
                    const q = Number(quarter);
                    const startMonth = (q - 1) * 3;
                    const d1 = new Date(Number(year), startMonth, 1);
                    const d2 = new Date(Number(year), startMonth + 3, 0);
                    params.start_date = d1.toISOString().slice(0,10);
                    params.end_date = d2.toISOString().slice(0,10);
                    // quarter -> monthly trend
                    params.group_by = 'month';
                }

                // Municipality: choose the select depending on the municipio_kind toggle
                const muniSelect = (selectedMunKind === 'residence') ? document.getElementById('municipio') : document.getElementById('municipioDefuncion');
                const muniVal = (muniSelect?.value || '').trim();
                // ignore generic 'Todos'/'All' placeholder values that some selects use
                if (muniVal && !/^(todos|all)$/i.test(muniVal)) {
                    if (/^\d+$/.test(muniVal)) params.municipality_id = muniVal; // send id
                    else {
                        // send as name fallback depending on which select
                        if (selectedMunKind === 'residence') params.municipio = muniVal;
                        else params.municipioDefuncion = muniVal;
                    }
                }

                // Cause (id if present)
                const causa = (document.getElementById('causa')?.value || '').trim();
                if (causa && !/^(todos|all)$/i.test(causa)) {
                    if (/^\d+$/.test(causa)) params.cause_id = causa;
                    else params.causa = causa;
                }

                // Sex mapping: 'hombre'->M, 'mujer'->F
                const sexo = (document.getElementById('sexo')?.value || '').trim();
                if (sexo && !/^(todos|all)$/i.test(sexo)) {
                    const s = sexo.toLowerCase();
                    if (s === 'hombre') params.sex = 'M';
                    else if (s === 'mujer') params.sex = 'F';
                    else params.sex = sexo;
                }

                // limit for top-N
                const limit = document.getElementById('chartLimit')?.value;
                if (limit && limit !== 'all') params.limit = limit;

                // Include municipio_kind so backend knows which municipality column to aggregate
                if (typeof selectedMunKind !== 'undefined' && selectedMunKind) {
                    params.municipio_kind = selectedMunKind;
                }

                // If group_by not set by the above logic, default to 'month' (this is what the backend expects by default)
                if (!params.group_by) params.group_by = 'month';

                return params;
            }

            let fetchTimeout = null;
            function loadChartsDebounced() {
                if (fetchTimeout) clearTimeout(fetchTimeout);
                fetchTimeout = setTimeout(loadCharts, 250);
            }

            function loadCharts() {
                const params = buildParamsFromFilterControls();
                // Actualizar subtítulo de la tarjeta de tendencia según la granularidad seleccionada y el rango legible
                try {
                    const sub = document.getElementById('tendenciaSubtitle');
                    if (sub) {
                        const gb = params.group_by || 'month';
                        const map = { day: 'Diaria', month: 'Mensual', year: 'Anual' };
                        const gran = map[gb] ? map[gb] : gb;
                        const periodLabel = getReadablePeriodLabel(params);
                        sub.textContent = `${gran} · ${periodLabel}`;
                    }
                } catch (e) {
                    console.warn('No se pudo actualizar subtítulo de tendencia:', e);
                }
                const qs = new URLSearchParams(params).toString();
                const url = chartsEndpoint + (qs ? ('?' + qs) : '');
                console.log('Fetching charts data from:', url);
                fetch(url, { credentials: 'same-origin' })
                    .then(async r => {
                        // Try to parse JSON even when HTTP status is not OK so we can display server-side debug info
                        let jsonBody = null;
                        try {
                            jsonBody = await r.json();
                        } catch (e) {
                            // fallback to raw text if JSON parsing fails
                            try { jsonBody = { __raw: await r.text() }; } catch (e2) { jsonBody = { __raw: null }; }
                        }

                        console.log('Charts data response (status ' + r.status + '):', jsonBody);

                        // show the JSON (or text) in a visible debug box on the page to help debugging
                        try {
                            let dbg = document.getElementById('chartsDebug');
                            if (!dbg) {
                                dbg = document.createElement('pre');
                                dbg.id = 'chartsDebug';
                                dbg.style.cssText = 'background:#f7fafc;border:1px solid #e2e8f0;padding:10px;margin:12px;white-space:pre-wrap;max-height:220px;overflow:auto;font-size:12px;';
                                const container = document.querySelector('.lg\:flex-1') || document.body;
                                container.insertBefore(dbg, container.firstChild);
                            }
                            dbg.textContent = JSON.stringify({ status: r.status, body: jsonBody }, null, 2);
                        } catch (e) {
                            console.warn('No se pudo mostrar debug box:', e);
                        }

                        if (!r.ok) {
                            console.error('Server returned HTTP', r.status, jsonBody);
                            // do not try to update charts on error
                            return;
                        }

                        updateChartsFromResponse(jsonBody);
                    })
                    .catch(err => {
                        console.error('Error cargando datos de gráficos (network/parsing):', err);
                    });
            }

            // Exponer funciones para ser invocadas desde el panel de filtros
            window.loadCharts = loadCharts;
            window.resetChartsToDefault = resetChartsToDefault;

            // (quick presets removed)

            // Ya NO escuchamos cambios automáticos en los filtros
            // const filtrosContainer = document.getElementById('estadisticas-filtros');
            // if (filtrosContainer) {
            //     filtrosContainer.addEventListener('change', loadChartsDebounced);
            //     filtrosContainer.addEventListener('input', loadChartsDebounced);
            // }

            // Escuchar el botón "Aplicar Filtros"
            const aplicarFiltrosBtn = document.getElementById('aplicarFiltros');
            if (aplicarFiltrosBtn) {
                aplicarFiltrosBtn.addEventListener('click', function() {
                    loadChartsDebounced();
                });
            }

            // Escuchar el botón "Limpiar" para reiniciar gráficos
            const limpiarFiltrosBtn = document.getElementById('limpiarFiltros');
            if (limpiarFiltrosBtn) {
                limpiarFiltrosBtn.addEventListener('click', function() {
                    // Reiniciar controles de gráficos a valores por defecto
                    resetChartsToDefault();
                    // Cargar gráficos con filtros vacíos (todos los datos)
                    setTimeout(() => loadChartsDebounced(), 100);
                });
            }

            // Función para reiniciar todos los controles de gráficos a valores por defecto
            function resetChartsToDefault() {
                try {
                    // Reiniciar selector de etiquetas (datalabels)
                    const dlSelect = document.getElementById('datalabelMode');
                    if (dlSelect) {
                        dlSelect.value = 'auto';
                        dataLabelMode = 'auto';
                    }

                    // Reiniciar selector de límite (Top N)
                    const limitSelect = document.getElementById('chartLimit');
                    if (limitSelect) {
                        limitSelect.value = 'all';
                    }

                    // Reiniciar selector de paleta
                    const paletteSelect = document.getElementById('colorPalette');
                    if (paletteSelect) {
                        paletteSelect.value = 'principal';
                        selectedPalette = 'principal';
                    }

                    // Reiniciar toggle de municipios a valor por defecto (residencia)
                    const muniKindToggle = document.getElementById('municipioKindToggle');
                    if (muniKindToggle) {
                        const buttons = muniKindToggle.querySelectorAll('.mun-kind-btn');
                        buttons.forEach(btn => {
                            btn.classList.remove('bg-[#611132]', 'text-white');
                            btn.classList.add('bg-transparent', 'text-gray-600');
                        });
                        const residenceBtn = muniKindToggle.querySelector('.mun-kind-btn[data-value="residence"]');
                        if (residenceBtn) {
                            residenceBtn.classList.remove('bg-transparent', 'text-gray-600');
                            residenceBtn.classList.add('bg-[#611132]', 'text-white');
                        }
                        selectedMunKind = 'residence';
                    }

                    // Reiniciar botones de tipo de gráfico a sus valores por defecto
                    Object.keys(charts).forEach(chartId => {
                        const container = document.querySelector(`#${chartId}`).closest('.chart-card');
                        if (!container) return;
                        
                        const buttons = container.querySelectorAll('.chart-type-btn');
                        buttons.forEach(btn => {
                            btn.classList.remove('active', 'bg-[#611132]', 'text-white');
                            btn.classList.add('bg-transparent', 'text-gray-600');
                        });

                        // Activar botón por defecto según el tipo de gráfico
                        let defaultType = 'bar';
                        if (chartId === 'tendenciaChart') defaultType = 'line';
                        else if (chartId === 'generoChart' || chartId === 'causaChart') defaultType = 'doughnut';

                        const defaultBtn = container.querySelector(`.chart-type-btn[data-chart-type="${defaultType}"]`);
                        if (defaultBtn) {
                            defaultBtn.classList.add('active', 'bg-[#611132]', 'text-white');
                            defaultBtn.classList.remove('bg-transparent', 'text-gray-600');
                        }

                        // Recrear gráfico con tipo por defecto si es necesario
                        const currentChart = charts[chartId];
                        if (currentChart && currentChart.config?.type !== defaultType) {
                            recreateChart(chartId, defaultType);
                        }
                    });

                    console.log('Controles de gráficos reiniciados a valores por defecto');
                } catch (e) {
                    console.warn('Error reiniciando controles de gráficos:', e);
                }
            }

            // Wire datalabel mode select
            const dlSelect = document.getElementById('datalabelMode');
            if (dlSelect) {
                // initialize from select value
                dataLabelMode = dlSelect.value || 'auto';
                dlSelect.addEventListener('change', function() {
                    dataLabelMode = this.value || 'auto';
                    // Recreate charts so new options (including datalabels formatters) are applied reliably.
                    try {
                        const chartIds = ['municipioChart','tendenciaChart','edadChart','generoChart','causaChart','jurisdiccionChart','compareChart'];
                        chartIds.forEach(id => {
                            const inst = charts[id];
                            const currentType = inst?.config?.type || inst?.options?.type || (id === 'tendenciaChart' ? 'line' : 'bar');
                            if (inst) recreateChart(id, currentType);
                        });
                    } catch (e) { console.warn('Error recreating charts for datalabel mode change', e); }
                });
            }

            // Wire chart limit (Top N) select - placed outside the filters container so we must explicitly listen
            const limitSelect = document.getElementById('chartLimit');
            if (limitSelect) {
                // ensure default exists
                if (!limitSelect.value) limitSelect.value = 'all';
                limitSelect.addEventListener('change', function() {
                    // trigger a reload so backend receives the `limit` param and charts update accordingly
                    try { loadChartsDebounced(); } catch (e) { console.warn('Error triggering reload on chartLimit change', e); }
                });
            }

            // Wire color palette select
            const paletteSelect = document.getElementById('colorPalette');
            if (paletteSelect) {
                // ensure default exists
                if (!paletteSelect.value) paletteSelect.value = 'principal';
                paletteSelect.addEventListener('change', function() {
                    selectedPalette = this.value || 'principal';
                    // Actualizar los colores de todos los gráficos
                    try {
                        Object.keys(charts).forEach(chartId => {
                            const chart = charts[chartId];
                            if (!chart || !chart.data || !chart.data.datasets) return;
                            
                            // El gráfico comparativo usa solo 2 colores fijos
                            if (chartId === 'compareChart') {
                                const cmpPalette = getPalette(2, 0.7);
                                chart.data.datasets[0].backgroundColor = 'rgba(100, 116, 139, 0.7)'; // Residencia
                                chart.data.datasets[0].borderColor = 'rgba(100, 116, 139, 1)';
                                chart.data.datasets[1].backgroundColor = cmpPalette.background[0]; // Defunción
                                chart.data.datasets[1].borderColor = cmpPalette.border[0];
                            } else if (chartId === 'municipioChart' || chartId === 'jurisdiccionChart') {
                                // Municipios y jurisdicciones usan paleta monocromática
                                const dataLength = chart.data.labels ? chart.data.labels.length : 0;
                                const palette = getPalette(dataLength, 0.7, true);
                                
                                // Actualizar colores de cada dataset
                                chart.data.datasets.forEach(dataset => {
                                    if (dataset) {
                                        dataset.backgroundColor = palette.background;
                                        dataset.borderColor = palette.border;
                                    }
                                });
                            } else {
                                // Otros gráficos usan la paleta completa multicolor
                                const dataLength = chart.data.labels ? chart.data.labels.length : 0;
                                const palette = getPalette(dataLength, 0.7);
                                
                                // Actualizar colores de cada dataset
                                chart.data.datasets.forEach(dataset => {
                                    if (dataset) {
                                        dataset.backgroundColor = palette.background;
                                        dataset.borderColor = palette.border;
                                    }
                                });
                            }
                            
                            chart.update();
                        });
                    } catch (e) {
                        console.warn('Error actualizando paleta de colores:', e);
                    }
                });
            }

            // Cargar inicialmente (sin filtros)
            loadCharts();

            // Sincronizar botones de tipo de gráfico con el tipo real de cada chart
            function syncChartTypeButtons() {
                Object.keys(charts).forEach(chartId => {
                    const chart = charts[chartId];
                    if (!chart) return;
                    
                    const realType = chart.config?.type || chart.options?.type;
                    const container = document.querySelector(`#${chartId}`).closest('.chart-card');
                    if (!container) return;
                    
                    // Determinar si es tipo 'area' (line con fill: true)
                    let displayType = realType;
                    if (realType === 'line' && chart.data.datasets[0]?.fill === true) {
                        displayType = 'area';
                    }
                    
                    // Encontrar todos los botones de tipo en este contenedor
                    const buttons = container.querySelectorAll('.chart-type-btn');
                    buttons.forEach(btn => {
                        btn.classList.remove('active', 'bg-[#611132]', 'text-white');
                        btn.classList.add('bg-transparent', 'text-gray-600');
                        
                        // Marcar como activo el que coincide con el tipo de display
                        if (btn.getAttribute('data-chart-type') === displayType) {
                            btn.classList.add('active', 'bg-[#611132]', 'text-white');
                            btn.classList.remove('bg-transparent', 'text-gray-600');
                        }
                    });
                });
            }

            // Sincronizar después de crear los gráficos
            setTimeout(syncChartTypeButtons, 100);

            // Lógica para selectores de fecha condicionales
            const dateRangeSelect = document.querySelector('select');
            if (dateRangeSelect) {
                dateRangeSelect.addEventListener('change', function() {
                    // Lógica para mostrar/ocultar selectores condicionales
                    console.log('Rango seleccionado:', this.value);
                });
            }
        });
    </script>

    <!-- AGREGAR FONT AWESOME PARA LOS ÍCONOS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
@endsection

@push('styles')
<style>
/* Ensure chart canvases don't overflow their card containers */
.chart-canvas { 
    display: block;
    max-width: 100%;
    box-sizing: border-box;
}

/* For very narrow screens reduce height a bit */
@media (max-width: 640px) {
    .chart-canvas { height: 220px !important; }
}

/* Wrapper that constrains the chart area and hides overflow */
.chart-wrapper { position: relative; overflow: hidden; }
.chart-wrapper { position: relative; overflow: hidden; margin-bottom: 18px; z-index: 0; }
.chart-wrapper canvas { width: 100% !important; height: 100% !important; display: block; }
</style>
@endpush