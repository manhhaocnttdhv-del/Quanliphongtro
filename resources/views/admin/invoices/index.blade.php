@extends('layouts.admin')
@section('title', 'Quản Lý Hóa Đơn')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div><h1 class="fs-3 mb-1">Hóa đơn</h1><p class="text-muted mb-0">{{ $invoices->total() }} hóa đơn</p></div>
    <a href="{{ route('admin.invoices.create') }}" class="btn btn-primary rounded-pill px-4"><i class="ti ti-plus me-1"></i>Tạo hóa đơn</a>
</div>

<div class="card mb-4 p-3">
    <form method="GET" class="row g-2">
        <div class="col-md-3">
            <select class="form-select" name="status">
                <option value="">Tất cả trạng thái</option>
                <option value="unpaid" {{ request('status')=='unpaid'?'selected':'' }}>Chưa thanh toán</option>
                <option value="paying" {{ request('status')=='paying'?'selected':'' }}>Đang thanh toán</option>
                <option value="paid" {{ request('status')=='paid'?'selected':'' }}>Đã thanh toán</option>
                <option value="overdue" {{ request('status')=='overdue'?'selected':'' }}>Quá hạn</option>
                <option value="cancelled" {{ request('status')=='cancelled'?'selected':'' }}>Đã hủy</option>
            </select>
        </div>
        <div class="col-md-2">
            <select class="form-select" name="month">
                <option value="">Tháng</option>
                @for($m=1;$m<=12;$m++)<option value="{{ $m }}" {{ request('month')==$m?'selected':'' }}>Tháng {{ $m }}</option>@endfor
            </select>
        </div>
        <div class="col-md-2"><input type="number" class="form-control" name="year" value="{{ request('year', now()->year) }}" placeholder="Năm"></div>
        <div class="col-md-2"><button class="btn btn-primary w-100">Lọc</button></div>
        <div class="col-md-2"><a href="{{ route('admin.invoices.index') }}" class="btn btn-outline-secondary w-100">Reset</a></div>
    </form>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr><th>#</th><th>Phòng</th><th>Người thuê</th><th>Tháng/Năm</th><th>Tổng tiền</th><th>Hạn TT</th><th>Trạng thái</th><th>Thao tác</th></tr>
            </thead>
            <tbody>
                @forelse($invoices as $invoice)
                    <tr>
                        <td><span class="text-muted small">{{ $invoice->transaction_id ?? '#'.$invoice->id }}</span></td>
                        <td><strong>{{ $invoice->room->name ?? 'N/A' }}</strong></td>
                        <td class="small">{{ $invoice->contract?->user?->name ?? '—' }}</td>
                        <td>{{ $invoice->month }}/{{ $invoice->year }}</td>
                        <td><strong>{{ number_format($invoice->total_amount) }}đ</strong></td>
                        <td class="small">
                            @if($invoice->due_date)
                                <span class="{{ $invoice->isOverdue() ? 'text-danger fw-bold' : 'text-muted' }}">
                                    {{ $invoice->due_date->format('d/m/Y') }}
                                </span>
                            @else
                                —
                            @endif
                        </td>
                        <td>
                            <span class="badge bg-{{ $invoice->statusBadge() }}-subtle text-{{ $invoice->statusBadge() }} border border-{{ $invoice->statusBadge() }}">
                                {{ $invoice->statusLabel() }}
                            </span>
                        </td>
                        <td>
                            <div class="d-flex gap-1">
                                <a href="{{ route('admin.invoices.show', $invoice) }}" class="btn btn-sm btn-outline-primary" title="Xem"><i class="ti ti-eye"></i></a>
                                @if($invoice->status === 'unpaid' || $invoice->status === 'overdue')
                                    <button class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#payModal{{ $invoice->id }}" title="Xác nhận TT"><i class="ti ti-check"></i></button>
                                @endif
                                @if(!$invoice->isPaid() && $invoice->status !== 'cancelled')
                                    <form method="POST" action="{{ route('admin.invoices.cancel', $invoice) }}" onsubmit="return confirm('Hủy hóa đơn này?')">
                                        @csrf
                                        <button class="btn btn-sm btn-outline-danger" title="Hủy"><i class="ti ti-x"></i></button>
                                    </form>
                                @endif
                            </div>

                            {{-- Payment Modal --}}
                            @if($invoice->status === 'unpaid' || $invoice->status === 'overdue')
                                <div class="modal fade" id="payModal{{ $invoice->id }}" tabindex="-1">
                                    <div class="modal-dialog modal-sm">
                                        <div class="modal-content" style="border-radius:14px;">
                                            <div class="modal-header border-0 pb-0"><h5 class="modal-title">Xác nhận thanh toán</h5><button class="btn-close" data-bs-dismiss="modal"></button></div>
                                            <form method="POST" action="{{ route('admin.invoices.confirm-payment', $invoice) }}">
                                                @csrf
                                                <div class="modal-body">
                                                    <div class="mb-3">
                                                        <label class="form-label fw-semibold small">Phương thức<span class="text-danger">*</span></label>
                                                        <select class="form-select" name="payment_method" required>
                                                            <option value="Tiền mặt">💵 Tiền mặt</option>
                                                            <option value="Chuyển khoản">🏦 Chuyển khoản</option>
                                                            <option value="MoMo">📱 MoMo</option>
                                                            <option value="QR Code">📷 QR Code</option>
                                                        </select>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label fw-semibold small">Mã tham chiếu</label>
                                                        <input type="text" class="form-control" name="payment_ref" placeholder="Mã GD (tuỳ chọn)">
                                                    </div>
                                                </div>
                                                <div class="modal-footer border-0 pt-0">
                                                    <button class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Đóng</button>
                                                    <button class="btn btn-success btn-sm">Xác nhận</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="8" class="text-center py-5 text-muted">Chưa có hóa đơn nào</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer bg-transparent">{{ $invoices->links() }}</div>
</div>
@endsection
