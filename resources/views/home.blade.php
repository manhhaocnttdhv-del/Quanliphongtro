@extends('layouts.user')
@section('title', 'Trang Chủ - ' . \App\Models\Setting::get('site_name', 'Nhà Trọ'))

@section('content')
<style>
/* ══════════════════════════════════
   HERO / SLIDER — Premium Redesign
══════════════════════════════════ */
.hero-wrapper {
    position: relative;
    width: 100%;
    height: 90vh;
    min-height: 560px;
    max-height: 780px;
    overflow: hidden;
}
.hero-wrapper .carousel,
.hero-wrapper .carousel-inner,
.hero-wrapper .carousel-item { height: 100%; }
.hero-wrapper .carousel-item img {
    width: 100%; height: 100%;
    object-fit: cover; object-position: center;
    animation: heroZoom 8s ease forwards;
}
@keyframes heroZoom {
    from { transform: scale(1.06); }
    to   { transform: scale(1.00); }
}
/* Multi-layer dark overlay */
.hero-wrapper .carousel-item::before {
    content: '';
    position: absolute; inset: 0; z-index: 1;
    background: linear-gradient(
        180deg,
        rgba(0,0,0,0.10) 0%,
        rgba(0,0,0,0.28) 45%,
        rgba(0,0,0,0.75) 100%
    );
}
/* ── Hero text (center) ── */
.hero-content {
    position: absolute; inset: 0; z-index: 5;
    display: flex; flex-direction: column;
    align-items: center; justify-content: center;
    text-align: center;
    padding: 0 20px 160px;
}
.hero-tag {
    display: inline-block;
    background: rgba(230,57,70,0.88);
    color: #fff; font-size: 11px; font-weight: 700;
    letter-spacing: 2.5px; text-transform: uppercase;
    padding: 6px 18px; border-radius: 30px; margin-bottom: 20px;
    animation: fadeDown .6s ease both;
    box-shadow: 0 4px 14px rgba(230,57,70,0.4);
}
.hero-title {
    color: #fff;
    font-size: clamp(34px, 5.5vw, 68px);
    font-weight: 900; line-height: 1.1;
    text-shadow: 0 6px 32px rgba(0,0,0,0.55);
    margin-bottom: 16px;
    animation: fadeDown .7s .1s ease both;
    max-width: 840px;
}
.hero-title span { color: #f4a223; }
.hero-desc {
    color: rgba(255,255,255,0.82);
    font-size: clamp(14px, 1.8vw, 18px);
    max-width: 520px; line-height: 1.65;
    animation: fadeDown .8s .2s ease both;
}
@keyframes fadeDown {
    from { opacity: 0; transform: translateY(-20px); }
    to   { opacity: 1; transform: translateY(0); }
}
/* ── Search bar (bottom) ── */
.hero-search-bar {
    position: absolute; bottom: 0; left: 0; right: 0;
    z-index: 10; padding: 0 24px 32px;
    display: flex; justify-content: center;
    animation: fadeUp .9s .25s ease both;
}
@keyframes fadeUp {
    from { opacity: 0; transform: translateY(28px); }
    to   { opacity: 1; transform: translateY(0); }
}
.search-glass {
    background: rgba(10,10,20,0.55);
    backdrop-filter: blur(20px) saturate(130%);
    -webkit-backdrop-filter: blur(20px) saturate(130%);
    border: 1px solid rgba(255,255,255,0.15);
    border-radius: 22px;
    padding: 20px 24px 20px;
    width: 100%; max-width: 1000px;
    box-shadow: 0 10px 50px rgba(0,0,0,0.35);
}
.search-glass-label {
    color: rgba(255,255,255,0.5);
    font-size: 10px; font-weight: 700;
    letter-spacing: 2px; text-transform: uppercase;
    margin-bottom: 13px;
}
.search-row {
    display: flex; gap: 10px;
    align-items: flex-end; flex-wrap: wrap;
}
.sf { flex: 1; min-width: 140px; }
.sf label {
    display: block;
    color: rgba(255,255,255,0.5);
    font-size: 10px; font-weight: 600;
    letter-spacing: .5px; text-transform: uppercase;
    margin-bottom: 5px;
}
.sf input, .sf select {
    background: rgba(255,255,255,0.10);
    color: #fff;
    border: 1px solid rgba(255,255,255,0.16);
    border-radius: 11px; padding: 9px 13px;
    font-size: 13px; width: 100%; outline: none;
    transition: border-color .2s, background .2s;
    appearance: none; -webkit-appearance: none;
    height: 40px;
}
.sf input::placeholder { color: rgba(255,255,255,0.38); }
.sf input:focus, .sf select:focus {
    border-color: rgba(255,255,255,0.45);
    background: rgba(255,255,255,0.16);
}
.sf select option { color: #111; background: #fff; }
.sf-btn { flex-shrink: 0; }
.hero-search-btn {
    background: linear-gradient(135deg, #e63946, #c1121f);
    color: #fff; border: none; border-radius: 12px;
    height: 40px; padding: 0 26px;
    font-size: 14px; font-weight: 700; cursor: pointer;
    white-space: nowrap;
    box-shadow: 0 4px 20px rgba(230,57,70,0.45);
    transition: transform .2s, box-shadow .2s;
    display: inline-flex; align-items: center; gap: 8px;
}
.hero-search-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 26px rgba(230,57,70,0.55);
}
/* Indicators */
.hero-wrapper .carousel-indicators {
    z-index: 6; bottom: 130px; margin-bottom: 0;
    gap: 5px;
}
.hero-wrapper .carousel-indicators li {
    width: 8px; height: 8px; border-radius: 50%;
    background: rgba(255,255,255,0.4); border: none;
    margin: 0; transition: all .35s;
}
.hero-wrapper .carousel-indicators li.active {
    background: #f4a223;
    width: 26px; border-radius: 4px;
}
/* Controls */
.hero-wrapper .carousel-control-prev,
.hero-wrapper .carousel-control-next {
    width: 42px; height: 42px;
    background: rgba(255,255,255,0.10);
    backdrop-filter: blur(8px);
    border-radius: 50%;
    top: 50%; transform: translateY(-60%);
    margin: 0 18px;
    opacity: 0; transition: opacity .3s;
}
.hero-wrapper:hover .carousel-control-prev,
.hero-wrapper:hover .carousel-control-next { opacity: 1; }
/* Mobile */
@media(max-width: 768px) {
    .hero-wrapper { height: 100svh; }
    .hero-content  { padding-bottom: 260px; }
    .hero-title    { font-size: 28px; }
    .hero-search-bar { padding: 0 12px 16px; }
    .search-row    { flex-direction: column; gap: 8px; }
    .sf            { min-width: 100%; }
    .sf-btn        { width: 100%; }
    .hero-search-btn { width: 100%; justify-content: center; }
    .hero-wrapper .carousel-indicators { bottom: 240px; }
}
</style>

<section class="hero-wrapper">

    {{-- ── Carousel ── --}}
    <div id="heroCarousel" class="carousel slide" data-ride="carousel" data-interval="5500">
        <ol class="carousel-indicators">
            @php $slides = $sliders->count() > 0 ? $sliders : collect([1,2,3]); @endphp
            @foreach($slides as $i => $slide)
                <li data-target="#heroCarousel" data-slide-to="{{ $i }}"
                    class="{{ $i === 0 ? 'active' : '' }}"></li>
            @endforeach
        </ol>
        <div class="carousel-inner">
            @if($sliders->count() > 0)
                @foreach($sliders as $i => $slider)
                    <div class="carousel-item {{ $i === 0 ? 'active' : '' }}">
                        <img src="{{ asset('storage/'.$slider->image_path) }}"
                             alt="{{ $slider->title ?? 'Slide '.($i+1) }}">
                    </div>
                @endforeach
            @else
                <div class="carousel-item active">
                    <img src="/user/images/banner1.jpg" alt="Phòng trọ chất lượng">
                </div>
                <div class="carousel-item">
                    <img src="/user/images/banner2.jpg" alt="Không gian sống tiện nghi">
                </div>
                <div class="carousel-item">
                    <img src="/user/images/banner3.jpg" alt="An ninh & thoáng mát">
                </div>
            @endif
        </div>
        <a class="carousel-control-prev" href="#heroCarousel" role="button" data-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
        </a>
        <a class="carousel-control-next" href="#heroCarousel" role="button" data-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
        </a>
    </div>

    {{-- ── Hero Text ── --}}
    <div class="hero-content">
        <div class="hero-tag">🏠 Hệ thống phòng trọ uy tín</div>
        <h1 class="hero-title">
            {{ \App\Models\Setting::get('hero_title', 'Tìm Phòng Trọ') }}
            <span>Chất Lượng</span>
        </h1>
        <p class="hero-desc">
            {{ \App\Models\Setting::get('hero_subtitle', 'Khám phá hàng trăm phòng trọ tiện nghi, an ninh và giá tốt nhất khu vực') }}
        </p>
    </div>

    {{-- ── Search Bar ── --}}
    <div class="hero-search-bar">
        <div class="search-glass">
            <div class="search-glass-label">🔍 Tìm kiếm phòng trọ</div>
            <form action="{{ route('rooms.index') }}" method="GET" id="searchRoomForm">
                <div class="search-row">
                    <div class="sf" style="flex:2;min-width:180px;">
                        <label>Tên phòng</label>
                        <input type="text" name="search" placeholder="Nhập tên phòng...">
                    </div>
                    <div class="sf">
                        <label>Tỉnh / TP</label>
                        <select name="province" id="province">
                            <option value="">Chọn Tỉnh/TP</option>
                        </select>
                    </div>
                    <div class="sf">
                        <label>Quận / Huyện</label>
                        <select name="district" id="district" disabled>
                            <option value="">Chọn Quận/Huyện</option>
                        </select>
                    </div>
                    <div class="sf">
                        <label>Phường / Xã</label>
                        <select name="ward" id="ward" disabled>
                            <option value="">Chọn Phường/Xã</option>
                        </select>
                    </div>
                    <div class="sf" style="min-width:130px;">
                        <label>Giá tối đa (VNĐ)</label>
                        <input type="number" name="max_price" placeholder="3,000,000">
                    </div>
                    <div class="sf-btn">
                        <button type="submit" class="hero-search-btn">
                            <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                <circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/>
                            </svg>
                            Tìm Phòng
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

</section>


@section('scripts')
<script>
$(document).ready(function () {
    const provinceSelect = $('#province');
    const districtSelect = $('#district');
    const wardSelect     = $('#ward');

    // Load provinces
    fetch('/api/regions/provinces')
        .then(r => r.json())
        .then(data => {
            data.forEach(p => {
                provinceSelect.append(`<option value="${p.name}" data-code="${p.code}" style="color:#000;">${p.name}</option>`);
            });
        });

    // Province → Districts
    provinceSelect.on('change', function () {
        const code = $(this).find(':selected').attr('data-code');
        districtSelect.empty().append('<option value="">Chọn Quận/Huyện</option>').prop('disabled', true);
        wardSelect.empty().append('<option value="">Chọn Phường/Xã</option>').prop('disabled', true);
        if (code) {
            fetch(`/api/regions/districts/${code}`)
                .then(r => r.json())
                .then(data => {
                    data.forEach(d => districtSelect.append(`<option value="${d.name}" data-code="${d.code}" style="color:#000;">${d.name}</option>`));
                    districtSelect.prop('disabled', false);
                });
        }
    });

    // District → Wards
    districtSelect.on('change', function () {
        const code = $(this).find(':selected').attr('data-code');
        wardSelect.empty().append('<option value="">Chọn Phường/Xã</option>').prop('disabled', true);
        if (code) {
            fetch(`/api/regions/wards/${code}`)
                .then(r => r.json())
                .then(data => {
                    data.forEach(w => wardSelect.append(`<option value="${w.name}" style="color:#000;">${w.name}</option>`));
                    wardSelect.prop('disabled', false);
                });
        }
    });
});
</script>
@endsection

    <div class="featured-rooms-section">
        <style>
            .featured-rooms-section {
                padding: 70px 0 80px;
                background: linear-gradient(180deg, #fff 0%, #f8fafc 100%);
            }

            .section-badge {
                display: inline-block;
                background: linear-gradient(135deg, #fef3c7, #fde68a);
                color: #92400e;
                font-size: 11px;
                font-weight: 800;
                letter-spacing: .8px;
                text-transform: uppercase;
                padding: 5px 16px;
                border-radius: 20px;
                margin-bottom: 12px;
            }

            .section-heading {
                font-size: 32px;
                font-weight: 800;
                color: #1e293b;
                margin-bottom: 8px;
            }

            .section-sub {
                color: #64748b;
                font-size: 15px;
                margin-bottom: 42px;
            }

            /* Room Card */
            .room-card {
                background: #fff;
                border-radius: 18px;
                overflow: hidden;
                box-shadow: 0 4px 20px rgba(0, 0, 0, .07);
                transition: transform .25s, box-shadow .25s;
                height: 100%;
                display: flex;
                flex-direction: column;
            }

            .room-card:hover {
                transform: translateY(-6px);
                box-shadow: 0 12px 40px rgba(0, 0, 0, .13);
            }

            .room-card-img {
                position: relative;
                overflow: hidden;
                height: 200px;
            }

            .room-card-img img {
                width: 100%;
                height: 100%;
                object-fit: cover;
                transition: transform .4s;
            }

            .room-card:hover .room-card-img img {
                transform: scale(1.06);
            }

            .room-card-badge {
                position: absolute;
                top: 12px;
                left: 12px;
                background: rgba(255, 255, 255, .92);
                border-radius: 20px;
                font-size: 10px;
                font-weight: 700;
                padding: 3px 10px;
            }

            .room-card-badge.available {
                color: #16a34a;
            }

            .room-card-badge.rented {
                color: #dc2626;
            }

            .room-card-price {
                position: absolute;
                bottom: 12px;
                right: 12px;
                background: linear-gradient(135deg, #f59e0b, #d97706);
                color: #fff;
                font-weight: 800;
                font-size: 12px;
                padding: 5px 12px;
                border-radius: 20px;
                box-shadow: 0 2px 8px rgba(0, 0, 0, .2);
            }

            .room-card-body {
                padding: 18px 20px 20px;
                flex: 1;
                display: flex;
                flex-direction: column;
            }

            .room-card-title {
                font-size: 15px;
                font-weight: 700;
                color: #1e293b;
                line-height: 1.4;
                margin-bottom: 8px;
                display: -webkit-box;
                -webkit-line-clamp: 2;
                -webkit-box-orient: vertical;
                overflow: hidden;
            }

            .room-card-title a {
                color: inherit;
                text-decoration: none;
            }

            .room-card-title a:hover {
                color: #f59e0b;
            }

            .room-card-addr {
                font-size: 11px;
                color: #94a3b8;
                margin-bottom: 10px;
                display: -webkit-box;
                -webkit-line-clamp: 1;
                -webkit-box-orient: vertical;
                overflow: hidden;
            }

            .room-card-addr i {
                color: #ef4444;
                margin-right: 3px;
            }

            .room-card-tags {
                margin-bottom: 12px;
                min-height: 22px;
            }

            .room-card-tags .tag {
                display: inline-block;
                background: #f1f5f9;
                color: #475569;
                font-size: 10px;
                font-weight: 600;
                padding: 3px 9px;
                border-radius: 6px;
                margin: 0 3px 3px 0;
            }

            .room-card-footer {
                display: flex;
                align-items: center;
                justify-content: space-between;
                margin-top: auto;
                padding-top: 12px;
                border-top: 1.5px solid #f1f5f9;
            }

            .room-card-rating {
                font-size: 11px;
                color: #94a3b8;
            }

            .room-card-rating .stars {
                color: #f59e0b;
                letter-spacing: 1px;
            }

            .btn-room-detail {
                background: linear-gradient(135deg, #1e293b, #334155);
                color: #fff;
                border: none;
                padding: 7px 18px;
                border-radius: 10px;
                font-size: 12px;
                font-weight: 700;
                text-decoration: none;
                transition: all .2s;
            }

            .btn-room-detail:hover {
                background: linear-gradient(135deg, #f59e0b, #d97706);
                color: #fff;
                transform: translateY(-1px);
            }

            /* View All Button */
            .btn-view-all {
                display: inline-flex;
                align-items: center;
                gap: 8px;
                background: linear-gradient(135deg, #f59e0b, #d97706);
                color: #fff;
                border: none;
                padding: 13px 36px;
                border-radius: 50px;
                font-size: 15px;
                font-weight: 700;
                text-decoration: none;
                box-shadow: 0 4px 18px rgba(245, 158, 11, .35);
                transition: all .25s;
            }

            .btn-view-all:hover {
                color: #fff;
                transform: translateY(-2px);
                box-shadow: 0 8px 24px rgba(245, 158, 11, .4);
            }
        </style>

        <div class="container">
            {{-- Section Header --}}
            <div class="text-center mb-2">
                <div class="section-badge">✨ Nổi bật</div>
            </div>
            <div class="text-center">
                <h2 class="section-heading">Phòng Trọ Nổi Bật</h2>
                <p class="section-sub">Khám phá các phòng còn trống chất lượng tốt nhất</p>
            </div>

            {{-- Cards --}}
            <div class="row g-4 justify-content-center">
                @forelse($featuredRooms as $room)
                    <div class="col-lg-4 col-md-6">
                        <div class="room-card">
                            {{-- Image --}}
                            <div class="room-card-img">
                                <a href="{{ route('rooms.show', $room) }}">
                                    @if($room->images->first())
                                        <img src="{{ asset('storage/' . $room->images->first()->image_path) }}"
                                            alt="{{ $room->name }}">
                                    @else
                                        <img src="/user/images/room1.jpg" alt="{{ $room->name }}">
                                    @endif
                                </a>
                                <div class="room-card-badge {{ $room->status === 'available' ? 'available' : 'rented' }}">
                                    {{ $room->status === 'available' ? '● Còn trống' : '● Đã thuê' }}
                                </div>
                                <div class="room-card-price">
                                    {{ number_format($room->price) }}đ/tháng
                                </div>
                            </div>

                            {{-- Body --}}
                            <div class="room-card-body">
                                <div class="room-card-title">
                                    <a href="{{ route('rooms.show', $room) }}">{{ $room->name }}</a>
                                </div>

                                @if($room->fullAddress())
                                    <div class="room-card-addr">
                                        <i class="fa fa-map-marker"></i>{{ $room->fullAddress() }}
                                    </div>
                                @endif

                                <div class="room-card-tags">
                                    @if($room->amenities)
                                        @foreach(array_slice($room->amenities, 0, 3) as $amenity)
                                            <span class="tag">{{ $amenity }}</span>
                                        @endforeach
                                    @endif
                                    @if($room->area)
                                        <span class="tag"><i class="fa fa-expand"></i> {{ $room->area }}m²</span>
                                    @endif
                                </div>

                                <div class="room-card-footer">
                                    <div class="room-card-rating">
                                        @php $avg = round($room->reviews_avg_rating ?? 0); @endphp
                                        <span class="stars">
                                            @for($s = 1; $s <= 5; $s++){{ $s <= $avg ? '★' : '☆' }}@endfor
                                        </span>
                                        ({{ $room->reviews_count ?? 0 }})
                                    </div>
                                    <a href="{{ route('rooms.show', $room) }}" class="btn-room-detail">
                                        Xem chi tiết →
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12 text-center py-5 text-muted">
                        <i class="fa fa-home fa-3x mb-3 d-block" style="color:#cbd5e1;"></i>
                        Hiện chưa có phòng trống. Vui lòng quay lại sau.
                    </div>
                @endforelse
            </div>

            {{-- View All --}}
            <div class="text-center mt-5">
                <a href="{{ route('rooms.index') }}" class="btn-view-all">
                    <i class="fa fa-th-large"></i> Xem tất cả phòng
                </a>
            </div>
        </div>
    </div>
@endsection