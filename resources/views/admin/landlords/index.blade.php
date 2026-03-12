@extends('layouts.admin')
@section('title', 'Quản lý Chủ trọ')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="fs-3 mb-1">Danh sách Chủ trọ</h1>
        <p class="text-muted small mb-0">Quản lý các đối tác vận hành nhà trọ trên hệ thống</p>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="bg-light">
                <tr>
                    <th class="ps-4">Chủ trọ</th>
                    <th>Thông tin liên hệ</th>
                    <th>Khu vực</th>
                    <th>Số phòng</th>
                    <th>Ngày tham gia</th>
                    <th class="pe-4 text-end">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                @forelse($landlords as $landlord)
                <tr>
                    <td class="ps-4">
                        <div class="d-flex align-items-center gap-3">
                            <div class="avatar-sm bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-center fw-bold">
                                {{ substr($landlord->name, 0, 1) }}
                            </div>
                            <div>
                                <div class="fw-bold text-dark">{{ $landlord->name }}</div>
                                <div class="text-muted small">ID: #{{ $landlord->id }}</div>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div class="small"><i class="ti ti-mail me-1"></i>{{ $landlord->email }}</div>
                        <div class="small"><i class="ti ti-phone me-1"></i>{{ $landlord->phone }}</div>
                    </td>
                    <td>
                        <span class="badge bg-light text-dark border">{{ $landlord->province_name ?? 'N/A' }}</span>
                    </td>
                    <td>
                        <span class="fw-bold text-primary">{{ $landlord->rooms_count }}</span> phòng
                    </td>
                    <td class="small">{{ $landlord->created_at->format('d/m/Y') }}</td>
                    <td class="pe-4 text-end">
                        <a href="{{ route('admin.landlords.show', $landlord) }}" class="btn btn-sm btn-outline-primary">Chi tiết</a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center py-5 text-muted">Chưa có chủ trọ nào đăng ký</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($landlords->hasPages())
    <div class="card-footer bg-white border-top-0 px-4 py-3">
        {{ $landlords->links() }}
    </div>
    @endif
</div>
@endsection
