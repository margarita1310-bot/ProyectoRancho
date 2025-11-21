<?php
/**
 * index.php
 * Página principal de selección
 * Permite elegir entre el área de usuario o administración
 */
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rancho La Joya - Inicio</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        :root {
            --color-dark: #200D02;
            --color-primary: #854507;
            --color-secondary: #11422A;
            --color-accent: #9A4530;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Cabin', sans-serif;
            background: linear-gradient(135deg, var(--color-dark) 0%, var(--color-primary) 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .container-selection {
            max-width: 900px;
            width: 100%;
        }
        
        .logo-container {
            text-align: center;
            margin-bottom: 40px;
            animation: fadeInDown 0.8s ease;
        }
        
        .logo-container img {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            border: 4px solid white;
            box-shadow: 0 8px 20px rgba(0,0,0,0.3);
            margin-bottom: 20px;
        }
        
        .logo-container h1 {
            color: white;
            font-family: 'Playfair Display', serif;
            font-size: clamp(28px, 5vw, 42px);
            margin-bottom: 10px;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
        }
        
        .logo-container p {
            color: rgba(255,255,255,0.9);
            font-size: clamp(14px, 2vw, 18px);
        }
        
        .selection-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 30px;
            padding: 0 10px;
        }
        
        .selection-card {
            background: white;
            border-radius: 20px;
            padding: 40px 30px;
            text-align: center;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            transition: all 0.4s ease;
            cursor: pointer;
            text-decoration: none;
            color: var(--color-dark);
            animation: fadeInUp 0.8s ease;
        }
        
        .selection-card:nth-child(2) {
            animation-delay: 0.2s;
        }
        
        .selection-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 40px rgba(0,0,0,0.3);
        }
        
        .selection-card .icon {
            width: 80px;
            height: 80px;
            margin: 0 auto 20px;
            background: linear-gradient(135deg, var(--color-primary), var(--color-accent));
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 36px;
            color: white;
            transition: all 0.3s ease;
        }
        
        .selection-card:hover .icon {
            transform: rotate(360deg) scale(1.1);
        }
        
        .selection-card.admin-card .icon {
            background: linear-gradient(135deg, var(--color-secondary), var(--color-dark));
        }
        
        .selection-card h2 {
            font-family: 'Playfair Display', serif;
            font-size: clamp(22px, 3vw, 28px);
            color: var(--color-dark);
            margin-bottom: 15px;
        }
        
        .selection-card p {
            color: #666;
            font-size: clamp(14px, 2vw, 16px);
            line-height: 1.6;
        }
        
        .selection-card .btn-access {
            margin-top: 20px;
            padding: 12px 30px;
            border-radius: 25px;
            border: 2px solid var(--color-primary);
            background: var(--color-primary);
            color: white;
            font-weight: 600;
            transition: all 0.3s ease;
            display: inline-block;
        }
        
        .selection-card.admin-card .btn-access {
            background: var(--color-secondary);
            border-color: var(--color-secondary);
        }
        
        .selection-card:hover .btn-access {
            transform: scale(1.05);
        }
        
        @keyframes fadeInDown {
            from {
                opacity: 0;
                transform: translateY(-30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        @media (max-width: 768px) {
            .logo-container {
                margin-bottom: 30px;
            }
            
            .logo-container img {
                width: 100px;
                height: 100px;
            }
            
            .selection-cards {
                gap: 20px;
            }
            
            .selection-card {
                padding: 30px 20px;
            }
            
            .selection-card .icon {
                width: 70px;
                height: 70px;
                font-size: 32px;
            }
        }
    </style>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cabin:wght@400;600&family=Playfair+Display:wght@400;700&display=swap" rel="stylesheet">
</head>
<body>
    <div class="container-selection">
        <div class="logo-container">
            <img src="/public/images/logo.jpg" alt="Rancho La Joya Logo" onerror="this.style.display='none'">
            <h1>Rancho La Joya</h1>
            <p>Bienvenido, selecciona tu área de acceso</p>
        </div>
        
        <div class="selection-cards">
            <a href="index-user.php" class="selection-card">
                <div class="icon">
                    <i class="bi bi-house-heart"></i>
                </div>
                <h2>Área de Usuario</h2>
                <p>Explora nuestro menú, reserva una mesa y descubre nuestras promociones y eventos especiales.</p>
                <span class="btn-access">Acceder</span>
            </a>
            
            <a href="index-admin.php" class="selection-card admin-card">
                <div class="icon">
                    <i class="bi bi-shield-lock"></i>
                </div>
                <h2>Administración</h2>
                <p>Panel de control para gestionar promociones, eventos, menú, reservas y mesas del restaurante.</p>
                <span class="btn-access">Ingresar</span>
            </a>
        </div>
    </div>
</body>
</html>
