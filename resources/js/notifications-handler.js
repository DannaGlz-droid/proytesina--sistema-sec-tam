/**
 * Manejador global para abrir reportes desde notificaciones
 * Este archivo se incluye en todas las páginas para permitir navegar
 * a un reporte específico desde cualquier lugar de la aplicación
 */

window.openPublicationFromNotification = function(publicationId, commentId) {
    // Obtener la ruta actual
    const currentPath = window.location.pathname;
    
    // Si ya estamos en la página de publicaciones, simplemente buscar y hacer click
    if (currentPath === '/reportes/publicaciones') {
        try {
            const btn = document.querySelector(`button[data-publication-id="${publicationId}"]`);
            if (btn) {
                // Click the existing button to fill and show the modal
                btn.click();

                // Wait for modal to render comments and then scroll to the comment
                let tries = 0;
                const iv = setInterval(() => {
                    tries += 1;
                    // Find currently visible modal (not hidden)
                    const modal = Array.from(document.querySelectorAll('[id^="modal"]')).find(m => !m.classList.contains('hidden'));
                    if (modal) {
                        const commentEl = modal.querySelector(`[data-comment-id="${commentId}"]`);
                        if (commentEl) {
                            commentEl.scrollIntoView({ behavior: 'smooth', block: 'center' });
                            commentEl.style.transition = 'box-shadow 0.3s ease';
                            commentEl.style.boxShadow = '0 0 0 3px rgba(59,130,246,0.25)';
                            setTimeout(() => { commentEl.style.boxShadow = 'none'; }, 3500);
                            clearInterval(iv);
                        }
                    }
                    if (tries > 30) clearInterval(iv);
                }, 200);
                return;
            }
        } catch (e) {
            console.error('openPublicationFromNotification error:', e);
        }
    }
    
    // Si no estamos en /reportes/publicaciones, construir la URL con parámetros
    let url = '/reportes/publicaciones?publication=' + publicationId;
    if (commentId) {
        url += '&comment=' + commentId;
    }
    
    // Navegar a la página
    window.location = url;
};

// Alternativa: si el sitio usa SPA (Single Page App), este código ejecutaría sin recargar:
// Pero por ahora, el fallback es una navegación normal
