@extends('layouts.user')
@section('title', 'Gửi Yêu Cầu Thuê - ' . $room->name)

@section('content')
<div class="contact" style="padding:60px 0;">
    <div class="container">
        <div class="titlepage text-center mb-5">
            <h2>Gửi Yêu Cầu Thuê Phòng</h2>
            <p>Điền thông tin bên dưới để gửi yêu cầu thuê phòng <strong>{{ $room->name }}</strong></p>
        </div>
        <div class="row justify-content-center">
            <div class="col-md-7">
                {{-- Room summary --}}
                <div class="card mb-4 p-3" style="border-radius:12px;background:#fff8e1;border:1px solid #f9a825;">
                    <div class="row align-items-center g-3">
                        <div class="col-3">
                            @if($room->images->first())
                                <img src="{{ asset('storage/'.$room->images->first()->image_path) }}" alt="{{ $room->name }}" class="img-fluid rounded">
                            @else
                                <img src="/user/images/room1.jpg" alt="{{ $room->name }}" class="img-fluid rounded">
                            @endif
                        </div>
                        <div class="col-9">
                            <h5 class="mb-1">{{ $room->name }}</h5>
                            <div class="text-muted small">
                                @if($room->area)<span>{{ $room->area }} m²</span> · @endif
                                @if($room->floor)Tầng {{ $room->floor }}@endif
                            </div>
                            <strong style="color:#f9a825;font-size:18px;">{{ number_format($room->price) }} VNĐ/tháng</strong>
                        </div>
                    </div>
                </div>

                <form id="request" class="main_form" method="POST" action="{{ route('rent-requests.store', $room) }}">
                    @csrf
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label class="form-label fw-semibold">Họ tên</label>
                            <input class="contactus" type="text" value="{{ auth()->user()->name }}" readonly style="background:#f5f5f5;">
                        </div>
                        <div class="col-md-12 mb-3">
                            <label class="form-label fw-semibold">Email</label>
                            <input class="contactus" type="text" value="{{ auth()->user()->email }}" readonly style="background:#f5f5f5;">
                        </div>
                        <div class="col-md-12 mb-3">
                            <label class="form-label fw-semibold">Ghi chú / Yêu cầu thêm</label>
                            <textarea class="textarea" name="note" placeholder="VD: Muốn xem phòng trực tiếp, hoặc ghi chú đặc biệt...">{{ old('note') }}</textarea>
                            @error('note')<span class="text-danger small">{{ $message }}</span>@enderror
                        </div>
                        <div class="col-md-12 mt-2">
                            <button class="send_btn" type="submit">
                                <i class="fa fa-paper-plane mr-1"></i> Gửi Yêu Cầu
                            </button>
                            <a href="{{ route('rooms.show', $room) }}" class="send_btn ml-2" style="background:#999;display:inline-block;">
                                Quay lại
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
