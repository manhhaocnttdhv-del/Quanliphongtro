@extends('layouts.user')
@section('title', 'Danh Sách Phòng - ' . \App\Models\Setting::get('site_name','Nhà Trọ'))

@section('content')
<style>
    .rooms-section { padding: 80px 0; background: #f8fafc; }
    .room-card { 
        background: #fff; 
        border-radius: 16px; 
        border: 1px solid rgba(0,0,0,0.05);
        overflow: hidden; 
        box-shadow: 0 4px 15px rgba(0,0,0,0.04); 
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); 
    }
    .room-card:hover { 
        transform: translateY(-8px); 
        box-shadow: 0 12px 30px rgba(0,0,0,0.08); 
    }
    .room-card .image-wrapper {
        position: relative;
        overflow: hidden;
    }
    .room-card img { 
        width: 100%; 
        height: 240px; 
        object-fit: cover; 
        transition: transform 0.5s;
    }
    .room-card:hover img {
        transform: scale(1.1);
    }
    .room-card .price-tag {
        position: absolute;
        bottom: 12px;
        right: 12px;
        background: rgba(255, 255, 255, 0.95);
        padding: 6px 14px;
        border-radius: 10px;
        font-weight: 700;
        color: #eab308;
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }
    .room-card-body { padding: 20px; }
    .room-badge { 
        font-size: 10px; 
        font-weight: 700; 
        padding: 4px 12px; 
        border-radius: 50px; 
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .search-bar { 
        background: #fff; 
        padding: 30px; 
        border-radius: 20px; 
        box-shadow: 0 10px 25px rgba(0,0,0,0.03); 
        margin-bottom: 40px; 
    }
    .search-bar .form-control {
        border-radius: 12px;
        padding: 12px 18px;
        border: 1px solid #e2e8f0;
        background: #f8fafc;
    }
    .search-bar .form-control:focus {
        background: #fff;
        border-color: #eab308;
        box-shadow: 0 0 0 3px rgba(234, 179, 8, 0.1);
    }
    .btn-search {
        border-radius: 12px;
        padding: 12px;
        font-weight: 600;
    }
</style>

<div class="rooms-section">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="fw-bold mb-2">Tìm kiếm không gian sống lý tưởng</h2>
            <p class="text-muted">Lọc danh sách các phòng trọ tốt nhất dành cho bạn</p>
        </div>

        {{-- Search bar --}}
        <div class="search-bar">
            <form method="GET" action="{{ route('rooms.index') }}">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label small fw-bold text-dark">Từ khóa tìm kiếm</label>
                        <input type="text" class="form-control" name="search" value="{{ request('search') }}" placeholder="Tên phòng, mô tả...">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small fw-bold text-dark">Tỉnh/Thành phố</label>
                        <select name="province" id="province" class="form-select">
                            <option value="">Tất cả</option>
                            @if(request('province'))
                                <option value="{{ request('province') }}" selected>{{ request('province') }}</option>
                            @endif
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small fw-bold text-dark">Quận/Huyện</label>
                        <select name="district" id="district" class="form-select" {{ request('province') ? '' : 'disabled' }}>
                            <option value="">Tất cả</option>
                            @if(request('district'))
                                <option value="{{ request('district') }}" selected>{{ request('district') }}</option>
                            @endif
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small fw-bold text-dark">Phường/Xã</label>
                        <select name="ward" id="ward" class="form-select" {{ request('district') ? '' : 'disabled' }}>
                            <option value="">Tất cả</option>
                            @if(request('ward'))
                                <option value="{{ request('ward') }}" selected>{{ request('ward') }}</option>
                            @endif
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small fw-bold text-dark">Giá tối đa</label>
                        <input type="number" class="form-control" name="max_price" value="{{ request('max_price') }}" placeholder="VD: 3,000,000">
                    </div>
                    <div class="col-md-1">
                        <label class="form-label d-none d-md-block">&nbsp;</label>
                        <button type="submit" class="btn btn-warning btn-search w-100 shadow-sm text-dark p-2">
                            <i class="fa fa-search"></i>
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <div class="row g-4">
            @forelse($rooms as $room)
                <div class="col-md-4 col-sm-6">
                    <div class="room-card h-100">
                        <div class="image-wrapper">
                            <a href="{{ route('rooms.show', $room) }}">
                                @if($room->images->first())
                                    <img src="{{ asset('storage/'.$room->images->first()->image_path) }}" alt="{{ $room->name }}">
                                @else
                                    <img src="/user/images/room{{ ($room->id % 6) + 1 }}.jpg" alt="{{ $room->name }}">
                                @endif
                            </a>
                            <div class="price-tag">{{ number_format($room->price) }}đ</div>
                        </div>
                        <div class="room-card-body">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span class="room-badge bg-{{ $room->statusBadge() === 'success' ? 'success' : 'secondary' }} text-white">
                                    {{ $room->statusLabel() }}
                                </span>
                                <div class="text-muted small">
                                    <i class="fa fa-map-marker me-1"></i>{{ $room->province_name ?? 'TP.HCM' }}
                                </div>
                            </div>
                            <h5 class="fw-bold mb-3">
                                <a href="{{ route('rooms.show', $room) }}" class="text-dark text-decoration-none hover-primary">
                                    {{ $room->name }}
                                </a>
                            </h5>
                            <p class="text-muted small mb-4" style="height: 48px; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                                {{ $room->description }}
                            </p>
                            <div class="d-flex align-items-center gap-4 text-muted small mb-4">
                                @if($room->area)
                                    <span><i class="fa fa-expand me-2 text-warning"></i>{{ $room->area }} m²</span>
                                @endif
                                @if($room->floor)
                                    <span><i class="fa fa-building me-2 text-warning"></i>Tầng {{ $room->floor }}</span>
                                @endif
                                <span><i class="fa fa-wifi me-2 text-warning"></i>Free Wi-Fi</span>
                            </div>
                            <div class="border-top pt-3">
                                <a href="{{ route('rooms.show', $room) }}" class="btn btn-outline-dark btn-sm w-100 rounded-pill py-2 fw-semibold border-2">
                                    Xem chi tiết phòng
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="text-center py-5">
                        <div class="mb-4">
                            <i class="fa fa-search fa-4x text-light opacity-50"></i>
                        </div>
                        <h4 class="text-muted fw-bold">Rất tiếc, không tìm thấy kết quả</h4>
                        <p class="text-muted mb-4">Bạn hãy thử thay đổi từ khóa hoặc khoảng giá nhé.</p>
                        <a href="{{ route('rooms.index') }}" class="btn btn-warning px-4 rounded-pill fw-bold text-dark">Xem tất cả phòng</a>
                    </div>
                </div>
            @endforelse
        </div>

        <div class="mt-5 d-flex justify-content-center">
            {{ $rooms->links() }}
        </div>
    </div>
</div>
@endsection
