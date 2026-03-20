@extends('layouts.user')
@section('title', 'Trang Chủ - ' . \App\Models\Setting::get('site_name','Nhà Trọ'))

@section('content')
<section class="banner_main">
    <div id="myCarousel" class="carousel slide banner" data-ride="carousel">
        <ol class="carousel-indicators">
            <li data-target="#myCarousel" data-slide-to="0" class="active"></li>
            <li data-target="#myCarousel" data-slide-to="1"></li>
            <li data-target="#myCarousel" data-slide-to="2"></li>
        </ol>
        <div class="carousel-inner">
            <div class="carousel-item active">
                <img class="first-slide" src="/user/images/banner1.jpg" alt="Banner 1">
            </div>
            <div class="carousel-item">
                <img class="second-slide" src="/user/images/banner2.jpg" alt="Banner 2">
            </div>
            <div class="carousel-item">
                <img class="third-slide" src="/user/images/banner3.jpg" alt="Banner 3">
            </div>
        </div>
        <a class="carousel-control-prev" href="#myCarousel" role="button" data-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
        </a>
        <a class="carousel-control-next" href="#myCarousel" role="button" data-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
        </a>
    </div>
    <div class="booking_ocline">
        <div class="container">
            <div class="row">
                <div class="col-md-5">
                    <div class="book_room">
                        <h1>Tìm Phòng Trọ</h1>
                        <form class="book_now" action="{{ route('rooms.index') }}" method="GET" id="searchRoomForm">
                            <div class="row">
                                <div class="col-md-12">
                                    <span>Tìm kiếm</span>
                                    <input class="online_book" placeholder="Tên phòng..." type="text" name="search">
                                </div>
                                <div class="col-md-12">
                                    <span>Tỉnh / Thành phố</span>
                                    <select name="province" id="province" class="online_book" style="background: rgba(255,255,255,0.1); color: #fff; border: 1px solid rgba(255,255,255,0.2); padding: 10px; width: 100%; margin-bottom: 20px;">
                                        <option value="" style="color:#000;">Chọn Tỉnh/TP</option>
                                    </select>
                                </div>
                                <div class="col-md-12">
                                    <span>Quận / Huyện</span>
                                    <select name="district" id="district" class="online_book" style="background: rgba(255,255,255,0.1); color: #fff; border: 1px solid rgba(255,255,255,0.2); padding: 10px; width: 100%; margin-bottom: 20px;" disabled>
                                        <option value="" style="color:#000;">Chọn Quận/Huyện</option>
                                    </select>
                                </div>
                                <div class="col-md-12">
                                    <span>Phường / Xã</span>
                                    <select name="ward" id="ward" class="online_book" style="background: rgba(255,255,255,0.1); color: #fff; border: 1px solid rgba(255,255,255,0.2); padding: 10px; width: 100%; margin-bottom: 20px;" disabled>
                                        <option value="" style="color:#000;">Chọn Phường/Xã</option>
                                    </select>
                                </div>
                                <div class="col-md-12">
                                    <span>Giá tối đa (VNĐ)</span>
                                    <input class="online_book" placeholder="VD: 3000000" type="number" name="max_price">
                                </div>
                                <div class="col-md-12">
                                    <button class="book_btn" type="submit">Tìm Phòng</button>
                                </div>
                            </div>
                        </form>
@section('scripts')
<script>
    $(document).ready(function() {
        const provinceSelect = $('#province');
        const districtSelect = $('#district');
        const wardSelect = $('#ward');

        // Load provinces
        fetch('/api/regions/provinces')
            .then(response => response.json())
            .then(data => {
                data.forEach(p => {
                    provinceSelect.append(`<option value="${p.name}" data-code="${p.code}" style="color:#000;">${p.name}</option>`);
                });
            });

        // Load districts when province changes
        provinceSelect.on('change', function() {
            const code = $(this).find(':selected').attr('data-code');
            districtSelect.empty().append('<option value="" style="color:#000;">Chọn Quận/Huyện</option>').prop('disabled', true);
            wardSelect.empty().append('<option value="" style="color:#000;">Chọn Phường/Xã</option>').prop('disabled', true);

            if (code) {
                fetch(`/api/regions/districts/${code}`)
                    .then(response => response.json())
                    .then(data => {
                        data.forEach(d => {
                            districtSelect.append(`<option value="${d.name}" data-code="${d.code}" style="color:#000;">${d.name}</option>`);
                        });
                        districtSelect.prop('disabled', false);
                    });
            }
        });

        // Load wards when district changes
        districtSelect.on('change', function() {
            const code = $(this).find(':selected').attr('data-code');
            wardSelect.empty().append('<option value="" style="color:#000;">Chọn Phường/Xã</option>').prop('disabled', true);

            if (code) {
                fetch(`/api/regions/wards/${code}`)
                    .then(response => response.json())
                    .then(data => {
                        data.forEach(w => {
                            wardSelect.append(`<option value="${w.name}" style="color:#000;">${w.name}</option>`);
                        });
                        wardSelect.prop('disabled', false);
                    });
            }
        });
    });
</script>
@endsection
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<div class="about">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-5">
                <div class="titlepage">
                    <h2>Về Chúng Tôi</h2>
                    <p>{{ \App\Models\Setting::get('site_name','Nhà Trọ') }} cung cấp các phòng trọ chất lượng, an ninh, thoáng mát với mức giá hợp lý. Hệ thống quản lý minh bạch, thanh toán tiện lợi qua QR và MoMo.</p>
                    <a class="read_more" href="{{ route('rooms.index') }}">Xem phòng</a>
                </div>
            </div>
            <div class="col-md-7">
                <div class="about_img">
                    <figure><img src="/user/images/about.png" alt="About"/></figure>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="featured-rooms-section">
    <style>
        .featured-rooms-section {
            padding: 70px 0 80px;
            background: linear-gradient(180deg, #fff 0%, #f8fafc 100%);
        }
        .section-badge {
            display: inline-block;
            background: linear-gradient(135deg,#fef3c7,#fde68a);
            color: #92400e;
            font-size: 11px; font-weight: 800;
            letter-spacing: .8px; text-transform: uppercase;
            padding: 5px 16px; border-radius: 20px;
            margin-bottom: 12px;
        }
        .section-heading {
            font-size: 32px; font-weight: 800; color: #1e293b;
            margin-bottom: 8px;
        }
        .section-sub { color: #64748b; font-size: 15px; margin-bottom: 42px; }

        /* Room Card */
        .room-card {
            background: #fff;
            border-radius: 18px;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0,0,0,.07);
            transition: transform .25s, box-shadow .25s;
            height: 100%;
            display: flex; flex-direction: column;
        }
        .room-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 12px 40px rgba(0,0,0,.13);
        }
        .room-card-img {
            position: relative; overflow: hidden;
            height: 200px;
        }
        .room-card-img img {
            width: 100%; height: 100%;
            object-fit: cover;
            transition: transform .4s;
        }
        .room-card:hover .room-card-img img { transform: scale(1.06); }
        .room-card-badge {
            position: absolute; top: 12px; left: 12px;
            background: rgba(255,255,255,.92);
            border-radius: 20px;
            font-size: 10px; font-weight: 700;
            padding: 3px 10px;
        }
        .room-card-badge.available { color: #16a34a; }
        .room-card-badge.rented    { color: #dc2626; }
        .room-card-price {
            position: absolute; bottom: 12px; right: 12px;
            background: linear-gradient(135deg,#f59e0b,#d97706);
            color: #fff; font-weight: 800; font-size: 12px;
            padding: 5px 12px; border-radius: 20px;
            box-shadow: 0 2px 8px rgba(0,0,0,.2);
        }
        .room-card-body {
            padding: 18px 20px 20px;
            flex: 1; display: flex; flex-direction: column;
        }
        .room-card-title {
            font-size: 15px; font-weight: 700; color: #1e293b;
            line-height: 1.4; margin-bottom: 8px;
            display: -webkit-box; -webkit-line-clamp: 2;
            -webkit-box-orient: vertical; overflow: hidden;
        }
        .room-card-title a { color: inherit; text-decoration: none; }
        .room-card-title a:hover { color: #f59e0b; }
        .room-card-addr {
            font-size: 11px; color: #94a3b8;
            margin-bottom: 10px;
            display: -webkit-box; -webkit-line-clamp: 1;
            -webkit-box-orient: vertical; overflow: hidden;
        }
        .room-card-addr i { color: #ef4444; margin-right: 3px; }
        .room-card-tags { margin-bottom: 12px; min-height: 22px; }
        .room-card-tags .tag {
            display: inline-block;
            background: #f1f5f9; color: #475569;
            font-size: 10px; font-weight: 600;
            padding: 3px 9px; border-radius: 6px;
            margin: 0 3px 3px 0;
        }
        .room-card-footer {
            display: flex; align-items: center;
            justify-content: space-between;
            margin-top: auto; padding-top: 12px;
            border-top: 1.5px solid #f1f5f9;
        }
        .room-card-rating { font-size: 11px; color: #94a3b8; }
        .room-card-rating .stars { color: #f59e0b; letter-spacing: 1px; }
        .btn-room-detail {
            background: linear-gradient(135deg,#1e293b,#334155);
            color: #fff; border: none;
            padding: 7px 18px; border-radius: 10px;
            font-size: 12px; font-weight: 700;
            text-decoration: none;
            transition: all .2s;
        }
        .btn-room-detail:hover {
            background: linear-gradient(135deg,#f59e0b,#d97706);
            color: #fff; transform: translateY(-1px);
        }
        /* View All Button */
        .btn-view-all {
            display: inline-flex; align-items: center; gap: 8px;
            background: linear-gradient(135deg,#f59e0b,#d97706);
            color: #fff; border: none;
            padding: 13px 36px; border-radius: 50px;
            font-size: 15px; font-weight: 700;
            text-decoration: none;
            box-shadow: 0 4px 18px rgba(245,158,11,.35);
            transition: all .25s;
        }
        .btn-view-all:hover {
            color: #fff; transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(245,158,11,.4);
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
                                    <img src="{{ asset('storage/'.$room->images->first()->image_path) }}"
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
                                        @for($s=1;$s<=5;$s++){{ $s<=$avg ? '★' : '☆' }}@endfor
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

