<div class="bg-nav text-white w-full font-sans">
    <div class="flex items-center h-9 lg:h-11 px-3 lg:px-5">
        <!-- Botón Centro de control -->
    <a href="{{ route('reportes.centro-de-control') }}" class="text-sm lg:text-base h-full px-4 lg:px-6 transition duration-200 ease-in-out flex items-center flex-shrink-0 relative group">
            <span class="relative py-1.5">
                Centro de control
                <span class="absolute bottom-0 left-0 w-0 h-0.5 bg-[#DB9703] transition-all duration-200 group-hover:w-full"></span>
            </span>
        </a>
        
        <!-- Botón Nuevo registro con dropdown -->
        <div class="relative h-full group" id="nuevoRegistroMenu">
            <button class="text-sm lg:text-base h-full px-4 lg:px-6 transition duration-200 ease-in-out flex items-center flex-shrink-0 group" id="menuButton">
                <span class="relative py-1.5 flex items-center">
                    Nuevo registro
                    <i class="fas fa-chevron-down text-xs ml-1 lg:ml-2 transition-transform duration-200" id="menuArrow"></i>
                    <span class="absolute bottom-0 left-0 w-0 h-0.5 bg-[#DB9703] transition-all duration-200 group-hover:w-full"></span>
                </span>
            </button>
            
            <!-- Menú desplegable -->
            <div class="absolute left-0 top-full mt-0 w-48 bg-white border border-[#404041] rounded-lg shadow-xl opacity-0 invisible transition-all duration-200 transform origin-top -translate-y-2 z-50" 
                 id="dropdownMenu">
                <div class="py-1">
                    <a href="{{ route('reportes.seguridad-vial') }}" class="block px-4 py-3 text-sm text-[#404041] hover:bg-[#611132] hover:text-white transition-all duration-200 border-b border-gray-100">
                        <i class="fas fa-car-side mr-2 w-4 text-center"></i>
                        Seguridad vial
                    </a>
                    <a href="{{ route('reportes.observatorio-de-lesiones') }}" class="block px-4 py-3 text-sm text-[#404041] hover:bg-[#611132] hover:text-white transition-all duration-200 border-b border-gray-100">
                        <i class="fas fa-chart-bar mr-2 w-4 text-center"></i>
                        Observatorio
                    </a>
                    <a href="{{ route('reportes.alcoholimetria') }}" class="block px-4 py-3 text-sm text-[#404041] hover:bg-[#611132] hover:text-white transition-all duration-200">
                        <i class="fas fa-vial mr-2 w-4 text-center"></i>
                        Alcoholimetría
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>


<script>
document.addEventListener('DOMContentLoaded', function() {
    const menuContainer = document.getElementById('nuevoRegistroMenu');
    const dropdownMenu = document.getElementById('dropdownMenu');
    const menuArrow = document.getElementById('menuArrow');
    
    menuContainer.addEventListener('mouseenter', function() {
        dropdownMenu.classList.remove('opacity-0', 'invisible', '-translate-y-2');
        dropdownMenu.classList.add('opacity-100', 'visible', 'translate-y-0');
        menuArrow.classList.add('rotate-180');
    });
    
    menuContainer.addEventListener('mouseleave', function() {
        dropdownMenu.classList.remove('opacity-100', 'visible', 'translate-y-0');
        dropdownMenu.classList.add('opacity-0', 'invisible', '-translate-y-2');
        menuArrow.classList.remove('rotate-180');
    });
});
</script>


<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">