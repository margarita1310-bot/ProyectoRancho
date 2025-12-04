/**
 * ux-admin-improvements.js
 * Mejoras de experiencia de usuario para el panel de administración
 * Incluye tooltips, animaciones, validación de formularios y feedback visual
 */

/**
 * Evento: Cuando el DOM está completamente cargado
 * Inicializa todas las mejoras de UX
 */
document.addEventListener('DOMContentLoaded', function () {
    initAdminTooltips();
    initTableAnimations();
    initButtonFeedback();
    initModalAnimations();
});

/**
 * Inicializar tooltips en el panel admin
 * Agrega tooltips a botones de acción y otros elementos interactivos
 *
 * @return {void}
 */
function initAdminTooltips() {
    // Obtener todos los botones de acción
    const actionButtons = document.querySelectorAll('.btn-editar, .btn-eliminar, .btn');

    // Agregar atributos de tooltip si no existen
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
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl, {
            delay: { show: 500, hide: 100 }
        });
    });
}

/**
 * Animaciones para filas de tabla al cargar
 * Crea efecto de fade-in escalonado
 *
 * @return {void}
 */
function initTableAnimations() {
    // Obtener todas las tablas
    const tables = document.querySelectorAll('.table tbody');

    // Agregar animación a cada fila
    tables.forEach(tbody => {
        const rows = tbody.querySelectorAll('tr');
        rows.forEach((row, index) => {
            row.style.animation = `fadeIn 0.3s ease-out ${index * 0.05}s both`;
        });
    });
}

/**
 * Feedback visual mejorado en botones
 * Agrega efecto ripple al hacer click
 *
 * @return {void}
 */
function initButtonFeedback() {
    // Obtener todos los botones
    const buttons = document.querySelectorAll('.btn');

    // Agregar listener a cada botón
    buttons.forEach(btn => {
        btn.addEventListener('click', function (e) {
            // Crear elemento ripple
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

            // Calcular posición del click
            const rect = this.getBoundingClientRect();
            ripple.style.left = (e.clientX - rect.left) + 'px';
            ripple.style.top = (e.clientY - rect.top) + 'px';

            // Agregar ripple al botón
            this.appendChild(ripple);

            // Remover ripple después de la animación
            setTimeout(() => ripple.remove(), 600);
        });
    });
}

/**
 * Animaciones para modales
 * Agrega transiciones al abrir/cerrar modales
 *
 * @return {void}
 */
function initModalAnimations() {
    // Obtener todos los modales
    const modals = document.querySelectorAll('.modal');

    // Agregar listeners a cada modal
    modals.forEach(modal => {
        modal.addEventListener('show.bs.modal', function () {
            const modalDialog = this.querySelector('.modal-dialog');
            if (modalDialog) {
                modalDialog.style.animation = 'fadeInUp 0.3s ease-out';
            }
        });

        modal.addEventListener('shown.bs.modal', function () {
            // Focus en el primer input cuando se abre el modal
            const firstInput = this.querySelector('input:not([type="hidden"]), select, textarea');
            if (firstInput && !firstInput.disabled) {
                setTimeout(() => firstInput.focus(), 100);
            }
        });
    });
}

/**
 * Mostrar loading en tabla
 * Reemplaza el contenido de una tabla con spinner
 *
 * @param {string} tableId ID de la tabla
 * @return {void}
 */
function showTableLoading(tableId) {
    // Obtener tbody
    const table = document.querySelector(`#${tableId} tbody`);
    if (!table) {
        return;
    }

    // Mostrar spinner
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
 * Reemplaza el contenido de una tabla con mensaje
 *
 * @param {string} tableId ID de la tabla
 * @param {string} message Mensaje a mostrar (default: "No hay datos para mostrar")
 * @return {void}
 */
function showEmptyTable(tableId, message = 'No hay datos para mostrar') {
    // Obtener tbody
    const table = document.querySelector(`#${tableId} tbody`);
    if (!table) {
        return;
    }

    // Mostrar mensaje
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
 * Validación de formulario con feedback visual
 * Valida campos requeridos y muestra mensajes de error
 *
 * @param {HTMLFormElement} formElement Elemento del formulario a validar
 * @return {boolean} True si el formulario es válido, false si no
 */
function validateForm(formElement) {
    // Validar que existe el formulario
    if (!formElement) {
        return false;
    }

    let isValid = true;

    // Obtener todos los campos requeridos
    const inputs = formElement.querySelectorAll('input[required], select[required], textarea[required]');

    // Validar cada campo
    inputs.forEach(input => {
        const value = input.value.trim();

        if (!value) {
            // Agregar clase de error
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
            // Remover clase de error y agregar validez
            input.classList.remove('is-invalid');
            input.classList.add('is-valid');

            // Remover mensaje de error
            const errorDiv = input.nextElementSibling;
            if (errorDiv && errorDiv.classList.contains('invalid-feedback')) {
                errorDiv.remove();
            }
        }
    });

    // Mostrar toast si hay errores
    if (!isValid) {
        showToast('error', 'Por favor completa todos los campos requeridos');
    }

    return isValid;
}

/**
 * Nota: Funciones como showToast, confirmDelete y setButtonLoading
 * se encuentran en utils.js y deben usarse desde ahí
 */
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
