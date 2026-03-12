<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Xác thực') - {{ \App\Models\Setting::get('site_name', 'Nhà Trọ') }}</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root {
            --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --glass-bg: rgba(255, 255, 255, 0.85);
            --glass-border: rgba(255, 255, 255, 0.5);
        }
        
        body {
            font-family: 'Inter', sans-serif;
            background: var(--primary-gradient);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .auth-card {
            background: var(--glass-bg);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid var(--glass-border);
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
            width: 100%;
            max-width: 500px;
            padding: 40px;
            transition: transform 0.3s ease;
        }
        
        .auth-card:hover {
            transform: translateY(-5px);
        }
        
        .auth-logo {
            font-size: 2rem;
            font-weight: 700;
            color: #4834d4;
            text-align: center;
            margin-bottom: 30px;
            text-decoration: none;
            display: block;
        }
        
        .btn-primary-custom {
            background: var(--primary-gradient);
            border: none;
            border-radius: 10px;
            padding: 12px;
            font-weight: 600;
            color: white;
            transition: opacity 0.2s;
        }
        
        .btn-primary-custom:hover {
            opacity: 0.9;
            color: white;
        }
        
        .form-control {
            border-radius: 10px;
            padding: 12px 15px;
            border: 1px solid #ddd;
            background: rgba(255, 255, 255, 0.9);
        }
        
        .form-control:focus {
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.25);
            border-color: #667eea;
        }
        
        .form-label {
            font-weight: 500;
            color: #444;
            margin-bottom: 8px;
        }
        
        .nav-pills-custom .nav-link {
            border-radius: 10px;
            color: #666;
            font-weight: 500;
            padding: 10px 20px;
        }
        
        .nav-pills-custom .nav-link.active {
            background: var(--primary-gradient);
            color: white;
        }

        /* Responsive adjustments for registration */
        .register-card {
            max-width: 800px;
        }
        
        .location-selects select {
            margin-bottom: 15px;
        }
    </style>
    @yield('styles')
</head>
<body>
    <div class="auth-card @yield('card-class')">
        <a href="{{ route('home') }}" class="auth-logo">
            <i class="fa fa-home me-2"></i>{{ \App\Models\Setting::get('site_name', 'Nhà Trọ') }}
        </a>
        
        @yield('content')
    </div>
    
    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    @yield('scripts')
</body>
</html>
