{{-- resources/views/cliente/dashboard.blade.php --}}
@php use App\Helpers\ProductoHelper; use App\Helpers\CarritoHelper; @endphp
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=yes">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Mi Cuenta | Tanques Tláloc</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@200;300;400;600;900&display=swap" rel="stylesheet">
    <!-- SweetAlert2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <!-- CSS Personalizado (TODOS LOS ESTILOS GLOBALES) -->
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    <link rel="icon" href="{{ asset('assets/img/logo.jpeg') }}">
    
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <style>
        /* ===== SOLO ESTILOS EXCLUSIVOS DEL DASHBOARD ===== */
        /* (NADA DEL HEADER, FOOTER O COMPONENTES GLOBALES) */
        
        /* VARIABLES GLOBALES - Solo las que necesita el dashboard */
        :root {
            --naranja: #ff6600;
            --naranja-oscuro: #e55a00;
            --gris-fondo: #f5f5f5;
            --gris-borde: #ebebeb;
            --texto-principal: #333;
            --texto-secundario: #666;
        }

        body {
            font-family: 'Cairo', sans-serif;
            background-color: var(--gris-fondo);
            color: var(--texto-principal);
        }

        /* ===== ESTILOS MÓVIL (hasta 991px) ===== */
        @media (max-width: 991px) {
            .dashboard-section {
                padding: 20px 0;
            }

            .container-custom {
                padding: 0 15px;
            }

            /* Welcome Card Móvil */
            .welcome-card-mobile {
                background: white;
                border-radius: 12px;
                margin: 15px 0;
                padding: 20px;
                box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
                border: 1px solid rgba(127, 173, 57, 0.1);
                position: relative;
                overflow: hidden;
            }

            .welcome-card-mobile::after {
                content: '';
                position: absolute;
                top: 0;
                left: 0;
                width: 4px;
                height: 100%;
                background: linear-gradient(to bottom, #7fad39, #ff6600);
            }

            .greeting-mobile {
                font-size: 1.5rem;
                font-weight: 700;
                margin-bottom: 5px;
                color: #333;
            }

            .greeting-mobile span {
                color: #7fad39;
            }

            /* Stats Grid Móvil */
            .stats-grid-mobile {
                display: grid;
                grid-template-columns: repeat(2, 1fr);
                gap: 10px;
                margin: 15px 0;
            }

            .stat-card-mobile {
                background: white;
                border-radius: 10px;
                padding: 15px;
                box-shadow: 0 2px 8px rgba(0,0,0,0.03);
                border: 1px solid #ebebeb;
                text-align: center;
            }

            .stat-icon-mobile {
                width: 45px;
                height: 45px;
                background: #f2f8eb;
                border-radius: 10px;
                display: flex;
                align-items: center;
                justify-content: center;
                margin: 0 auto 10px;
                color: #7fad39;
                font-size: 1.2rem;
            }

            .stat-number-mobile {
                font-size: 1.6rem;
                font-weight: 700;
                color: #333;
                line-height: 1.2;
            }

            .stat-label-mobile {
                font-size: 0.7rem;
                color: #666;
                font-weight: 500;
                text-transform: uppercase;
            }

            /* Actions Grid Móvil */
            .actions-section-mobile {
                margin: 15px 0;
            }

            .section-title-mobile {
                display: flex;
                justify-content: space-between;
                align-items: center;
                margin-bottom: 12px;
            }

            .section-title-mobile h3 {
                font-size: 1.1rem;
                font-weight: 700;
                color: #333;
                margin: 0;
            }

            .section-title-mobile a {
                color: #7fad39;
                text-decoration: none;
                font-size: 0.85rem;
                font-weight: 600;
            }

            .actions-grid-mobile {
                display: grid;
                grid-template-columns: repeat(4, 1fr);
                gap: 8px;
            }

            .action-btn-mobile {
                background: white;
                border: 1px solid #ebebeb;
                border-radius: 10px;
                padding: 10px 5px;
                text-align: center;
                text-decoration: none;
                color: #333;
                transition: all 0.2s;
            }

            .action-btn-mobile i {
                font-size: 1.3rem;
                color: #7fad39;
                display: block;
                margin-bottom: 3px;
            }

            .action-btn-mobile span {
                font-size: 0.65rem;
                font-weight: 600;
            }

            .action-btn-mobile:hover {
                background: #7fad39;
                color: white;
            }

            .action-btn-mobile:hover i {
                color: white;
            }

            /* Orders List Móvil */
            .orders-list-mobile {
                margin: 15px 0;
            }

            .order-item-mobile {
                background: white;
                border-radius: 12px;
                padding: 15px;
                margin-bottom: 10px;
                box-shadow: 0 2px 8px rgba(0,0,0,0.03);
                border: 1px solid #ebebeb;
                display: flex;
                align-items: center;
                gap: 12px;
                cursor: pointer;
            }

            .order-icon-mobile {
                width: 50px;
                height: 50px;
                background: #f2f8eb;
                border-radius: 10px;
                display: flex;
                align-items: center;
                justify-content: center;
                color: #7fad39;
                font-size: 1.2rem;
                flex-shrink: 0;
            }

            .order-info-mobile {
                flex: 1;
            }

            .order-header-mobile {
                display: flex;
                justify-content: space-between;
                align-items: center;
                margin-bottom: 5px;
            }

            .order-folio-mobile {
                font-weight: 700;
                color: #333;
                font-size: 0.9rem;
            }

            .order-date-mobile {
                font-size: 0.65rem;
                color: #666;
            }

            .order-footer-mobile {
                display: flex;
                justify-content: space-between;
                align-items: center;
            }

            .order-total-mobile {
                font-weight: 700;
                color: #7fad39;
                font-size: 0.95rem;
            }

            .order-status-mobile {
                font-size: 0.6rem;
                padding: 3px 8px;
                border-radius: 30px;
                font-weight: 600;
            }

            /* Account Card Móvil */
            .account-card-mobile {
                background: white;
                border-radius: 12px;
                margin: 15px 0;
                padding: 20px;
                box-shadow: 0 2px 8px rgba(0,0,0,0.03);
                border: 1px solid #ebebeb;
            }

            .account-field-mobile {
                margin-bottom: 15px;
                padding-bottom: 15px;
                border-bottom: 1px solid #ebebeb;
            }

            .account-field-mobile:last-child {
                border-bottom: none;
                margin-bottom: 0;
                padding-bottom: 0;
            }

            .field-label-mobile {
                font-size: 0.65rem;
                color: #999;
                text-transform: uppercase;
                letter-spacing: 0.3px;
                margin-bottom: 3px;
            }

            .field-value-mobile {
                font-weight: 600;
                color: #333;
                font-size: 0.95rem;
            }

            .btn-edit-mobile {
                background: #ff6600;
                color: white;
                border: none;
                border-radius: 8px;
                padding: 12px 20px;
                font-weight: 600;
                width: 100%;
                text-align: center;
                text-decoration: none;
                display: block;
                transition: all 0.2s;
                margin-top: 15px;
            }

            .btn-edit-mobile:hover {
                background: #e55a00;
                color: white;
            }

            .btn-edit-mobile i {
                margin-right: 8px;
            }

            /* Botón Flotante Móvil */
            .fab-mobile {
                position: fixed;
                bottom: 20px;
                right: 20px;
                width: 60px;
                height: 60px;
                background: #ff6600;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                color: white;
                font-size: 1.8rem;
                box-shadow: 0 5px 20px rgba(255, 102, 0, 0.4);
                text-decoration: none;
                transition: all 0.2s;
                z-index: 100;
                border: 3px solid white;
            }

            .fab-mobile:hover {
                transform: scale(1.1);
                background: #e55a00;
                color: white;
            }

            /* Ocultar elementos de desktop */
            .desktop-only {
                display: none;
            }
        }

        /* ===== ESTILOS DESKTOP (992px en adelante) ===== */
        @media (min-width: 992px) {
            .dashboard-section {
                padding: 40px 0;
            }

            .container-custom {
                max-width: 1200px;
                margin: 0 auto;
                padding: 0 20px;
            }

            /* Welcome Card Desktop */
            .welcome-dashboard {
                background: linear-gradient(135deg, rgba(0, 0, 0, 0.8) 0%, rgba(0, 0, 0, 0.7) 100%),
                            url('{{ asset('assets/img/hero/hero-foreground.png') }}') no-repeat center center;
                background-size: cover;
                border-radius: 12px;
                padding: 40px;
                color: white;
                margin-bottom: 30px;
                position: relative;
                overflow: hidden;
            }

            .welcome-dashboard::before {
                content: '💧';
                position: absolute;
                bottom: -20px;
                right: -20px;
                font-size: 150px;
                opacity: 0.1;
                transform: rotate(15deg);
            }

            .welcome-dashboard h1 {
                font-size: 2.5rem;
                font-weight: 700;
                margin-bottom: 10px;
            }

            .welcome-dashboard h1 span {
                color: #7fad39;
            }

            .welcome-dashboard p {
                font-size: 1.1rem;
                opacity: 0.9;
                margin-bottom: 0;
            }

            /* Stats Grid Desktop */
            .stats-grid {
                display: grid;
                grid-template-columns: repeat(4, 1fr);
                gap: 20px;
                margin-bottom: 40px;
            }

            .stat-card {
                background: white;
                border: 1px solid #ebebeb;
                border-radius: 12px;
                padding: 25px 20px;
                transition: all 0.3s ease;
                box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
                text-align: center;
            }

            .stat-card:hover {
                transform: translateY(-5px);
                box-shadow: 0 15px 30px rgba(127, 173, 57, 0.15);
                border-color: #7fad39;
            }

            .stat-icon {
                width: 60px;
                height: 60px;
                background: #f2f8eb;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                margin: 0 auto 15px;
                color: #7fad39;
                font-size: 1.5rem;
                transition: all 0.3s ease;
            }

            .stat-card:hover .stat-icon {
                background: #7fad39;
                color: white;
                transform: rotateY(360deg);
            }

            .stat-number {
                font-size: 2rem;
                font-weight: 800;
                color: #2c3e50;
                margin-bottom: 5px;
            }

            .stat-label {
                font-size: 0.85rem;
                color: #7f8c8d;
                font-weight: 600;
                text-transform: uppercase;
                letter-spacing: 0.5px;
            }

            /* Quick Actions Desktop */
            .quick-actions-grid {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
                gap: 20px;
                margin-bottom: 40px;
            }

            .quick-action-card {
                background: white;
                border-radius: 16px;
                overflow: hidden;
                box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
                transition: all 0.3s ease;
                position: relative;
                height: 180px;
                border: 1px solid #f0f0f0;
                text-decoration: none;
                display: block;
            }

            .quick-action-card:hover {
                transform: translateY(-8px);
                box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
                border-color: #7fad39;
            }

            .quick-action-overlay {
                height: 100%;
                display: flex;
                flex-direction: column;
                justify-content: center;
                align-items: center;
                padding: 20px;
                background: #ffffff;
                text-align: center;
                transition: all 0.3s ease;
            }

            .quick-action-overlay i {
                font-size: 2.5rem;
                margin-bottom: 12px;
                color: #7fad39;
                transition: transform 0.3s ease;
            }

            .quick-action-card:hover i {
                transform: scale(1.15);
            }

            .quick-action-overlay h4 {
                font-size: 1.1rem;
                font-weight: 700;
                margin: 0;
                color: #2d3436;
            }

            .quick-action-overlay p {
                font-size: 0.85rem;
                color: #636e72;
                margin-top: 5px;
            }

            /* Orders Section Desktop */
            .orders-section {
                margin-bottom: 40px;
                background: white;
                border-radius: 12px;
                padding: 25px;
                box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            }

            .section-header {
                display: flex;
                justify-content: space-between;
                align-items: center;
                margin-bottom: 20px;
            }

            .section-header h2 {
                font-size: 1.8rem;
                font-weight: 800;
                color: #2c3e50;
                position: relative;
                display: inline-block;
                margin: 0;
            }

            .section-header h2::after {
                content: '';
                position: absolute;
                bottom: -8px;
                left: 0;
                width: 60px;
                height: 4px;
                background: linear-gradient(to right, #7fad39, #ff6600);
                border-radius: 2px;
            }

            .section-header a {
                color: #7fad39;
                text-decoration: none;
                font-weight: 600;
                padding: 8px 20px;
                border: 2px solid #7fad39;
                border-radius: 5px;
                transition: all 0.3s;
            }

            .section-header a:hover {
                background: #7fad39;
                color: white;
            }

            .table-responsive {
                overflow-x: auto;
            }

            .table-custom {
                width: 100%;
                border-collapse: collapse;
            }

            .table-custom thead th {
                background: #f8f9fa;
                color: #2c3e50;
                font-weight: 700;
                border-bottom: 2px solid #7fad39;
                padding: 15px;
            }

            .table-custom td {
                padding: 15px;
                border-bottom: 1px solid #ebebeb;
            }

            .folio-badge {
                font-weight: 700;
                color: #7fad39;
            }

            .status-badge {
                padding: 5px 15px;
                border-radius: 20px;
                font-size: 0.8rem;
                font-weight: 600;
                display: inline-block;
            }

            .btn-action-table {
                width: 35px;
                height: 35px;
                border-radius: 8px;
                border: 1px solid #ebebeb;
                background: white;
                color: #7f8c8d;
                transition: all 0.3s;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                margin: 0 3px;
            }

            .btn-action-table:hover {
                background: #7fad39;
                color: white;
                border-color: #7fad39;
            }

            .btn-cancel:hover {
                background: #dc3545;
                color: white;
                border-color: #dc3545;
            }

            /* Account Section Desktop */
            .account-section {
                background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
                border-radius: 12px;
                padding: 30px;
                margin-top: 40px;
                box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            }

            .account-grid {
                display: grid;
                grid-template-columns: repeat(2, 1fr);
                gap: 20px;
                margin: 20px 0;
            }

            .account-item {
                background: white;
                border-radius: 10px;
                padding: 20px;
                box-shadow: 0 2px 8px rgba(0, 0, 0, 0.03);
                border-left: 4px solid #7fad39;
                transition: all 0.3s;
            }

            .account-item:hover {
                transform: translateY(-3px);
                box-shadow: 0 10px 20px rgba(127, 173, 57, 0.1);
            }

            .account-item i {
                font-size: 1.3rem;
                color: #7fad39;
                margin-bottom: 10px;
            }

            .account-item .label {
                font-size: 0.7rem;
                color: #7f8c8d;
                text-transform: uppercase;
                letter-spacing: 0.3px;
                margin-bottom: 3px;
            }

            .account-item .value {
                font-size: 1rem;
                font-weight: 600;
                color: #2c3e50;
            }

            .btn-edit-account {
                background: #ff6600;
                color: white;
                border: none;
                padding: 12px 30px;
                border-radius: 8px;
                font-weight: 600;
                transition: all 0.3s;
                display: inline-block;
                text-decoration: none;
            }

            .btn-edit-account:hover {
                background: #e55a00;
                transform: translateY(-2px);
                color: white;
            }

            .btn-edit-account i {
                margin-right: 8px;
            }

            /* Ocultar elementos móviles */
            .mobile-only {
                display: none;
            }

            .fab-mobile {
                display: none;
            }
        }

        /* Empty State compartido */
        .empty-state {
            text-align: center;
            padding: 40px 20px;
            background: white;
            border-radius: 12px;
        }

        .empty-state i {
            font-size: 3rem;
            color: #ebebeb;
            margin-bottom: 15px;
        }

        .empty-state p {
            color: #7f8c8d;
            margin-bottom: 20px;
        }

        /* ===== BUSCADOR SUPERIOR - MI CUENTA TLÁLOC ===== */
        .top-search-section {
            background: linear-gradient(135deg, #7fad39 0%, #5d8c29 100%);
            padding: 25px 0;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.15);
            position: relative;
            overflow: hidden;
        }

        /* Efecto de brillo animado */
        .top-search-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 100%;
            background: linear-gradient(45deg, 
                transparent 0%, 
                rgba(255, 255, 255, 0.08) 50%, 
                transparent 100%);
            animation: shine 3s infinite linear;
        }

        @keyframes shine {
            0% { transform: translateX(-100%); }
            100% { transform: translateX(100%); }
        }

        /* Línea decorativa inferior */
        .top-search-section::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, 
                transparent 0%, 
                rgba(255, 255, 255, 0.6) 50%, 
                transparent 100%);
        }

        .top-search-container {
            max-width: 800px;
            margin: 0 auto;
            text-align: center;
            color: white;
            position: relative;
            z-index: 1;
            padding: 0 15px;
        }

        /* Título principal */
        .top-search-container h4 {
            font-weight: 700;
            font-size: 2rem;
            margin-bottom: 10px;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.2);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .top-search-container h4 i {
            color: #ffdd40;
            font-size: 2rem;
            filter: drop-shadow(0 2px 3px rgba(0, 0, 0, 0.3));
            animation: bounce 2s infinite;
        }

        @keyframes bounce {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-5px); }
        }

        /* Subtítulo */
        .top-search-container p {
            color: rgba(255, 255, 255, 0.95);
            font-size: 1.2rem;
            margin-bottom: 0;
            font-weight: 400;
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.2);
            letter-spacing: 0.5px;
        }

        /* Responsive Móvil */
        @media (max-width: 768px) {
            .top-search-section {
                padding: 20px 0;
            }
            
            .top-search-container h4 {
                font-size: 1.6rem;
            }
            
            .top-search-container h4 i {
                font-size: 1.6rem;
            }
            
            .top-search-container p {
                font-size: 1rem;
            }
        }

        @media (max-width: 576px) {
            .top-search-section {
                padding: 15px 0;
            }
            
            .top-search-container h4 {
                font-size: 1.4rem;
                flex-direction: column;
                gap: 5px;
            }
            
            .top-search-container h4 i {
                font-size: 1.4rem;
            }
            
            .top-search-container p {
                font-size: 0.95rem;
                padding: 0 10px;
            }
        }

        /* Animación de entrada */
        .top-search-section {
            animation: fadeInDown 0.6s ease-out;
        }

        @keyframes fadeInDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* ===== TARJETA DE ACCIONES RÁPIDAS ===== */
        .quick-actions-container {
            background: white;
            border-radius: 20px;
            padding: 25px;
            margin-bottom: 40px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
            border: 1px solid rgba(127, 173, 57, 0.1);
            transition: all 0.3s ease;
        }

        .quick-actions-container:hover {
            box-shadow: 0 15px 40px rgba(127, 173, 57, 0.1);
            border-color: rgba(127, 173, 57, 0.2);
        }

        /* Header de la tarjeta */
        .quick-actions-header {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 25px;
            padding-bottom: 20px;
            border-bottom: 2px solid #f0f0f0;
            position: relative;
        }

        .quick-actions-header::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            width: 80px;
            height: 2px;
            background: linear-gradient(90deg, #7fad39, #ff6600);
            border-radius: 2px;
        }

        .quick-actions-header .header-icon {
            width: 50px;
            height: 50px;
            background: linear-gradient(135deg, #7fad39, #5d8c29);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.5rem;
            box-shadow: 0 5px 15px rgba(127, 173, 57, 0.3);
        }

        .quick-actions-header .header-text h3 {
            font-size: 1.4rem;
            font-weight: 700;
            color: #333;
            margin-bottom: 5px;
        }

        .quick-actions-header .header-text p {
            font-size: 0.9rem;
            color: #666;
            margin: 0;
        }

        /* Grid de acciones (tus cards existentes) */
        .quick-actions-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
        }

        /* Tus cards existentes (sin cambios) */
        .quick-action-card {
            background: #f8f9fa;
            border-radius: 16px;
            overflow: hidden;
            transition: all 0.3s ease;
            position: relative;
            height: 160px;
            border: 1px solid #f0f0f0;
            text-decoration: none;
            display: block;
        }

        .quick-action-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            border-color: #7fad39;
        }

        .quick-action-overlay {
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 20px;
            background: #ffffff;
            text-align: center;
            transition: all 0.3s ease;
        }

        .quick-action-overlay i {
            font-size: 2.2rem;
            margin-bottom: 10px;
            color: #7fad39;
            transition: transform 0.3s ease;
        }

        .quick-action-card:hover i {
            transform: scale(1.15);
        }

        .quick-action-overlay h4 {
            font-size: 1rem;
            font-weight: 700;
            margin-bottom: 5px;
            color: #333;
        }

        .quick-action-overlay p {
            font-size: 0.8rem;
            color: #666;
            margin: 0;
        }

        /* Color especial para botón de salir */
        .quick-action-card.card-salir:hover {
            border-color: #ff7675;
        }

        .quick-action-card.card-salir i {
            color: #ff7675;
        }

        /* Responsive para móvil */
        @media (max-width: 768px) {

        
            .quick-actions-container {
                padding: 15px;
                margin-bottom: 20px;
            }
            
            .quick-actions-header {
                margin-bottom: 15px;
                padding-bottom: 15px;
            }
            
            .quick-actions-header .header-icon {
                width: 40px;
                height: 40px;
                font-size: 1.2rem;
            }
            
            .quick-actions-header .header-text h3 {
                font-size: 1.2rem;
            }
            
            .quick-actions-header .header-text p {
                font-size: 0.8rem;
            }
            
            .quick-actions-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 10px;
            }
            
            .quick-action-card {
                height: 140px;
            }
            
            .quick-action-overlay i {
                font-size: 1.8rem;
            }
            
            .quick-action-overlay h4 {
                font-size: 0.9rem;
            }
            
            .quick-action-overlay p {
                font-size: 0.7rem;
            }
        }

        @media (max-width: 480px) {
            .quick-actions-grid {
                grid-template-columns: 1fr;
            }
            
            .quick-action-card {
                height: 120px;
            }
            
            .quick-action-overlay {
                flex-direction: row;
                justify-content: flex-start;
                gap: 15px;
                padding: 15px;
            }
            
            .quick-action-overlay i {
                margin-bottom: 0;
                font-size: 2rem;
            }
            
            .quick-action-overlay div {
                text-align: left;
            }
            
            .quick-action-overlay h4 {
                margin-bottom: 2px;
            }
        }

        /* ===== BADGES DE ESTADO PARA TABLA ===== */
.status-badge {
    padding: 6px 15px;
    border-radius: 30px;
    font-size: 0.8rem;
    font-weight: 600;
    display: inline-block;
    text-align: center;
    min-width: 100px;
}

/* Estado Pendiente */
.status-badge.status-pendiente {
    background: rgba(255, 193, 7, 0.15);
    color: #856404;
    border: 1px solid rgba(255, 193, 7, 0.3);
}

/* Estado Enviado */
.status-badge.status-enviado {
    background: rgba(127, 173, 57, 0.15);
    color: #7fad39;
    border: 1px solid rgba(127, 173, 57, 0.3);
}

/* Estado Entregado */
.status-badge.status-entregado {
    background: rgba(40, 167, 69, 0.15);
    color: #28a745;
    border: 1px solid rgba(40, 167, 69, 0.3);
}

/* Estado Cancelado */
.status-badge.status-cancelado {
    background: rgba(220, 53, 69, 0.15);
    color: #dc3545;
    border: 1px solid rgba(220, 53, 69, 0.3);
}

@media (max-width: 991px) {
    /* ... tus estilos móvil existentes ... */

    /* Badges de estado en móvil */
    .order-status-mobile {
        font-size: 0.6rem;
        padding: 4px 10px;
        border-radius: 30px;
        font-weight: 600;
        display: inline-block;
        min-width: 70px;
        text-align: center;
    }

    .status-pendiente {
        background: rgba(255, 193, 7, 0.15);
        color: #856404;
        border: 1px solid rgba(255, 193, 7, 0.3);
    }

    .status-enviado {
        background: rgba(127, 173, 57, 0.15);
        color: #7fad39;
        border: 1px solid rgba(127, 173, 57, 0.3);
    }

    .status-entregado {
        background: rgba(40, 167, 69, 0.15);
        color: #28a745;
        border: 1px solid rgba(40, 167, 69, 0.3);
    }

    .status-cancelado {
        background: rgba(220, 53, 69, 0.15);
        color: #dc3545;
        border: 1px solid rgba(220, 53, 69, 0.3);
    }

    /* Mejorar el footer de la tarjeta de pedido en móvil */
    .order-footer-mobile {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: 8px;
    }

    .order-total-mobile {
        font-weight: 700;
        color: #7fad39;
        font-size: 1rem;
    }
}

    </style>
</head>
<body>

    <!-- ===== HEADER COMPARTIDO ===== -->
    <nav class="navbar navbar-expand-lg navbar-light main-navbar sticky-top">
        <div class="container">
            <a class="navbar-brand" href="{{ route('home') }}">
                <img src="{{ asset('assets/img/logo-transparente.png') }}" alt="Tanques Tlaloc">
            </a>
            
            <div class="d-flex align-items-center">

                
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMain">
                    <span class="navbar-toggler-icon"></span>
                </button>
            </div>
            
            <div class="collapse navbar-collapse" id="navbarMain">
                <ul class="navbar-nav mx-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('home') }}">Inicio</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('tienda') }}">Tienda</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('tienda', ['categoria' => 2]) }}">Tinaco Bala</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('contacto') }}">Contacto</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="{{ route('cliente.dashboard') }}">Mi Cuenta</a>
                    </li>
                </ul>
                
                <div class="d-none d-lg-flex align-items-center">
                    @if(auth('cliente')->check())
                        <div class="dropdown me-3">
                            <button class="btn btn-outline-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                <i class="fas fa-user me-2"></i>
                                {{ auth('cliente')->user()->nombre }}
                            </button>
                            <ul class="dropdown-menu">
                                <li>
                                    <a class="dropdown-item active" href="{{ route('cliente.dashboard') }}">
                                        <i class="fas fa-tachometer-alt me-2"></i>Mi Cuenta
                                    </a>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="dropdown-item">
                                            <i class="fas fa-sign-out-alt me-2"></i>Cerrar Sesión
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-outline-primary me-3">
                            <i class="fas fa-user me-2"></i>Login
                        </a>
                    @endauth
                    
                    <a href="{{ route('carrito') }}" class="btn btn-primary position-relative">
                        <i class="fas fa-shopping-cart"></i>
                        <span class="cart-badge">{{ $cartCount ?? 0 }}</span>

                    </a>
                </div>
                
                <div class="d-lg-none mt-3">
                    @if(auth('cliente')->check())
                        <div class="d-grid gap-2">
                            <span class="btn btn-outline-primary w-100 mb-2 disabled">
                                <i class="fas fa-user me-2"></i>
                                {{ auth('cliente')->user()->nombre }}
                            </span>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="btn btn-danger w-100">
                                    <i class="fas fa-sign-out-alt me-2"></i>Cerrar Sesión
                                </button>
                            </form>
                        </div>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-outline-primary w-100 mb-2">
                            <i class="fas fa-user me-2"></i>Iniciar Sesión
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </nav>

    <!-- ===== BUSCADOR SUPERIOR COMPARTIDO ===== -->
    <section class="top-search-section">
        <div class="container">
            <div class="top-search-container">
                <h4>
                    <i class="fas fa-search me-2"></i>Mi Cuenta Tláloc
                </h4>
                <p>Gestiona tus pedidos y datos personales</p>
            </div>
        </div>
    </section>

    <!-- ===== DASHBOARD CONTENT ===== -->
    <section class="dashboard-section">
        <div class="container-custom">

            <!-- ===== VERSIÓN MÓVIL ===== -->
            <div class="mobile-only">

                <!-- WELCOME CARD MÓVIL -->
                <div class="welcome-card-mobile">
                    <div >
                    </div>
                    <div class="greeting-mobile">
                        ¡Hola, <span>{{ explode(' ', $cliente->nombre)[0] }}</span>!
                    </div>
                    <p style="color: #666; font-size: 0.9rem; margin-bottom: 0;">
                        Gestiona tus pedidos y datos personales
                    </p>
                </div>

                <!-- STATS MÓVIL -->
                <div class="stats-grid-mobile">
                    <div class="stat-card-mobile">
                        <div class="stat-icon-mobile">
                            <i class="fas fa-box-open"></i>
                        </div>
                        <div class="stat-number-mobile">{{ $estadisticas['total_pedidos'] }}</div>
                        <div class="stat-label-mobile">Pedidos</div>
                    </div>

                    <div class="stat-card-mobile">
                        <div class="stat-icon-mobile">
                            <i class="fas fa-clock"></i>
                        </div>
                        <div class="stat-number-mobile">{{ $estadisticas['pendientes'] + $estadisticas['enviados'] }}</div>
                        <div class="stat-label-mobile">Activos</div>
                    </div>

                    <div class="stat-card-mobile">
                        <div class="stat-icon-mobile">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <div class="stat-number-mobile">{{ $estadisticas['entregados'] }}</div>
                        <div class="stat-label-mobile">Entregados</div>
                    </div>

                    <div class="stat-card-mobile">
                        <div class="stat-icon-mobile">
                            <i class="fas fa-wallet"></i>
                        </div>
                        <div class="stat-number-mobile">${{ number_format($estadisticas['total_gastado'], 0) }}</div>
                        <div class="stat-label-mobile">Gastado</div>
                    </div>
                </div>

                <!-- ACCIONES RÁPIDAS MÓVIL -->
                <div class="actions-section-mobile">
                    <div class="section-title-mobile">
                        <h3>Acciones rápidas</h3>
                    </div>

                    <div class="actions-grid-mobile">
                        <a href="{{ route('tienda') }}" class="action-btn-mobile">
                            <i class="fas fa-store"></i>
                            <span>Tienda</span>
                        </a>
                        <a href="/cliente/pedidos" class="action-btn-mobile">
                            <i class="fas fa-truck"></i>
                            <span>Pedidos</span>
                        </a>
                        <a href="/cliente/completar-perfil" class="action-btn-mobile">
                            <i class="fas fa-user"></i>
                            <span>Perfil</span>
                        </a>
                        <a href="#" class="action-btn-mobile" onclick="confirmarLogout(event)">
                            <i class="fas fa-sign-out-alt"></i>
                            <span>Salir</span>
                        </a>
                    </div>
                </div>

                <!-- PEDIDOS RECIENTES MÓVIL -->
                <div class="actions-section-mobile">
                    <div class="section-title-mobile">
                        <h3>Pedidos recientes</h3>
                        <a href="/cliente/pedidos">Ver todos <i class="fas fa-chevron-right"></i></a>
                    </div>

                    <div class="orders-list-mobile">
                        @forelse($pedidosRecientes->take(3) as $pedido)
                        <div class="order-item-mobile" onclick="window.location.href='/cliente/pedido/{{ $pedido->id }}'">
                            <div class="order-icon-mobile">
                                @if($pedido->estado == 'entregado')
                                    <i class="fas fa-check-circle"></i>
                                @elseif($pedido->estado == 'pendiente')
                                    <i class="fas fa-clock"></i>
                                @elseif($pedido->estado == 'enviado')
                                    <i class="fas fa-truck"></i>
                                @else
                                    <i class="fas fa-box"></i>
                                @endif
                            </div>
                            <div class="order-info-mobile">
                                <div class="order-header-mobile">
                                    <span class="order-folio-mobile">#{{ $pedido->folio }}</span>
                                    <span class="order-date-mobile">{{ $pedido->created_at->format('d/m/Y') }}</span>
                                </div>
                                <div class="order-footer-mobile">
                                    <span class="order-total-mobile">${{ number_format($pedido->total, 0) }}</span>
                                    @php
                                        $statusClass = match($pedido->estado) {
                                            'pendiente' => 'status-pendiente',
                                            'enviado' => 'status-enviado',
                                            'entregado' => 'status-entregado',
                                            default => ''
                                        };
                                    @endphp
                                    <span class="order-status-mobile {{ $statusClass }}">{{ ucfirst($pedido->estado) }}</span>
                                </div>
                            </div>
                        </div>
                        @empty
                        <div class="empty-state">
                            <i class="fas fa-box-open"></i>
                            <p>Aún no tienes pedidos</p>
                            <a href="{{ route('tienda') }}" class="btn-primary">
                                <i class="fas fa-store me-2"></i>Ir a la tienda
                            </a>
                        </div>
                        @endforelse
                    </div>
                </div>

                <!-- DATOS DE CUENTA MÓVIL -->
                <div class="account-card-mobile">
                    <div class="section-title-mobile" style="margin-bottom: 15px;">
                        <h3>Mis datos</h3>
                    </div>

                    <div class="account-field-mobile">
                        <div class="field-label-mobile">Nombre completo</div>
                        <div class="field-value-mobile">{{ $cliente->nombre }}</div>
                    </div>

                    <div class="account-field-mobile">
                        <div class="field-label-mobile">Correo electrónico</div>
                        <div class="field-value-mobile">{{ $cliente->email }}</div>
                    </div>

                    <div class="account-field-mobile">
                        <div class="field-label-mobile">Teléfono</div>
                        <div class="field-value-mobile">{{ $cliente->telefono ?? 'No registrado' }}</div>
                    </div>

                    <div class="account-field-mobile">
                        <div class="field-label-mobile">Dirección de envío</div>
                        <div class="field-value-mobile">
                            @if($cliente->direccion)
                                {{ $cliente->direccion }}, {{ $cliente->ciudad }}, {{ $cliente->estado }}
                            @else
                                No registrada
                            @endif
                        </div>
                    </div>

                    <a href="/cliente/completar-perfil" class="btn-edit-mobile">
                        <i class="fas fa-edit"></i> Editar información
                    </a>
                </div>

                <!-- BOTÓN FLOTANTE MÓVIL -->
                <a href="{{ route('tienda') }}" class="fab-mobile">
                    <i class="fas fa-plus"></i>
                </a>

            </div> <!-- Fin mobile-only -->

            <!-- ===== VERSIÓN DESKTOP ===== -->
            <div class="desktop-only">

                <!-- WELCOME CARD DESKTOP -->
                <div class="welcome-dashboard">
                    <div >
                        
                  </div>
                    <h1>¡Hola, <span>{{ explode(' ', $cliente->nombre)[0] }}</span>!</h1>
                    <p>Bienvenido a tu panel de control. Aquí puedes gestionar todos tus pedidos y datos personales.</p>
                </div>

                <!-- STATS DESKTOP -->
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-icon"><i class="fas fa-box-open"></i></div>
                        <div class="stat-number">{{ $estadisticas['total_pedidos'] }}</div>
                        <div class="stat-label">Pedidos Totales</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon"><i class="fas fa-clock"></i></div>
                        <div class="stat-number">{{ $estadisticas['pendientes'] + $estadisticas['enviados'] }}</div>
                        <div class="stat-label">Pedidos Activos</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon"><i class="fas fa-check-circle"></i></div>
                        <div class="stat-number">{{ $estadisticas['entregados'] }}</div>
                        <div class="stat-label">Entregados</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon"><i class="fas fa-wallet"></i></div>
                        <div class="stat-number">${{ number_format($estadisticas['total_gastado'], 0) }}</div>
                        <div class="stat-label">Total Gastado</div>
                    </div>
                </div>

                <!-- ACCIONES RÁPIDAS DESKTOP -->
               
                <div class="quick-actions-container">
                    <div class="quick-actions-header">
                        <div class="header-icon">
                            <i class="fas fa-bolt"></i>
                        </div>
                        <div class="header-text">
                            <h3>Acciones Rápidas</h3>
                            <p>Lo que necesitas al alcance de un clic</p>
                        </div>
                    </div>
                    
                    <div class="quick-actions-grid">
                        <a href="{{ route('tienda') }}" class="quick-action-card card-tienda">
                            <div class="quick-action-overlay">
                                <i class="fas fa-store"></i>
                                <h4>Tienda</h4>
                                <p>Explorar productos</p>
                            </div>
                        </a>

                        <a href="{{ route('cliente.pedidos') }}" class="quick-action-card card-pedidos">
                            <div class="quick-action-overlay">
                                <i class="fas fa-truck"></i>
                                <h4>Mis Pedidos</h4>
                                <p>Rastreo y detalles</p>
                            </div>
                        </a>

                        <a href="{{ route('cliente.completar-perfil') }}" class="quick-action-card card-perfil">
                            <div class="quick-action-overlay">
                                <i class="fas fa-user-circle"></i>
                                <h4>Mi Perfil</h4>
                                <p>Gestionar cuenta</p>
                            </div>
                        </a>

                        <a href="#" class="quick-action-card card-salir" onclick="confirmarLogout(event)">
                            <div class="quick-action-overlay">
                                <i class="fas fa-power-off"></i>
                                <h4>Salir</h4>
                                <p>Cerrar sesión</p>
                            </div>
                        </a>
                    </div>
                </div>
                <!-- PEDIDOS RECIENTES DESKTOP -->
                <div class="orders-section">
                    <div class="section-header">
                        <h2>Pedidos Recientes</h2>
                        <a href="/cliente/pedidos">Ver Todos <i class="fas fa-arrow-right ms-2"></i></a>
                    </div>

                    @if($pedidosRecientes->count() > 0)
                    <div class="table-responsive">
                        <table class="table-custom">
                            <thead>
                                <tr>
                                    <th>Folio</th>
                                    <th>Fecha</th>
                                    <th>Total</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($pedidosRecientes as $pedido)
                                <tr>
                                    <td><span class="folio-badge">#{{ $pedido->folio }}</span></td>
                                    <td>{{ $pedido->created_at->format('d/m/Y') }}</td>
                                    <td>${{ number_format($pedido->total, 2) }}</td>
                                    <td>
                                        @php
                                            $statusClass = match($pedido->estado) {
                                                'pendiente' => 'status-pendiente',
                                                'enviado' => 'status-enviado',
                                                'entregado' => 'status-entregado',
                                                default => ''
                                            };
                                        @endphp
                                        <span class="status-badge {{ $statusClass }}">{{ ucfirst($pedido->estado) }}</span>
                                    </td>
                                    <td>
                                        <a href="/cliente/pedido/{{ $pedido->id }}" class="btn-action-table"><i class="fas fa-eye"></i></a>
                                        @if($pedido->estado == 'pendiente')
                                        <button class="btn-action-table btn-cancel" onclick="confirmarCancelacion({{ $pedido->id }})"><i class="fas fa-times"></i></button>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="empty-state">
                        <i class="fas fa-box-open"></i>
                        <p>Aún no has realizado ningún pedido</p>
                        <a href="{{ route('tienda') }}" class="btn-primary"><i class="fas fa-store me-2"></i>Ir a la Tienda</a>
                    </div>
                    @endif
                </div>

                <!-- DATOS DE CUENTA DESKTOP -->
                <div class="account-section">
                    <div class="section-header">
                        <h2>Mis Datos</h2>
                    </div>

                    <div class="account-grid">
                        <div class="account-item">
                            <i class="fas fa-user"></i>
                            <div class="label">Nombre Completo</div>
                            <div class="value">{{ $cliente->nombre }}</div>
                        </div>
                        <div class="account-item">
                            <i class="fas fa-envelope"></i>
                            <div class="label">Correo Electrónico</div>
                            <div class="value">{{ $cliente->email }}</div>
                        </div>
                        <div class="account-item">
                            <i class="fas fa-phone"></i>
                            <div class="label">Teléfono</div>
                            <div class="value">{{ $cliente->telefono ?? 'No registrado' }}</div>
                        </div>
                        <div class="account-item">
                            <i class="fas fa-map-marker-alt"></i>
                            <div class="label">Dirección de Envío</div>
                            <div class="value">
                                @if($cliente->direccion)
                                    {{ $cliente->direccion }}<br>
                                    {{ $cliente->ciudad }}, {{ $cliente->estado }}
                                @else
                                    No registrada
                                @endif
                            </div>
                        </div>
                    </div>

                    <a href="/cliente/completar-perfil" class="btn-edit-account">
                        <i class="fas fa-edit me-2"></i>Editar Información
                    </a>
                </div>

            </div> <!-- Fin desktop-only -->

        </div>
    </section>

    <!-- ===== FOOTER COMPARTIDO ===== -->
    <footer class="main-footer">
        <div class="container">
            <div class="row">
                <div class="col-md-4">
                    <div class="footer-brand">
                        <img src="{{ asset('assets/img/logo-transparente.png') }}" alt="Tanques Tlaloc">
                        <h5>Tanques Tlaloc</h5>
                        <p>Especialistas en ROTOMOLDEO</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="footer-links">
                        <h6>Enlaces Rápidos</h6>
                        <ul>
                            <li><a href="{{ route('tienda') }}">Tienda</a></li>
                            <li><a href="/cliente/pedidos">Mis Pedidos</a></li>
                            <li><a href="/cliente/completar-perfil">Mi Perfil</a></li>
                        </ul>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="footer-links">
                        <h6>Ayuda</h6>
                        <ul>
                            <li><a href="{{ route('contacto') }}">Contacto</a></li>
                            <li><a href="#" onclick="contactarWhatsApp(event)">Soporte WhatsApp</a></li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="row mt-4">
                <div class="col-12 text-center">
                    <p class="small text-white-50 mb-0">
                        &copy; {{ date('Y') }} Tanques Tlaloc. Todos los derechos reservados.
                    </p>
                </div>
            </div>
        </div>
    </footer>

    <!-- FORMULARIO LOGOUT -->
    <form id="logout-form" method="POST" action="{{ route('logout') }}" style="display: none;">
        @csrf
    </form>

    <!-- SCRIPTS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        function confirmarLogout(event) {
            event.preventDefault();
            Swal.fire({
                title: '¿Cerrar sesión?',
                text: '¿Estás seguro de que quieres salir?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#7fad39',
                cancelButtonColor: '#ff6600',
                confirmButtonText: 'Sí, salir',
                cancelButtonText: 'Cancelar',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('logout-form').submit();
                }
            });
        }

        function confirmarCancelacion(id) {
            Swal.fire({
                title: '¿Cancelar pedido?',
                text: 'Esta acción no se puede revertir',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#7fad39',
                confirmButtonText: 'Sí, cancelar',
                cancelButtonText: 'Volver'
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Cancelado',
                        text: 'El pedido ha sido cancelado',
                        timer: 2000,
                        showConfirmButton: false
                    });
                }
            });
        }

        function contactarWhatsApp(event) {
            event.preventDefault();
            window.open('https://wa.me/5215540175803', '_blank');
        }

        @if(session('success'))
            Swal.fire({
                icon: 'success',
                title: '¡Excelente!',
                text: '{{ session('success') }}',
                timer: 3000,
                showConfirmButton: false,
                toast: true,
                position: 'top-end'
            });
        @endif
    </script>
</body>
</html>