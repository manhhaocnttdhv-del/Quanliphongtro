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
                <option value="pending"  {{ request('status')=='pending'  ? 'selected':'' }}>Chờ duyệt</option>
                <option value="approved" {{ request('status')=='approved' ? 'selected':'' }}>Đã duyệt</option>
                <option value="rejected" {{ request('status')=='rejected' ? 'selected':'' }}>Từ chối</option>
            </select>
        </div>
        <div class="col-md-2"><button class="btn btn-primary w-100">Lọc</button></div>
    </form>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Người thuê</th>
                    <th>Phòng</th>
                    <th>Ngày vào dự kiến</th>
                    <th>Ghi chú</th>
                    <th>Ngày gửi</th>
                    <th>Trạng thái</th>
                    <th>Thao tác</th>
                </tr>
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
                        <td class="small">
                            @if($req->move_in_date)
                                <span class="badge bg-info-subtle text-info border border-info">
                                    {{ $req->move_in_date->format('d/m/Y') }}
                                </span>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td><div class="text-muted small" style="max-width:160px;">{{ $req->note ?? '—' }}</div></td>
                        <td class="small">{{ $req->created_at->format('d/m/Y H:i') }}</td>
                        <td><span class="badge bg-{{ $req->statusBadge() }}-subtle text-{{ $req->statusBadge() }} border border-{{ $req->statusBadge() }}">{{ $req->statusLabel() }}</span></td>
                        <td>
                            @if($req->status === 'pending')
                                <div class="d-flex gap-1">
                                    <button class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#approveModal{{ $req->id }}">
                                        <i class="ti ti-check"></i> Duyệt
                                    </button>
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
                                                <h5 class="modal-title">Duyệt yêu cầu — {{ $req->room->name }}</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <form method="POST" action="{{ route('admin.rent-requests.approve', $req) }}">
                                                @csrf
                                                <div class="modal-body">
                                                    {{-- Thông tin người thuê --}}
                                                    <div class="alert alert-light border mb-3 small">
                                                        <div><strong>Người thuê:</strong> {{ $req->user->name }} — {{ $req->user->phone }}</div>
                                                        <div><strong>Giá niêm yết:</strong> {{ number_format($req->room->price) }}đ/tháng</div>
                                                        @if($req->move_in_date)
                                                            <div><strong>Ngày dự kiến vào:</strong> {{ $req->move_in_date->format('d/m/Y') }}</div>
                                                        @endif
                                                        @if($req->note)
                                                            <div><strong>Ghi chú:</strong> {{ $req->note }}</div>
                                                        @endif
                                                    </div>

                                                    <div class="row g-3">
                                                        <div class="col-6">
                                                            <label class="form-label fw-semibold">Ngày bắt đầu <span class="text-danger">*</span></label>
                                                            <input type="date" class="form-control" name="start_date"
                                                                value="{{ $req->move_in_date ? $req->move_in_date->format('Y-m-d') : now()->format('Y-m-d') }}"
                                                                required>
                                                        </div>
                                                        <div class="col-6">
                                                            <label class="form-label fw-semibold">Ngày kết thúc</label>
                                                            <input type="date" class="form-control" name="end_date">
                                                        </div>
                                                    </div>

                                                    <div class="row g-3 mt-1">
                                                        <div class="col-6">
                                                            <label class="form-label fw-semibold">Giá thuê/tháng (VNĐ) <span class="text-danger">*</span></label>
                                                            <input type="number" class="form-control" name="monthly_rent"
                                                                value="{{ $req->room->price }}"
                                                                required min="0" step="50000">
                                                            <div class="form-text">Có thể điều chỉnh nếu đã thương lượng</div>
                                                        </div>
                                                        <div class="col-6">
                                                            <label class="form-label fw-semibold">Tiền cọc (VNĐ) <span class="text-danger">*</span></label>
                                                            <input type="number" class="form-control" name="deposit"
                                                                value="{{ $req->room->price }}"
                                                                required min="0" step="50000">
                                                        </div>
                                                    </div>

                                                    <div class="mt-3">
                                                        <label class="form-label fw-semibold">Ghi chú hợp đồng</label>
                                                        <textarea class="form-control" name="notes" rows="2" placeholder="Điều khoản bổ sung, lưu ý..."></textarea>
                                                    </div>

                                                    @php
                                                        $otherPendingCount = \App\Models\RentRequest::where('room_id', $req->room_id)
                                                            ->where('id', '!=', $req->id)
                                                            ->where('status', 'pending')
                                                            ->count();
                                                    @endphp
                                                    @if($otherPendingCount > 0)
                                                        <div class="alert alert-warning mt-3 small mb-0">
                                                            <i class="ti ti-alert-triangle me-1"></i>
                                                            Có <strong>{{ $otherPendingCount }}</strong> yêu cầu khác đang chờ cho phòng này.
                                                            Khi duyệt, tất cả sẽ tự động bị từ chối và nhận thông báo.
                                                        </div>
                                                    @endif
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                                                    <button type="submit" class="btn btn-success"><i class="ti ti-check me-1"></i>Xác nhận duyệt & tạo hợp đồng</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @else
                                @if($req->status === 'approved' && $req->agreed_rent && $req->agreed_rent != $req->room->price)
                                    <span class="text-muted small">Đã duyệt<br>
                                        <span class="text-success small">{{ number_format($req->agreed_rent) }}đ/tháng</span>
                                    </span>
                                @else
                                    <span class="text-muted small">Đã xử lý</span>
                                @endif
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="8" class="text-center text-muted py-5">Chưa có yêu cầu nào</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer bg-transparent">{{ $rentRequests->links() }}</div>
</div>
@endsection
