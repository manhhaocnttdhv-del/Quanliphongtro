<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', \App\Models\Setting::get('site_name', 'Nhà Trọ'))</title>
    <meta name="description" content="@yield('description', 'Hệ thống quản lý phòng trọ')">
    <link rel="stylesheet" href="/user/css/bootstrap.min.css">
    <link rel="stylesheet" href="/user/css/style.css">
    <link rel="stylesheet" href="/user/css/responsive.css">
    <link rel="stylesheet" href="https://netdna.bootstrapcdn.com/font-awesome/4.0.3/css/font-awesome.css">
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
                                            <a class="nav-link notification-bell" href="#" id="notifDropdown" data-toggle="dropdown">
                                                <i class="fa fa-bell" style="font-size:18px;color:#fff;"></i>
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
                                                <i class="fa fa-user-circle"></i> {{ auth()->user()->name }}
                                            </a>
                                            <div class="dropdown-menu dropdown-menu-right">
                                                @if(auth()->user()->isAdmin())
                                                    <a class="dropdown-item fw-bold" href="{{ route('admin.dashboard') }}" style="color: #eab308 !important;">
                                                        <i class="fa fa-dashboard mr-1"></i> Vào trang Quản trị
                                                    </a>
                                                @endif
                                                <a class="dropdown-item" href="{{ route('user.invoices') }}">
                                                    <i class="fa fa-file-text-o mr-1"></i> Hóa đơn của tôi
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

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show m-0" role="alert" style="border-radius:0;">
            <div class="container">{{ session('success') }}</div>
            <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show m-0" role="alert" style="border-radius:0;">
            <div class="container">{{ session('error') }}</div>
            <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
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

    <script src="/user/js/jquery.min.js"></script>
    <script src="/user/js/bootstrap.bundle.min.js"></script>
    <script src="/user/js/jquery-3.0.0.min.js"></script>
    <script src="/user/js/jquery.mCustomScrollbar.concat.min.js"></script>
    <script src="/user/js/custom.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/fancybox/2.1.5/jquery.fancybox.pack.js"></script>
    @yield('scripts')
</body>
</html>
