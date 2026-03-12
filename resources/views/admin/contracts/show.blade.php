@extends('layouts.admin')
@section('title', 'Chi Tiết Hợp Đồng #' . $contract->id)

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="fs-3 mb-0">Hợp đồng #{{ $contract->id }}</h1>
    <div class="d-flex gap-2">
        @if($contract->status === 'active')
            <form method="POST" action="{{ route('admin.contracts.end', $contract) }}" onsubmit="return confirm('Kết thúc hợp đồng này? Phòng sẽ được đặt về trạng thái trống.')">
                @csrf
                <button class="btn btn-warning"><i class="ti ti-square-off me-1"></i>Kết thúc HĐ</button>
            </form>
        @endif
        <a href="{{ route('admin.contracts.index') }}" class="btn btn-outline-secondary"><i class="ti ti-arrow-left me-1"></i>Quay lại</a>
    </div>
</div>

<div class="row g-4">
    <div class="col-md-6">
        <div class="card p-4">
            <h5 class="mb-3 border-bottom pb-2">Thông tin hợp đồng</h5>
            <div class="row gy-2">
                <div class="col-6 text-muted">Người thuê</div><div class="col-6 fw-semibold">{{ $contract->user->name }}</div>
                <div class="col-6 text-muted">SĐT</div><div class="col-6">{{ $contract->user->phone }}</div>
                <div class="col-6 text-muted">Email</div><div class="col-6">{{ $contract->user->email }}</div>
                <div class="col-6 text-muted">Phòng</div><div class="col-6 fw-semibold">{{ $contract->room->name }}</div>
                <div class="col-6 text-muted">Ngày bắt đầu</div><div class="col-6">{{ $contract->start_date->format('d/m/Y') }}</div>
                <div class="col-6 text-muted">Ngày kết thúc</div><div class="col-6">{{ $contract->end_date?->format('d/m/Y') ?? '—' }}</div>
                <div class="col-6 text-muted">Tiền cọc</div><div class="col-6">{{ number_format($contract->deposit) }}đ</div>
                <div class="col-6 text-muted">Tiền thuê/tháng</div><div class="col-6 fw-bold text-primary">{{ number_format($contract->monthly_rent) }}đ</div>
                <div class="col-6 text-muted">Trạng thái</div>
                <div class="col-6"><span class="badge {{ $contract->status === 'active' ? 'bg-success' : 'bg-secondary' }}">{{ $contract->status === 'active' ? 'Đang hiệu lực' : 'Đã kết thúc' }}</span></div>
                @if($contract->notes)
                    <div class="col-6 text-muted">Ghi chú</div><div class="col-6">{{ $contract->notes }}</div>
                @endif
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-transparent d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Hóa đơn thuộc hợp đồng</h5>
                <a href="{{ route('admin.invoices.create') }}" class="btn btn-sm btn-primary"><i class="ti ti-plus me-1"></i>Tạo hóa đơn</a>
            </div>
            <div class="table-responsive">
                <table class="table table-sm mb-0">
                    <thead class="table-light"><tr><th>Tháng/Năm</th><th>Tổng tiền</th><th>Trạng thái</th></tr></thead>
                    <tbody>
                        @forelse($contract->invoices as $invoice)
                            <tr>
                                <td>{{ $invoice->month }}/{{ $invoice->year }}</td>
                                <td>{{ number_format($invoice->total_amount) }}đ</td>
                                <td><span class="badge bg-{{ $invoice->statusBadge() }}-subtle text-{{ $invoice->statusBadge() }}">{{ $invoice->statusLabel() }}</span></td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="text-center py-3 text-muted">Chưa có hóa đơn</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
