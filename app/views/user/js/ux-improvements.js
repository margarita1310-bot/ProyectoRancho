
document.addEventListener('DOMContentLoaded', function() {
    initNavbarScroll();
    initSmoothScroll();
    initFormValidation();
    initLoadingAnimations();
    initTooltips();
});

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

function initSmoothScroll() {
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            const href = this.getAttribute('href');
            if (href === '#' || href === '') return;
            
            e.preventDefault();
            const target = document.querySelector(href);
            
            if (target) {
                target.style.animation = 'fadeIn 0.5s ease-out';
                
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
                
                setTimeout(() => {
                    target.style.animation = '';
                }, 500);
            }
        });
    });
}

function initFormValidation() {
    const formReserva = document.getElementById('form-reserva');
    if (!formReserva) return;

    const inputs = formReserva.querySelectorAll('input, select, textarea');
    
    inputs.forEach(input => {
        input.addEventListener('blur', function() {
            validateInput(this);
        });
        input.addEventListener('input', function() {
            this.classList.remove('is-invalid');
            const errorMsg = this.parentElement.querySelector('.invalid-feedback');
            if (errorMsg) errorMsg.remove();
        });
    });
}

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
        
        const contenedor = input.closest('.mb-3') || input.closest('.input-group')?.parentElement || input.parentElement;
        let errorDiv = contenedor.querySelector('.invalid-feedback');
        if (errorDiv) errorDiv.remove();
        errorDiv = document.createElement('div');
        errorDiv.className = 'invalid-feedback';
        errorDiv.style.display = 'block';
        errorDiv.textContent = errorMessage;
        const inputGroup = input.closest('.input-group');
        if (inputGroup) {
            inputGroup.parentElement.appendChild(errorDiv);
        } else {
            input.parentElement.appendChild(errorDiv);
        }
    } else if (value) {
        input.classList.add('is-valid');
        input.classList.remove('is-invalid');
        
        const contenedor = input.closest('.mb-3') || input.closest('.input-group')?.parentElement || input.parentElement;
        const errorMsg = contenedor.querySelector('.invalid-feedback');
        if (errorMsg) errorMsg.remove();
    }
}

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
    
    document.querySelectorAll('#reservar, #promociones, #eventos, #menu, #nosotros').forEach(section => {
        observer.observe(section);
    });
}

function initTooltips() {
    const tooltipElements = document.querySelectorAll('[data-bs-toggle="tooltip"]');
    tooltipElements.forEach(el => {
        new bootstrap.Tooltip(el);
    });
}

// Exportar solo validateInput para uso global
window.uxImprovements = {
    validateInput
};

