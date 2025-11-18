// Utils: toasts y parseResponse
function showToast(type, message, timeout = 4000) {
    try {
        const container = document.getElementById('toast-container');
        if (!container) {
            console.warn('No toast container found');
            alert(message);
            return;
        }
        const toastId = 'toast-' + Date.now() + '-' + Math.floor(Math.random()*1000);
        const bgClass = type === 'success' ? 'bg-success text-white' : (type === 'warning' ? 'bg-warning text-dark' : 'bg-danger text-white');
        const toastEl = document.createElement('div');
        toastEl.className = `toast ${bgClass}`;
        toastEl.role = 'alert';
        toastEl.ariaLive = 'assertive';
        toastEl.ariaAtomic = 'true';
        toastEl.id = toastId;
        toastEl.innerHTML = `
            <div class="d-flex">
                <div class="toast-body">${message}</div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        `;
        container.appendChild(toastEl);
        const bsToast = new bootstrap.Toast(toastEl, { delay: timeout });
        bsToast.show();
        toastEl.addEventListener('hidden.bs.toast', () => { toastEl.remove(); });
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
