<?php
/**
 * diagnostic.php
 * Página de diagnóstico para validar la configuración
 * ELIMINAR O PROTEGER EN PRODUCCIÓN
 */

require_once __DIR__ . '/config/config.php';
require_once APP_ROOT . '/helpers/ConfigValidator.php';

// Solo permitir acceso local o con contraseña
$allowAccess = false;

if (!empty($_GET['key']) && $_GET['key'] === md5('rancho_lajoya_diagnostic')) {
    $allowAccess = true;
}

if (!$allowAccess && in_array($_SERVER['REMOTE_ADDR'], ['127.0.0.1', 'localhost', '::1'])) {
    $allowAccess = true;
}

if (!$allowAccess) {
    http_response_code(403);
    die('Acceso denegado');
}

$report = ConfigValidator::getReport();

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Diagnóstico del Sistema - Rancho La Joya</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }
        
        .container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            border-radius: 10px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
            overflow: hidden;
        }
        
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        
        .header h1 {
            margin-bottom: 10px;
        }
        
        .content {
            padding: 30px;
        }
        
        .check {
            display: flex;
            align-items: center;
            padding: 15px;
            margin-bottom: 15px;
            border-radius: 5px;
            background: #f5f5f5;
            border-left: 4px solid #999;
        }
        
        .check.success {
            background: #d4edda;
            border-left-color: #28a745;
        }
        
        .check.warning {
            background: #fff3cd;
            border-left-color: #ffc107;
        }
        
        .check.error {
            background: #f8d7da;
            border-left-color: #dc3545;
        }
        
        .check-icon {
            font-size: 24px;
            margin-right: 15px;
            min-width: 30px;
            text-align: center;
        }
        
        .check-content {
            flex: 1;
        }
        
        .check-title {
            font-weight: bold;
            margin-bottom: 5px;
        }
        
        .check-detail {
            font-size: 12px;
            color: #666;
        }
        
        .section-title {
            font-size: 18px;
            font-weight: bold;
            margin-top: 30px;
            margin-bottom: 15px;
            color: #333;
            border-bottom: 2px solid #667eea;
            padding-bottom: 10px;
        }
        
        .section-title:first-child {
            margin-top: 0;
        }
        
        .summary {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 5px;
            margin-bottom: 30px;
        }
        
        .summary-item {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid #e0e0e0;
        }
        
        .summary-item:last-child {
            border-bottom: none;
        }
        
        .summary-label {
            font-weight: bold;
        }
        
        .summary-value {
            text-align: right;
            color: #666;
        }
        
        .footer {
            background: #f8f9fa;
            padding: 20px 30px;
            text-align: center;
            font-size: 12px;
            color: #666;
            border-top: 1px solid #e0e0e0;
        }
        
        @media (max-width: 600px) {
            .header, .content, .footer {
                padding: 15px;
            }
            
            .check {
                flex-direction: column;
                text-align: center;
            }
            
            .check-icon {
                margin-right: 0;
                margin-bottom: 10px;
            }
            
            .summary-item {
                flex-direction: column;
            }
            
            .summary-value {
                text-align: left;
                margin-top: 5px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🔍 Diagnóstico del Sistema</h1>
            <p>Rancho La Joya - Verificación de Configuración</p>
        </div>
        
        <div class="content">
            <!-- Resumen General -->
            <div class="summary">
                <div class="summary-item">
                    <span class="summary-label">Versión PHP:</span>
                    <span class="summary-value"><?php echo $report['php_version']; ?></span>
                </div>
                <div class="summary-item">
                    <span class="summary-label">Servidor:</span>
                    <span class="summary-value"><?php echo htmlspecialchars($report['server_software']); ?></span>
                </div>
                <div class="summary-item">
                    <span class="summary-label">URL Base:</span>
                    <span class="summary-value"><?php echo htmlspecialchars($report['base_url']); ?></span>
                </div>
                <div class="summary-item">
                    <span class="summary-label">Fecha/Hora:</span>
                    <span class="summary-value"><?php echo date('d/m/Y H:i:s'); ?></span>
                </div>
            </div>
            
            <!-- Verificaciones -->
            <div class="section-title">✓ Verificaciones</div>
            
            <!-- PHP Version -->
            <div class="check <?php echo $report['php_version_ok'] ? 'success' : 'error'; ?>">
                <div class="check-icon"><?php echo $report['php_version_ok'] ? '✓' : '✗'; ?></div>
                <div class="check-content">
                    <div class="check-title">Versión de PHP</div>
                    <div class="check-detail">
                        Se requiere PHP 7.4+ (Tienes: <?php echo $report['php_version']; ?>)
                    </div>
                </div>
            </div>
            
            <!-- Extensiones -->
            <div class="check <?php echo $report['extensions_ok'] ? 'success' : 'error'; ?>">
                <div class="check-icon"><?php echo $report['extensions_ok'] ? '✓' : '✗'; ?></div>
                <div class="check-content">
                    <div class="check-title">Extensiones PHP Necesarias</div>
                    <div class="check-detail">
                        <?php 
                        if ($report['extensions_ok']) {
                            echo 'Todas las extensiones requeridas están instaladas';
                        } else {
                            echo 'Faltan: ' . implode(', ', $report['missing_extensions']);
                        }
                        ?>
                    </div>
                </div>
            </div>
            
            <!-- .env -->
            <div class="check <?php echo $report['env_file_ok'] ? 'success' : 'warning'; ?>">
                <div class="check-icon"><?php echo $report['env_file_ok'] ? '✓' : '⚠'; ?></div>
                <div class="check-content">
                    <div class="check-title">Archivo .env</div>
                    <div class="check-detail">
                        <?php echo $report['env_file_ok'] ? 'Archivo de configuración presente' : 'Archivo .env no encontrado. Copiar de .env.example'; ?>
                    </div>
                </div>
            </div>
            
            <!-- Base de Datos -->
            <div class="check <?php echo $report['database_ok'] ? 'success' : 'error'; ?>">
                <div class="check-icon"><?php echo $report['database_ok'] ? '✓' : '✗'; ?></div>
                <div class="check-content">
                    <div class="check-title">Conexión a Base de Datos</div>
                    <div class="check-detail">
                        <?php echo $report['database_ok'] ? 'Conexión exitosa' : 'No se puede conectar. Verificar credenciales en .env'; ?>
                    </div>
                </div>
            </div>
            
            <!-- Directorios escribibles -->
            <div class="check <?php echo $report['writable_dirs_ok'] ? 'success' : 'warning'; ?>">
                <div class="check-icon"><?php echo $report['writable_dirs_ok'] ? '✓' : '⚠'; ?></div>
                <div class="check-content">
                    <div class="check-title">Directorios Escribibles</div>
                    <div class="check-detail">
                        <?php echo $report['writable_dirs_ok'] ? 'Directorios con permisos adecuados' : 'Algunos directorios no tienen permisos de escritura'; ?>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="footer">
            <p>⚠️ Esta página de diagnóstico debe ser eliminada o protegida en producción</p>
            <p>Última actualización: <?php echo date('Y-m-d H:i:s'); ?></p>
        </div>
    </div>
</body>
</html>
