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
    <!-- CSS Personalizado -->
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    <link rel="icon" href="{{ asset('assets/img/logo.jpeg') }}">
    
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <style>
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

        @media (max-width: 991px) {
            .dashboard-section { padding: 20px 0; }
            .container-custom { padding: 0 15px; }
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
            .greeting-mobile { font-size: 1.5rem; font-weight: 700; margin-bottom: 5px; color: #333; }
            .greeting-mobile span { color: #7fad39; }
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
            .stat-number-mobile { font-size: 1.6rem; font-weight: 700; color: #333; line-height: 1.2; }
            .stat-label-mobile { font-size: 0.7rem; color: #666; font-weight: 500; text-transform: uppercase; }
            .actions-section-mobile { margin: 15px 0; }
            .section-title-mobile {
                display: flex;
                justify-content: space-between;
                align-items: center;
                margin-bottom: 12px;
            }
            .section-title-mobile h3 { font-size: 1.1rem; font-weight: 700; color: #333; margin: 0; }
            .section-title-mobile a { color: #7fad39; text-decoration: none; font-size: 0.85rem; font-weight: 600; }
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
            .action-btn-mobile i { font-size: 1.3rem; color: #7fad39; display: block; margin-bottom: 3px; }
            .action-btn-mobile span { font-size: 0.65rem; font-weight: 600; }
            .action-btn-mobile:hover { background: #7fad39; color: white; }
            .action-btn-mobile:hover i { color: white; }
            .orders-list-mobile { margin: 15px 0; }
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
            .order-info-mobile { flex: 1; }
            .order-header-mobile {
                display: flex;
                justify-content: space-between;
                align-items: center;
                margin-bottom: 5px;
            }
            .order-folio-mobile { font-weight: 700; color: #333; font-size: 0.9rem; }
            .order-date-mobile { font-size: 0.65rem; color: #666; }
            .order-footer-mobile {
                display: flex;
                justify-content: space-between;
                align-items: center;
            }
            .order-total-mobile { font-weight: 700; color: #7fad39; font-size: 0.95rem; }
            .order-status-mobile {
                font-size: 0.6rem;
                padding: 4px 10px;
                border-radius: 30px;
                font-weight: 600;
                min-width: 70px;
                text-align: center;
            }
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
            .account-field-mobile:last-child { border-bottom: none; margin-bottom: 0; padding-bottom: 0; }
            .field-label-mobile { font-size: 0.65rem; color: #999; text-transform: uppercase; letter-spacing: 0.3px; margin-bottom: 3px; }
            .field-value-mobile { font-weight: 600; color: #333; font-size: 0.95rem; }
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
            .btn-edit-mobile:hover { background: #e55a00; color: white; }
            .btn-edit-mobile i { margin-right: 8px; }
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
            .fab-mobile:hover { transform: scale(1.1); background: #e55a00; color: white; }
            .desktop-only { display: none; }
        }

        @media (min-width: 992px) {
            .dashboard-section { padding: 40px 0; }
            .container-custom { max-width: 1200px; margin: 0 auto; padding: 0 20px; }
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
            .welcome-dashboard h1 { font-size: 2.5rem; font-weight: 700; margin-bottom: 10px; }
            .welcome-dashboard h1 span { color: #7fad39; }
            .welcome-dashboard p { font-size: 1.1rem; opacity: 0.9; margin-bottom: 0; }
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
            .stat-number { font-size: 2rem; font-weight: 800; color: #2c3e50; margin-bottom: 5px; }
            .stat-label { font-size: 0.85rem; color: #7f8c8d; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; }
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
            .quick-action-overlay i { font-size: 2.5rem; margin-bottom: 12px; color: #7fad39; transition: transform 0.3s ease; }
            .quick-action-card:hover i { transform: scale(1.15); }
            .quick-action-overlay h4 { font-size: 1.1rem; font-weight: 700; margin: 0; color: #2d3436; }
            .quick-action-overlay p { font-size: 0.85rem; color: #636e72; margin-top: 5px; }
            .orders-section {
                margin-bottom: 40px;
                background: white;
                border-radius: 16px;
                padding: 25px;
                box-shadow: 0 5px 20px rgba(0, 0, 0, 0.05);
            }
            .section-header {
                display: flex;
                justify-content: space-between;
                align-items: center;
                margin-bottom: 20px;
                flex-wrap: wrap;
                gap: 10px;
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
                display: inline-flex;
                align-items: center;
                gap: 5px;
            }
            .section-header a:hover { background: #7fad39; color: white; }
            .table-responsive { overflow-x: auto; border-radius: 12px; }
            .table-custom { width: 100%; border-collapse: collapse; }
            .table-custom thead th {
                background: #2c3e50;
                color: white;
                font-weight: 700;
                padding: 15px 20px;
                font-size: 0.9rem;
                text-transform: uppercase;
                letter-spacing: 0.5px;
                border: none;
            }
            .table-custom tbody tr {
                transition: all 0.2s ease;
                border-bottom: 1px solid #edf2f7;
            }
            .table-custom tbody tr:hover { background: #f8fafc; }
            .table-custom td { padding: 15px 20px; vertical-align: middle; font-size: 0.95rem; color: #2d3748; }
            .folio-badge {
                font-weight: 700;
                color: #7fad39;
                background: rgba(127, 173, 57, 0.1);
                padding: 6px 12px;
                border-radius: 8px;
                font-size: 0.9rem;
                display: inline-block;
                font-family: 'Courier New', monospace;
                border: 1px solid rgba(127, 173, 57, 0.2);
            }
            .action-buttons { display: flex; gap: 8px; align-items: center; }
            .btn-action-table {
                width: 38px;
                height: 38px;
                border-radius: 10px;
                border: none;
                background: white;
                color: #4a5568;
                transition: all 0.2s ease;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                text-decoration: none;
                font-size: 1rem;
                border: 1px solid #edf2f7;
            }
            .btn-view { background: #ebf8ff; color: #3182ce; border: 1px solid #bee3f8; }
            .btn-view:hover { background: #3182ce; color: white; border-color: #3182ce; }
            .btn-cancel { background: #fff5f5; color: #e53e3e; border: 1px solid #fed7d7; }
            .btn-cancel:hover { background: #e53e3e; color: white; border-color: #e53e3e; }
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
            .account-item i { font-size: 1.3rem; color: #7fad39; margin-bottom: 10px; }
            .account-item .label { font-size: 0.7rem; color: #7f8c8d; text-transform: uppercase; letter-spacing: 0.3px; margin-bottom: 3px; }
            .account-item .value { font-size: 1rem; font-weight: 600; color: #2c3e50; }
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
            .btn-edit-account:hover { background: #e55a00; transform: translateY(-2px); color: white; }
            .btn-edit-account i { margin-right: 8px; }
            .mobile-only { display: none; }
            .fab-mobile { display: none; }
        }

        .status-badge {
            padding: 8px 16px;
            border-radius: 30px;
            font-size: 0.85rem;
            font-weight: 600;
            display: inline-block;
            text-align: center;
            min-width: 110px;
            letter-spacing: 0.3px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
            transition: all 0.2s ease;
        }
        .status-badge:hover { transform: translateY(-2px); box-shadow: 0 5px 10px rgba(0,0,0,0.1); }
        .status-badge.status-pendiente { background: linear-gradient(135deg, #fff3cd 0%, #ffe69c 100%); color: #856404; border-left: 3px solid #ffc107; }
        .status-badge.status-confirmado { background: linear-gradient(135deg, #7ee7aa 0%, #5ba36a 100%); color: #0c5460; border-left: 3px solid #17a2b8; }
        .status-badge.status-enviado { background: linear-gradient(135deg, #cce5ff 0%, #b8daff 100%); color: #004085; border-left: 3px solid #007bff; }
        .status-badge.status-entregado { background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%); color: #155724; border-left: 3px solid #28a745; }
        .status-badge.status-cancelado { background: linear-gradient(135deg, #f8d7da 0%, #f5c2c7 100%); color: #721c24; border-left: 3px solid #dc3545; }

        .order-status-mobile {
            font-size: 0.6rem;
            padding: 4px 10px;
            border-radius: 30px;
            font-weight: 600;
            min-width: 70px;
            text-align: center;
        }
        .order-status-mobile.status-pendiente { background: #fff3cd; color: #856404; border: 1px solid #ffeaa7; }
        .order-status-mobile.status-confirmado { background: #d1ecf1; color: #0c5460; border: 1px solid #bee5eb; }
        .order-status-mobile.status-enviado { background: #cce5ff; color: #004085; border: 1px solid #b8daff; }
        .order-status-mobile.status-entregado { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .order-status-mobile.status-cancelado { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }

        .empty-state { text-align: center; padding: 40px 20px; background: white; border-radius: 12px; }
        .empty-state i { font-size: 3rem; color: #ebebeb; margin-bottom: 15px; }
        .empty-state p { color: #7f8c8d; margin-bottom: 20px; }

        .top-search-section {
            background: linear-gradient(135deg, #7fad39 0%, #5d8c29 100%);
            padding: 25px 0;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.15);
            position: relative;
            overflow: hidden;
        }
        .top-search-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 100%;
            background: linear-gradient(45deg, transparent 0%, rgba(255, 255, 255, 0.08) 50%, transparent 100%);
            animation: shine 3s infinite linear;
        }
        @keyframes shine {
            0% { transform: translateX(-100%); }
            100% { transform: translateX(100%); }
        }
        .top-search-section::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, transparent 0%, rgba(255, 255, 255, 0.6) 50%, transparent 100%);
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
        .top-search-container p {
            color: rgba(255, 255, 255, 0.95);
            font-size: 1.2rem;
            margin-bottom: 0;
            font-weight: 400;
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.2);
            letter-spacing: 0.5px;
        }
        @media (max-width: 768px) {
            .top-search-section { padding: 20px 0; }
            .top-search-container h4 { font-size: 1.6rem; }
            .top-search-container h4 i { font-size: 1.6rem; }
            .top-search-container p { font-size: 1rem; }
        }

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
        .quick-actions-header .header-text h3 { font-size: 1.4rem; font-weight: 700; color: #333; margin-bottom: 5px; }
        .quick-actions-header .header-text p { font-size: 0.9rem; color: #666; margin: 0; }
        .quick-actions-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
        }
        @media (max-width: 768px) {
            .quick-actions-container { padding: 15px; margin-bottom: 20px; }
            .quick-actions-header { margin-bottom: 15px; padding-bottom: 15px; }
            .quick-actions-header .header-icon { width: 40px; height: 40px; font-size: 1.2rem; }
            .quick-actions-header .header-text h3 { font-size: 1.2rem; }
            .quick-actions-grid { grid-template-columns: repeat(2, 1fr); gap: 10px; }
        }
        @media (max-width: 480px) { .quick-actions-grid { grid-template-columns: 1fr; } }
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-light main-navbar sticky-top">
        <div class="container">
            <a class="navbar-brand" href="{{ route('home') }}">
                <img src="{{ asset('assets/img/logo-transparente.png') }}" alt="Tanques Tlaloc">
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMain">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarMain">
                <ul class="navbar-nav mx-auto">
                    <li class="nav-item"><a class="nav-link" href="{{ route('home') }}">Inicio</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('tienda') }}">Tienda</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('tienda', ['categoria' => 2]) }}">Tinaco Bala</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('contacto') }}">Contacto</a></li>
                    <li class="nav-item"><a class="nav-link active" href="{{ route('cliente.dashboard') }}">Mi Cuenta</a></li>
                </ul>
                <div class="d-none d-lg-flex align-items-center">
                    @if(auth('cliente')->check())
                        <div class="dropdown me-3">
                            <button class="btn btn-outline-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                <i class="fas fa-user me-2"></i>{{ auth('cliente')->user()->nombre }}
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item active" href="{{ route('cliente.dashboard') }}"><i class="fas fa-tachometer-alt me-2"></i>Mi Cuenta</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="dropdown-item"><i class="fas fa-sign-out-alt me-2"></i>Cerrar Sesión</button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-outline-primary me-3"><i class="fas fa-user me-2"></i>Login</a>
                    @endauth
                    <a href="{{ route('carrito') }}" class="btn btn-primary position-relative">
                        <i class="fas fa-shopping-cart"></i>
                        <span class="cart-badge">{{ $cartCount ?? 0 }}</span>
                    </a>
                </div>
                <div class="d-lg-none mt-3">
                    @if(auth('cliente')->check())
                        <div class="d-grid gap-2">
                            <span class="btn btn-outline-primary w-100 mb-2 disabled"><i class="fas fa-user me-2"></i>{{ auth('cliente')->user()->nombre }}</span>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="btn btn-danger w-100"><i class="fas fa-sign-out-alt me-2"></i>Cerrar Sesión</button>
                            </form>
                        </div>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-outline-primary w-100 mb-2"><i class="fas fa-user me-2"></i>Iniciar Sesión</a>
                    @endif
                </div>
            </div>
        </div>
    </nav>

    <section class="top-search-section">
        <div class="container">
            <div class="top-search-container">
                <h4><i class="fas fa-search me-2"></i>Mi Cuenta Tláloc</h4>
                <p>Gestiona tus pedidos y datos personales</p>
            </div>
        </div>
    </section>

    <section class="dashboard-section">
        <div class="container-custom">

            <!-- VERSIÓN MÓVIL -->
            <div class="mobile-only">
                <div class="welcome-card-mobile">
                    <div class="greeting-mobile">¡Hola, <span>{{ explode(' ', $cliente->nombre)[0] }}</span>!</div>
                    <p style="color: #666; font-size: 0.9rem; margin-bottom: 0;">Gestiona tus pedidos y datos personales</p>
                </div>

                <div class="stats-grid-mobile">
                    <div class="stat-card-mobile"><div class="stat-icon-mobile"><i class="fas fa-box-open"></i></div><div class="stat-number-mobile">{{ $estadisticas['total_pedidos'] }}</div><div class="stat-label-mobile">Pedidos</div></div>
                    <div class="stat-card-mobile"><div class="stat-icon-mobile"><i class="fas fa-clock"></i></div><div class="stat-number-mobile">{{ $estadisticas['pendientes'] + $estadisticas['enviados'] }}</div><div class="stat-label-mobile">Activos</div></div>
                    <div class="stat-card-mobile"><div class="stat-icon-mobile"><i class="fas fa-check-circle"></i></div><div class="stat-number-mobile">{{ $estadisticas['entregados'] }}</div><div class="stat-label-mobile">Entregados</div></div>
                    <div class="stat-card-mobile"><div class="stat-icon-mobile"><i class="fas fa-wallet"></i></div><div class="stat-number-mobile">${{ number_format($estadisticas['total_gastado'], 0) }}</div><div class="stat-label-mobile">Gastado</div></div>
                </div>

                <div class="actions-section-mobile">
                    <div class="section-title-mobile"><h3>Acciones rápidas</h3></div>
                    <div class="actions-grid-mobile">
                        <a href="{{ route('tienda') }}" class="action-btn-mobile"><i class="fas fa-store"></i><span>Tienda</span></a>
                        <a href="{{ route('cliente.pedidos') }}" class="action-btn-mobile"><i class="fas fa-truck"></i><span>Pedidos</span></a>
                        <a href="{{ route('cliente.completar-perfil') }}" class="action-btn-mobile"><i class="fas fa-user"></i><span>Perfil</span></a>
                        <a href="#" class="action-btn-mobile" onclick="confirmarLogout(event)"><i class="fas fa-sign-out-alt"></i><span>Salir</span></a>
                    </div>
                </div>

                <div class="actions-section-mobile">
                    <div class="section-title-mobile"><h3>Pedidos recientes</h3><a href="{{ route('cliente.pedidos') }}">Ver todos <i class="fas fa-chevron-right"></i></a></div>
                    <div class="orders-list-mobile">
                        @forelse($pedidosRecientes->take(3) as $pedido)
                        <div class="order-item-mobile" onclick="window.location.href='{{ route('cliente.pedido.ver', $pedido->id) }}'">
                            <div class="order-icon-mobile">
                                @if($pedido->estado == 'entregado') <i class="fas fa-check-circle"></i>
                                @elseif($pedido->estado == 'pendiente') <i class="fas fa-clock"></i>
                                @elseif($pedido->estado == 'enviado') <i class="fas fa-truck"></i>
                                @elseif($pedido->estado == 'confirmado') <i class="fas fa-check"></i>
                                @else <i class="fas fa-box"></i> @endif
                            </div>
                            <div class="order-info-mobile">
                                <div class="order-header-mobile"><span class="order-folio-mobile">#{{ $pedido->folio }}</span><span class="order-date-mobile">{{ $pedido->created_at->format('d/m/Y') }}</span></div>
                                <div class="order-footer-mobile">
                                    <span class="order-total-mobile">${{ number_format($pedido->total, 0) }}</span>
                                    @php $statusClass = match($pedido->estado) {
                                        'pendiente' => 'status-pendiente', 'confirmado' => 'status-confirmado',
                                        'enviado' => 'status-enviado', 'entregado' => 'status-entregado',
                                        'cancelado' => 'status-cancelado', default => ''
                                    }; @endphp
                                    <span class="order-status-mobile {{ $statusClass }}">{{ ucfirst($pedido->estado) }}</span>
                                </div>
                            </div>
                        </div>
                        @empty
                        <div class="empty-state"><i class="fas fa-box-open"></i><p>Aún no tienes pedidos</p><a href="{{ route('tienda') }}" class="btn btn-primary"><i class="fas fa-store me-2"></i>Ir a la tienda</a></div>
                        @endforelse
                    </div>
                </div>

                <div class="account-card-mobile">
                    <div class="section-title-mobile" style="margin-bottom: 15px;"><h3>Mis datos</h3></div>
                    <div class="account-field-mobile"><div class="field-label-mobile">Nombre completo</div><div class="field-value-mobile">{{ $cliente->nombre }}</div></div>
                    <div class="account-field-mobile"><div class="field-label-mobile">Correo electrónico</div><div class="field-value-mobile">{{ $cliente->email }}</div></div>
                    <div class="account-field-mobile"><div class="field-label-mobile">Teléfono</div><div class="field-value-mobile">{{ $cliente->telefono ?? 'No registrado' }}</div></div>
                    <div class="account-field-mobile"><div class="field-label-mobile">Dirección de envío</div><div class="field-value-mobile">@if($cliente->direccion){{ $cliente->direccion }}, {{ $cliente->ciudad }}, {{ $cliente->estado }}@else No registrada @endif</div></div>
                    <a href="{{ route('cliente.completar-perfil') }}" class="btn-edit-mobile"><i class="fas fa-edit"></i> Editar información</a>
                </div>

                <a href="{{ route('tienda') }}" class="fab-mobile"><i class="fas fa-plus"></i></a>
            </div>

            <!-- VERSIÓN DESKTOP -->
            <div class="desktop-only">
                <div class="welcome-dashboard"><h1>¡Hola, <span>{{ explode(' ', $cliente->nombre)[0] }}</span>!</h1><p>Bienvenido a tu panel de control. Aquí puedes gestionar todos tus pedidos y datos personales.</p></div>

                <div class="stats-grid">
                    <div class="stat-card"><div class="stat-icon"><i class="fas fa-box-open"></i></div><div class="stat-number">{{ $estadisticas['total_pedidos'] }}</div><div class="stat-label">Pedidos Totales</div></div>
                    <div class="stat-card"><div class="stat-icon"><i class="fas fa-clock"></i></div><div class="stat-number">{{ $estadisticas['pendientes'] + $estadisticas['enviados'] }}</div><div class="stat-label">Pedidos Activos</div></div>
                    <div class="stat-card"><div class="stat-icon"><i class="fas fa-check-circle"></i></div><div class="stat-number">{{ $estadisticas['entregados'] }}</div><div class="stat-label">Entregados</div></div>
                    <div class="stat-card"><div class="stat-icon"><i class="fas fa-wallet"></i></div><div class="stat-number">${{ number_format($estadisticas['total_gastado'], 0) }}</div><div class="stat-label">Total Gastado</div></div>
                </div>

                <div class="quick-actions-container">
                    <div class="quick-actions-header"><div class="header-icon"><i class="fas fa-bolt"></i></div><div class="header-text"><h3>Acciones Rápidas</h3><p>Lo que necesitas al alcance de un clic</p></div></div>
                    <div class="quick-actions-grid">
                        <a href="{{ route('tienda') }}" class="quick-action-card card-tienda"><div class="quick-action-overlay"><i class="fas fa-store"></i><h4>Tienda</h4><p>Explorar productos</p></div></a>
                        <a href="{{ route('cliente.pedidos') }}" class="quick-action-card card-pedidos"><div class="quick-action-overlay"><i class="fas fa-truck"></i><h4>Mis Pedidos</h4><p>Rastreo y detalles</p></div></a>
                        <a href="{{ route('cliente.completar-perfil') }}" class="quick-action-card card-perfil"><div class="quick-action-overlay"><i class="fas fa-user-circle"></i><h4>Mi Perfil</h4><p>Gestionar cuenta</p></div></a>
                        <a href="#" class="quick-action-card card-salir" onclick="confirmarLogout(event)"><div class="quick-action-overlay"><i class="fas fa-power-off"></i><h4>Salir</h4><p>Cerrar sesión</p></div></a>
                    </div>
                </div>

                <div class="orders-section">
                    <div class="section-header"><h2>Pedidos Recientes</h2><a href="{{ route('cliente.pedidos') }}">Ver Todos <i class="fas fa-arrow-right ms-2"></i></a></div>
                    @if($pedidosRecientes->count() > 0)
                    <div class="table-responsive">
                        <table class="table-custom">
                            <thead><tr><th>Folio</th><th>Fecha</th><th>Total</th><th>Estado</th><th>Acciones</th></tr></thead>
                            <tbody>
                                @foreach($pedidosRecientes as $pedido)
                                <tr>
                                    <td><span class="folio-badge">#{{ $pedido->folio }}</span></td>
                                    <td>{{ $pedido->created_at->format('d/m/Y') }}</td>
                                    <td>${{ number_format($pedido->total, 2) }}</td>
                                    <td>@php $statusClass = match($pedido->estado) {
                                        'pendiente' => 'status-pendiente', 'confirmado' => 'status-confirmado',
                                        'enviado' => 'status-enviado', 'entregado' => 'status-entregado',
                                        'cancelado' => 'status-cancelado', default => ''
                                    }; @endphp<span class="status-badge {{ $statusClass }}">{{ ucfirst($pedido->estado) }}</span></td>
                                    <td><div class="action-buttons"><a href="{{ route('cliente.pedido.ver', $pedido->id) }}" class="btn-action-table btn-view"><i class="fas fa-eye"></i></a>@if($pedido->estado == 'pendiente')<button class="btn-action-table btn-cancel" onclick="confirmarCancelacion({{ $pedido->id }})"><i class="fas fa-times"></i></button>@endif</div></td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="empty-state"><i class="fas fa-box-open"></i><p>Aún no has realizado ningún pedido</p><a href="{{ route('tienda') }}" class="btn btn-primary"><i class="fas fa-store me-2"></i>Ir a la Tienda</a></div>
                    @endif
                </div>

                <div class="account-section">
                    <div class="section-header"><h2>Mis Datos</h2></div>
                    <div class="account-grid">
                        <div class="account-item"><i class="fas fa-user"></i><div class="label">Nombre Completo</div><div class="value">{{ $cliente->nombre }}</div></div>
                        <div class="account-item"><i class="fas fa-envelope"></i><div class="label">Correo Electrónico</div><div class="value">{{ $cliente->email }}</div></div>
                        <div class="account-item"><i class="fas fa-phone"></i><div class="label">Teléfono</div><div class="value">{{ $cliente->telefono ?? 'No registrado' }}</div></div>
                        <div class="account-item"><i class="fas fa-map-marker-alt"></i><div class="label">Dirección de Envío</div><div class="value">@if($cliente->direccion){{ $cliente->direccion }}<br>{{ $cliente->ciudad }}, {{ $cliente->estado }}@else No registrada @endif</div></div>
                    </div>
                    <a href="{{ route('cliente.completar-perfil') }}" class="btn-edit-account"><i class="fas fa-edit me-2"></i>Editar Información</a>
                </div>
            </div>
        </div>
    </section>

    <footer class="main-footer">
        <div class="container">
            <div class="row">
                <div class="col-md-4"><div class="footer-brand"><img src="{{ asset('assets/img/logo-transparente.png') }}" alt="Tanques Tlaloc"><h5>Tanques Tlaloc</h5><p>Especialistas en ROTOMOLDEO</p></div></div>
                <div class="col-md-4"><div class="footer-links"><h6>Enlaces Rápidos</h6><ul><li><a href="{{ route('tienda') }}">Tienda</a></li><li><a href="{{ route('cliente.pedidos') }}">Mis Pedidos</a></li><li><a href="{{ route('cliente.completar-perfil') }}">Mi Perfil</a></li></ul></div></div>
                <div class="col-md-4"><div class="footer-links"><h6>Ayuda</h6><ul><li><a href="{{ route('contacto') }}">Contacto</a></li><li><a href="#" onclick="contactarWhatsApp(event)">Soporte WhatsApp</a></li></ul></div></div>
            </div>
            <div class="row mt-4"><div class="col-12 text-center"><p class="small text-white-50 mb-0">&copy; {{ date('Y') }} Tanques Tlaloc. Todos los derechos reservados.</p></div></div>
        </div>
    </footer>

    <form id="logout-form" method="POST" action="{{ route('logout') }}" style="display: none;">@csrf</form>

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
                        title: 'Cancelando...',
                        text: 'Por favor espera',
                        allowOutsideClick: false,
                        didOpen: () => { Swal.showLoading(); }
                    });
                    
                    fetch('/cliente/pedido/' + id + '/cancelar', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Content-Type': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire({
                                icon: 'success',
                                title: '¡Cancelado!',
                                text: data.message,
                                timer: 2000,
                                showConfirmButton: false
                            }).then(() => { location.reload(); });
                        } else {
                            Swal.fire({ icon: 'error', title: 'Error', text: data.message });
                        }
                    })
                    .catch(error => {
                        Swal.fire({ icon: 'error', title: 'Error', text: 'Ocurrió un error al cancelar el pedido' });
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