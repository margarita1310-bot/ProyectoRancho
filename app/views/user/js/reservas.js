/*
 * reservas.js
 * Maneja el formulario de reservas del usuario
 */

document.addEventListener('DOMContentLoaded', () => {
    const formReserva = document.getElementById('form-reserva');
    const inputFecha = document.getElementById('fecha');
    const selectMesa = document.getElementById('id_mesa');
    const inputHora = document.getElementById('hora');
    const helpHora = document.getElementById('hora-help');
    
    if (formReserva) {
        formReserva.addEventListener('submit', manejarEnvioReserva);
    }

    // Configurar fecha mínima (hoy)
    if (inputFecha) {
        const hoy = new Date().toISOString().split('T')[0];
        inputFecha.setAttribute('min', hoy);
        
        // Cargar mesas cuando cambie la fecha y aplicar horario
        inputFecha.addEventListener('change', (e) => { 
            aplicarHorarioPorDia();
            cargarMesasDisponibles();
        });
        // Si ya hay fecha cargada
        if (inputFecha.value) aplicarHorarioPorDia();
    }

    // Inicializar hora deshabilitada si no hay fecha
    if (inputHora && !inputFecha.value) {
        inputHora.disabled = true;
        if (helpHora) helpHora.textContent = 'Selecciona una fecha para ver el horario disponible.';
    }
});

// Horario por día (0=Domingo ... 6=Sábado)
const HORARIO_DIA = {
    0: { abierto: true,  min: '15:00', max: '21:00', label: 'Domingo 3:00 pm - 9:00 pm' },
    1: { abierto: true,  min: '11:00', max: '22:00', label: 'Lunes 11:00 am - 10:00 pm' },
    2: { abierto: true,  min: '10:00', max: '22:00', label: 'Martes 10:00 am - 10:00 pm' },
    3: { abierto: true,  min: '10:30', max: '22:00', label: 'Miércoles 10:30 am - 10:00 pm' },
    4: { abierto: true,  min: '11:00', max: '23:30', label: 'Jueves 11:00 am - 11:30 pm' },
    5: { abierto: true,  min: '11:00', max: '22:00', label: 'Viernes 11:00 am - 10:00 pm' },
    6: { abierto: false, label: 'Sábado cerrado' }
};

function aplicarHorarioPorDia() {
    const inputFecha = document.getElementById('fecha');
    const inputHora = document.getElementById('hora');
    const help = document.getElementById('hora-help');
    if (!inputFecha || !inputHora) return;

    inputHora.value = '';
    const val = inputFecha.value;
    if (!val) {
        inputHora.disabled = true;
        inputHora.removeAttribute('min');
        inputHora.removeAttribute('max');
        inputHora.removeAttribute('step');
        if (help) help.textContent = 'Selecciona una fecha para ver el horario disponible.';
        return;
    }
    const d = new Date(val + 'T00:00:00');
    const dow = d.getDay();
    const cfg = HORARIO_DIA[dow];
    if (!cfg || !cfg.abierto) {
        inputHora.disabled = true;
        inputHora.removeAttribute('min');
        inputHora.removeAttribute('max');
        inputHora.removeAttribute('step');
        if (help) help.textContent = cfg && cfg.label ? cfg.label : 'Cerrado';
        return;
    }
    inputHora.disabled = false;
    inputHora.setAttribute('min', cfg.min);
    inputHora.setAttribute('max', cfg.max);
    inputHora.setAttribute('step', '1800'); // 30 minutos
    if (help) help.textContent = 'Horario disponible: ' + cfg.label;
}

async function cargarMesasDisponibles() {
    const inputFecha = document.getElementById('fecha');
    const selectMesa = document.getElementById('id_mesa');
    
    if (!inputFecha || !selectMesa) return;
    
    const fecha = inputFecha.value;
    
    if (!fecha) {
        selectMesa.innerHTML = '<option selected disabled value="">Primero selecciona una fecha</option>';
        return;
    }
    
    try {
        // Mostrar mensaje de carga
        selectMesa.innerHTML = '<option selected disabled value="">Cargando mesas...</option>';
        selectMesa.disabled = true;
        
        const response = await fetch(`../../../../app/controllers/ReservaController.php?action=obtenerMesasDisponiblesPorFecha&fecha=${fecha}`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        });
        
        console.log('Response status:', response.status);
        console.log('Response headers:', response.headers.get('content-type'));
        
        // Leer la respuesta como texto primero para debugging
        const responseText = await response.text();
        console.log('Response text:', responseText);
        
        if (!response.ok) {
            throw new Error(`Error del servidor: ${response.status}`);
        }
        
        // Intentar parsear como JSON
        let mesas;
        try {
            mesas = JSON.parse(responseText);
        } catch (e) {
            console.error('No se pudo parsear JSON:', e);
            console.error('Respuesta recibida:', responseText);
            throw new Error('El servidor no devolvió un JSON válido');
        }
        
        // Limpiar select
        selectMesa.innerHTML = '';
        
        if (mesas.length === 0) {
            selectMesa.innerHTML = '<option selected disabled value="">No hay mesas disponibles para esta fecha</option>';
            return;
        }
        
        // Agregar opción por defecto
        const optionDefault = document.createElement('option');
        optionDefault.value = '';
        optionDefault.textContent = 'Selecciona una mesa';
        optionDefault.selected = true;
        optionDefault.disabled = true;
        selectMesa.appendChild(optionDefault);
        
        // Agregar mesas disponibles
        mesas.forEach(mesa => {
            const option = document.createElement('option');
            option.value = mesa.id_mesa;
            option.textContent = `Mesa ${mesa.numero}`;
            selectMesa.appendChild(option);
        });
        
        selectMesa.disabled = false;
        
    } catch (error) {
        console.error('Error al cargar mesas:', error);
        selectMesa.innerHTML = '<option selected disabled value="">Error al cargar mesas</option>';
        selectMesa.disabled = false;
    }
}

async function manejarEnvioReserva(e) {
    e.preventDefault();

    // Obtener datos del formulario
    const formData = new FormData(e.target);
    const datos = {
        nombre: formData.get('nombre'),
        email: formData.get('email'),
        telefono: formData.get('telefono'),
        personas: formData.get('personas'),
        fecha: formData.get('fecha'),
        hora: formData.get('hora'),
        id_mesa: formData.get('id_mesa')
    };

    // Validación básica
    if (!datos.nombre || !datos.email || !datos.telefono || !datos.personas || !datos.fecha || !datos.hora) {
        mostrarMensaje('Por favor completa todos los campos.', 'error');
        return;
    }

    // Validar hora dentro del horario del día
    const d = new Date(datos.fecha + 'T00:00:00');
    const dow = d.getDay();
    const cfg = HORARIO_DIA[dow] || { abierto: false };
    if (!cfg.abierto) {
        mostrarMensaje('El lugar está cerrado ese día. Selecciona otra fecha.', 'error');
        return;
    }
    const toMin = (hm) => { const [h,m] = hm.split(':').map(Number); return h*60 + (m||0); };
    const v = toMin(datos.hora);
    if (v < toMin(cfg.min) || v > toMin(cfg.max)) {
        mostrarMensaje(`La hora debe estar entre ${cfg.min} y ${cfg.max} para ese día.`, 'error');
        return;
    }

    // Validar email
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(datos.email)) {
        mostrarMensaje('Por favor ingresa un email válido.', 'error');
        return;
    }

    // Validar teléfono (10 dígitos)
    const telefonoRegex = /^\d{10}$/;
    if (!telefonoRegex.test(datos.telefono.replace(/\s|-/g, ''))) {
        mostrarMensaje('Por favor ingresa un teléfono válido de 10 dígitos.', 'error');
        return;
    }

    // Validar número de personas
    if (parseInt(datos.personas) < 1 || parseInt(datos.personas) > 20) {
        mostrarMensaje('El número de personas debe estar entre 1 y 20.', 'error');
        return;
    }

    try {
        // Deshabilitar botón de envío
        const btnSubmit = e.target.querySelector('button[type="submit"]');
        if (btnSubmit) {
            btnSubmit.disabled = true;
            btnSubmit.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Enviando...';
        }

        // Enviar al backend
        const response = await fetch('/app/controllers/UserController.php?action=crearReserva', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify(datos)
        });
        
        if (!response.ok) throw new Error('Error del servidor');
        
        const resultado = await response.json();
        
        if (resultado.status === 'ok') {
            mostrarMensaje('¡Reserva enviada exitosamente! Folio: ' + (resultado.folio || ''), 'success');
            
            // Limpiar formulario
            e.target.reset();
        } else {
            throw new Error(resultado.mensaje || 'Error al crear reserva');
        }

        // Restaurar botón
        if (btnSubmit) {
            btnSubmit.disabled = false;
            btnSubmit.innerHTML = 'Reservar';
        }

    } catch (error) {
        console.error('Error al enviar reserva:', error);
        mostrarMensaje('Error al enviar la reserva. Por favor intenta de nuevo.', 'error');
        
        // Restaurar botón
        const btnSubmit = e.target.querySelector('button[type="submit"]');
        if (btnSubmit) {
            btnSubmit.disabled = false;
            btnSubmit.innerHTML = 'Reservar';
        }
    }
}

function mostrarMensaje(mensaje, tipo) {
    // Crear elemento de alerta
    const alerta = document.createElement('div');
    alerta.className = `alert alert-${tipo === 'error' ? 'danger' : 'success'} alert-dismissible fade show position-fixed`;
    alerta.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
    alerta.innerHTML = `
        ${mensaje}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;

    // Agregar al body
    document.body.appendChild(alerta);

    // Remover después de 5 segundos
    setTimeout(() => {
        alerta.remove();
    }, 5000);
}
