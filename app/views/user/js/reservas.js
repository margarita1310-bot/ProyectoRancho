
document.addEventListener('DOMContentLoaded', () => {
    const formReserva = document.getElementById('form-reserva');
    const inputFecha = document.getElementById('fecha');
    const selectMesa = document.getElementById('id_mesa');
    const inputHora = document.getElementById('hora');
    
    if (formReserva) {
        formReserva.addEventListener('submit', manejarEnvioReserva);
    }

    if (inputFecha) {
        const hoy = new Date().toISOString().split('T')[0];
        inputFecha.setAttribute('min', hoy);
        inputFecha.addEventListener('change', (e) => { 
            aplicarHorarioPorDia();
            cargarMesasDisponibles();
        });
        if (inputFecha.value) aplicarHorarioPorDia();
    }

    if (inputHora && !inputFecha.value) {
        inputHora.disabled = true;
    }
    if (inputHora) {
        inputHora.addEventListener('change', validarHoraEnTiempoReal);
        inputHora.addEventListener('blur', normalizarFormatoHora);
        inputHora.addEventListener('change', normalizarFormatoHora);
    }
});

function normalizarFormatoHora(e) {
    const input = e.target;
    if (!input.value) return;
    const partes = input.value.split(':');
    if (partes.length === 3) {
        input.value = `${partes[0]}:${partes[1]}`;
    }
    if (partes.length >= 2) {
        const horas = String(partes[0]).padStart(2, '0');
        const minutos = String(partes[1]).padStart(2, '0');
        input.value = `${horas}:${minutos}`;
    }
}

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
    if (!inputFecha || !inputHora) return;

    inputHora.value = '';
    const val = inputFecha.value;
    if (!val) {
        inputHora.disabled = true;
        inputHora.removeAttribute('min');
        inputHora.removeAttribute('max');
        inputHora.removeAttribute('step');
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
        return;
    }
    inputHora.disabled = false;
    inputHora.setAttribute('min', cfg.min);
    inputHora.setAttribute('max', cfg.max);
    inputHora.setAttribute('step', '1800');
    
    const alertaHora = inputHora.closest('.mb-3').querySelector('.alert-info');
    if (alertaHora) {
        alertaHora.style.display = 'none';
    }
    inputHora.addEventListener('change', function() {
        const alertaHora = this.closest('.mb-3').querySelector('.alert-info');
        if (alertaHora && this.value) {
            alertaHora.style.display = 'none';
        }
    }, { once: true });
}

function validarHoraEnTiempoReal() {
    const inputFecha = document.getElementById('fecha');
    const inputHora = document.getElementById('hora');
    
    if (!inputFecha || !inputHora || !inputFecha.value || !inputHora.value) return;
    
    const fecha = inputFecha.value;
    const hora = inputHora.value;
    
    const d = new Date(fecha + 'T00:00:00');
    const dow = d.getDay();
    const cfg = HORARIO_DIA[dow];
    
    if (!cfg || !cfg.abierto) return;
    
    const toMin = (hm) => { const [h,m] = hm.split(':').map(Number); return h*60 + (m||0); };
    const horaMin = toMin(hora);
    const minPermitido = toMin(cfg.min);
    const maxPermitido = toMin(cfg.max);
    inputHora.classList.remove('is-valid', 'is-invalid');
    const contenedor = inputHora.parentElement.parentElement;
    const feedbacksAnteriores = contenedor.querySelectorAll('.invalid-feedback');
    feedbacksAnteriores.forEach(fb => fb.remove());
    
    if (horaMin < minPermitido || horaMin > maxPermitido) {
        inputHora.classList.add('is-invalid');
        const feedback = document.createElement('div');
        feedback.className = 'invalid-feedback';
        feedback.textContent = `La hora debe estar entre ${cfg.min} y ${cfg.max} para ${cfg.label.split(' ')[0]}.`;
        contenedor.appendChild(feedback);
    } else {
        inputHora.classList.add('is-valid');
        const feedbacksRestantes = contenedor.querySelectorAll('.invalid-feedback');
        feedbacksRestantes.forEach(fb => fb.remove());
    }
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
    
    const alertaMesa = selectMesa.closest('.mb-3').querySelector('.alert-info');
    if (alertaMesa) {
        alertaMesa.style.display = 'none';
    }
    
    try {
        selectMesa.innerHTML = '<option selected disabled value="">Cargando mesas...</option>';
        selectMesa.disabled = true;
        
        const response = await fetch(`../../../../app/controllers/ReservaController.php?action=obtenerMesasDisponiblesPorFecha&fecha=${fecha}`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        });
        
        console.log('Response status:', response.status);
        console.log('Response headers:', response.headers.get('content-type'));
        
        const responseText = await response.text();
        console.log('Response text:', responseText);
        
        if (!response.ok) {
            throw new Error(`Error del servidor: ${response.status}`);
        }
        
        let mesas;
        try {
            mesas = JSON.parse(responseText);
        } catch (e) {
            console.error('No se pudo parsear JSON:', e);
            console.error('Respuesta recibida:', responseText);
            throw new Error('El servidor no devolvió un JSON válido');
        }
        selectMesa.innerHTML = '';
        
        if (mesas.length === 0) {
            selectMesa.innerHTML = '<option selected disabled value="">No hay mesas disponibles para esta fecha</option>';
            return;
        }
        
        const optionDefault = document.createElement('option');
        optionDefault.value = '';
        optionDefault.textContent = 'Selecciona una mesa';
        optionDefault.selected = true;
        optionDefault.disabled = true;
        selectMesa.appendChild(optionDefault);
        mesas.forEach(mesa => {
            const option = document.createElement('option');
            option.value = mesa.id_mesa;
            option.textContent = `Mesa ${mesa.numero}`;
            selectMesa.appendChild(option);
        });
        selectMesa.disabled = false;
        selectMesa.addEventListener('change', function() {
            const alertaMesa = this.closest('.mb-3').querySelector('.alert-info');
            if (alertaMesa && this.value) {
                alertaMesa.style.display = 'none';
            }
        }, { once: true });
        
    } catch (error) {
        console.error('Error al cargar mesas:', error);
        selectMesa.innerHTML = '<option selected disabled value="">Error al cargar mesas</option>';
        selectMesa.disabled = false;
    }
}

async function manejarEnvioReserva(e) {
    e.preventDefault();

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

    if (!datos.nombre || !datos.email || !datos.telefono || !datos.personas || !datos.fecha || !datos.hora || !datos.id_mesa) {
        mostrarMensaje('Por favor completa todos los campos.', 'error');
        return;
    }

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

    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(datos.email)) {
        mostrarMensaje('Por favor ingresa un email válido.', 'error');
        return;
    }

    const telefonoRegex = /^\d{10}$/;
    if (!telefonoRegex.test(datos.telefono.replace(/\s|-/g, ''))) {
        mostrarMensaje('Por favor ingresa un teléfono válido de 10 dígitos.', 'error');
        return;
    }

    if (parseInt(datos.personas) < 1 || parseInt(datos.personas) > 10) {
        mostrarMensaje('El número de personas debe estar entre 1 y 10.', 'error');
        return;
    }

    try {
        const btnSubmit = e.target.querySelector('button[type="submit"]');
        if (btnSubmit) {
            btnSubmit.disabled = true;
            btnSubmit.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Enviando...';
        }

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
            
            e.target.reset();
            const selectMesa = document.getElementById('id_mesa');
            if (selectMesa) {
                selectMesa.innerHTML = '<option selected disabled value="">Primero selecciona una fecha</option>';
                selectMesa.disabled = false;
            }
            
            const inputHora = document.getElementById('hora');
            if (inputHora) {
                inputHora.disabled = true;
                inputHora.value = '';
                inputHora.removeAttribute('min');
                inputHora.removeAttribute('max');
                inputHora.removeAttribute('step');
                inputHora.classList.remove('is-valid', 'is-invalid');
            }
            
            const mesaContainer = document.getElementById('id_mesa');
            if (mesaContainer) {
                const alertaMesa = mesaContainer.closest('.mb-3').querySelector('.alert-info');
                if (alertaMesa) alertaMesa.style.display = '';
            }
            
            const horaContainer = document.getElementById('hora');
            if (horaContainer) {
                const alertaHora = horaContainer.closest('.mb-3').querySelector('.alert-info');
                if (alertaHora) alertaHora.style.display = '';
            }
        } else {
            throw new Error(resultado.mensaje || 'Error al crear reserva');
        }

        if (btnSubmit) {
            btnSubmit.disabled = false;
            btnSubmit.innerHTML = 'Reservar';
        }

    } catch (error) {
        console.error('Error al enviar reserva:', error);
        mostrarMensaje('Error al enviar la reserva. Por favor intenta de nuevo.', 'error');
        
        const btnSubmit = e.target.querySelector('button[type="submit"]');
        if (btnSubmit) {
            btnSubmit.disabled = false;
            btnSubmit.innerHTML = 'Reservar';
        }
    }
}

function mostrarMensaje(mensaje, tipo) {
    const alerta = document.createElement('div');
    alerta.className = `alert alert-${tipo === 'error' ? 'danger' : 'success'} alert-dismissible fade show position-fixed`;
    alerta.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
    alerta.innerHTML = `
        ${mensaje}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    document.body.appendChild(alerta);
    setTimeout(() => {
        alerta.remove();
    }, 5000);
}
