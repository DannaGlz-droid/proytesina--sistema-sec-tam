<div id="toast-container" class="fixed bottom-4 right-4 space-y-2 z-50 pointer-events-none" style="max-width: 400px;"></div>

<script>
    window.showToast = function(message, type = 'success', duration = 3000) {
        const container = document.getElementById('toast-container');
        if (!container) return;

        // Colores según tipo
        const colors = {
            'success': { bg: 'bg-green-500', icon: 'fas fa-check-circle' },
            'error': { bg: 'bg-red-500', icon: 'fas fa-exclamation-circle' },
            'warning': { bg: 'bg-yellow-500', icon: 'fas fa-exclamation-triangle' },
            'info': { bg: 'bg-blue-500', icon: 'fas fa-info-circle' }
        };

        const config = colors[type] || colors['success'];

        // Crear elemento toast
        const toast = document.createElement('div');
        toast.className = `${config.bg} text-white px-4 py-3 rounded-lg shadow-lg flex items-center gap-3 pointer-events-auto animate-in fade-in slide-in-from-right-4 duration-300`;
        toast.innerHTML = `
            <i class="${config.icon} text-lg"></i>
            <span class="text-sm font-medium">${message}</span>
            <button onclick="this.parentElement.remove()" class="ml-auto opacity-70 hover:opacity-100 transition-opacity">
                <i class="fas fa-times"></i>
            </button>
        `;

        container.appendChild(toast);

        // Auto-remover después de duración especificada
        if (duration > 0) {
            setTimeout(() => {
                toast.style.animation = 'fadeOut 0.3s ease-out forwards';
                setTimeout(() => toast.remove(), 300);
            }, duration);
        }
    };

    // Agregue estilos de animación
    if (!document.getElementById('toast-styles')) {
        const style = document.createElement('style');
        style.id = 'toast-styles';
        style.textContent = `
            @keyframes fadeOut {
                to {
                    opacity: 0;
                    transform: translateX(100%);
                }
            }
        `;
        document.head.appendChild(style);
    }
</script>
