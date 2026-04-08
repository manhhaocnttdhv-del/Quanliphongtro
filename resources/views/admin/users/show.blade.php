@extends('layouts.admin')
@section('title', 'Chi tiết: ' . $user->name)

@section('content')
<div class="container-fluid">
    <div class="d-flex align-items-center gap-3 mb-4">
        <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="fas fa-arrow-left me-1"></i> Quay lại
        </a>
        <div>
            <h4 class="mb-0 fw-bold">Chi tiết người dùng</h4>
        </div>
    </div>

    <div class="row g-4">
        {{-- Profile Card --}}
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center py-5">
                    <div class="rounded-circle bg-primary bg-opacity-10 text-primary fw-bold d-flex align-items-center justify-content-center mx-auto mb-3"
                         style="width:80px;height:80px;font-size:32px;">
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    </div>
                    <h5 class="fw-bold mb-1">{{ $user->name }}</h5>
                    <p class="text-muted mb-3 small">{{ $user->email }}</p>

                    @if($user->role === 'landlord')
                        <span class="badge bg-success bg-opacity-15 text-success border border-success border-opacity-25 px-3 py-2 mb-3 d-inline-block">
                            <i class="fas fa-home me-1"></i>Chủ trọ
                        </span>
                    @else
                        <span class="badge bg-info bg-opacity-15 text-info border border-info border-opacity-25 px-3 py-2 mb-3 d-inline-block">
                            <i class="fas fa-user me-1"></i>Người thuê
                        </span>
                    @endif

                    <div class="border-top pt-3 mt-2">
                        {{-- Đổi role --}}
                        @if($user->role === 'tenant')
                            <form method="POST" action="{{ route('admin.users.update-role', $user) }}"
                                  class="mb-2" onsubmit="return confirm('Nâng tài khoản này thành Chủ trọ?')">
                                @csrf @method('PATCH')
                                <input type="hidden" name="role" value="landlord">
                                <button type="submit" class="btn btn-sm btn-outline-success w-100">
                                    <i class="fas fa-arrow-up me-1"></i>Nâng lên Chủ trọ
                                </button>
                            </form>
                        @else
                            <form method="POST" action="{{ route('admin.users.update-role', $user) }}"
                                  class="mb-2" onsubmit="return confirm('Hạ tài khoản này xuống Người thuê?')">
                                @csrf @method('PATCH')
                                <input type="hidden" name="role" value="tenant">
                                <button type="submit" class="btn btn-sm btn-outline-warning text-dark w-100">
                                    <i class="fas fa-arrow-down me-1"></i>Hạ xuống Người thuê
                                </button>
                            </form>
                        @endif
                        <form method="POST" action="{{ route('admin.users.destroy', $user) }}"
                              onsubmit="return confirm('Xoá tài khoản {{ $user->name }}? Không thể hoàn tác.')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger w-100">
                                <i class="fas fa-trash me-1"></i>Xoá tài khoản
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            {{-- Thông tin cá nhân --}}
            <div class="card border-0 shadow-sm mt-4">
                <div class="card-header bg-white fw-bold border-bottom py-3">
                    <i class="fas fa-id-card me-2 text-primary"></i>Thông tin cá nhân
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="small text-muted">Số điện thoại</div>
                        <div class="fw-semibold">{{ $user->phone ?? '—' }}</div>
                    </div>
                    <div class="mb-3">
                        <div class="small text-muted">CCCD/CMND</div>
                        <div class="fw-semibold">{{ $user->id_card ?? '—' }}</div>
                    </div>
                    <div class="mb-3">
                        <div class="small text-muted">Ngày sinh</div>
                        <div class="fw-semibold">{{ $user->dob ? $user->dob->format('d/m/Y') : '—' }}</div>
                    </div>
                    <div class="mb-3">
                        <div class="small text-muted">Giới tính</div>
                        <div class="fw-semibold">{{ $user->gender ?? '—' }}</div>
                    </div>
                    <div class="mb-3">
                        <div class="small text-muted">Địa chỉ</div>
                        <div class="fw-semibold small">
                            {{ collect([$user->address_detail, $user->ward_name, $user->district_name, $user->province_name])->filter()->implode(', ') ?: '—' }}
                        </div>
                    </div>
                    <div>
                        <div class="small text-muted">Tham gia</div>
                        <div class="fw-semibold">{{ $user->created_at->format('d/m/Y H:i') }}</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Right side: Activity --}}
        <div class="col-lg-8">
            {{-- Stats --}}
            <div class="row g-3 mb-4">
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm text-center py-3">
                        <div class="text-primary fw-bold fs-3">{{ $user->rentRequests->count() }}</div>
                        <div class="small text-muted">Yêu cầu thuê</div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm text-center py-3">
                        <div class="text-success fw-bold fs-3">{{ $user->contracts->count() }}</div>
                        <div class="small text-muted">Hợp đồng</div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm text-center py-3">
                        <div class="text-warning fw-bold fs-3">
                            {{ $user->contracts->where('status', 'active')->count() }}
                        </div>
                        <div class="small text-muted">HĐ đang thuê</div>
                    </div>
                </div>
            </div>

            {{-- Rent Requests --}}
            @if($user->rentRequests->count() > 0)
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white fw-bold border-bottom py-3">
                    <i class="fas fa-paper-plane me-2 text-primary"></i>Yêu cầu thuê phòng
                </div>
                <div class="table-responsive">
                    <table class="table table-hover mb-0 align-middle small">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-3">Phòng</th>
                                <th>Ngày gửi</th>
                                <th>Trạng thái</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($user->rentRequests->take(10) as $req)
                            <tr>
                                <td class="ps-3 fw-semibold">{{ $req->room->name ?? '—' }}</td>
                                <td class="text-muted">{{ $req->created_at->format('d/m/Y') }}</td>
                                <td>
                                    @php $statusMap = ['pending'=>['warning','Chờ duyệt'],'approved'=>['success','Đã duyệt'],'rejected'=>['danger','Từ chối']]; @endphp
                                    <span class="badge bg-{{ $statusMap[$req->status][0] ?? 'secondary' }}-subtle text-{{ $statusMap[$req->status][0] ?? 'secondary' }} border border-{{ $statusMap[$req->status][0] ?? 'secondary' }}-subtle">
                                        {{ $statusMap[$req->status][1] ?? $req->status }}
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endif

            {{-- Contracts --}}
            @if($user->contracts->count() > 0)
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white fw-bold border-bottom py-3">
                    <i class="fas fa-file-contract me-2 text-success"></i>Lịch sử hợp đồng
                </div>
                <div class="table-responsive">
                    <table class="table table-hover mb-0 align-middle small">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-3">Phòng</th>
                                <th>Bắt đầu</th>
                                <th>Kết thúc</th>
                                <th>Trạng thái</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($user->contracts->take(10) as $contract)
                            <tr>
                                <td class="ps-3 fw-semibold">{{ $contract->room->name ?? '—' }}</td>
                                <td class="text-muted">{{ \Carbon\Carbon::parse($contract->start_date)->format('d/m/Y') }}</td>
                                <td class="text-muted">{{ $contract->end_date ? \Carbon\Carbon::parse($contract->end_date)->format('d/m/Y') : '—' }}</td>
                                <td>
                                    <span class="badge bg-{{ $contract->status === 'active' ? 'success' : 'secondary' }}-subtle text-{{ $contract->status === 'active' ? 'success' : 'secondary' }}">
                                        {{ $contract->status === 'active' ? 'Đang thuê' : 'Đã kết thúc' }}
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endif

            @if($user->rentRequests->count() === 0 && $user->contracts->count() === 0)
                <div class="card border-0 shadow-sm">
                    <div class="card-body text-center py-5 text-muted">
                        <i class="fas fa-inbox fa-3x mb-3 d-block opacity-25"></i>
                        Người dùng chưa có hoạt động nào.
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
