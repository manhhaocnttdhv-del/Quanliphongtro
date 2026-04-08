@extends('layouts.admin')
@section('title', 'Quản lý Người dùng')

@section('content')
<div class="container-fluid">
    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1 fw-bold">Quản lý Người dùng</h4>
            <p class="text-muted mb-0 small">Quản lý chủ trọ và người thuê trong hệ thống</p>
        </div>
    </div>

    {{-- Stats --}}
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="rounded-circle d-flex align-items-center justify-content-center bg-primary bg-opacity-10 text-primary" style="width:50px;height:50px;font-size:22px;">
                        <i class="fas fa-users"></i>
                    </div>
                    <div>
                        <div class="small text-muted">Tổng người dùng</div>
                        <div class="fw-bold fs-4">{{ $totalTenants + $totalLandlords }}</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="rounded-circle d-flex align-items-center justify-content-center bg-success bg-opacity-10 text-success" style="width:50px;height:50px;font-size:22px;">
                        <i class="fas fa-home"></i>
                    </div>
                    <div>
                        <div class="small text-muted">Chủ trọ</div>
                        <div class="fw-bold fs-4">{{ $totalLandlords }}</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="rounded-circle d-flex align-items-center justify-content-center bg-info bg-opacity-10 text-info" style="width:50px;height:50px;font-size:22px;">
                        <i class="fas fa-user"></i>
                    </div>
                    <div>
                        <div class="small text-muted">Người thuê</div>
                        <div class="fw-bold fs-4">{{ $totalTenants }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Search & Filter --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.users.index') }}">
                <div class="row g-2 align-items-end">
                    <div class="col-md-5">
                        <label class="form-label small fw-bold">Tìm kiếm</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white"><i class="fas fa-search text-muted"></i></span>
                            <input type="text" class="form-control" name="search" value="{{ request('search') }}" placeholder="Tên, email, số điện thoại...">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-bold">Vai trò</label>
                        <select name="role" class="form-select">
                            <option value="">Tất cả</option>
                            <option value="tenant"   {{ request('role') === 'tenant'   ? 'selected' : '' }}>Người thuê</option>
                            <option value="landlord" {{ request('role') === 'landlord' ? 'selected' : '' }}>Chủ trọ</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100">Lọc</button>
                    </div>
                    <div class="col-md-2">
                        <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary w-100">Xoá lọc</a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Table --}}
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">#</th>
                            <th>Người dùng</th>
                            <th>Liên hệ</th>
                            <th>Vai trò</th>
                            <th class="text-center">Yêu cầu thuê</th>
                            <th class="text-center">Hợp đồng</th>
                            <th>Ngày tạo</th>
                            <th class="text-center pe-4">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $user)
                        <tr>
                            <td class="ps-4 text-muted small">{{ $user->id }}</td>
                            <td>
                                <div class="d-flex align-items-center gap-3">
                                    <div class="rounded-circle bg-primary bg-opacity-10 text-primary fw-bold d-flex align-items-center justify-content-center"
                                         style="width:40px;height:40px;font-size:16px;flex-shrink:0;">
                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="fw-semibold">{{ $user->name }}</div>
                                        <div class="small text-muted">{{ $user->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="small">{{ $user->phone ?? '—' }}</div>
                                @if($user->province_name)
                                    <div class="small text-muted">{{ $user->province_name }}</div>
                                @endif
                            </td>
                            <td>
                                @if($user->role === 'landlord')
                                    <span class="badge bg-success bg-opacity-15 text-success border border-success border-opacity-25 px-3 py-2">
                                        <i class="fas fa-home me-1"></i>Chủ trọ
                                    </span>
                                @else
                                    <span class="badge bg-info bg-opacity-15 text-info border border-info border-opacity-25 px-3 py-2">
                                        <i class="fas fa-user me-1"></i>Người thuê
                                    </span>
                                @endif
                            </td>
                            <td class="text-center">
                                <span class="fw-semibold">{{ $user->rent_requests_count }}</span>
                            </td>
                            <td class="text-center">
                                <span class="fw-semibold">{{ $user->contracts_count }}</span>
                            </td>
                            <td class="small text-muted">{{ $user->created_at->format('d/m/Y') }}</td>
                            <td class="text-center pe-4">
                                <div class="d-flex gap-1 justify-content-center">
                                    <a href="{{ route('admin.users.show', $user) }}"
                                       class="btn btn-sm btn-outline-primary" title="Chi tiết">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    {{-- Đổi role --}}
                                    @if($user->role === 'tenant')
                                        <form method="POST" action="{{ route('admin.users.update-role', $user) }}"
                                              onsubmit="return confirm('Nâng lên Chủ trọ?')">
                                            @csrf @method('PATCH')
                                            <input type="hidden" name="role" value="landlord">
                                            <button type="submit" class="btn btn-sm btn-outline-success" title="Nâng thành Chủ trọ">
                                                <i class="fas fa-arrow-up"></i>
                                            </button>
                                        </form>
                                    @else
                                        <form method="POST" action="{{ route('admin.users.update-role', $user) }}"
                                              onsubmit="return confirm('Hạ xuống Người thuê?')">
                                            @csrf @method('PATCH')
                                            <input type="hidden" name="role" value="tenant">
                                            <button type="submit" class="btn btn-sm btn-outline-warning text-dark" title="Hạ xuống Người thuê">
                                                <i class="fas fa-arrow-down"></i>
                                            </button>
                                        </form>
                                    @endif
                                    {{-- Xoá --}}
                                    <form method="POST" action="{{ route('admin.users.destroy', $user) }}"
                                          onsubmit="return confirm('Xoá tài khoản {{ $user->name }}? Hành động này không thể khôi phục.')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Xoá">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-5 text-muted">
                                <i class="fas fa-users fa-3x mb-3 d-block opacity-25"></i>
                                Không có người dùng nào.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($users->hasPages())
        <div class="card-footer bg-white border-0 d-flex justify-content-center pt-3">
            {{ $users->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
