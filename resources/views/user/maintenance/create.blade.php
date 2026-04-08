@extends('layouts.user')
@section('title', 'Gửi Yêu Cầu Bảo Trì')

@section('styles')
<style>
.form-hero {
    background: linear-gradient(135deg, #1e3a5f 0%, #2d6a4f 100%);
    padding: 50px 0 30px;
    color: #fff;
}
.form-card {
    border: none;
    border-radius: 20px;
    box-shadow: 0 10px 40px rgba(0,0,0,.08);
}
.priority-option { cursor: pointer; }
.priority-option input[type=radio] { display: none; }
.priority-option label {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 10px 16px;
    border: 2px solid #e5e7eb;
    border-radius: 10px;
    cursor: pointer;
    font-size: 14px;
    transition: all .2s;
}
.priority-option input:checked + label { border-color: #3b82f6; background: #eff6ff; }
.priority-urgent input:checked + label { border-color: #ef4444; background: #fef2f2; }
.priority-high input:checked + label { border-color: #f59e0b; background: #fffbeb; }
.priority-low input:checked + label { border-color: #94a3b8; background: #f8fafc; }
.upload-area {
    border: 2px dashed #d1d5db;
    border-radius: 12px;
    padding: 30px;
    text-align: center;
    cursor: pointer;
    transition: border-color .2s;
}
.upload-area:hover { border-color: #3b82f6; }
#preview-images { display: flex; gap: 8px; flex-wrap: wrap; margin-top: 12px; }
#preview-images img { width: 80px; height: 65px; object-fit: cover; border-radius: 8px; }
</style>
@endsection

@section('content')
<div class="form-hero">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('maintenance.index') }}" class="text-white opacity-75 text-decoration-none">Bảo trì</a></li>
                <li class="breadcrumb-item active text-white">Gửi yêu cầu mới</li>
            </ol>
        </nav>
        <h1 class="h3 fw-bold mb-0">🔧 Gửi Yêu Cầu Sửa Chữa</h1>
    </div>
</div>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="form-card card p-4 p-md-5">
                <form method="POST" action="{{ route('maintenance.store') }}" enctype="multipart/form-data">
                    @csrf

                    {{-- Room --}}
                    <div class="mb-4">
                        <label class="form-label fw-semibold">Phòng <span class="text-danger">*</span></label>
                        <select name="room_id" class="form-select @error('room_id') is-invalid @enderror" required>
                            <option value="">— Chọn phòng —</option>
                            @foreach($rooms as $room)
                                <option value="{{ $room->id }}" {{ old('room_id') == $room->id ? 'selected' : '' }}>
                                    {{ $room->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('room_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    {{-- Title --}}
                    <div class="mb-4">
                        <label class="form-label fw-semibold">Tiêu đề sự cố <span class="text-danger">*</span></label>
                        <input type="text" name="title" class="form-control @error('title') is-invalid @enderror"
                               value="{{ old('title') }}" placeholder="VD: Điện phòng bị chập, Vòi nước bị rò rỉ..." required>
                        @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    {{-- Description --}}
                    <div class="mb-4">
                        <label class="form-label fw-semibold">Mô tả chi tiết <span class="text-danger">*</span></label>
                        <textarea name="description" rows="4"
                                  class="form-control @error('description') is-invalid @enderror"
                                  placeholder="Mô tả cụ thể vị trí, tình trạng, mức độ nghiêm trọng của sự cố..." required>{{ old('description') }}</textarea>
                        @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    {{-- Priority --}}
                    <div class="mb-4">
                        <label class="form-label fw-semibold d-block">Mức độ ưu tiên <span class="text-danger">*</span></label>
                        <div class="row g-2">
                            <div class="col-6 col-sm-3 priority-option priority-low">
                                <input type="radio" name="priority" id="p_low" value="low" {{ old('priority','medium') == 'low' ? 'checked' : '' }}>
                                <label for="p_low">⚪ Thấp</label>
                            </div>
                            <div class="col-6 col-sm-3 priority-option">
                                <input type="radio" name="priority" id="p_med" value="medium" {{ old('priority','medium') == 'medium' ? 'checked' : '' }}>
                                <label for="p_med">🔵 Trung bình</label>
                            </div>
                            <div class="col-6 col-sm-3 priority-option priority-high">
                                <input type="radio" name="priority" id="p_high" value="high" {{ old('priority') == 'high' ? 'checked' : '' }}>
                                <label for="p_high">🟠 Cao</label>
                            </div>
                            <div class="col-6 col-sm-3 priority-option priority-urgent">
                                <input type="radio" name="priority" id="p_urgent" value="urgent" {{ old('priority') == 'urgent' ? 'checked' : '' }}>
                                <label for="p_urgent">🔴 Khẩn cấp</label>
                            </div>
                        </div>
                        @error('priority')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>

                    {{-- Images --}}
                    <div class="mb-4">
                        <label class="form-label fw-semibold">Ảnh đính kèm <span class="text-muted fw-normal">(tối đa 5 ảnh)</span></label>
                        <div class="upload-area" onclick="document.getElementById('file-input').click()">
                            <i class="fa fa-camera fa-2x text-muted mb-2"></i>
                            <div class="text-muted">Nhấn để tải ảnh lên hoặc kéo & thả ảnh vào đây</div>
                            <div class="text-muted small">JPG, PNG, WEBP – tối đa 3MB mỗi ảnh</div>
                        </div>
                        <input type="file" id="file-input" name="images[]" multiple accept="image/*" class="d-none">
                        <div id="preview-images"></div>
                        @error('images')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                        @error('images.*')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>

                    <div class="d-flex gap-3">
                        <a href="{{ route('maintenance.index') }}" class="btn btn-outline-secondary flex-fill">Hủy</a>
                        <button type="submit" class="btn btn-primary flex-fill fw-semibold">
                            <i class="fa fa-paper-plane me-2"></i>Gửi yêu cầu
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.getElementById('file-input').addEventListener('change', function (e) {
    const preview = document.getElementById('preview-images');
    preview.innerHTML = '';
    [...e.target.files].slice(0, 5).forEach(file => {
        const reader = new FileReader();
        reader.onload = ev => {
            const img = document.createElement('img');
            img.src = ev.target.result;
            preview.appendChild(img);
        };
        reader.readAsDataURL(file);
    });
});
</script>
@endsection
