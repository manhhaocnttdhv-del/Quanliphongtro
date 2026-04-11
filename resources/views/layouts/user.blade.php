<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', \App\Models\Setting::get('site_name', 'Nhà Trọ'))</title>
    <meta name="description" content="@yield('description', 'Hệ thống quản lý phòng trọ')">
    <link rel="stylesheet" href="{{ asset('user/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('user/css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('user/css/responsive.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/font-awesome@4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fancybox/2.1.5/jquery.fancybox.min.css" media="screen">
    <style>
        .notification-bell { position: relative; cursor: pointer; }
        .notification-badge { position: absolute; top: -8px; right: -8px; background: #e74c3c; color: #fff; border-radius: 50%; padding: 2px 6px; font-size: 10px; }
        .auth-nav-links { display: flex; align-items: center; gap: 10px; }
        .auth-nav-links a { color: #fff; text-decoration: none; }
        .nav-notification-panel { min-width: 320px; max-height: 400px; overflow-y: auto; }
        .notif-unread { background: #f0f7ff; }
        /* Fix dropdown item visibility */
        .dropdown-menu .dropdown-item { color: #212529 !important; padding: 10px 20px; }
        .dropdown-menu .dropdown-item i { width: 20px; }
        .dropdown-menu .dropdown-item:hover { background-color: #f8f9fa; color: #fe0000 !important; }
    </style>
    @yield('styles')
</head>
<body class="main-layout">
    <div class="loader_bg"><div class="loader"><img src="/user/images/loading.gif" alt="#"/></div></div>

    <header>
        <div class="header">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-xl-3 col-lg-3 col-md-3 col-sm-3 col logo_section">
                        <div class="logo">
                            <a href="{{ route('home') }}">
                                @php $logo = \App\Models\Setting::get('site_logo'); @endphp
                                @if($logo)
                                    <img src="{{ asset('storage/'.$logo) }}" alt="Logo" height="50">
                                @else
                                    <span style="color:#fff;font-size:20px;font-weight:700;">{{ \App\Models\Setting::get('site_name','Nhà Trọ') }}</span>
                                @endif
                            </a>
                        </div>
                    </div>
                    <div class="col-xl-9 col-lg-9 col-md-9 col-sm-9">
                        <nav class="navigation navbar navbar-expand-md navbar-dark">
                            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navMain">
                                <span class="navbar-toggler-icon"></span>
                            </button>
                            <div class="collapse navbar-collapse" id="navMain">
                                <ul class="navbar-nav mr-auto">
                                    <li class="nav-item {{ request()->routeIs('home') ? 'active': '' }}">
                                        <a class="nav-link" href="{{ route('home') }}">Trang Chủ</a>
                                    </li>
                                    <li class="nav-item {{ request()->routeIs('rooms.*') ? 'active': '' }}">
                                        <a class="nav-link" href="{{ route('rooms.index') }}">Danh sách phòng</a>
                                    </li>
                                    @auth
                                        @if(auth()->user()->isAdmin())
                                            <li class="nav-item">
                                                <a class="nav-link" href="{{ route('admin.dashboard') }}" style="background: #eab308; color: #000 !important; border-radius: 8px; padding: 8px 15px !important; margin-left: 10px; font-weight: 700; border: none;">
                                                    <i class="fa fa-dashboard mr-1"></i> Quản Trị
                                                </a>
                                            </li>
                                        @endif
                                    @endauth
                                </ul>
                                <ul class="navbar-nav ml-auto auth-nav-links">
                                    @auth
                                        {{-- Notification Bell --}}
                                        <li class="nav-item dropdown">
                                            <a class="nav-link notification-bell" href="#" id="notifDropdown" data-toggle="dropdown" aria-label="Thông báo">
                                                {{-- Bell icon filled --}}
                                                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="currentColor" style="vertical-align:middle;color:#333;">
                                                    <path d="M12 22c1.1 0 2-.9 2-2h-4c0 1.1.9 2 2 2zm6-6v-5c0-3.07-1.64-5.64-4.5-6.32V4c0-.83-.67-1.5-1.5-1.5s-1.5.67-1.5 1.5v.68C7.63 5.36 6 7.92 6 11v5l-2 2v1h16v-1l-2-2z"/>
                                                </svg>
                                                @if(auth()->user()->unreadNotifications->count() > 0)
                                                    <span class="notification-badge">{{ auth()->user()->unreadNotifications->count() }}</span>
                                                @endif
                                            </a>
                                            <div class="dropdown-menu dropdown-menu-right nav-notification-panel p-0" aria-labelledby="notifDropdown">
                                                <div class="d-flex justify-content-between align-items-center px-3 py-2 border-bottom">
                                                    <strong>Thông báo</strong>
                                                    @if(auth()->user()->unreadNotifications->count() > 0)
                                                        <form method="POST" action="{{ route('notifications.readAll') }}">
                                                            @csrf
                                                            <button class="btn btn-link btn-sm p-0">Đọc tất cả</button>
                                                        </form>
                                                    @endif
                                                </div>
                                                @forelse(auth()->user()->notifications->take(8) as $notif)
                                                    <div class="px-3 py-2 border-bottom {{ $notif->read_at ? '' : 'notif-unread' }}">
                                                        <div class="fw-bold small">{{ $notif->data['title'] ?? '' }}</div>
                                                        <div class="small text-muted">{{ $notif->data['message'] ?? '' }}</div>
                                                        <div class="small text-secondary">{{ $notif->created_at->diffForHumans() }}</div>
                                                    </div>
                                                @empty
                                                    <div class="px-3 py-3 text-center text-muted small">Không có thông báo</div>
                                                @endforelse
                                            </div>
                                        </li>
                                        <li class="nav-item dropdown">
                                            <a class="nav-link dropdown-toggle" href="#" data-toggle="dropdown">
                                                {{-- User icon filled --}}
                                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="currentColor" style="vertical-align:middle;margin-right:4px;color:#333;">
                                                    <path d="M12 12c2.7 0 4.8-2.1 4.8-4.8S14.7 2.4 12 2.4 7.2 4.5 7.2 7.2 9.3 12 12 12zm0 2.4c-3.2 0-9.6 1.6-9.6 4.8v2.4h19.2v-2.4c0-3.2-6.4-4.8-9.6-4.8z"/>
                                                </svg>
                                                {{ auth()->user()->name }}
                                            </a>
                                            <div class="dropdown-menu dropdown-menu-right">
                                                @if(auth()->user()->isAdmin())
                                                    <a class="dropdown-item fw-bold" href="{{ route('admin.dashboard') }}" style="color: #eab308 !important;">
                                                        <i class="fa fa-dashboard mr-1"></i> Vào trang Quản trị
                                                    </a>
                                                @endif
                                                <a class="dropdown-item" href="{{ route('bookings.my') }}" style="{{ request()->routeIs('bookings.*') ? 'color:#f97316!important;background:#fff7ed;' : '' }}">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="currentColor" style="vertical-align:middle;margin-right:6px;color:#f97316;"><path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-7 3c1.93 0 3.5 1.57 3.5 3.5S13.93 13 12 13s-3.5-1.57-3.5-3.5S10.07 6 12 6zm7 13H5v-.23c0-.62.28-1.2.76-1.58C7.47 15.82 9.64 15 12 15s4.53.82 6.24 2.19c.48.38.76.97.76 1.58V19z"/></svg>
                                                    Đơn đặt phòng
                                                    @php
                                                        $pendingBookings = auth()->user() ? \App\Models\Booking::where('user_id', auth()->id())->where('status', 'pending')->where('expired_at', '>', now())->count() : 0;
                                                    @endphp
                                                    @if($pendingBookings > 0)
                                                        <span style="background:#f97316;color:#fff;border-radius:10px;padding:1px 7px;font-size:11px;margin-left:4px;">{{ $pendingBookings }}</span>
                                                    @endif
                                                </a>
                                                <a class="dropdown-item" href="{{ route('user.contracts.index') }}">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="currentColor" style="vertical-align:middle;margin-right:6px;color:#10b981;"><path d="M14 2H6c-1.1 0-2 .9-2 2v16c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V8l-6-6zm2 16H8v-2h8v2zm0-4H8v-2h8v2zm-3-5V3.5L18.5 9H13z"/></svg>
                                                    Hợp đồng của tôi
                                                </a>
                                                <a class="dropdown-item" href="{{ route('user.invoices') }}">
                                                    <i class="fa fa-file-text-o mr-1"></i> Hóa đơn của tôi
                                                </a>
                                                <a class="dropdown-item" href="{{ route('maintenance.index') }}">
                                                    <i class="fa fa-wrench mr-1"></i> Yêu cầu bảo trì
                                                </a>
                                                <a class="dropdown-item" href="{{ route('profile.edit') }}">
                                                    <i class="fa fa-cog mr-1"></i> Hồ sơ
                                                </a>
                                                <div class="dropdown-divider"></div>
                                                <form method="POST" action="{{ route('logout') }}">
                                                    @csrf
                                                    <button type="submit" class="dropdown-item"><i class="fa fa-sign-out mr-1"></i> Đăng xuất</button>
                                                </form>
                                            </div>
                                        </li>
                                    @else
                                        <li class="nav-item"><a class="nav-link" href="{{ route('login') }}">Đăng nhập</a></li>
                                        <li class="nav-item"><a class="nav-link" href="{{ route('register') }}">Đăng ký</a></li>
                                    @endauth
                                </ul>
                            </div>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </header>

    {{-- Validation Errors (inline) --}}
    @if($errors->any())
        <div style="background:#fff3cd;border-left:4px solid #dc3545;padding:16px 20px;margin:0;">
            <div class="container">
                <div style="display:flex;align-items:center;gap:8px;margin-bottom:8px;">
                    <i class="fa fa-exclamation-circle" style="color:#dc3545;font-size:18px;"></i>
                    <strong style="color:#dc3545;">Vui lòng kiểm tra lại:</strong>
                </div>
                <ul style="margin:0;padding-left:20px;font-size:14px;color:#333;">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    @yield('content')

    <footer>
        <div class="footer">
            <div class="container">
                <div class="row">
                    <div class="col-md-4">
                        <h3>Liên hệ</h3>
                        <ul class="conta">
                            <li><i class="fa fa-map-marker"></i> {{ \App\Models\Setting::get('site_address','') }}</li>
                            <li><i class="fa fa-mobile"></i> {{ \App\Models\Setting::get('site_phone','') }}</li>
                        </ul>
                    </div>
                    <div class="col-md-4">
                        <h3>Menu</h3>
                        <ul class="link_menu">
                            <li><a href="{{ route('home') }}">Trang chủ</a></li>
                            <li><a href="{{ route('rooms.index') }}">Danh sách phòng</a></li>
                            @auth
                                <li><a href="{{ route('user.invoices') }}">Hóa đơn</a></li>
                            @endauth
                        </ul>
                    </div>
                    <div class="col-md-4">
                        <h3>{{ \App\Models\Setting::get('site_name','Nhà Trọ') }}</h3>
                        <p style="color:#ccc;">Hệ thống quản lý phòng trọ chuyên nghiệp, minh bạch và tiện lợi.</p>
                    </div>
                </div>
            </div>
            <div class="copyright">
                <div class="container">
                    <p>© {{ date('Y') }} {{ \App\Models\Setting::get('site_name','Nhà Trọ') }}. All Rights Reserved.</p>
                </div>
            </div>
        </div>
    </footer>

    {{-- Toast Notification Container --}}
    <div id="toast-container" style="position:fixed;top:20px;right:20px;z-index:99999;display:flex;flex-direction:column;gap:10px;max-width:400px;"></div>

    <style>
        .toast-notification {
            display: flex; align-items: flex-start; gap: 12px;
            padding: 16px 20px; border-radius: 14px;
            background: #fff; box-shadow: 0 8px 32px rgba(0,0,0,0.15);
            border-left: 4px solid; min-width: 300px;
            animation: toastSlideIn 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            transition: all 0.3s ease;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
        }
        .toast-notification.toast-hiding { animation: toastSlideOut 0.3s ease forwards; }
        .toast-notification.toast-success { border-left-color: #10b981; }
        .toast-notification.toast-error   { border-left-color: #ef4444; }
        .toast-notification.toast-warning { border-left-color: #f59e0b; }
        .toast-notification.toast-info    { border-left-color: #3b82f6; }
        .toast-icon {
            width: 36px; height: 36px; border-radius: 10px;
            display: flex; align-items: center; justify-content: center; flex-shrink: 0;
            font-size: 16px;
        }
        .toast-success .toast-icon { background: #ecfdf5; color: #10b981; }
        .toast-error   .toast-icon { background: #fef2f2; color: #ef4444; }
        .toast-warning .toast-icon { background: #fffbeb; color: #f59e0b; }
        .toast-info    .toast-icon { background: #eff6ff; color: #3b82f6; }
        .toast-body { flex: 1; }
        .toast-title { font-weight: 600; font-size: 14px; margin-bottom: 2px; color: #1e293b; }
        .toast-message { font-size: 13px; color: #64748b; line-height: 1.4; }
        .toast-close {
            background: none; border: none; color: #94a3b8; cursor: pointer;
            padding: 0; font-size: 18px; line-height: 1; flex-shrink: 0;
        }
        .toast-close:hover { color: #1e293b; }
        .toast-progress {
            position: absolute; bottom: 0; left: 4px; right: 0; height: 3px;
            border-radius: 0 0 14px 0;
            animation: toastProgress 5s linear forwards;
        }
        .toast-success .toast-progress { background: #10b981; }
        .toast-error   .toast-progress { background: #ef4444; }
        .toast-warning .toast-progress { background: #f59e0b; }
        .toast-info    .toast-progress { background: #3b82f6; }
        @keyframes toastSlideIn { from { opacity:0; transform:translateX(100px); } to { opacity:1; transform:translateX(0); } }
        @keyframes toastSlideOut { from { opacity:1; transform:translateX(0); } to { opacity:0; transform:translateX(100px); } }
        @keyframes toastProgress { from { width:100%; } to { width:0%; } }
    </style>

    <script src="{{ asset('user/js/jquery.min.js') }}"></script>
    <script src="{{ asset('user/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('user/js/jquery-3.0.0.min.js') }}"></script>
    <script src="{{ asset('user/js/jquery.mCustomScrollbar.concat.min.js') }}"></script>
    <script src="{{ asset('user/js/custom.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/fancybox/2.1.5/jquery.fancybox.pack.js"></script>
    <script>
        function showToast(type, title, message, duration) {
            duration = duration || 5000;
            var icons = {
                success: '<i class="fa fa-check-circle"></i>',
                error:   '<i class="fa fa-exclamation-triangle"></i>',
                warning: '<i class="fa fa-exclamation-circle"></i>',
                info:    '<i class="fa fa-info-circle"></i>',
            };
            var container = document.getElementById('toast-container');
            var toast = document.createElement('div');
            toast.className = 'toast-notification toast-' + type;
            toast.style.position = 'relative';
            toast.innerHTML =
                '<div class="toast-icon">' + (icons[type] || icons.info) + '</div>' +
                '<div class="toast-body">' +
                    '<div class="toast-title">' + title + '</div>' +
                    '<div class="toast-message">' + message + '</div>' +
                '</div>' +
                '<button class="toast-close" onclick="dismissToast(this)">&times;</button>' +
                '<div class="toast-progress"></div>';
            container.appendChild(toast);
            setTimeout(function() { dismissToast(toast.querySelector('.toast-close')); }, duration);
        }

        function dismissToast(btn) {
            var toast = btn.closest('.toast-notification');
            if (!toast || toast.classList.contains('toast-hiding')) return;
            toast.classList.add('toast-hiding');
            setTimeout(function() { toast.remove(); }, 300);
        }

        @if(session('success'))
            showToast('success', 'Thành công!', @json(session('success')));
        @endif
        @if(session('error'))
            showToast('error', 'Lỗi!', @json(session('error')));
        @endif
        @if(session('warning'))
            showToast('warning', 'Cảnh báo!', @json(session('warning')));
        @endif
        @if(session('info'))
            showToast('info', 'Thông tin', @json(session('info')));
        @endif
    </script>
    @yield('scripts')
</body>
</html>
