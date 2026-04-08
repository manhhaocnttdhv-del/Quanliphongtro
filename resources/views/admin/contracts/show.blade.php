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
        <div class="card mb-4">
            <div class="card-header bg-transparent d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Thành viên trong phòng</h5>
                <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addMemberModal">
                    <i class="ti ti-plus me-1"></i>Thêm người ở
                </button>
            </div>
            <div class="table-responsive">
                <table class="table table-sm mb-0 align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Họ tên</th>
                            <th>SĐT</th>
                            <th>CCCD</th>
                            <th class="text-end">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($contract->members as $member)
                            <tr>
                                <td>
                                    <div class="fw-semibold">{{ $member->name }}</div>
                                    <small class="text-muted">{{ $member->genderLabel() }} - {{ $member->dob?->format('d/m/Y') }}</small>
                                </td>
                                <td>{{ $member->phone }}</td>
                                <td>{{ $member->id_card_number }}</td>
                                <td class="text-end">
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-icon btn-light" data-bs-toggle="dropdown"><i class="ti ti-dots-vertical"></i></button>
                                        <ul class="dropdown-menu dropdown-menu-end">
                                            <li><button class="dropdown-item" data-bs-toggle="modal" data-bs-target="#editMemberModal{{ $member->id }}"><i class="ti ti-edit me-2"></i>Sửa</button></li>
                                            <li>
                                                <form action="{{ route('admin.room-members.destroy', $member) }}" method="POST" onsubmit="return confirm('Xác nhận xóa thành viên này?')">
                                                    @csrf @method('DELETE')
                                                    <button class="dropdown-item text-danger"><i class="ti ti-trash me-2"></i>Xóa</button>
                                                </form>
                                            </li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="text-center py-3 text-muted">Chưa có thành viên thêm kèm</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

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

<!-- Add Member Modal -->
<div class="modal fade" id="addMemberModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form action="{{ route('admin.room-members.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="room_id" value="{{ $contract->room_id }}">
            <input type="hidden" name="contract_id" value="{{ $contract->id }}">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Thêm thành viên mới</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Họ tên <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">SĐT</label>
                            <input type="text" name="phone" class="form-control">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Số CCCD</label>
                            <input type="text" name="id_card_number" class="form-control">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Ngày sinh</label>
                            <input type="date" name="dob" class="form-control">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Giới tính</label>
                            <select name="gender" class="form-select">
                                <option value="male">Nam</option>
                                <option value="female">Nữ</option>
                                <option value="other">Khác</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Mặt trước CCCD</label>
                        <input type="file" name="id_card_front" class="form-control" accept="image/*">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Mặt sau CCCD</label>
                        <input type="file" name="id_card_back" class="form-control" accept="image/*">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-primary">Lưu thành viên</button>
                </div>
            </div>
        </form>
    </div>
</div>

@foreach($contract->members as $member)
<!-- Edit Member Modal {{ $member->id }} -->
<div class="modal fade" id="editMemberModal{{ $member->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form action="{{ route('admin.room-members.update', $member) }}" method="POST" enctype="multipart/form-data">
            @csrf @method('PATCH')
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Sửa thông tin thành viên</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Họ tên <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" value="{{ $member->name }}" required>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">SĐT</label>
                            <input type="text" name="phone" class="form-control" value="{{ $member->phone }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Số CCCD</label>
                            <input type="text" name="id_card_number" class="form-control" value="{{ $member->id_card_number }}">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Ngày sinh</label>
                            <input type="date" name="dob" class="form-control" value="{{ $member->dob?->format('Y-m-d') }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Giới tính</label>
                            <select name="gender" class="form-select">
                                <option value="male" {{ $member->gender == 'male' ? 'selected':'' }}>Nam</option>
                                <option value="female" {{ $member->gender == 'female' ? 'selected':'' }}>Nữ</option>
                                <option value="other" {{ $member->gender == 'other' ? 'selected':'' }}>Khác</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between">
                            <label class="form-label">Mặt trước CCCD</label>
                            @if($member->id_card_front) <a href="{{ asset('storage/'.$member->id_card_front) }}" target="_blank" class="small">Xem cũ</a> @endif
                        </div>
                        <input type="file" name="id_card_front" class="form-control" accept="image/*">
                    </div>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between">
                            <label class="form-label">Mặt sau CCCD</label>
                            @if($member->id_card_back) <a href="{{ asset('storage/'.$member->id_card_back) }}" target="_blank" class="small">Xem cũ</a> @endif
                        </div>
                        <input type="file" name="id_card_back" class="form-control" accept="image/*">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-primary">Lưu thay đổi</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endforeach
@endsection
