/**
 * utilsJS
 * Script de utilidades compartidas para el panel administrativo
 * Incluye funciones para mostrar toasts y parsear respuestas del servidor
 */

/**
 * Muestra una notificación Toast (Toastify) con Bootstrap
 * Soporta tipos: success, warning, error
 *
 * @param {string} type Tipo de toast (success, warning, error)
 * @param {string} message Mensaje a mostrar
 * @param {number} timeout Duración en milisegundos (default: 4000)
 * @return {void}
 */
function showToast(type, message, timeout = 4000) {
    try {
        // Verificar que Bootstrap esté disponible
        if (typeof bootstrap === 'undefined') {
            console.error('Bootstrap no está cargado');
            alert(message);
            return;
        }

        // Obtener o crear contenedor de toasts
        let container = document.getElementById('toast-container');

        // Si no existe el contenedor, crearlo
        if (!container) {
            console.warn('No toast container found, creating one...');
            container = document.createElement('div');
            container.id = 'toast-container';
            container.className = 'toast-container position-fixed bottom-0 end-0 p-3';
            container.setAttribute('aria-live', 'polite');
            container.setAttribute('aria-atomic', 'true');
            container.style.zIndex = '9999';
            document.body.appendChild(container);
        }

        // Generar ID único para el toast
        const toastId = 'toast-' + Date.now() + '-' + Math.floor(Math.random() * 1000);

        // Determinar clases CSS según tipo
        const bgClass = type === 'success' ? 'bg-success text-white' : (type === 'warning' ? 'bg-warning text-dark' : 'bg-danger text-white');
        const closeButtonClass = type === 'warning' ? 'btn-close' : 'btn-close btn-close-white';

        // Crear elemento del toast
        const toastEl = document.createElement('div');
        toastEl.className = `toast ${bgClass}`;
        toastEl.role = 'alert';
        toastEl.setAttribute('aria-live', 'assertive');
        toastEl.setAttribute('aria-atomic', 'true');
        toastEl.id = toastId;
        toastEl.innerHTML = `
            <div class="d-flex">
                <div class="toast-body">${message}</div>
                <button type="button" class="${closeButtonClass} me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        `;

        // Agregar toast al contenedor
        container.appendChild(toastEl);

        // Crear instancia de Bootstrap Toast
        const bsToast = new bootstrap.Toast(toastEl, {
            delay: timeout,
            animation: true,
            autohide: true
        });

        // Mostrar toast
        bsToast.show();

        // Remover elemento del DOM cuando se oculte
        toastEl.addEventListener('hidden.bs.toast', () => {
            toastEl.remove();
        });

        // Log para debug
        console.log('Toast mostrado:', message);
    } catch (e) {
        // Fallback a alert si hay error
        console.error('showToast error', e);
        alert(message);
    }
}

/**
 * Parsea una respuesta del servidor
 * Detecta automáticamente si es JSON o texto
 *
 * @async
 * @param {Response} response Objeto Response del fetch
 * @return {Promise<Object|string>} Datos parseados
 * @throws {Error} Si la respuesta no es válida
 */
async function parseResponse(response) {
    try {
        // Obtener tipo de contenido
        const contentType = (response.headers.get('content-type') || '').toLowerCase();

        // Si es JSON, parsear como JSON
        if (contentType.includes('application/json')) {
            const json = await response.json();
            return json;
        }

        // Si no es JSON, leer como texto
        const text = await response.text();

        // Crear snippet de texto para mostrar (máx 300 caracteres)
        const snippet = (text || '').replace(/\s+/g, ' ').trim().slice(0, 300);

        // Mostrar error si hay texto
        showToast('error', snippet || `Error ${response.status}`);

        // Lanzar error
        throw new Error('Non-JSON response');
    } catch (e) {
        throw e;
    }
}
