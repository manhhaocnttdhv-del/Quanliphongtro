@extends('layouts.admin')
@section('title', 'Quản Lý Yêu Cầu Thuê')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="fs-3 mb-1">Yêu cầu thuê phòng</h1>
        <p class="text-muted mb-0">Phê duyệt hoặc từ chối yêu cầu của người thuê</p>
    </div>
</div>

<div class="card mb-4 p-3">
    <form method="GET" class="row g-2">
        <div class="col-md-4">
            <select class="form-select" name="status">
                <option value="">Tất cả trạng thái</option>
                <option value="pending" {{ request('status')=='pending'?'selected':'' }}>Chờ duyệt</option>
                <option value="approved" {{ request('status')=='approved'?'selected':'' }}>Đã duyệt</option>
                <option value="rejected" {{ request('status')=='rejected'?'selected':'' }}>Từ chối</option>
            </select>
        </div>
        <div class="col-md-2"><button class="btn btn-primary w-100">Lọc</button></div>
    </form>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr><th>#</th><th>Người thuê</th><th>Phòng</th><th>Ghi chú</th><th>Ngày gửi</th><th>Trạng thái</th><th>Thao tác</th></tr>
            </thead>
            <tbody>
                @forelse($rentRequests as $req)
                    <tr>
                        <td>{{ $req->id }}</td>
                        <td>
                            <div class="fw-semibold">{{ $req->user->name }}</div>
                            <div class="text-muted small">{{ $req->user->phone }}</div>
                        </td>
                        <td>
                            <div class="fw-semibold">{{ $req->room->name }}</div>
                            <div class="text-muted small">{{ number_format($req->room->price) }}đ/tháng</div>
                        </td>
                        <td><div class="text-muted small" style="max-width:160px;">{{ $req->note ?? '—' }}</div></td>
                        <td class="small">{{ $req->created_at->format('d/m/Y H:i') }}</td>
                        <td><span class="badge bg-{{ $req->statusBadge() }}-subtle text-{{ $req->statusBadge() }} border border-{{ $req->statusBadge() }}">{{ $req->statusLabel() }}</span></td>
                        <td>
                            @if($req->status === 'pending')
                                <div class="d-flex gap-1">
                                    <button class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#approveModal{{ $req->id }}"><i class="ti ti-check"></i> Duyệt</button>
                                    <form method="POST" action="{{ route('admin.rent-requests.reject', $req) }}" onsubmit="return confirm('Từ chối yêu cầu này?')">
                                        @csrf
                                        <button class="btn btn-sm btn-danger"><i class="ti ti-x"></i> Từ chối</button>
                                    </form>
                                </div>

                                {{-- Approve Modal --}}
                                <div class="modal fade" id="approveModal{{ $req->id }}" tabindex="-1">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Duyệt yêu cầu - {{ $req->room->name }}</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <form method="POST" action="{{ route('admin.rent-requests.approve', $req) }}">
                                                @csrf
                                                <div class="modal-body">
                                                    <div class="mb-3">
                                                        <label class="form-label fw-semibold">Ngày bắt đầu <span class="text-danger">*</span></label>
                                                        <input type="date" class="form-control" name="start_date" value="{{ now()->format('Y-m-d') }}" required>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label fw-semibold">Ngày kết thúc</label>
                                                        <input type="date" class="form-control" name="end_date">
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label fw-semibold">Tiền cọc (VNĐ) <span class="text-danger">*</span></label>
                                                        <input type="number" class="form-control" name="deposit" value="{{ $req->room->price }}" required min="0">
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label fw-semibold">Ghi chú hợp đồng</label>
                                                        <textarea class="form-control" name="notes" rows="2"></textarea>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                                                    <button type="submit" class="btn btn-success"><i class="ti ti-check me-1"></i>Xác nhận duyệt</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <span class="text-muted small">Đã xử lý</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="text-center text-muted py-5">Chưa có yêu cầu nào</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer bg-transparent">{{ $rentRequests->links() }}</div>
</div>
@endsection
