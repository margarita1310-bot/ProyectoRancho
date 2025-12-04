function showToast(type, message, timeout = 4000) {
    try {
        // Verificar que Bootstrap esté disponible
        if (typeof bootstrap === 'undefined') {
            console.error('Bootstrap no está cargado');
            alert(message);
            return;
        }

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
        
        const toastId = 'toast-' + Date.now() + '-' + Math.floor(Math.random()*1000);
        const bgClass = type === 'success' ? 'bg-success text-white' : (type === 'warning' ? 'bg-warning text-dark' : 'bg-danger text-white');
        const closeButtonClass = type === 'warning' ? 'btn-close' : 'btn-close btn-close-white';
        
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
        
        container.appendChild(toastEl);
        
        const bsToast = new bootstrap.Toast(toastEl, { 
            delay: timeout,
            animation: true,
            autohide: true
        });
        
        bsToast.show();
        
        toastEl.addEventListener('hidden.bs.toast', () => { 
            toastEl.remove(); 
        });
        
        console.log('Toast mostrado:', message); // Para debug
    } catch (e) {
        console.error('showToast error', e);
        alert(message);
    }
}

async function parseResponse(response) {
    try {
        const contentType = (response.headers.get('content-type') || '').toLowerCase();
        if (contentType.includes('application/json')) {
            const json = await response.json();
            return json;
        }
        const text = await response.text();
        const snippet = (text || '').replace(/\s+/g, ' ').trim().slice(0, 300);
        showToast('error', snippet || `Error ${response.status}`);
        throw new Error('Non-JSON response');
    } catch (e) {
        throw e;
    }
}
