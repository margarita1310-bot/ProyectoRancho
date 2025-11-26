/*
 * menu.js
 * Carga y muestra productos del menú con filtros de categoría
 */

let productosCache = [];
let vistaCompleta = true; // Estado del toggle
let categoriaActual = null; // Categoría seleccionada

document.addEventListener('DOMContentLoaded', () => {
    cargarProductos();
    configurarFiltros();
    configurarToggleVista();
});

async function cargarProductos() {
    const container = document.getElementById('menu-container');
    if (!container) return;

    try {
        const response = await fetch('/app/controllers/UserController.php?action=getProductos', {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        });
        
        if (!response.ok) throw new Error('Error al cargar productos');
        
        const data = await response.json();
        
        if (!data || !Array.isArray(data)) {
            container.innerHTML = `
                <div class="col-12">
                    <div class="alert alert-info d-flex align-items-center" role="alert">
                        <svg class="bi flex-shrink-0 me-3" width="24" height="24" role="img" aria-label="Info:">
                            <use xlink:href="#info-fill"/>
                        </svg>
                        <div>
                            No hay productos disponibles en el menú.
                        </div>
                    </div>
                </div>
            `;
            return;
        }

        productosCache = data;
        mostrarProductos(productosCache);

    } catch (error) {
        console.error('Error al cargar productos:', error);
        container.innerHTML = `
            <div class="col-12">
                <div class="alert alert-danger d-flex align-items-center" role="alert">
                    <svg class="bi flex-shrink-0 me-3" width="24" height="24" role="img" aria-label="Error:">
                        <use xlink:href="#x-circle-fill"/>
                    </svg>
                    <div>
                        <strong>Error al cargar el menú.</strong> Por favor, intenta más tarde.
                    </div>
                </div>
            </div>
        `;
    }
}

function configurarFiltros() {
    const categorias = [
        { id: 'btn-todos', nombre: null },
        { id: 'btn-botellas', nombre: 'Botellas' },
        { id: 'btn-shots', nombre: 'Shots' },
        { id: 'btn-cubas', nombre: 'Cubas' },
        { id: 'btn-cervezas', nombre: 'Cervezas' },
        { id: 'btn-cocteles', nombre: 'Cocteles' },
        { id: 'btn-tacos', nombre: 'Tacos' }
    ];

    categorias.forEach(cat => {
        const btn = document.getElementById(cat.id);
        if (btn) {
            btn.addEventListener('click', () => {
                categoriaActual = cat.nombre;
                aplicarFiltros();
                actualizarBotonActivo(cat.id);
            });
        }
    });
}

function configurarToggleVista() {
    const toggle = document.getElementById('toggle-vista-completa');
    if (toggle) {
        toggle.addEventListener('change', (e) => {
            vistaCompleta = e.target.checked;
            aplicarFiltros();
        });
    }
}

function aplicarFiltros() {
    let productosFiltrados = productosCache;
    
    // Filtrar por categoría
    if (categoriaActual !== null) {
        productosFiltrados = productosCache.filter(p => 
            p.categoria && p.categoria.toLowerCase() === categoriaActual.toLowerCase()
        );
    }
    
    // Limitar cantidad si no es vista completa
    if (!vistaCompleta) {
        productosFiltrados = productosFiltrados.slice(0, 6); // Mostrar solo 6 productos
    }
    
    mostrarProductos(productosFiltrados);
}

function mostrarProductos(productos) {
    const container = document.getElementById('menu-container');
    if (!container) return;

    if (productos.length === 0) {
        container.innerHTML = `
            <div class="col-12">
                <div class="alert alert-warning d-flex align-items-center" role="alert">
                    <svg class="bi flex-shrink-0 me-3" width="24" height="24" role="img" aria-label="Warning:">
                        <use xlink:href="#exclamation-triangle-fill"/>
                    </svg>
                    <div>
                        <strong>No hay productos disponibles</strong> en esta categoría.
                    </div>
                </div>
            </div>
        `;
        return;
    }

    container.innerHTML = productos.map(producto => {
        return `
            <div class="col-md-6 col-lg-4 mb-3">
                <div class="card card-menu-simple">
                    <div class="card-body text-center">
                        <h5 class="producto-nombre">${producto.nombre}</h5>
                        <div class="producto-precio-simple">
                            $${parseFloat(producto.precio).toFixed(2)}
                        </div>
                    </div>
                </div>
            </div>
        `;
    }).join('');
}

function actualizarBotonActivo(btnId) {
    // Remover clase active de todos los botones
    document.querySelectorAll('.btn-filter').forEach(btn => {
        btn.classList.remove('active');
    });
    
    // Agregar clase active al botón seleccionado
    const btnActivo = document.getElementById(btnId);
    if (btnActivo) btnActivo.classList.add('active');
}
