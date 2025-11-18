// Navegación del sidebar y tooltips
function mostrarContenido(id) {
    document.querySelectorAll('.content > div').forEach(div => div.classList.add('d-none'));
    const section = document.getElementById(id);
    if (section) section.classList.remove('d-none');
    document.querySelectorAll('.nav-link').forEach(link => link.classList.remove('active'));
    try {
        const selector = `[onclick="mostrarContenido('${id}')"]`;
        const link = document.querySelector(selector);
        if (link) link.classList.add('active');
    } catch (e) {
        console.warn('No se pudo actualizar link activo:', e);
    }
}

// Sidebar toggle
const btnMenu = document.getElementById("btn-menu");
const sidebar = document.querySelector(".sidebar");
const linksSidebar = document.querySelectorAll(".sidebar .nav-link");
if (btnMenu) btnMenu.addEventListener("click", () => {
  if (!sidebar) return;
  sidebar.classList.toggle("active");
  if (sidebar.classList.contains("active")) {
    document.body.classList.add("sidebar-open");
  } else {
      document.body.classList.remove("sidebar-open");
  }
});
linksSidebar.forEach(link => {
  link.addEventListener("click", () => {
    if (!sidebar) return;
    sidebar.classList.remove("active");
    document.body.classList.remove("sidebar-open");
  });
});

// Tooltips
const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
const tooltipList = [...tooltipTriggerList].map(el => new bootstrap.Tooltip(el));

// Mostrar inicio por defecto
document.addEventListener('DOMContentLoaded', () => {
    try { mostrarContenido('inicio'); } catch (e) {}
});
