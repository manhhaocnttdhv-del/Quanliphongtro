@extends('layouts.admin')
@section('title', 'Quản lý Slider')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h1 class="fs-3 mb-1">Quản lý Slider</h1>
        <p class="text-muted mb-0">Quản lý hình ảnh slider hiển thị trên trang chủ</p>
    </div>
    <a href="{{ route('admin.sliders.create') }}" class="btn btn-primary">
        <i class="ti ti-plus me-1"></i> Thêm slide
    </a>
</div>

@if($sliders->isEmpty())
    <div class="card p-5 text-center text-muted">
        <i class="ti ti-photo-off" style="font-size:48px;opacity:.3;"></i>
        <p class="mt-3">Chưa có slide nào. <a href="{{ route('admin.sliders.create') }}">Thêm ngay</a></p>
    </div>
@else
    <div class="row g-3">
        @foreach($sliders as $slider)
        <div class="col-lg-4 col-md-6">
            <div class="card overflow-hidden h-100" style="border-radius:14px;">
                {{-- Ảnh preview --}}
                <div style="height:180px;overflow:hidden;position:relative;">
                    <img src="{{ asset('storage/'.$slider->image_path) }}"
                         alt="{{ $slider->title }}"
                         style="width:100%;height:100%;object-fit:cover;">
                    {{-- Order badge --}}
                    <span class="badge bg-dark position-absolute" style="top:10px;left:10px;font-size:11px;">
                        #{{ $slider->order }}
                    </span>
                    {{-- Status badge --}}
                    <span class="badge position-absolute" style="top:10px;right:10px;font-size:11px;
                        background:{{ $slider->is_active ? '#10b981' : '#94a3b8' }};">
                        {{ $slider->is_active ? '● Bật' : '○ Tắt' }}
                    </span>
                </div>
                <div class="card-body p-3">
                    <div class="fw-semibold mb-1" style="font-size:14px;">
                        {{ $slider->title ?: '(Không có tiêu đề)' }}
                    </div>
                    @if($slider->subtitle)
                        <div class="text-muted small mb-2">{{ Str::limit($slider->subtitle, 60) }}</div>
                    @endif
                    @if($slider->link)
                        <div class="small text-primary mb-2">
                            <i class="ti ti-link me-1"></i>{{ Str::limit($slider->link, 40) }}
                        </div>
                    @endif
                    <div class="d-flex gap-2 mt-2">
                        {{-- Toggle active --}}
                        <form method="POST" action="{{ route('admin.sliders.toggle', $slider) }}">
                            @csrf
                            <button type="submit" class="btn btn-sm {{ $slider->is_active ? 'btn-warning' : 'btn-success' }}">
                                <i class="ti ti-{{ $slider->is_active ? 'eye-off' : 'eye' }}"></i>
                                {{ $slider->is_active ? 'Tắt' : 'Bật' }}
                            </button>
                        </form>
                        <a href="{{ route('admin.sliders.edit', $slider) }}" class="btn btn-sm btn-outline-primary">
                            <i class="ti ti-edit"></i> Sửa
                        </a>
                        <form method="POST" action="{{ route('admin.sliders.destroy', $slider) }}"
                              onsubmit="return confirm('Xóa slide này?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                <i class="ti ti-trash"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
@endif
@endsection
