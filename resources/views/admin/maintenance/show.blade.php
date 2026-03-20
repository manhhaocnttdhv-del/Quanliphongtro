@extends('layouts.admin')
@section('title', 'Chi tiết Yêu Cầu Bảo Trì #' . $maintenance->id)

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1">
                <li class="breadcrumb-item"><a href="{{ route('admin.maintenance.index') }}" class="text-decoration-none text-muted">Bảo trì</a></li>
                <li class="breadcrumb-item active">Yêu cầu #{{ $maintenance->id }}</li>
            </ol>
        </nav>
        <h1 class="fs-3 fw-bold mb-0">{{ $maintenance->title }}</h1>
    </div>
    <a href="{{ route('admin.maintenance.index') }}" class="btn btn-outline-secondary">
        <i class="ti ti-arrow-left me-1"></i>Quay lại
    </a>
</div>

<div class="row g-4">
    {{-- Left: Detail --}}
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-transparent border-0 pt-4 px-4">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-circle bg-warning-subtle d-flex align-items-center justify-content-center" style="width:48px;height:48px;">
                        <i class="ti ti-tool fs-4 text-warning"></i>
                    </div>
                    <div>
                        <h5 class="mb-1 fw-bold">Mô tả sự cố</h5>
                        <div class="d-flex gap-2">
                            <span class="badge rounded-pill bg-{{ $maintenance->priorityBadge() }}-subtle text-{{ $maintenance->priorityBadge() }} border border-{{ $maintenance->priorityBadge() }}-subtle">
                                <i class="ti ti-flag me-1"></i>{{ $maintenance->priorityLabel() }}
                            </span>
                            <span class="badge rounded-pill bg-{{ $maintenance->statusBadge() }}-subtle text-{{ $maintenance->statusBadge() }}">
                                {{ $maintenance->statusLabel() }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body px-4 pb-4">
                <p class="text-secondary lh-lg mt-3">{{ $maintenance->description }}</p>

                {{-- Images --}}
                @if($maintenance->images && count($maintenance->images))
                    <h6 class="fw-semibold mt-4 mb-3 text-muted text-uppercase" style="font-size:11px;letter-spacing:1px;">Ảnh đính kèm</h6>
                    <div class="d-flex flex-wrap gap-2">
                        @foreach($maintenance->images as $img)
                            <a href="{{ Storage::url($img) }}" target="_blank">
                                <img src="{{ Storage::url($img) }}" class="rounded-3 shadow-sm" style="width:120px;height:90px;object-fit:cover;" alt="ảnh bảo trì">
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        {{-- Admin Note --}}
        @if($maintenance->admin_note)
            <div class="card border-0 shadow-sm border-start border-4 border-info mb-4">
                <div class="card-body px-4">
                    <h6 class="fw-semibold mb-2"><i class="ti ti-note text-info me-2"></i>Ghi chú xử lý</h6>
                    <p class="mb-0 text-secondary">{{ $maintenance->admin_note }}</p>
                </div>
            </div>
        @endif
    </div>

    {{-- Right: Info + Action --}}
    <div class="col-lg-4">
        {{-- Room & Tenant Info --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-transparent border-0 pt-4 px-4">
                <h6 class="fw-bold mb-0"><i class="ti ti-info-circle me-2 text-primary"></i>Thông tin</h6>
            </div>
            <div class="card-body px-4 pb-4">
                <div class="mb-3 pb-3 border-bottom">
                    <div class="text-muted small text-uppercase mb-1" style="letter-spacing:0.5px;">Phòng</div>
                    <div class="fw-semibold">{{ $maintenance->room->name }}</div>
                </div>
                <div class="mb-3 pb-3 border-bottom">
                    <div class="text-muted small text-uppercase mb-1" style="letter-spacing:0.5px;">Khách thuê</div>
                    <div class="fw-semibold">{{ $maintenance->user->name }}</div>
                    <div class="small text-muted">{{ $maintenance->user->phone }}</div>
                    <div class="small text-muted">{{ $maintenance->user->email }}</div>
                </div>
                <div class="mb-3 pb-3 border-bottom">
                    <div class="text-muted small text-uppercase mb-1" style="letter-spacing:0.5px;">Ngày gửi</div>
                    <div class="fw-semibold">{{ $maintenance->created_at->format('d/m/Y H:i') }}</div>
                </div>
                @if($maintenance->resolved_at)
                    <div>
                        <div class="text-muted small text-uppercase mb-1" style="letter-spacing:0.5px;">Ngày hoàn thành</div>
                        <div class="fw-semibold text-success">{{ $maintenance->resolved_at->format('d/m/Y H:i') }}</div>
                    </div>
                @endif
            </div>
        </div>

        {{-- Update Status Form --}}
        @if($maintenance->status !== 'done' && $maintenance->status !== 'rejected')
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent border-0 pt-4 px-4">
                    <h6 class="fw-bold mb-0"><i class="ti ti-edit me-2 text-success"></i>Cập nhật xử lý</h6>
                </div>
                <div class="card-body px-4 pb-4">
                    <form method="POST" action="{{ route('admin.maintenance.update-status', $maintenance) }}">
                        @csrf
                        @method('PATCH')
                        <div class="mb-3">
                            <label class="form-label fw-semibold small">Trạng thái</label>
                            <select name="status" class="form-select" required>
                                <option value="pending" {{ $maintenance->status === 'pending' ? 'selected' : '' }}>Chờ xử lý</option>
                                <option value="in_progress" {{ $maintenance->status === 'in_progress' ? 'selected' : '' }}>Đang xử lý</option>
                                <option value="done">✅ Hoàn thành</option>
                                <option value="rejected">❌ Từ chối</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold small">Ghi chú phản hồi</label>
                            <textarea name="admin_note" class="form-control" rows="3" placeholder="Mô tả cách xử lý, lý do từ chối...">{{ $maintenance->admin_note }}</textarea>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="ti ti-check me-2"></i>Lưu thay đổi
                        </button>
                    </form>
                </div>
            </div>
        @else
            <div class="card border-0 shadow-sm {{ $maintenance->status === 'done' ? 'border-success' : 'border-danger' }} border-2">
                <div class="card-body px-4 py-4 text-center">
                    <i class="ti ti-{{ $maintenance->status === 'done' ? 'circle-check text-success' : 'circle-x text-danger' }} fs-1 d-block mb-2"></i>
                    <div class="fw-semibold">{{ $maintenance->statusLabel() }}</div>
                    @if($maintenance->resolved_at)
                        <div class="text-muted small mt-1">{{ $maintenance->resolved_at->format('d/m/Y H:i') }}</div>
                    @endif
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
