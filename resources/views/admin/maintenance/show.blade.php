@extends('layouts.admin')

@section('title', 'Chi tiết bảo trì #' . $maintenance->id)

@section('content')
<div class="mb-4">
    <h4 class="fw-bold mb-0">Chi tiết yêu cầu bảo trì #{{ $maintenance->id }}</h4>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.maintenance.index') }}">Bảo trì</a></li>
            <li class="breadcrumb-item active">Chi tiết</li>
        </ol>
    </nav>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white border-bottom py-3">
                <h6 class="fw-bold mb-0">Thông tin sự cố</h6>
            </div>
            <div class="card-body">
                <h5 class="fw-bold mb-3 text-primary">{{ $maintenance->title }}</h5>
                <p class="text-muted mb-4" style="white-space: pre-wrap;">{{ $maintenance->description }}</p>

                @if($maintenance->images && count($maintenance->images) > 0)
                    <h6 class="fw-bold mb-3">Hình ảnh đính kèm:</h6>
                    <div class="row g-3">
                        @foreach($maintenance->images as $img)
                            <div class="col-md-4">
                                <a href="{{ asset('storage/' . $img) }}" target="_blank">
                                    <img src="{{ asset('storage/' . $img) }}" class="img-fluid rounded shadow-sm" alt="Maintenance Image" style="height: 150px; width: 100%; object-fit: cover;">
                                </a>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-bottom py-3">
                <h6 class="fw-bold mb-0">Cập nhật xử lý</h6>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.maintenance.update', $maintenance) }}" method="POST">
                    @csrf
                    @method('PATCH')
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Trạng thái</label>
                            <select name="status" class="form-select">
                                <option value="pending" {{ $maintenance->status == 'pending' ? 'selected' : '' }}>Chờ xử lý</option>
                                <option value="in_progress" {{ $maintenance->status == 'in_progress' ? 'selected' : '' }}>Đang xử lý</option>
                                <option value="resolved" {{ $maintenance->status == 'resolved' ? 'selected' : '' }}>Đã giải quyết</option>
                                <option value="cancelled" {{ $maintenance->status == 'cancelled' ? 'selected' : '' }}>Đã hủy</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Mức độ ưu tiên</label>
                            <select name="priority" class="form-select">
                                <option value="low" {{ $maintenance->priority == 'low' ? 'selected' : '' }}>Thấp</option>
                                <option value="medium" {{ $maintenance->priority == 'medium' ? 'selected' : '' }}>Trung bình</option>
                                <option value="high" {{ $maintenance->priority == 'high' ? 'selected' : '' }}>Cao</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold small">Ghi chú xử lý / Phản hồi cho người thuê</label>
                            <textarea name="admin_notes" rows="4" class="form-control" placeholder="Nhập nội dung phản hồi tại đây...">{{ $maintenance->admin_notes }}</textarea>
                        </div>
                        <div class="col-12 text-end">
                            <button type="submit" class="btn btn-primary px-4">Lưu cập nhật</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white border-bottom py-3">
                <h6 class="fw-bold mb-0">Thông tin liên quan</h6>
            </div>
            <div class="card-body p-0">
                <ul class="list-group list-group-flush small">
                    <li class="list-group-item px-3 py-3 border-0">
                        <div class="text-muted mb-1">Căn phòng</div>
                        <div class="fw-bold">{{ $maintenance->room->name }}</div>
                        <div class="text-muted small">{{ $maintenance->room->address_detail }}</div>
                    </li>
                    <hr class="m-0 mx-3">
                    <li class="list-group-item px-3 py-3 border-0">
                        <div class="text-muted mb-1">Người báo cáo</div>
                        <div class="fw-bold">{{ $maintenance->user->name }}</div>
                        <div class="text-primary">{{ $maintenance->user->phone }}</div>
                    </li>
                    <hr class="m-0 mx-3">
                    <li class="list-group-item px-3 py-3 border-0 border-bottom-0">
                        <div class="text-muted mb-1">Ngày gửi</div>
                        <div class="fw-bold">{{ $maintenance->created_at->format('d/m/Y H:i') }}</div>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
