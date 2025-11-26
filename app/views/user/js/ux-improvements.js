/**
 * ux-improvements.js
 * Mejoras de experiencia de usuario para la vista del usuario
 * - Navbar sticky animado
 * - Smooth scroll mejorado
 * - Loading states
 * - Animaciones de entrada
 */

document.addEventListener('DOMContentLoaded', function() {
    initNavbarScroll();
    initSmoothScroll();
    initFormValidation();
    initLoadingAnimations();
    initTooltips();
});

/**
 * Navbar con efecto al hacer scroll
 */
function initNavbarScroll() {
    const navbar = document.querySelector('.navbar');
    if (!navbar) return;

    let lastScroll = 0;
    
    window.addEventListener('scroll', () => {
        const currentScroll = window.pageYOffset;
        
        if (currentScroll > 100) {
            navbar.classList.add('scrolled');
        } else {
            navbar.classList.remove('scrolled');
        }
        
        lastScroll = currentScroll;
    });
}

/**
 * Smooth scroll mejorado con animación
 */
function initSmoothScroll() {
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            const href = this.getAttribute('href');
            if (href === '#' || href === '') return;
            
            e.preventDefault();
            const target = document.querySelector(href);
            
            if (target) {
                // Agregar clase de animación
                target.style.animation = 'fadeIn 0.5s ease-out';
                
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
                
                // Limpiar animación después
                setTimeout(() => {
                    target.style.animation = '';
                }, 500);
            }
        });
    });
}

/**
 * Validación visual en formularios
 */
function initFormValidation() {
    const formReserva = document.getElementById('form-reserva');
    if (!formReserva) return;

    const inputs = formReserva.querySelectorAll('input, select, textarea');
    
    inputs.forEach(input => {
        // Validar en tiempo real
        input.addEventListener('blur', function() {
            validateInput(this);
        });
        
        // Limpiar error al escribir
        input.addEventListener('input', function() {
            this.classList.remove('is-invalid');
            const errorMsg = this.parentElement.querySelector('.invalid-feedback');
            if (errorMsg) errorMsg.remove();
        });
    });
}

/**
 * Valida un input individual
 */
function validateInput(input) {
    const value = input.value.trim();
    let isValid = true;
    let errorMessage = '';
    
    if (input.hasAttribute('required') && !value) {
        isValid = false;
        errorMessage = 'Este campo es requerido';
    } else if (input.type === 'email' && value) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(value)) {
            isValid = false;
            errorMessage = 'Email inválido';
        }
    } else if (input.type === 'tel' && value) {
        const phoneRegex = /^[0-9]{10}$/;
        if (!phoneRegex.test(value.replace(/\s/g, ''))) {
            isValid = false;
            errorMessage = 'Teléfono debe tener 10 dígitos';
        }
    }
    
    if (!isValid) {
        input.classList.add('is-invalid');
        input.classList.remove('is-valid');
        
        // Agregar mensaje de error
        let errorDiv = input.parentElement.querySelector('.invalid-feedback');
        if (!errorDiv) {
            errorDiv = document.createElement('div');
            errorDiv.className = 'invalid-feedback';
            input.parentElement.appendChild(errorDiv);
        }
        errorDiv.textContent = errorMessage;
    } else if (value) {
        input.classList.add('is-valid');
        input.classList.remove('is-invalid');
        const errorMsg = input.parentElement.querySelector('.invalid-feedback');
        if (errorMsg) errorMsg.remove();
    }
}

/**
 * Animaciones de entrada para elementos visibles
 */
function initLoadingAnimations() {
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -100px 0px'
    };
    
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.animation = 'fadeInUp 0.6s ease-out';
                observer.unobserve(entry.target);
            }
        });
    }, observerOptions);
    
    // Observar secciones
    document.querySelectorAll('#reservar, #promociones, #eventos, #menu, #nosotros').forEach(section => {
        observer.observe(section);
    });
}

/**
 * Inicializar tooltips de Bootstrap
 */
function initTooltips() {
    // Agregar tooltips a botones e iconos
    const tooltipElements = document.querySelectorAll('[data-bs-toggle="tooltip"]');
    tooltipElements.forEach(el => {
        new bootstrap.Tooltip(el);
    });
}

/**
 * Mostrar loading spinner en contenedor
 */
function showLoading(container) {
    if (!container) return;
    
    const loadingHTML = `
        <div class="loading-container">
            <div class="loading-spinner"></div>
            <p class="loading-text">Cargando...</p>
        </div>
    `;
    
    container.innerHTML = loadingHTML;
}

/**
 * Mostrar mensaje de éxito con animación
 */
function showSuccessMessage(message, container) {
    if (!container) return;
    
    const successHTML = `
        <div class="alert alert-success alert-dismissible fade show" role="alert" style="animation: fadeInUp 0.5s ease-out;">
            <svg width="20" height="20" fill="currentColor" class="me-2" style="vertical-align: middle;">
                <use xlink:href="#info-fill"/>
            </svg>
            <strong>¡Éxito!</strong> ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    `;
    
    const div = document.createElement('div');
    div.innerHTML = successHTML;
    container.prepend(div.firstElementChild);
    
    // Auto-dismiss después de 5 segundos
    setTimeout(() => {
        const alert = container.querySelector('.alert');
        if (alert) {
            alert.classList.remove('show');
            setTimeout(() => alert.remove(), 300);
        }
    }, 5000);
}

/**
 * Mostrar mensaje de error con animación
 */
function showErrorMessage(message, container) {
    if (!container) return;
    
    const errorHTML = `
        <div class="alert alert-danger alert-dismissible fade show" role="alert" style="animation: fadeInUp 0.5s ease-out;">
            <svg width="20" height="20" fill="currentColor" class="me-2" style="vertical-align: middle;">
                <use xlink:href="#x-circle-fill"/>
            </svg>
            <strong>Error:</strong> ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    `;
    
    const div = document.createElement('div');
    div.innerHTML = errorHTML;
    container.prepend(div.firstElementChild);
    
    // Auto-dismiss después de 7 segundos
    setTimeout(() => {
        const alert = container.querySelector('.alert');
        if (alert) {
            alert.classList.remove('show');
            setTimeout(() => alert.remove(), 300);
        }
    }, 7000);
}

// Exportar funciones para uso global
window.uxImprovements = {
    showLoading,
    showSuccessMessage,
    showErrorMessage,
    validateInput
};
