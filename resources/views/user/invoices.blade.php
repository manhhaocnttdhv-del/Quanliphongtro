@extends('layouts.user')
@section('title', 'Hóa Đơn Của Tôi')

@section('content')
<div style="padding:50px 0;background:#f9f9f9;min-height:70vh;">
    <div class="container">
        <div class="titlepage mb-4">
            <h2>Hóa Đơn Của Tôi</h2>
        </div>

        @if($invoices->isEmpty())
            <div class="text-center py-5">
                <i class="fa fa-file-text-o fa-4x text-muted d-block mb-3"></i>
                <h5 class="text-muted">Chưa có hóa đơn nào</h5>
            </div>
        @else
            <div class="table-responsive" style="background:#fff;border-radius:12px;overflow:hidden;box-shadow:0 2px 12px rgba(0,0,0,.06);">
                <table class="table table-hover mb-0">
                    <thead style="background:#f9a825;color:#fff;">
                        <tr>
                            <th>Phòng</th>
                            <th>Tháng/Năm</th>
                            <th>Tiền phòng</th>
                            <th>Điện</th>
                            <th>Nước</th>
                            <th>Tổng tiền</th>
                            <th>Trạng thái</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($invoices as $invoice)
                            <tr>
                                <td><strong>{{ $invoice->room->name ?? 'N/A' }}</strong></td>
                                <td>Tháng {{ $invoice->month }}/{{ $invoice->year }}</td>
                                <td>{{ number_format($invoice->room_fee) }}đ</td>
                                <td>{{ number_format($invoice->electricity_fee) }}đ</td>
                                <td>{{ number_format($invoice->water_fee) }}đ</td>
                                <td><strong>{{ number_format($invoice->total_amount) }}đ</strong></td>
                                <td>
                                    <span class="badge bg-{{ $invoice->statusBadge() }}">{{ $invoice->statusLabel() }}</span>
                                </td>
                                <td>
                                    <a href="{{ route('user.invoices.show', $invoice) }}" class="btn btn-sm btn-warning">
                                        <i class="fa fa-eye"></i>
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
