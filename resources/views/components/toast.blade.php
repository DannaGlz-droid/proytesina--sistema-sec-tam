<div id="toast-container" class="fixed bottom-4 right-4 space-y-2 z-50 pointer-events-none" style="max-width: 400px;"></div>

<script>
    window.showToast = function(message, type = 'success', duration = 3000) {
        const container = document.getElementById('toast-container');
        if (!container) return;

        const variants = {
            success: { surface: 'bg-emerald-50 border-emerald-300', icon: 'fas fa-check-circle text-emerald-700' },
            error: { surface: 'bg-red-50 border-red-300', icon: 'fas fa-exclamation-circle text-red-700' },
            warning: { surface: 'bg-amber-50 border-amber-300', icon: 'fas fa-exclamation-triangle text-amber-700' },
            info: { surface: 'bg-blue-50 border-blue-300', icon: 'fas fa-info-circle text-blue-700' }
        };

        const config = variants[type] || variants.success;
        const toast = document.createElement('div');
        toast.className = `${config.surface} text-[#2f3337] border-l-4 border px-4 py-3 rounded-lg shadow-xl ring-1 ring-black/5 flex items-center gap-3 pointer-events-auto`;
        toast.style.animation = 'toastEnter 0.24s ease-out forwards';

        const icon = document.createElement('i');
        icon.className = `${config.icon} text-base flex-none`;

        const text = document.createElement('span');
        text.className = 'text-sm font-lora font-medium leading-snug';
        text.textContent = message;

        const closeButton = document.createElement('button');
        closeButton.type = 'button';
        closeButton.className = 'ml-auto text-gray-400 hover:text-gray-700 transition-colors';
        closeButton.setAttribute('aria-label', 'Cerrar notificacion');
        closeButton.innerHTML = '<i class="fas fa-times text-xs"></i>';
        closeButton.addEventListener('click', () => toast.remove());

        toast.append(icon, text, closeButton);
        container.appendChild(toast);

        if (duration > 0) {
            setTimeout(() => {
                toast.style.animation = 'fadeOut 0.3s ease-out forwards';
                setTimeout(() => toast.remove(), 300);
            }, duration);
        }
    };

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

            @keyframes toastEnter {
                from {
                    opacity: 0;
                    transform: translate3d(18px, 6px, 0) scale(0.98);
                }
                to {
                    opacity: 1;
                    transform: translate3d(0, 0, 0) scale(1);
                }
            }
        `;
        document.head.appendChild(style);
    }

    document.addEventListener('DOMContentLoaded', function() {
        @if(session('success'))
            window.showToast(@json(session('success')), 'success');
        @endif

        @if(session('error'))
            window.showToast(@json(session('error')), 'error');
        @endif

        @if(session('warning'))
            window.showToast(@json(session('warning')), 'warning');
        @endif

        @if(session('info'))
            window.showToast(@json(session('info')), 'info');
        @endif
    });
</script>
