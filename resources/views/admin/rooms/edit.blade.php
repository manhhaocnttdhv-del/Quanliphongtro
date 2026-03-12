@extends('layouts.admin')
@section('title', 'Sửa Phòng - ' . $room->name)

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="fs-3 mb-0">Sửa phòng: {{ $room->name }}</h1>
    <a href="{{ route('admin.rooms.index') }}" class="btn btn-outline-secondary"><i class="ti ti-arrow-left me-1"></i>Quay lại</a>
</div>

<div class="card">
    <div class="card-body p-4">
        {{-- Existing images --}}
        @if($room->images->count() > 0)
            <div class="mb-4">
                <label class="form-label fw-semibold">Ảnh hiện tại</label>
                <div class="row g-2">
                    @foreach($room->images as $img)
                        <div class="col-md-2 col-4 position-relative">
                            <img src="{{ asset('storage/'.$img->image_path) }}" class="img-fluid rounded" style="height:100px;object-fit:cover;width:100%;">
                            <form method="POST" action="{{ route('admin.rooms.images.destroy', $img) }}">
                                @csrf @method('DELETE')
                                <button class="btn btn-danger btn-sm position-absolute top-0 end-0 m-1 rounded-circle" style="width:24px;height:24px;padding:0;font-size:12px;" onclick="return confirm('Xoá ảnh này?')">×</button>
                            </form>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <form method="POST" action="{{ route('admin.rooms.update', $room) }}" enctype="multipart/form-data">
            @csrf @method('PUT')
            @include('admin.rooms._form', ['room' => $room])
            <div class="mt-4">
                <button type="submit" class="btn btn-primary px-4"><i class="ti ti-device-floppy me-1"></i>Lưu thay đổi</button>
            </div>
        </form>
    </div>
</div>
@endsection
