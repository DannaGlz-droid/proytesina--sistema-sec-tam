<div class="bg-nav text-white w-full font-sans">
    <div class="flex items-center h-9 lg:h-11 px-3 lg:px-5">
        <button class="text-sm lg:text-base h-full hover:bg-[#9B4D6E] hover:text-[#E8CA8B] text-white py-1.5 px-6 lg:px-8 transition duration-200 ease-in-out flex items-center flex-shrink-0 relative after:hover:content-[''] after:hover:absolute after:hover:left-0 after:hover:right-0 after:hover:bottom-0 after:hover:h-0.5 after:hover:bg-[#DB9703]">
            Centro de control
        </button>
        
        <div class="relative h-full" id="nuevoRegistroMenu">
            <button class="text-sm lg:text-base h-full hover:bg-[#9B4D6E] hover:text-[#E8CA8B] text-white py-1.5 px-6 lg:px-8 transition duration-200 ease-in-out flex items-center flex-shrink-0 relative after:hover:content-[''] after:hover:absolute after:hover:left-0 after:hover:right-0 after:hover:bottom-0 after:hover:h-0.5 after:hover:bg-[#DB9703]">
                Nuevo registro
                <i class="fas fa-chevron-down text-xs ml-1 lg:ml-2 transition-transform duration-200" id="menuArrow"></i>
            </button>
            
            <!-- Menú desplegable -->
            <div class="absolute left-0 top-full mt-0 w-48 bg-white border border-[#404041] rounded-lg shadow-xl opacity-0 invisible transition-all duration-200 transform origin-top -translate-y-2 z-50" 
                 id="dropdownMenu">
                <div class="py-1">
                    <a href="#" class="block px-4 py-3 text-sm text-[#404041] hover:bg-[#611132] hover:text-white transition-all duration-200 border-b border-gray-100">
                        <i class="fas fa-car-side mr-2 w-4 text-center"></i>
                        Seguridad vial
                    </a>
                    <a href="#" class="block px-4 py-3 text-sm text-[#404041] hover:bg-[#611132] hover:text-white transition-all duration-200 border-b border-gray-100">
                        <i class="fas fa-chart-bar mr-2 w-4 text-center"></i>
                        Observatorio
                    </a>
                    <a href="#" class="block px-4 py-3 text-sm text-[#404041] hover:bg-[#611132] hover:text-white transition-all duration-200">
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