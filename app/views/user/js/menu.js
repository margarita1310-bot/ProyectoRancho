/*
 * menu.js
 * Carga y muestra productos del menú con filtros de categoría
 */

let productosCache = [];

document.addEventListener('DOMContentLoaded', () => {
    cargarProductos();
    configurarFiltros();
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
            container.innerHTML = '<p class="text-center">No hay productos disponibles.</p>';
            return;
        }

        productosCache = data;
        mostrarProductos(productosCache);

    } catch (error) {
        console.error('Error al cargar productos:', error);
        container.innerHTML = '<p class="text-center text-danger">Error al cargar el menú.</p>';
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
                if (cat.nombre === null) {
                    // Mostrar todos
                    mostrarProductos(productosCache);
                } else {
                    // Filtrar por categoría
                    const filtrados = productosCache.filter(p => 
                        p.categoria && p.categoria.toLowerCase() === cat.nombre.toLowerCase()
                    );
                    mostrarProductos(filtrados);
                }
                actualizarBotonActivo(cat.id);
            });
        }
    });
}

function mostrarProductos(productos) {
    const container = document.getElementById('menu-container');
    if (!container) return;

    if (productos.length === 0) {
        container.innerHTML = '<p class="text-center">No hay productos en esta categoría.</p>';
        return;
    }

    container.innerHTML = productos.map(producto => {
        // Determinar icono según categoría
        let icono = 'fa-circle';
        const cat = producto.categoria ? producto.categoria.toLowerCase() : '';
        if (cat === 'botellas') icono = 'fa-wine-bottle';
        else if (cat === 'shots') icono = 'fa-glass-whiskey';
        else if (cat === 'cubas') icono = 'fa-glass-martini-alt';
        else if (cat === 'cervezas') icono = 'fa-beer';
        else if (cat === 'cocteles') icono = 'fa-cocktail';
        else if (cat === 'tacos') icono = 'fa-utensils';

        return `
            <div class="col-md-4 col-lg-3 mb-4">
                <div class="card card-producto h-100">
                    <div class="card-body-producto">
                        <h5 class="card-title text-dark fw-bold">${producto.nombre}</h5>
                        ${producto.categoria ? `
                            <span class="badge badge-categoria">
                                ${producto.categoria}
                            </span>
                        ` : ''}
                        <div class="producto-precio">
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
