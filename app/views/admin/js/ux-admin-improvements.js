/**
 * ux-admin-improvements.js
 * Mejoras de experiencia de usuario para el panel de administración
 * - Loading states para tablas
 * - Confirmaciones mejoradas
 * - Tooltips informativos
 * - Animaciones de feedback
 */

document.addEventListener('DOMContentLoaded', function() {
    initAdminTooltips();
    initTableAnimations();
    initButtonFeedback();
    initModalAnimations();
});

/**
 * Inicializar tooltips en el panel admin
 */
function initAdminTooltips() {
    // Agregar tooltips a botones de acción
    const actionButtons = document.querySelectorAll('.btn-editar, .btn-eliminar, .btn');
    
    actionButtons.forEach(btn => {
        if (!btn.getAttribute('title') && !btn.getAttribute('data-bs-original-title')) {
            if (btn.classList.contains('btn-editar')) {
                btn.setAttribute('title', 'Editar este elemento');
                btn.setAttribute('data-bs-toggle', 'tooltip');
            } else if (btn.classList.contains('btn-eliminar')) {
                btn.setAttribute('title', 'Eliminar este elemento');
                btn.setAttribute('data-bs-toggle', 'tooltip');
            }
        }
    });
    
    // Inicializar tooltips de Bootstrap
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function(tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl, {
            delay: { show: 500, hide: 100 }
        });
    });
}

/**
 * Animaciones para filas de tabla al cargar
 */
function initTableAnimations() {
    const tables = document.querySelectorAll('.table tbody');
    
    tables.forEach(tbody => {
        const rows = tbody.querySelectorAll('tr');
        rows.forEach((row, index) => {
            row.style.animation = `fadeIn 0.3s ease-out ${index * 0.05}s both`;
        });
    });
}

/**
 * Feedback visual mejorado en botones
 */
function initButtonFeedback() {
    const buttons = document.querySelectorAll('.btn');
    
    buttons.forEach(btn => {
        btn.addEventListener('click', function(e) {
            // Efecto ripple
            const ripple = document.createElement('span');
            ripple.style.cssText = `
                position: absolute;
                border-radius: 50%;
                background: rgba(255, 255, 255, 0.6);
                width: 100px;
                height: 100px;
                margin-top: -50px;
                margin-left: -50px;
                animation: ripple 0.6s;
                pointer-events: none;
            `;
            
            const rect = this.getBoundingClientRect();
            ripple.style.left = (e.clientX - rect.left) + 'px';
            ripple.style.top = (e.clientY - rect.top) + 'px';
            
            this.appendChild(ripple);
            
            setTimeout(() => ripple.remove(), 600);
        });
    });
}

/**
 * Animaciones para modales
 */
function initModalAnimations() {
    const modals = document.querySelectorAll('.modal');
    
    modals.forEach(modal => {
        modal.addEventListener('show.bs.modal', function() {
            const modalDialog = this.querySelector('.modal-dialog');
            if (modalDialog) {
                modalDialog.style.animation = 'fadeInUp 0.3s ease-out';
            }
        });
        
        modal.addEventListener('shown.bs.modal', function() {
            // Focus en el primer input
            const firstInput = this.querySelector('input:not([type="hidden"]), select, textarea');
            if (firstInput && !firstInput.disabled) {
                setTimeout(() => firstInput.focus(), 100);
            }
        });
    });
}

/**
 * Mostrar loading en tabla
 */
function showTableLoading(tableId) {
    const table = document.querySelector(`#${tableId} tbody`);
    if (!table) return;
    
    table.innerHTML = `
        <tr>
            <td colspan="100%" class="text-center py-5">
                <div class="loading-container">
                    <div class="loading-spinner"></div>
                    <p class="loading-text">Cargando datos...</p>
                </div>
            </td>
        </tr>
    `;
}

/**
 * Mostrar mensaje cuando tabla está vacía
 */
function showEmptyTable(tableId, message = 'No hay datos para mostrar') {
    const table = document.querySelector(`#${tableId} tbody`);
    if (!table) return;
    
    table.innerHTML = `
        <tr>
            <td colspan="100%" class="text-center py-4">
                <div class="alert alert-info m-0" role="alert">
                    <i class="bi bi-info-circle"></i> ${message}
                </div>
            </td>
        </tr>
    `;
}

/**
 * Nota: showToast, confirmDelete y setButtonLoading han sido removidas.
 * Usar las funciones globales de utils.js en su lugar.
 */

/**
 * Validación de formulario con feedback visual
 */
function validateForm(formElement) {
    if (!formElement) return false;
    
    let isValid = true;
    const inputs = formElement.querySelectorAll('input[required], select[required], textarea[required]');
    
    inputs.forEach(input => {
        const value = input.value.trim();
        
        if (!value) {
            input.classList.add('is-invalid');
            isValid = false;
            
            // Agregar mensaje de error si no existe
            if (!input.nextElementSibling || !input.nextElementSibling.classList.contains('invalid-feedback')) {
                const errorDiv = document.createElement('div');
                errorDiv.className = 'invalid-feedback';
                errorDiv.textContent = 'Este campo es requerido';
                input.parentNode.insertBefore(errorDiv, input.nextSibling);
            }
        } else {
            input.classList.remove('is-invalid');
            input.classList.add('is-valid');
            
            // Remover mensaje de error
            const errorDiv = input.nextElementSibling;
            if (errorDiv && errorDiv.classList.contains('invalid-feedback')) {
                errorDiv.remove();
            }
        }
    });
    
    if (!isValid) {
        showToast('Por favor completa todos los campos requeridos', 'error');
    }
    
    return isValid;
}

// Agregar estilos para animación ripple si no existen
if (!document.getElementById('ripple-animation-styles')) {
    const style = document.createElement('style');
    style.id = 'ripple-animation-styles';
    style.textContent = `
        @keyframes ripple {
            from {
                opacity: 1;
                transform: scale(0);
            }
            to {
                opacity: 0;
                transform: scale(2);
            }
        }
    `;
    document.head.appendChild(style);
}

// Exportar funciones para uso global
window.adminUX = {
    showTableLoading,
    showEmptyTable,
    confirmDelete,
    showToast,
    setButtonLoading,
    validateForm
};
