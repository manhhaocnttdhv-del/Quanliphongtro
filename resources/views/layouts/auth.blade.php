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
        
        {{-- Validation errors (keep inline near form) --}}
        @if($errors->any())
            <div class="alert alert-danger mb-3 border-0 shadow-sm" style="border-left:4px solid #dc3545 !important; border-radius:12px;">
                <div class="d-flex align-items-center gap-2 mb-2">
                    <i class="fa fa-exclamation-circle text-danger"></i>
                    <strong>Vui lòng kiểm tra lại:</strong>
                </div>
                <ul class="mb-0 ps-3" style="font-size:13px;">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @yield('content')
    </div>

    {{-- Toast Container --}}
    <div id="toast-container" style="position:fixed;top:20px;right:20px;z-index:99999;display:flex;flex-direction:column;gap:10px;max-width:380px;"></div>

    <style>
        .toast-notification {
            display:flex;align-items:flex-start;gap:12px;
            padding:16px 20px;border-radius:14px;
            background:#fff;box-shadow:0 8px 32px rgba(0,0,0,0.15);
            border-left:4px solid;min-width:280px;
            animation:toastSlideIn 0.4s cubic-bezier(0.175,0.885,0.32,1.275);
            transition:all 0.3s ease;font-family:'Inter',sans-serif;
        }
        .toast-notification.toast-hiding{animation:toastSlideOut 0.3s ease forwards;}
        .toast-notification.toast-success{border-left-color:#10b981;}
        .toast-notification.toast-error{border-left-color:#ef4444;}
        .toast-notification.toast-warning{border-left-color:#f59e0b;}
        .toast-notification.toast-info{border-left-color:#3b82f6;}
        .toast-icon{width:36px;height:36px;border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;font-size:16px;}
        .toast-success .toast-icon{background:#ecfdf5;color:#10b981;}
        .toast-error .toast-icon{background:#fef2f2;color:#ef4444;}
        .toast-warning .toast-icon{background:#fffbeb;color:#f59e0b;}
        .toast-info .toast-icon{background:#eff6ff;color:#3b82f6;}
        .toast-body{flex:1;}
        .toast-title{font-weight:600;font-size:14px;margin-bottom:2px;color:#1e293b;}
        .toast-message{font-size:13px;color:#64748b;line-height:1.4;}
        .toast-close{background:none;border:none;color:#94a3b8;cursor:pointer;padding:0;font-size:18px;line-height:1;flex-shrink:0;}
        .toast-close:hover{color:#1e293b;}
        .toast-progress{position:absolute;bottom:0;left:4px;right:0;height:3px;border-radius:0 0 14px 0;animation:toastProgress 5s linear forwards;}
        .toast-success .toast-progress{background:#10b981;}
        .toast-error .toast-progress{background:#ef4444;}
        .toast-warning .toast-progress{background:#f59e0b;}
        .toast-info .toast-progress{background:#3b82f6;}
        @keyframes toastSlideIn{from{opacity:0;transform:translateX(100px);}to{opacity:1;transform:translateX(0);}}
        @keyframes toastSlideOut{from{opacity:1;transform:translateX(0);}to{opacity:0;transform:translateX(100px);}}
        @keyframes toastProgress{from{width:100%;}to{width:0%;}}
    </style>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        function showToast(type,title,message,duration){
            duration=duration||5000;
            var icons={success:'<i class="fa fa-check-circle"></i>',error:'<i class="fa fa-exclamation-triangle"></i>',warning:'<i class="fa fa-exclamation-circle"></i>',info:'<i class="fa fa-info-circle"></i>'};
            var c=document.getElementById('toast-container'),t=document.createElement('div');
            t.className='toast-notification toast-'+type;t.style.position='relative';
            t.innerHTML='<div class="toast-icon">'+(icons[type]||icons.info)+'</div><div class="toast-body"><div class="toast-title">'+title+'</div><div class="toast-message">'+message+'</div></div><button class="toast-close" onclick="dismissToast(this)">&times;</button><div class="toast-progress"></div>';
            c.appendChild(t);setTimeout(function(){dismissToast(t.querySelector('.toast-close'));},duration);
        }
        function dismissToast(b){var t=b.closest('.toast-notification');if(!t||t.classList.contains('toast-hiding'))return;t.classList.add('toast-hiding');setTimeout(function(){t.remove();},300);}

        @if(session('success'))
            showToast('success','Thành công!',@json(session('success')));
        @endif
        @if(session('error'))
            showToast('error','Lỗi!',@json(session('error')));
        @endif
        @if(session('warning'))
            showToast('warning','Cảnh báo!',@json(session('warning')));
        @endif
    </script>
    @yield('scripts')
</body>
</html>

