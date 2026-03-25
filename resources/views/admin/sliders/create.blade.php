@extends('layouts.admin')
@section('title', 'Thêm Slide Mới')

@section('content')
<div class="mb-4">
    <a href="{{ route('admin.sliders.index') }}" class="btn btn-sm btn-light mb-3">
        <i class="ti ti-arrow-left me-1"></i> Quay lại
    </a>
    <h1 class="fs-3 mb-1">Thêm Slide Mới</h1>
</div>

<div class="row justify-content-center">
    <div class="col-lg-7">
        <div class="card p-4">
            <form method="POST" action="{{ route('admin.sliders.store') }}" enctype="multipart/form-data">
                @csrf

                <div class="mb-3">
                    <label class="form-label fw-semibold">Ảnh Slide <span class="text-danger">*</span></label>
                    <input type="file" class="form-control @error('image') is-invalid @enderror"
                           name="image" accept="image/*" required id="imageInput">
                    @error('image')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    <div id="imagePreview" class="mt-2 d-none">
                        <img id="previewImg" src="" alt="Preview"
                             style="max-height:200px;border-radius:10px;border:1px solid #e2e8f0;">
                    </div>
                    <div class="form-text">Nên dùng ảnh 1920×600px, tối đa 4MB.</div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Tiêu đề</label>
                    <input type="text" class="form-control @error('title') is-invalid @enderror"
                           name="title" value="{{ old('title') }}" placeholder="VD: Phòng trọ giá rẻ, tiện nghi">
                    @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Mô tả ngắn</label>
                    <input type="text" class="form-control @error('subtitle') is-invalid @enderror"
                           name="subtitle" value="{{ old('subtitle') }}" placeholder="Dòng mô tả ngắn dưới tiêu đề">
                    @error('subtitle')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Link (khi click vào slide)</label>
                    <input type="url" class="form-control @error('link') is-invalid @enderror"
                           name="link" value="{{ old('link') }}" placeholder="https://...">
                    @error('link')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Thứ tự hiển thị</label>
                        <input type="number" class="form-control" name="order" value="{{ old('order', 0) }}" min="0">
                        <div class="form-text">Số nhỏ hơn hiển thị trước.</div>
                    </div>
                    <div class="col-md-6 d-flex align-items-end">
                        <div class="form-check form-switch mb-2">
                            <input class="form-check-input" type="checkbox" name="is_active" value="1"
                                   id="isActiveCheck" checked>
                            <label class="form-check-label fw-semibold" for="isActiveCheck">Hiển thị ngay</label>
                        </div>
                    </div>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="ti ti-device-floppy me-1"></i> Lưu slide
                    </button>
                    <a href="{{ route('admin.sliders.index') }}" class="btn btn-light px-4">Hủy</a>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.getElementById('imageInput').addEventListener('change', function () {
    const file = this.files[0];
    if (!file) return;
    const reader = new FileReader();
    reader.onload = e => {
        document.getElementById('previewImg').src = e.target.result;
        document.getElementById('imagePreview').classList.remove('d-none');
    };
    reader.readAsDataURL(file);
});
</script>
@endpush
@endsection
