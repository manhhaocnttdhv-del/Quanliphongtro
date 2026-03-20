@extends('layouts.user')
@section('title', 'Hóa Đơn Của Tôi')

@section('content')
<div style="padding:50px 0;background:#f9f9f9;min-height:70vh;">
    <div class="container">
        <div class="titlepage mb-4">
            <h2>Hóa Đơn Của Tôi</h2>
        </div>

        {{-- Summary Cards --}}
        @php
            $totalUnpaid = $invoices->where('status', 'unpaid')->sum('total_amount');
            $totalOverdue = $invoices->where('status', 'overdue')->sum('total_amount');
            $totalPaid = $invoices->where('status', 'paid')->sum('total_amount');
        @endphp
        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <div class="p-3 rounded-3 bg-white shadow-sm border-start border-warning border-3">
                    <div class="text-muted small">Chưa thanh toán</div>
                    <div class="fw-bold fs-5 text-warning">{{ number_format($totalUnpaid) }}đ</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="p-3 rounded-3 bg-white shadow-sm border-start border-danger border-3">
                    <div class="text-muted small">Quá hạn</div>
                    <div class="fw-bold fs-5 text-danger">{{ number_format($totalOverdue) }}đ</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="p-3 rounded-3 bg-white shadow-sm border-start border-success border-3">
                    <div class="text-muted small">Đã thanh toán</div>
                    <div class="fw-bold fs-5 text-success">{{ number_format($totalPaid) }}đ</div>
                </div>
            </div>
        </div>

        @if($invoices->isEmpty())
            <div class="text-center py-5">
                <i class="fa fa-file-text-o fa-4x text-muted d-block mb-3"></i>
                <h5 class="text-muted">Chưa có hóa đơn nào</h5>
            </div>
        @else
            <div class="table-responsive" style="background:#fff;border-radius:12px;overflow:hidden;box-shadow:0 2px 12px rgba(0,0,0,.06);">
                <table class="table table-hover mb-0">
                    <thead style="background:#4f46e5;color:#fff;">
                        <tr>
                            <th>Mã HĐ</th>
                            <th>Phòng</th>
                            <th>Tháng/Năm</th>
                            <th>Tổng tiền</th>
                            <th>Hạn TT</th>
                            <th>Trạng thái</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($invoices as $invoice)
                            <tr>
                                <td><span class="text-muted small">{{ $invoice->transaction_id ?? '#'.$invoice->id }}</span></td>
                                <td><strong>{{ $invoice->room->name ?? 'N/A' }}</strong></td>
                                <td>{{ $invoice->month }}/{{ $invoice->year }}</td>
                                <td><strong>{{ number_format($invoice->total_amount) }}đ</strong></td>
                                <td>
                                    @if($invoice->due_date)
                                        <span class="{{ $invoice->isOverdue() ? 'text-danger fw-bold' : 'text-muted' }}">
                                            {{ $invoice->due_date->format('d/m/Y') }}
                                        </span>
                                    @else
                                        —
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-{{ $invoice->statusBadge() }}">{{ $invoice->statusLabel() }}</span>
                                </td>
                                <td>
                                    <a href="{{ route('user.invoices.show', $invoice) }}" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                                        <i class="fa fa-eye me-1"></i>Xem
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-3">{{ $invoices->links() }}</div>
        @endif
    </div>
</div>
@endsection
