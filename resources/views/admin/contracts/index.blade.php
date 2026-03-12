@extends('layouts.admin')
@section('title', 'Quản Lý Hợp Đồng')

@section('content')
<div class="mb-4">
    <h1 class="fs-3 mb-1">Danh sách hợp đồng</h1>
    <p class="text-muted mb-0">{{ $contracts->total() }} hợp đồng</p>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr><th>#</th><th>Người thuê</th><th>Phòng</th><th>Bắt đầu</th><th>Kết thúc</th><th>Tiền cọc</th><th>Tiền thuê/tháng</th><th>Trạng thái</th><th></th></tr>
            </thead>
            <tbody>
                @forelse($contracts as $contract)
                    <tr>
                        <td>{{ $contract->id }}</td>
                        <td>
                            <div class="fw-semibold">{{ $contract->user->name }}</div>
                            <div class="small text-muted">{{ $contract->user->phone }}</div>
                        </td>
                        <td><strong>{{ $contract->room->name }}</strong></td>
                        <td>{{ $contract->start_date->format('d/m/Y') }}</td>
                        <td>{{ $contract->end_date?->format('d/m/Y') ?? 'Không xác định' }}</td>
                        <td>{{ number_format($contract->deposit) }}đ</td>
                        <td>{{ number_format($contract->monthly_rent) }}đ</td>
                        <td>
                            <span class="badge {{ $contract->status === 'active' ? 'bg-success-subtle text-success' : 'bg-secondary-subtle text-secondary' }}">
                                {{ $contract->status === 'active' ? 'Đang hiệu lực' : 'Đã kết thúc' }}
                            </span>
                        </td>
                        <td>
                            <a href="{{ route('admin.contracts.show', $contract) }}" class="btn btn-sm btn-outline-primary"><i class="ti ti-eye"></i></a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="9" class="text-center py-5 text-muted">Chưa có hợp đồng</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer bg-transparent">{{ $contracts->links() }}</div>
</div>
@endsection
