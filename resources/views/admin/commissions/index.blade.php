@extends('layouts.admin')
@section('title', 'Quản lý Hoa hồng')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="fs-3 mb-1">Quản lý Phí Hoa hồng</h1>
        <p class="text-muted small mb-0">Theo dõi và thu phí quản lý từ các chủ trọ</p>
    </div>
</div>

<div class="card border-0 shadow-sm overflow-hidden">
    <div class="card-header bg-white py-3 border-bottom-0">
        <form action="{{ route('admin.commissions.index') }}" method="GET" class="row g-2">
            <div class="col-md-3">
                <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="">Tất cả trạng thái</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Chưa thu (Pending)</option>
                    <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>Đã thu (Paid)</option>
                </select>
            </div>
        </form>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="bg-light">
                <tr>
                    <th class="ps-4">Hóa đơn</th>
                    <th>Chủ trọ</th>
                    <th>Giá trị HĐ</th>
                    <th>% Phí</th>
                    <th>Tiền Hoa hồng</th>
                    <th>Trạng thái</th>
                    <th class="pe-4 text-end">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                @forelse($commissions as $comm)
                <tr>
                    <td class="ps-4">
                        <div class="fw-bold">#{{ $comm->invoice_id }}</div>
                        <div class="small text-muted">{{ $comm->invoice->room->name ?? 'N/A' }}</div>
                    </td>
                    <td>
                        <div class="fw-semibold">{{ $comm->landlord->name }}</div>
                        <div class="small text-muted">{{ $comm->landlord->phone }}</div>
                    </td>
                    <td>{{ number_format($comm->invoice->total_amount) }}đ</td>
                    <td><span class="badge bg-info-subtle text-info">{{ $comm->rate }}%</span></td>
                    <td><span class="fw-bold text-danger">{{ number_format($comm->amount) }}đ</span></td>
                    <td>
                        @if($comm->status === 'paid')
                            <span class="badge bg-success-subtle text-success"><i class="ti ti-check me-1"></i>Đã thu</span>
                        @else
                            <span class="badge bg-warning-subtle text-warning"><i class="ti ti-clock me-1"></i>Chờ thanh toán</span>
                        @endif
                    </td>
                    <td class="pe-4 text-end">
                        @if($comm->status === 'pending')
                        <form action="{{ route('admin.commissions.pay', $comm) }}" method="POST" style="display:inline;">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-primary" onclick="return confirm('Xác nhận đã thu phí này?')">Xử lý thu</button>
                        </form>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center py-5 text-muted">Chưa có dữ liệu hoa hồng</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($commissions->hasPages())
    <div class="card-footer bg-white border-top-0 px-4 py-3">
        {{ $commissions->links() }}
    </div>
    @endif
</div>
@endsection
