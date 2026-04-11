<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8" />
    <title>@yield('title', 'Admin') — {{ \App\Models\Setting::get('site_name', 'Nhà Trọ') }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/png" sizes="32x32" href="/admin_v2/assets/images/favicon_io/favicon-32x32.png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #4f46e5;
            --primary-light: #e0e7ff;
            --sidebar-width: 260px;
            --topbar-height: 64px;
        }

        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: #f1f5f9;
            margin: 0;
        }

        /* ─── SIDEBAR ─── */
        .sidebar {
            width: var(--sidebar-width);
            min-height: 100vh;
            background: linear-gradient(180deg, #1e1b4b 0%, #312e81 100%);
            position: fixed;
            top: 0;
            left: 0;
            z-index: 100;
            transition: all .3s ease;
            overflow-y: auto;
        }

        .sidebar .logo-area {
            padding: 24px 24px 20px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
        }

        .sidebar .logo-area a {
            color: #fff;
            text-decoration: none;
            font-size: 18px;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .sidebar .nav-section {
            padding: 16px 16px 4px;
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            color: rgba(255, 255, 255, 0.35);
        }

        .sidebar .nav-link {
            color: rgba(255, 255, 255, 0.65);
            border-radius: 10px;
            padding: 10px 16px;
            margin: 2px 12px;
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 13px;
            font-weight: 500;
            transition: all .2s;
            text-decoration: none;
        }

        .sidebar .nav-link:hover {
            background: rgba(255, 255, 255, 0.08);
            color: #fff;
        }

        .sidebar .nav-link.active {
            background: rgba(255, 255, 255, 0.12);
            color: #fff;
            font-weight: 600;
        }

        .sidebar .nav-link i {
            font-size: 18px;
            width: 20px;
            text-align: center;
        }

        .sidebar .nav-link .badge {
            margin-left: auto;
            font-size: 10px;
        }

        /* ─── TOPBAR ─── */
        .topbar {
            height: var(--topbar-height);
            background: #fff;
            border-bottom: 1px solid #e2e8f0;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 24px;
            position: fixed;
            top: 0;
            left: var(--sidebar-width);
            right: 0;
            z-index: 99;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
        }

        /* ─── CONTENT ─── */
        .content {
            margin-left: var(--sidebar-width);
            margin-top: var(--topbar-height);
            padding: 28px;
            min-height: calc(100vh - var(--topbar-height));
        }

        /* ─── COMPONENTS ─── */
        .card {
            border: 1px solid #e2e8f0;
            border-radius: 14px;
            background: #fff;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
        }

        .icon-shape {
            width: 42px;
            height: 42px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .icon-shape.icon-md {
            width: 48px;
            height: 48px;
        }

        .btn-icon {
            width: 38px;
            height: 38px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0;
            border-radius: 10px;
        }

        .notification-dropdown {
            min-width: 340px;
            max-height: 420px;
            overflow-y: auto;
            border-radius: 14px !important;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.12) !important;
        }

        .notif-unread {
            background: #f0f7ff;
        }

        @media(max-width:991px) {
            .sidebar {
                left: calc(-1 * var(--sidebar-width));
            }

            .sidebar.show {
                left: 0;
            }

            .topbar,
            .content {
                left: 0;
                margin-left: 0;
            }

            .topbar {
                left: 0;
            }
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
            <span class="fw-semibold d-none d-md-inline"
                style="color: var(--primary);">{{ \App\Models\Setting::get('site_name', 'Nhà Trọ') }}</span>
        </div>
        <div>
            <ul class="list-unstyled d-flex align-items-center mb-0 gap-2">
                {{-- Notification Bell --}}
                <li class="dropdown">
                    <a href="#" class="position-relative btn-icon btn btn-light btn-sm rounded-circle"
                        data-bs-toggle="dropdown">
                        <i class="ti ti-bell" style="font-size:18px;"></i>
                        @php $unreadCount = auth()->user()->unreadNotifications->count(); @endphp
                        @if($unreadCount > 0)
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"
                                style="font-size:9px;">{{ $unreadCount }}</span>
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
                            <div class="px-3 py-2 border-bottom {{ $notif->read_at ? '' : 'notif-unread' }}"
                                style="font-size:13px;">
                                <div class="fw-semibold">{{ $notif->data['title'] ?? '' }}</div>
                                <div class="text-muted">{{ $notif->data['message'] ?? '' }}</div>
                                <div class="text-secondary" style="font-size:11px;">
                                    {{ $notif->created_at->diffForHumans() }}
                                </div>
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
                        <div class="btn btn-light btn-sm d-flex align-items-center gap-2 rounded-pill px-3">
                            <i class="ti ti-user-circle"></i>
                            <span class="small d-none d-md-inline">{{ auth()->user()->name }}</span>
                            <span class="badge bg-primary bg-opacity-15 text-primary" style="font-size:10px;">
                                {{ auth()->user()->isSuperAdmin() ? 'Admin' : (auth()->user()->isStaff() ? 'Staff' : 'Chủ trọ') }}
                            </span>
                        </div>
                    </a>
                    <div class="dropdown-menu dropdown-menu-end p-0" style="min-width:220px; border-radius:12px;">
                        <div class="px-3 py-3 border-bottom">
                            <div class="fw-semibold small">{{ auth()->user()->name }}</div>
                            <div class="text-muted" style="font-size:12px;">{{ auth()->user()->email }}</div>
                        </div>
                        <div class="p-2">
                            @can('manage-settings')
                                <a class="dropdown-item rounded" href="{{ route('admin.settings.index') }}"><i
                                        class="ti ti-settings me-2"></i>Cài đặt</a>
                            @endcan
                            <form method="POST" action="{{ route('logout') }}">@csrf
                                <button type="submit" class="dropdown-item rounded text-danger"><i
                                        class="ti ti-logout me-2"></i>Đăng xuất</button>
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
            <a href="{{ route('admin.dashboard') }}">
                🏠 {{ \App\Models\Setting::get('site_name', 'Nhà Trọ') }}
            </a>
        </div>
        <ul class="nav flex-column mt-2">
            {{-- Dashboard --}}
            <li class="nav-section">Tổng quan</li>
            <li>
                <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"
                    href="{{ route('admin.dashboard') }}">
                    <i class="ti ti-home"></i><span>Dashboard</span>
                </a>
            </li>

            {{-- ═══ SUPER ADMIN: Hệ thống ═══ --}}
            @can('manage-system')
                <li class="nav-section">Hệ thống</li>
                <li>
                    <a class="nav-link {{ request()->routeIs('admin.landlords.*') ? 'active' : '' }}"
                        href="{{ route('admin.landlords.index') }}">
                        <i class="ti ti-home-2"></i><span>Chủ trọ</span>
                    </a>
                </li>
                <li>
                    <a class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}"
                        href="{{ route('admin.users.index') }}">
                        <i class="ti ti-users"></i><span>Người dùng</span>
                        @php $totalUsers = \App\Models\User::whereNotIn('role', ['super_admin'])->count(); @endphp
                        @if($totalUsers > 0)<span class="badge bg-light text-dark">{{ $totalUsers }}</span>@endif
                    </a>
                </li>
                <li>
                    <a class="nav-link {{ request()->routeIs('admin.commissions.*') ? 'active' : '' }}"
                        href="{{ route('admin.commissions.index') }}">
                        <i class="ti ti-coin"></i><span>Phí hoa hồng</span>
                    </a>
                </li>
            @endcan

            {{-- ═══ STAFF: Kiểm duyệt ═══ --}}
            @can('approve-rooms')
                @if(auth()->user()->isStaff())
                    <li class="nav-section">Kiểm duyệt</li>
                    <li>
                        <a class="nav-link {{ request()->routeIs('admin.rooms.*') ? 'active' : '' }}"
                            href="{{ route('admin.rooms.index') }}?approval=pending">
                            <i class="ti ti-building"></i><span>Duyệt phòng</span>
                            @php $pendingRm = \App\Models\Room::where('approval_status', 'pending')->count(); @endphp
                            @if($pendingRm > 0)<span class="badge bg-warning">{{ $pendingRm }}</span>@endif
                        </a>
                    </li>
                    <li>
                        <a class="nav-link {{ request()->routeIs('admin.rent-requests.*') ? 'active' : '' }}"
                            href="{{ route('admin.rent-requests.index') }}">
                            <i class="ti ti-file-description"></i><span>Yêu cầu thuê</span>
                            @php $pendingRR = \App\Models\RentRequest::where('status', 'pending')->count(); @endphp
                            @if($pendingRR > 0)<span class="badge bg-danger">{{ $pendingRR }}</span>@endif
                        </a>
                    </li>
                    <li>
                        <a class="nav-link {{ request()->routeIs('admin.maintenance.*') ? 'active' : '' }}"
                            href="{{ route('admin.maintenance.index') }}">
                            <i class="ti ti-tool"></i><span>Bảo trì</span>
                            @php $pendingMt = \App\Models\MaintenanceRequest::where('status', 'pending')->count(); @endphp
                            @if($pendingMt > 0)<span class="badge bg-danger">{{ $pendingMt }}</span>@endif
                        </a>
                    </li>
                @endif
            @endcan

            {{-- ═══ LANDLORD: Quản lý phòng ═══ --}}
            @can('manage-rooms')
                <li class="nav-section">Quản lý phòng</li>
                <li>
                    <a class="nav-link {{ request()->routeIs('admin.rooms.*') ? 'active' : '' }}"
                        href="{{ route('admin.rooms.index') }}">
                        <i class="ti ti-building"></i><span>Phòng trọ</span>
                    </a>
                </li>
                <li>
                    <a class="nav-link {{ request()->routeIs('admin.rent-requests.*') ? 'active' : '' }}"
                        href="{{ route('admin.rent-requests.index') }}">
                        <i class="ti ti-file-description"></i><span>Yêu cầu thuê</span>
                        @php
                            $pending = \App\Models\RentRequest::where('status', 'pending')
                                ->whereHas('room', fn($q) => $q->where('landlord_id', auth()->id()))
                                ->count();
                        @endphp
                        @if($pending > 0)<span class="badge bg-danger">{{ $pending }}</span>@endif
                    </a>
                </li>
                <li>
                    <a class="nav-link {{ request()->routeIs('admin.bookings.*') ? 'active' : '' }}"
                        href="{{ route('admin.bookings.index') }}">
                        <i class="ti ti-calendar-check"></i><span>Đơn đặt phòng</span>
                        @php
                            $pendingBookingsAdmin = \App\Models\Booking::when(auth()->user()->isLandlord(), fn($q) => $q->whereHas('room', fn($r) => $r->where('landlord_id', auth()->id())))->where('status', 'pending')->where('expired_at', '>', now())->count();
                        @endphp
                        @if($pendingBookingsAdmin > 0)<span class="badge bg-warning text-dark">{{ $pendingBookingsAdmin }}</span>@endif
                    </a>
                </li>
                <li>
                    <a class="nav-link {{ request()->routeIs('admin.contracts.*') ? 'active' : '' }}"
                        href="{{ route('admin.contracts.index') }}">
                        <i class="ti ti-contract"></i><span>Hợp đồng</span>
                    </a>
                </li>
                <li>
                    <a class="nav-link {{ request()->routeIs('admin.utilities.*') ? 'active' : '' }}"
                        href="{{ route('admin.utilities.index') }}">
                        <i class="ti ti-bolt"></i><span>Điện nước</span>
                    </a>
                </li>
                <li>
                    <a class="nav-link {{ request()->routeIs('admin.invoices.*') ? 'active' : '' }}"
                        href="{{ route('admin.invoices.index') }}">
                        <i class="ti ti-receipt"></i><span>Hóa đơn</span>
                    </a>
                </li>
                <li>
                    <a class="nav-link {{ request()->routeIs('admin.maintenance.*') ? 'active' : '' }}"
                        href="{{ route('admin.maintenance.index') }}">
                        <i class="ti ti-tool"></i><span>Bảo trì</span>
                        @php
                            $pendingMaint = \App\Models\MaintenanceRequest::whereHas('room', fn($q) => $q->where('landlord_id', auth()->id()))->where('status', 'pending')->count();
                        @endphp
                        @if($pendingMaint > 0)<span class="badge bg-danger">{{ $pendingMaint }}</span>@endif
                    </a>
                </li>
            @endcan

            {{-- ═══ Báo cáo (nhiều role) ═══ --}}
            @can('view-reports')
                <li class="nav-section">Phân tích</li>
                <li>
                    <a class="nav-link {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}"
                        href="{{ route('admin.reports.index') }}">
                        <i class="ti ti-chart-bar"></i><span>Báo cáo</span>
                    </a>
                </li>
                @can('manage-commissions')
                    <li>
                        <a class="nav-link {{ request()->routeIs('admin.commissions.*') ? 'active' : '' }}"
                            href="{{ route('admin.commissions.index') }}">
                            <i class="ti ti-coin"></i><span>Phí hoa hồng</span>
                        </a>
                    </li>
                @endcan
            @endcan

            {{-- ═══ Cài đặt ═══ --}}
            @can('manage-settings')
                <li class="nav-section">Tùy chỉnh</li>
                {{-- Slider: chỉ admin/super_admin, KHÔNG hiện với chủ trọ --}}
                @if(!auth()->user()->isLandlord())
                    <li>
                        <a class="nav-link {{ request()->routeIs('admin.sliders.*') ? 'active' : '' }}"
                            href="{{ route('admin.sliders.index') }}">
                            <i class="ti ti-photo"></i><span>Slider</span>
                        </a>
                    </li>
                @endif
                <li>
                    <a class="nav-link {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}"
                        href="{{ route('admin.settings.index') }}">
                        <i class="ti ti-settings"></i><span>Cài đặt</span>
                    </a>
                </li>
            @endcan

            <li class="nav-section">Khác</li>
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
            {{-- Validation Errors --}}
            @if($errors->any())
                <div class="alert alert-danger border-0 shadow-sm mb-4"
                    style="border-radius:12px; border-left: 4px solid #dc3545 !important;">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <i class="ti ti-alert-circle fs-5 text-danger"></i>
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
    </main>

    {{-- Toast Notification Container --}}
    <div id="toast-container"
        style="position:fixed;top:80px;right:20px;z-index:9999;display:flex;flex-direction:column;gap:10px;max-width:400px;">
    </div>

    <style>
        .toast-notification {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            padding: 16px 20px;
            border-radius: 14px;
            background: #fff;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.12);
            border-left: 4px solid;
            min-width: 320px;
            animation: toastSlideIn 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            transition: all 0.3s ease;
        }

        .toast-notification.toast-hiding {
            animation: toastSlideOut 0.3s ease forwards;
        }

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

        .toast-notification.toast-error {
            border-left-color: #ef4444;
        }

        .toast-notification.toast-warning {
            border-left-color: #f59e0b;
        }

        .toast-notification.toast-info {
            border-left-color: #3b82f6;
        }

        .toast-icon {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .toast-success .toast-icon {
            background: #ecfdf5;
            color: #10b981;
        }

        .toast-error .toast-icon {
            background: #fef2f2;
            color: #ef4444;
        }

        .toast-warning .toast-icon {
            background: #fffbeb;
            color: #f59e0b;
        }

        .toast-info .toast-icon {
            background: #eff6ff;
            color: #3b82f6;
        }

        .toast-body {
            flex: 1;
        }

        .toast-title {
            font-weight: 600;
            font-size: 13px;
            margin-bottom: 2px;
        }

        .toast-message {
            font-size: 13px;
            color: #64748b;
            line-height: 1.4;
        }

        .toast-close {
            background: none;
            border: none;
            color: #94a3b8;
            cursor: pointer;
            padding: 0;
            font-size: 18px;
            line-height: 1;
            flex-shrink: 0;
            transition: color 0.2s;
        }

        .toast-close:hover {
            color: #1e293b;
        }

        .toast-progress {
            position: absolute;
            bottom: 0;
            left: 4px;
            right: 0;
            height: 3px;
            border-radius: 0 0 14px 0;
            animation: toastProgress 5s linear forwards;
        }

        .toast-success .toast-progress {
            background: #10b981;
        }

        .toast-error .toast-progress {
            background: #ef4444;
        }

        .toast-warning .toast-progress {
            background: #f59e0b;
        }

        .toast-info .toast-progress {
            background: #3b82f6;
        }

        @keyframes toastSlideIn {
            from {
                opacity: 0;
                transform: translateX(100px);
            }

            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        @keyframes toastSlideOut {
            from {
                opacity: 1;
                transform: translateX(0);
            }

            to {
                opacity: 0;
                transform: translateX(100px);
            }
        }

        @keyframes toastProgress {
            from {
                width: 100%;
            }

            to {
                width: 0%;
            }
        }
    </style>

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

        // Toast Notification System
        function showToast(type, title, message, duration = 5000) {
            const icons = {
                success: '<i class="ti ti-circle-check fs-5"></i>',
                error: '<i class="ti ti-alert-triangle fs-5"></i>',
                warning: '<i class="ti ti-alert-circle fs-5"></i>',
                info: '<i class="ti ti-info-circle fs-5"></i>',
            };
            const container = document.getElementById('toast-container');
            const toast = document.createElement('div');
            toast.className = `toast-notification toast-${type}`;
            toast.style.position = 'relative';
            toast.innerHTML = `
            <div class="toast-icon">${icons[type] || icons.info}</div>
            <div class="toast-body">
                <div class="toast-title">${title}</div>
                <div class="toast-message">${message}</div>
            </div>
            <button class="toast-close" onclick="dismissToast(this)">&times;</button>
            <div class="toast-progress"></div>
        `;
            container.appendChild(toast);
            setTimeout(() => dismissToast(toast.querySelector('.toast-close')), duration);
        }

        function dismissToast(btn) {
            const toast = btn.closest('.toast-notification');
            if (!toast || toast.classList.contains('toast-hiding')) return;
            toast.classList.add('toast-hiding');
            setTimeout(() => toast.remove(), 300);
        }

        // Auto-show session messages as toasts
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