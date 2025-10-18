@extends('layouts.principal')
@section('title', 'Estadísticas')
@section('content')

    @include('components.header')
    @include('components.nav')

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
                    <div class="lg:w-80">
                        <x-filtros.estadisticas />
                    </div>

                    <!-- Columna Derecha - Gráficos (ESPACIO RESTANTE) -->
                    <div class="lg:flex-1">
                        
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
                                <canvas id="municipioChart"></canvas>
                            </x-graficos.tarjeta>
                        </x-graficos.categoria>

                        <!-- Categoría: Tendencia Temporal -->
                        <x-graficos.categoria 
                            titulo="Tendencia Temporal" 
                            icono="calendar-alt"
                        >
                            <x-graficos.tarjeta 
                                titulo="Tendencia Mensual" 
                                :tipos="['line', 'bar']" 
                                tipoInicial="line"
                                graficoId="tendenciaChart"
                            >
                                <canvas id="tendenciaChart"></canvas>
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
                                <canvas id="edadChart"></canvas>
                            </x-graficos.tarjeta>
                            
                            <x-graficos.tarjeta 
                                titulo="Distribución por Género" 
                                :tipos="['doughnut', 'pie', 'bar']" 
                                tipoInicial="doughnut"
                                graficoId="generoChart"
                            >
                                <canvas id="generoChart"></canvas>
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
                                <canvas id="causaChart"></canvas>
                            </x-graficos.tarjeta>
                        </x-graficos.categoria>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Incluir Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- INCLUIR EL MODAL DE DESCARGAS -->
    @include('components.modal-descargas')

    <!-- Script para gráficos (ACTUALIZADO) -->
    <script>
        // Mapa de gráficos global para acceso desde el modal
        let charts = {};

        document.addEventListener('DOMContentLoaded', function() {
            // Inicializar gráficos
            const municipioCtx = document.getElementById('municipioChart').getContext('2d');
            const tendenciaCtx = document.getElementById('tendenciaChart').getContext('2d');
            const edadCtx = document.getElementById('edadChart').getContext('2d');
            const generoCtx = document.getElementById('generoChart').getContext('2d');
            const causaCtx = document.getElementById('causaChart').getContext('2d');
            
            // Datos de ejemplo
            const sampleData = {
                municipios: ['Monterrey', 'Guadalupe', 'San Nicolás', 'Apodaca', 'Escobedo', 'Santa Catarina', 'Allende'],
                municipioCounts: [125, 98, 76, 64, 52, 45, 38],
                meses: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'],
                mesCounts: [85, 92, 78, 105, 120, 135, 142, 138, 125, 110, 95, 88],
                edades: ['0-18', '19-35', '36-50', '51-65', '65+'],
                edadCounts: [45, 120, 185, 210, 175],
                generos: ['Hombre', 'Mujer'],
                generoCounts: [420, 315],
                causas: ['Accidente vial', 'Ahogamiento', 'Cáncer', 'COVID-19', 'Diabetes', 'Otras'],
                causaCounts: [210, 85, 170, 145, 130, 200]
            };
            
            // Crear gráficos
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
                    plugins: {
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
                        }
                    }
                }
            });

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
                        
                        // Cambiar tipo de gráfico
                        chart.config.type = chartType;
                        chart.update();
                    }
                });
            });

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