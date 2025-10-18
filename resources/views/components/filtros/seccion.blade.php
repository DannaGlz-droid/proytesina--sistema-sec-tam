@props(['icono', 'titulo', 'abierto' => true])

<div class="filter-section">
    <button class="filter-section-header flex items-center justify-between w-full text-left mb-2" type="button">
        <div class="flex items-center gap-2">
            <i class="fas fa-{{ $icono }} text-[#611132] text-sm"></i>
            <h4 class="font-semibold text-[#404041] text-sm font-lora">{{ $titulo }}</h4>
        </div>
        <i class="fas fa-chevron-down text-[#611132] text-xs transition-transform duration-300"></i>
    </button>
    <div class="filter-section-content transition-all duration-300 ease-in-out overflow-hidden" 
         style="{{ $abierto ? 'max-height: 500px; opacity: 1;' : 'max-height: 0; opacity: 0;' }}">
        <div class="space-y-2 py-1">
            {{ $slot }}
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Inicializar todas las secciones de filtro
    document.querySelectorAll('.filter-section').forEach(section => {
        const header = section.querySelector('.filter-section-header');
        const content = section.querySelector('.filter-section-content');
        const icon = header.querySelector('.fa-chevron-down');
        
        if (!header || !content) return;
        
        // Configurar estado inicial basado en el estilo actual
        const isCurrentlyOpen = content.style.maxHeight === '500px' || 
                               content.style.maxHeight === '' || 
                               content.style.maxHeight.includes('px');
        
        if (isCurrentlyOpen) {
            content.style.maxHeight = content.scrollHeight + 'px';
            content.style.opacity = '1';
            icon.style.transform = 'rotate(0deg)';
        } else {
            content.style.maxHeight = '0';
            content.style.opacity = '0';
            icon.style.transform = 'rotate(-90deg)';
        }

        header.addEventListener('click', function() {
            if (content.style.maxHeight && content.style.maxHeight !== '0px') {
                // Cerrar con animación
                content.style.maxHeight = '0';
                content.style.opacity = '0';
                content.style.paddingTop = '0';
                content.style.paddingBottom = '0';
                icon.style.transform = 'rotate(-90deg)';
            } else {
                // Abrir con animación
                content.style.maxHeight = content.scrollHeight + 'px';
                content.style.opacity = '1';
                content.style.paddingTop = '';
                content.style.paddingBottom = '';
                icon.style.transform = 'rotate(0deg)';
            }
        });
    });

    // Recalcular alturas cuando cambie el contenido (útil para responsive)
    window.addEventListener('resize', function() {
        document.querySelectorAll('.filter-section-content').forEach(content => {
            if (content.style.maxHeight && content.style.maxHeight !== '0px') {
                content.style.maxHeight = content.scrollHeight + 'px';
            }
        });
    });
});
</script>