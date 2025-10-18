<!-- components/modal-descargas.blade.php -->
<div id="modalDescargas" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden transition-opacity duration-300">
    <div class="bg-white border border-[#404041] rounded-lg lg:rounded-xl shadow-md transform transition-all duration-300 scale-95 opacity-0 max-w-2xl w-full mx-4">
        <!-- Header - AGREGADO rounded-t-lg lg:rounded-t-xl -->
        <div class="border-b border-gray-300 p-6 bg-white rounded-t-lg lg:rounded-t-xl">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-[#611132] rounded-lg flex items-center justify-center">
                        <i class="fas fa-download text-white text-lg"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-[#404041] font-lora">Opciones de descarga</h3>
                        <p class="text-sm text-gray-600 font-lora mt-1">Selecciona el formato de descarga</p>
                    </div>
                </div>
                <button class="modal-cerrar text-gray-500 hover:text-[#404041] transition-colors duration-200 p-1 rounded">
                    <i class="fas fa-times text-lg"></i>
                </button>
            </div>
        </div>

        <!-- Opciones de descarga HORIZONTALES - Solo iconos color A57F2C -->
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Opción PDF -->
                <button class="border border-[#404041] rounded-lg p-5 text-left transition-all duration-200 hover:border-[#404041] hover:bg-gray-50 group h-full" id="descargarPDF">
                    <div class="flex flex-col items-center text-center">
                        <div class="mb-4">
                            <i class="fas fa-file-pdf text-[#A57F2C] text-3xl"></i>
                        </div>
                        <h4 class="font-semibold text-[#404041] font-lora mb-2">Descargar en PDF</h4>
                        <p class="text-xs text-gray-600 font-lora">
                            Exporta las gráficas en PDF para reportes o impresión.
                        </p>
                    </div>
                </button>

                <!-- Opción PNG -->
                <button class="border border-[#404041] rounded-lg p-5 text-left transition-all duration-200 hover:border-[#404041] hover:bg-gray-50 group h-full" id="descargarPNG">
                    <div class="flex flex-col items-center text-center">
                        <div class="mb-4">
                            <i class="fas fa-image text-[#A57F2C] text-3xl"></i>
                        </div>
                        <h4 class="font-semibold text-[#404041] font-lora mb-2">Descargar en PNG</h4>
                        <p class="text-xs text-gray-600 font-lora">
                            Guarda las gráficas como imágenes para compartir o insertar.
                        </p>
                    </div>
                </button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('modalDescargas');
    const cerrarButtons = modal.querySelectorAll('.modal-cerrar');
    const descargarPDF = document.getElementById('descargarPDF');
    const descargarPNG = document.getElementById('descargarPNG');
    
    let tipoDescarga = 'todo'; // 'todo' o 'individual'
    let chartIdIndividual = '';

    // Función para mostrar modal
    function mostrarModal(tipo, chartId = '') {
        tipoDescarga = tipo;
        chartIdIndividual = chartId;
        
        modal.classList.remove('hidden');
        setTimeout(() => {
            const content = modal.querySelector('div > div');
            content.style.transform = 'scale(1)';
            content.style.opacity = '1';
        }, 50);
    }

    // Función para cerrar modal
    function cerrarModal() {
        const content = modal.querySelector('div > div');
        content.style.transform = 'scale(0.95)';
        content.style.opacity = '0';
        
        setTimeout(() => {
            modal.classList.add('hidden');
        }, 300);
    }

    // Eventos de cierre
    cerrarButtons.forEach(btn => {
        btn.addEventListener('click', cerrarModal);
    });

    // Cerrar al hacer click fuera
    modal.addEventListener('click', (e) => {
        if (e.target === modal) {
            cerrarModal();
        }
    });

    // Cerrar con ESC
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && !modal.classList.contains('hidden')) {
            cerrarModal();
        }
    });

    // Descargar PDF
    descargarPDF.addEventListener('click', function() {
        cerrarModal();
        setTimeout(() => {
            if (tipoDescarga === 'todo') {
                descargarTodoPDF();
            } else {
                descargarIndividualPDF(chartIdIndividual);
            }
        }, 300);
    });

    // Descargar PNG
    descargarPNG.addEventListener('click', function() {
        cerrarModal();
        setTimeout(() => {
            if (tipoDescarga === 'todo') {
                descargarTodoPNG();
            } else {
                descargarIndividualPNG(chartIdIndividual);
            }
        }, 300);
    });

    // Funciones de descarga PDF
    function descargarTodoPDF() {
        // Simular descarga PDF
        const link = document.createElement('a');
        link.href = '#';
        link.download = `estadisticas_completas_${new Date().toISOString().slice(0, 10)}.pdf`;
        link.click();
        
        console.log('📄 Descargando TODAS las gráficas en PDF...');
    }

    function descargarIndividualPDF(chartId) {
        // Simular descarga PDF individual
        const link = document.createElement('a');
        link.href = '#';
        link.download = `${chartId}_${new Date().toISOString().slice(0, 10)}.pdf`;
        link.click();
        
        console.log(`📄 Descargando gráfica ${chartId} en PDF...`);
    }

    // Funciones de descarga PNG (usando la lógica existente)
    function descargarTodoPNG() {
        Object.keys(charts).forEach((chartId, index) => {
            setTimeout(() => {
                const chart = charts[chartId];
                if (chart) {
                    const imageLink = document.createElement('a');
                    imageLink.href = chart.toBase64Image();
                    imageLink.download = `${chartId}_${new Date().toISOString().slice(0, 10)}.png`;
                    imageLink.click();
                }
            }, index * 300);
        });
    }

    function descargarIndividualPNG(chartId) {
        const chart = charts[chartId];
        if (chart) {
            const imageLink = document.createElement('a');
            imageLink.href = chart.toBase64Image();
            imageLink.download = `${chartId}_${new Date().toISOString().slice(0, 10)}.png`;
            imageLink.click();
        }
    }

    // Hacer funciones globales para que puedan ser llamadas desde otros scripts
    window.mostrarModalDescargas = mostrarModal;
});
</script>