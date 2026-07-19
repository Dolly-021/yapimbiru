<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#0369a1">
    <link rel="icon" type="image/png" href="{{ asset('images/yapim.png?v=' . time()) }}">
    <link rel="shortcut icon" href="{{ asset('favicon.png?v=' . time()) }}" type="image/png">
    <title>@yield('title', 'Dashboard') - SMK YAPIM BIRU-BIRU</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            background: #f5f7fa;
            min-height: 100vh;
        }
        /* Navbar */
        .navbar {
            background: linear-gradient(135deg, #0369a1 0%, #06b6d4 50%, #14b8a6 100%);
            color: white;
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .navbar-brand {
            font-size: 1.5rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        .navbar-brand:hover {
            transform: scale(1.05);
            filter: brightness(1.1);
        }
        .navbar-brand img {
            transition: transform 0.3s ease;
        }
        .navbar-brand:hover img {
            transform: rotate(5deg);
        }
        .user-info {
            display: flex;
            align-items: center;
            gap: 1.5rem;
        }
        .user-name {
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        .user-name span:first-child {
            font-weight: 600;
            font-size: 1rem;
        }
        .role-badge {
            background: rgba(255,255,255,0.25);
            color: white;
            padding: 0.35rem 0.85rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .logout-btn {
            background: rgba(255,255,255,0.15);
            color: white;
            border: 1px solid rgba(255,255,255,0.3);
            padding: 0.5rem 1.25rem;
            border-radius: 6px;
            cursor: pointer;
            transition: all 0.3s;
            font-weight: 500;
        }
        .logout-btn:hover {
            background: rgba(255,255,255,0.25);
            transform: translateY(-1px);
        }
        /* Container */
        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 2rem;
        }
        /* Welcome Card */
        .welcome-card {
            background: linear-gradient(135deg, #0369a1 0%, #06b6d4 50%, #14b8a6 100%);
            padding: 2.5rem;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(3, 105, 161, 0.3);
            margin-bottom: 2rem;
            color: white;
        }
        .welcome-card h2 {
            font-size: 2rem;
            margin-bottom: 0.75rem;
            font-weight: 700;
        }
        .welcome-card p {
            font-size: 1.05rem;
            opacity: 0.95;
            line-height: 1.6;
        }
        /* Stats Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        .stat-card {
            background: white;
            padding: 2rem;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
            transition: all 0.3s;
            position: relative;
            overflow: hidden;
        }
        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 5px;
            height: 100%;
            background: linear-gradient(135deg, #0369a1 0%, #06b6d4 50%, #14b8a6 100%);
        }
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.12);
        }
        .stat-icon {
            width: 50px;
            height: 50px;
            background: linear-gradient(135deg, #0369a1 0%, #06b6d4 50%, #14b8a6 100%);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1rem;
            color: white;
            font-size: 1.5rem;
        }
        .stat-value {
            font-size: 2.5rem;
            font-weight: 700;
            color: #2d3748;
            margin-bottom: 0.5rem;
        }
        .stat-label {
            color: #718096;
            font-size: 1rem;
            font-weight: 500;
        }
        /* Content Section */
        .content-section {
            background: white;
            padding: 2rem;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
            margin-bottom: 2rem;
        }
        .section-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: #2d3748;
            margin-bottom: 1.5rem;
            padding-bottom: 1rem;
            border-bottom: 2px solid #e2e8f0;
        }
        /* Table */
        .data-table {
            width: 100%;
            border-collapse: collapse;
        }
        .data-table thead {
            background: #f7fafc;
        }
        .data-table th {
            padding: 1rem;
            text-align: left;
            font-weight: 600;
            color: #4a5568;
            border-bottom: 2px solid #e2e8f0;
        }
        .data-table thead th {
            color: #4a5568 !important;
        }
        .data-table thead tr:hover th {
            color: #4a5568 !important;
            background: #f7fafc !important;
        }
        .data-table td {
            padding: 1rem;
            border-bottom: 1px solid #e2e8f0;
            color: #2d3748;
        }
        .data-table tr:hover {
            background: #f7fafc;
        }
        /* Button */
        .btn {
            display: inline-block;
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s;
            border: none;
            cursor: pointer;
        }
        .btn-primary {
            background: linear-gradient(135deg, #fbbf24 0%, #f97316 100%);
            color: white;
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(251, 191, 36, 0.4);
        }
        .btn-secondary {
            background: #e2e8f0;
            color: #4a5568;
        }
        .btn-secondary:hover {
            background: #cbd5e0;
            transform: none !important;
            box-shadow: none !important;
        }
        .btn-sm {
            padding: 0.5rem 1rem;
            font-size: 0.875rem;
        }
        /* Badge */
        .badge {
            display: inline-block;
            padding: 0.35rem 0.75rem;
            border-radius: 15px;
            font-size: 0.75rem;
            font-weight: 600;
        }
        .badge-success {
            background: #c6f6d5;
            color: #22543d;
        }
        .badge-danger {
            background: #fed7d7;
            color: #742a2a;
        }
        .badge-warning {
            background: #feebc8;
            color: #744210;
        }
        .badge-info {
            background: #bee3f8;
            color: #2c5282;
        }
        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 3rem 2rem;
            color: #718096;
        }
        .empty-state i {
            font-size: 3rem;
            margin-bottom: 1rem;
            opacity: 0.5;
        }
        /* Responsive */
        @media (max-width: 768px) {
            .navbar {
                flex-direction: column;
                gap: 1rem;
                padding: 1rem;
            }
            .container {
                padding: 1rem;
            }
            .stats-grid {
                grid-template-columns: 1fr;
            }
            .data-table {
                font-size: 0.875rem;
            }
        }
    </style>
    @yield('styles')
</head>
<body>
    @include('partials.navbar')

    <div class="container">
        @yield('content')
    </div>

    @include('partials.alerts')

    @yield('scripts')
</body>
</html>
