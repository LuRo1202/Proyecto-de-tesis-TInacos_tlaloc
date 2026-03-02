<?php
// CONFIGURACIÓN TANQUES TLALOC - COMPATIBLE CON MAILER.PHP
namespace App\Mailer;

// 1. INFORMACIÓN DE LA EMPRESA
define('EMPRESA_NOMBRE', 'Tanques Tlaloc');
define('EMPRESA_TELEFONO', '+52 55 4017 5803');
define('EMPRESA_DIRECCION', 'Av Morelos Oriente 186 a, Colonia San Cristobal Centro, Ecatepec de Morelos, Estado de México');
define('EMPRESA_SITIO', 'https://tanquestlaloc.com');
define('EMPRESA_WHATSAPP', '5215540175803');

// 2. TU CORREO DESTINO
define('MI_CORREO_PRINCIPAL', 'rogeliolucas173@gmail.com');

// 3. CONFIGURACIÓN GMAIL SMTP
define('SMTP_ACTIVO', true);
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_USER', 'rogeliolucas173@gmail.com');
define('SMTP_PASS', 'fqqiqhuyquspnkei');
define('SMTP_SECURE', 'tls');

// 4. REMITENTE DEL CORREO
define('CORREO_FROM', 'rogeliolucas173@gmail.com');
define('CORREO_FROM_NAME', 'Tanques Tlaloc - Contacto Web');

// 5. ZONA HORARIA MÉXICO
date_default_timezone_set('America/Mexico_City');

// 6. MODO DEBUG (FALSE para producción - esto evita el error)
define('DEBUG_MODE', false);

// 7. COLORES PARA PLANTILLA DE CORREO (VERDES como tu diseño)
define('COLOR_PRIMARIO', '#7fad39');    // Verde principal
define('COLOR_SECUNDARIO', '#5a8a20');  // Verde oscuro
define('COLOR_FONDO', '#f0f4f8');
define('COLOR_TEXTO', '#2D3748');

// Configuración de errores
if (DEBUG_MODE) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}

// 8. INFORMACIÓN ADICIONAL PARA EL SITIO
define('TELEFONOS', [
    '55 4017 5803',
    '444 184 4270', 
    '81 8654 0464'
]);

define('CORREO_EMPRESA', 'tanquestlaloc@outlook.com');
define('SLOGAN', 'Creadores del Tinaco Bala - Especialistas en Rotomoldeo');
?>