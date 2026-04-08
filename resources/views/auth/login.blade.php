@extends('layouts.auth')
@section('title', 'Đăng nhập')

@section('content')
    <div class="auth-header">
        <h2>Chào mừng trở lại</h2>
        <p>Đăng nhập để tiếp tục</p>
    </div>

    @if(session('status'))
        <div class="alert-info-custom mb-4">{{ session('status') }}</div>
    @endif

    <form method="POST" action="{{ route('login') }}" class="auth-form">
        @csrf

        <div class="form-group">
            <label for="email">
                <i class="fa fa-envelope"></i> Email
            </label>
            <input id="email" type="email" name="email"
                class="form-input @error('email') is-invalid @enderror"
                value="{{ old('email') }}" required autofocus autocomplete="username"
                placeholder="email@example.com">
            @error('email')
                <span class="error-msg">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group">
            <div class="label-row">
                <label for="password">
                    <i class="fa fa-lock"></i> Mật khẩu
                </label>
                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}" class="forgot-link">Quên mật khẩu?</a>
                @endif
            </div>
            <div class="input-eye-wrap">
                <input id="password" type="password" name="password"
                    class="form-input @error('password') is-invalid @enderror"
                    required autocomplete="current-password"
                    placeholder="••••••••">
                <button type="button" class="eye-btn" onclick="togglePwd('password', this)">
                    <i class="fa fa-eye"></i>
                </button>
            </div>
            @error('password')
                <span class="error-msg">{{ $message }}</span>
            @enderror
        </div>

        <div class="remember-row">
            <label class="checkbox-label">
                <input type="checkbox" name="remember" id="remember_me">
                <span class="checkmark"></span>
                Ghi nhớ đăng nhập
            </label>
        </div>

        <button type="submit" class="btn-submit">
            <i class="fa fa-sign-in-alt me-2"></i> Đăng nhập
        </button>

        <p class="auth-switch">
            Chưa có tài khoản?
            <a href="{{ route('register') }}">Đăng ký ngay</a>
        </p>
    </form>
@endsection
