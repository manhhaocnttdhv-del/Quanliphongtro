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
        fetch('https://provinces.open-api.vn/api/p/')
            .then(response => response.json())
            .then(data => {
                data.forEach(p => {
                    provinceSelect.append(`<option value="${p.name}" data-code="${p.code}" style="color:#000;">${p.name}</option>`);
                });
            });

        // Load districts when province changes
        provinceSelect.on('change', function() {
            const code = $(this).find(':selected').data('code');
            districtSelect.empty().append('<option value="" style="color:#000;">Chọn Quận/Huyện</option>').prop('disabled', true);
            wardSelect.empty().append('<option value="" style="color:#000;">Chọn Phường/Xã</option>').prop('disabled', true);
            
            if (code) {
                fetch(`https://provinces.open-api.vn/api/p/${code}?depth=2`)
                    .then(response => response.json())
                    .then(data => {
                        data.districts.forEach(d => {
                            districtSelect.append(`<option value="${d.name}" data-code="${d.code}" style="color:#000;">${d.name}</option>`);
                        });
                        districtSelect.prop('disabled', false);
                    });
            }
        });

        // Load wards when district changes
        districtSelect.on('change', function() {
            const code = $(this).find(':selected').data('code');
            wardSelect.empty().append('<option value="" style="color:#000;">Chọn Phường/Xã</option>').prop('disabled', true);
            
            if (code) {
                fetch(`https://provinces.open-api.vn/api/d/${code}?depth=2`)
                    .then(response => response.json())
                    .then(data => {
                        data.wards.forEach(w => {
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

<div class="our_room">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="titlepage">
                    <h2>Phòng Trọ Nổi Bật</h2>
                    <p>Khám phá các phòng còn trống hiện tại</p>
                </div>
            </div>
        </div>
        <div class="row">
            @forelse($featuredRooms as $room)
                <div class="col-md-4 col-sm-6 mb-4">
                    <div id="serv_hover" class="room">
                        <div class="room_img">
                            <a href="{{ route('rooms.show', $room) }}">
                                @if($room->images->first())
                                    <figure><img src="{{ asset('storage/'.$room->images->first()->image_path) }}" alt="{{ $room->name }}" style="height:200px;object-fit:cover;width:100%;"></figure>
                                @else
                                    <figure><img src="/user/images/room1.jpg" alt="{{ $room->name }}" style="height:200px;object-fit:cover;width:100%;"></figure>
                                @endif
                            </a>
                        </div>
                        <div class="bed_room">
                            <h3><a href="{{ route('rooms.show', $room) }}">{{ $room->name }}</a></h3>
                            <div class="mb-2">
                                @if($room->amenities)
                                    @foreach(array_slice($room->amenities, 0, 3) as $amenity)
                                        <span class="badge bg-light text-dark border me-1" style="font-size:10px;">{{ $amenity }}</span>
                                    @endforeach
                                @endif
                            </div>
                            <p>{{ Str::limit($room->description, 60) }}</p>
                            <div class="d-flex justify-content-between align-items-center mt-2">
                                <strong style="color:#f9a825;">{{ number_format($room->price) }} VNĐ/tháng</strong>
                                <a href="{{ route('rooms.show', $room) }}" class="btn btn-sm" style="background:#f9a825;color:#fff;border-radius:20px;padding:4px 14px;">Xem chi tiết</a>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12 text-center py-5">
                    <p>Hiện chưa có phòng trống. Vui lòng quay lại sau.</p>
                </div>
            @endforelse
        </div>
        <div class="row">
            <div class="col-12 text-center mt-3">
                <a href="{{ route('rooms.index') }}" class="read_more">Xem tất cả phòng</a>
            </div>
        </div>
    </div>
</div>
@endsection
