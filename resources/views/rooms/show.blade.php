@extends('layouts.user')

@section('title', $room->name . ' - Chi Tiết Phòng')

@section('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
<style>
    .room-detail { padding: 40px 0 80px; background: linear-gradient(180deg, #f8fafc 0%, #eef2f7 100%); }

    /* ===== BREADCRUMB ===== */
    .breadcrumb-wrap .breadcrumb {
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.04);
        padding: 12px 20px;
        font-size: 13px;
    }

    /* ===== GALLERY ===== */
    .gallery-main {
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 10px 30px rgba(0,0,0,0.08);
        margin-bottom: 12px;
        position: relative;
        height: 420px;
        background: #1e293b;
    }
    .gallery-main img { width: 100%; height: 100%; object-fit: cover; }
    .gallery-main .status-badge {
        position: absolute; top: 16px; left: 16px;
        font-size: 11px; font-weight: 700; padding: 5px 14px;
        border-radius: 50px; text-transform: uppercase; letter-spacing: 0.5px;
    }
    .gallery-main .img-count {
        position: absolute; bottom: 16px; right: 16px;
        background: rgba(0,0,0,0.5); color: #fff;
        font-size: 12px; font-weight: 600; padding: 4px 12px;
        border-radius: 8px; backdrop-filter: blur(4px);
    }
    .thumb-scroll {
        display: flex; gap: 10px; overflow-x: auto;
        padding-bottom: 4px; margin-bottom: 28px;
    }
    .thumb-scroll::-webkit-scrollbar { height: 4px; }
    .thumb-scroll::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 4px; }
    .thumb-item {
        flex: 0 0 90px; height: 65px; border-radius: 10px;
        overflow: hidden; cursor: pointer;
        border: 2px solid transparent; transition: all 0.2s;
    }
    .thumb-item.active, .thumb-item:hover { border-color: #f59e0b; }
    .thumb-item img { width: 100%; height: 100%; object-fit: cover; }

    /* ===== INFO CARD ===== */
    .info-card {
        background: #fff;
        border-radius: 20px;
        padding: 28px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.04);
        border: 1px solid rgba(0,0,0,0.04);
        margin-bottom: 24px;
    }
    .info-card h4, .info-card h3 { font-weight: 700; color: #1e293b; }
    .card-section-title {
        font-size: 16px; font-weight: 700; color: #1e293b;
        padding-bottom: 14px; margin-bottom: 18px;
        border-bottom: 2px solid #f1f5f9;
        display: flex; align-items: center; gap: 8px;
    }
    .card-section-title i { color: #f59e0b; }

    /* Description */
    .room-description {
        line-height: 1.8; color: #475569; font-size: 15px;
        white-space: pre-wrap; word-break: break-word;
    }

    /* Amenity chips */
    .amenity-chip {
        display: inline-flex; align-items: center; gap: 6px;
        background: #f0fdf4; color: #15803d; border: 1px solid #bbf7d0;
        padding: 5px 14px; border-radius: 50px; font-size: 13px; font-weight: 600;
        margin: 4px;
    }
    .amenity-chip i { font-size: 11px; }

    /* Spec list */
    .spec-row { display: flex; align-items: center; gap: 14px; padding: 12px 0; border-bottom: 1px solid #f8fafc; }
    .spec-row:last-child { border-bottom: none; }
    .spec-icon {
        width: 38px; height: 38px; border-radius: 10px; flex-shrink: 0;
        background: #f8fafc; display: flex; align-items: center; justify-content: center;
        font-size: 15px; color: #64748b;
    }
    .spec-label { font-size: 11px; color: #94a3b8; font-weight: 600; text-transform: uppercase; }
    .spec-value { font-size: 14px; font-weight: 700; color: #1e293b; }

    /* Map */
    #roomMap { height: 300px; border-radius: 14px; overflow: hidden; border: 2px solid #e2e8f0; }

    /* ===== BOOKING SIDEBAR ===== */
    .booking-card {
        background: #fff; border-radius: 20px; padding: 28px;
        box-shadow: 0 8px 30px rgba(0,0,0,0.06);
        border: 1px solid rgba(0,0,0,0.04);
        position: sticky; top: 90px;
    }
    .price-badge {
        background: linear-gradient(135deg, #fef3c7, #fde68a);
        border: 1px dashed #f59e0b; border-radius: 14px;
        padding: 18px; margin-bottom: 22px; text-align: center;
    }
    .price-badge .label { font-size: 11px; color: #92400e; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; }
    .price-badge .amount { font-size: 30px; font-weight: 800; color: #b45309; line-height: 1.2; }
    .price-badge .per { font-size: 13px; color: #92400e; }
    .rating-summary { display: flex; align-items: center; gap: 8px; margin-bottom: 18px; }
    .rating-big { font-size: 32px; font-weight: 800; color: #f59e0b; }
    .rating-stars i { font-size: 14px; }
    .rating-count { font-size: 12px; color: #94a3b8; }

    .btn-book {
        display: block; width: 100%; text-align: center;
        padding: 16px; background: linear-gradient(135deg, #f59e0b, #d97706);
        color: #fff; text-decoration: none; border: none;
        border-radius: 14px; font-size: 16px; font-weight: 700;
        box-shadow: 0 8px 20px rgba(217,119,6,0.25);
        transition: all 0.3s; cursor: pointer;
    }
    .btn-book:hover { transform: translateY(-2px); box-shadow: 0 12px 28px rgba(217,119,6,0.35); color: #fff; }
    .btn-book:disabled { background: #e2e8f0; color: #94a3b8; box-shadow: none; cursor: not-allowed; }

    .landlord-card {
        display: flex; align-items: center; gap: 14px;
        padding: 16px; background: #f8fafc; border-radius: 14px; margin-top: 16px;
    }
    .landlord-avatar {
        width: 46px; height: 46px; border-radius: 50%;
        background: linear-gradient(135deg, #3b82f6, #6366f1);
        display: flex; align-items: center; justify-content: center;
        color: #fff; font-weight: 700; font-size: 18px; flex-shrink: 0;
    }

    /* ===== REVIEWS ===== */
    .review-card {
        background: #f8fafc; border-radius: 14px; padding: 18px; margin-bottom: 14px;
        border: 1px solid #f1f5f9; transition: box-shadow 0.2s;
    }
    .review-card:hover { box-shadow: 0 4px 12px rgba(0,0,0,0.05); }
    .reviewer-avatar {
        width: 40px; height: 40px; border-radius: 50%;
        background: linear-gradient(135deg, #10b981, #059669);
        display: flex; align-items: center; justify-content: center;
        color: #fff; font-weight: 700; font-size: 15px; flex-shrink: 0;
    }
    .review-stars i { font-size: 13px; }
    .review-title { font-weight: 700; font-size: 14px; color: #1e293b; }
    .review-body { font-size: 14px; color: #475569; line-height: 1.7; margin-top: 8px; }
    .review-date { font-size: 11px; color: #94a3b8; }

    /* Star Rating Input */
    .star-input { display: flex; gap: 4px; flex-direction: row-reverse; justify-content: flex-end; }
    .star-input input { display: none; }
    .star-input label {
        font-size: 28px; color: #e2e8f0; cursor: pointer;
        transition: color 0.15s, transform 0.1s;
    }
    .star-input label:hover,
    .star-input label:hover ~ label,
    .star-input input:checked ~ label { color: #f59e0b; }
    .star-input label:hover { transform: scale(1.15); }

    /* Rating bar */
    .rating-bar-row { display: flex; align-items: center; gap: 10px; margin-bottom: 6px; }
    .rating-bar-row .star-label { font-size: 12px; color: #64748b; white-space: nowrap; width: 40px; text-align: right; }
    .rating-bar { flex: 1; height: 8px; background: #f1f5f9; border-radius: 4px; overflow: hidden; }
    .rating-bar-fill { height: 100%; background: linear-gradient(90deg, #f59e0b, #fbbf24); border-radius: 4px; transition: width 0.4s; }
    .rating-bar-count { font-size: 12px; color: #94a3b8; width: 20px; }
</style>
@endsection

@section('content')
<div class="room-detail">
    <div class="container">

        {{-- Breadcrumb --}}
        <nav class="breadcrumb-wrap mb-4" aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-decoration-none text-muted">Trang chủ</a></li>
                <li class="breadcrumb-item"><a href="{{ route('rooms.index') }}" class="text-decoration-none text-muted">Phòng trọ</a></li>
                <li class="breadcrumb-item active text-dark fw-bold">{{ Str::limit($room->name, 50) }}</li>
            </ol>
        </nav>

        <div class="row g-4">
            {{-- LEFT: Gallery + Details + Reviews --}}
            <div class="col-lg-8">

                {{-- Gallery --}}
                <div class="gallery-main">
                    @php $firstImg = $room->images->first(); @endphp
                    @if($firstImg)
                        <img id="mainImg" src="{{ asset('storage/'.$firstImg->image_path) }}" alt="{{ $room->name }}">
                    @else
                        <img id="mainImg" src="https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?w=800&h=500&fit=crop" alt="{{ $room->name }}">
                    @endif
                    <span class="status-badge bg-{{ $room->isAvailable() ? 'success' : 'danger' }} text-white">
                        {{ $room->statusLabel() }}
                    </span>
                    @if($room->images->count() > 1)
                        <div class="img-count"><i class="fa fa-camera me-1"></i>{{ $room->images->count() }} ảnh</div>
                    @endif
                </div>

                @if($room->images->count() > 1)
                <div class="thumb-scroll">
                    @foreach($room->images as $idx => $img)
                        <div class="thumb-item {{ $idx === 0 ? 'active' : '' }}"
                             onclick="changeImage(this, '{{ asset('storage/'.$img->image_path) }}')">
                            <img src="{{ asset('storage/'.$img->image_path) }}" alt="">
                        </div>
                    @endforeach
                </div>
                @endif

                {{-- Chi tiết phòng --}}
                <div class="info-card">
                    <div class="card-section-title"><i class="fa fa-info-circle"></i>Mô tả phòng</div>
                    @if($room->description)
                        <div class="room-description">{{ $room->description }}</div>
                    @else
                        <p class="text-muted fst-italic">Chủ phòng chưa cập nhật mô tả.</p>
                    @endif
                </div>

                {{-- Tiện nghi --}}
                @if(is_array($room->amenities) && count($room->amenities) > 0)
                <div class="info-card">
                    <div class="card-section-title"><i class="fa fa-star"></i>Tiện ích & Dịch vụ</div>
                    <div>
                        @foreach($room->amenities as $amenity)
                            <span class="amenity-chip"><i class="fa fa-check"></i>{{ $amenity }}</span>
                        @endforeach
                    </div>
                </div>
                @endif

                {{-- Chi phí --}}
                <div class="info-card">
                    <div class="card-section-title"><i class="fa fa-money"></i>Chi phí hàng tháng</div>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <div class="spec-row">
                                <div class="spec-icon"><i class="fa fa-bolt text-warning"></i></div>
                                <div><div class="spec-label">Giá điện</div><div class="spec-value text-warning">{{ number_format($room->electricity_price) }}đ/kWh</div></div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="spec-row">
                                <div class="spec-icon"><i class="fa fa-tint text-info"></i></div>
                                <div><div class="spec-label">Giá nước</div><div class="spec-value text-info">{{ number_format($room->water_price) }}đ/m³</div></div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="spec-row">
                                <div class="spec-icon"><i class="fa fa-cog text-muted"></i></div>
                                <div><div class="spec-label">Dịch vụ</div><div class="spec-value">{{ $room->service_fee > 0 ? number_format($room->service_fee).'đ/tháng' : 'Miễn phí' }}</div></div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Map --}}
                @if($room->hasLocation())
                <div class="info-card">
                    <div class="d-flex justify-content-between align-items-center card-section-title">
                        <span><i class="fa fa-map-marker"></i>Vị trí phòng</span>
                        <a href="https://www.google.com/maps?q={{ $room->latitude }},{{ $room->longitude }}"
                           target="_blank" class="btn btn-sm btn-outline-primary rounded-pill px-3 fw-semibold" style="font-size:12px;">
                            <i class="fa fa-external-link me-1"></i>Google Maps
                        </a>
                    </div>
                    @if($room->fullAddress())
                        <p class="text-muted small mb-3"><i class="fa fa-map-pin me-1 text-warning"></i>{{ $room->fullAddress() }}</p>
                    @endif
                    <div id="roomMap"></div>
                </div>
                @elseif($room->fullAddress())
                <div class="info-card">
                    <div class="card-section-title"><i class="fa fa-map-marker"></i>Địa chỉ</div>
                    <p class="text-muted mb-0"><i class="fa fa-map-pin me-1 text-warning"></i>{{ $room->fullAddress() }}</p>
                </div>
                @endif

                {{-- ===== ĐÁNH GIÁ & BÌNH LUẬN ===== --}}
                <div class="info-card" id="reviews">
                    @php
                        $reviews = $room->reviews;
                        $avgRating = $room->averageRating();
                        $ratingCount = $reviews->count();
                        $bars = [5=>0, 4=>0, 3=>0, 2=>0, 1=>0];
                        foreach ($reviews as $rv) { $bars[$rv->rating] = ($bars[$rv->rating] ?? 0) + 1; }
                    @endphp

                    <div class="card-section-title"><i class="fa fa-star"></i>Đánh giá & Bình luận</div>

                    {{-- Rating Summary --}}
                    <div class="row g-4 mb-4 align-items-center">
                        <div class="col-auto text-center">
                            <div class="rating-big">{{ $ratingCount > 0 ? $avgRating : '0.0' }}</div>
                            <div class="rating-stars" style="color:#f59e0b;">
                                @for($i=1; $i<=5; $i++)
                                    <i class="fa fa-{{ $i <= round($avgRating) ? 'star' : 'star-o' }}"></i>
                                @endfor
                            </div>
                            <div class="rating-count mt-1">{{ $ratingCount }} đánh giá</div>
                        </div>
                        <div class="col">
                            @for($star=5; $star>=1; $star--)
                            @php $cnt = $bars[$star]; $pct = $ratingCount > 0 ? ($cnt/$ratingCount*100) : 0; @endphp
                            <div class="rating-bar-row">
                                <div class="star-label">{{ $star }} <i class="fa fa-star" style="color:#f59e0b;font-size:10px;"></i></div>
                                <div class="rating-bar"><div class="rating-bar-fill" style="width:{{ $pct }}%"></div></div>
                                <div class="rating-bar-count">{{ $cnt }}</div>
                            </div>
                            @endfor
                        </div>
                    </div>

                    {{-- Alert messages --}}
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show rounded-3" role="alert">
                            <i class="fa fa-check-circle me-2"></i>{{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    {{-- Review form --}}
                    @auth
                        <div class="p-4 mb-4" style="background:#fffbeb;border-radius:14px;border:1px solid #fde68a;">
                            <h6 class="fw-bold mb-3">
                                {{ $userReview ? '✏️ Cập nhật đánh giá của bạn' : '💬 Viết đánh giá' }}
                            </h6>
                            <form action="{{ route('rooms.reviews.store', $room) }}" method="POST">
                                @csrf
                                <div class="mb-3">
                                    <label class="form-label fw-bold small">Chọn số sao</label>
                                    <div class="star-input">
                                        @for($s=5; $s>=1; $s--)
                                            <input type="radio" name="rating" id="star{{ $s }}" value="{{ $s }}"
                                                {{ ($userReview && $userReview->rating == $s) ? 'checked' : '' }}>
                                            <label for="star{{ $s }}" title="{{ $s }} sao">★</label>
                                        @endfor
                                    </div>
                                    @error('rating')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                                </div>
                                <div class="mb-3">
                                    <input type="text" name="title" class="form-control" placeholder="Tiêu đề ngắn (không bắt buộc)"
                                           value="{{ old('title', $userReview?->title) }}" maxlength="100">
                                </div>
                                <div class="mb-3">
                                    <textarea name="comment" class="form-control" rows="3"
                                              placeholder="Chia sẻ trải nghiệm của bạn về phòng này..."
                                              required minlength="10" maxlength="1000">{{ old('comment', $userReview?->comment) }}</textarea>
                                    @error('comment')<div class="text-danger small">{{ $message }}</div>@enderror
                                </div>
                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-warning fw-bold text-dark px-4">
                                        <i class="fa fa-paper-plane me-1"></i>{{ $userReview ? 'Cập nhật' : 'Gửi đánh giá' }}
                                    </button>
                                    @if($userReview)
                                        <form action="{{ route('rooms.reviews.destroy', $userReview) }}" method="POST" class="d-inline">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger"
                                                onclick="return confirm('Xóa đánh giá này?')">
                                                <i class="fa fa-trash"></i> Xóa
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </form>
                        </div>
                    @else
                        <div class="p-3 mb-4 text-center" style="background:#f8fafc;border-radius:12px;border:1px dashed #e2e8f0;">
                            <i class="fa fa-star text-warning me-1"></i>
                            <a href="{{ route('login') }}" class="fw-semibold text-warning">Đăng nhập</a> để viết đánh giá
                        </div>
                    @endauth

                    {{-- Review List --}}
                    @forelse($reviews as $review)
                        <div class="review-card">
                            <div class="d-flex align-items-start gap-3">
                                <div class="reviewer-avatar">{{ mb_substr($review->user->name ?? 'A', 0, 1) }}</div>
                                <div class="flex-grow-1">
                                    <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
                                        <div>
                                            <div class="fw-bold text-dark" style="font-size:14px;">{{ $review->user->name ?? 'Người dùng' }}</div>
                                            <div class="review-stars">
                                                @for($i=1; $i<=5; $i++)
                                                    <i class="fa fa-{{ $i <= $review->rating ? 'star' : 'star-o' }}" style="color:#f59e0b;"></i>
                                                @endfor
                                                <span class="review-date ms-2">{{ $review->created_at->diffForHumans() }}</span>
                                            </div>
                                        </div>
                                        @if(auth()->id() === $review->user_id || auth()->user()?->isAdmin())
                                            <form action="{{ route('rooms.reviews.destroy', $review) }}" method="POST">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger py-0 px-2"
                                                    onclick="return confirm('Xóa bình luận này?')">
                                                    <i class="fa fa-trash" style="font-size:11px;"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                    @if($review->title)
                                        <div class="review-title mt-1">{{ $review->title }}</div>
                                    @endif
                                    <div class="review-body">{{ $review->comment }}</div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-4 text-muted">
                            <i class="fa fa-comment-o fa-2x mb-2 d-block" style="color:#e2e8f0;"></i>
                            Chưa có đánh giá nào. Hãy là người đầu tiên!
                        </div>
                    @endforelse
                </div>

            </div>

            {{-- RIGHT: Booking Sidebar --}}
            <div class="col-lg-4">
                <div class="booking-card">

                    {{-- Rating mini --}}
                    @if($room->reviews->count() > 0)
                    <div class="rating-summary">
                        <div class="rating-big" style="font-size:24px;">{{ $avgRating }}</div>
                        <div>
                            <div class="rating-stars" style="color:#f59e0b;">
                                @for($i=1; $i<=5; $i++)
                                    <i class="fa fa-{{ $i <= round($avgRating) ? 'star' : 'star-o' }}" style="font-size:12px;"></i>
                                @endfor
                            </div>
                            <div class="rating-count">{{ $room->reviews->count() }} đánh giá</div>
                        </div>
                    </div>
                    @endif

                    <h5 class="fw-bold mb-3 text-dark" style="line-height:1.4;">{{ $room->name }}</h5>

                    <div class="price-badge">
                        <div class="label">Giá thuê / tháng</div>
                        <div class="amount">{{ number_format($room->price) }}đ</div>
                    </div>

                    {{-- Specs --}}
                    <div class="mb-4">
                        <div class="spec-row">
                            <div class="spec-icon"><i class="fa fa-expand"></i></div>
                            <div><div class="spec-label">Diện tích</div><div class="spec-value">{{ $room->area ?? '?' }} m²</div></div>
                        </div>
                        <div class="spec-row">
                            <div class="spec-icon"><i class="fa fa-building text-primary"></i></div>
                            <div><div class="spec-label">Tầng</div><div class="spec-value">{{ $room->floor ? 'Tầng '.$room->floor : 'Không rõ' }}</div></div>
                        </div>
                        <div class="spec-row">
                            <div class="spec-icon"><i class="fa fa-bolt text-warning"></i></div>
                            <div><div class="spec-label">Điện</div><div class="spec-value text-warning">{{ number_format($room->electricity_price) }}đ/kWh</div></div>
                        </div>
                        <div class="spec-row">
                            <div class="spec-icon"><i class="fa fa-tint text-info"></i></div>
                            <div><div class="spec-label">Nước</div><div class="spec-value text-info">{{ number_format($room->water_price) }}đ/m³</div></div>
                        </div>
                        @if($room->fullAddress())
                        <div class="spec-row">
                            <div class="spec-icon"><i class="fa fa-map-marker text-danger"></i></div>
                            <div><div class="spec-label">Địa chỉ</div><div class="spec-value" style="font-size:13px;">{{ $room->fullAddress() }}</div></div>
                        </div>
                        @endif
                    </div>

                    {{-- Book button --}}
                    @if($room->isAvailable())
                        @auth
                            <a href="{{ route('bookings.create', $room) }}" class="btn-book" style="background:linear-gradient(135deg,#f97316,#ea580c);margin-bottom:10px;display:block;">
                                💳 ĐẶT PHÒNG & ĐẶT CỌC NGAY
                            </a>
                            @if(!$hasActiveRequest)
                            <a href="{{ route('rent-requests.create', $room) }}" class="btn-book" style="background:linear-gradient(135deg,#1e293b,#334155);font-size:13px;padding:12px;">
                                📋 Gửi yêu cầu xem phòng
                            </a>
                            @else
                            <div class="alert alert-warning rounded-3 text-center py-2 mt-2" style="font-size:13px;">
                                <i class="fa fa-clock me-1"></i>Đã gửi yêu cầu xem phòng, đang chờ duyệt
                            </div>
                            @endif
                        @else
                            <a href="{{ route('login') }}" class="btn-book" style="background:linear-gradient(135deg,#1e293b,#334155)">
                                🔐 ĐĂNG NHẬP ĐỂ ĐẶT PHÒNG
                            </a>
                        @endauth
                    @elseif($room->status === 'reserved')
                        <button class="btn-book" disabled style="background:linear-gradient(135deg,#f59e0b,#d97706);">⏳ Đang được giữ chỗ</button>
                    @else
                        <button class="btn-book" disabled>❌ Đã có người thuê</button>
                    @endif

                    {{-- Landlord info --}}
                    <div class="landlord-card mt-3">
                        <div class="landlord-avatar">{{ mb_substr($room->landlord->name ?? 'A', 0, 1) }}</div>
                        <div class="flex-grow-1">
                            <div style="font-size:10px;color:#94a3b8;font-weight:700;text-transform:uppercase;">Chủ phòng</div>
                            <div class="fw-bold text-dark">{{ $room->landlord->name ?? 'Admin' }}</div>
                        </div>
                        <a href="#reviews" class="btn btn-sm btn-outline-warning rounded-pill" style="font-size:11px;">
                            {{ $room->reviews->count() }} đánh giá
                        </a>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
function changeImage(el, src) {
    document.getElementById('mainImg').src = src;
    document.querySelectorAll('.thumb-item').forEach(t => t.classList.remove('active'));
    el.classList.add('active');
}

@if($room->hasLocation())
(function() {
    const lat = {{ $room->latitude }};
    const lng = {{ $room->longitude }};
    const roomName = @json($room->name);
    const address = @json($room->fullAddress());

    const map = L.map('roomMap').setView([lat, lng], 16);
    L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', {
        attribution: '&copy; OpenStreetMap &copy; CARTO',
        subdomains: 'abcd', maxZoom: 20
    }).addTo(map);

    const pin = L.divIcon({
        html: '<div style="background:#e74c3c;width:20px;height:20px;border-radius:50% 50% 50% 0;transform:rotate(-45deg);border:3px solid #fff;box-shadow:0 3px 8px rgba(0,0,0,.4);"></div>',
        iconSize: [20, 20], iconAnchor: [10, 20], className: ''
    });
    L.marker([lat, lng], { icon: pin })
        .addTo(map)
        .bindPopup(`<b>${roomName}</b><br><small>${address ?? ''}</small>`)
        .openPopup();
})();
@endif
</script>
@endsection
