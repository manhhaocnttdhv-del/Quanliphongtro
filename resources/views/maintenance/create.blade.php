@extends('layouts.user')

@section('title', 'Gửi yêu cầu bảo trì')

@section('content')
<div class="contact" style="padding: 60px 0;">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="titlepage text-center mb-5">
                    <h2>Báo cáo sự cố bảo trì</h2>
                    <p>Mô tả chi tiết vấn đề bạn đang gặp phải để chúng tôi hỗ trợ nhanh nhất.</p>
                </div>
            </div>
        </div>
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow-sm border-0 rounded-lg">
                    <div class="card-body p-5">
                        <form action="{{ route('maintenance.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="form-group mb-4">
                                <label for="room_id" class="fw-bold">Chọn phòng <span class="text-danger">*</span></label>
                                <select name="room_id" id="room_id" class="form-control @error('room_id') is-invalid @enderror" required>
                                    <option value="">-- Chọn phòng của bạn --</option>
                                    @foreach($rooms as $room)
                                        <option value="{{ $room->id }}">{{ $room->name }}</option>
                                    @endforeach
                                </select>
                                @error('room_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group mb-4">
                                <label for="title" class="fw-bold">Tiêu đề sự cố <span class="text-danger">*</span></label>
                                <input type="text" name="title" id="title" class="form-control @error('title') is-invalid @enderror" placeholder="VD: Hỏng bóng đèn, Rò rỉ nước..." value="{{ old('title') }}" required>
                                @error('title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group mb-4">
                                <label for="priority" class="fw-bold">Mức độ ưu tiên <span class="text-danger">*</span></label>
                                <select name="priority" id="priority" class="form-control" required>
                                    <option value="low">Thấp (Có thể đợi)</option>
                                    <option value="medium" selected>Trung bình (Cần xử lý trong ngày)</option>
                                    <option value="high">Cao (Khẩn cấp)</option>
                                </select>
                            </div>

                            <div class="form-group mb-4">
                                <label for="description" class="fw-bold">Mô tả chi tiết <span class="text-danger">*</span></label>
                                <textarea name="description" id="description" rows="5" class="form-control @error('description') is-invalid @enderror" placeholder="Hãy mô tả rõ tình trạng sự cố..." required>{{ old('description') }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group mb-4">
                                <label for="images" class="fw-bold">Ảnh minh họa (không bắt buộc)</label>
                                <input type="file" name="images[]" id="images" class="form-control-file d-block mt-1" multiple accept="image/*">
                                <small class="text-muted">Bạn có thể chọn nhiều ảnh cùng lúc.</small>
                            </div>

                            <div class="d-flex justify-content-between mt-5">
                                <a href="{{ route('maintenance.index') }}" class="btn btn-light px-4">Quay lại</a>
                                <button type="submit" class="btn btn-primary px-5" style="background:#fe0000; border:none;">Gửi yêu cầu</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
