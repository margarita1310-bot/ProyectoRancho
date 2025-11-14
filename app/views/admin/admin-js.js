//Navegacion del side-bar
function mostrarContenido(id) {
  // Oculta todas las secciones
  document.querySelectorAll('.content > div').forEach(div => div.classList.add('d-none'));
  
  // Muestra la seleccionada
  document.getElementById(id).classList.remove('d-none');
  
  // Actualiza la clase activa del sidebar
  document.querySelectorAll('.nav-link').forEach(link => link.classList.remove('active'));
  event.target.closest('.nav-link').classList.add('active');
}

//Validaciones del inicio de sesión
document.getElementById("login-form").addEventListener("submit", function(e) {
    e.preventDefault();

    const userValido = "admin";
    const passwordValida = "rancho123";

    const user = document.getElementById("user").value.trim();
    const password = document.getElementById("password").value.trim();

    const alerta = document.getElementById("alerta");

    //Validaciones
    if (user === userValido && password === passwordValida) {
        window.location.href = "/vista/admin.html";
    } else {
        alerta.textContent = "Usuario o contraseña incorrectos.";
        alerta.style.color = "#9A4530";
    }
});
//Accion al pasar por el icono de info
const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
const tooltipList = [...tooltipTriggerList].map(el => new bootstrap.Tooltip(el));

// Función genérica para abrir un modal
function abrirModal(modo, tipo, datos = null) {
    const titulo = document.getElementById(`titulo-modal-${tipo}`);
    const boton = document.getElementById(`btn-guardar-${tipo}`);
    const form = document.getElementById(`form-${tipo}`);
    const modal = document.getElementById(`modal-${tipo}`);

    //Configuración del titulo y botón según el modo
    if (modo === 'crear') {
        titulo.textContent = tipo === 'promociones' ? 'Nueva Promoción'
         : tipo === 'eventos' ? 'Nuevo Evento'
         : tipo === 'menu' ? 'Nuevo Ítem del Menú'
         : 'Nueva Mesa';
        boton.textContent = 'Guardar';
        form.reset();
    } else if (modo === 'editar' && datos) {
        titulo.textContent = tipo === 'promociones' ? 'Editar Promoción'
         : tipo === 'eventos' ? 'Editar Evento'
         : tipo === 'menu' ? 'Editar Ítem del Menú'
         : 'Editar Mesa';
        boton.textContent = 'Guardar cambios';
        // Rellenar el formulario con los datos existentes
        for (const key in datos) {
            const campo = document.getElementById(`${key}-${tipo}`);
            if (campo) campo.value = datos[key];
        }
    }
    modal.classList.add('activo');
}
// Función genérica para cerrar un modal
function cerrarModal(tipo) {
    const modal = document.getElementById(`modal-${tipo}`);
    modal.classList.remove('activo');
}

//Acciones para  eliminar elementos
function abrirDelete(tipo, nombre) {
    mostrarAlertaEliminar(tipo, nombre, () => {
        // Aquí pones lo que debe pasar al confirmar
        console.log(`${tipo} "${nombre}" eliminada correctamente`);
        
        // Si tienes una función real que elimina de la BD o de una lista, la llamas aquí:
        // eliminarElementoDeBD(tipo, nombre);
    });
}

function mostrarAlertaEliminar(tipo, nombre, callbackConfirmar) {
    const overlay = document.getElementById("delete-overlay");
    const title = document.getElementById("delete-title");
    const message = document.getElementById("delete-message");
    const btnConfirm = document.getElementById("btn-confirm-delete");
    
    title.textContent = `Eliminar ${tipo}`;
    message.textContent = `¿Deseas eliminar "${nombre}" de forma permanente? Esta acción no se puede deshacer.`;

    overlay.style.display = "flex";
    
    // Elimina listeners anteriores y agrega el nuevo
    btnConfirm.replaceWith(btnConfirm.cloneNode(true));
    const newBtn = document.getElementById("btn-confirm-delete");
    
    newBtn.addEventListener("click", () => {
        if (typeof callbackConfirmar === "function") callbackConfirmar();
        cerrarDelete();
    });
}

function cerrarDelete() {
    document.getElementById("delete-overlay").style.display = "none";
}