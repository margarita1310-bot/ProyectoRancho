
let productosCache = [];
let vistaCompleta = true;
let categoriaActual = null;

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
                    <div class="alert alert-info" role="alert">
                        <i class="bi bi-info-circle"></i> No hay productos disponibles en el menú.
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
                <div class="alert alert-danger" role="alert">
                    <i class="bi bi-x-circle"></i> <strong>Error al cargar el menú.</strong> Por favor, intenta más tarde.
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
    
    if (categoriaActual !== null) {
        productosFiltrados = productosCache.filter(p => 
            p.categoria && p.categoria.toLowerCase() === categoriaActual.toLowerCase()
        );
    }
    if (!vistaCompleta) {
        productosFiltrados = productosFiltrados.slice(0, 6);
    }
    
    mostrarProductos(productosFiltrados);
}

function mostrarProductos(productos) {
    const container = document.getElementById('menu-container');
    if (!container) return;

    if (productos.length === 0) {
        container.innerHTML = `
            <div class="col-12">
                <div class="alert alert-warning" role="alert">
                    <i class="bi bi-exclamation-triangle"></i> No hay productos disponibles en esta categoría.
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
    document.querySelectorAll('.btn-filter').forEach(btn => {
        btn.classList.remove('active');
    });
    const btnActivo = document.getElementById(btnId);
    if (btnActivo) btnActivo.classList.add('active');
}
