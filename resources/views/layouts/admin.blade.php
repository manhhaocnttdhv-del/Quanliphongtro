<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8"/>
    <title>@yield('title', 'Admin') — {{ \App\Models\Setting::get('site_name','Nhà Trọ') }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/png" sizes="32x32" href="/admin_v2/assets/images/favicon_io/favicon-32x32.png">
    <link rel="stylesheet" href="/admin_v2/assets/scss/main.css" onerror="this.onerror=null;">
    {{-- Fallback: load main.css compiled from inapp --}}
    <style>
        :root { --primary: #0d6efd; }
        body { font-family: 'Inter', sans-serif; background: #f5f6fa; }
        .sidebar { width: 240px; min-height: 100vh; background: #fff; border-right: 1px solid #e9ecef; position: fixed; top: 0; left: 0; z-index: 100; transition: all .3s; }
        .sidebar .logo-area { padding: 20px 24px; border-bottom: 1px solid #e9ecef; }
        .sidebar .logo-area span { font-size: 18px; font-weight: 700; color: var(--primary); }
        .sidebar .nav-link { color: #495057; border-radius: 8px; padding: 10px 16px; margin: 2px 8px; display: flex; align-items: center; gap: 10px; font-size: 14px; }
        .sidebar .nav-link:hover, .sidebar .nav-link.active { background: #e8f0fe; color: var(--primary); }
        .sidebar .nav-link i { font-size: 18px; width: 20px; }
        .sidebar small.nav-text { color: #adb5bd; font-size: 11px; text-transform: uppercase; letter-spacing: 1px; }
        .topbar { height: 60px; background: #fff; border-bottom: 1px solid #e9ecef; display: flex; align-items: center; justify-content: space-between; padding: 0 24px; position: fixed; top: 0; left: 240px; right: 0; z-index: 99; }
        .content { margin-left: 240px; margin-top: 60px; padding: 24px; }
        .card { border: 1px solid #e9ecef; border-radius: 12px; background: #fff; }
        .icon-shape { width: 42px; height: 42px; border-radius: 10px; display: flex; align-items: center; justify-content: center; }
        .icon-shape.icon-md { width: 48px; height: 48px; }
        .btn-icon { width: 36px; height: 36px; display: inline-flex; align-items: center; justify-content: center; padding: 0; border-radius: 8px; }
        .avatar { object-fit: cover; }
        .avatar-sm { width: 36px; height: 36px; }
        .avatar-md { width: 48px; height: 48px; }
        .notification-dropdown { min-width: 320px; max-height: 400px; overflow-y: auto; }
        .notif-unread { background: #f0f7ff; }
        @media(max-width:991px) {
            .sidebar { left: -240px; }
            .sidebar.show { left: 0; }
            .topbar, .content { left: 0; margin-left: 0; }
        }
    </style>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">
    @yield('styles')
</head>
<body>
<div id="overlay" class="d-none d-md-none"></div>

{{-- TOPBAR --}}
<nav class="topbar">
    <div class="d-flex align-items-center gap-2">
        <button id="toggleBtn" class="btn btn-light btn-icon btn-sm d-none d-lg-flex">
            <i class="ti ti-layout-sidebar-left-expand"></i>
        </button>
        <button id="mobileBtn" class="btn btn-light btn-icon btn-sm d-lg-none">
            <i class="ti ti-menu-2"></i>
        </button>
        <span class="fw-semibold text-primary d-none d-md-inline">{{ \App\Models\Setting::get('site_name','Nhà Trọ') }}</span>
    </div>
    <div>
        <ul class="list-unstyled d-flex align-items-center mb-0 gap-2">
            {{-- Notification Bell --}}
            <li class="dropdown">
                <a href="#" class="position-relative btn-icon btn btn-light btn-sm rounded-circle" data-bs-toggle="dropdown">
                    <i class="ti ti-bell" style="font-size:18px;"></i>
                    @php $unreadCount = auth()->user()->unreadNotifications->count(); @endphp
                    @if($unreadCount > 0)
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size:9px;">{{ $unreadCount }}</span>
                    @endif
                </a>
                <div class="dropdown-menu dropdown-menu-end notification-dropdown p-0">
                    <div class="d-flex justify-content-between align-items-center px-3 py-2 border-bottom">
                        <strong class="small">Thông báo</strong>
                        @if($unreadCount > 0)
                            <form method="POST" action="{{ route('admin.notifications.readAll') }}">@csrf
                                <button class="btn btn-link btn-sm p-0 small">Đọc tất cả</button>
                            </form>
                        @endif
                    </div>
                    @forelse(auth()->user()->notifications->take(6) as $notif)
                        <div class="px-3 py-2 border-bottom {{ $notif->read_at ? '' : 'notif-unread' }}" style="font-size:13px;">
                            <div class="fw-semibold">{{ $notif->data['title'] ?? '' }}</div>
                            <div class="text-muted">{{ $notif->data['message'] ?? '' }}</div>
                            <div class="text-secondary" style="font-size:11px;">{{ $notif->created_at->diffForHumans() }}</div>
                        </div>
                    @empty
                        <div class="px-3 py-3 text-center text-muted small">Chưa có thông báo</div>
                    @endforelse
                    <div class="text-center py-2"><a href="#" class="small text-primary">Xem tất cả</a></div>
                </div>
            </li>
            {{-- User dropdown --}}
            <li class="dropdown">
                <a href="#" data-bs-toggle="dropdown">
                    <div class="btn btn-light btn-sm d-flex align-items-center gap-2">
                        <i class="ti ti-user-circle"></i>
                        <span class="small d-none d-md-inline">{{ auth()->user()->name }}</span>
                    </div>
                </a>
                <div class="dropdown-menu dropdown-menu-end p-0" style="min-width:200px;">
                    <div class="px-3 py-3 border-bottom">
                        <div class="fw-semibold small">{{ auth()->user()->name }}</div>
                        <div class="text-muted" style="font-size:12px;">{{ auth()->user()->email }}</div>
                    </div>
                    <div class="p-2">
                        <a class="dropdown-item rounded" href="{{ route('admin.settings.index') }}"><i class="ti ti-settings me-2"></i>Cài đặt</a>
                        <form method="POST" action="{{ route('logout') }}">@csrf
                            <button type="submit" class="dropdown-item rounded text-danger"><i class="ti ti-logout me-2"></i>Đăng xuất</button>
                        </form>
                    </div>
                </div>
            </li>
        </ul>
    </div>
</nav>

{{-- SIDEBAR --}}
<aside id="sidebar" class="sidebar">
    <div class="logo-area">
        <a href="{{ route('admin.dashboard') }}" class="text-decoration-none">
            <span>🏠 {{ \App\Models\Setting::get('site_name','Nhà Trọ') }}</span>
        </a>
    </div>
    <ul class="nav flex-column mt-2">
        <li class="px-4 py-2"><small class="nav-text">Tổng quan</small></li>
        <li>
            <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
                <i class="ti ti-home"></i><span>Dashboard</span>
            </a>
        </li>

        @if(auth()->user()->isSuperAdmin())
            <li class="px-4 pt-3 pb-1"><small class="nav-text">Hệ thống</small></li>
            <li>
                <a class="nav-link {{ request()->routeIs('admin.landlords.*') ? 'active' : '' }}" href="{{ route('admin.landlords.index') }}">
                    <i class="ti ti-users"></i><span>Chủ trọ</span>
                </a>
            </li>
            <li>
                <a class="nav-link {{ request()->routeIs('admin.commissions.*') ? 'active' : '' }}" href="{{ route('admin.commissions.index') }}">
                    <i class="ti ti-coin"></i><span>Phí hoa hồng</span>
                </a>
            </li>
        @endif

        @if(auth()->user()->isLandlord())
            <li class="px-4 pt-3 pb-1"><small class="nav-text">Quản lý phòng</small></li>
            <li>
                <a class="nav-link {{ request()->routeIs('admin.rooms.*') ? 'active' : '' }}" href="{{ route('admin.rooms.index') }}">
                    <i class="ti ti-building"></i><span>Phòng trọ</span>
                </a>
            </li>
            <li>
                <a class="nav-link {{ request()->routeIs('admin.rent-requests.*') ? 'active' : '' }}" href="{{ route('admin.rent-requests.index') }}">
                    <i class="ti ti-file-description"></i><span>Yêu cầu thuê
                        @php 
                            $pending = \App\Models\RentRequest::where('status','pending')
                                ->whereHas('room', fn($q) => $q->where('landlord_id', auth()->id()))
                                ->count(); 
                        @endphp
                        @if($pending > 0)<span class="badge bg-danger ms-auto">{{ $pending }}</span>@endif
                    </span>
                </a>
            </li>
            <li>
                <a class="nav-link {{ request()->routeIs('admin.contracts.*') ? 'active' : '' }}" href="{{ route('admin.contracts.index') }}">
                    <i class="ti ti-contract"></i><span>Hợp đồng</span>
                </a>
            </li>
            <li>
                <a class="nav-link {{ request()->routeIs('admin.utilities.*') ? 'active' : '' }}" href="{{ route('admin.utilities.index') }}">
                    <i class="ti ti-bolt"></i><span>Điện nước</span>
                </a>
            </li>
            <li>
                <a class="nav-link {{ request()->routeIs('admin.invoices.*') ? 'active' : '' }}" href="{{ route('admin.invoices.index') }}">
                    <i class="ti ti-receipt"></i><span>Hóa đơn</span>
                </a>
            </li>
            <li>
                <a class="nav-link {{ request()->routeIs('admin.maintenance.*') ? 'active' : '' }}" href="{{ route('admin.maintenance.index') }}">
                    <i class="ti ti-tool"></i><span>Bảo trì 
                        @php 
                            $pendingMaint = \App\Models\MaintenanceRequest::where('status','pending');
                            if(auth()->user()->isLandlord()) {
                                $pendingMaint->whereHas('room', fn($q) => $q->where('landlord_id', auth()->id()));
                            }
                            $pendingMaintCount = $pendingMaint->count();
                        @endphp
                        @if($pendingMaintCount > 0)<span class="badge bg-warning ms-auto">{{ $pendingMaintCount }}</span>@endif
                    </span>
                </a>
            </li>
            <li>
                <a class="nav-link {{ request()->routeIs('admin.commissions.*') ? 'active' : '' }}" href="{{ route('admin.commissions.index') }}">
                    <i class="ti ti-coin"></i><span>Phí hoa hồng</span>
                </a>
            </li>
        @endif

        <li class="px-4 pt-3 pb-1"><small class="nav-text">Tùy chỉnh</small></li>
        <li>
            <a class="nav-link {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}" href="{{ route('admin.settings.index') }}">
                <i class="ti ti-settings"></i><span>Cài đặt</span>
            </a>
        </li>
        <li>
            <a class="nav-link" href="{{ route('home') }}" target="_blank">
                <i class="ti ti-external-link"></i><span>Xem trang chủ</span>
            </a>
        </li>
    </ul>
</aside>

{{-- CONTENT --}}
<main id="content" class="content">
    <div class="container-fluid">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @yield('content')
    </div>
</main>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
    // Sidebar toggle
    document.getElementById('toggleBtn')?.addEventListener('click', () => {
        document.getElementById('sidebar').classList.toggle('collapsed');
        document.getElementById('content').classList.toggle('expanded');
    });
    document.getElementById('mobileBtn')?.addEventListener('click', () => {
        document.getElementById('sidebar').classList.toggle('show');
    });
</script>
@yield('scripts')
</body>
</html>
