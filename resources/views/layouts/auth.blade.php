<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Xác thực') - {{ \App\Models\Setting::get('site_name', 'Nhà Trọ') }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #f0f2f5;
            padding: 24px 16px;
        }

        /* ── Wrapper ── */
        .auth-card {
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 4px 24px rgba(0,0,0,.08);
            width: 100%;
            max-width: 440px;
            padding: 40px 36px;
            animation: fadeUp .35s ease both;
        }
        .auth-card.register-card { max-width: 640px; }

        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(16px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        /* ── Logo ── */
        .auth-logo {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            font-weight: 700;
            font-size: 1.25rem;
            color: #4f46e5;
            text-decoration: none;
            margin-bottom: 28px;
        }
        .auth-logo .logo-icon {
            width: 36px; height: 36px;
            background: linear-gradient(135deg, #6366f1, #4f46e5);
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            color: #fff; font-size: .95rem;
        }

        /* ── Header ── */
        .auth-header { text-align: center; margin-bottom: 28px; }
        .auth-header h2 { font-size: 1.5rem; font-weight: 700; color: #111827; }
        .auth-header p  { font-size: .875rem; color: #6b7280; margin-top: 4px; }

        /* ── Error block ── */
        .error-block {
            background: #fef2f2;
            border: 1px solid #fecaca;
            border-radius: 10px;
            padding: 12px 16px;
            margin-bottom: 20px;
            font-size: .8rem;
            color: #b91c1c;
        }
        .error-block ul { padding-left: 16px; margin: 6px 0 0; }

        /* ── Form ── */
        .auth-form { display: flex; flex-direction: column; gap: 16px; }

        .form-group { display: flex; flex-direction: column; gap: 6px; }
        .form-row-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; }
        @media (max-width: 540px) { .form-row-2 { grid-template-columns: 1fr; } }

        .form-group label {
            font-size: .8rem;
            font-weight: 600;
            color: #374151;
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .form-group label i { color: #9ca3af; font-size: .75rem; }

        .label-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .forgot-link {
            font-size: .78rem;
            color: #6366f1;
            text-decoration: none;
            font-weight: 500;
        }
        .forgot-link:hover { text-decoration: underline; }

        /* ── Inputs ── */
        .form-input {
            width: 100%;
            padding: 10px 14px;
            border: 1.5px solid #e5e7eb;
            border-radius: 10px;
            font-size: .9rem;
            color: #111827;
            background: #fff;
            transition: border-color .2s, box-shadow .2s;
            outline: none;
        }
        .form-input::placeholder { color: #9ca3af; }
        .form-input:focus {
            border-color: #6366f1;
            box-shadow: 0 0 0 3px rgba(99,102,241,.15);
        }
        .form-input.is-invalid { border-color: #ef4444; }

        .input-eye-wrap { position: relative; }
        .input-eye-wrap .form-input { padding-right: 40px; }
        .eye-btn {
            position: absolute;
            right: 12px; top: 50%;
            transform: translateY(-50%);
            background: none; border: none;
            color: #9ca3af; cursor: pointer;
            font-size: .85rem; padding: 0;
        }
        .eye-btn:hover { color: #6366f1; }

        .error-msg { font-size: .76rem; color: #ef4444; }

        /* ── Role selector ── */
        .role-selector {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
        }
        .role-btn {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 6px;
            padding: 12px 10px;
            border: 1.5px solid #e5e7eb;
            border-radius: 12px;
            background: #fff;
            color: #6b7280;
            font-size: .85rem;
            font-weight: 500;
            cursor: pointer;
            transition: all .2s;
        }
        .role-btn i { font-size: 1.1rem; }
        .role-btn:hover { border-color: #6366f1; color: #6366f1; background: #eef2ff; }
        .role-btn.active {
            border-color: #6366f1;
            background: #eef2ff;
            color: #4f46e5;
            font-weight: 600;
        }

        /* ── Remember checkbox ── */
        .remember-row { margin-top: -4px; }
        .checkbox-label {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: .85rem;
            color: #6b7280;
            cursor: pointer;
            user-select: none;
        }
        .checkbox-label input[type="checkbox"] { accent-color: #6366f1; width: 15px; height: 15px; }
        .checkmark { display: none; }

        /* ── Submit button ── */
        .btn-submit {
            width: 100%;
            padding: 11px;
            background: linear-gradient(135deg, #6366f1, #4f46e5);
            color: #fff;
            font-weight: 600;
            font-size: .9rem;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            transition: opacity .2s, transform .1s;
            margin-top: 4px;
        }
        .btn-submit:hover  { opacity: .92; }
        .btn-submit:active { transform: scale(.98); }

        /* ── Switch link ── */
        .auth-switch {
            text-align: center;
            font-size: .85rem;
            color: #6b7280;
            margin-top: 4px;
        }
        .auth-switch a {
            color: #6366f1;
            font-weight: 600;
            text-decoration: none;
        }
        .auth-switch a:hover { text-decoration: underline; }

        /* ── Toast ── */
        #toast-container {
            position: fixed; top: 20px; right: 20px;
            z-index: 99999; display: flex;
            flex-direction: column; gap: 10px; max-width: 360px;
        }
        .toast-notification {
            display: flex; align-items: flex-start; gap: 12px;
            padding: 14px 18px; border-radius: 12px;
            background: #fff; box-shadow: 0 6px 24px rgba(0,0,0,.12);
            border-left: 4px solid; min-width: 260px; position: relative;
            animation: toastIn .35s cubic-bezier(.175,.885,.32,1.275);
            font-family: 'Inter', sans-serif;
        }
        .toast-notification.toast-hiding { animation: toastOut .25s ease forwards; }
        .toast-notification.toast-success { border-left-color: #10b981; }
        .toast-notification.toast-error   { border-left-color: #ef4444; }
        .toast-notification.toast-warning { border-left-color: #f59e0b; }
        .toast-notification.toast-info    { border-left-color: #3b82f6; }
        .toast-icon { width:32px; height:32px; border-radius:8px; display:flex; align-items:center; justify-content:center; flex-shrink:0; font-size:14px; }
        .toast-success .toast-icon { background:#ecfdf5; color:#10b981; }
        .toast-error   .toast-icon { background:#fef2f2; color:#ef4444; }
        .toast-warning .toast-icon { background:#fffbeb; color:#f59e0b; }
        .toast-info    .toast-icon { background:#eff6ff; color:#3b82f6; }
        .toast-body    { flex:1; }
        .toast-title   { font-weight:600; font-size:13px; color:#111827; margin-bottom:2px; }
        .toast-message { font-size:12px; color:#6b7280; }
        .toast-close   { background:none; border:none; color:#9ca3af; cursor:pointer; font-size:16px; flex-shrink:0; }
        .toast-close:hover { color:#111827; }
        @keyframes toastIn  { from{opacity:0;transform:translateX(80px)} to{opacity:1;transform:translateX(0)} }
        @keyframes toastOut { from{opacity:1;transform:translateX(0)}    to{opacity:0;transform:translateX(80px)} }

        /* ── Alert info ── */
        .alert-info-custom {
            background: #eff6ff; border: 1px solid #bfdbfe;
            border-radius: 10px; padding: 10px 14px;
            font-size: .85rem; color: #1d4ed8;
        }
    </style>
    @yield('styles')
</head>
<body>
    <div class="auth-card @yield('card-class')">

        {{-- Logo --}}
        <a href="{{ route('home') }}" class="auth-logo">
            <span class="logo-icon"><i class="fa fa-home"></i></span>
            {{ \App\Models\Setting::get('site_name', 'Nhà Trọ') }}
        </a>

        {{-- Validation errors --}}
        @if($errors->any())
            <div class="error-block">
                <strong><i class="fa fa-exclamation-circle me-1"></i> Vui lòng kiểm tra lại:</strong>
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @yield('content')
    </div>

    {{-- Toast Container --}}
    <div id="toast-container"></div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        // Toggle show/hide password
        function togglePwd(id, btn) {
            const inp = document.getElementById(id);
            const icon = btn.querySelector('i');
            if (inp.type === 'password') {
                inp.type = 'text';
                icon.classList.replace('fa-eye', 'fa-eye-slash');
            } else {
                inp.type = 'password';
                icon.classList.replace('fa-eye-slash', 'fa-eye');
            }
        }

        // Toast
        function showToast(type, title, message, duration) {
            duration = duration || 4000;
            var icons = {
                success: '<i class="fa fa-check-circle"></i>',
                error:   '<i class="fa fa-exclamation-triangle"></i>',
                warning: '<i class="fa fa-exclamation-circle"></i>',
                info:    '<i class="fa fa-info-circle"></i>'
            };
            var c = document.getElementById('toast-container');
            var t = document.createElement('div');
            t.className = 'toast-notification toast-' + type;
            t.innerHTML = '<div class="toast-icon">' + (icons[type] || icons.info) + '</div>'
                + '<div class="toast-body"><div class="toast-title">' + title + '</div>'
                + '<div class="toast-message">' + message + '</div></div>'
                + '<button class="toast-close" onclick="dismissToast(this)">&times;</button>';
            c.appendChild(t);
            setTimeout(function () { dismissToast(t.querySelector('.toast-close')); }, duration);
        }
        function dismissToast(b) {
            var t = b.closest('.toast-notification');
            if (!t || t.classList.contains('toast-hiding')) return;
            t.classList.add('toast-hiding');
            setTimeout(function () { t.remove(); }, 280);
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
    </script>
    @yield('scripts')
</body>
</html>
