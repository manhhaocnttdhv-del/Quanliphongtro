@extends('layouts.user')

@section('title', $room->name . ' - Chi Tiết Phòng')

@section('content')
<style>
    .room-detail { padding: 60px 0; background: #f8fafc; }
    .room-gallery-main {
        position: relative;
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        margin-bottom: 20px;
    }
    .room-gallery-main img {
        width: 100%;
        height: 450px;
        object-fit: cover;
    }
    .thumb-scroll {
        display: flex;
        gap: 12px;
        overflow-x: auto;
        padding-bottom: 10px;
    }
    .thumb-item {
        flex: 0 0 100px;
        height: 70px;
        border-radius: 12px;
        overflow: hidden;
        cursor: pointer;
        border: 2px solid transparent;
        transition: all 0.2s;
    }
    .thumb-item.active, .thumb-item:hover {
        border-color: #eab308;
    }
    .thumb-item img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .info-card { 
        background: #fff; 
        border-radius: 24px; 
        padding: 32px; 
        box-shadow: 0 4px 25px rgba(0,0,0,0.03); 
        border: 1px solid rgba(0,0,0,0.05);
    }
    .price-display {
        background: #fefce8;
        padding: 20px;
        border-radius: 16px;
        border: 1px dashed #eab308;
        margin-bottom: 24px;
    }
    .price-value {
        font-size: 32px;
        font-weight: 800;
        color: #ca8a04;
    }
    .spec-item {
        display: flex;
        align-items: center;
        gap: 16px;
        padding: 14px 0;
        border-bottom: 1px solid #f1f5f9;
    }
    .spec-item:last-child { border-bottom: none; }
    .spec-icon {
        width: 40px;
        height: 40px;
        background: #f8fafc;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #64748b;
    }
    .request-btn { 
        background: #eab308; 
        color: #000; 
        border: none; 
        padding: 18px; 
        border-radius: 16px; 
        font-size: 18px; 
        font-weight: 700; 
        width: 100%; 
        box-shadow: 0 10px 20px rgba(234, 179, 8, 0.2);
        transition: all 0.3s;
    }
    .request-btn:hover { 
        transform: translateY(-2px);
        box-shadow: 0 15px 30px rgba(234, 179, 8, 0.3);
        background: #fbbf24;
    }
</style>

<div class="room-detail">
    <div class="container">
        <nav aria-label="breadcrumb" class="mb-5">
            <ol class="breadcrumb bg-white p-3 rounded-pill shadow-sm px-4">
                <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-decoration-none text-muted">Trang chủ</a></li>
                <li class="breadcrumb-item"><a href="{{ route('rooms.index') }}" class="text-decoration-none text-muted">Phòng trọ</a></li>
                <li class="breadcrumb-item active fw-bold text-dark">{{ $room->name }}</li>
            </ol>
        </nav>

        <div class="row g-5">
            {{-- Left Side: Gallery & Description --}}
            <div class="col-lg-8">
                <div class="room-gallery-main">
                    @if($room->images->count() > 0)
                        <img id="mainImg" src="{{ asset('storage/'.$room->images->first()->image_path) }}" alt="{{ $room->name }}">
                    @else
                        <img id="mainImg" src="/user/images/room1.jpg" alt="{{ $room->name }}">
                    @endif
                    <div class="position-absolute top-0 start-0 m-4">
                        <span class="badge bg-{{ $room->isAvailable() ? 'success' : 'danger' }} px-3 py-2 rounded-pill shadow">
                            {{ $room->statusLabel() }}
                        </span>
                    </div>
                </div>

                @if($room->images->count() > 1)
                    <div class="thumb-scroll mb-5">
                        @foreach($room->images as $index => $img)
                            <div class="thumb-item {{ $index === 0 ? 'active' : '' }}" onclick="changeImage(this, '{{ asset('storage/'.$img->image_path) }}')">
                                <img src="{{ asset('storage/'.$img->image_path) }}">
                            </div>
                        @endforeach
                    </div>
                @endif

                <div class="info-card mb-5">
                    <h3 class="fw-bold mb-4">Chi tiết phòng</h3>
                    <div style="line-height: 1.8; color: #475569; font-size: 16px;">
                        {!! nl2br(e($room->description)) !!}
                    </div>
                </div>

                @if($room->amenities)
                    <div class="info-card">
                        <h4 class="fw-bold mb-4">Tiện ích & Dịch vụ</h4>
                        <div class="row g-4">
                            @foreach($room->amenities as $amenity)
                                <div class="col-md-4 col-sm-6">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="spec-icon text-success bg-success bg-opacity-10">
                                            <i class="fa fa-check"></i>
                                        </div>
                                        <span class="fw-semibold">{{ $amenity }}</span>
                                    </div>
                                </div>
                            @endforeach
                            <div class="col-md-4 col-sm-6">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="spec-icon text-info bg-info bg-opacity-10">
                                        <i class="fa fa-wifi"></i>
                                    </div>
                                    <span class="fw-semibold">Wi-Fi Tốc độ cao</span>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            {{-- Right Side: Booking & Stats --}}
            <div class="col-lg-4">
                <div class="sticky-top" style="top: 100px; z-index: 10;">
                    <div class="info-card">
                        <h2 class="fw-bold mb-3 h4">{{ $room->name }}</h2>
                        
                        <div class="price-display">
                            <div class="small text-muted mb-1 fw-bold">GIÁ THUÊ CỐ ĐỊNH</div>
                            <div class="price-value">{{ number_format($room->price) }}đ <span class="small text-muted fw-normal" style="font-size: 14px;">/tháng</span></div>
                        </div>

                        <div class="mb-5">
                            <div class="spec-item">
                                <div class="spec-icon"><i class="fa fa-expand"></i></div>
                                <div>
                                    <div class="small text-muted">Diện tích</div>
                                    <div class="fw-bold">{{ $room->area ?? '25' }} m²</div>
                                </div>
                            </div>
                            <div class="spec-item">
                                <div class="spec-icon"><i class="fa fa-bolt"></i></div>
                                <div>
                                    <div class="small text-muted">Giá điện</div>
                                    <div class="fw-bold text-warning">{{ number_format($room->electricity_price) }}đ/kWh</div>
                                </div>
                            </div>
                            <div class="spec-item">
                                <div class="spec-icon"><i class="fa fa-tint"></i></div>
                                <div>
                                    <div class="small text-muted">Giá nước</div>
                                    <div class="fw-bold text-info">{{ number_format($room->water_price) }}đ/m³</div>
                                </div>
                            </div>
                        </div>

                        @if($room->isAvailable())
                            @auth
                                @if($hasActiveRequest)
                                    <div class="alert alert-soft-warning p-3 border-0 rounded-4 text-center">
                                        <i class="fa fa-clock me-2"></i>Bạn đã gửi yêu cầu thuê
                                    </div>
                                @else
                                    <a href="{{ route('rent-requests.create', $room) }}" class="request-btn d-block text-center text-decoration-none">
                                        ĐẶT PHÒNG NGAY
                                    </a>
                                @endif
                            @else
                                <a href="{{ route('login') }}" class="request-btn d-block text-center text-decoration-none shadow-none bg-dark text-white">
                                    ĐĂNG NHẬP ĐỂ ĐẶT PHÒNG
                                </a>
                            @endauth
                        @else
                            <button class="request-btn bg-secondary bg-opacity-25 text-muted cursor-not-allowed" disabled>
                                ĐÃ CÓ NGƯỜI THUÊ
                            </button>
                        @endif

                        <div class="mt-5 pt-4 border-top">
                            <div class="d-flex align-items-center gap-3">
                                <div class="avatar-md rounded-circle bg-primary bg-opacity-10 d-flex align-items-center justify-content-center text-primary fw-bold" style="width: 54px; height: 54px; font-size: 20px;">
                                    {{ substr($room->landlord->name ?? 'A', 0, 1) }}
                                </div>
                                <div class="flex-grow-1">
                                    <div class="small text-muted fw-bold text-uppercase" style="font-size: 10px;">Chủ phòng</div>
                                    <div class="fw-bold text-dark">{{ $room->landlord->name ?? 'Admin' }}</div>
                                </div>
                                <div class="text-warning">
                                    <i class="fa fa-star"></i>
                                    <i class="fa fa-star"></i>
                                    <i class="fa fa-star"></i>
                                    <i class="fa fa-star"></i>
                                    <i class="fa fa-star"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
