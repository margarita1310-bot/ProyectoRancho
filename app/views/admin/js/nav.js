/**
 * navJS
 * Script para gestionar navegación y sidebar del panel administrativo
 * Incluye cambio de vistas, toggle del menú y tooltips de Bootstrap
 */

/**
 * Muestra una sección de contenido específica y actualiza link activo
 * Oculta todas las demás secciones
 *
 * @param {string} id ID de la sección a mostrar
 * @return {void}
 */
function mostrarContenido(id) {
    // Ocultar todas las secciones de contenido
    document.querySelectorAll('.content > div').forEach(div => {
        div.classList.add('d-none');
    });

    // Mostrar sección específica
    const section = document.getElementById(id);
    if (section) {
        section.classList.remove('d-none');
    }

    // Remover clase activa de todos los links
    document.querySelectorAll('.nav-link').forEach(link => {
        link.classList.remove('active');
    });

    // Agregar clase activa al link correspondiente
    try {
        const selector = `[onclick="mostrarContenido('${id}')"]`;
        const link = document.querySelector(selector);
        if (link) {
            link.classList.add('active');
        }
    } catch (e) {
        // Registrar advertencia si hay error al buscar el link
        console.warn('No se pudo actualizar link activo:', e);
    }
}

/**
 * Evento: Cuando el DOM está completamente cargado
 * Inicializa tooltips y contenido por defecto
 */
document.addEventListener('DOMContentLoaded', () => {
    try {
        // Mostrar sección de inicio por defecto
        mostrarContenido('inicio');
    } catch (e) {
        // Silenciar errores si no existe sección de inicio
    }
});

// Obtener referencias a elementos del DOM
const btnMenu = document.getElementById('btn-menu');
const sidebar = document.querySelector('.sidebar');
const linksSidebar = document.querySelectorAll('.sidebar .nav-link');

/**
 * Evento: Botón para toggle del sidebar
 * Abre/cierra el menú lateral
 */
if (btnMenu) {
    btnMenu.addEventListener('click', () => {
        if (!sidebar) {
            return;
        }

        // Toggle clase activa del sidebar
        sidebar.classList.toggle('active');

        // Agregar o remover clase del body
        if (sidebar.classList.contains('active')) {
            document.body.classList.add('sidebar-open');
        } else {
            document.body.classList.remove('sidebar-open');
        }
    });
}

/**
 * Evento: Links del sidebar
 * Cierra el menú cuando se hace click en un link
 */
linksSidebar.forEach(link => {
    link.addEventListener('click', () => {
        if (!sidebar) {
            return;
        }

        // Cerrar sidebar
        sidebar.classList.remove('active');
        document.body.classList.remove('sidebar-open');
    });
});

/**
 * Inicializar tooltips de Bootstrap
 * Aplica a todos los elementos con atributo data-bs-toggle="tooltip"
 */
const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
const tooltipList = [...tooltipTriggerList].map(el => new bootstrap.Tooltip(el));
