@extends('layouts.auth')
@section('title', 'Đăng nhập')

@section('content')
    <h3 class="text-center mb-4 fw-bold" style="color: #444;">Mừng bạn quay lại!</h3>
    
    @if(session('status'))
        <div class="alert alert-success mb-4">{{ session('status') }}</div>
    @endif

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input id="email" type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" required autofocus autocomplete="username">
            @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <div class="d-flex justify-content-between">
                <label for="password" class="form-label">Mật khẩu</label>
                @if (Route::has('password.request'))
                    <a class="text-sm text-primary text-decoration-none" href="{{ route('password.request') }}" style="font-size: 0.85rem;">
                        Quên mật khẩu?
                    </a>
                @endif
            </div>
            <input id="password" type="password" name="password" class="form-control @error('password') is-invalid @enderror" required autocomplete="current-password">
            @error('password')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-4 form-check">
            <input id="remember_me" type="checkbox" class="form-check-input" name="remember">
            <label class="form-check-label text-muted" for="remember_me" style="font-size: 0.9rem;">Ghi nhớ đăng nhập</label>
        </div>

        <button type="submit" class="btn btn-primary-custom w-100 mb-3">
            Đăng nhập ngay
        </button>
        
        <div class="text-center mt-3">
            <span class="text-muted">Chưa có tài khoản?</span>
            <a href="{{ route('register') }}" class="text-primary text-decoration-none fw-semibold">Đăng ký ngay</a>
        </div>
    </form>
@endsection
